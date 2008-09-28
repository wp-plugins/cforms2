<?php

	###sorry, but WP2.2 doesn quickly enough flush the cache!
	###if ( function_exists (wp_cache_close) ){
	###	wp_cache_flush();
	###	wp_cache_close();
	###}

	$noDISP = '1'; $no='';
	if( $_REQUEST['no']<>'1' )
		$noDISP = $no = $_REQUEST['no'];

	for ( $i=(int)$noDISP; $i<$FORMCOUNT; $i++) {  // move all forms "to the left"

		for ( $j=1; $j<=$cformsSettings['form'.$i]['cforms'.$i.'_count_fields']; $j++)  //delete all extra fields!
		  unset( $cformsSettings['form'.$i]['cforms'.$i.'_count_field_'.$j] );

		for ( $j=1; $j<=$cformsSettings['form'.$no]['cforms'.($i+1).'_count_fields']; $j++)
		  $cformsSettings['form'.$i]['cforms'.$i.'_count_field_'.$j] = $cformsSettings['form'.($i+1)]['cforms'.($i+1).'_count_field_'.$j];

		$cformsSettings['form'.$i]['cforms'.$i.'_count_fields']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_count_fields'];
		$cformsSettings['form'.$i]['cforms'.$i.'_required']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_required'];
		$cformsSettings['form'.$i]['cforms'.$i.'_emailrequired']= $cformsSettings['form'.$no]['cforms'.($i+1).'_emailrequired'];

		$cformsSettings['form'.$i]['cforms'.$i.'_confirm'] 		= $cformsSettings['form'.$no]['cforms'.($i+1).'_confirm'];
		$cformsSettings['form'.$i]['cforms'.$i.'_ajax'] 		= $cformsSettings['form'.$no]['cforms'.($i+1).'_ajax'];
		$cformsSettings['form'.$i]['cforms'.$i.'_fname']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_fname'];
		$cformsSettings['form'.$i]['cforms'.$i.'_csubject']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_csubject'];
		$cformsSettings['form'.$i]['cforms'.$i.'_cmsg']			= $cformsSettings['form'.$no]['cforms'.($i+1).'_cmsg'];
		$cformsSettings['form'.$i]['cforms'.$i.'_cmsg_html'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_cmsg_html'];
		$cformsSettings['form'.$i]['cforms'.$i.'_email'] 		= $cformsSettings['form'.$no]['cforms'.($i+1).'_email'];
		$cformsSettings['form'.$i]['cforms'.$i.'_fromemail'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_fromemail'];
		$cformsSettings['form'.$i]['cforms'.$i.'_bcc']	   		= $cformsSettings['form'.$no]['cforms'.($i+1).'_bcc'];
		$cformsSettings['form'.$i]['cforms'.$i.'_header']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_header'];
		$cformsSettings['form'.$i]['cforms'.$i.'_header_html']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_header_html'];
		$cformsSettings['form'.$i]['cforms'.$i.'_formdata']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_formdata'];
		$cformsSettings['form'.$i]['cforms'.$i.'_space']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_space'];
		$cformsSettings['form'.$i]['cforms'.$i.'_noattachments']= $cformsSettings['form'.$no]['cforms'.($i+1).'_noattachments'];

		$cformsSettings['form'.$i]['cforms'.$i.'_upload_dir'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_upload_dir'];
		$cformsSettings['form'.$i]['cforms'.$i.'_upload_ext'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_upload_ext'];
		$cformsSettings['form'.$i]['cforms'.$i.'_upload_size'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_upload_size'];

		$cformsSettings['form'.$i]['cforms'.$i.'_subject']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_subject'];
		$cformsSettings['form'.$i]['cforms'.$i.'_submit_text']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_submit_text'];
		$cformsSettings['form'.$i]['cforms'.$i.'_success'] 		= $cformsSettings['form'.$no]['cforms'.($i+1).'_success'];
		$cformsSettings['form'.$i]['cforms'.$i.'_failure']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_failure'];
		$cformsSettings['form'.$i]['cforms'.$i.'_limittxt']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_limittxt'];
		$cformsSettings['form'.$i]['cforms'.$i.'_working']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_working'];
		$cformsSettings['form'.$i]['cforms'.$i.'_popup']	  	= $cformsSettings['form'.$no]['cforms'.($i+1).'_popup'];
		$cformsSettings['form'.$i]['cforms'.$i.'_showpos']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_showpos'];

		$cformsSettings['form'.$i]['cforms'.$i.'_redirect']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_redirect'];
		$cformsSettings['form'.$i]['cforms'.$i.'_redirect_page']= $cformsSettings['form'.$no]['cforms'.($i+1).'_redirect_page'];
		$cformsSettings['form'.$i]['cforms'.$i.'_action']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_action'];
		$cformsSettings['form'.$i]['cforms'.$i.'_action_page']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_action_page'];

		$cformsSettings['form'.$i]['cforms'.$i.'_tracking']		= $cformsSettings['form'.$no]['cforms'.($i+1).'_tracking'];

		$cformsSettings['form'.$i]['cforms'.$i.'_tellafriend']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_tellafriend'];
		$cformsSettings['form'.$i]['cforms'.$i.'_dashboard'] 	= $cformsSettings['form'.$no]['cforms'.($i+1).'_dashboard'];
		$cformsSettings['form'.$i]['cforms'.$i.'_maxentries']	= $cformsSettings['form'.$no]['cforms'.($i+1).'_maxentries'];

		###sorry, but WP2.2 doesn quickly enough flush the cache!
		###if ( function_exists (wp_cache_init) ){
		###	wp_cache_init();
		###	wp_cache_flush();
		###}
	}

    unset( $cformsSettings['form'.$FORMCOUNT] );

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

	$cformsSettings['global']['cforms_formcount'] = (string)($FORMCOUNT);

	update_option('cforms_settings',$cformsSettings);

	echo '<div id="message" class="updated fade"><p>'. __('Form deleted', 'cforms').'.</p></div>';
?>