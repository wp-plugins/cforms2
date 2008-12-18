<?php
/*
Plugin Name: cforms
Plugin URI: http://www.deliciousdays.com/cforms-plugin
Description: cformsII offers unparalleled flexibility in deploying contact forms across your blog. Features include: comprehensive SPAM protection, Ajax support, Backup & Restore, Multi-Recipients, Role Manager support, Database tracking and many more. Please see the <a href="http://www.deliciousdays.com/cforms-forum?forum=2&topic=2&page=1">VERSION HISTORY</a> for <strong>what's new</strong> and current <strong>bugfixes</strong>.
Author: Oliver Seidel
Version: 9.4
Author URI: http://www.deliciousdays.com
*/

/*
Copyright 2006-2008  Oliver Seidel   (email : oliver.seidel@deliciousdays.com)

WHAT's NEW in cformsII - v9.4

README:
v9.0 introduces a completely new method of storing its settings,
	***** please make a WP database backup first (!) *****
and then migrate your settings (cforms will guide you!)

*) feature: post form submission admin-email-TO: address correction ("dynamic admin email address")
*) feature: specific insert position can now be determined when adding new fields
*) feature: TAF: added support for proper WP 2.7 custom field registration (screen options)

*) bugfix: major bug causing the tracking page to not display correctly
*) bugfix: fixed some WP2.7 admin UI incompatibilities
*) bugfix: fixed TAF-option for WP2.7 New post/page UI (drag&drop, open/close)
*) bugfix: multi-select / check box groups for multi-page form hack
*) bugfix: special characters would cause captcha reload to revert back to certain default settings
*) bugfix: fixed autosave issue with TellAFriend flag being deleted

*) other: added default number (5) of shown RSS entries, if not configured by user

*/
$localversion = '9.4';


### db settings
global $wpdb, $wp_db_version;
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### button stuff
if( !class_exists('buttonsnap') )
	require_once (dirname(__FILE__) . '/buttonsnap.php');

require_once(dirname(__FILE__) . '/lib_functions.php');

### new global settings container, will eventually be the only one!
global $cformsSettings;
$cformsSettings = get_option('cforms_settings');


### do a couple of things necessary as part of plugin activation
$dummy = __('Please go to the <a href="%s">cforms admin page</a> and run the update process.', 'cforms');
add_action('admin_notices', create_function('', 'global $plugindir, $cformsSettings; if (is_array($cformsSettings) && $cformsSettings[\'global\'][\'update\']) echo \'<div class="error"><p><strong>\' . sprintf( __(\'Please go to the <a href="%s">cforms admin page</a> and run the update process.\', \'cforms\') ,\'admin.php?page=\'.$plugindir.\'/cforms-global-settings.php\') . \'</strong></p></div>\';') );
register_activation_hook( __FILE__, 'cforms_activate' );
function cforms_activate() {
	cforms_init();
	require_once(dirname(__FILE__) . '/lib_activate.php');
}


### settings corrputed?
if ( !is_array($cformsSettings) ){
	add_action('admin_menu', 'settings_corrupted');
    return;
}

### if all data has been erased quit
function settings_corrupted() {
	$tmp = basename(dirname(__FILE__));

	if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $tmp.'/cforms-corrupted.php');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $tmp.'/cforms-corrupted.php');
	}
}

require_once (dirname(__FILE__) . '/lib_aux.php');
require_once (dirname(__FILE__) . '/editor.php');

### http://trac.wordpress.org/ticket/3002
$plugindir   = $cformsSettings['global']['plugindir'];
$cforms_root = $cformsSettings['global']['cforms_root'];

### need this for captchas
add_action('template_redirect', 'start_cforms_session');
function start_cforms_session() {
	@session_cache_limiter('private, must-revalidate');
	@session_cache_expire(0);
	@session_start();
}



###
### main function
###
function cforms($args = '',$no = '') {

	global $smtpsettings, $styles, $subID, $cforms_root, $wpdb, $track, $wp_db_version, $cformsSettings;

	//Safety, in case someone uses '1' for the default form
	$no = ($no=='1')?'':$no;

	parse_str($args, $r);	// parse all args, and if not specified, initialize to defaults

	//custom fields support
	if ( !(strpos($no,'+') === false) ) {
	    $no = substr($no,0,-1);
		$customfields = build_fstat($args);
		$field_count = count($customfields);
		$custom=true;
	} else {
		$custom=false;
		$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];
	}

	$content = '';

	$err=0;
	$filefield=0;   ### for multiple file upload fields

	$validations = array();
	$all_valid = 1;
	$off=0;
	$fieldsetnr=1;

	$c_errflag=false;
	$custom_error='';
	$usermessage_class='';

	### ??? check for WP2.0.2
	if ( $wp_db_version >= 3440 && function_exists('wp_get_current_user') )
		$user = wp_get_current_user();


	if( isset($_REQUEST['sendbutton'.$no]) ) {  /* alternative sending: both events r ok!  */

		require_once (dirname(__FILE__) . '/lib_nonajax.php');

		$usermessage_class = $all_valid?' success':' failure';

	}


	if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' && $send2author ) ### called from lib_WPcomments ?
		return $all_valid;


	###
	### paint form
	###
	$success=false;
	if ( isset($_GET['cfemail']) && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' ){ ###  fix for WP Comment (loading after redirect)
		$usermessage_class = ' success';
		$success=true;
		if ( $_GET['cfemail']=='sent' )
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']) );
		elseif ( $_GET['cfemail']=='posted' )
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms_commentsuccess']) );
	}


	$break='<br />';
	$nl="\n";
	$tab="\t";
	$tt="\t\t";
	$ntt="\n\t\t";
	$nttt="\n\t\t\t";

	### either show message above or below
	$usermessage_text	= check_default_vars($usermessage_text,$no);
	$usermessage_text	= check_cust_vars($usermessage_text,$track,$no);
   	$umc = ($usermessage_class<>''&&$no>1)?' '.$usermessage_class.$no:'';

	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],0,1)=='y' ) {
		$content .= $ntt . '<div id="usermessage'.$no.'a" class="cf_info' . $usermessage_class . $umc .' ">' . $usermessage_text . '</div>';
		$actiontarget = 'a';
 	} else if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' )
		$actiontarget = 'b';

	### redirect == 2 : hide form?    || or if max entries reached!
	if ( ($cformsSettings['form'.$no]['cforms'.$no.'_redirect']==2 && isset($_REQUEST['sendbutton'.$no]) && $all_valid) )
		return $content;
	else if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && get_cforms_submission_left($no)<=0) || !cf_check_time($no) ){

		if ( $cflimit == "reached" )
			return stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt']);
		else
			return $content.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_limittxt']);

	}

 	### alternative form action
	$alt_action=false;
	if( $cformsSettings['form'.$no]['cforms'.$no.'_action']=='1' ) {
		$action = $cformsSettings['form'.$no]['cforms'.$no.'_action_page'];
		$alt_action=true;
	}
	else if( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' )
		$action = $cforms_root . '/lib_WPcomment.php'; ### re-route and use WP comment processing
 	else
		$action = get_current_page(false) . '#usermessage'. $no . $actiontarget;

	$content .= $ntt . '<form enctype="multipart/form-data" action="' . $action . '" method="post" class="cform" id="cforms'.$no.'form">' . $nl;


	### start with no fieldset
	$fieldsetopen = false;
	$verification = false;

	$captcha = false;
	$upload = false;
	$fscount = 1;
	$ol = false;

	for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom )
      		$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i]);
		else
    		$field_stat = explode('$#$', $customfields[$i-1]);

		$field_name       = $field_stat[0];
		$field_type       = $field_stat[1];
		$field_required   = $field_stat[2];
		$field_emailcheck = $field_stat[3];
		$field_clear      = $field_stat[4];
		$field_disabled   = $field_stat[5];
		$field_readonly   = $field_stat[6];


		### ommit certain fields
		if( in_array($field_type,array('cauthor','url','email')) && $user->ID )
			continue;


		### check for custom err message and split field_name
	    $obj = explode('|err:', $field_name,2);
	    $fielderr = $obj[1];

		if ( $fielderr <> '')	{

		    switch ( $field_type ) {

			    case 'upload':
					$custom_error .= 'cf_uploadfile' . $no . '-'. $i . '$#$'.$fielderr.'|';
	    			break;

			    case 'captcha':
					$custom_error .= 'cforms_captcha' . $no . '$#$'.$fielderr.'|';
	    			break;

			    case 'verification':
					$custom_error .= 'cforms_q'. $no . '$#$'.$fielderr.'|';
	    			break;

				case "cauthor":
				case "url":
				case "email":
				case "comment":
					$custom_error .= $field_type . '$#$'.$fielderr.'|';
	    			break;

			    default:
    				preg_match('/^([^#\|]*).*/',$field_name,$input_name);
    				if ( strpos($input_name[1],'[id:')>0 )
    					preg_match ('/\[id:(.+)\]/',$input_name[1],$input_name);

					$custom_error .= ($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1')?cf_sanitize_ids($input_name[1]):'cf'.$no.'_field_'.$i;
					$custom_error .= '$#$'.$fielderr.'|';
	    			break;
		    }

		}

		### check for title attrib
	    $obj = explode('|title:', $obj[0],2);
		$fieldTitle = ($obj[1]<>'')?' title="'.str_replace('"','&quot;',stripslashes($obj[1])).'"':'';

		### special treatment for selectboxes
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','send2author','luv','subscribe','checkbox','checkboxgroup','ccbox','emailtobox'))  ){

			$chkboxClicked = array();
			if (  in_array($field_type,array('luv','subscribe','checkbox','ccbox')) && strpos($obj[0],'|set:')>1 ){
				$chkboxClicked = explode('|set:', stripslashes($obj[0]) );
				$obj[0] = $chkboxClicked[0];
			}

			$options = explode('#', stripslashes($obj[0]) );
            $field_name = $options[0];

		}


		### check if fieldset is open
		if ( !$fieldsetopen && !$ol && $field_type<>'fieldsetstart') {
			$content .= $tt . '<ol class="cf-ol">';
			$ol = true;
		}


		$labelclass='';
		### visitor verification
		if ( !$verification && $field_type == 'verification' ) {
			srand(microtime()*1000003);
        	$qall = explode( "\r\n", $cformsSettings['global']['cforms_sec_qa'] );
			$n = rand(0,(count(array_keys($qall))-1));
			$q = $qall[ $n ];
			$q = explode( '=', $q );  ### q[0]=qestion  q[1]=answer
			$field_name = stripslashes(htmlspecialchars($q[0]));
			$labelclass = ' class="secq"';
		}
		else if ( $field_type == 'captcha' )
			$labelclass = ' class="seccap"';


		$defaultvalue = '';
		### setting the default val & regexp if it exists
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','send2author','luv','subscribe','checkbox','checkboxgroup','ccbox','emailtobox','multiselectbox','selectbox','verification')) ) {

		    ### check if default val & regexp are set
		    $obj = explode('|', $obj[0],3);

			if ( $obj[2] <> '')	$reg_exp = str_replace('"','&quot;',stripslashes($obj[2])); else $reg_exp='';
		    if ( $obj[1] <> '')	$defaultvalue = str_replace('"','&quot;', check_default_vars(stripslashes(($obj[1])),$no) );

			$field_name = $obj[0];
		}

		### Label ID's
		$labelIDx = '';
		$labelID  = ($cformsSettings['global']['cforms_labelID']=='1')?' id="label-'.$no.'-'.$i.'"':'';

		### <li> ID's
		$liID = ( $cformsSettings['global']['cforms_liID']=='1' ||
				  substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y" ||
				  substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y" )?' id="li-'.$no.'-'.$i.'"':'';

		### input field names & label
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1' ){

			if ( strpos($field_name,'[id:')!==false ){
				$idPartA = strpos($field_name,'[id:');
				$idPartB = strpos($field_name,']',$idPartA);
				$input_id = $input_name = cf_sanitize_ids( substr($field_name,$idPartA+4,($idPartB-$idPartA)-4) );

				$field_name = substr_replace($field_name,'',$idPartA,($idPartB-$idPartA)+1);

			} else
				$input_id = $input_name = cf_sanitize_ids($field_name);

		} else
			$input_id = $input_name = 'cf'.$no.'_field_'.$i;

		$field_class = '';

		switch ($field_type){
			case 'luv':
				$input_id = $input_name = 'luv';
				break;
			case 'subscribe':
				$input_id = $input_name = 'subscribe';
				break;
			case 'verification':
				if( is_user_logged_in() && $cformsSettings['global']['cforms_captcha_def']['foqa']<>'1' )
					continue(2);
				$input_id = $input_name = 'cforms_q'.$no;
				break;
			case 'captcha':
				if( is_user_logged_in() && $cformsSettings['global']['cforms_captcha_def']['fo']<>'1' )
					continue(2);
				$input_id = $input_name = 'cforms_captcha'.$no;
				break;
			case 'upload':
				$input_id = $input_name = 'cf_uploadfile'.$no.'-'.$i;
				$field_class = 'upload';
				break;
			case "send2author":
			case "email":
			case "cauthor":
			case "url":
				$input_id = $input_name = $field_type;
			case "datepicker":
			case "yourname":
			case "youremail":
			case "friendsname":
			case "friendsemail":
			case "textfield":
			case "pwfield":
				$field_class = 'single';
				break;
			case "hidden":
				$field_class = 'hidden';
				break;
			case 'comment':
				$input_id = $input_name = $field_type;
				$field_class = 'area';
				break;
			case 'textarea':
				$field_class = 'area';
				break;
		}


		### additional field classes
		if ( $field_disabled )		$field_class .= ' disabled';
		if ( $field_readonly )		$field_class .= ' readonly';
		if ( $field_emailcheck )	$field_class .= ' fldemail';
		if ( $field_required ) 		$field_class .= ' fldrequired';


		$field_value = '';

		### an error ocurred:
		$liERR = $insertErr = '';

		if(! $all_valid){
			### errors...
			if ( $validations[$i]==1 )
				$field_class .= '';
			else{
				$field_class .= ' cf_error';

				### enhanced error display
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y")
					$liERR = ' class="cf_li_err"';
				if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y")
					$insertErr = ($fielderr<>'')?'<ul class="cf_li_text_err"><li>'.stripslashes($fielderr).'</li></ul>':'';
			}


			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' ){
				$field_value = $_REQUEST[$input_name];  ### in this case it's an array! will do the stripping later
			}
			else
				$field_value = str_replace('"','&quot;',stripslashes($_REQUEST[$input_name]));

		}
		else if( (!isset($_REQUEST['sendbutton'.$no]) && isset($_REQUEST[$input_name])) || $cformsSettings['form'.$no]['cforms'.$no.'_dontclear'] ){
		    ### only pre-populating fields...
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' )
				$field_value = $_REQUEST[$input_name];  ### in this case it's an array! will do the stripping later
			else
				$field_value = str_replace('"','&quot;',stripslashes($_REQUEST[$input_name]));
	    }

		### print label only for non "textonly" fields! Skip some others too, and handle them below indiv.
		if( ! in_array($field_type,array('hidden','textonly','fieldsetstart','fieldsetend','ccbox','luv','subscribe','checkbox','checkboxgroup','send2author','radiobuttons')) )
			$content .= $nttt . '<li'.$liID.$liERR.'>'.$insertErr.'<label' . $labelID . ' for="'.$input_id.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';


		if ( $field_value=='' && $defaultvalue<>'' ) ### if not reloaded (due to err) then use default values
			$field_value=$defaultvalue;

		### field disabled or readonly, greyed out?
		$disabled = $field_disabled?' disabled="disabled"':'';
		$readonly = $field_readonly?' readonly="readonly"':'';

		$dp = '';
		$naming = false;
		$field  = '';
		$val = '';
		$force_checked = false;
		$cookieset = '';

		switch($field_type) {

			case "upload":
	  			$upload=true;  ### set upload flag for ajax suppression!
				$field = '<input' . $readonly.$disabled . ' type="file" name="cf_uploadfile'.$no.'[]" id="cf_uploadfile'.$no.'-'.$i.'" class="cf_upload ' . $field_class . '"'.$fieldTitle.'/>';
				break;

			case "textonly":
				$field .= $nttt . '<li'.$liID.' class="textonly' . (($defaultvalue<>'')?' '.$defaultvalue:'') . '"' . (($reg_exp<>'')?' style="'.$reg_exp.'" ':'') . '>' . stripslashes(($field_name)) . '</li>';
				break;

			case "fieldsetstart":
				if ($fieldsetopen) {
						$field = $ntt . '</ol>' . $nl .
								 $tt . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				}
				if (!$fieldsetopen) {
						if ($ol)
							$field = $ntt . '</ol>' . $nl;

						$field .= $tt .'<fieldset class="cf-fs'.$fscount++.'">' . $nl .
								  $tt . '<legend>' . stripslashes($field_name) . '</legend>' . $nl .
								  $tt . '<ol class="cf-ol">';
						$fieldsetopen = true;
						$ol = true;
		 		}
				break;

			case "fieldsetend":
				if ($fieldsetopen) {
						$field = $ntt . '</ol>' . $nl .
								 $tt . '</fieldset>' . $nl;
						$fieldsetopen = false;
						$ol = false;
				} else $field='';
				break;

			case "verification":
				$field = '<input type="text" name="'.$input_name.'" id="cforms_q'.$no.'" class="secinput ' . $field_class . '" value=""'.$fieldTitle.'/>';
		    	$verification=true;
				break;

			case "captcha":
				//$_SESSION['turing_string_'.$no] = rc(); // done now in captcha.php

				$field = '<input type="text" name="'.$input_name.'" id="cforms_captcha'.$no.'" class="secinput' . $field_class . '" value=""'.$fieldTitle.'/>'.
						 '<img id="cf_captcha_img'.$no.'" class="captcha" src="'.$cforms_root.'/cforms-captcha.php?ts='.$no.get_captcha_uri().'" alt=""/>'.
						 '<a title="'.__('reset captcha image', 'cforms').'" href="javascript:reset_captcha(\''.$no.'\')"><img class="captcha-reset" src="'.$cforms_root.'/images/spacer.gif" alt="Captcha"/></a>';
		    	$captcha=true;
				break;

			case "cauthor":
				$cookieset = 'comment_author_'.COOKIEHASH;
			case "url":
				$cookieset = ($cookieset=='')?'comment_author_url_'.COOKIEHASH:$cookieset;
			case "email":
				$cookieset = ($cookieset=='')?'comment_author_email_'.COOKIEHASH:$cookieset;
				$field_value = ( $_COOKIE[$cookieset]<>'' ) ? $_COOKIE[$cookieset] : $field_value;
			case "datepicker":
			case "yourname":
			case "youremail":
			case "friendsname":
			case "friendsemail":
			case "textfield":
			case "pwfield":
				$type = ($field_type=='pwfield')?'password':'text';
				$field_class = ($field_type=='datepicker')?$field_class.' cf_date':$field_class;

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<input' . $readonly.$disabled . ' type="'.$type.'" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.$fieldTitle.'/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"'.$fieldTitle.'/>';

				$field .= $dp;
				break;

			case "hidden":

				preg_match_all('/\\{([^\\{]+)\\}/',$field_value,$findall);
				if ( count($findall[1]) > 0 ) {
				$allfields = get_post_custom( get_the_ID() );

					foreach ( $findall[1] as $fvar ) {
						if( $allfields[$fvar][0] <> '')
							$field_value = str_replace('{'.$fvar.'}', $allfields[$fvar][0], $field_value);
					}
				}

                if ( preg_match('/^<([a-zA-Z0-9]+)>$/',$field_value,$getkey) )
                    $field_value = $_GET[$getkey[1]];

				$field .= $nttt . '<li class="cf_hidden"><input type="hidden" class="cfhidden" name="'.$input_name.'" id="'.$input_id.'" value="' . $field_value  . '"'.$fieldTitle.'/></li>';
				break;

			case "comment":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="comment" id="comment" class="' . $field_class . '"'. $onfocus.$fieldTitle.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="comment" id="comment_regexp" value="'.$reg_exp.'"'.$fieldTitle.'/>';
				break;

			case "textarea":

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '"'. $onfocus.$fieldTitle.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"'.$fieldTitle.'/>';
				break;

			case "subscribe":
				if ( class_exists('sg_subscribe') && $field_type=='subscribe' ){
					global $sg_subscribe;
					sg_subscribe_start();
					if( ($email = $sg_subscribe->current_viewer_subscription_status())=='admin' && current_user_can('manage_options') ){
						$field .= '<li'.$liID.'>'.str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->author_text).'</li>';
						continue;
					}else if($email<>''){
						$field .= '<li'.$liID.'>'.str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->subscribed_text).'</li>';
						continue;
					}
					$val = ' value="subscribe"';
				}
			case "luv":
				if ( function_exists('comment_luv') && $field_type=='luv' ){
					get_currentuserinfo() ;
					global $user_level;
					if( $user_level==10 )
						continue (2);
					//empty for now
					$val = ' value="luv"';
				}
	   		case "ccbox":
			case "checkbox":
				if ( ! $field_value )
					$preChecked = ( strpos($chkboxClicked[1],'true') !== false ) ? ' checked="checked"':'';
				else
					$preChecked = $field_value ? ' checked="checked"':'';

				$err='';
				if( !$all_valid && $validations[$i]<>1 )
					$err = ' cf_errortxt';

				if ( $options[1]<>'' ) {
					    $opt = explode('|', $options[1],2);
				 		$before = '<li'.$liID.$liERR.'>'.$insertErr;
						$after  = '<label'. $labelID . ' for="'.$input_id.'" class="cf-after'.$err.'"><span>' . $opt[0] . '</span></label></li>';
				 		$ba = 'a';
				}
				else {
					    $opt = explode('|', $field_name,2);
						$before = '<li'.$liID.$liERR.'>'.$insertErr.'<label' . $labelID . ' for="'.$input_name.'" class="cf-before'. $err .'"><span>' . $opt[0] . '</span></label>';
				 		$after  = '</li>';
				 		$ba = 'b';
				}
				### if | val provided, then use "X"
				if( $val=='' )
					$val = ($opt[1]<>'')?' value="'.$opt[1].'"':'';
				$field = $nttt . $before . '<input' . $readonly.$disabled . ' type="checkbox" name="'.$input_name.'" id="'.$input_id.'" class="cf-box-' . $ba . $field_class . '"'.$val.$fieldTitle.$preChecked.'/>' . $after;

				break;


			case "checkboxgroup":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';
				array_shift($options);
				$field .= $nttt . '<li'.$liID.' class="cf-box-title">' . (($field_name)) . '</li>' .
						  $nttt . '<li'.$liID_b.' class="cf-box-group">';
				$id=1; $j=0;
				foreach( $options as $option  ) {

						### supporting names & values
						$boxPreset = explode('|set:', $option );
				    	$opt = explode('|', $boxPreset[0],2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

	                    $checked = '';
	                    if ( is_array($field_value) ){
		                    if ( $opt[1]==stripslashes((str_replace('"',"&quot;",strip_tags($field_value[$j])))) )  {
		                        $checked = 'checked="checked"';
		                        $j++;
		                    }
	                    }else{
							if ( strpos($boxPreset[1],'true')!==false )
						    $checked = ' checked="checked"';
	                    }

						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= $nttt . $tab . '<br />';
						else
							$field .= $nttt . $tab . '<input' . $readonly.$disabled . ' type="checkbox" id="'. $input_id .'-'. $id . '" name="'. $input_name . '[]" value="'.$opt[1].'" '.$checked.' class="cf-box-b"'.$fieldTitle.'/>'.
									  '<label' . $labelIDx . ' for="'. $input_id .'-'. ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>";

					}
				$field .= $nttt . '</li>';
				break;


			case "multiselectbox":
				### $field .= $nttt . '<li><label ' . $labelID . ' for="'.$input_name.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';
				$field .= '<select' . $readonly.$disabled . ' multiple="multiple" name="'.$input_name.'[]" id="'.$input_id.'" class="cfselectmulti ' . $field_class . '"'.$fieldTitle.'>';
				array_shift($options);
				$j=0;
				foreach( $options as $option  ) {
                    ### supporting names & values
					$optPreset = explode('|set:', $option );
				    $opt = explode('|', $optPreset[0],2);
                    if ( $opt[1]=='' ) $opt[1] = $opt[0];

                    $checked = '';

					if ( is_array($field_value) ){
	                    if ( $opt[1]==stripslashes(htmlspecialchars(str_replace('"',"&quot;",strip_tags($field_value[$j])))) )  {
	                        $checked = ' selected="selected"';
	                        $j++;
	                    }
	                }else{
						if ( strpos($optPreset[1],'true')!==false )
						    $checked = ' selected="selected"';
	                }

                    $field.= $nttt . $tab . '<option value="'. str_replace('"','&quot;',$opt[1]) .'"'.$checked.'>'.$opt[0].'</option>';

				}
				$field.= $nttt . '</select>';
				break;

			case "emailtobox":
			case "selectbox":
				$field = '<select' . $readonly.$disabled . ' name="'.$input_name.'" id="'.$input_id.'" class="cformselect' . $field_class . '" '.$fieldTitle.'>';
				array_shift($options); $jj=$j=0;

				foreach( $options as $option  ) {

					### supporting names & values
					$optPreset = explode('|set:', $option );
				    $opt = explode('|', $optPreset[0],2);
					if ( $opt[1]=='' ) $opt[1] = $opt[0];

						### email-to-box valid entry?
				    if ( $field_type == 'emailtobox' && $opt[1]<>'-' )
								$jj = $j++; else $jj = '-';

				    $checked = '';

					if( $field_value == '' ) {
							if ( strpos($optPreset[1],'true')!==false )
							    $checked = ' selected="selected"';
					}	else
							if ( $opt[1]==$field_value || $jj==$field_value )
								$checked = ' selected="selected"';

					$field.= $nttt . $tab . '<option value="'.(($field_type=='emailtobox')?$jj:$opt[1]).'"'.$checked.'>'.$opt[0].'</option>';

				}
				$field.= $nttt . '</select>';
				break;

			case "send2author":
				$force_checked = ( strpos($field_stat[0],'|set:')===false )? true:false;
			case "radiobuttons":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';	### only if label ID's active

				array_shift($options);
				$field .= $nttt . '<li'.$liID.' class="cf-box-title">' . (($field_name)) . '</li>' .
						  $nttt . '<li'.$liID_b.' class="cf-box-group">';
				$id=1;
				foreach( $options as $option  ) {
				    $checked = '';

						### supporting names & values
						$radioPreset = explode('|set:', $option );
				    	$opt = explode('|', $radioPreset[0],2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {

								if ( strpos($radioPreset[1],'true')!==false || ($force_checked && $id==1))
								    $checked = ' checked="checked"';

						}	else
								if ( $opt[1]==$field_value ) $checked = ' checked="checked"';

						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= $nttt . $tab . '<br />';
						else
							$field .= $nttt . $tab .
								  '<input' . $readonly.$disabled . ' type="radio" id="'. $input_id .'-'. $id . '" name="'.$input_name.'" value="'.$opt[1].'"'.$checked.' class="cf-box-b'.(($second)?' cformradioplus':'').'"'.$fieldTitle.'/>'.
								  '<label' . $labelIDx . ' for="'. $input_id .'-'. ($id++) . '" class="cf-after"><span>'.$opt[0] . "</span></label>";

					}
				$field .= $nttt  . '</li>';
				break;

		}

		### add new field
		$content .= $field;

		### adding "required" text if needed
		if($field_emailcheck == 1)
			$content .= '<span class="emailreqtxt">'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_emailrequired']).'</span>';
		else if($field_required == 1 && !in_array($field_type,array('ccbox','luv','subscribe','checkbox')) )
			$content .= '<span class="reqtxt">'.stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_required']).'</span>';

		### close out li item
		if ( ! in_array($field_type,array('hidden','fieldsetstart','fieldsetend','radiobuttons','luv','subscribe','checkbox','checkboxgroup','ccbox','textonly','send2author')) )
			$content .= '</li>';

	} ### all fields


	if ( $ol )
		$content .= $ntt . '</ol>';
	if ( $fieldsetopen )
		$content .= $ntt . '</fieldset>';


	### rest of the form
	if ( $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' && !$upload && !$custom && !$alt_action )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( ($upload || $custom || $alt_action) && $cformsSettings['form'.$no]['cforms'.$no.'_ajax']=='1' )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '';


	### just to appease "strict"
	$content .= $ntt . '<fieldset class="cf_hidden">'.$nttt.'<legend>&nbsp;</legend>';

	### if visitor verification turned on:
	if ( $verification )
		$content .= $nttt .'<input type="hidden" name="cforms_a'.$no.'" id="cforms_a'.$no.'" value="' . md5(rawurlencode(strtolower($q[1]))) . '"/>';

	/*if ( $captcha ){
		if ( $captchaopt['i']=='i' )
			$tu = strtolower($_SESSION['turing_string_'.$no]);
		else
			$tu = $_SESSION['turing_string_'.$no];
		$content .= $nttt .'<input type="hidden" name="cforms_cap'.$no.'" id="cforms_cap'.$no.'" value="' . md5($tu) . '" alt="'.$captchaopt['i'].'"/>';
	}
*/
	$custom_error=substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1).substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],4,1).$custom_error;

	if ( $cformsSettings['form'.$no]['cforms'.$no.'_tellafriend']>0 ){
		if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)==2 )
			$nono = ''; else $nono = $no;

		$content .= $nttt . '<input type="hidden" name="comment_post_ID'.$nono.'" id="comment_post_ID'.$nono.'" value="' . ( isset($_GET['pid'])? $_GET['pid'] : get_the_ID() ) . '"/>' .
					$nttt . '<input type="hidden" name="cforms_pl'.$no.'" id="cforms_pl'.$no.'" value="' . ( isset($_GET['pid'])? get_permalink($_GET['pid']) : get_permalink() ) . '"/>';
	}

	$content .= $nttt . '<input type="hidden" name="cf_working'.$no.'" id="cf_working'.$no.'" value="'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_working']).'"/>'.
				$nttt . '<input type="hidden" name="cf_failure'.$no.'" id="cf_failure'.$no.'" value="'.rawurlencode($cformsSettings['form'.$no]['cforms'.$no.'_failure']).'"/>'.
				$nttt . '<input type="hidden" name="cf_codeerr'.$no.'" id="cf_codeerr'.$no.'" value="'.rawurlencode($cformsSettings['global']['cforms_codeerr']).'"/>'.
				$nttt . '<input type="hidden" name="cf_customerr'.$no.'" id="cf_customerr'.$no.'" value="'.rawurlencode($custom_error).'"/>'.
				$nttt . '<input type="hidden" name="cf_popup'.$no.'" id="cf_popup'.$no.'" value="'.$cformsSettings['form'.$no]['cforms'.$no.'_popup'].'"/>';

	$content .= $ntt . '</fieldset>';


	$content .= $ntt . '<p class="cf-sb"><input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . $cformsSettings['form'.$no]['cforms'.$no.'_submit_text'] . '"'.
				$ajaxenabled.'/></p>';

	$content .= $ntt . '</form>';

	### link love? you bet ;)
		$content .= $ntt . '<p class="linklove" id="ll'. $no .'"><a href="http://www.deliciousdays.com/cforms-plugin"><em>cforms</em> contact form by delicious:days</a></p>';


	### either show message above or below
	$usermessage_text	= check_default_vars($usermessage_text,$no);
	$usermessage_text	= check_cust_vars($usermessage_text,$track,$no);

	if( substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=='y' && !($success&&$cformsSettings['form'.$no]['cforms'.$no.'_redirect']==2))
		$content .= $tt . '<div id="usermessage'.$no.'b" class="cf_info ' . $usermessage_class . $umc . '" >' . $usermessage_text . '</div>' . $nl;

	return $content;
}


### some css for positioning the form elements
function cforms_style() {
	global $wp_query, $cforms_root, $localversion, $cformsSettings;

	### add content actions and filters
	$page_obj = $wp_query->get_queried_object();

	$onPages  = str_replace(' ','',stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_include'] )));
	$onPagesA = explode(',', $onPages);

	if( $onPages=='' || in_array($page_obj->ID,$onPagesA) ){

		echo "\n<!-- Start Of Script Generated By cforms v".$localversion." [Oliver Seidel | www.deliciousdays.com] -->\n";
		if( $cformsSettings['global']['cforms_no_css']<>'1' )
			echo '<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/styling/' . $cformsSettings['global']['cforms_css'] . '" />'."\n";
		echo '<script type="text/javascript" src="' . $cforms_root. '/js/cforms.js"></script>'."\n";
		if( $cformsSettings['global']['cforms_datepicker']=='1' ){
			$nav = $cformsSettings['global']['cforms_dp_nav'];
			$dformat = str_replace(array('M','EE','E'),array('m','dddd','ddd'),stripslashes($cformsSettings['global']['cforms_dp_date']));
			echo '<script type="text/javascript" src="' . $cforms_root. '/js/calendar.js"></script>'."\n";
			echo '<script type="text/javascript">'."\n".
				 "\t".'var cforms = jQuery.noConflict();'."\n".
				 "\t".'Date.dayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
				 "\t".'Date.abbrDayNames = ['.stripslashes($cformsSettings['global']['cforms_dp_days']).'];'."\n".
				 "\t".'Date.monthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
				 "\t".'Date.abbrMonthNames = ['.stripslashes($cformsSettings['global']['cforms_dp_months']).'];'."\n".
				 "\t".'Date.firstDayOfWeek = '.stripslashes($cformsSettings['global']['cforms_dp_start']).';'."\n".
				 "\t".''."\n".
				 "\t".'Date.fullYearStart = "20";'."\n".
				 "\t".'cforms.dpText = { TEXT_PREV_YEAR:"'.stripslashes($nav[0]).'",'. ### Previous year
				 'TEXT_PREV_MONTH:"'.stripslashes($nav[1]).'",'.
				 'TEXT_NEXT_YEAR:"'.stripslashes($nav[2]).'",'.
				 'TEXT_NEXT_MONTH:"'.stripslashes($nav[3]).'",'.
				 'TEXT_CLOSE:"'.stripslashes($nav[4]).'",'.
				 'TEXT_CHOOSE_DATE:"'.stripslashes($nav[5]).'",'.
				 'ROOT:"'.$cforms_root.'"};'."\n".
				 "\t".'cforms(function() { Date.format = "dd/mm/yyyy"; cforms(".cf_date").datePicker( {startDate:"01/01/1899",verticalOffset:5,horizontalOffset:5} ); Date.format = "'.$dformat.'"; });'."\n".
				 '</script>'."\n";
		}
		echo '<!-- End Of Script Generated By cforms -->'."\n\n";
	}
}

function findlast( $haystack,$needle,$offset=NULL ){
	if( ($pos = strpos( strrev($haystack) , strrev($needle) , $offset)) === false ) return false;
    return strlen($haystack) - $pos - strlen($needle);
}

### replace placeholder by generated code
function cforms_insert( $content ) {
	global $post, $cformsSettings; $newcontent='';

	$last=0;
	if ( ($a=strpos($content,'<!--cforms'))!==false ) {  ### only if form tag is present!

		$p_offset= 0;
		$part_content = substr( $content, 0, $a-$last );
		$p_open  = findlast($part_content,'<p>');
		$p_close = findlast($part_content,'</p>');

		### wrapped in <p> ?
		$p_offset = ($p_close < $p_open || ($p_open!==false && $p_close===false) ) ? $p_open : $a;

		$forms = $cformsSettings['global']['cforms_formcount'];

		$fns = array();
		for ($i=0;$i<$forms;$i++) {
			$no = ($i==0)?'':($i+1);
			$fns[sanitize_title_with_dashes($cformsSettings['form'.$no]['cforms'.$no.'_fname'])] = $i+1;
		}

		while( $a !== false ){

			$b = strpos($content,'-->',$a);

			$Fid = substr($content,$a+10,($b-$a-10));
			$Fname = '';

			if ( ($fQ=strpos($Fid,'"'))!==false )
				$Fname = sanitize_title_with_dashes(substr( $Fid, $fQ+1, strpos($Fid,'"',$fQ+1)-$fQ-1 ));

			$newcontent .= substr($content,$last,$p_offset-$last);

			if( $Fname !== '' )
				$newcontent .= cforms('',$fns[$Fname]);
			else
				$newcontent .= cforms('',$Fid);


			$p_open_after  = strpos($content,'<p>',$b);
			$p_close_after = strpos($content,'</p>',$b);

			### wrapped in <p> ?
			$b = ($p_close_after < $p_open_after || ($p_close_after!==false && $p_open_after===false)) ? $p_close_after+1 : $b;  //add'l +3 covered by $last = $b+3; !! :-)


			$a = strpos($content,'<!--cforms',$b);
			$last = $b+3;


			### next wrapping <p> tags
			$part_content = substr( $content, $last, $a-$last );
			$p_open  = findlast($part_content,'<p>');
			$p_close = findlast($part_content,'</p>');

			### wrapped in <p> ?
			$p_offset = ($p_close < $p_open) ? $a-(strlen($part_content)-$p_open) : $a;

		}
		$newcontent .= substr($content,$last);

		return $newcontent;
	}
	else
		return $content;
}

### build field_stat string from array (for custom forms)
function build_fstat($fields) {
    $cfarray = array();
    for($i=0; $i<count($fields['label']); $i++) {
        if ( $fields['type'][$i] == '') $fields['type'][$i] = 'textfield';
        if ( $fields['isreq'][$i] == '') $fields['isreq'][$i] = '0';
        if ( $fields['isemail'][$i] == '') $fields['isemail'][$i] = '0';
        if ( $fields['isclear'][$i] == '') $fields['isclear'][$i] = '0';
        if ( $fields['isdisabled'][$i] == '') $fields['isdisabled'][$i] = '0';
        if ( $fields['isreadonly'][$i] == '') $fields['isreadonly'][$i] = '0';
        $cfarray[$i]=$fields['label'][$i].'$#$'.$fields['type'][$i].'$#$'.$fields['isreq'][$i].'$#$'.$fields['isemail'][$i].'$#$'.$fields['isclear'][$i].'$#$'.$fields['isdisabled'][$i].'$#$'.$fields['isreadonly'][$i];
    }
    return $cfarray;
}

### inserts a cform anywhere you want
function insert_cform($no='') {
	global $post;

	$no = ($no=='0')?'':$no;  ### safety net
	$no = check_form_name($no);

	if ( isset($_GET['pid']) )
		$pid = $_GET['pid'];
	else if ($post->ID == 0)
		$pid = false;
	else
		$pid = $post->ID;

	if ( !$pid )
		echo cforms('',$no);
	else
		echo check_for_taf($no,$pid)?cforms('',$no):'';
}

### inserts a custom cform anywhere you want
function insert_custom_cform($fields='',$no='') {
	global $post;

	$no = ($no=='0')?'':$no;  ### safety net
	$no = check_form_name($no);

	if ( isset($_GET['pid']) )
		$pid = $_GET['pid'];
	else if ($post->ID == 0)
		$pid = false;
	else
		$pid = $post->ID;

	if ( !$pid )
		echo cforms($fields,$no.'+');
	else
		echo check_for_taf($no,$pid)?cforms($fields,$no.'+'):'';
}

### check form names/id's
function check_form_name($no) {
	global $cformsSettings;

	if( is_numeric($no) || $no=='' ) return $no;

	$forms = $cformsSettings['global']['cforms_formcount'];

	for ($i=0;$i<$forms;$i++) {
		$no2 = ($i==0)?'':($i+1);
		if ( stripslashes($cformsSettings['form'.$no]['cforms'.$no2.'_fname']) == $no )
			return $no2;
	}
}

### check if t-f-a is set
function check_for_taf($no,$pid) {
	global $cformsSettings;

	if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)<>'1')
		return true;

	$tmp = get_post_custom($pid);
	return ( $tmp["tell-a-friend"][0] == '1' )?true:false;
}

### public function: check if post is t-f-a enabled
function is_tellafriend($pid) {
	$tmp = get_post_custom($pid);
	return ($tmp["tell-a-friend"][0]=='1')?true:false;
}

### WP 2.7 admin menu hook
function cforms_post_box(){
	global $tafstring;
	echo $tafstring;
}
function add_cforms_post_boxes(){
	add_meta_box('cformspostbox', __('cforms Tell-A-Friend', 'cforms'), 'cforms_post_box', 'post', 'normal', 'high');
	add_meta_box('cformspostbox', __('cforms Tell-A-Friend', 'cforms'), 'cforms_post_box', 'page', 'normal', 'high');
}

### up to WP 2.7 and lower
function taf_admin() {
	global $wp_db_version, $tafstring;
    if ( $wp_db_version >= 6846 ){
        ?>
        <fieldset id="cformsTAF" class="postbox closed">
            <h3><?php _e('cforms Tell-A-Friend', 'cforms'); ?></h3>
            <div class="inside"><?php echo $tafstring; ?></div>
        </fieldset>
        <?php
    }else {
        ?>
        <fieldset id="cformsTAF" class="dbx-box">
            <h3 class="dbx-handle"><?php _e('cforms Tell-A-Friend', 'cforms'); ?></h3>
                <div class="dbx-content"><?php echo $tafstring; ?></div>
        </fieldset>
        <?php
    }
}

### Add Tell A Friend processing
function enable_tellafriend($post_ID) {
	global $wpdb;

	if ( isset($_POST['action']) && $_POST['action']=='autosave' )
    	return;

	$tellafriend_status = isset($_POST['tellafriend']);

	if($tellafriend_status && intval($post_ID) > 0)
		add_post_meta($post_ID, 'tell-a-friend', '1', true);
	else if ( isset($_POST['post_ID']) )
		delete_post_meta($post_ID, 'tell-a-friend');
}

### cforms widget
function widget_cforms_init() {

	global $cforms_root, $wp_registered_widgets, $cformsSettings;

	if (! function_exists("register_sidebar_widget")) {
		return;
	}

	function widget_cforms($args) {
		$cformsSettings = get_option('cforms_settings');

		extract($args);
		preg_match('/^.*widgetcform([^"]*)".*$/',$before_widget,$form_no);
		$no = ($form_no[1]=='0')?'':(int)($form_no[1]);
		$title = htmlspecialchars(stripslashes($cformsSettings['global']['widget_title'][$form_no[1]]));
		echo $before_widget.$before_title.$title.$after_title;;
		insert_cform($no);
		echo $after_widget;
	}

	### Function: WP-Polls Widget Options
	function widget_cforms_options($no) {
		global $wpdb;
		$cformsSettings = get_option('cforms_settings');

		if ( isset($_POST['cforms-title'.$no]) ) {
			$cformsSettings['global']['widget_title'][$no] = strip_tags($_POST['cforms-title'.$no]);
			update_option('cforms_settings',$cformsSettings);
		}

		echo '<p style="text-align: left;"><label for="cforms-title'.$no.'">';
		_e('Title', 'cforms');
		echo ': </label><input type="text" id="cforms-title'.$no.'" name="cforms-title'.$no.'" value="'.htmlspecialchars(stripslashes($cformsSettings['global']['widget_title'][$no])).'" /></p>'."\n";
		echo '<input type="hidden" id="cforms-submit'.$no.'" name="cforms-submit'.$no.'" value="'.$no.'" />'."\n";

	}

	for ( $i=0;$i<$cformsSettings['global']['cforms_formcount'];$i++ ) {

		$no = ($i==0)?'0':($i+1);
		$name = 'cformsII'. (($i==0)?'':' no.'.($i+1));
		$form = substr($cformsSettings['form'.$no]['cforms'.$no.'_fname'],0,10).'...';

		register_sidebar_widget($name, 'widget_cforms','widgetcform'.$no);
		register_widget_control($name, 'widget_cforms_options', 200, 300, $no);

		$wp_registered_widgets[sanitize_title($name)]['description'] = ($i==0)?__('Add cforms default form', 'cforms'):__('Add form', 'cforms').' "'.$form.'"';
	}

}

### get # of submission left (max subs)
function get_cforms_submission_left($no='') {
	global $wpdb, $cformsSettings;

	if ( $no==0 || $no==1 ) $no='';
	$max   = (int)$cformsSettings['form'.$no]['cforms'.$no.'_maxentries'];

	if( $max == '' || $max == 0 || $cformsSettings['global']['cforms_database']=='0' )
		return -1;

	$entries = $wpdb->get_row("SELECT count(id) as submitted FROM {$wpdb->cformssubmissions} WHERE form_id='{$no}'");

	if( $max-$entries->submitted > 0)
		return ($max-$entries->submitted);
	else
		return 0;
}

### get current page
function get_request_uri() {
	$request_uri = $_SERVER['REQUEST_URI'];
	if ( !isset($_SERVER['REQUEST_URI']) || (strpos($_SERVER['SERVER_SOFTWARE'],'IIS')!==false && strpos($_SERVER['REQUEST_URI'],'wp-admin')===false) ){
	    if(isset($_SERVER['SCRIPT_NAME']))
	        $request_uri = $_SERVER['SCRIPT_NAME'];
	    else
	        $request_uri = $_SERVER['PHP_SELF'];
	}
	return $request_uri;
}

### PLUGIN VERSION CHECK ON PLUGINS PAGE
add_action( 'after_plugin_row', 'cf_check_plugin_version' );
function cf_check_plugin_version($plugin)
{
	global $plugindir,$localversion;

 	if( strpos($plugindir.'/cforms.php',$plugin)!==false )
 	{
		$checkfile = "http://www.deliciousdays.com/download/cforms.chk";

		$vcheck = wp_remote_fopen($checkfile);

		if($vcheck)
		{
			$version = $localversion;

			$status = explode('@', $vcheck);
			$theVersion = $status[1];
			$theMessage = $status[3];

			if( (version_compare(strval($theVersion), strval($version), '>') == 1) )
			{
				$msg = __("Latest version available:", "sforum").' <strong>'.$theVersion.'</strong><br />'.$theMessage;
				$msg.= '<br /><a href="http://www.deliciousdays.com/cforms-plugin/">cforms by delicious:days</a>';
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em;">'.$msg.'</td>';
			} else {
				return;
			}
		}
	}
}

### add actions
global $tafstring;
if (function_exists('add_action')){

	### widget init
	add_action('plugins_loaded', 'widget_cforms_init');

	### get location?
	$request_uri = get_request_uri();

	$admin   = ( strpos($request_uri,'wp-admin')!==false )?true:false;
	$cfadmin = ( strpos($_SERVER['QUERY_STRING'],$plugindir.'/cforms')!==false )?true:false;

	### dashboard
	if ( $cformsSettings['global']['cforms_showdashboard']=='1' && $cformsSettings['global']['cforms_database']=='1' ) {
		require_once(dirname(__FILE__) . '/lib_dashboard.php');
		add_action( 'activity_box_end', 'cforms_dashboard', 1 );
	}
	### cforms specific stuff
	if ( $cfadmin ) {
		require_once(dirname(__FILE__) . '/lib_functions.php');
		add_action('admin_head', 'cforms_options_page_style');
		add_action('init', 'download_cforms');
        add_action('wp_print_scripts', 'cforms_scripts' );
	}
 	### other admin stuff
	if ( $admin ) {
		require_once(dirname(__FILE__) . '/lib_functions.php');
		add_action('admin_menu', 'cforms_menu');

	    ### Check all forms for TAF and set variables
	    for ( $i=1;$i<=$cformsSettings['global']['cforms_formcount'];$i++ ) {
	        $tafenabled = ( substr($cformsSettings['form'.(($i=='1')?'':$i)]['cforms'.(($i=='1')?'':$i).'_tellafriend'],0,1)=='1') ? true : false;
	        if ( $tafenabled ) break;
	    }
	    $tafform = ($i==1)?'':$i;

	    if ( $tafenabled ){
	        $edit_post = intval($_GET['post']);
            $tmp = get_post_custom($edit_post);
            $taf = $tmp["tell-a-friend"][0];

            $tafchk = ($taf=='1' || ($edit_post=='' && substr($cformsSettings['form'.$tafform]['cforms'.$tafform.'_tellafriend'],1,1)=='1') )?'checked="checked"':'';

			$tafstring = '<label for="tellafriend" class="selectit"><input type="checkbox" id="tellafriend" name="tellafriend" value="1"'. $tafchk .'/>&nbsp;'. __('T-A-F enable this post/page', 'cforms').'</label>';

	        ### add admin boxes
	        if ( $wp_db_version < 6846 ){
	            add_action('dbx_post_sidebar', 'taf_admin');
	            add_action('dbx_page_sidebar', 'taf_admin');        ###  < WP25.
	        }else if ( $wp_db_version < 9872 ) {
	            add_action('edit_form_advanced', 'taf_admin');      ### >= WP2.5
	            add_action('edit_page_form', 'taf_admin');          ### >= WP2.5
	        }else{
	            add_action('admin_menu', 'add_cforms_post_boxes');  ### >= WP2.7
	        }
			add_action('save_post', 'enable_tellafriend');

	    } ### if tafenabled

	} ### if admin

}

add_filter('wp_head', 'cforms_style');
add_filter('the_content', 'cforms_insert',10);
?>