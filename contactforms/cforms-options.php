<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;


### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}


// default to 1 & get real #
$FORMCOUNT=get_option('cforms_formcount');


// if all data has been erased quit
if ($FORMCOUNT == ''){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>

	<?php
	die;
}


if(isset($_REQUEST['addbutton'])) {


	$FORMCOUNT=$FORMCOUNT+1;
	$no = $noDISP = $FORMCOUNT;
	
	update_option('cforms_formcount', (string)($FORMCOUNT));
	
	add_option('cforms'.$no.'_count_fields', '5');
	add_option('cforms'.$no.'_count_field_1', __('My Fieldset$#$fieldsetstart$#$0$#$0$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_2', __('Your Name|Your Name$#$textfield$#$1$#$0$#$1', 'cforms'));
	add_option('cforms'.$no.'_count_field_3', __('Email$#$textfield$#$1$#$1$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_4', __('Website|http://$#$textfield$#$0$#$0$#$0', 'cforms'));
	add_option('cforms'.$no.'_count_field_5', __('Message$#$textarea$#$0$#$0$#$0', 'cforms'));
	
	add_option('cforms'.$no.'_required', __('(required)', 'cforms'));
	add_option('cforms'.$no.'_emailrequired', __('(valid email required)', 'cforms'));
	
	add_option('cforms'.$no.'_ajax', '1');
	add_option('cforms'.$no.'_confirm', '0');
	add_option('cforms'.$no.'_fname', __('Your form #$no', 'cforms'));
	add_option('cforms'.$no.'_csubject', __('Re: Your note', 'cforms'));
	add_option('cforms'.$no.'_cmsg', __('Dear Sender,', 'cforms') . "\n". __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
	add_option('cforms'.$no.'_email', get_bloginfo('admin_email', 'cforms'));
	
	add_option('cforms'.$no.'_subject', __('A comment from a site visitor', 'cforms'));
	add_option('cforms'.$no.'_submit_text', __('Send Comment', 'cforms'));
	add_option('cforms'.$no.'_success', __('Thank you for your comment!', 'cforms'));
	add_option('cforms'.$no.'_failure', __('Please fill in all the required fields.', 'cforms'));
	add_option('cforms'.$no.'_working', __('One moment please...', 'cforms'));
	add_option('cforms'.$no.'_popup', __('nn', 'cforms'));
	
	echo '<div class="updated"><p>'.__('New form added.', 'cforms').'</p></div>';


} elseif(isset($_REQUEST['dupbutton'])) {


	$noDISP='1'; $no='';
	if( isset($_REQUEST['no']) ) {
		if( $_REQUEST['no']<>'1' )
			$noDISP = $no = $_REQUEST['no'];
	}
	
	$FORMCOUNT=$FORMCOUNT+1;
	
	update_option('cforms_formcount', (string)($FORMCOUNT));
	
	add_option('cforms'.$FORMCOUNT.'_count_fields', get_option('cforms'.$no.'_count_fields'));
	
	for ( $j=1; $j<=get_option('cforms'.$no.'_count_fields'); $j++)  //delete all extra fields!
		  add_option('cforms'.$FORMCOUNT.'_count_field_'.$j, get_option('cforms'.$no.'_count_field_'.$j));
	
	add_option('cforms'.$FORMCOUNT.'_required', get_option('cforms'.$no.'_required'));
	add_option('cforms'.$FORMCOUNT.'_emailrequired', get_option('cforms'.$no.'_emailrequired'));
	
	add_option('cforms'.$FORMCOUNT.'_ajax', get_option('cforms'.$no.'_ajax'));
	add_option('cforms'.$FORMCOUNT.'_confirm', get_option('cforms'.$no.'_confirm'));
	add_option('cforms'.$FORMCOUNT.'_fname', "Duplicate of form #$noDISP");
	add_option('cforms'.$FORMCOUNT.'_csubject', get_option('cforms'.$no.'_csubject'));
	add_option('cforms'.$FORMCOUNT.'_cmsg', get_option('cforms'.$no.'_cmsg'));
	add_option('cforms'.$FORMCOUNT.'_email', get_option('cforms'.$no.'_email'));
	
	add_option('cforms'.$FORMCOUNT.'_subject', get_option('cforms'.$no.'_subject'));
	add_option('cforms'.$FORMCOUNT.'_submit_text', get_option('cforms'.$no.'_submit_text'));
	add_option('cforms'.$FORMCOUNT.'_success', get_option('cforms'.$no.'_success'));
	add_option('cforms'.$FORMCOUNT.'_failure', get_option('cforms'.$no.'_failure'));
	add_option('cforms'.$FORMCOUNT.'_working', get_option('cforms'.$no.'_working'));
	add_option('cforms'.$FORMCOUNT.'_popup', get_option('cforms'.$no.'_popup'));
	
	echo '<div class="updated"><p>'.__('New form added.', 'cforms').'</p></div>';
	
	//set $no afterwards: need it to duplicate fields
	$no = $noDISP = $FORMCOUNT;


} elseif( isset($_REQUEST['uploadcformsdata']) ) {  // restore backup


  $file = $_FILES['import'];
	$err = '';
			
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
			$err = $file['error'];

	// A non-empty file will pass this test.
	if ( !( $file['size'] > 0 ) )
			$err = __('File is empty. Please upload something more substantial.', 'cforms');

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
			$err = __('Specified file failed upload test.', 'cforms');

	if ( $err <> '' ){

	  echo '<div class="updated"><p>'.__('Error:', 'cforms').' '.$err.'</p></div>';

	} else {

	  // current form
	  $noDISP = '1'; $no='';
		if( $_REQUEST['noSub']<>'1' )
			$noDISP = $no = $_REQUEST['noSub'];

		$importdata = file($file['tmp_name']);

		$cf=0;
		if ( !(strpos($importdata[0], 'cf:')===false) ) {
					update_option('cforms'.$no.'_count_fields',substr( trim($importdata[0]), 3) );
					$cf = (int) substr( trim($importdata[0]), 3);
		}
		
		if ( !(strpos($importdata[1], 'ff:')===false) ) {
					$fields = explode( '+++', substr( trim($importdata[1]), 3) );
				  for ( $i=1; $i<=$cf; $i++)  //now delete all fields from last form
							update_option('cforms'.$no.'_count_field_'.$i, $fields[$i-1] );

					//delete the rest until all gone
					while ( get_option( 'cforms'.$no.'_count_field_'.$i++ ) <> "" && $i<100 ) // 100: just to be safe
 							delete_option( 'cforms'.$no.'_count_field_'.($i-1) );
		}

		if ( !(strpos($importdata[2], 'rq:')===false) )
					update_option('cforms'.$no.'_required',substr( trim($importdata[2]), 3) );

		if ( !(strpos($importdata[3], 'er:')===false) )
					update_option('cforms'.$no.'_emailrequired',substr( trim($importdata[3]), 3) );

		if ( !(strpos($importdata[4], 'ac:')===false) )
					update_option('cforms'.$no.'_confirm',substr( trim($importdata[4]), 3) );

		if ( !(strpos($importdata[5], 'jx:')===false) )
					update_option('cforms'.$no.'_ajax',substr( trim($importdata[5]), 3) );

		if ( !(strpos($importdata[6], 'fn:')===false) )
					update_option('cforms'.$no.'_fname',substr( trim($importdata[6]), 3) );

		if ( !(strpos($importdata[7], 'cs:')===false) )
					update_option('cforms'.$no.'_csubject',substr( trim($importdata[7]), 3) );

		if ( !(strpos($importdata[8], 'cm:')===false) )
					update_option('cforms'.$no.'_cmsg', str_replace ('$n$', "\r\n",substr( trim($importdata[8]), 3) ));

		if ( !(strpos($importdata[9], 'em:')===false) )
					update_option('cforms'.$no.'_email',substr( trim($importdata[9]), 3) );

		if ( !(strpos($importdata[10], 'sj:')===false) )
					update_option('cforms'.$no.'_subject',substr( trim($importdata[10]), 3) );

		if ( !(strpos($importdata[11], 'su:')===false) )
					update_option('cforms'.$no.'_submit_text',substr( trim($importdata[11]), 3) );

		if ( !(strpos($importdata[12], 'sc:')===false) )
					update_option('cforms'.$no.'_success',str_replace ('$n$', "\r\n",substr( trim($importdata[12]), 3) ));

		if ( !(strpos($importdata[13], 'fl:')===false) )
					update_option('cforms'.$no.'_failure',str_replace ('$n$', "\r\n",substr( trim($importdata[13]), 3) ));

		if ( !(strpos($importdata[14], 'wo:')===false) )
					update_option('cforms'.$no.'_working',substr( trim($importdata[14]), 3) );

		if ( !(strpos($importdata[15], 'pp:')===false) )
					update_option('cforms'.$no.'_popup',substr( trim($importdata[15]), 3) );

	}
	
	
} elseif(isset($_REQUEST['delbutton']) && $FORMCOUNT>1 && $_REQUEST['no']<>'1') {  // 1..4d...5..6


  // current form
  $noDISP = '1'; $no='';
	if( $_REQUEST['no']<>'1' )
		$noDISP = $no = $_REQUEST['no'];

  for ( $i=(int)$noDISP; $i<$FORMCOUNT; $i++) {  //move all forms "to the left"
  
    for ( $j=1; $j<=get_option('cforms'.$i.'_count_fields'); $j++)  //delete all extra fields!
      delete_option('cforms'.$i.'_count_field_'.$j);
      
    for ( $j=1; $j<=get_option('cforms'.($i+1).'_count_fields'); $j++) { //now delete last form
      $tempo = get_option('cforms'.($i+1).'_count_field_'.$j);
      add_option('cforms'.$i.'_count_field_'.$j,$tempo);
    }
    
    $tempo = get_option('cforms'.($i+1).'_count_fields');
    update_option('cforms'.$i.'_count_fields',$tempo);
    $tempo = get_option('cforms'.($i+1).'_required');
    update_option('cforms'.$i.'_required',$tempo);
    $tempo = get_option('cforms'.($i+1).'_emailrequired');
    update_option('cforms'.$i.'_emailrequired',$tempo);

    $tempo = get_option('cforms'.($i+1).'_confirm');
    update_option('cforms'.$i.'_confirm',$tempo);
    $tempo = get_option('cforms'.($i+1).'_ajax');
    update_option('cforms'.$i.'_ajax',$tempo);
    $tempo = get_option('cforms'.($i+1).'_fname');
    update_option('cforms'.$i.'_fname',$tempo);
    $tempo = get_option('cforms'.($i+1).'_csubject');
    update_option('cforms'.$i.'_csubject',$tempo);
    $tempo = get_option('cforms'.($i+1).'_cmsg');
    update_option('cforms'.$i.'_cmsg',$tempo);
    $tempo = get_option('cforms'.($i+1).'_email');
    update_option('cforms'.$i.'_email',$tempo);

    $tempo = get_option('cforms'.($i+1).'_subject');
    update_option('cforms'.$i.'_subject',$tempo);
    $tempo = get_option('cforms'.($i+1).'_submit_text');
    update_option('cforms'.$i.'_submit_text',$tempo);
    $tempo = get_option('cforms'.($i+1).'_success');
    update_option('cforms'.$i.'_success',$tempo);
    $tempo = get_option('cforms'.($i+1).'_failure');
    update_option('cforms'.$i.'_failure',$tempo);
    $tempo = get_option('cforms'.($i+1).'_working');
    update_option('cforms'.$i.'_working',$tempo);
    $tempo = get_option('cforms'.($i+1).'_popup');
    update_option('cforms'.$i.'_popup',$tempo);
  }
  
  for ( $i=1; $i<=get_option('cforms'.$FORMCOUNT.'_count_fields'); $i++)  //now delete all fields from last form
    delete_option('cforms'.$FORMCOUNT.'_count_field_'.$i);

  delete_option('cforms'.$FORMCOUNT.'_count_fields');
  delete_option('cforms'.$FORMCOUNT.'_required');
  delete_option('cforms'.$FORMCOUNT.'_emailrequired');

  delete_option('cforms'.$FORMCOUNT.'_confirm');
  delete_option('cforms'.$FORMCOUNT.'_ajax');
  delete_option('cforms'.$FORMCOUNT.'_fname');
  delete_option('cforms'.$FORMCOUNT.'_csubject');
  delete_option('cforms'.$FORMCOUNT.'_cmsg');
  delete_option('cforms'.$FORMCOUNT.'_email');

  delete_option('cforms'.$FORMCOUNT.'_subject');
  delete_option('cforms'.$FORMCOUNT.'_submit_text');
  delete_option('cforms'.$FORMCOUNT.'_success');
  delete_option('cforms'.$FORMCOUNT.'_failure');
  delete_option('cforms'.$FORMCOUNT.'_working');
  delete_option('cforms'.$FORMCOUNT.'_popup');

  $FORMCOUNT=$FORMCOUNT-1;
  
  if ( $FORMCOUNT>1 && ((int)$_REQUEST['no'])>1 ) {
  		if( isset($_REQUEST['no']) && (int)$_REQUEST['no']<=$FORMCOUNT) // otherwise stick with the current form
  			$noDISP = $no = $_REQUEST['no'];
    	else
				$no = $noDISP = $FORMCOUNT;
  } else {
    $noDISP = '1';
    $no='';
    }
    
  update_option('cforms_formcount', (string)($FORMCOUNT));
  echo '<div class="updated"><p>'. __('Form deleted', 'cforms').'.</p></div>';


} else {


  // set paramters to default, if not exists
  $noDISP='1';
  $no='';
  if( isset($_REQUEST['switchform']) ) { // only set when hitting form chg buttons
  		if( $_REQUEST['switchform']<>'1' )
  			$noDISP = $no = $_REQUEST['switchform'];
  }
  else if( isset($_REQUEST['go']) ) { // only set when hitting form chg buttons
  		if( $_REQUEST['pickform']<>'1' )
  			$noDISP = $no = $_REQUEST['pickform'];
  }
  else{
  		if( isset($_REQUEST['noSub']) && (int)$_REQUEST['noSub']>1 ) // otherwise stick with the current form
  			$noDISP = $no = $_REQUEST['noSub'];
  }
  
}



//default: $field_count = what's in the DB
$field_count = get_option('cforms'.$no.'_count_fields');

// new field added (will actually be added below!)
if( isset($_REQUEST['AddField']) && isset($_REQUEST['field_count_submit']) )
{
		$field_count = $_REQUEST['field_count_submit'];
		$field_count ++;
		update_option('cforms'.$no.'_count_fields', $field_count);
}

// set to nothing
$usermsg='&nbsp;';

// Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || isset($_REQUEST['Submit4']) || isset($_REQUEST['AddField']) || array_search("X", $_REQUEST) ) {

	$verification=false;
	$ccbox=false;
	$emailtobox=false;
	$verification=false;
	$upload=false;

	for($i = 1; $i <= $field_count; $i++) {

	  if ($_REQUEST['field_' . $i . '_name']<>''){    // Newly "AddField" does not exist yet!

				$allgood=true;
				$name = str_replace('$#$', '$', $_REQUEST['field_' . $i . '_name']);
				$type = $_REQUEST['field_' . $i . '_type'];
				$required = 0;
				$emailcheck = 0;
				$clear = 0;


				if( $type=='verification' ){
					$allgood = $verification?false:true;
					$usermsg .= $verification?__('Only one <em>Visitor verification</em> field is permitted!', 'cforms').'<br/>':'';
					$verification=true;
				}
				if( $type=='ccbox' ){
					$allgood = $ccbox?false:true;
					$usermsg .= $ccbox?__('Only one <em>CC:</em> field is permitted!', 'cforms').'<br/>':'';
					$ccbox=true;
				}
				if( $type=='emailtobox' ){
					$allgood = $emailtobox?false:true;
					$usermsg .= $emailtobox?__('Only one <em>Multiple Recipients</em> field is permitted!'.'<br/>', 'cforms'):'';
					$emailtobox=true;
				}
				if( $type=='upload' ){
					$allgood = $upload?false:true;
					$usermsg .= $upload?__('Only one <em>File UPload</em> field is permitted!', 'cforms').'<br/>':'';
					$upload=true;
				}

						
				if(isset($_REQUEST['field_' . $i . '_required']) && in_array($type,array('textfield','textarea','checkbox','multiselectbox','selectbox','emailtobox','upload')) ) {
					$required = 1;
				}
				
				if(isset($_REQUEST['field_' . $i . '_emailcheck']) && $type == 'textfield' ){
					$emailcheck = 1;
				}

				if(isset($_REQUEST['field_' . $i . '_clear']) && in_array($type,array('textfield','textarea')) ) {
					$clear = 1;
				}
				
				if ($allgood)
						update_option('cforms'.$no.'_count_field_' . $i, $name . '$#$' . $type . '$#$' . $required. '$#$'. $emailcheck . '$#$'. $clear);

				$all_fields[$i-1]=$name . '$#$' . $type . '$#$' . $required. '$#$' . $emailcheck . '$#$'. $clear;
				
		}
	}

	update_option('cforms'.$no.'_confirm', $_REQUEST['cforms_confirm']?'1':'0');
	update_option('cforms'.$no.'_ajax', $_REQUEST['cforms_ajax']?'1':'0');
	update_option('cforms'.$no.'_popup', ($_REQUEST['cforms_popup1']?'y':'n').($_REQUEST['cforms_popup2']?'y':'n') );

	update_option('cforms'.$no.'_fname', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_fname']));
	update_option('cforms'.$no.'_csubject', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_csubject']));
	update_option('cforms'.$no.'_cmsg', preg_replace("/\\\+/", "\\",$_REQUEST['cforms_cmsg']));

  	update_option('cforms'.$no.'_required', $_REQUEST['cforms_required']);
  	update_option('cforms'.$no.'_emailrequired', $_REQUEST['cforms_emailrequired']);
	update_option('cforms'.$no.'_success', $_REQUEST['cforms_success']);
	update_option('cforms'.$no.'_failure', $_REQUEST['cforms_failure']);
	update_option('cforms'.$no.'_working', $_REQUEST['cforms_working']);

	update_option('cforms'.$no.'_submit_text', $_REQUEST['cforms_submit_text']);
	update_option('cforms'.$no.'_email', $_REQUEST['cforms_email']);
	update_option('cforms'.$no.'_subject', $_REQUEST['cforms_subject']);
	
	// did the order of fields change ?
	if(isset($_REQUEST['field_order']) && $_REQUEST['field_order']<>'') {
		$j=0;
		$temp = explode('=',$_REQUEST['field_order']);
		$order = explode(',', $temp[1]);

		//echo $temp[1]."<br>";  // debug
		$tempcount = isset($_REQUEST['AddField'])?($field_count-1):($field_count);
		while($j < $tempcount)
		{
	      $new_f = substr($order[$j],0,-1);
				if ( $j <> $new_f )
						update_option('cforms'.$no.'_count_field_'.($j+1),$all_fields[$new_f]);
					  //echo "$j=$new_f :: ".$all_fields[$j]." == ".$all_fields[$new_f]."<br/>";  //debug
		$j++;
		}

	} //if order changed
}


// delete field if we find one and move the rest up
$deletefound = 0;
if(strlen(get_option('cforms'.$no.'_count_field_' . $field_count)) > 0) {

	$temp_count = 1;
	while($temp_count <= $field_count) {
	
		if(isset($_REQUEST['DeleteField' . $temp_count])) {
			$deletefound = 1;
			update_option('cforms'.$no.'_count_fields', ($field_count - 1));
		}
		
		if($deletefound && $temp_count<$field_count) {
			$temp_val = get_option('cforms'.$no.'_count_field_' . ($temp_count+1));
			update_option('cforms'.$no.'_count_field_' . ($temp_count), $temp_val);
		}
		
		$temp_count++;
	} // while

	if($deletefound == 1) {  //now delete
	  delete_option('cforms'.$no.'_count_field_' . $field_count);
		$field_count--;
	}

} //if

//
// prep drop down box for form selection
//

$formlistbox = '<select id="pickform" name="pickform">';

for ($i=1; $i<=$FORMCOUNT; $i++){

	$j   = ( $i > 1 )?$i:'';
	$sel = ($noDISP==$i)?' selected="selected"':'';

  $formlistbox .= '<option value="'.$i.'" '.$sel.'>'.get_option('cforms'.$j.'_fname').'</option>';
}
$formlistbox .= '</select><input type="submit" class="allbuttons go" name="go"  value="'.__('Go', 'cforms').'">';

?>

<div class="wrap"><a id="top"></a><img src="<?php echo $cforms_root; ?>/images/p1-title.jpg">

	<p><?php echo str_replace('[url]','?page='. $plugindir.'/cforms-help.php#inserting',__('This plugin allows you <a href="[url]">to insert</a> one or more custom designed contact forms, which on submission (preferably via ajax) will send the visitor info via email and optionally store the feedback in the database, too.', 'cforms')); ?></p>

	<form name="chgform" method="post" action="#">
			<!-- <span class="bignumber">#'.$noDISP.'</span> -->
			<div class="chgformbox">
				<div class="chgL"><?php _e('Your forms:', 'cforms'); echo $formlistbox; ?></div>
				<div class="chgR">
					<input class="allbuttons addbutton" type="submit" name="addbutton" value="<?php _e('add form', 'cforms'); ?>"/>&nbsp;&nbsp;
			    	<input class="allbuttons dupbutton" type="submit" name="dupbutton" value="<?php _e('duplicate form', 'cforms'); ?>"/>&nbsp;&nbsp;
			    	<?php
			      if ( (int)$noDISP > 1)
			        echo '<input class="allbuttons delbutton" type="submit" name="delbutton" value="'.__('delete THIS form(!)', 'cforms').'"/>';
			      ?>
				</div>
				<div class="chgM">
					<?php
			    	for ($i=1; $i<=$FORMCOUNT; $i++) {
			    		$j   = ( $i > 1 )?$i:'';
			     		echo '<input title="'.get_option('cforms'.$j.'_fname').'" class="allbuttons chgbutton'.(($i <> $noDISP)?'':'hi').'" type="submit" name="switchform" value="'.$i.'"/>';
		     		}
			    	?>
			 </div>
			</div>

      <input type="hidden" name="no" value="<?php echo $noDISP; ?>"/>
  </form>



	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#anchorfields">
		<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>" />

			<p class="areadesc">
				<?php _e('If you have many different forms, you may give each an optional name to better identify incoming emails:', 'cforms') ?>
			</p>
			<p class="mainoptions">
					<input style="float:right;" type="submit" name="Submit1" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#';" />
					<span class="bignumber"><?php _e('Form Name', 'cforms') ?></span>
						<input name="cforms_fname" class="cforms_fname" size="40" value="<?php echo (get_option('cforms'.$no.'_fname'));  ?>" />
						<input type="checkbox" class="cforms_ajax" name="cforms_ajax" <?php if(get_option('cforms'.$no.'_ajax')=="1") echo "checked=\"checked\""; ?>/>
					<span class="bignumber"><?php _e('Ajax enabled', 'cforms') ?></span>
			</p>


			<p class="backup">
					<input type="submit" name="savecformsdata" class="allbuttons backupbutton"  value="<?php _e('Backup This Form', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
					<label for="upload"><?php _e(' or restore previously saved settings:', 'cforms'); ?></label>
					<input type="file" id="upload" name="import" size="25" />
					<input type="submit" name="uploadcformsdata" class="allbuttons restorebutton" value="<?php _e('Restore Settings', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
			</p>


	<a id="anchorfields"></a>
	<fieldset class="cformsoptions">
		<p class="cflegend"><?php _e('Form Fields', 'cforms') ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#fields',__('Please see the help section for information on how to deploy the <a href="[url]">supported fields</a>.', 'cforms')); ?></p>

		<p class="ex"><?php _e('For the <em>auto confirmation</em> feature to work, make sure to mark at least one field <code class="codehighlight">Is Email</code>, otherwise <strong>NO</strong> auto confirmation email will be sent out! If multiple fields are checked "Is Email", only the first in the list will receive a notification.', 'cforms') ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#hfieldsets',__('How to structure your form using <strong>FIELDSETS</strong>? Check out the <a href="[url]">help section</a> for a sample screenshot depicting proper setup.', 'cforms')); ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#single',__('<strong>Default values</strong> can be set for single & multi-line fields. <a href="[url]">See help</a>. In case you\'d like to <strong>auto clear</strong> a default value <em>on field focus</em>, set <code class="codehighlight">Auto Clear</code>.', 'cforms')); ?></p>

		<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#regexp',__('Single & Multi line input fields support custom <strong>regular expressions</strong> for specific field validation! Check the <a href="[url]">help</a> section for examples and make sure to check <code class="codehighlight">Is Required</code>.', 'cforms')); ?></p>

		<p id="cformswarning"><?php echo $usermsg; ?></p>

		<div class="tableheader">
			<span class="field1th"><?php _e('No.', 'cforms'); ?></span>
			<span class="field2th"><?php _e('Field Name', 'cforms'); ?></span>
			<span class="field3th"><?php _e('Type', 'cforms'); ?></span>
			<span class="field4th"><?php _e('Is Required', 'cforms'); ?></span>
			<span class="field5th"><?php _e('Is E-Mail', 'cforms'); ?></span>
			<span class="field6th"><?php _e('Auto Clear', 'cforms'); ?></span>
		</div>
			
			<div id="cformsfieldsbox">
					<div class="dbx-group" id="cformsfields">
						<?php

						// pre-check for verification field
						$ccboxused=false;
						$emailtoboxused=false;
						$verificationused=false;
						$uploadused=false;
						for($i = 1; $i <= $field_count; $i++) {

								$allfields[$i] = get_option('cforms'.$no.'_count_field_' . $i);
								
								if ( strpos($allfields[$i],'verification') )
										$verificationused = true;
								if ( strpos($allfields[$i],'emailtobox') )
										$emailtoboxused = true;
								if ( strpos($allfields[$i],'ccbox') )
										$ccboxused = true;
								if ( strpos($allfields[$i],'upload') )
										$uploadused = true;

						}

						$alternate=' rowalt';
						
						for($i = 1; $i <= $field_count; $i++) {
								$field_stat = explode('$#$', $allfields[$i]);
								$field_name = __('New Field', 'cforms');
								$field_type = 'textfield';
								$field_required = '0';
								$field_emailcheck = '0';
								$field_clear = '0';

								if(sizeof($field_stat) >= 3) {
									$field_name = stripslashes(htmlspecialchars($field_stat[0]));
									$field_type = $field_stat[1];
									$field_required = $field_stat[2];
									$field_emailcheck = $field_stat[3];
									$field_clear = $field_stat[4];
								}
								else if(sizeof($field_stat) == 1){
									add_option('cforms'.$no.'_count_field_' . $i, __('New Field$#$textfield$#$0$#$0$#$0', 'cforms'));
								}

            switch ( $field_type ) {
							case 'emailtobox':
										$specialclass = 'style="background:#CBDDFE"';
										break;
							case 'ccbox':
										$specialclass = 'style="background:#D8FFCA"';
										break;
							case 'verification':
										$specialclass = 'style="background:#FFCDCA"';
										break;
							case 'textonly':
										$specialclass = 'style="background:#f0f0f0"';
										break;
							case 'fieldsetstart':
							case 'fieldsetend':
										$specialclass = 'style="background:#ECFEA5"';
										break;
							case 'upload':
										$specialclass = 'style="background:#FAF0AF"';
										break;
							default:
										$specialclass = '';
										break;
						}
						
						$alternate = $alternate==' rowalt'?'':' rowalt';
						?>

						<div class="dbx-box">
									<a class="dbx-handle" href="javascript:void(0)" style="float:left;">&nbsp;</a>
									<ul class="dbx-cforms<?php echo $alternate?>">
										<li class="fieldno"><?php echo $i; ?></li>
										<li class="fieldname"><input class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend' || $field_type == 'verification')?'--':$field_name; ?>" /></li>
										<li class="fieldtype">
												<select class="selfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_type" id="field_<?php echo($i); ?>_type">
													<option value="textonly" <?php echo($field_type == 'textonly'?' selected="selected"':''); ?> ><?php _e('Text only (no input)', 'cforms'); ?></option>
													<option value="textfield" <?php echo($field_type == 'textfield'?' selected="selected"':''); ?> ><?php _e('Single line of text', 'cforms'); ?></option>
													<option value="textarea" <?php echo($field_type == 'textarea'?' selected="selected"':''); ?> ><?php _e('Multiple lines of text', 'cforms'); ?></option>
													<option value="checkbox" <?php echo($field_type == 'checkbox'?' selected="selected"':''); ?> ><?php _e('Check Box', 'cforms'); ?></option>
													<option value="selectbox" <?php echo($field_type == 'selectbox'?' selected="selected"':''); ?> ><?php _e('Select Box', 'cforms'); ?></option>
													<option value="multiselectbox" <?php echo($field_type == 'multiselectbox'?' selected="selected"':''); ?> ><?php _e('Multi Select Box', 'cforms'); ?></option>
													<option value="radiobuttons" <?php echo($field_type == 'radiobuttons'?' selected="selected"':''); ?> ><?php _e('Radio Buttons', 'cforms'); ?></option>
													<?php if ( !$ccboxused || $field_type=="ccbox" ) : ?>
													<option value="ccbox" <?php echo($field_type == 'ccbox'?' selected="selected"':''); ?> ><?php _e('CC: option for user', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$emailtoboxused || $field_type=="emailtobox" ) : ?>
													<option value="emailtobox" <?php echo($field_type == 'emailtobox'?' selected="selected"':''); ?> ><?php _e('Multiple Recipients', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$verificationused || $field_type=="verification" ) : ?>
														<option value="verification" <?php echo($field_type == 'verification'?' selected="selected"':''); ?> ><?php _e('Visitor verification', 'cforms'); ?></option>
													<?php	endif; ?>
													<?php if ( !$uploadused || $field_type=="upload" ) : ?>
														<option value="upload" <?php echo($field_type == 'upload'?' selected="selected"':''); ?> ><?php _e('File Upload Box', 'cforms'); ?></option>
													<?php	endif; ?>
													<option value="fieldsetstart" <?php echo($field_type == 'fieldsetstart'?' selected="selected"':''); ?> ><?php _e('New Fieldset', 'cforms'); ?></option>
													<option value="fieldsetend" <?php echo($field_type == 'fieldsetend'?' selected="selected"':''); ?> ><?php _e('End Fieldset', 'cforms'); ?></option>
												</select>
						            <?php
						              if($field_count > 1)
									          echo '<input class="xbutton" type="submit" name="'.__('Delete field', 'cforms').$i.'" value="" />';
						            ?>
										</li>
										<li class="fieldisreq">
											<?php if( !in_array($field_type,array('radiobuttons','fieldsetstart','fieldsetend','ccbox','verification','textonly')) ) {
													?><input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_required" value="required"  <?php echo($field_required == '1'?' checked="checked"':''); ?> /><?php
														} else if ( !in_array($field_type,array('checkbox','multiselectbox','selectbox','radiobuttons','fieldsetstart','fieldsetend','ccbox','verification','textonly')) ) { echo('Required <input type="hidden" name="field_' . $i . '_required" value="required" />'); } ?>
										&nbsp;</li>
										<li class="fieldisemail">
											<?php if( in_array($field_type,array('upload','textarea','checkbox','multiselectbox','selectbox','radiobuttons','fieldsetstart','fieldsetend','ccbox','emailtobox','verification','textonly')) ) echo '&nbsp;'; else { ?>
														<input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_emailcheck" value="required"  <?php echo($field_emailcheck == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
										<li class="fieldclear">
											<?php if( ! in_array($field_type,array('textarea','textfield')) ) echo '&nbsp;'; else { ?>
														<input class="chkfld" type="checkbox" name="field_<?php echo($i); ?>_clear" value="required"  <?php echo($field_clear == '1'?' checked="checked"':''); ?> /><?php }?>
										&nbsp;</li>
									</ul>
						</div> <!--box-->
						
				<?php	}	// for  ?>
			
				</div> <!--group-->
		</div> <!--cformsfieldsbox-->

		<div class="addfieldbox"><input type="submit" name="AddField" class="allbuttons addfield" value="<?php _e('Add another field &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchorfields';" /></div>
		<input type="hidden" name="field_order" value="" />
		<input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />
	</fieldset>

	<p align="center" style="font-size:11px;margin:20px 0 0;"><em><?php _e('Grab the circle to the left of each field to change the order of the fields (drag&drop)', 'cforms'); ?></em></p>
	<p align="right" style="margin-top:0"><input type="submit" name="Submit2" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchorfields';" /></p>



		<a id="anchormessage"></a>
		<fieldset class="cformsoptions">
			<p class="cflegend"><?php _e('Messages, Text and Button Label', 'cforms') ?></p>

			<p><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-global-settings.php#visitorv',__('These are the messages displayed to the user on successful (or failed) form submission. These messages are form specific, a general message for entering a wrong <strong>visitor verification code</strong> can be found <a href="[url]">here</a>.', 'cforms')); ?></p>

			<p class="ex"><?php _e('Please <strong>do not use HTML code</strong> in these fields instead, adjust the (cforms.css) stylesheet to your needs. Line breaks and quotes are fine. The actual success & failure message can in addition be shown in a popup <strong>alert box</strong>. This may come handy in case your forms are extremely long and the user can\'t see the submission status at the top anymore.', 'cforms') ?></p>

			<br/>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Waiting message', 'cforms'); ?></div>
				<div class="optionsboxR"><input name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_working')));  ?>" /></div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><?php _e('"required" label', 'cforms'); ?></div>
				<div class="optionsboxR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_required'))); ?>"/></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('"valid email required" label', 'cforms'); ?></div>
				<div class="optionsboxR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_emailrequired'))); ?>"/></div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><?php _e('<strong>Success message</strong><br/>form filled out correctly', 'cforms'); ?></div>
				<div class="optionsboxR">
					<textarea style="float:left" name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_success'))); ?></textarea>
					<div style="float:left"><input type="checkbox" id="cforms_popup1" name="cforms_popup1" <?php if(substr(get_option('cforms'.$no.'_popup'),0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup1"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
				</div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('<strong>Failure message</strong><br/>missing fields or wrong field formats<br/>(regular expr.)', 'cforms'); ?></div>
					<div class="optionsboxR"><textarea style="float:left" name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_failure'))); ?></textarea>
					<div style="float:left"><input type="checkbox" id="cforms_popup2" name="cforms_popup2" <?php if(substr(get_option('cforms'.$no.'_popup'),1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup2"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
				</div>
			</div>
			<div class="optionsbox" style="margin-top:10px;">
				<div class="optionsboxL"><?php _e('Submit button text', 'cforms'); ?></div>
				<div class="optionsboxR"><input name="cforms_submit_text" id="cforms_submit_text" value="<?php echo (get_option('cforms'.$no.'_submit_text'));  ?>" /></div>
			</div>

		</fieldset>
		<p align="right"><input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchormessage';" /></p>



		<a id="anchoremail"></a>
		<fieldset class="cformsoptions">
			<p class="cflegend"><?php _e('Form Admin / Email Options', 'cforms') ?></p>

			<p><?php _e('These settings will be used for the email sent to you. Both "<strong>xx@yy.zz</strong>" and "<strong>abc &lt;xx@yy.zz&gt;</strong>" formats are valid, but check if your mailserver does accept the format of choice!"', 'cforms') ?></p>

			<p class="ex"><?php echo str_replace('[url]','?page='.$plugindir.'/cforms-help.php#multirecipients',__('More than one "<strong>form admin</strong>"? Simply add additional email addresses separated by a <strong style="color:red">comma</strong>. &nbsp; <em><u>Note:</u></em> &nbsp; If you want the visitor to choose from any of these per select box, you need to add a corresponding "<code class="codehighlight">Multiple Recipients</code>" input field <a href="#anchorfields">above</a> (see the HELP section for <a href="[url]">details</a> on the entry format expected!). If <strong>no</strong> "Multiple Recipients" input field is defined above, the submitted form data will go out to <strong>every email recipient</strong>!', 'cforms')); ?></p>
			<br/>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Email address(es)', 'cforms') ?></div>
				<div class="optionsboxR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_email'))); ?>" /></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Subject', 'cforms') ?></div>
				<div class="optionsboxR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo(get_option('cforms'.$no.'_subject')); ?>" /></div>
			</div>
		</fieldset>

		<a name="autoconf" id="autoconf"></a>
		<fieldset class="cformsoptions">
			<p class="cflegend"><?php _e('Auto Confirmation Options', 'cforms') ?></p>

			<p><?php _e('These settings apply to an auto response/confirmation sent to the visitor. If your form includes a "CC me" field <strong>AND</strong> the visitor checked it, no confirmation email is sent!', 'cforms') ?></p>

			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Enable', 'cforms') ?></div>
				<div class="optionsboxR"><input type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php if(get_option('cforms'.$no.'_confirm')=="1") echo "checked=\"checked\""; ?>/></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Subject', 'cforms') ?></div>
				<div class="optionsboxR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_csubject'))); ?>" /></div>
			</div>
			<div class="optionsbox">
				<div class="optionsboxL"><?php _e('Message', 'cforms') ?></div>
				<div class="optionsboxR"><textarea name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_cmsg'))); ?></textarea></div>
			</div>
		</fieldset>
		<p align="right"><input type="submit" name="Submit4" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchoremail';" /></p>


		</form>


	<?php cforms_footer(); ?>
</div>
