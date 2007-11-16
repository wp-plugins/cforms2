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


// SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
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


if( isset($_REQUEST['deleteall']) ) {  // erase all cforms data

	$alloptions =  $wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name LIKE 'cforms%'");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	?>
	<div id="message" class="updated fade"><p><strong><?php _e('All cforms related data has been deleted.', 'cforms') ?></strong></p></div>

	<div class="wrap">
	<h2><?php _e('Thank you for using cforms.', 'cforms') ?></h2>
	<p><?php _e('You can go straight to your <strong>Plugins</strong> tab and disable the plugin now!', 'cforms') ?></p>
	</div>
	<?php
	
	die;


} else if ( isset($_REQUEST['deletetables']) ) {

	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	update_option('cforms_database', '0');

	?>
	<div id="message" class="updated fade">
		<p>
		<strong>
			<?php echo sprintf (__('cforms tracking tables %s have been deleted.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?>
			<br />
			<?php _e('Please backup/clean-up your upload directory, chances are that when you turn tracking back on, existing (older) attachments may be <u>overwritten</u>!') ?>
			<br />
			<?php _e('<small>(only of course, if your form includes a file upload field)</small>') ?>
		</strong>
		</p>
	</div>
	<?php

}



// Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || 
	isset($_REQUEST['Submit4']) || isset($_REQUEST['Submit5']) || isset($_REQUEST['Submit6']) ) {

//	update_option('cforms_linklove', $_REQUEST['cforms_linklove']?'1':'0');
	update_option('cforms_show_quicktag', $_REQUEST['cforms_show_quicktag']?'1':'0');
	update_option('cforms_sec_qa', $_REQUEST['cforms_sec_qa'] );
	update_option('cforms_codeerr', $_REQUEST['cforms_codeerr']);
	update_option('cforms_database', $_REQUEST['cforms_database']?'1':'0');
	update_option('cforms_showdashboard', $_REQUEST['cforms_showdashboard']?'1':'0');
	update_option('cforms_datepicker', $_REQUEST['cforms_datepicker']?'1':'0');
	update_option('cforms_dp_date', $_REQUEST['cforms_dp_date']);
	update_option('cforms_dp_days', $_REQUEST['cforms_dp_days']);
	update_option('cforms_dp_months', $_REQUEST['cforms_dp_months']);
	update_option('cforms_dp_today', $_REQUEST['cforms_dp_today']);

	$smtpsettings[0]=$_REQUEST['cforms_smtp_onoff']?'1':'0';
	$smtpsettings[1]=$_REQUEST['cforms_smtp_host'];
	$smtpsettings[2]=$_REQUEST['cforms_smtp_user'];
	if ( !preg_match('/^\*+$/',$_REQUEST['cforms_smtp_pass']) ) {
		$smtpsettings[3]=$_REQUEST['cforms_smtp_pass'];
		}
	update_option('cforms_smtp', implode('$#$',$smtpsettings) );

	update_option('cforms_upload_err1', $_REQUEST['cforms_upload_err1']);
	update_option('cforms_upload_err2', $_REQUEST['cforms_upload_err2']);
	update_option('cforms_upload_err3', $_REQUEST['cforms_upload_err3']);
	update_option('cforms_upload_err4', $_REQUEST['cforms_upload_err4']);
	update_option('cforms_upload_err5', $_REQUEST['cforms_upload_err5']);

	// Setup database tables ?
	if ( isset($_REQUEST['cforms_database']) && $_REQUEST['cforms_database_new']=='true' ) {
	
		if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ){
			
			$sql = "CREATE TABLE " . $wpdb->cformssubmissions . " (
					  id int(11) unsigned auto_increment,
					  form_id varchar(3) default '',
					  sub_date timestamp,
					  email varchar(40) default '', 
					  ip varchar(15) default '', 
					  PRIMARY KEY  (id) );";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			
			$sql = "CREATE TABLE " . $wpdb->cformsdata . " (
					  f_id int(11) unsigned auto_increment primary key, 
					  sub_id int(11) unsigned NOT NULL, 
					  field_name varchar(100) NOT NULL default '', 
					  field_val text);";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);

			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('cforms tracking tables %s have been created.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
			</div>
			<?php
			
		} else {

			$sets = $wpdb->get_var("SELECT count(id) FROM $wpdb->cformssubmissions");
			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('Found existing cforms tracking tables with %s records!', 'cforms'),$sets) ?></strong></p>
			</div>
			<?php	
		}
	}
	
}


?>

<div class="wrap" id="top">
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p2-title.jpg" alt=""/>

	<p><?php _e('All settings and configuration options on this page apply to all forms.', 'cforms') ?></p>

	<form id="cformsdata" name="mainform" method="post" action="">
	 <input type="hidden" name="cforms_database_new" value="<?php if(get_option('cforms_database')=="0") echo 'true'; ?>"/>

		<fieldset id="popupdate" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit6" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#popupdate';"/><a id="b9" class="blindminus" onfocus="this.blur()" onclick="toggleui(9);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Popup Date Picker', 'cforms') ?></p>

			<div id="o9">
				<p><?php echo sprintf(__('If you\'d like to offer a Javascript based date picker for more convenient date entry, enable this feature here. This will add a <strong>new input field</strong> for you to add to your form. See <a href="%s" %s>Help!</a> for more info and <strong>date formats</strong>.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#datepicker','onclick="setshow(19)"') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_datepicker" name="cforms_datepicker" <?php if(get_option('cforms_datepicker')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_datepicker"><strong><?php _e('Enable Javascript date picker', 'cforms') ?></strong></label></div>
				</div>
	
				<?php if ( get_option('cforms_datepicker')=='1' ) : ?>
					<p class="ex"><?php _e('Note that turning on this feature will result in loading an additional Javascript file to support the date picker.', 'cforms') ?></p>
				<?php endif; ?>
				
	
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_date"><strong><?php _e('Date Format', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_date" name="cforms_dp_date" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_date') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_days"><strong><?php _e('Days (Columns)', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_days" name="cforms_dp_days" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_days') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_months"><strong><?php _e('Months', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_months" name="cforms_dp_months" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_months') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_today"><strong><?php _e('Today', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_today" name="cforms_dp_today" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_today') )); ?>"/></div>
				</div>
			
			</div>
		</fieldset>
		
		
		<fieldset id="smtp" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#smtp';"/><a id="b10" class="blindminus" onfocus="this.blur()" onclick="toggleui(10);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('SMTP Server Settings', 'cforms') ?></p>

			<div id="o10">
				<p><?php _e('In case your web hosting provider doesn\'t support the <strong>native PHP mail()</strong> command feel free to configure cforms to utilize an SMTP mail server to deliver the emails.', 'cforms') ?></p>
	
				<p class="ex"><?php echo sprintf(__('This requires either the latest WP code (coming with the <strong>phpmailer class</strong>) or the <a href="%s">respective files</a> to be copied to your wp-include/ directory. Further, due to the <u>limitations</u> of <em>phpmailer</em> neither <strong>SSL</strong> nor <strong>TLS</strong> are supported for authentication, simply spoken this option may or may not work for your specific SMTP server.', 'cforms'),'http://phpmailer.sourceforge.net/') ?></p>
	
				<?php
					if ( $smtpsettings[0]=='1' ) {
						if ( !file_exists(ABSPATH . WPINC . '/class-phpmailer.php') )
							echo '<div id="message" class="updated fade"><p>'.__('<strong>ERROR</strong>: Can\'t find "<strong>class-phpmailer.php</strong>" in your WP include directory!<br/>If you intend to use an specific STMP server, please make sure that your WP installation is up-to-date and supports the <em>phpmailer</em> class.', 'cforms').'</p></div>';
					}
				?>
				
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_smtp_onoff" name="cforms_smtp_onoff" <?php if($smtpsettings[0]=="1") echo "checked=\"checked\""; ?>/><label for="cforms_smtp_onoff"><strong><?php _e('Enable a SMTP server for relaying emails.', 'cforms') ?></strong></label></div>
				</div>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_smtp_host"><strong><?php _e('SMTP server address', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_host" name="cforms_smtp_host" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[1])); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_user"><strong><?php _e('Username', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_user" name="cforms_smtp_user" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[2])); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_pass"><strong><?php _e('Password', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_pass" name="cforms_smtp_pass" value="<?php echo str_repeat('*',strlen($smtpsettings[3])); ?>"/><p style="width:280px"><?php _e('Please note, that in a normal WP environment you do not need to configure these settings!', 'cforms') ?></p></div>
				</div>
			
			</div>
		</fieldset>


		<fieldset id="upload" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#upload';"/><a id="b11" class="blindminus" onfocus="this.blur()" onclick="toggleui(11);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Global File Upload Settings', 'cforms') ?></p>

			<div id="o11">
				<p>
					<?php echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php echo sprintf(__('Form specific settings (directory path etc.) have been moved to <a href="%s" %s>here</a>.', 'cforms'),'?page='.$plugindir.'/cforms-options.php#fileupload','onclick="setshow(0)"'); ?>
				</p>
	
				<p class="ex"><?php _e('Also, note that by adding a <em>File Upload Box</em> to your form, the Ajax (if enabled) submission method will (automatically) <strong>gracefully degrade</strong> to the standard method, due to general HTML limitations.', 'cforms') ?></p>
	
				<p style="padding-top:15px;"><?php _e('Specify error messages shown in case something goes awry:', 'cforms') ?></p>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_upload_err5"><strong><?php _e('File type not allowed', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err5" id="cforms_upload_err5" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err5'))); ?></textarea></div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err1"><strong><?php _e('Generic (unknown) error', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err1" id="cforms_upload_err1" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err1'))); ?></textarea></div>
				</div>
				
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err2"><strong><?php _e('File is empty', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err2'))); ?></textarea></div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err3"><strong><?php _e('File size too big', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err3" id="cforms_upload_err3" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err3'))); ?></textarea></div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err4"><strong><?php _e('Error during upload', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err4" id="cforms_upload_err4" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err4'))); ?></textarea></div>
				</div>

			</div>
		</fieldset>


		<fieldset id="wpeditor" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#wpeditor';"/><a id="b12" class="blindminus" onfocus="this.blur()" onclick="toggleui(12);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('WP Editor Button support', 'cforms') ?></p>

			<div id="o12">
				<p><?php _e('If you would like to use editor buttons to insert your cforms please enable them below.', 'cforms') ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"><img src="<?php echo $cforms_root; ?>/images/button.gif" alt=""/></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if(get_option('cforms_show_quicktag')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_show_quicktag"><strong><?php _e('Enable TinyMCE', 'cforms') ?></strong> <?php _e('&amp; Code editor buttons', 'cforms') ?></label></div>
				</div>
			</div>
		</fieldset>


		<fieldset id="visitorv" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit1" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#visitorv';"/><a id="b13" class="blindminus" onfocus="this.blur()" onclick="toggleui(13);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Visitor Verification Settings (Q&amp;A)', 'cforms') ?></p>

			<div id="o13">
				<p><?php _e('Getting a lot of <strong>SPAM</strong>? Use these Q&amp;A\'s to counteract spam and ensure it\'s a human submitting the form. To use in your form, add the corresponding input field "<code>Visitor verification</code>" preferably in its own FIELDSET!', 'cforms') ?></p>
				<p class="ex"><strong><u><?php _e('Note:') ?></u></strong> <?php _e('The below error/failure message is also used for <strong>captcha</strong> verification!', 'cforms') ?></p>
	
				<div class="optionsbox" style="margin-top:25px;">
					<div class="optionsboxL"><label for="cforms_codeerr"><?php _e('<strong>Failure message</strong><br />(for a wrong answer)', 'cforms'); ?></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" name="cforms_codeerr" id="cforms_codeerr" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_codeerr'))); ?></textarea></div>
				</div>
	
				<?php $qa = stripslashes(htmlspecialchars(get_option('cforms_sec_qa'))); ?>
		
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_sec_qa"><?php _e('<strong>Questions &amp; Answers</strong><br />format: Q=A', 'cforms') ?></label></div>
					<div class="optionsboxR"><textarea rows="80px" cols="280px" name="cforms_sec_qa" id="cforms_sec_qa" ><?php echo $qa; ?></textarea></div>
				</div>
	
				<p><?php echo sprintf(__('Depending on your personal preferences and level of SPAM security you intend to put in place, you can also use <a href="%s" %s>cforms\' CAPTCHA feature</a>!', 'cforms'),'?page='.$plugindir.'/cforms-help.php#captcha','onclick="setshow(19)"'); ?></p>
	
			</div>
		</fieldset>


		<fieldset id="tracking" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit2" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#tracking';"/><a id="b14" class="blindminus" onfocus="this.blur()" onclick="toggleui(14);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Database Input Tracking', 'cforms') ?></p>

			<div id="o14">
				<p><?php _e('If you like to track your form submissions also via the database, please enable this feature below. If required, this will create two new tables and you\'ll see a new sub tab "<strong>Tracking</strong>" under the cforms menu.', 'cforms') ?></p>
		
				<p><?php echo sprintf(__('If you\'ve enabled the <a href="%s" %s>auto confirmation message</a> feature or have included a <code>CC: me</code> input field, you can optionally configure the subject line/message of the email to include the form tracking ID by using the variable <code>{ID}</code>.', 'cforms'),'?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_database" name="cforms_database" <?php if(get_option('cforms_database')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_database"><span class="abbr" title="<?php _e('Will create two new tables in your WP database.', 'cforms') ?>"><strong><?php _e('Enable Database Tracking', 'cforms') ?></strong></span></label></div>
				</div>

				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_showdashboard" name="cforms_showdashboard" <?php if(get_option('cforms_showdashboard')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_showdashboard"><span class="abbr" title="<?php _e('Make sure to enable your forms individually as well!', 'cforms') ?>"><strong><?php _e('Show last form submissions on dashboard.', 'cforms') ?></strong></span></label></div>
				</div>
				
				<?php if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) :?>
				<div class="optionsbox" style="margin-top:25px;">
					<div class="optionsboxL"><label for="deletetables"><?php _e('<strong>Wipe out</strong> all collected cforms submission data and drop tables.', 'cforms') ?></label></div>
					<div class="optionsboxR"><input type="submit" title="<?php _e('Be careful with this one!', 'cforms') ?>" name="deletetables" class="allbuttons delbutton" value="<?php _e('Delete cforms Tracking Tables', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all collected data?', 'cforms') ?>');"/></div>
				</div>
				<?php endif; ?>

			</div>
		</fieldset>


		<fieldset class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><a id="b15" class="blindminus" onfocus="this.blur()" onclick="toggleui(15);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Uninstalling / Removing cforms', 'cforms') ?></p>

			<div id="o15">
				<p><?php _e('Generally, deactivating this plugin does <strong>not</strong> erase any of its data, if you like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms') ?></p>

				<p><?php _e('This erases <strong>all</strong> cforms data (form &amp; plugin settings). <strong>This is irrevocable!</strong> Be careful.', 'cforms') ?>&nbsp;&nbsp;&nbsp;
					 <input type="submit" name="deleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all of the plugin config data?', 'cforms') ?>');"/>
				</p>
			</div>
		</fieldset>


	</form>

	<?php cforms_footer(); ?>
</div>
