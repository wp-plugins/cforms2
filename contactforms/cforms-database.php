<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';
		
### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms') && !current_user_can('track_cforms')) {
	die(__('Access Denied','cforms'));
}


// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	return;
}


//
// sorting of entries
//
if ( isset($_POST['order']) ) {

	$orderdir = $_POST['orderdir'];
	$order = $_POST['order'];

} else { 

	$orderdir = 'DESC';
	$order = 'sub_date';	

}



//
// delete checked entries
//
if ( (isset($_POST['delete'])) ) {

	$i=0;
	foreach ($_POST['entries'] as $entry) :
		$entry = (int) $entry;

		$temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$fileval->form_id.'_upload_dir'))) );
		$fileuploaddir = $temp[0];

		$fileval = $wpdb->get_row("SELECT DISTINCT field_val,form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id = '$entry' AND id=sub_id AND field_name LIKE '%[*]%'");
		$file = $fileuploaddir.'/'.$entry.'-'.$fileval->field_val;
		
		$del='';
		if ( $fileval->field_val <> '' ){
			if ( file_exists( $file ) )
				unlink ( $file );
		}

		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE id = '$entry'");
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE sub_id = '$entry'");

		$i++;
	endforeach;
	
	?>
	<div id="message" class="updated fade"><p><strong><?php echo $i; ?> <?php _e('Entries successfully removed from the tables!', 'cforms') ?></strong><br />
		<em><?php _e('Note: If you erroneously deleted an entry, no worries, you should still have an email copy.', 'cforms') ?></em></p></div>
	<?php

}



// delete an entry?
if ( isset($_POST['sqlwhere']) ){

	foreach( array_keys($_POST) as $arg){
		if ( ! (strpos($arg, 'xbutton') === false) )
			$entry = substr( $arg,7 );
	}

	$filevalues = $wpdb->get_results("SELECT field_val,form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id = '$entry' AND id=sub_id AND field_name LIKE '%[*]%'");

	
	$del='';
	$found = 0;
	
	foreach( $filevalues as $fileval ) {

		$temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$fileval->form_id.'_upload_dir'))) );
		$fileuploaddir = $temp[0];
	
		$file = $fileuploaddir.'/'.$entry.'-'.$fileval->field_val;

		if ( $fileval->field_val <> '' ){
			if ( file_exists( $file ) ){
				unlink ( $file );
				$found = $found | 1;
			}
			else{
				$found = $found | 2;
			}
		}
	}

	if ( $found==3 )
		$del = ' '.__('(some associated attachment/s were not found!)','cforms');
	else if ( $found==2 )
		$del = ' '.__('(associated attachment/s were not found!)','cforms');
	else if ( $found==1 )
		$del = ' '.__('(including all attachment/s)','cforms');
		
	$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE id = '$entry'");
	$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE sub_id = '$entry'");

	?>
	<div id="message" class="updated fade"><p><strong><?php echo $i; ?> <?php _e('Entry successfully removed', 'cforms'); echo $del; ?>.</strong></p></div>
	<?php
}



//
// load a specific entry
//
if ( ($_POST['showid']<>'' || isset($_POST['showselected']) || isset($_POST['sqlwhere'])) && !isset($_POST['filter_x']) && !isset($_POST['delete']) && !isset($_POST['downloadselectedcforms']) && !$reorder ) {

	if ( isset($_POST['showselected']) && isset($_POST['entries']) )
		$sub_id = implode(',', $_POST['entries']);
	else if ( $_POST['showid']<>'' )
		$sub_id = $_POST['showid'];
	else if ( isset($_POST['sqlwhere']) )
		$sub_id = $_POST['sqlwhere'];	
	else
		$sub_id = '-1';
	
	$sql="SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id in ($sub_id) AND sub_id=id ORDER BY sub_id, f_id";
	$entries = $wpdb->get_results($sql);
	
	?>

	<div class="wrap"><a id="top"></a>
	<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg" alt=""/>

	<?php if ($entries) :

		echo '<form name="datactrl" method="post" action="#"><input type="hidden" name="sqlwhere" value="'.$sub_id.'">';
		
		$sub_id='';
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){
				$sub_id = $entry->sub_id;
				echo '<div class="showform">Form: <span>'. stripslashes(get_option('cforms'.$entry->form_id.'_fname')) . '</span> &nbsp; <em>(ID:' . $entry->sub_id . ')</em>' .
						'&nbsp; <input class="allbuttons altx" type="submit" name="xbutton'.$entry->sub_id.'" title="'.__('delete this entry', 'cforms').'" value=""/></div>' . "\n";
			}

			$name = $entry->field_name==''?'&nbsp;':stripslashes($entry->field_name);
			$val  = $entry->field_val ==''?'&nbsp;':stripslashes($entry->field_val);

			if (strpos($name,'[*]')!==false) {  // attachments?

					$no   = $entry->form_id; 

					$temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_dir'))) );
					$fileuploaddir = $temp[0];
					$fileuploaddirurl = $temp[1];
										
					if ( $fileuploaddirurl=='' ){
	                    $fileurl = $fileuploaddir.'/'.$entry->sub_id.'-'.strip_tags($val);
	                    $fileurl = get_settings('siteurl') . substr( $fileurl, strpos($fileurl, '/wp-content/') );
					}
					else
	                    $fileurl = $fileuploaddirurl.'/'.$entry->sub_id.'-'.strip_tags($val);

					echo '<div class="showformfield" style="margin:4px 0;color:#3C575B;"><div class="L">';
					_e('Attached file:', 'cforms');
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms') . '</div></div>' . "\n";					
					else
						echo 	'</div><div class="R">' . '<a href="' . $fileurl . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='page') {  // special field: page 
			
					echo '<div class="showformfield" style="margin-bottom:10px;color:#3C575B;"><div class="L">';
					_e('Submitted via page', 'cforms');
					echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			} elseif ( strpos($name,'Fieldset')!==false ) {
			
					echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val)  . '</div></div>' . "\n";
			
			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}

		echo '</form>';


	else : ?>
	
		<p><?php _e('Sorry, no form data found.', 'cforms') ?></p>

	<?php endif;


} else {

	$WHERE = 'WHERE ';
	$WHERE .= ( isset($_POST['filter_form']) && $_POST['filter_form']<>'*')?" form_id='{$_POST['filter_form']}' AND":"";
	$WHERE .= ($_POST['filter_email']<>'')?" email LIKE '%{$_POST['filter_email']}%' AND":"";
	$WHERE .= ($_POST['filter_ip']<>'')?" ip LIKE '%{$_POST['filter_ip']}%' AND":"";
	$WHERE .= ($_POST['filter_date1']<>'' && $_POST['filter_date1']<>'from')?" sub_date >= '{$_POST['filter_date1']}' AND":"";
	$WHERE .= ($_POST['filter_date2']<>'' && $_POST['filter_date1']<>'to')?" sub_date <= '{$_POST['filter_date2']}' AND":"";

	$WHERE = substr( $WHERE,0,strrpos($WHERE, 'AND') );
			
	//
	// load entries
	//
	$sql="SELECT * FROM {$wpdb->cformssubmissions} $WHERE ORDER BY $order $orderdir";
	$entries = $wpdb->get_results($sql);

	$formselect  = '<select name="filter_form" id="filter_form">';
	$formselect .= '<option value="*" '.( (!isset($_POST['filter_form']))?'selected="selected"':'' ).'>'.__('**all forms**','cforms').'</option>';

	for ($i=1; $i <= get_option('cforms_formcount'); $i++){
		$n = ( $i==1 )?'':$i; 
		$selected = ( isset($_POST['filter_form']) && $_POST['filter_form']==$n )?'selected="selected"':'';
		$formselect .= '<option value="'.$n.'" '.$selected.'>'.stripslashes(get_option('cforms'.$n.'_fname')).'</option>';
	}
	
	$formselect .= '</select>';

	?>

	<div class="wrap"><a id="top"></a>
	<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg" alt=""/>

		<?php if ($entries) :?>

		<p><?php _e('Keep track of all form submissions &amp; data entered, view individual entries or a whole bunch and download as TAB or CSV formatted file. Attachments can be accessed in the details section. When deleting entries, associated attachments will be removed, too! ', 'cforms') ?></p>

		<p><?php echo sprintf( __('Use the below filter fields <img src="%s" alt="Filter" title="Click to filter"/> to narrow down the number of shown entries.', 'cforms'),$cforms_root.'/images/search_icon.gif') ?></p>

		<form id="cformsdata" name="form" method="post" action="">
				<input type="hidden" name="showid" value=""/>
				<input type="hidden" name="order" value="<?php echo $order; ?>"/>
				<input type="hidden" name="orderdir" value="<?php echo $orderdir; ?>"/>
				<input type="hidden" name="checkflag" value="0"/>


				<ul class="sortheader">
					<li class="col0">#</li>
					<li class="col1"><?php _e('?') ?><br /><input type="image" id="filterbutton" name="filter" src="<?php echo $cforms_root; ?>/images/search_icon.gif" title="<?php _e('Search','cforms') ?>" alt="<?php _e('Search','cforms') ?>"/></li>
					<li class="col2"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);sort_entries('form_id');"><?php _e('Form','cforms') ?></a></span><br/><?php echo $formselect; ?></li>
					<li class="col3"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);sort_entries('email');"><?php _e('Who','cforms') ?></a></span><br/><input type="text" name="filter_email" id="filter_email" value="<?php echo $_POST['filter_email']; ?>"/></li>
					<li class="col4"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);sort_entries('sub_date');"><?php _e('When','cforms') ?></a></span><br/><input type="text" name="filter_date1" id="filter_date1" value="<?php echo (($_POST['filter_date1']=='')?__('from','cforms'):$_POST['filter_date1']); ?>"/><br/><input type="text" name="filter_date2" id="filter_date2" value="<?php echo (($_POST['filter_date2']=='')?__('to','cforms'):$_POST['filter_date2']); ?>"/></li>
					<li class="col5"><span class="abbr" title="<?php _e('click to sort', 'cforms'); ?>"><a href="javascript:void(0);sort_entries('ip');"><?php _e('IP','cforms') ?></a></span><br/><input type="text" name="filter_ip" id="filter_ip" value="<?php echo $_POST['filter_ip']; ?>"/></li>
				</ul>


				<ul class="selectrow" style="margin:8px auto;">
					<li>
						<label for="allchktop"><input type="checkbox" id="allchktop" name="allchktop" onclick="javascript:checkonoff('form','entries[]');"/> <strong><?php _e('select/deselect all', 'cforms') ?></strong></label>
					</li>
				</ul>

				<div class="r15container"><div id="r15" class="rbox">
				
					<div id="trackingdata">
					<?php
					$class=''; $i=0;
					foreach ($entries as $entry)
					{
						$class = ('alternate' == $class) ? '' : 'alternate'; ?>
	
						<ul class="datarow <?php echo $class; ?>">
							<li class="col0"><?php echo $entry->id; ?></li>
							<li class="col1"><input type="checkbox" name="entries[]" id="e<?php echo $entry->id; ?>" value="<?php echo $entry->id; ?>" /></li>
							<li class="col2" onclick="checkentry('e<?php echo $entry->id; ?>')"><?php echo stripslashes(get_option('cforms'.$entry->form_id.'_fname')); ?></li>
							<li class="col3" onclick="checkentry('e<?php echo $entry->id; ?>')"><?php echo $entry->email; ?></li>
							<li class="col4" onclick="checkentry('e<?php echo $entry->id; ?>')"><?php echo $entry->sub_date; ?></li>
							<li class="col5" onclick="checkentry('e<?php echo $entry->id; ?>')"><a href="http://geomaplookup.cinnamonthoughts.org/?ip=<?php echo $entry->ip; ?>" title="<?php _e('IP Lookup', 'cforms') ?>"><?php echo $entry->ip; ?></a></li>
							<li class="col6" onclick="checkentry('e<?php echo $entry->id; ?>')"><?php echo '<a href="#" onclick="document.form.showid.value=\''.$entry->id.'\';document.form.submit();">'; ?><?php _e('view', 'cforms') ?></a></li>
						</ul>
	
					<?php
					}
					?>
					</div>
					
					<div id="rh15"></div>
				</div></div>
				
				<ul class="selectrow" style="margin:14px auto 8px auto;">
					<li>
						<label for="allchkbottom"><input type="checkbox" id="allchkbottom" name="allchkbottom" onclick="javascript:checkonoff('form','entries[]');"/> <strong><?php _e('select/deselect all', 'cforms') ?></strong></label>
					</li>
				</ul>


				<div class="dataheader">
					<input type="submit" class="allbuttons delete" name="delete" value="<?php _e('delete selected entries', 'cforms') ?>" onclick="return confirm('Do you really want to erase the selected records?');"/>
					<input type="submit" class="allbuttons showselected" name="showselected" value="<?php _e('show selected entries', 'cforms') ?>" />&nbsp;&nbsp;
					<input type="submit" class="allbuttons downloadselectedcforms" name="downloadselectedcforms" value="<?php _e('download selected entries', 'cforms') ?>" />
					<select name="downloadformat" class="downloadformat">
						<option value="csv"><?php _e('CSV', 'cforms') ?></option>
						<option value="txt"><?php _e('TXT (tab delimited)', 'cforms') ?></option>
					</select>
				</div>

			</form>

		<?php else :?>

		<p><?php _e('No data available at this time.','cforms'); ?></p>

		<?php endif; 

} // all data or just one

cforms_footer();
echo '</div>';
?>
