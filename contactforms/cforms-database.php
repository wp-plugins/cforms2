<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = 'contactforms';    // if you change this setting, see also cforms.php !!
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';


### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die('Access Denied');
}


// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
		?>
		<div class="wrap">
		<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
		<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, '.
								'or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
		</div>
		<?php
		die;
}


//
// sorting of entries
//
if ( isset($_POST['order']) ) {

	$orderdir = $_POST['orderdir'];
	$order = $_POST['order'];

} else { 

	$orderdir = 'DESC';
	$order = 'date';	

}



//
// delete checked entries
//
if ( (isset($_POST['delete'])) ) {

	$i=0;
	foreach ($_POST['entries'] as $entry) :
		$entry = (int) $entry;
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformssubmissions} WHERE id = '$entry'");
		$nuked = $wpdb->query("DELETE FROM {$wpdb->cformsdata} WHERE sub_id = '$entry'");
		$i++;
	endforeach;
	
	?>
	<div id="message" class="updated fade"><p><strong><?php echo $i; ?> <?php _e('entries succesfully removed from the tables!', 'cforms') ?></strong><br/>
		<em><?php _e('Note: If you erroneously deleted an entry, no worries, you should still have an email copy.', 'cforms') ?></em></p></div>
	<?php

}



//
// load a specific entry
//
if ( ($_POST['showid']<>'' || isset($_POST['showselected'])) 
		&& !isset($_POST['delete']) && !isset($_POST['downloadselectedcforms']) && !$reorder ) {

	if ( isset($_POST['showselected']) && isset($_POST['entries']) )
		$sub_id = implode(',', $_POST['entries']);
	else if ( $_POST['showid']<>'' )
		$sub_id = $_POST['showid'];
	else
		$sub_id = '-1';
	
	
	$sql="SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id in ($sub_id) AND sub_id=id ORDER BY sub_id";
	$entries = $wpdb->get_results($sql);
	
	?>

	<div class="wrap"><a id="top"></a><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg">

	<?php if ($entries) :

		_e('<p>Your results:</p>', 'cforms');

		$sub_id='';
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){
				$sub_id = $entry->sub_id;
				echo '<div class="showform">Form: <span>'. get_option('cforms'.$entry->form_id.'_fname') . '</span></div>' . "\n";
			}

			$name = $entry->field_name==''?'&nbsp;':$entry->field_name;
			$val  = $entry->field_val ==''?'&nbsp;':$entry->field_val;

			if ($name=='page') {
			
					echo '<div class="showformfield" style="margin-bottom:10px;color:#3C575B;"><div class="L">';
					_e('Submitted via page', 'cforms');
					echo 	'</div><div class="R">' . str_replace("\n","<br/>", strip_tags($val) ) . '</div></div>' . "\n";

			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div class="R">' . str_replace("\n","<br/>", strip_tags($val) ) . '</div></div>' . "\n";

			}

		}


	else : ?>
	
		<p><?php _e('Sorry, no form data found.', 'cforms') ?></p>

	<?php endif;


} else {


	//
	// load entries
	//
	$sql="SELECT * FROM {$wpdb->cformssubmissions} ORDER BY $order $orderdir";
	$entries = $wpdb->get_results($sql);

	?>

	<div class="wrap"><a id="top"></a><img src="<?php echo $cforms_root; ?>/images/p3-title.jpg">

		<?php if ($entries) :?>

		<p><?php _e('Keep track of all form submissions & data entered, view individual entries or a whole bunch and download '.
								'as TAB or CSV formatted file.', 'cforms') ?></p>

		<form id="cformsdata" name="form" method="post" action="">
				<input type="hidden" name="showid" value=""/>
				<input type="hidden" name="order" value="<?php echo $order; ?>"/>
				<input type="hidden" name="orderdir" value="<?php echo $orderdir; ?>"/>
				<input type="hidden" name="checkflag" value="0"/>


				<div class="dataheader">
					<input type="submit" class="delete" name="delete" value="<?php _e('delete selected entries', 'cforms') ?>" onclick="return confirm('Do you really want to erase the selected records?');"/>
					<input type="submit" class="showselected" name="showselected" value="<?php _e('show selected entries', 'cforms') ?>" />&nbsp;&nbsp;
					<input type="submit" class="downloadselectedcforms" name="downloadselectedcforms" value="<?php _e('download selected entries', 'cforms') ?>" />
					<select name="downloadformat" class="downloadformat">
						<option value="csv"><?php _e('CSV', 'cforms') ?></option>
						<option value="txt"><?php _e('TXT (tab delimited)', 'cforms') ?></option>
					</select>
				</div>

				<ul class="dataheader">
					<li class="col0">#</li>
					<li class="col1"><?php _e('?') ?></li>
					<li class="col2"><a href="javascript:void(0);" onclick="sort_entries('form_id');"><?php _e('Form') ?></a></li>
					<li class="col3"><a href="javascript:void(0);" onclick="sort_entries('email');"><?php _e('Who') ?></a></li>
					<li class="col4"><a href="javascript:void(0);" onclick="sort_entries('date');"><?php _e('When') ?></a><li></li>
					<li class="col5"><a href="javascript:void(0);" onclick="sort_entries('ip');"><?php _e('IP') ?></a></li>
				</ul>


				<ul class="selectrow" style="margin-bottom:10px; border-bottom:1px solid #EBEBEB;">
					<li><input type="checkbox" id="allchktop" name="allchktop" onClick="javascript:checkonoff('form','entries[]');"/>
						<label for="allchktop" style="font-size:10px;" ><strong><?php _e('select/deselect all', 'cforms') ?></strong></label></li>
				</ul>

				<?php
				$class=''; $i=0;
				foreach ($entries as $entry)
				{
					$class = ('alternate' == $class) ? '' : 'alternate'; ?>

					<ul class="datarow <?php echo $class; ?>">
						<li class="col0"><?php echo $i++; ?></li>
						<li class="col1"><input type="checkbox" name="entries[]" value="<?php echo $entry->id; ?>" /></li>
						<li class="col2"><?php echo get_option('cforms'.$entry->form_id.'_fname'); ?></li>
						<li class="col3"><?php echo $entry->email; ?></li>
						<li class="col4"><?php echo $entry->date; ?></li>
						<li class="col5"><?php echo $entry->ip; ?></li>
						<li class="col6"><?php echo '<a href="#" onclick="document.form.showid.value=\''.$entry->id.'\';document.form.submit();">'; ?><?php _e('view', 'cforms') ?></a></li>
					</ul>

				<?php
				}
				?>

				<ul class="selectrow" style="margin-top:10px; border-top:1px solid #EBEBEB;">
					<li><input type="checkbox" id="allchkbottom" name="allchkbottom" onClick="javascript:checkonoff('form','entries[]');"/>
						<label for="allchkbottom" style="font-size:10px;" ><strong><?php _e('select/deselect all', 'cforms') ?></strong></label></li>
				</ul>

				<div class="dataheader">
					<input type="submit" class="delete" name="delete" value="<?php _e('delete selected entries', 'cforms') ?>" onclick="return confirm('Do you really want to erase the selected records?');" />
					<input type="submit" class="showselected" name="showselected" value="<?php _e('show selected entries', 'cforms') ?>" />&nbsp;&nbsp;
					<input type="submit" class="downloadselectedcforms" name="downloadselectedcforms" value="<?php _e('download selected entries', 'cforms') ?>" />
				</div>

			</form>

		<?php else :?>

		<p>No data available at this time.</p>

		<?php endif; 

} // all data or just one

cforms_footer();
echo '</div>';
?>
