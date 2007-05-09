<?php
/*
Plugin Name: cforms
Plugin URI: http://www.deliciousdays.com/cforms-plugin
Description: cforms offers convenient deployment of multiple contact forms throughout your blog or even on the same page. The form submission utilizes AJAX, however, falls back to a standard method in case AJAX/Javascript is not supported.
Author: Oliver Seidel
Version: 4.2
Author URI: http://www.deliciousdays.com
*/

/*
Copyright 2006  Oliver Seidel   (email : oliver.seidel@deliciousdays.com)
/*

v4.2 (maintenance & bug fixes)

*) NOTE: the format for check box groups has been enhanced, check HELP!
*) bugfix: "Subject for Email" could not be saved "Is Required"
*) other: "Subject for Email", user definable subject is now appended
*) other: "Subject for Email" is now part of the email form submission body
*) other: form structure re-done! XHTML'fied
*) other: streamlined CSS
*) other: added a warning msg re: "Show messages" settings

v4.1 (features)
*) feature: support for shown but disabled form element
*) feature: "user message" positioning, now optionally at the bottom of the form
*) feature: "multi-select" via check boxes, grouped check boxes
*) feature: new special field: subject field
*) other: revised and cleaned up Help! section

v4 (features & bugfix)
*) feature: captcha support for additional SPAM protection
*) feature: select & configure stylesheets via admin UI
*) bugfix: IE margin-bottom hover bug
*) bugfix: deleting form fields (on the general form config page) was broken due a 
     new bug that was introduced as part of the localization effort
*) other: change the INSERT queries using LAST_INSERT_ID() due to overly sensitive
     SQL servers.
*) other: more house keeping

v3.5 (mostly maintenance)
*) feature: slightly enhanced Tracking page ("delete" now also removes attachments)
	tracking data view now permits selective deletion of submission entries
*) feature: text fields can optionally be auto cleared on focus (if browser is JS enabled)
*) feature: attachments (uploads) are now stored on the server and can be accessed 
	via the "Tracking" page
*) feature: added optional ID tracking to forms (& emails sent out)
*) bugfix: editor button wouldn't show due to wrong image path
*) bugfix: order of fields on the "Tracking" page fixed, to ensure an absolute order
	NOTE: as part of the install/update either deactivate/reactive the plugin or delete 
    the existing Tracking tables, to make use of the new table structure)
*) bugfix: due to a WP bug, the use of plugin_basename had to be adjusted
*) bugfix: fixed support for non-utf8 blogs ( mb_convert_encoding etc.)
*) other: code cleanup (big thanks to Sven!) to allow proper localization
			current languages supported: 
			English, default
			German, provided by Sven Wappler
*) other: changed data counter (column 1) on the Tracking page to reflect unique 
    form submission ID, that a visitor could possibly reference.
   
v3.4
*) feature: multi-select fields
*) feature: dynamic forms (form creation at run-time)
*) bugfix: minor display bug on admin page: "add new field" button
*) bugfix: fixed a CSS bug to better support 3 column WP themes 
   (w/ middle column not floated)

v3.3
*) feature: "file upload field" can now be mandatory
*) feature: additional select box for more intuitive form selection
*) drop down "-" option for multi recipients
*) bugfix: select (drop down) boxes did not save values for non ajax method
*) bugfix: when using "multi-recipients" field & first entry used, email would
		still go out to everyone
*) bugfix: charsets other than UTF-8 caused issues with special characters in emails
*) other: added form name as hover text for form buttons

v3.2.2
*) feature: most attachment types (images, docs etc) are now recognized
*) bugfix: not really a bug, but no "extra" attachments anymore
*) bugfix: more special characters in response messages

v3.2
*) feature: file upload; only works with non-ajax send method (chosen autoamtically)
		due to HTML constraints. ajax support does NOT need to be explicitly disabled
*) feature: select boxes (drop downs) now can be "required" -> to support situations,
		where you don't want a default value to kick in, but want the visito to make a choice!
		see HELP! section for more info on how to use this new feature
*) feature: checkboxes : now can be "required" -> for "I have read the above" type
		scenarious, where the user has to comply/agree to a statement
*) feature: radio buttons, you can now click on the labels to toggle the selection
*) feature: radio & select boxes (drop down): now accept a "display value" & a "submit value"
		see HELP! section for more info
*) feature: "submit button" is now disabled after sending to prevent multiple
		submissions in case the web servers response is delayed (Ajax!)
*) feature: download supports both CSV and TXT (tab delimited)
*) bugfix: time correction in email (now considers blog time/date configuration)
*) bugfix: failure and success msgs would not show special characters properly
*) bugfix: “database tables found msg” would always show when settings were saved
*) bugfix: labels (left of an input field) would not display special chars correctly
*) other: renamed a few functions to avoid conflicts with other plugins
*) other: modified checkboxes: text to the right is by default "clickable"
*) other: W3C XHTML compliance now fully supported even when using REG EXPRESSIONS!

v3
*) bugfix: changed the priority of the plugin: fixes layout issues due to wpautop
*) bugfix: fixed ajax (email) issues with CC: and Visitor verification fields
*) bugfix: fixed a few minor layout issues
*) UI: new admin uinterface
*) feature: full support for  for role-manager support, see here for a current release:
	 http://www.im-web-gefunden.de/wordpress-plugins/role-manager/
*) feature: database tracking of form input & download as a CSV file
*) feature: backup and restore individual form settings (doesn't affect plugin-wide settings)
*) feature: erase all cforms data before deactivating/uninstalling the plugin
*) feature: added a new special field: "textonly" to add fully customizable paragraphs to your forms
*) feature: verification question to counteract spam
*) feature: custom regular expressions for single line input fields
     usage: separate regexp via pipe '|' symbol:  fieldname|defaultval|regexp
		        e.g. Phone|+49|^\+?[0-9- \(\)]+$
*) new menu structure (now top level menu!)
*) admin code clean up
*) if a NON DEFAULT permalink structure is used, the page the form was submitted from
	 if being included in the email
*) changed the order of error msgs: first field validation / verification code error
*) verification codes accept answers case insensitive

v2.5
*) feature: multiple email recipients ("form admins"): mass sending & selective sending
		by (visitor)
*) CFORMS.CSS includes custom settings for form #2 (to see it in action, create
		a second form (#2) with one FIELDSET and a few input fields)
*) feature: order of fields; fields can now be sorted via drag & drop
*) feature: forms can be duplicated
*) Fully integrated with TinyMCE & code editor. FF: hover over form placeholder
		and form object will be displayed. IE: select form placeholder and click on
		the cforms editor button
*) feature: default values for line & multi line input fields: use a "|" as a delimiter
*) UI: "Update Settings" returns directly to config section
*) bugfix: quotes and single quotes in input fields fixed
*) bugfix: adding/deleting fields will respect (=save) other changes made
*) UI: all form fields can now be deleted up until the last field
*) feature: CC optional for visitor / if CC'ed not auto confirmatin will be sent add'l
*) feature: enhanced email layout - supporting defined fieldset
*) feature: REPLY-TO set for emails to both form admins & visitors (CC'ed)
*) non ajax form submisssion: page reloads and now jumps directly to form (& success msg)
*) code clean up and a handful of minor big fixes

v2.1.1
*) bugfix: IE not showing AJAX / popup message stati
*) bugfix: send button jumping to the left after submitting
*) check boxes: text can now be displayed both to the left and right

v2.1
*) fieldsets are now supported: CSS: .cformfieldsets addresses all sets,
		cformfieldsetX (with X=1,2,3...) individual ones.
*) form code clean-up: more standardized with a minimum on necessary elements and
		got rid of all the legacy DIVs
*) javascript has been "outsourced" making your html so much nicer :)

v2
!!) when upgrading to v2:
		!) please edit each form on your plugins config page to verify that the email
			field is checked with "Is Email" to ensure email verification
*) additional form fields: checkboxes, radio buttons and select fields
		*) please note the expected "Field Name" entry format, separating input field items 
		   form the field name: i.e. radio buttons: field-name#button1#button2#button3#...
*) ajax support can be optionally turned off
*) a form can now have as few input fields as two
*) more flexibilty in choosing email entry field. NOTE: if you have multiple email
		fields in your form, only the first will be used for sending the auto confirmation to
*) "valid email required" placeholder added to indicate required input format for email fields
*) optional popup window for user messages (may be helpful for very long forms)
*) code cleanup

v1.90
*) email header correction: "From:" doesn't claim to be visitor's email
address anymore this should fix most paranoid mail server

v1.81
*) form name added for either email filtering or simply better differentiation
*) admin email: can now be just "xx@yy.zz" or "abc <xx@yy.zz>" (from name removed)
*) changes to email header: simplified and "WP compliant"
*) added to cforms.css: success and failure styles
*) bug fix related to the use of a single forms (#2 and up) and insertion of ajax code
*) FINALLY fixed "CR"s for multi-line response messages (success & failure fields)

v1.71
*) HTML bug resolved & localization for "waiting message"
*) default value for email recipient is now the blog admins' email address
*) added a function call to insert form anywhere on your blog
*) added new version support

v1.6
*) bug fixes: email/form functionality w/ standard send mechanism

v1.5
*) clean up, external css, multiple forms support & user auto confirmation

*/

load_plugin_textdomain('cforms');

//http://trac.wordpress.org/ticket/3002
$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

require		(dirname(__FILE__) . '/buttonsnap.php');
require_once(dirname(__FILE__) . '/editor.php');
include_once(dirname(__FILE__) . '/wp.php');

//need this for captchas
session_start();

### download form settings or tracked form data ??
add_action('init', 'download_cforms');
function download_cforms() {

	if( strpos($_SERVER['HTTP_REFERER'], $plugindir.'/cforms') !== false ) {
		global $wpdb;

		$br="\n";
		$buffer='';
		if( isset($_REQUEST['savecformsdata']) ) {
		
			// current form
			$noDISP = '1'; $no='';
			if( $_REQUEST['noSub']<>'1' )
				$noDISP = $no = $_REQUEST['noSub'];
				
			$buffer .= 'cf:'.get_option('cforms'.$no.'_count_fields') . $br;
			$buffer .= 'ff:';
			for ( $i=1; $i<=get_option('cforms'.$no.'_count_fields'); $i++)  //now delete all fields from last form
			$buffer .= get_option('cforms'.$no.'_count_field_'.$i)."+++";
			
			$buffer .= $br;
			$buffer .= 'rq:'.get_option('cforms'.$no.'_required') . $br;
			$buffer .= 'er:'.get_option('cforms'.$no.'_emailrequired') . $br;
			
			$buffer .= 'ac:'.get_option('cforms'.$no.'_confirm') . $br;
			$buffer .= 'jx:'.get_option('cforms'.$no.'_ajax') . $br;
			$buffer .= 'fn:'.get_option('cforms'.$no.'_fname') . $br;
			$buffer .= 'cs:'.get_option('cforms'.$no.'_csubject') . $br;
			$buffer .= 'cm:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_cmsg')) . $br;
			$buffer .= 'em:'.get_option('cforms'.$no.'_email') . $br;
			
			$buffer .= 'sj:'.get_option('cforms'.$no.'_subject') . $br;
			$buffer .= 'su:'.get_option('cforms'.$no.'_submit_text') . $br;
			$buffer .= 'sc:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_success')) . $br;
			$buffer .= 'fl:'.preg_replace ( '|\r\n|', '$n$',get_option('cforms'.$no.'_failure')) . $br;
			$buffer .= 'wo:'.get_option('cforms'.$no.'_working') . $br;
			$buffer .= 'pp:'.get_option('cforms'.$no.'_popup') . $br;
			$buffer .= 'sp:'.get_option('cforms'.$no.'_showpos') . $br;
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=\"formconfig.txt\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " .(string)(strlen($buffer)) );
			print $buffer;
			exit();
		} // form settings
		else if( isset($_REQUEST['downloadselectedcforms']) && isset($_POST['entries']) ) {

			$sub_id = implode(',', $_POST['entries']);
			$sql = "SELECT *, form_id FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id in ($sub_id) AND sub_id=id ORDER BY sub_id DESC";
			$entries = $wpdb->get_results($sql);
		
			$sub_id='';
			foreach ($entries as $entry){
		
				if( $sub_id<>$entry->sub_id ){

					if ( $sub_id<>'' ) 
						$buffer = substr($buffer,0,-1) . $br;
						
					$sub_id = $entry->sub_id;

					$format = ($_REQUEST['downloadformat']=="csv")?",":"\t";
					
					$buffer .= '"Form: ' . get_option('cforms'.$entry->form_id.'_fname'). '"'. $format .'"'. $entry->date .'"' . $format;
				}

				$buffer .= '"' . str_replace('"','""', utf8_decode($entry->field_val)) . '"' . $format;
		
			}
	
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: text/download");
			header("Content-Type: text/csv");
			header("Content-Disposition: attachment; filename=\"formdata." . $_REQUEST['downloadformat'] . "\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " .(string)(strlen($buffer)) );
			print $buffer;	
			exit();
		} // tracked form submissions

	} // on a cforms page?
}



if (isset($_GET['activate']) && $_GET['activate'] == 'true') {

		/*file upload*/
		add_option('cforms_upload_dir', ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/attachments');
		add_option('cforms_upload_ext', 'txt,zip,doc,rtf,xls');
		add_option('cforms_upload_size', '1024');
		add_option('cforms_upload_err1', __('Generic file upload error. Please try again', 'cforms'));
		add_option('cforms_upload_err2', __('File is empty. Please upload something more substantial.', 'cforms'));
		add_option('cforms_upload_err3', __('Sorry, file is too large. You may try to zip your file.', 'cforms'));
		add_option('cforms_upload_err4', __('File upload failed. Please try again or contact the blog admin.', 'cforms'));
		add_option('cforms_upload_err5', __('File not accepted, file type not allowed.', 'cforms'));

		/*for default form*/
		add_option('cforms_count_field_1', __('My Fieldset$#$fieldsetstart$#$0$#$0$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_2', __('Your Name|Your Name$#$textfield$#$1$#$0$#$1$#$0', 'cforms'));
		add_option('cforms_count_field_3', __('Email$#$textfield$#$1$#$1$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_4', __('Website|http://$#$textfield$#$0$#$0$#$0$#$0', 'cforms'));
		add_option('cforms_count_field_5', __('Message$#$textarea$#$0$#$0$#$0$#$0', 'cforms'));

		/*form verification questions*/
		add_option('cforms_sec_qa', __('What color is snow?=white', 'cforms'). "\r\n" . __('The color of grass is=green', 'cforms'). "\r\n" . __('Ten minus five equals=five', 'cforms'));
		add_option('cforms_formcount', '1');
		add_option('cforms_show_quicktag', '1');
		add_option('cforms_count_fields', '5');
		add_option('cforms_required', __('(required)', 'cforms'));
		add_option('cforms_emailrequired', __('(valid email required)', 'cforms'));

		add_option('cforms_confirm', '0');
		add_option('cforms_ajax', '1');
		add_option('cforms_fname', __('Your default form', 'cforms'));
		add_option('cforms_csubject', __('Re: Your note', 'cforms'));
		add_option('cforms_cmsg', __('Dear Sender,', 'cforms') . "\n". __('Thank you for your note!', 'cforms') . "\n". __('We will get back to you as soon as possible.', 'cforms') . "\n\n");
		add_option('cforms_email', get_bloginfo('admin_email'));

		add_option('cforms_subject', __('A comment from a site visitor', 'cforms'));
		add_option('cforms_submit_text', __('Send Comment', 'cforms'));
		add_option('cforms_success', __('Thank you for your comment!', 'cforms'));
		add_option('cforms_failure', __('Please fill in all the required fields.', 'cforms'));
		add_option('cforms_codeerr', __('Please double-check your verification code.', 'cforms'));
		add_option('cforms_working', __('One moment please...', 'cforms'));
		add_option('cforms_popup', 'nn');
		add_option('cforms_showpos', 'yn');
		add_option('cforms_database', '0');

		add_option('cforms_css', 'cforms.css');

		add_option('cforms_subid', '0');
		add_option('cforms_subid_text', '(Submission ID#{id})' );
		
		/* updates existing tracking db */
		if ( $wpdb->get_var("show tables like '$wpdb->cformsdata'") == $wpdb->cformsdata ) {

			// Fetch the table column structure from the database
			$tablefields = $wpdb->get_results("DESCRIBE {$wpdb->cformsdata};");

            $afield = array();
			foreach($tablefields as $field)
                array_push ($afield,$field->Field); 
            
            if ( !in_array('f_id', $afield) ) {
    			$sql = "ALTER TABLE " . $wpdb->cformsdata . " 
    					  ADD f_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    					  CHANGE field_name field_name varchar(100) NOT NULL default '';";
    			$wpdb->query($sql);
              	echo '<div id="message" class="updated fade"><p><strong>' . __('Existing cforms Tracking Tables updated.', 'cforms') . '</strong></p></div>';
            }
            
        }
}



// Can't use WP's function here, so lets use our own
if ( !function_exists('getip') ) :
function getip()
{
	if (isset($_SERVER))
	{
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))  $ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))		$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		else																					$ip_addr = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) 			$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )	  			$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		else																	  			$ip_addr = getenv( 'REMOTE_ADDR' );
	}
return $ip_addr;
}
endif;


//
// ajax submission of form
//
function cforms_submitcomment($content) {

	global $wpdb;

	$segments = explode('$#$', $content);
	$params = array();


	for($i = 1; $i <= sizeof($segments); $i++) {
		$params['field_' . $i] = $segments[$i];
	}

	if ( $segments[0]=='1' ) $no=''; else $no = $segments[0];


	if ( get_option('cforms'.$no.'_fname') <> '' ){
		$title   = "\n" . __('A new submission (form: ','cforms'). '"' . get_option('cforms'.$no.'_fname') . "\")\n";
		$page    = substr( $_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'?')-1);
		$page    = (trim($page)=='')?'/':trim($page);
		$message = $title . str_repeat('=', strlen($title)-2 ) . "\n" .
								 __('Submitted on: ','cforms') . mysql2date(get_option('date_format'), current_time('mysql')) . ' @ ' . gmdate(get_option('time_format'), current_time('timestamp')) . "\n" .
								 __('Via: ','cforms') . $page . "\n" .
								 __('By ','cforms') . getip() . __(" (visitor IP)",'cforms') . "\n\n";
	} else
		$message='';

	$track = array(); 
 	$to_one = "-1";
  	$ccme = false;
	$field_email='';
	$vsubject='';
	$off=0;

	for($i = 1; $i <= sizeof($params)-1; $i++) {

			$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));


			// filter non input fields
			while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {
																				
					if ( $field_stat[1] <> 'textonly' ){ // include and make only fieldsets pretty!

							//just for email looks
							$space='-'; $n = (62 - strlen($field_stat[0])) / 2;
							if ( strlen($field_name) < 58 )
								$space = str_repeat("-", $n );
							$message .= substr("\n$space$field_stat[0]$space",0,60) . "\n\n";

					}
					
		   			//get next in line...
					$off++;
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					
					if( $field_stat[1] == '')
						break 2; // all fields searched, break both while & for

			}

			$field_name = $field_stat[0];
			$field_type = $field_stat[1];

			// set subject for later if subject field found
			if ( $field_stat[1] == 'vsubject' )
				$vsubject = $params ['field_' . $i]; 

			// strip out default value
			if ( ($pos=strpos($field_name,'|')) )
			    $field_name = substr($field_name,0,$pos);

			// lets find an email field ("Is Email") and that's not empty!
			if ( $field_email == '' && $field_stat[3]=='1') {
					$field_email = $params ['field_' . $i];
			}

			// special case: select & radio
			if ( $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" || $field_type == "checkboxgroup") {
			  $field_name = explode('#',$field_name);
			  $field_name = $field_name[0];
			}

			// special case: check box
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			  $field_name = explode('#',$field_name);
			  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
				// if ccbox & checked
			  if ($field_type == "ccbox" && $params ['field_' . $i]=="X" )
			      $ccme = true;
			}

			if ( $field_type == "emailtobox" ){  						//special case where the value needs to bet get from the DB!

				$field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
				$to_one = $params ['field_' . $i];

				$off = (strpos($field_name[1],'|')===false) ? 1 : 2; // names come usually right after the label


				$value = $field_name[(int)$to_one+$off];  // values start from 0 or after!
				$field_name = $field_name[0];

	 		}
			else {
			    if ( strtoupper(get_option('blog_charset')) <> 'UTF-8' && function_exists('mb_convert_encoding'))
        		    $value = mb_convert_encoding(utf8_decode($params ['field_' . $i]), get_option('blog_charset'));   // convert back and forth to support also other than UTF8 charsets
                else
                    $value = $params ['field_' . $i];
            }

			//for db tracking
			$track[$field_name] = $value;

			
			// break for textarea
 			if ( $field_type == "textarea" )
					$value = "\n\n" . $value . "\n";

			// just for looks
		  	$space='';
			if ( strlen(stripslashes($field_name)) < 30 )   // don't count ->\"  sometimes adds more spaces?!?
				  $space = str_repeat(" ",30-strlen(stripslashes($field_name)));

			//for email
			if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' )
				$message .= $field_name . ': '. $space . $value . "\n";
					
	} // for

	//
	//reply to all email recipients
	//
	$replyto = preg_replace( array('/\s/','/;|#|\|/'), array('',','), stripslashes(get_option('cforms'.$no.'_email')) );

	//
	// multiple recipients? and to whom is the email sent? to_one = picked recip.
	if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;


	// FIRST into the database is required!
	if ( get_option('cforms_database') == '1'  ) {

		$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,date,email,ip) VALUES ".
					 "('" . $no . "', NOW(),'" . $field_email . "', '" . getip() . "');");

		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformsdata;");
   		$subID = ($subID->number=='')?'1':$subID->number;

		$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
					 "('$subID','page','$page'),";

		foreach ( array_keys($track) as $key )
			$sql .= "('$subID','$key','$track[$key]'),";

		$wpdb->query(substr($sql,0,-1));		
					 
	}


	// ready to send email
	// email header for regular email
	$eol = "\n";
	$headers = "From: \"".get_option('blogname')."\" <wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . ">" . $eol;
	$headers.= "Reply-To: " . $field_email . $eol;
	$headers.= "MIME-Version: 1.0"  .$eol;
	$headers.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;

	//either use configured subject or user determined
	$vsubject = get_option('cforms'.$no.'_subject').$vsubject;
		
	if( @mail($to, $vsubject, stripslashes($message), $headers) )
	{
		  // send copy or notification?
	    if ( (get_option('cforms'.$no.'_confirm')=='1' && $field_email<>'') || $ccme )  // not if no email & already CC'ed
	    {
					$headers2 = "From: \"".get_option('blogname')."\" <wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . ">" . $eol;
					$headers2.= "Reply-To: " . $replyto . $eol;
					$headers2.= "MIME-Version: 1.0"  .$eol;
					$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;

				    $subject2 = get_option('cforms'.$no.'_csubject');
					$message2 = get_option('cforms'.$no.'_cmsg');
                    
                    //include a unique form sub ID? Only if tracking is enabled.
                    $uniqueID = ( get_option('cforms_subid') && get_option('cforms_database'))?str_replace('{id}',$subID,get_option('cforms_subid_text')):'';

					if ( $ccme ) 
					  $sent = @mail($field_email, stripslashes($subject2).$uniqueID, stripslashes($message), $headers2);
					else
					  $sent = @mail($field_email, stripslashes($subject2).$uniqueID, stripslashes($message2), $headers2);

		  		if( !$sent )
		  			return "Error occured while sending the auto confirmation message";
	    } // cc

			// return success msg
	    $pre = $segments[0].substr(get_option('cforms'.$no.'_popup'),0,1);
	    return $pre.
						 preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success'))).'|'.
						 '<root>'.preg_replace ( '/(.*)(\r\n|$)/', '<text>\1</text>', stripslashes(get_option('cforms'.$no.'_success'))).'</root>';
	}
	else
		return "Error occured while sending the message";

} //function



// sajax stuff
// global $REQUEST_URI;

$sajax_request_type = "GET";

$sajax_debug_mode = 0;
$sajax_export_list = array();
$sajax_remote_uri = $REQUEST_URI;

cf_sajax_export("cforms_submitcomment");
cf_sajax_handle_client_request();

function cf_sajax_handle_client_request() {
		global $sajax_export_list;

		$mode = "";

		if (! empty($_GET["rs"]))
			$mode = "get";

		if (!empty($_POST["rs"]))
			$mode = "post";

		if (empty($mode))
			return;

		if ($mode == "get") {
			// Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			// always modified
			header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
			header ("Pragma: no-cache");                          // HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"]))
				$args = $_GET["rsargs"];
			else
				$args = array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"]))
				$args = $_POST["rsargs"];
			else
				$args = array();
		}

		if (! in_array($func_name, $sajax_export_list))
			echo "-:$func_name not callable(1)";
		else {
			echo "+:";
			$result = call_user_func_array($func_name, $args);
			echo $result;
		}
		exit;
	}

function cf_sajax_export() {
		global $sajax_export_list;

		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
}




function cforms($args = '',$no = '') {

	global $cforms_root;
	global $wpdb;
	parse_str($args, $r);	// parse all args, and if not specified, initialize to defaults

	//custom fields support
	if ( !(strpos($no,'+') === false) ) {
	    $no = substr($no,0,-1);
		$customfields = build_fstat($args);
		$field_count = count($customfields);
		$custom=true; 
	} else {
		$custom=false;
		$field_count = get_option('cforms'.$no.'_count_fields');
	}
	
	$content = '';
	$indent .= "\t";


	$err=0;
	
	$validations = array();
	$all_valid = 1;
	$off=0;


	if( isset($_POST['sendbutton'.$no]) ) {  /* alternative sending: both events r ok!  */

		//
		// VALIDATE all fields 
		//
		for($i = 1; $i <= $field_count; $i++) {
		
					if ( !$custom ) 
						$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					else
						$field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);

					// filter non input fields
					while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {
							$off++;

							if ( !$custom ) 
                                $field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
                            else
                                $field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);
                                
							if( $field_stat[1] == '')
									break 2; // all fields searched, break both while & for
					}

				$field_name = $field_stat[0];
				$field_type = $field_stat[1];
				$field_required = $field_stat[2];
				$field_emailcheck = $field_stat[3];
				
				
				if( $field_emailcheck ) {  // email field
				
						$validations[$i+$off] = cforms_is_email( $_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);
						if ( !$validations[$i+$off] && $err==0 ) $err=1;
					
				}
				else if( $field_required ) { // just required

						if( $field_type=="textfield" || $field_type=="textarea" || $field_type=="vsubject" ){

									// textfields empty ?
									$validations[$i+$off] = !empty($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);

									// regexp set for textfields?
									$obj = explode('|', $field_name, 3);
				  				if ( $obj[2] <> '') {
				  				
											$reg_exp = stripslashes($obj[2]);
											if( !preg_match('/'.$reg_exp.'/',$_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]) )
											    $validations[$i+$off] = false;
									}

						}else if( $field_type=="checkbox" ) {

									// textfields empty ?
									$validations[$i+$off] = !empty($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)]);

						}else if( $field_type=="selectbox" || $field_type=="emailtobox" ) {

									// textfields empty ?
									$validations[$i+$off] = !($_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)] == '-' );

						}else if( $field_type=="multiselectbox" ) {
						
									// how many multiple selects ?
                                    $all_options = $_POST['cf'.$no.'_field_' . ((int)$i+(int)$off)];
									if ( count($all_options) == 0 )                               
        									$validations[$i+$off] = false;
                                    else						    
        									$validations[$i+$off] = true;

						}else if( $field_type=="upload" ) {  // prelim upload check

									$validations[$i+$off] = !($_FILES['cf_uploadfile'.$no][name]=='');
									if ( !$validations[$i+$off] && $err==0 )
											{ $err=3; $fileerr = get_option('cforms_upload_err2'); }
						}

						if ( !$validations[$i+$off] && $err==0 ) $err=1;
					
				}
				else if( $field_type == 'verification' ){  // visitor verification code
				
	            		$validations[$i+$off] = 1;
						if ( $_POST['cforms_a'.$no] <> md5(strtolower($_POST['cforms_q'.$no])) ) {
								$validations[$i+$off] = 0;
								$err = !($err)?2:$err;
								}
								
				}
				else if( $field_type == 'captcha' ){  // captcha verification
				
	            		$validations[$i+$off] = 1;
						if ( $_POST['cforms_cap'.$no] <> md5(strtolower($_POST['cforms_captcha'.$no])) ) {
								$validations[$i+$off] = 0;
								$err = !($err)?2:$err;
								}
						
				}
				else
					$validations[$i+$off] = 1;

				$all_valid = $all_valid && $validations[$i+$off];

			}


		//
		// have to upload a file?
		//
		$uploadedfile='';

		if( isset($_FILES['cf_uploadfile'.$no]) && $_FILES['cf_uploadfile'.$no][name]<>'' ){

			  $file = $_FILES['cf_uploadfile'.$no];

				$fileerr = '';
				// A successful upload will pass this test. It makes no sense to override this one.
				if ( $file['error'] > 0 )
						$fileerr = get_option('cforms_upload_err1');
						
				// A successful upload will pass this test. It makes no sense to override this one.
				$fileext = substr($file['name'],strrpos($file['name'], '.')+1,strlen($file['name']));
				$allextensions = explode(',' ,  preg_replace('/\s/', '', get_option('cforms_upload_ext')) );
				
				if ( get_option('cforms_upload_ext')<>'' && !in_array($fileext, $allextensions) )
						$fileerr = get_option('cforms_upload_err5');

				// A non-empty file will pass this test.
				if ( !( $file['size'] > 0 ) )
						$fileerr = get_option('cforms_upload_err2');

				// A non-empty file will pass this test.
				if ( $file['size'] >= (int)get_option('cforms_upload_size') * 1024 )
						$fileerr = get_option('cforms_upload_err3');


				// A properly uploaded file will pass this test. There should be no reason to override this one.
				if (! @ is_uploaded_file( $file['tmp_name'] ) )
						$fileerr = get_option('cforms_upload_err4');


				if ( $fileerr <> '' ){

						$err = 3;
						$all_valid = false;

				} else {

						// cool, got the file!

	  		$uploadedfile = file($file['tmp_name']);

            $fp = fopen($file['tmp_name'], "rb"); //Open it
            $filedata = fread($fp, filesize($file['tmp_name'])); //Read it
            $filedata = chunk_split(base64_encode($filedata)); //Chunk it up and encode it as base64 so it can emailed
            fclose($fp);

				} // file uploaded

		} // no file upload triggered


	} // if isset sendbutton


	//
	// what kind of error message?
	//
	switch($err){
		case 0:
				$usermessage_text = '';
				break;
		case 1:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms'.$no.'_failure') );
				break;
		case 2:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms_codeerr') );
				break;
		case 3:
				$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), $fileerr);
				break;
	}


	$usermessage_class = $all_valid?'success':'failure';

	
	//
	// all valid? get ready to send
	//
	if( (isset($_POST['sendbutton'.$no]) ) && $all_valid ) {

			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success')) );

			if ( get_option('cforms'.$no.'_fname') <> '' ) { //\nA new submission (form: \"','cforms')
					$title   = "\n" . __('A new submission (form: ','cforms'). '"' . get_option('cforms'.$no.'_fname') . "\")\n";
					$page    = $_SERVER['REQUEST_URI'];
					$page    = (trim($page)=='')?'/':trim($page);
					$message = $title . str_repeat('=', strlen($title)-2 ) . "\n" .
									 __('Submitted on: ','cforms') . mysql2date(get_option('date_format'), current_time('mysql')) . ' @ ' . gmdate(get_option('time_format'), current_time('timestamp')) . "\n" .
									 __('Via: ','cforms') . $page . "\n" .
									 __('By ','cforms') . getip() . __(' (visitor IP)','cforms') . $message . "\n\n";
		 		} else
	 		  $message='';


			$track = array(); 
	  		$to_one = "-1";
			$ccme = false;
			$vsubject = '';
			$field_email = '';

			for($i = 1; $i <= $field_count; $i++) {

				if ( !$custom ) 
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i ));
				else
					$field_stat = explode('$#$', $customfields[$i-1]);


				// filter non input fields
				while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' ) {

						//just for email looks
						$space='-'; $n = (62 - strlen($field_stat[0])) / 2;
						if ( strlen($field_name) < 58 )
							$space = str_repeat("-", $n );
						$message .= substr("\n$space$field_stat[0]$space",0,60) . "\n\n";
						
						//get next in line...
						$i++;

						if ( !$custom ) 
      						$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i ));
						else
           					$field_stat = explode('$#$', $customfields[$i-1]);

						if( $field_stat[1] == '')
								break 2; // all fields searched, break both while & for
				}

				$field_name = $field_stat[0];
      			$field_type = $field_stat[1];

				// set subject for later if subject field found
				if ( $field_stat[1] == 'vsubject' )
					$vsubject = $_POST['cf'.$no.'_field_' . $i];

				// strip out default value
				if ( ($pos=strpos($field_name,'|')) )
				    $field_name = substr($field_name,0,$pos);


				// find email address
				if ( $field_email == '' && $field_stat[3]=='1')
						$field_email = $_POST['cf'.$no.'_field_' . $i];


				// special case: select box & radio box
				if ( $field_type == "checkboxgroup" || $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" ) { //only needed for field name
				  $field_name = explode('#',$field_name);
				  $field_name = $field_name[0];
				}


				// special case: check box
				if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
				  $field_name = explode('#',$field_name);
				  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
					// if ccbox
				  if ($field_type == "ccbox" && isset($_POST['cf'.$no.'_field_' . $i]) )
				      $ccme = true;
				}


			if ( $field_type == "emailtobox" ){  				//special case where the value needs to bet get from the DB!

                $field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
                $to_one 		= $_POST['cf'.$no.'_field_' . $i];
                
                $off = (strpos($field_name[1],'|')===false) ? 1 : 2; // names come usually right after the label
                
                $value 			= $field_name[(int)$to_one+$off];  // values start from 0 or after!
                $field_name = $field_name[0];
	 		}
	 		else if ( $field_type == "upload" ){
	 		
	 			$value = $file['name'];
	 		}	 		
	 		else if ( $field_type == "multiselectbox" || $field_type == "checkboxgroup"){
	 		    
                $all_options = $_POST['cf'.$no.'_field_' . $i];
	 		    if ( count($all_options) > 0)
                    $value = implode(',', $all_options);
                else
                    $value = '';
                    
            }	 		
			else
				$value = $_POST['cf'.$no.'_field_' . $i];       // covers all other fields' values


			// break for textarea
			if ( $field_type == "textarea" )
					$value = "\n\n" . $value . "\n";

			// for looks
			$space='';
			if ( strlen(stripslashes($field_name)) < 30 )
				  $space = str_repeat(" ",30-strlen(stripslashes($field_name)));

			$field_name .= ': ' . $space;

			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			
					if ( isset($_POST['cf'.$no.'_field_' . $i]) )
						$value = 'X';
					else
						$value = '-';

			} else if ( $field_type == "radiobuttons" ) {

					if ( ! isset($_POST['cf'.$no.'_field_' . $i]) )
						$value = '-';
			} 

			//for db tracking
			$trackname = ($field_type == "upload")?$field_name.'[*]':$field_name; 
			$track[$trackname] = $value;

			if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' && $field_stat[1] <> 'textonly' )
					$message .= $field_name . $value . "\n";


		} //for all fields



		// FIRST into the database is required!
		if ( get_option('cforms_database') == '1'  ) {
	
			$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,date,email,ip) VALUES ".
						 "('" . $no . "', NOW(),'" . $field_email . "', '" . getip() . "');");
	
    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformsdata;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
						 "('$subID','page','$page'),";
						 
			foreach ( array_keys($track) as $key )
				$sql .= "('$subID','$key','$track[$key]'),";
			
			$wpdb->query(substr($sql,0,-1));

            //copy attachment to local server dir
            copy($file['tmp_name'],get_option('cforms_upload_dir').'/'.$subID.'-'.$file['name']);
						 
		}


		//set header
		$replyto = preg_replace( array('/\s/','/;|#|\|/'), array('',','), stripslashes(get_option('cforms'.$no.'_email')) );

		// multiple recipients? and to whom is the email sent?
		if ( $to_one <> "-1" ) {
				$all_to_email = explode(',', $replyto);
				$replyto = $to = $all_to_email[ $to_one ];
		} else
				$to = $replyto;


		$eol = "\n";
		if ( !(isset($_FILES['cf_uploadfile'.$no]) && $filedata<>'') ) {
		
				// ready to send email
				// email header for regular email
				$headers = "From: \"".get_option('blogname')."\" <wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . ">" . $eol;
				$headers.= "Reply-To: " . $field_email . $eol;
				$headers.= "MIME-Version: 1.0"  .$eol;
				$headers.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;

				$fullmsg = stripslashes($message);
				
		} else {

				// different header for attached files
		 		//
		 		$all_mime = array("txt"=>"text/plain", "htm"=>"text/html", "html"=>"text/html", "gif"=>"image/gif", "png"=>"image/x-png",
		 						 "jpeg"=>"image/jpeg", "jpg"=>"image/jpeg", "tif"=>"image/tiff", "bmp"=>"image/x-ms-bmp", "wav"=>"audio/x-wav",
		 						 "mpeg"=>"video/mpeg", "mpg"=>"video/mpeg", "mov"=>"video/quicktime", "avi"=>"video/x-msvideo",
		 						 "rtf"=>"application/rtf", "pdf"=>"application/pdf", "zip"=>"application/zip", "hqx"=>"application/mac-binhex40",
		 						 "sit"=>"application/x-stuffit", "exe"=>"application/octet-stream", "ppz"=>"application/mspowerpoint",
								 "ppt"=>"application/vnd.ms-powerpoint", "ppj"=>"application/vnd.ms-project", "xls"=>"application/vnd.ms-excel",
								 "doc"=>"application/msword");

				$mime = (!$all_mime[$fileext])?'application/octet-stream':$all_mime[$fileext];

				$headers = "From: \"".get_option('blogname')."\" <wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . ">" . $eol;
				$headers.= "Reply-To: " . $field_email . $eol;
				$headers.= "MIME-Version: 1.0"  .$eol;
				$headers.= "Content-Type: multipart/mixed; boundary=\"----=MIME_BOUNDRY_main_message\"" . $eol;

				$fullmsg .= "This is a multi-part message in MIME format." . $eol;
			    $fullmsg .= $eol;
				$fullmsg .= "------=MIME_BOUNDRY_main_message" . $eol;
				$fullmsg .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
			    $fullmsg .= "Content-Transfer-Encoding: quoted-printable" . $eol;
			    $fullmsg .= $eol;
			    /* Add our message, in this case it's plain text.  You could also add HTML by changing the Content-Type to text/html */
			    $fullmsg .= stripslashes($message);
			    $fullmsg .= $eol;
				$fullmsg .= "------=MIME_BOUNDRY_main_message" . $eol;
				
				$fullmsg .= "Content-Type: $mime;\n\tname=\"" . $file['name'] . "\"" . $eol;
				$fullmsg .= "Content-Transfer-Encoding: base64" . $eol;
				$fullmsg .= "Content-Disposition: inline;\n\tfilename=\"" . $file['name'] . "\"\n" . $eol;
				$fullmsg .= "\n" . $eol;
				$fullmsg .= $filedata; //The base64 encoded message

 		}


		//
		// finally send mails
		//
		
		//either use configured subject or user determined
		$vsubject = get_option('cforms'.$no.'_subject').' '.$vsubject;

		if( @mail($to, $vsubject, $fullmsg, $headers) )
		{
			  // send copy or notification?
		    if ( (get_option('cforms'.$no.'_confirm')=='1' && $field_email<>'') || $ccme )  // not if no email & already CC'ed
		    {
						$headers2 = "From: \"".get_option('blogname')."\" <wordpress@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . ">" . $eol;
						$headers2.= "Reply-To: " . $replyto . $eol;
						$headers2.= "MIME-Version: 1.0"  .$eol;
						$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;

					 	$subject2 = get_option('cforms'.$no.'_csubject');
						$message2 = get_option('cforms'.$no.'_cmsg');

                        //include a unique form sub ID? Only if tracking is enabled.
                        $uniqueID = ( get_option('cforms_subid') && get_option('cforms_database'))?str_replace('{id}',$subID,get_option('cforms_subid_text')):'';
    
    					if ( $ccme ) 
    					  $sent = @mail($field_email, stripslashes($subject2).$uniqueID, stripslashes($message), $headers2);
    					else
    					  $sent = @mail($field_email, stripslashes($subject2).$uniqueID, stripslashes($message2), $headers2);
    
	
			  		if( !$sent )
			  			$usermessage_text = "Error occured while sending the auto confirmation message";
		    }
  	} // if first email already failed

	} //if isset & valid sendbutton

	//
	// paint form
	//

	$break='<br />';
	$nl="\n";
	$tab="\t";

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),0,1)=='y' ) {
		$content .= $nl . $indent . $tab . '<p id="usermessage'.$no.'a" class="info ' . $usermessage_class . '" >' . $usermessage_text . '&nbsp;</p>' . $nl;
		$actiontarget = 'a';
 	} else if ( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' )
		$actiontarget = 'b';
 	
	$content .= $indent . $tab . '<form enctype="multipart/form-data" action="' . get_permalink() . '#usermessage'.$no.$actiontarget.'" method="post" class="cform" id="cforms'.$no.'form">' . $nl;

	// start with no fieldset
	$fieldsetopen = false;
	$verification = false;
	$captcha = false;
	$upload = false;
	$fscount = 1;
	$ol = false;
	
	for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom ) 
      		$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i));
		else
    		$field_stat = explode('$#$', $customfields[$i-1]);
		
		$field_name = $field_stat[0];
		$field_type = $field_stat[1];
		$field_required = $field_stat[2];
		$field_emailcheck = $field_stat[3];
		$field_clear = $field_stat[4];
		$field_disabled = $field_stat[5];


		//special treatment for selectboxes  
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','checkbox','checkboxgroup','ccbox','emailtobox'))  ){
			$options = explode('#', stripslashes(htmlspecialchars($field_name)) );
            $field_name = $options[0];
		}
		
		
		$labelclass='';
		//visitor verification
		if ( !$verification && $field_type == 'verification' ) {
			srand(microtime()*1000003);
        	$qall = explode( "\r\n", get_option('cforms_sec_qa') );
			$n = rand(0,(count(array_keys($qall))-1));
			$q = $qall[ $n ];
			$q = explode( '=', $q );  //  q[0]=qestion  q[1]=answer
			$field_name = stripslashes(htmlspecialchars($q[0]));
			$labelclass = ' class="secq"';
		}
		else if ( $field_type == 'captcha' )
			$labelclass = ' class="seccap"';

		$defaultvalue = '';
		// no labels and other goodies for fieldsets, radio- & checkboxes !
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox')) ) {

		    // check if default val & regexp are set
		    $obj = explode('|', $field_name,3);

			if ( $obj[2] <> '')	$reg_exp = stripslashes($obj[2]); else $reg_exp='';
		    if ( $obj[1] <> '')	$defaultvalue = stripslashes(htmlspecialchars($obj[1]));

				$field_name = $obj[0];

				//set label for
				switch ($field_type){
				  case 'verification':
					  $label = 'cforms_q'.$no;
				  break;
				  case 'captcha':
					  $label = 'cforms_captcha'.$no;
				  break;
				  case 'upload':
					  $label = 'cf_uploadfile'.$no;
				  break;
				  default:
					  $label = 'cf'.$no.'_field_' . $i;
				  break;
				}


				//check if fieldset is open
				if ( !$fieldsetopen && !$ol ) {
					$content .= $indent . $tab . '<ol class="cf-ol">' . $nl;
					$ol = true;
				}

				//print label only for non "textonly" fields! Skip some others too, and handle them below indiv.
				if( $field_type <> 'textonly' )
					$content .= $nl . $indent . $tab . $tab . '<li><label for="'.$label.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';

		}

		
		
		// field classes
		$field_class = 'default';
		
		if ( $field_disabled )			$field_class .= ' disabled';
		else if ( $field_emailcheck )	$field_class .= ' fldemail';
		else if ( $field_required ) 	$field_class .= ' fldrequired';

		
		$field_value = ''; 
		if(!isset($_POST['sendbutton'.$no]) && isset($_GET['cf'.$no.'_field_'.$i]))
			$field_value = $_GET['cf'.$no.'_field_'.$i];

		// an error ocurred:
		if(! $all_valid){
			$field_class .= ($validations[$i]==1)?'':' error';  //error ?
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' ){
			     $field_value = $_POST['cf'.$no.'_field_' . $i];  // in this case it's an array! will do the stripping later
			}
			else
			     $field_value = stripslashes(htmlspecialchars($_POST['cf'.$no.'_field_' . $i]));
		}
		
		if ( $field_value=='' && $defaultvalue<>'' ) // if not reloaded (due to err) then use default values
					$field_value=$defaultvalue;    

		// field disabled, greyed out?
		$disabled = $field_disabled?'disabled="disabled"':'';

		$field = '';
		switch($field_type) {

			case "upload":
	  			$upload=true;  // set upload flag for ajax suppression!
				$field = '<input ' . $disabled . ' type="file" name="cf_uploadfile'.$no.'" id="cf_uploadfile'.$no.'" class="cf_upload ' . $field_class . '"/>';
				break;

			case "textonly":
				$field .= $indent . $tab . $tab . '<li class="textonly' . (($defaultvalue<>'')?' '.$defaultvalue:'') . '" ' . (($reg_exp<>'')?' style="'.$reg_exp.'" ':'') . '>' . stripslashes(htmlspecialchars($field_name)) . '</li>';
				break;

			case "fieldsetstart":
				if ($fieldsetopen) {
						$field = $nl . $indent . $tab . '</ol>' . $nl .
								 $indent . $tab . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				}
				if (!$fieldsetopen) {
						if ($ol)
							$field = $nl . $indent . $tab . '</ol>' . $nl;

						$field .= $indent . $tab .'<fieldset class="cf-fs'.$fscount++.'">' . $nl .
								  $indent . $tab . '<legend>' . $field_name . '</legend>' . $nl .
								  $indent . $tab . '<ol class="cf-ol">';
						$fieldsetopen = true;
						$ol = true;
		 		}
				break;

			case "fieldsetend":
				if ($fieldsetopen) {
						$field = $indent . $tab . '</ol>' . $nl .
								 $indent . $tab . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				} else $field='';
				break;

			case "verification":
				$field = '<input type="text" name="cforms_q'.$no.'" id="cforms_q'.$no.'" class="secinput ' . $field_class . '" value="" />';
		    	$verification=true;
				break;

			case "captcha":
				$_SESSION['turing_string'] = rc(4,5);
				$field = '<input type="text" name="cforms_captcha'.$no.'" id="cforms_captcha'.$no.'" class="secinput ' . $field_class . '" value="" />'.
						 '<img class="captcha" src="'.$cforms_root.'/cforms-captcha.php" alt=""/>';
		    	$captcha=true;
				break;

			case "vsubject":
			case "textfield":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';
					
				$field = '<input ' . $disabled . ' type="text" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.'/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="cf'.$no.'_field_' . $i . '_regexp" id="cf'.$no.'_field_' . $i . '_regexp" value="'.$reg_exp.'"/>';
	
				break;

			case "textarea":

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea ' . $disabled . ' cols="30" rows="8" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="' . $field_class . '"'. $onfocus.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="cf'.$no.'_field_' . $i . '_regexp" id="cf'.$no.'_field_' . $i . '_regexp" value="'.$reg_exp.'"/>';
				break;

	   		case "ccbox":
			case "checkbox":
				$err='';
				if(! $all_valid)
						$err = ($validations[$i]==1)?'':' errortxt';  //error ?

			  if ( $options[1]<>'' ) {
				 		$before = $nl . $indent . $tab . $tab . '<li>';
						$after  = '<label ' . $disabled . ' for="cf'.$no.'_field_' . $i . '" class="cf-after'.$err.'"><span>' . ($options[1]) . '</span></label></li>';
				 		$ba = 'a ';
				}
				else {
						$before = $nl . $indent . $tab . $tab . '<li><label ' . $disabled . ' for="cf'.$no.'_field_' . $i . '" class="cf-before'. $err .'"><span>' . ($field_name) . '</span></label>';
				 		$after  = '</li>';
				 		$ba = 'b ';
				}
				$field = $before . '<input ' . $disabled . ' type="checkbox" name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="cf-box-' . $ba . $field_class . '" '.($field_value?'checked="checked"':'').' />' . $after;

				break;


			case "checkboxgroup":
				array_shift($options);
				$field .= $nl . $indent . $tab . $tab . '<li class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nl . $indent . $tab . $tab . '<li class="cf-box-group">' . $nl;
				$id=1; $j=0;
				foreach( $options as $option  ) {

						//supporting names & values
					    $opt = explode('|', $option,2);

						if ( $opt[1]=='' ) $opt[1] = $opt[0];

	                    $checked = '';
	                    if ( $opt[1]==stripslashes(htmlspecialchars($field_value[$j])) )  {
	                        $checked = 'checked="checked"';
	                        $j++;
	                    }
						
						if ( $opt[0]=='' )
							$field .= $indent . $tab . $tab . $tab . '<br />' . $nl;
						else
							$field .= $indent . $tab . $tab . $tab . '<input ' . $disabled . ' type="checkbox" id="cf'.$no.'_field_'.$i. $id . '" name="cf'.$no.'_field_' . $i . '[]" value="'.$opt[1].'" '.$checked.' class="cf-box-b"/>'.
											'<label ' . $disabled . ' for="cf'.$no.'_field_'. $i . ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>" . $nl;
											
					}
				$field .= $indent . $tab . $tab . '</li>';
				break;
				
				
			case "multiselectbox":
				$field = '<select ' . $disabled . ' multiple="multiple" name="cf'.$no.'_field_' . $i . '[]" id="cf'.$no.'_field_' . $i . '" class="cfselectmulti ' . $field_class . '">';
				array_shift($options);
				$second = false;
				$j=0;
				foreach( $options as $option  ) {
                    //supporting names & values
                    $opt = explode('|', $option,2);
                    if ( $opt[1]=='' ) $opt[1] = $opt[0];
                    
                    $checked = '';
                    if ( $opt[1]==stripslashes(htmlspecialchars($field_value[$j])) )  {
                        $checked = 'selected="selected"';
                        $j++;
                    }
                        
                    $field.= '<option value="'. $opt[1] .'" '.$checked.'>'.$opt[0].'</option>';
                    $second = true;
                    
				}
				$field.= '</select>';
				break;

			case "emailtobox":
			case "selectbox":
				$field = '<select ' . $disabled . ' name="cf'.$no.'_field_' . $i . '" id="cf'.$no.'_field_' . $i . '" class="cformselect ' . $field_class . '">';
				array_shift($options); $jj=$j=0;
				$second = false;
				foreach( $options as $option  ) {

						//supporting names & values
				    $opt = explode('|', $option,2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						//email-to-box valid entry?
				    if ( $field_type == 'emailtobox' && $opt[1]<>'-' )
								$jj = $j++; else $jj = '-';

				    $checked = '';
						if( $field_value == '' || $field_value == '-') {
								if ( !$second )
								    $checked = 'selected="selected"';
						}	else
								if ( $opt[1]==$field_value || $jj==$field_value ) $checked = 'selected="selected"';
						    
						$field.= '<option value="'.(($field_type=='emailtobox')?$jj:$opt[1]).'" '.$checked.'>'.$opt[0].'</option>';
						$second = true;
				}
				$field.= '</select>';
				break;

			case "radiobuttons":
				array_shift($options);
				$field .= $nl . $indent . $tab . $tab . '<li class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nl . $indent . $tab . $tab . '<li>';
				$second = false; $id=1;
				foreach( $options as $option  ) {
				    $checked = '';

						//supporting names & values
				    $opt = explode('|', $option,2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {
								if ( !$second )
								    $checked = 'checked="checked"';
						}	else
								if ( $opt[1]==$field_value ) $checked = 'checked="checked"';
						
						$field .= '<input ' . $disabled . ' type="radio" id="cf'.$no.'_field_'.$i. $id . '" name="cf'.$no.'_field_' . $i . '" value="'.$opt[1].'" '.$checked.' class="cf-box-a'.(($second)?' cformradioplus':'').'"/>'.
											'<label ' . $disabled . ' for="cf'.$no.'_field_'. $i . ($id++) . '" class="cf-after"><span>'.$opt[0] . "</span></label>$break";
											
						$second = true;
					}
				$field .= '</li>';
				break;
				
		}
		
		// add new field
		$content .= $field;

		// adding "required" text if needed
		if($field_emailcheck == 1)
			$content .= '<span class="emailreqtxt">&nbsp;'.stripslashes(get_option('cforms'.$no.'_emailrequired')).'</span>';
		else if($field_required == 1 && $field_type <> 'checkbox' && $field_type <> 'multiselectbox' && $field_type <> 'selectbox' && $field_type <> 'upload' )
			$content .= '<span class="reqtxt">&nbsp;'.stripslashes(get_option('cforms'.$no.'_required')).'</span>';

		//close out li item
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox','textonly')) )
			$content .= '</li>';
		
	} //all fields


	if ( $ol )
		$content .= $nl . $indent . $tab . '</ol>';
	if ( $fieldsetopen )
		$content .= $nl . $indent . $tab . '</fieldset>' . $nl;


	// rest of the form

	if ( get_option('cforms'.$no.'_ajax')=='1' && !$upload && !$custom)   // ajax enabled & no upload file field!
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( $upload || $custom )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '';

	// if visitor verification turned on:
	if ( $verification )
		$content .= $nl . $indent . $tab . '<input type="hidden" name="cforms_a'.$no.'" id="cforms_a'.$no.'" value="' . md5(strtolower($q[1])) . '"/>';

	if ( $captcha )
		$content .= $nl . $indent . $tab . '<input type="hidden" name="cforms_cap'.$no.'" id="cforms_cap'.$no.'" value="' . md5(strtolower($_SESSION['turing_string'])) . '"/>';

	$content .= $nl . $indent . $tab . '<input type="hidden" name="_working'.$no.'" id="_working'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_working')).'"/>'. $nl .
				$indent . $tab . '<input type="hidden" name="_failure'.$no.'" id="_failure'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_failure')).'"/>'. $nl .
				$indent . $tab . '<input type="hidden" name="_codeerr'.$no.'" id="_codeerr'.$no.'" value="'.rawurlencode(get_option('cforms_codeerr')).'"/>'. $nl .
				$indent . $tab . '<input type="hidden" name="_popup'.$no.'"   id="_popup'.$no.'"   value="'.get_option('cforms'.$no.'_popup').'"/>' . $nl;

	$content .= $indent . $tab . '<p class="cf-sb"><input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . get_option('cforms'.$no.'_submit_text') . '"'.
										$ajaxenabled.'/></p>' . $nl;

	$content .= $indent . $tab . '</form>' . $nl;

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' )
		$content .= $indent . $tab . '<p id="usermessage'.$no.'b" class="info ' . $usermessage_class . '" >' . $usermessage_text . '&nbsp;</p>' . $nl;

	return $content;
}

// replace placeholder by generated code
function cforms_insert( $content ) {

  $forms = get_option('cforms_formcount');
  for ($i=1;$i<=$forms;$i++)
  {
    if($i==1)
    {
      if(preg_match('#<!--cforms-->#', $content))
  	   	$content = preg_replace('/(<p>)?<!--cforms-->(<\/p>)?/', cforms(''), $content);
    } else {
      if(preg_match('#<!--cforms'.$i.'-->#', $content))
    		$content = preg_replace('/(<p>)?<!--cforms'.$i.'-->(<\/p>)?/', cforms('',$i), $content);
    }
	}

	return $content;
}

// captcha random code
function rc($min,$max) 
{
	$src = 'abcdefghijkmnpqrstuvwxyz';   
	$src .= '23456789';         
	$srclen = strlen($src)-1;
	
	$length = mt_rand($min,$max);
	$Code = '';
	
	for($i=0; $i<$length; $i++) 
		$Code .= substr($src, mt_rand(0, $srclen), 1);
	
	return $Code;

}

function cforms_is_email($string)
{ return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $string); }

// some css for positioning the form elements
function cforms_style() {
	global $cforms_root;
	echo "\n\t<!-- Start Of Script Generated By cforms v".cforms_info('localversion')." [Oliver Seidel | www.deliciousdays.com] -->\n";
	echo "\t".'<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/styling/' . get_option('cforms_css') . '" />'."\n";
	echo "\t".'<script type="text/javascript" src="' . $cforms_root. '/js/cforms.js"></script>'."\n";
	echo "\t".'<!-- End Of Script Generated By cforms -->'."\n\n";
}


// some css for arranging the table fields in wp-admin
function cforms_options_page_style() {

	if (   strpos($_SERVER['REQUEST_URI'], 'plugins.php') !== false ) {
		if( cforms_remote_version_check()==1 ) {
			global $cforms_root;
			$nl="\n";
			echo '<script type="text/javascript" src="' . $cforms_root . '/js/cformsadmin.js"></script>' . $nl .
				 '<script type=\'text/javascript\'>' . $nl .
				 '//<![CDATA[' . $nl .
				 'addLoadEvent(newcformsversion);' . $nl .
				 '//]]>'. $nl .
				 '</script>' . $nl;
	  } // no newer release avail.
	} // not plugin page
  
 	// other admin pages
	global $cforms_root;
	if (   strpos($_SERVER['REQUEST_URI'], $plugindir.'/cforms') !== false )
		echo	'<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/cforms-admin.css" />' . "\n" .
				'<script type="text/javascript" src="' . $cforms_root . '/js/dbx.js"></script>' . "\n" .
				'<script type="text/javascript" src="' . $cforms_root . '/js/cformsadmin.js"></script>' . "\n";
}

//footer unbder all options pages
function cforms_footer() {
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo str_replace('[url]','http://www.deliciousdays.com/cforms-forum/',__('For more information and support, visit the <strong>cforms</strong> <a href="[url]" title="cforms support forum">support forum</a>. ', 'cforms')) ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>

	<p align="center">Version <?php echo cforms_info('localversion'); ?></p>
<?php 
}

//build field_stat string from array (for custom forms)
function build_fstat($fields) {	
    $cfarray = array();
    for($i=0; $i<count($fields['label']); $i++) {
        if ( $fields['type'][$i] == '') $fields['type'][$i] = 'textfield';
        if ( $fields['isreq'][$i] == '') $fields['isreq'][$i] = '0';
        if ( $fields['isemail'][$i] == '') $fields['isemail'][$i] = '0';
        $cfarray[$i]=$fields['label'][$i].'$#$'.$fields['type'][$i].'$#$'.$fields['isreq'][$i].'$#$'.$fields['isemail'][$i];
    }
    return $cfarray;
}

// inserts a cform anywhere you want
function insert_cform($no='') {	echo cforms('',$no); }

// inserts a custom cform anywhere you want
function insert_custom_cform($fields='',$no='') { echo cforms($fields,$no.'+'); }


// Set 'manage_database' Capabilities To Administrator
add_action('activate_'.$plugindir.'/cforms.php', 'cforms_init');
function cforms_init() {
	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
}


function cforms_menu() {
	global $plugindir, $wpdb;
	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;
	
	if (function_exists('add_menu_page')) {
		add_menu_page(__('cforms', 'cforms'), __('cforms', 'cforms'), 'manage_cforms', $plugindir.'/cforms-options.php');
	}
	if (function_exists('add_submenu_page')) {
		add_submenu_page($plugindir.'/cforms-options.php', __('Plugin Settings', 'cforms'), __('Plugin Settings', 'cforms'), 'manage_cforms', $plugindir.'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($plugindir.'/cforms-options.php', __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'manage_cforms', $plugindir.'/cforms-database.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $plugindir.'/cforms-css.php');
		add_submenu_page($plugindir.'/cforms-options.php', __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $plugindir.'/cforms-help.php');
	}
}


if (function_exists('add_action')){
  add_action('admin_head', 'cforms_options_page_style');
  add_action('admin_menu', 'cforms_menu');
}

// add content actions and filters
add_filter('wp_head', 'cforms_style'); 
add_filter('the_content', 'cforms_insert',10);

?>
