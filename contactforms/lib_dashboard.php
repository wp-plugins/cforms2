<?php
### Show entries on dashboard
function cforms_dashboard() {
	// Daten lesen von Funktion fs_getfeeds()
	global $wpdb, $plugindir;

	if(!current_user_can('track_cforms'))
		return;

	$WHERE='';
	for($i=0; $i<get_option('cforms_formcount'); $i++){
		$no = ($i==0)?'':($i+1);
		if ( get_option('cforms'.$no.'_dashboard') == '1' )
			$WHERE .= "'$no',";
	}	
	
	if ( $WHERE <> '')
		$WHERE = "WHERE form_id in (".substr($WHERE,0,-1).")";
	else
		return;	
			
	$entries = $wpdb->get_results("SELECT * FROM {$wpdb->cformssubmissions} $WHERE ORDER BY sub_date DESC LIMIT 0,5");	
	
	$content = "<h3>" . __('Recent cforms entries','cforms') . " <a href='admin.php?page=".$plugindir."/cforms-database.php'>&raquo;</a> </h3>";
	$content.= "<ul style='font-size:0.8em'>";
	if( count($entries)>0 ){
		foreach($entries as $entry)
				$content.= "<li>".get_option('cforms'.$entry->form_id.'_fname')." [<a href='admin.php?page=".$plugindir."/cforms-database.php#e$entry->id'>$entry->email</a>] @ $entry->sub_date</li>";
	}
	else
		$content.= "<li>".__('No entries so far','cforms')."</li>";
		
	$content.= "</ul>";

	print 
	'<script language="javascript" type="text/javascript"> 
		var ele = document.getElementById("zeitgeist");
		if (ele) {
			var div = document.createElement("DIV");
			div.innerHTML = "'.$content.'";
			ele.appendChild(div);
	} </script>';
	
}
?>
