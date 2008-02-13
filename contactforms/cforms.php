<?php
/*
Plugin Name: cforms II
Plugin URI: http://www.deliciousdays.com/cforms-plugin
Description: cforms II offers unparalleled flexibility in deploying contact forms across your blog. Features include: comprehensive SPAM protection, Ajax support, Backup & Restore, Multi-Recipients, Role Manager support, Database tracking and many more. Please see the <a href="http://www.deliciousdays.com/cforms-forum?forum=2&topic=2&page=1">VERSION HISTORY</a> for <strong>what's new</strong> and current <strong>bugfixes</strong>.
Author: Oliver Seidel
Version: 7.5
Author URI: http://www.deliciousdays.com
*/

/*
Copyright 2006-2008  Oliver Seidel   (email : oliver.seidel@deliciousdays.com)
/*

***
*** PLEASE NOTE UPDATED "WP comment feature" code snippet, check with the HELP page
***

WHAT's NEW in cforms II - v7.5

*) feature: WP comments feature completely revised
	+) no more dependency on wp-comments-post.php
	+) fully supporting comment form validation (esp. nonAjax!)
	+) Ajax'iefied

*) bugfix: PHP regexp testing for '0' caused a false positive
*) bugfix: T-A-F enable new posts/pages by default -> was broken if TAF form was your default (1st) form
*) bugfix: a few CSS fixes (.mailerr and other)
*) other: major admin UI clean-up, making it xHTML compliant again

*/

$localversion = '7.5';
load_plugin_textdomain('cforms');

### http://trac.wordpress.org/ticket/3002
$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

global $dpflag;
$dpflag = false;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

require		(dirname(__FILE__) . '/buttonsnap.php');
require_once(dirname(__FILE__) . '/editor.php');


### SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

if ( ABSPATH=='' || WPINC=='' ) {
	define('ABSPATH', substr(dirname(__FILE__),0,strpos(dirname(__FILE__),'/wp-includes')) );
	define('WPINC', '/wp-includes/');
} 

if ( $smtpsettings[0]=='1' ) {
	if ( file_exists(dirname(__FILE__) . '/phpmailer/class.phpmailer.php') ) {
		require_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php');
		require_once(dirname(__FILE__) . '/phpmailer/class.smtp.php');
		require_once(dirname(__FILE__) . '/phpmailer/cforms_phpmailer.php');
	}
	else
		$smtpsettings[0]=='0';
}

### other global stuff
$track = array(); 
$Ajaxpid = '';
$AjaxURL = '';


### need this for captchas
add_action('template_redirect', 'start_cforms_session');
function start_cforms_session() {
	@session_cache_limiter('private, must-revalidate');
	@session_cache_expire(0);
	@session_start();
}



// do a couple of things necessary as part of plugin activation
if (isset($_GET['activate']) && $_GET['activate'] == 'true')
	require_once(dirname(__FILE__) . '/lib_activate.php');



// load download function
if ( strpos($_SERVER['HTTP_REFERER'], $plugindir.'/cforms') !== false )
	require_once(dirname(__FILE__) . '/lib_functions.php');



// Can't use WP's function here, so lets use our own
if ( !function_exists('cf_getip') ) :
function cf_getip()
{
	if (isset($_SERVER))
	{
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))	$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		else										$ip_addr = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) 	$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )	  	$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		else										$ip_addr = getenv( 'REMOTE_ADDR' );
	}
	return $ip_addr;
}
endif;


//
// Special Character Suppoer in subject lines
//
function encode_header ($str) {

	$x = preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
	
	if ($x == 0)
		return ($str);

	$maxlen = 75 - 7 - strlen( get_option('blog_charset') );
	
	$encoded = base64_encode($str);
	$maxlen -= $maxlen % 4;
	$encoded = trim(chunk_split($encoded, $maxlen, "\n"));
	
	$encoded = preg_replace('/^(.*)$/m', " =?".get_option('blog_charset')."?B?\\1?=", $encoded);
	$encoded = trim($encoded);
	
	return $encoded;

}


//
// write DB record
//

function write_tracking_record($no,$field_email){

		global $wpdb;
		global $track;

		if ( get_option('cforms_database') == '1'  ) {

			$page = (get_option('cforms'.$no.'_tellafriend')=='2')?$_POST['cforms_pl'.$no]:get_current_page(); // WP comment fix

			$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,email,ip,sub_date) VALUES ".
						 "('" . $no . "', '" . $field_email . "', '" . cf_getip() . "', '".gmdate('Y-m-d H:i:s', current_time('timestamp'))."');");
	
    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformsdata;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
						 "('$subID','page','$page'),";
						 
			foreach ( array_keys($track) as $key )
				$sql .= "('$subID','".addslashes($key)."','".addslashes($track[$key])."'),";
			
			$wpdb->query(substr($sql,0,-1));			
		}
		else
			$subID = 'noid';
			
	return $subID;
}


//
// replace standard & custom variables in message/subject text
//

function get_current_page(){

	if ( strpos($_SERVER['REQUEST_URI'],'?')>0 )
		$page = substr( $_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'?')-1);
	else
		$page = $_SERVER['REQUEST_URI'];
	
	$page = (trim($page)=='')?$_SERVER['HTTP_REFERER']:trim($page); // for ajax
	return $page;
	
}

function check_default_vars($m,$no) {

		global $subID, $Ajaxpid, $AjaxURL, $post, $wpdb, $wp_db_version;

		if ( $_POST['comment_post_ID'.$no] )
			$pid = $_POST['comment_post_ID'.$no];
		else if ( $Ajaxpid<>'' )
			$pid = $Ajaxpid;
		else if ( function_exists('get_the_ID') )
			$pid = get_the_ID();

		if ( $_POST['cforms_pl'.$no] )
			$permalink = $_POST['cforms_pl'.$no];
		else if ( $Ajaxpid<>'' )
			$permalink = $AjaxURL;
		else if ( function_exists('get_permalink') && function_exists('get_userdata') )
			$permalink = get_permalink($pid);
				
		// special fields: {Form Name}, {Date}, {Page}, {IP}, {PERMALINK}, {TITLE}, {EXCERPT},{BLOGNAME}
		$date = mysql2date(get_option('date_format'), current_time('mysql'));
		$time = gmdate(get_option('time_format'), current_time('timestamp'));
		$page = get_current_page();
				
		if ( get_option('cforms'.$no.'_tellafriend')=='2' ) // WP comment fix
			$page = $permalink;

		$find = $wpdb->get_row("SELECT p.post_title, p.post_excerpt, u.display_name FROM $wpdb->posts AS p LEFT JOIN ($wpdb->users AS u) ON p.post_author = u.ID WHERE p.ID='$pid'");

		if ( $wp_db_version >= 3440 ) //&& function_exists( 'wp_get_current_user' )
			$CurrUser = wp_get_current_user();
		
		$m 	= str_replace( '{Form Name}',	get_option('cforms'.$no.'_fname'), $m );
		$m 	= str_replace( '{Page}',		$page, $m );
		$m 	= str_replace( '{Date}',		$date, $m );
		$m 	= str_replace( '{Author}',		$find->display_name, $m );
		$m 	= str_replace( '{Time}',		$time, $m );
		$m 	= str_replace( '{IP}',			cf_getip(), $m );
		$m 	= str_replace( '{BLOGNAME}',	get_option('blogname'), $m );

		$m 	= str_replace( '{CurUserID}',	$CurrUser->ID, $m );
		$m 	= str_replace( '{CurUserName}',	$CurrUser->display_name, $m );
		$m 	= str_replace( '{CurUserEmail}',$CurrUser->user_email, $m );
		
		$m 	= str_replace( '{Permalink}',	$permalink, $m );
		$m 	= str_replace( '{Title}',		$find->post_title, $m );
		$m 	= str_replace( '{Excerpt}',		$find->post_excerpt, $m );

		$m 	= preg_replace( "/\r\n\./", "\r\n", $m );			
		
		if  ( get_option('cforms_database') && $subID<>'' )
			$m 	= str_replace( '{ID}', $subID, $m );
							 
		return $m;
}

function check_cust_vars($m,$t) {

	preg_match_all('/\\{([^\\{]+)\\}/',$m,$findall);
	
	if ( count($findall[1]) > 0 ) {
		$allvars = array_keys($t);
		foreach ( $findall[1] as $fvar ) {
			if( in_array($fvar,$allvars ) )
				$m = str_replace('{'.$fvar.'}', $t[$fvar], $m);
		}
	}
	return $m;
	
}


// Common HTML message information
$styles="<HEAD>\n<style><!--\n".
		".fs-td { font:bold 1.2em Arial; letter-spacing:2px; border-bottom:2px solid #7babfb; padding:10px 0 5px; text-align:center; background:#ddecff;}\n".
		".data-td { font-weight:bold; padding-right:20px; vertical-align:top; }\n".
		".datablock { background:#c1ddff; width:90%; padding:2px;}\n".
		".cforms { font:normal 10px Arial; color:#777;}\n".
		"--></style>\n</HEAD>";

//
// ajax submission of form
//
function cforms_submitcomment($content) {

	global $wpdb, $subID, $styles, $smtpsettings, $track, $Ajaxpid, $AjaxURL, $wp_locale;

	$isAjaxWPcomment = strpos($content,'***');// WP comment feature
	$content = explode('***', $content);
	$content = $content[0];

	$content = explode('+++', $content); // Added special fields
	$Ajaxpid = $content[1];
	$AjaxURL = $content[2];

	$segments = explode('$#$', $content[0]);
	$params = array();

	$sep = (strpos(__FILE__,'/')===false)?'\\':'/';
	$WPpluggable = substr( dirname(__FILE__),0,strpos(dirname(__FILE__),'wp-content')) . 'wp-includes'.$sep.'pluggable.php';
	if ( file_exists($WPpluggable) )
		require_once($WPpluggable);
	
	$CFfunctions = dirname(__FILE__).$sep.'my-functions.php';
	if ( file_exists($CFfunctions) )
		include_once($CFfunctions);

	if( $isAjaxWPcomment ){
		$wpconfig = substr( dirname(__FILE__),0,strpos(dirname(__FILE__),'wp-content')) . 'wp-config.php';
		require_once($wpconfig);
	
		if ( function_exists('wp_get_current_user') )	
			$user = wp_get_current_user();
	}
	
/*
	$locale = get_locale();

	$wplocale = substr( dirname(__FILE__),0,strpos(dirname(__FILE__),'wp-content')) . 'wp-includes/locale.php';
	if ( file_exists($wplocale) )
		include_once($wplocale);


	if ( class_exists('WP_Locale') )
		$wp_locale =& new WP_Locale();
*/

	for($i = 1; $i <= sizeof($segments); $i++)
		$params['field_' . $i] = $segments[$i];

	// fix reference to first form
	if ( $segments[0]=='1' ) $params['id'] = $no = ''; else $params['id'] = $no = $segments[0];


	// user filter ?
	if( function_exists('my_cforms_ajax_filter') )
		$params = my_cforms_ajax_filter($params);


	// init variables
	$formdata = '';
	$htmlformdata = '';

	$track = array();
	$trackinstance = array();
	
 	$to_one = "-1";
  	$ccme = false;
	$field_email = '';
	$off = 0;
	$fieldsetnr=1;

	$taf_youremail = false;
	$taf_friendsemail = false;

	// form limit reached
	if ( get_option('cforms'.$no.'_maxentries')<>'' && get_cforms_submission_left($no)==0 ){
	    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
	    return $pre . preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_limittxt'))) . $hide;
	}

	//space for pre formatted text layout
	$customspace = (int)(get_option('cforms'.$no.'_space')>0)?get_option('cforms'.$no.'_space'):30;


	for($i = 1; $i <= sizeof($params)-2; $i++) {

			$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));

			// filter non input fields
			while ( in_array($field_stat[1],array('fieldsetstart','fieldsetend','textonly')) ) {
																				
					if ( $field_stat[1] <> 'textonly' ){ // include and make only fieldsets pretty!

							//just for email looks
							$space='-'; 
							$n = ((($customspace*2)+2) - strlen($field_stat[0])) / 2;
							$n = ($n<0)?0:$n;
							if ( strlen($field_stat[0]) < (($customspace*2)-2) )
								$space = str_repeat("-", $n );
								
							$formdata .= substr("\n$space$field_stat[0]$space",0,($customspace*2)) . "\n\n";
							$htmlformdata .= '<tr><td class=3D"fs-td" colspan=3D"2">' . $field_stat[0] . '</td></tr>';
							
							if ( $field_stat[1] == 'fieldsetstart' )
								$track['Fieldset'.$fieldsetnr++] = $field_stat[0];

					}
					
		   			//get next in line...
					$off++;
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					
					if( $field_stat[1] == '')
						break 2; // all fields searched, break both while & for

			}

			// filter all redundant WP comment fields if user is logged in
			while ( in_array($field_stat[1],array('cauthor','email','url')) && $user->ID ) {
 
			 		switch( $field_stat[1] ){
						case 'cauthor': $track['cauthor'] = $user->display_name; break; 
						case 'email': $track['email'] = $field_email = $user->user_email; break; 
						case 'url': $track['url'] = $user->user_url; break;
					}					
					$formdata .= stripslashes( $field_stat[1] ). ': '. $space . $track[$field_stat[1]] . "\n";
					$htmlformdata .= '<tr><td class=3D"data-td">' . $field_stat[1] . '</td><td>' . $track[$field_stat[1]] . '</td></tr>';
	
					$off++;
					$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
					
					if( $field_stat[1] == '')
						break 2; // all fields searched, break both while & for
			}

			$field_name = $field_stat[0];
			$field_type = $field_stat[1];

			// strip out default value
			if ( ($pos=strpos($field_name,'|')) )
			    $field_name = substr($field_name,0,$pos);

			// special WP comment fields
			if( in_array($field_stat[1],array('cauthor','email','url','comment','send2author')) )
				$field_name = $field_stat[1];

			// special Tell-A-Friend fields
			if ( $taf_friendsemail == '' && $field_type=='friendsemail' && $field_stat[3]=='1')
					$field_email = $taf_friendsemail = $params ['field_' . $i];
			if ( $taf_youremail == '' && $field_type=='youremail' && $field_stat[3]=='1')
					$taf_youremail = $params ['field_' . $i];
			if ( $field_type=='friendsname' )
					$taf_friendsname = $params ['field_' . $i];
			if ( $field_type=='yourname' )
					$taf_yourname = $params ['field_' . $i];


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

			if ( $field_type == "emailtobox" ){  			//special case where the value needs to bet get from the DB!

				$field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
				$to_one = $params ['field_' . $i];

				$offset = (strpos($field_name[1],'|')===false) ? 1 : 2; // names come usually right after the label


				$value = $field_name[(int)$to_one+$offset];  // values start from 0 or after!
				$field_name = $field_name[0];

	 		}
			else {
			    if ( strtoupper(get_option('blog_charset')) <> 'UTF-8' && function_exists('mb_convert_encoding'))
        		    $value = mb_convert_encoding(utf8_decode( stripslashes( $params['field_' . $i] ) ), get_option('blog_charset'));   // convert back and forth to support also other than UTF8 charsets
                else
                    $value = stripslashes( $params['field_' . $i] );
            }

			//only if hidden!
			if( $field_type == 'hidden' )
				$value = rawurldecode($value);


			// Q&A verification
			if ( $field_type == "verification" ) 
					$field_name = __('Q&A','cforms');

			
			//for db tracking
			$inc='';
			$trackname=trim($field_name);
			if ( array_key_exists($trackname, $track) ){
				if ( $trackinstance[$trackname]=='' )
					$trackinstance[$trackname]=2;
				$inc = '___'.($trackinstance[$trackname]++);
			}
			$track[$trackname.$inc] = $value;

			//for all equal except textareas!
			$htmlvalue = str_replace("=","=3D",$value);
			$htmlfield_name = $field_name;

			// just for looks: break for textarea
 			if ( $field_type == "textarea" || $field_type == "comment" ) {
					$field_name = "\n" . $field_name;
					$htmlvalue = str_replace(array("=","\n"),array("=3D","<br />\n"),$value);
					$value = "\n" . $value . "\n";
			}

			// just for looks:rest
		  	$space='';
			if ( strlen(stripslashes($field_name)) < $customspace )   // don't count ->\"  sometimes adds more spaces?!?
				  $space = str_repeat(" ",$customspace-strlen(stripslashes($field_name)));

			// create formdata block for email
			if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' ) {
				$formdata .= stripslashes( $field_name ). ': '. $space . $value . "\n";
				$htmlformdata .= '<tr><td class=3D"data-td">' . $htmlfield_name . '</td><td>' . $htmlvalue . '</td></tr>';
			}
					
	} // for

	// assemble html formdata
	$htmlformdata = '<div class=3D"datablock"><table width=3D"100%" cellpadding=3D"2">' . stripslashes( $htmlformdata ) . '</table></div><span class=3D"cforms">powered by <a href=3D"http://www.deliciousdays.com/cforms-plugin">cformsII</a></span>';


	//
	// allow the user to use form data for other apps
	//
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') )
		my_cforms_action($trackf);



	// Catch WP-Comment function
	if ( $isAjaxWPcomment!==false && $track['send2author']=='0' ){

		require_once (dirname(__FILE__) . '/lib_WPcomment.php');

		if ($WPsuccess){
			$hide='';
			// redirect to a different page on suceess?
			if      ( get_option('cforms'.$no.'_redirect')==1 ) return get_option('cforms'.$no.'_redirect_page');
			else if ( get_option('cforms'.$no.'_redirect')==2 )	$hide = '|~~~';
	
		    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
		    return $pre . $WPresp . $hide;
		} 
		else {
		    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),1,1);
		    return $pre . $WPresp .'|---';
		}
		
	}


	
	//
	//reply to all email recipients
	//		
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes(get_option('cforms'.$no.'_email')) );

	// multiple recipients? and to whom is the email sent? to_one = picked recip.
	if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;

	// T-A-F overwrite?
	if ( $taf_youremail && $taf_friendsemail && substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";



	//
	// FIRST write into the cforms tables!
	//
	$subID = write_tracking_record($no,$field_email);


	//
	// ready to send email
	// email header 
	//

	$html_show = ( substr(get_option('cforms'.$no.'_formdata'),2,1)=='1' )?true:false;
	
	$fmessage='';
	
	$eol = "\n";
	if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
		$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
	
	$headers = "From: ". $frommail . $eol;
	$headers.= "Reply-To: " . $field_email . $eol;

	if ( ($tempBcc = stripslashes(get_option('cforms'.$no.'_bcc'))) != "")
	    $headers.= "Bcc: " . $tempBcc . $eol;

	$headers.= "MIME-Version: 1.0"  .$eol;
	if ($html_show) {
		$headers.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
		$fmessage = "This is a multi-part message in MIME format."  . $eol;
		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
	}
	else
		$headers.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";
	
	// prep message text, replace variables
	$message = get_option('cforms'.$no.'_header');
	$message = check_default_vars($message,$no);
	$message = stripslashes( check_cust_vars($message,$track) );

	// text text
	$fmessage .= $message . $eol;
	
	// need to add form data summary or is all in the header anyway?
	if(substr(get_option('cforms'.$no.'_formdata'),0,1)=='1')
		$fmessage .= $eol . $formdata . $eol;


	// HTML text
	if ($html_show) {
	
		// actual user message
		$htmlmessage = get_option('cforms'.$no.'_header_html');					
		$htmlmessage = check_default_vars($htmlmessage,$no);
		$htmlmessage = str_replace(array("=","\n"),array("=3D","<br />\n"), stripslashes( check_cust_vars($htmlmessage,$track) ) );

		$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;
		$fmessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;

		$fmessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
		$fmessage .= "<HTML>" . $eol;
		$fmessage .= $styles;
		$fmessage .= "<BODY>" . $eol;

		$fmessage .= $htmlmessage;

		// need to add form data summary or is all in the header anyway?
		if(substr(get_option('cforms'.$no.'_formdata'),1,1)=='1')
			$fmessage .= $eol . $htmlformdata;

		$fmessage .= "</BODY></HTML>"  . $eol . $eol;

	}


	//either use configured subject or user determined
	$vsubject = get_option('cforms'.$no.'_subject');
	$vsubject = check_default_vars($vsubject,$no);
	$vsubject = stripslashes( check_cust_vars($vsubject,$track) );


	// SMTP server or native PHP mail() ?
	if ( $smtpsettings[0]=='1' )
		$sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata );
	else
		$sentadmin = @mail($to, encode_header($vsubject), $fmessage, $headers);	

	if( $sentadmin==1 )
	{
		  // send copy or notification?
	    if ( (get_option('cforms'.$no.'_confirm')=='1' && $field_email<>'') || $ccme )  // not if no email & already CC'ed
	    {

					if ( ($frommail=stripslashes(get_option('cforms'.$no.'_fromemail')))=='' )
						$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';	
					
					// HTML message part?
					$html_show_ac = ( substr(get_option('cforms'.$no.'_formdata'),3,1)=='1' )?true:false;

					$automessage = '';

					$headers2 = "From: ". $frommail . $eol;
					$headers2.= "Reply-To: " . $replyto . $eol;
					
					if ( substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' ) //TAF: add CC
						$headers2.= "CC: " . $replyto . $eol;
					
					$headers2.= "MIME-Version: 1.0"  .$eol;
					if( $html_show_ac || ($html_show && $ccme) ){
						$headers2.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
						$automessage = "This is a multi-part message in MIME format."  . $eol;
						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
					}
					else
						$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"";
					

					// actual user message
					$cmsg = get_option('cforms'.$no.'_cmsg');					
					$cmsg = check_default_vars($cmsg,$no);
					$cmsg = check_cust_vars($cmsg,$track);
					
					// text text
					$automessage .= $cmsg  . $eol;

					// HTML text
					if ( $html_show_ac ) {
					
						// actual user message
						$cmsghtml = get_option('cforms'.$no.'_cmsg_html');					
						$cmsghtml = check_default_vars($cmsghtml,$no);
						$cmsghtml = str_replace(array("=","\n"),array("=3D","<br />\n"), check_cust_vars($cmsghtml,$track) );

						$automessage .= "------MIME_BOUNDRY_main_message"  . $eol;
						$automessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
						$automessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;
	
						$automessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
						$automessage .= "<HTML><BODY>"  . $eol;
						$automessage .= $cmsghtml;
						$automessage .= "</BODY></HTML>"  . $eol . $eol;
					}

					// replace variables
				    $subject2 = get_option('cforms'.$no.'_csubject');
					$subject2 = check_default_vars($subject2,$no);
					$subject2 = check_cust_vars($subject2,$track);
					
					// different cc & ac subjects?
					$t = explode('$#$',$subject2);
					$t[1] = ($t[1]<>'') ? $t[1] : $t[0];


					// email tracking via 3rd party?
					$field_email = (get_option('cforms'.$no.'_tracking')<>'')?$field_email.get_option('cforms'.$no.'_tracking'):$field_email;

					// if in Tell-A-Friend Mode, then overwrite header stuff...
					if ( $taf_youremail && $taf_friendsemail && substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' )
						$field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>";

					
					if ( $ccme ) {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[1]), $message, $formdata, $htmlmessage, $htmlformdata,'ac' );
						else
							$sent = @mail($field_email, encode_header(stripslashes($t[1])), $fmessage, $headers2); //takes $message!!
					}
					else {
						if ( $smtpsettings[0]=='1' )
							$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[0]) , $cmsg , '', $cmsghtml, '','ac' );
						else
							$sent = @mail($field_email, encode_header(stripslashes($t[0])), stripslashes($automessage), $headers2);
					}
					
		  		if( $sent<>'1' ) {
					$err = __('Error occurred while sending the auto confirmation message: ','cforms') . ($smtpsettings[0]?" ($sent)":'');
				    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),1,1);
				    return $pre . $err .'|!!!';
		  			
		  		}
	    } // cc

		$hide='';
		// redirect to a different page on suceess?
		if ( get_option('cforms'.$no.'_redirect')==1 ) {
			return get_option('cforms'.$no.'_redirect_page');
		}
		else if ( get_option('cforms'.$no.'_redirect')==2 || get_cforms_submission_left($no)==0 )
			$hide = '|~~~';

		// return success msg
	    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),0,1);
	    return $pre . preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success'))) . $hide;

	} // no admin mail sent!

	else {

		// return error msg
		$err = __('Error occurred while sending the message: ','cforms') . ($smtpsettings[0]?'<br />'.$sentadmin:'');
	    $pre = $segments[0].'*$#'.substr(get_option('cforms'.$no.'_popup'),1,1);
	    return $pre . $err .'|!!!';
	}


} //function


//
// sajax stuff
//

if (!isset($SAJAX_INCLUDED)) {

	$GLOBALS['sajax_version'] = '0.12';	
	$GLOBALS['sajax_debug_mode'] = 0;
	$GLOBALS['sajax_export_list'] = array();
	$GLOBALS['sajax_request_type'] = 'POST';
	$GLOBALS['sajax_remote_uri'] = '';
	$GLOBALS['sajax_failure_redirect'] = '';
	 
	function sajax_init() {
	}
	
	function sajax_get_my_uri() {
		return $_SERVER["REQUEST_URI"];
	}
	$sajax_remote_uri = sajax_get_my_uri();
	
	function sajax_get_js_repr($value) {
		$type = gettype($value);
		
		if ($type == "boolean") {
			return ($value) ? "Boolean(true)" : "Boolean(false)";
		} 
		elseif ($type == "integer") {
			return "parseInt($value)";
		} 
		elseif ($type == "double") {
			return "parseFloat($value)";
		} 
		elseif ($type == "array" || $type == "object" ) {
			$s = "{ ";
			if ($type == "object") {
				$value = get_object_vars($value);
			} 
			foreach ($value as $k=>$v) {
				$esc_key = sajax_esc($k);
				if (is_numeric($k)) 
					$s .= "$k: " . sajax_get_js_repr($v) . ", ";
				else
					$s .= "\"$esc_key\": " . sajax_get_js_repr($v) . ", ";
			}
			if (count($value))
				$s = substr($s, 0, -2);
			return $s . " }";
		} 
		else {
			$esc_val = sajax_esc($value);
			$s = "'$esc_val'";
			return $s;
		}
	}

	function sajax_handle_client_request() {
		global $sajax_export_list;
		
		$mode = "";
		
		if (! empty($_GET["rs"])) 
			$mode = "get";
		
		if (!empty($_POST["rs"]))
			$mode = "post";
			
		if (empty($mode)) 
			return;

		$target = "";
		
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
			echo "-:$func_name not callable";
		else {
			echo "+:";
			$result = call_user_func_array($func_name, $args);
			echo "var res = " . trim(sajax_get_js_repr($result)) . "; res;";
		}
		exit;
	}
	
	// javascript escape a value
	function sajax_esc($val)
	{
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
	}

	function sajax_get_one_stub($func_name) {
		ob_start();	
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function sajax_show_one_stub($func_name) {
		echo sajax_get_one_stub($func_name);
	}
	
	function sajax_export() {
		global $sajax_export_list;
		
		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}
	
	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown;
		global $sajax_export_list;
		
		$html = "";
		if (! $sajax_js_has_been_shown) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}
	
	$SAJAX_INCLUDED = 1;
}

// $sajax_debug_mode = 1;
sajax_init();
sajax_export("cforms_submitcomment");
sajax_export("reset_captcha");
sajax_handle_client_request();	


//
// reset captcha image
//
function reset_captcha( $no = '' ){
    @session_start();
	$_SESSION['turing_string_'.$no] = rc();
		
	//fix for windows!!!
	if ( strpos(__FILE__,'\\') ){
		$path = preg_replace( '|.*(wp-content.*)cforms.php|','${1}', __FILE__ );
		$path = '/'.str_replace('\\','/',$path);
	}
	else
		$path = preg_replace( '|.*(/wp-content/.*)/.*|','${1}', __FILE__ );
	
	$path = get_bloginfo('wpurl') . $path;
	
	$newimage = md5($_SESSION['turing_string_'.$no]).'|'.$no.'|'.$path.'/cforms-captcha.php?ts='.$no.get_captcha_uri();	 
	return $newimage;
}



//
// main function
//

function cforms($args = '',$no = '') {

	global $smtpsettings, $styles, $subID, $cforms_root, $wpdb, $track, $wp_db_version;

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
		$field_count = get_option('cforms'.$no.'_count_fields');
	}
	
	$content = '';

	$err=0;
	$filefield=0;   // for multiple file upload fields
	
	$validations = array();
	$all_valid = 1;
	$off=0;
	$fieldsetnr=1;

	$c_errflag=false;
	$custom_error='';
	$usermessage_class='';

	//??? check for WP2.0.2
	if ( $wp_db_version >= 3440 && function_exists('wp_get_current_user') )
		$user = wp_get_current_user();

	
	if( isset($_REQUEST['sendbutton'.$no]) ) {  /* alternative sending: both events r ok!  */

		require_once (dirname(__FILE__) . '/lib_nonajax.php');

		$usermessage_class = $all_valid?' success':' failure';

	}


	if ( get_option('cforms'.$no.'_tellafriend')=='2' && $send2author ) //called from lib_WPcomments ?
		return $all_valid;


	//
	// paint form
	//
	$success=false;
	if ( isset($_GET['cfemail']) && get_option('cforms'.$no.'_tellafriend')=='2' ){ // fix for WP Comment (loading after redirect)
		$usermessage_class = ' success';
		$success=true;
		if ( $_GET['cfemail']=='sent' )
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms'.$no.'_success')) );
		elseif ( $_GET['cfemail']=='posted' )	
			$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes(get_option('cforms_commentsuccess')) );	
	}

	
	$break='<br />';
	$nl="\n";
	$tab="\t";
	$tt="\t\t";
	$ntt="\n\t\t";
	$nttt="\n\t\t\t";

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),0,1)=='y' ) {
		$content .= $ntt . '<div id="usermessage'.$no.'a" class="cf_info' . $usermessage_class . '">' . $usermessage_text . '</div>';
		$actiontarget = 'a';
 	} else if ( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' )
		$actiontarget = 'b';


	// redirect == 2 : hide form?    || or if max entries reached!
	if ( (get_option('cforms'.$no.'_redirect')==2 && isset($_REQUEST['sendbutton'.$no]) && $all_valid) )
		return $content;
	else if ( get_option('cforms'.$no.'_maxentries')<>'' && get_cforms_submission_left($no)==0 ){

		if ( $cflimit == "reached" )
			return stripslashes(get_option('cforms'.$no.'_limittxt'));
		else
			return $content.stripslashes(get_option('cforms'.$no.'_limittxt'));
		
	}
 	
 	//alternative form action
	$alt_action=false;
	if( get_option('cforms'.$no.'_action')=='1' ) {
		$action = get_option('cforms'.$no.'_action_page');
		$alt_action=true;
	}
	else if( get_option('cforms'.$no.'_tellafriend')=='2' )
		$action = $cforms_root . '/lib_WPcomment.php'; // re-route and use WP comment processing
 	else
		$action = $_SERVER['REQUEST_URI'] . '#usermessage'. $no . $actiontarget;

 	
	$content .= $ntt . '<form enctype="multipart/form-data" action="' . $action . '" method="post" class="cform" id="cforms'.$no.'form">' . $nl;


	// start with no fieldset
	$fieldsetopen = false;
	$verification = false;
	
	$captcha = false;
	$upload = false;
	$fscount = 1;
	$ol = false;
	global $dpflag;

	for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom ) 
      		$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . $i));
		else
    		$field_stat = explode('$#$', $customfields[$i-1]);
		
		$field_name       = $field_stat[0];
		$field_type       = $field_stat[1];
		$field_required   = $field_stat[2];
		$field_emailcheck = $field_stat[3];
		$field_clear      = $field_stat[4];
		$field_disabled   = $field_stat[5];
		$field_readonly   = $field_stat[6];


		// ommit certain fields
		if( in_array($field_type,array('cauthor','url','email')) && $user->ID )
			continue;


		// check for custom err message and split field_name
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
							$custom_error .= (get_option('cforms'.$no.'_customnames')=='1')?str_replace(' ','_',$input_name[1]):'cf'.$no.'_field_'.$i;
							$custom_error .= '$#$'.$fielderr.'|';
			    			break;
			    }
		
		}		

		//special treatment for selectboxes  
		if (  in_array($field_type,array('multiselectbox','selectbox','radiobuttons','send2author','checkbox','checkboxgroup','ccbox','emailtobox'))  ){

			$options = explode('#', stripslashes(($obj[0])) );
            $field_name = $options[0];
            
		}


		//check if fieldset is open
		if ( !$fieldsetopen && !$ol && $field_type<>'fieldsetstart') {
			$content .= $tt . '<ol class="cf-ol">';
			$ol = true;
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
		// setting the default val & regexp if it exists
		if ( ! in_array($field_type,array('fieldsetstart','fieldsetend','radiobuttons','send2author','checkbox','checkboxgroup','ccbox','emailtobox','multiselectbox','selectbox','verification')) ) {

		    // check if default val & regexp are set
		    $obj = explode('|', $obj[0],3);

			if ( $obj[2] <> '')	$reg_exp = str_replace('"','&quot;',stripslashes($obj[2])); else $reg_exp='';
		    if ( $obj[1] <> '')	$defaultvalue = check_default_vars(stripslashes(($obj[1])),$no);

			$field_name = $obj[0];
		}

		//Label ID's
		$labelIDx = '';
		$labelID  = (get_option('cforms_labelID')=='1')?' id="label-'.$no.'-'.$i.'"':'';
		
		//<li> ID's
		$liID = ( get_option('cforms_liID')=='1' || 
				  substr(get_option('cforms'.$no.'_showpos'),2,1)=="y" || 
				  substr(get_option('cforms'.$no.'_showpos'),3,1)=="y" )?' id="li-'.$no.'-'.$i.'"':'';
		
		//input field names & label
		$input_id = $input_name = (get_option('cforms'.$no.'_customnames')=='1')?str_replace(' ','_',$field_name):'cf'.$no.'_field_'.$i;
						
		$field_class = '';
		
		switch ($field_type){
			case 'verification':
				$input_id = $input_name = 'cforms_q'.$no;
				break;
			case 'captcha':
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


		// additional field classes		
		if ( $field_disabled )		$field_class .= ' disabled';
		if ( $field_readonly )		$field_class .= ' readonly';
		if ( $field_emailcheck )	$field_class .= ' fldemail';
		if ( $field_required ) 		$field_class .= ' fldrequired';

		
		$field_value = ''; 
		
		//pre-populating fields...
		if( !isset($_REQUEST['sendbutton'.$no]) && isset($_GET[$input_name]) )
			$field_value = $_REQUEST[$input_name];

		// an error ocurred:
		$liERR = $insertErr = '';

		if(! $all_valid){

			if ( $validations[$i]==1 )
				$field_class .= '';
			else{
				$field_class .= ' cf_error';
				
				//enhanced error display
				if(substr(get_option('cforms'.$no.'_showpos'),2,1)=="y")
					$liERR = ' class="cf_li_err"';
				if(substr(get_option('cforms'.$no.'_showpos'),3,1)=="y")
					$insertErr = ($fielderr<>'')?'<ul class="cf_li_text_err"><li>'.stripslashes($fielderr).'</li></ul>':'';
			}
			
			
			if ( $field_type == 'multiselectbox' || $field_type == 'checkboxgroup' ){
				$field_value = $_REQUEST[$input_name];  // in this case it's an array! will do the stripping later
			}
			else
				$field_value = str_replace('"','&quot;',stripslashes($_REQUEST[$input_name]));

		}


		//print label only for non "textonly" fields! Skip some others too, and handle them below indiv.
		if( ! in_array($field_type,array('hidden','textonly','fieldsetstart','fieldsetend','ccbox','checkbox','checkboxgroup','send2author','radiobuttons')) )
			$content .= $nttt . '<li'.$liID.$liERR.'>'.$insertErr.'<label' . $labelID . ' for="'.$input_id.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';

		
		if ( $field_value=='' && $defaultvalue<>'' ) // if not reloaded (due to err) then use default values
			$field_value=$defaultvalue;    

		// field disabled or readonly, greyed out?
		$disabled = $field_disabled?' disabled="disabled"':'';
		$readonly = $field_readonly?' readonly="readonly"':'';

		$dp = '';
		$naming = false;
		$field  = '';
		switch($field_type) {

			case "upload":
	  			$upload=true;  // set upload flag for ajax suppression!
				$field = '<input' . $readonly.$disabled . ' type="file" name="cf_uploadfile'.$no.'[]" id="cf_uploadfile'.$no.'-'.$i.'" class="cf_upload ' . $field_class . '"/>';
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
				$field = '<input type="text" name="'.$input_name.'" id="cforms_q'.$no.'" class="secinput ' . $field_class . '" value=""/>';
		    	$verification=true;
				break;

			case "captcha":			
				$_SESSION['turing_string_'.$no] = rc();
				
				$field = '<input type="text" name="'.$input_name.'" id="cforms_captcha'.$no.'" class="secinput' . $field_class . '" value=""/>'.
						 '<img id="cf_captcha_img'.$no.'" class="captcha" src="'.$cforms_root.'/cforms-captcha.php?ts='.$no.get_captcha_uri().'" alt=""/>'.
						 '<a title="'.__('reset captcha image', 'cforms').'" href="javascript:reset_captcha(\''.$no.'\')"><img class="captcha-reset" src="'.$cforms_root.'/images/spacer.gif" alt="Captcha"/></a>';
		    	$captcha=true;
				break;
			
			case "cauthor":
			case "url":
			case "email":			
			case "datepicker":
			case "yourname":
			case "youremail":
			case "friendsname":
			case "friendsemail":
			case "textfield":
			case "pwfield":
				$type = ($field_type=='pwfield')?'password':'text';
				$field_class = ($field_type=='datepicker')?$field_class.' cf_date':$field_class;
				
				if ($field_type=='datepicker') 
					$dp = '<a href="javascript: void(0);" onmouseover="window.status=\'Show Calendar\';" onmouseout="window.status=\'\';"  '.
					'onclick="dp.select(document.forms[\'cforms'.$no.'form\'].'.$input_id.',\'anchor'.$i.'\',\''.get_option('cforms_dp_date').'\'); return false;" id="anchor'.$i.'" name="anchor'.$i.'">'.
					'<img class="imgcalendar" src="'.$cforms_root.'/js/calendar.gif" alt=""/></a>';

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';
					
				$field = '<input' . $readonly.$disabled . ' type="'.$type.'" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '" value="' . $field_value  . '"'.$onfocus.'/>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"/>';

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
				
				$field .= $nttt . '<li class="cf_hidden"><input type="hidden" class="cfhidden" name="'.$input_name.'" id="'.$input_id.'" value="' . $field_value  . '"/></li>';
				break;

			case "comment":
			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="comment" id="comment" class="' . $field_class . '"'. $onfocus.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="comment" id="comment_regexp" value="'.$reg_exp.'"/>';
				break;

			case "textarea":

			    $onfocus = $field_clear?' onfocus="clearField(this)" onblur="setField(this)"' : '';

				$field = '<textarea' . $readonly.$disabled . ' cols="30" rows="8" name="'.$input_name.'" id="'.$input_id.'" class="' . $field_class . '"'. $onfocus.'>' . $field_value  . '</textarea>';
				  if ( $reg_exp<>'' )
	           		 $field .= '<input type="hidden" name="'.$input_name.'_regexp" id="'.$input_id.'_regexp" value="'.$reg_exp.'"/>';
				break;

	   		case "ccbox":
			case "checkbox":
				$err='';
				if( !$all_valid && $validations[$i]<>1 )
					$err = ' cf_errortxt';

				if ( $options[1]<>'' ) {
				 		$before = '<li'.$liID.$liERR.'>'.$insertErr;
						$after  = '<label'. $labelID . ' for="'.$input_id.'" class="cf-after'.$err.'"><span>' . ($options[1]) . '</span></label></li>';
				 		$ba = 'a';
				}
				else {					
						$before = '<li'.$liID.$liERR.'>'.$insertErr.'<label' . $labelID . ' for="'.$input_name.'" class="cf-before'. $err .'"><span>' . ($field_name) . '</span></label>';
				 		$after  = '</li>';
				 		$ba = 'b';
				}
				$field = $nttt . $before . '<input' . $readonly.$disabled . ' type="checkbox" name="'.$input_name.'" id="'.$input_id.'" class="cf-box-' . $ba . $field_class . '"'.($field_value?' checked="checked"':'').'/>' . $after;

				break;


			case "checkboxgroup":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';
				array_shift($options);
				$field .= $nttt . '<li'.$liID.' class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nttt . '<li'.$liIDb.' class="cf-box-group">';
				$id=1; $j=0;
				foreach( $options as $option  ) {

						//supporting names & values
					    $opt = explode('|', $option,2);

						if ( $opt[1]=='' ) $opt[1] = $opt[0];

	                    $checked = '';
	                    if ( $opt[1]==stripslashes(($field_value[$j])) )  {
	                        $checked = 'checked="checked"';
	                        $j++;
	                    }
						
						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';

						if ( $opt[0]=='' )
							$field .= $nttt . $tab . '<br />';
						else
							$field .= $nttt . $tab . '<input' . $readonly.$disabled . ' type="checkbox" id="'. $input_id . $id . '" name="'. $input_name . '[]" value="'.$opt[1].'" '.$checked.' class="cf-box-b"/>'.
									  '<label' . $labelIDx . ' for="'. $input_id . ($id++) . '" class="cf-group-after"><span>'.$opt[0] . "</span></label>";
											
					}
				$field .= $nttt . '</li>';
				break;
				
				
			case "multiselectbox":
				//$field .= $nttt . '<li><label ' . $labelID . ' for="'.$input_name.'"'. $labelclass . '><span>' . stripslashes(($field_name)) . '</span></label>';
				$field .= '<select' . $readonly.$disabled . ' multiple="multiple" name="'.$input_name.'[]" id="'.$input_id.'" class="cfselectmulti ' . $field_class . '">';
				array_shift($options);
				$second = false;
				$j=0;
				foreach( $options as $option  ) {
                    //supporting names & values
                    $opt = explode('|', $option,2);
                    if ( $opt[1]=='' ) $opt[1] = $opt[0];
                    
                    $checked = '';
                    if ( $opt[1]==stripslashes(htmlspecialchars($field_value[$j])) )  {
                        $checked = ' selected="selected"';
                        $j++;
                    }
                        
                    $field.= $nttt . $tab . '<option value="'. str_replace('"','&quot;',$opt[1]) .'"'.$checked.'>'.$opt[0].'</option>';
                    $second = true;
                    
				}
				$field.= $nttt . '</select>';
				break;

			case "emailtobox":
			case "selectbox":
				$field = '<select' . $readonly.$disabled . ' name="'.$input_name.'" id="'.$input_id.'" class="cformselect' . $field_class . '">';
				array_shift($options); $jj=$j=0;
				$second = false;
				foreach( $options as $option  ) {

					//supporting names & values
				    $opt = explode('|', $option,2);		if ( $opt[1]=='' ) $opt[1] = $opt[0];

						//email-to-box valid entry?
				    if ( $field_type == 'emailtobox' && $opt[1]<>'-' )
								$jj = $j++; else $jj = '-';

				    $checked = '';

					if( $field_value == '' || $field_value == '-') {
							if ( !$second )
							    $checked = ' selected="selected"';
					}	else
							if ( $opt[1]==$field_value || $jj==$field_value ) 
								$checked = ' selected="selected"';
					    
					$field.= $nttt . $tab . '<option value="'.(($field_type=='emailtobox')?$jj:$opt[1]).'"'.$checked.'>'.$opt[0].'</option>';
					$second = true;
					
				}
				$field.= $nttt . '</select>';
				break;

			case "send2author":				
			case "radiobuttons":
				$liID_b = ($liID <>'')?substr($liID,0,-1) . 'items"':'';	//only if label ID's active

				array_shift($options);
				$field .= $nttt . '<li'.$liID.' class="cf-box-title">' . (($field_name)) . '</li>' . 
						  $nttt . '<li'.$liID_b.' class="cf-box-group">';
				$second = false; $id=1;
				foreach( $options as $option  ) {
				    $checked = '';

						//supporting names & values
				    $opt = explode('|', $option,2);
						if ( $opt[1]=='' ) $opt[1] = $opt[0];

						if( $field_value == '' ) {
								if ( !$second )
								    $checked = ' checked="checked"';
						}	else
								if ( $opt[1]==$field_value ) $checked = ' checked="checked"';
						
						if ( $labelID<>'' ) $labelIDx = substr($labelID,0,-1) . $id . '"';
						
						$field .= $nttt . $tab .
								  '<input' . $readonly.$disabled . ' type="radio" id="'. $input_id . $id . '" name="'.$input_name.'" value="'.$opt[1].'"'.$checked.' class="cf-box-b'.(($second)?' cformradioplus':'').'"/>'.
								  '<label' . $labelIDx . ' for="'. $input_id . ($id++) . '" class="cf-after"><span>'.$opt[0] . "</span></label>";
											
						$second = true;
					}
				$field .= $nttt  . '</li>';
				break;
				
		}
		
		// add new field
		$content .= $field;

		// adding "required" text if needed
		if($field_emailcheck == 1)
			$content .= '<span class="emailreqtxt">'.stripslashes(get_option('cforms'.$no.'_emailrequired')).'</span>';
		else if($field_required == 1 && $field_type <> 'checkbox')
			$content .= '<span class="reqtxt">'.stripslashes(get_option('cforms'.$no.'_required')).'</span>';

		//close out li item
		if ( ! in_array($field_type,array('hidden','fieldsetstart','fieldsetend','radiobuttons','checkbox','checkboxgroup','ccbox','textonly','send2author')) )
			$content .= '</li>';
		
	} //all fields


	if ( $ol )
		$content .= $ntt . '</ol>';
	if ( $fieldsetopen )
		$content .= $ntt . '</fieldset>';


	// rest of the form
	if ( get_option('cforms'.$no.'_ajax')=='1' && !$upload && !$custom && !$alt_action )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', false)"';
	else if ( ($upload || $custom || $alt_action) && get_option('cforms'.$no.'_ajax')=='1' )
		$ajaxenabled = ' onclick="return cforms_validate(\''.$no.'\', true)"';
	else
		$ajaxenabled = '';


	// just to appease "strict"
	$content .= $ntt . '<fieldset class="cf_hidden">'.$nttt.'<legend>&nbsp;</legend>';

	// if visitor verification turned on:
	if ( $verification )
		$content .= $nttt .'<input type="hidden" name="cforms_a'.$no.'" id="cforms_a'.$no.'" value="' . md5(rawurlencode(strtolower($q[1]))) . '"/>';

	if ( $captcha )
		$content .= $nttt .'<input type="hidden" name="cforms_cap'.$no.'" id="cforms_cap'.$no.'" value="' . md5($_SESSION['turing_string_'.$no]) . '"/>';

	$custom_error=substr(get_option('cforms'.$no.'_showpos'),2,1).substr(get_option('cforms'.$no.'_showpos'),3,1).substr(get_option('cforms'.$no.'_showpos'),4,1).$custom_error;

	if ( get_option('cforms'.$no.'_tellafriend')>0 ){
		if ( get_option('cforms'.$no.'_tellafriend')==2 ) 
			$nono = ''; else $nono = $no;
			
		$content .= $nttt . '<input type="hidden" name="comment_post_ID'.$nono.'" id="comment_post_ID'.$nono.'" value="' . ( isset($_GET['pid'])? $_GET['pid'] : get_the_ID() ) . '"/>' . 
					$nttt . '<input type="hidden" name="cforms_pl'.$no.'" id="cforms_pl'.$no.'" value="' . ( isset($_GET['pid'])? get_permalink($_GET['pid']) : get_permalink() ) . '"/>';
	}

	$content .= $nttt . '<input type="hidden" name="cf_working'.$no.'" id="cf_working'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_working')).'"/>'.
				$nttt . '<input type="hidden" name="cf_failure'.$no.'" id="cf_failure'.$no.'" value="'.rawurlencode(get_option('cforms'.$no.'_failure')).'"/>'.
				$nttt . '<input type="hidden" name="cf_codeerr'.$no.'" id="cf_codeerr'.$no.'" value="'.rawurlencode(get_option('cforms_codeerr')).'"/>'.
				$nttt . '<input type="hidden" name="cf_customerr'.$no.'" id="cf_customerr'.$no.'" value="'.rawurlencode($custom_error).'"/>'.
				$nttt . '<input type="hidden" name="cf_popup'.$no.'" id="cf_popup'.$no.'" value="'.get_option('cforms'.$no.'_popup').'"/>';

	$content .= $ntt . '</fieldset>';


	$content .= $ntt . '<p class="cf-sb"><input type="submit" name="sendbutton'.$no.'" id="sendbutton'.$no.'" class="sendbutton" value="' . get_option('cforms'.$no.'_submit_text') . '"'.
				$ajaxenabled.'/></p>';
		
	$content .= $ntt . '</form>';

	if ( get_option('cforms_datepicker')=='1' && !$dpflag ){
		$content .= $ntt . '<div id="datepicker" style="position:absolute;visibility:hidden;z-index:999;"></div><script type="text/javascript">if ( !dp ) dpinit();</script>';
		$dpflag   = true;		
	}
	
	//link love? you bet ;)
		$content .= $ntt . '<p class="linklove" id="ll'. $no .'"><a href="http://www.deliciousdays.com/cforms-plugin"><em>cforms</em> contact form by delicious:days</a></p>';
	

	//either show message above or below
	if( substr(get_option('cforms'.$no.'_showpos'),1,1)=='y' && !($success&&get_option('cforms'.$no.'_redirect')==2))
		$content .= $tt . '<div id="usermessage'.$no.'b" class="cf_info ' . $usermessage_class . '" >' . $usermessage_text . '</div>' . $nl;

	return $content;
}


// prep captcha get call
function get_captcha_uri() 
{
	$captcha = get_option('cforms_captcha_def'); 
	$h = ( $captcha['h']<>'' ) ? stripslashes(htmlspecialchars( $captcha['h'] )) : 25;
	$w = ( $captcha['w']<>'' ) ? stripslashes(htmlspecialchars( $captcha['w'] )) : 115;
	$c = ( $captcha['c']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c'] )) : '000066';
	$l = ( $captcha['l']<>'' ) ? stripslashes(htmlspecialchars( $captcha['l'] )) : '000066';
	$f = ( $captcha['f']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f'] )) : 'font4.ttf';
	$a1 = ( $captcha['a1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['a1'] )) : -12;
	$a2 = ( $captcha['a2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['a2'] )) : 12;
	$f1 = ( $captcha['f1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f1'] )) : 17;
	$f2 = ( $captcha['f2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f2'] )) : 19;
	$bg = ( $captcha['bg']<>'' ) ? stripslashes(htmlspecialchars( $captcha['bg'] )) : '1.gif';
		
	return "&w={$w}&h={$h}&c={$c}&l={$l}&f={$f}&a1={$a1}&a2={$a2}&f1={$f1}&f2={$f2}&b={$bg}";
}


// captcha random code
function rc() 
{
	$captcha = get_option('cforms_captcha_def'); 
	$min = ( $captcha['c1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c1'] )) : 4;
	$max = ( $captcha['c2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c2'] )) : 5;
	$src = ( $captcha['ac']<>'' ) ? stripslashes(htmlspecialchars( $captcha['ac'] )) : 'abcdefghijkmnpqrstuvwxyz23456789';

	$srclen = strlen($src)-1;	
	$length = mt_rand($min,$max);
	$Code = '';
	
	for($i=0; $i<$length; $i++) 
		$Code .= substr($src, mt_rand(0, $srclen), 1);
	
	return $Code;
}


// some css for positioning the form elements
function cforms_style() {
	global $wp_query, $cforms_root, $localversion;

	// add content actions and filters
	$page_obj = $wp_query->get_queried_object();
	
	$onPages  = str_replace(' ','',stripslashes(htmlspecialchars( get_option('cforms_include') )));	
	$onPagesA = explode(',', $onPages);

	if( $onPages=='' || in_array($page_obj->ID,$onPagesA) ){

		echo "\n<!-- Start Of Script Generated By cforms v".$localversion." [Oliver Seidel | www.deliciousdays.com] -->\n";
		if( get_option('cforms_no_css')<>'1' )
			echo '<link rel="stylesheet" type="text/css" href="' . $cforms_root . '/styling/' . get_option('cforms_css') . '" />'."\n";
		echo '<script type="text/javascript" src="' . $cforms_root. '/js/cforms.js"></script>'."\n";
		if( get_option('cforms_datepicker')=='1' ){
			echo '<script type="text/javascript" src="' . $cforms_root. '/js/simplecalendar.js"></script>'."\n";
			echo '<script type="text/javascript">'."\n".
				 'var dp;'."\n". 
				 'function dpinit(){'."\n". 
				 "\t".'dp = new CalendarPopup(\'datepicker\');'."\n".
				 "\t".'dp.setTodayText(\''.stripslashes(get_option('cforms_dp_today')).'\');'."\n".
				 "\t".'dp.showYearNavigation();'."\n".
				 "\t".'dp.setMonthNames('.stripslashes(get_option('cforms_dp_months')).');'."\n".
				 "\t".'dp.setDayHeaders('.stripslashes(get_option('cforms_dp_days')).');'."\n".
				 "\t".'dp.setWeekStartDay('.stripslashes(get_option('cforms_dp_start')).');}'."\n".
				 '</script>'."\n";
		}
		echo '<!-- End Of Script Generated By cforms -->'."\n\n";
		
	}
}


// replace placeholder by generated code
function cforms_insert( $content ) {
	global $post;

	if ( strpos($content,'<!--cforms')!==false ) {  //only if form tag is present!

		$forms = get_option('cforms_formcount');
	
		for ($i=1;$i<=$forms;$i++) {
			if($i==1) {

		  		if(preg_match('#<!--cforms1?-->#', $content) && check_for_taf('',$post->ID) )
		   			$content = preg_replace('/(<p>)?<!--cforms1?-->(<\/p>)?/', cforms('').'$1$2', $content);

			} else {

		 		if(preg_match('#<!--cforms'.$i.'-->#', $content) && check_for_taf($i,$post->ID) )
					$content = preg_replace('/(<p>)?<!--cforms'.$i.'-->(<\/p>)?/', cforms('',$i).'$1$2', $content);

			}
  			$content = str_replace('<p></p>','',$content);
		}
		
	}
	
	return $content;
}


//build field_stat string from array (for custom forms)
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

// inserts a cform anywhere you want
function insert_cform($no='') {	
	global $post;

	if ( isset($_GET['pid']) )
		$pid = $_GET['pid'];
	else if ($post->ID == 0)
		$pid = false;
	else
		$pid = $post->ID;
		
	if ( !$pid )
		cforms('',$no);
	else
		echo check_for_taf($no,$pid)?cforms('',$no):''; 
}

// inserts a custom cform anywhere you want
function insert_custom_cform($fields='',$no='') { 
	global $post;
	
	if ( isset($_GET['pid']) )
		$pid = $_GET['pid'];
	else if ($post->ID == 0)
		$pid = false;
	else
		$pid = $post->ID;
		
	if ( !$pid )
		cforms($fields,$no.'+');
	else
		echo check_for_taf($no,$pid)?cforms($fields,$no.'+'):''; 
}

// check if t-f-a is set
function check_for_taf($no,$pid) {

	$tmp = get_post_custom($pid);
	$taf = $tmp["tell-a-friend"][0];

	if ( substr(get_option('cforms'.$no.'_tellafriend'),0,1)<>'1')
		return true;
	else {
		if ( $taf=='1' )
			return true;
		else
			return false;
	}

}

// check if post is t-f-a enabled
function is_tellafriend($pid) {
	$tmp = get_post_custom($pid);
	return ($tmp["tell-a-friend"][0]=='1')?true:false;
}



### Add Tell A Friend checkbox to admin
add_action('dbx_post_sidebar', 'taf_admin');
add_action('dbx_page_sidebar', 'taf_admin');
function taf_admin() {
	global $wpdb;
	$edit_post = intval($_GET['post']);

	for ( $i=1;$i<=get_option('cforms_formcount');$i++ ) {
		$tafenabled = ( substr(get_option('cforms'.(($i=='1')?'':$i).'_tellafriend'),0,1)=='1') ? true : false;
		if ( $tafenabled ) break;
	}
	$i = ($i==1)?'':$i;
	
	if ( $tafenabled && function_exists('get_post_custom') ){

		$tmp = get_post_custom($edit_post);
		$taf = $tmp["tell-a-friend"][0];
		
		$chk = ($taf=='1' || ($edit_post=='' && substr(get_option('cforms'.$i.'_tellafriend'),1,1)=='1') )?'checked="checked"':'';
				
		?>
		<fieldset id="poststickystatusdiv" class="dbx-box">
			<h3 class="dbx-handle"><?php _e('cforms Tell-A-Friend', 'cforms'); ?></h3> 
			<div class="dbx-content">
				<label for="tellafriend" class="selectit"><input type="checkbox" id="tellafriend" name="tellafriend" value="1"<?php echo $chk; ?>/>&nbsp;<?php _e('Show Form', 'cforms'); ?></label>
			</div>
		</fieldset>
		<?php
	}
}

### Add Tell A Friend processing
add_action('save_post', 'enable_tellafriend');
function enable_tellafriend($post_ID) {
	global $wpdb;
	
	$tellafriend_status = isset($_POST['tellafriend']);

	if($tellafriend_status && intval($post_ID) > 0)
		add_post_meta($post_ID, 'tell-a-friend', '1', true);
	else
		delete_post_meta($post_ID, 'tell-a-friend');
}



function widget_cforms_init() {

	global $cforms_root;

	if (! function_exists("register_sidebar_widget")) {
		return;
	}
	
	function widget_cforms($args) {
		extract($args);
		
		echo $before_widget;
		
		preg_match('/^.*widgetcform([^"]*)".*$/',$before_widget,$form_no);

		$no = ($form_no[1]=='0')?'':(int)($form_no[1]+1);
		echo $before_widget . "<!--$no-->" . insert_cform($no) . $after_widget;
	}

	for ( $i=0;$i<get_option('cforms_formcount');$i++ ) {
	
		$no = ($i==0)?'':($i+1);
		$form = substr(get_option('cforms'.$no.'_fname'),0,10).'...';
		$url = 'url('.$cforms_root.'/images/cfii.gif) no-repeat right 1px;';
		
		$nodisp = ($i==0)?'default':'#'.($i+1);
		register_sidebar_widget('<span style="line-height:1.2em;padding:5px;display:block;height:31px;width:94%;position:relative; top:-1px; left:0;background:'.$url.'">cforms II ('.$nodisp.')<br />&raquo;'.$form.'</span>', 'widget_cforms','widgetcform'.$i);
	}

}

// get # of submission left (max subs)
function get_cforms_submission_left($no='') {  
	global $wpdb;

	if ( $no==0 || $no==1 ) $no='';
	$max   = (int)get_option('cforms'.$no.'_maxentries');
	if( $max == '' || get_option('cforms_database')=='0' )
		return -1;
		
	$entries = $wpdb->get_row("SELECT count(id) as submitted FROM {$wpdb->cformssubmissions} WHERE form_id='{$no}'");

	if( $max-$entries->submitted > 0)
		return ($max-$entries->submitted);
	else
		return 0;
}

### add actions
if (function_exists('add_action')){

	global $plugindir;
	
	add_action('plugins_loaded', 'widget_cforms_init');

	### dashboard
	if ( $_SERVER['SCRIPT_FILENAME'] <> '' )
		$loc = $_SERVER['SCRIPT_FILENAME'];
	else if ( $_SERVER['SCRIPT_URI'] <> '' )
		$loc = $_SERVER['SCRIPT_URI'];
	else	
		$loc = "/wp-admin/index.php";
		
	$admin = dirname($loc);	
	$admin = ( strpos($admin,'wp-admin')!==false )?true:false;

	if ( get_option('cforms_showdashboard')=='1' && get_option('cforms_database')=='1' ) {	
		require_once(dirname(__FILE__) . '/lib_dashboard.php');
		add_action( 'activity_box_end', 'cforms_dashboard', 1 );
	}

	// Set 'manage_database' Capabilities To Administrator
	if ( $admin ) {
		require_once(dirname(__FILE__) . '/lib_functions.php');
		add_action('activate_'.$plugindir.'/cforms.php', 'cforms_init');
		add_action('admin_head', 'cforms_options_page_style');
		add_action('admin_menu', 'cforms_menu');
		add_action('init', 'download_cforms');
	}

}

add_filter('wp_head', 'cforms_style'); 
add_filter('the_content', 'cforms_insert',10);

?>
