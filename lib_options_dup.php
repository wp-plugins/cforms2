<?php

	$noDISP='1'; $no='';
	if( isset($_REQUEST['no']) ) {
		if( $_REQUEST['no']<>'1' )
			$noDISP = $no = $_REQUEST['no'];
	}

	$FORMCOUNT=$FORMCOUNT+1;

	###sorry, but WP2.2 doesn quickly enough flush the cache!
	###if ( function_exists (wp_cache_close) ) {
	###	wp_cache_flush();
	###	wp_cache_close();
	###}
	$cformsSettings['global']['cforms_formcount'] =(string)($FORMCOUNT);

	### new settings container
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_rsskey'] = $cformsSettings['form'.$no]['cforms'.$no.'_rsskey'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_rss'] = $cformsSettings['form'.$no]['cforms'.$no.'_rss'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_rss_count'] = $cformsSettings['form'.$no]['cforms'.$no.'_rss_count'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_dontclear'] = $cformsSettings['form'.$no]['cforms'.$no.'_dontclear'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_count_fields'] = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];

	for ( $j=1; $j<= $cformsSettings['form'.$no]['cforms'.$no.'_count_fields']; $j++)  //delete all extra fields!
		  $cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_count_field_'.$j] = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_'.$j];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_required'] = $cformsSettings['form'.$no]['cforms'.$no.'_required'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_emailrequired'] = $cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_ajax'] = $cformsSettings['form'.$no]['cforms'.$no.'_ajax'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_confirm'] = $cformsSettings['form'.$no]['cforms'.$no.'_confirm'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_fname'] = __("Duplicate of form #$noDISP",'cforms');
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_csubject'] = $cformsSettings['form'.$no]['cforms'.$no.'_csubject'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_cmsg'] = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_cmsg_html'] = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_email'] = $cformsSettings['form'.$no]['cforms'.$no.'_email'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_bcc'] = $cformsSettings['form'.$no]['cforms'.$no.'_bcc'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_header'] = $cformsSettings['form'.$no]['cforms'.$no.'_header'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_header_html'] = $cformsSettings['form'.$no]['cforms'.$no.'_header_html'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_formdata'] = $cformsSettings['form'.$no]['cforms'.$no.'_formdata'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_space'] = $cformsSettings['form'.$no]['cforms'.$no.'_space'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_noattachments'] = $cformsSettings['form'.$no]['cforms'.$no.'_noattachments'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_subject'] = $cformsSettings['form'.$no]['cforms'.$no.'_subject'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_submit_text'] = $cformsSettings['form'.$no]['cforms'.$no.'_submit_text'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_success'] = $cformsSettings['form'.$no]['cforms'.$no.'_success'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_failure'] = $cformsSettings['form'.$no]['cforms'.$no.'_failure'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_limittxt'] = $cformsSettings['form'.$no]['cforms'.$no.'_limittxt'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_working'] = $cformsSettings['form'.$no]['cforms'.$no.'_working'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_popup'] = $cformsSettings['form'.$no]['cforms'.$no.'_popup'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_showpos'] = $cformsSettings['form'.$no]['cforms'.$no.'_showpos'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_redirect'] = $cformsSettings['form'.$no]['cforms'.$no.'_redirect'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_redirect_page'] = $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_action'] = $cformsSettings['form'.$no]['cforms'.$no.'_action'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_action_page'] = $cformsSettings['form'.$no]['cforms'.$no.'_action_page'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_upload_dir'] = $cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_upload_ext'] = $cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_upload_size'] = $cformsSettings['form'.$no]['cforms'.$no.'_upload_size'];

	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_tracking'] = $cformsSettings['form'.$no]['cforms'.$no.'_tracking'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_tellafriend'] = $cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_dashboard'] = $cformsSettings['form'.$no]['cforms'.$no.'_dashboard'];
	$cformsSettings['form'.$FORMCOUNT]['cforms'.$FORMCOUNT.'_maxentries'] = $cformsSettings['form'.$no]['cforms'.$no.'_maxentries'];

	echo '<div id="message" class="updated fade"><p>'.__('The form has been duplicated, you\'re now working on the copy.', 'cforms').'</p></div>';

	update_option('cforms_settings',$cformsSettings);

	###sorry, but WP2.2 doesn quickly enough flush the cache!
	###if ( function_exists (wp_cache_init) ){
	###	wp_cache_init();
	###	wp_cache_flush();
	###}

	//set $no afterwards: need it to duplicate fields
	$no = $noDISP = $FORMCOUNT;

?>