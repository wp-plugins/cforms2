<?php
$buffer='';
function download_cforms() {

	global $buffer, $wpdb, $cformsSettings;
	$br="\n";

	if( isset($_REQUEST['savecformsdata']) || isset($_REQUEST['saveallcformsdata']) ) {

		if( isset($_REQUEST['savecformsdata']) ){
	        $noDISP = '1'; $no='';
	        if( $_REQUEST['noSub']<>'1' )
	            $noDISP = $no = $_REQUEST['noSub'];

	    	$buffer .= SaveArray($cformsSettings['form'.$no]).$br;
			$filename = 'form-settings.txt';
		}else{
	    	$buffer .= SaveArray($cformsSettings).$br;
			$filename = 'all-cforms-settings.txt';
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " .(string)(strlen($buffer)) );
		print $buffer;
		exit();

	} ### saving form settings

}

### Save the array
function SaveArray($vArray)
{
	global $buffer;
    // Every array starts with chr(1)+"{"
    $buffer .=  "\0{";

    // Go through the given array
    reset($vArray);
    while (true)
    {
        $Current = current($vArray);
        $MyKey = addslashes(strval( key($vArray) ));
        if (is_array($Current)) {
            $buffer .= $MyKey."\0";
            SaveArray($Current);
            $buffer .= "\0";
        } else {
            $Current = addslashes($Current);
            $buffer .= "$MyKey\0$Current\0";
        }

        ++$i;
        while (!next($vArray))
            if (++$i > count($vArray)) break;

        if ($i > count($vArray)) break;
    }
    $buffer .= "\0}";
}


### Add cforms menu to admin
function cforms_menu() {
	global $wpdb, $submenu;

	$cformsSettings = get_option('cforms_settings');

	$tablesup = ($wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions)?true:false;

	if (function_exists('add_menu_page')) {
		add_menu_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-options.php');
	}
	elseif (function_exists('add_management_page')) {
		add_management_page(__('cformsII', 'cforms'), __('cformsII', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-options.php');
	}

	if (function_exists('add_submenu_page')) {
		add_submenu_page($cformsSettings['global']['plugindir'].'/cforms-options.php', __('Global Settings', 'cforms'), __('Global Settings', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-global-settings.php');
		if ( ($tablesup || isset($_REQUEST['cforms_database'])) && !isset($_REQUEST['deletetables']) )
			add_submenu_page($cformsSettings['global']['plugindir'].'/cforms-options.php', __('Tracking', 'cforms'), __('Tracking', 'cforms'), 'track_cforms', $cformsSettings['global']['plugindir'].'/cforms-database.php');
		add_submenu_page($cformsSettings['global']['plugindir'].'/cforms-options.php', __('Styling', 'cforms'), __('Styling', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-css.php');
		add_submenu_page($cformsSettings['global']['plugindir'].'/cforms-options.php', __('Help!', 'cforms'), __('Help!', 'cforms'), 'manage_cforms', $cformsSettings['global']['plugindir'].'/cforms-help.php');
	}
}

function cforms_init() {
	global $wpdb;

	$plugindir   = basename(dirname(__FILE__));

	$role = get_role('administrator');
	if(!$role->has_cap('manage_cforms')) {
		$role->add_cap('manage_cforms');
	}
	if(!$role->has_cap('track_cforms')) {
		$role->add_cap('track_cforms');
	}

	### alter tracking tables if needed
	$tables = $wpdb->get_col("SHOW TABLES FROM `" . DB_NAME . "` LIKE '$wpdb->cformssubmissions'",0);

	if( $tables[0]==$wpdb->cformssubmissions ) {
		$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->cformssubmissions}");
		if ( $columns[2]->Field == 'date' )
			$result = $wpdb->query("ALTER TABLE `{$wpdb->cformssubmissions}` CHANGE `date` `sub_date` TIMESTAMP");
	}

	### try to adjust cforms.js automatically
	$jsContent = $jsContentNew = '';
	if ( $fhandle = fopen(dirname(__FILE__).'/js/cforms.js', "r") ) {
		$jsContent = fread($fhandle, filesize(dirname(__FILE__).'/js/cforms.js'));
	    fclose($fhandle);

		$URIprefix = get_option('siteurl');
		$pathToAjax = $URIprefix . '/wp-content/plugins/cforms/lib_ajax.php';

        if ( defined('WP_CONTENT_URL') )
			$pathToAjax = $URIprefix.'/'.WP_CONTENT_URL.'/plugins/'.$plugindir. '/lib_ajax.php';

        if ( defined('WP_PLUGIN_URL') )
			$pathToAjax = $URIprefix.'/'.WP_PLUGIN_URL.'/'.$plugindir. '/lib_ajax.php';

        if ( defined('PLUGINDIR') )
			$pathToAjax = $URIprefix.'/'.PLUGINDIR.'/'.$plugindir. '/lib_ajax.php';

       	$jsContentNew = str_replace('\'/wp-content/plugins/cforms/lib_ajax.php\'',"'{$pathToAjax}'",$jsContent);
	}
	if ( $jsContentNew<>'' && $jsContentNew<>$jsContent && ($fhandle = fopen(dirname(__FILE__).'/js/cforms.js', "w")) ) {
	    fwrite($fhandle, $jsContentNew);
	    fclose($fhandle);
	}

	### save ABSPATH for ajax routines
	if ( defined('ABSPATH') && ($fhandle = fopen(dirname(__FILE__).'/abspath.php', "w")) ) {
	    fwrite($fhandle, "<?php \$abspath = '". ABSPATH ."'; ?>\n");
	    fclose($fhandle);
	}

}

### some css for arranging the table fields in wp-admin
function cforms_options_page_style() {
	$cformsSettings = get_option('cforms_settings');
	echo	'<link rel="stylesheet" type="text/css" href="' . $cformsSettings['global']['cforms_root'] . '/cforms-admin.css" />' . "\n" .
			'<script type="text/javascript" src="' . $cformsSettings['global']['cforms_root'] . '/js/jquery.js"></script>' . "\n" .
			'<script type="text/javascript" src="' . $cformsSettings['global']['cforms_root'] . '/js/interface.js"></script>' . "\n".
			'<script type="text/javascript" src="' . $cformsSettings['global']['cforms_root'] . '/js/cformsadmin.js"></script>' . "\n";
}

### footer unbder all options pages
function cforms_footer() {
	global $localversion;
?>	<p style="padding-top:50px; font-size:11px; text-align:center;">
		<em>
			<?php echo sprintf(__('For more information and support, visit the %s support forum %s. ', 'cforms'),'<strong>cforms</strong> <a href="http://www.deliciousdays.com/cforms-forum/" title="cforms support forum">','</a>') ?>
			<?php _e('Translation provided by Oliver Seidel, for updates <a href="http://deliciousdays.com/cforms-plugin">check here.</a>', 'cforms') ?>
		</em>
	</p>

	<p align="center">Version v<?php echo $localversion; ?></p>
<?php
}

### if all data has been erased quit
function check_erased() {
	global $cformsSettings;
    if ( $cformsSettings['global']['cforms_formcount'] == '' ){
		?>
	    <div class="wrap">
	    <h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	    <p class="ex"><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	    <p class="ex"><?php _e('In case disabling/enabling doesn\'t seem to properly set the plugin defaults, try login out and back in and <strong>don\'t select the checkbox for activation</strong> on the plugin page.', 'cforms') ?></p>
	    </div>
		<?php
	    return true;
	}
	return false;
}
?>