<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir   = substr( plugin_basename( __FILE__ ),0,strrpos(plugin_basename( __FILE__ ),'/') );
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;


### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die('Access Denied');
}

?>

<div class="wrap"><a id="top"></a><img src="<?php echo $cforms_root; ?>/images/p4-title.jpg">


		<a id="inserting"></a>
	    <h3 style="margin-top:25px;"><?php _e('Inserting a form', 'cforms'); ?></h3>

		<p><?php _e('Please use <code style="background:#D8FFCC">&lt;!--cforms--&gt;</code> for the first form and/or '.
					'<code style="background:#D8FFCC">&lt;!--cforms<span style="color:red; font-weight:bold;">X</span>--&gt;</code> for your other forms to include '.
					'them in your <em>Pages/Posts</em>. You can apply the aforementioned code either manually or via the editor button (if turned in the '.
					'<a href="?page='.$plugindir.'/cforms-global-settings.php">Plugin Settings</a>). ', 'cforms'); ?></p>

		<p><?php _e('Alternatively, you can specifically insert a form (into the sidebar for instance etc.) per the PHP '.
					'function call <code style="background:#D8FFCC">insert_cform();</code> for the default/first form and/or '.
					'<code style="background:#D8FFCC">insert_cform(\'<span style="color:red; font-weight:bold;">X</span>\');</code> for any other '.
					'form. ', 'cforms'); ?></p>
					
		<p><?php _e('Note: "<span style="color:red; font-weight:bold;">X</span>" represents the number of the form, starting '.
					'with <span style="color:red; font-weight:bold;">2</span>, 3,4 ..and so forth.', 'cforms'); ?></p>


	

		<a id="fields"></a>
	    <h3><?php _e('Supported Form Fields', 'cforms'); ?></h3>

	    <?php _e('These are the supported input fields by cforms and the expected <em>format</em> for their '.
				 'corresponding <em>Field Names</em>.', 'cforms');?></p>

	    <?php _e('While the <em>Field Names</em> are usually just the label of a field (e.g. "Your Name"), they can contain special '.
				'characters for special functionality (e.g. default values, regular expressions for extended field validation '.
				'etc.):', 'cforms');?></p>

		<a id="single"></a>
		<ul class="helpfields">
		  <strong><?php _e('Text only (no input) field:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>text paragraph</em><span style="color:red; font-weight:bold;">|</span>'.
							'<em>css class</em><span style="color:red; font-weight:bold;">|</span><em>optional style</em>', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Please make sure to provide all required information||'.
										'padding:0 100px; font-style:italic; font-size:9x; font-weight:bold;</code>', 'cforms');?></li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-text.png"  alt=""/>
			<li style="margin-top:25px;"><?php _e('The above expression applies the custom class "<code style="background:#D8FFCC">mytextclass</code>" <strong>AND</strong> the specific '.
										'styles "<code style="background:#D8FFCC">padding:0 100px; font-style:italic; font-size:9x; font-weight:bold;</code>" to the paragraph.', 'cforms');?>
			</li>
		</ul>


		<br style="clear:both;"/>


		<a id="single"></a>
		<ul class="helpfields">
		  <strong><?php _e('Single/Multi line input fields:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name</em><span style="color:red; font-weight:bold;">|</span>'.
							'<em>default value</em><span style="color:red; font-weight:bold;">|</span><em><a href="#regexp">'.
							'regular expression</a></em>', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Reference #|xxx-xx-xxx|^[0-9A-Z-]+$</code>', 'cforms');?></li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-single.png"  alt=""/>
			<li style="margin-top:25px;"><?php _e('If you need to omit the <em>default value</em>, the syntax would be: '.
						'<code style="background:#D8FFCC">Your name||^[a-zA-Z \.]+$</code>', 'cforms');?>
			</li>
		</ul>
		

		<br style="clear:both;"/>


		<ul class="helpfields">
		  <strong><?php _e('Select boxes & Radio buttons:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name<span style="color:red; font-weight:bold;">#</span>'.
							'option1<span style="color:red; font-weight:bold;">|</span>value1<span style="color:red; font-weight:bold;">#</span>option2'.
							'<span style="color:red; font-weight:bold;">|</span>value2<span style="color:red; font-weight:bold;">#</span>option3</em>...', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Your age#12-18|kiddo#19-30|young#31-45#45+|older</code>', 'cforms');?></li>
			<li><?php _e('Starting with a \'#\', e.g. #item1#item2#item3... will result in not showing a label '.
						'to the left of the field', 'cforms');?>
			</li>
			<li><?php _e('The <b>option</b> placeholder determins the text displayed to the visitor, <strong>value</strong> what is being sent '.
										'in the email.', 'cforms');?></li>
			<li><?php _e('Is no <strong>value</strong> explicitly given, then the option text = the value sent in the email.', 'cforms');?></li>
			<li><?php _e('<strong><u>Special case (Select box marked "Is Required"):</u></strong> Using a minus symbol <code style="background:#D8FFCC">-</code> as the value (after '.
									'<code style="background:#D8FFCC">|</code>), will mark an option as "not valid"!', 'cforms');?>
									
     			<ul style="margin-top:25px; padding:0; ">
								<img src="<?php echo $cforms_root; ?>/images/example-dropdown.png" style="float:left;" alt=""/>
							  <li><?php _e('<strong>Select box</strong>: <code style="background:#D8FFCC">Your age'.
														 '#Please pick your age group|-#12-18|kiddo#19-30|young#31-45#45+|older</code>', 'cforms');?>
								</li>
							  <li><?php _e('The first parameter, "<strong>Please pick your age group</strong>" has a value of <code style="background:#D8FFCC">-</code>, '.
															'hence it will not validate, forcing the visitor to make a valid selection.', 'cforms');?>
								</li>
							  <li><?php _e('Option "12-18 & 19-30" have both values set, which will be sent in the email instead of the displayed numbers.', 'cforms');?></li>
							  <li><?php _e('"45+" has NO value set, hence the value sent defaults to the text displayed ("45+").', 'cforms');?></li>
						</ul>
			 </li>
		</ul>


		<br style="clear:both;"/>

		<a id="multiselect"></a>
		<ul class="helpfields">
		  <strong><?php _e('Multi select boxes:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name<span style="color:red; font-weight:bold;">#</span>'.
							'option1<span style="color:red; font-weight:bold;">|</span>value1<span style="color:red; font-weight:bold;">#</span>option2'.
							'<span style="color:red; font-weight:bold;">|</span>value2<span style="color:red; font-weight:bold;">#</span>option3</em>...', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Please choose#red#blue#green#yellow#orange#pink</code>', 'cforms');?></li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-ms.png"  alt=""/>
			<li style="margin-top:25px;"><?php _e('Multi select fields can be set to <strong>Is Required</strong>. Unless at least one entry is selected the form won\'t validate.', 'cforms');?>
			<li><?php _e('If <code style="background:#D8FFCC">value1,2,..</code> are not specfified, they default to '.
                                            '<code style="background:#D8FFCC">option1,2,...</code>.', 'cforms');?></li>
			<li><?php _e('Examples for specific values could be the matching color codes: e.g. <code style="background:#D8FFCC">red|#ff0000</code>', 'cforms');?></li>
		</ul>


		<br style="clear:both;"/>


		<ul class="helpfields" style="clear:both;">
		  <strong><?php _e('Check boxes:', 'cforms'); ?></strong>
			<li style="margin-top:10px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name <u>left</u></em><span style="color:red; font-weight:bold;">#'.
							'</span><em>field name <u>right</em></u>', 'cforms');?></li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">#please check if you\'d like more information</code>', 'cforms');?></li>
			<li>
				<?php _e('You can freely choose on which side of the check box the label appears (e.g. <code style="background:#D8FFCC">#label-right-only</code>).', 'cforms');?></li>
			<li>
				<?php _e('If <strong>both</strong> left and right labels are provided, the form email (&DB tracking) will consider only the <strong>right one</strong>', 'cforms');?></li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-checkbox.png"  alt=""/>
			<li style="margin-top:25px;">
				<?php _e('Checkboxes can be flagged "<strong>Is Required</strong>" to support special requiremnts, e.g.: when you require the visitor to confirm '.
									'that he/she has read term & conditions, before submitting the form.', 'cforms');?></li>
		</ul>


		<br style="clear:both;"/>


		<ul class="helpfields">
		  <strong><?php _e('CC: option for user:', 'cforms'); ?></strong>
			<li style="margin-top:10px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name<u>left</u></em><span style="color:red; font-weight:bold;">#'.
							'</span><em>field name <u>right</em></u>', 'cforms');?></li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">#please cc: me</code>', 'cforms');?></li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-cc.png"  alt=""/>
			<li style="margin-top:25px;"><?php _e('If the visitor chooses to be CC\'ed <strong>AND</strong> than no additional auto confirmation email '.
						'(<a href="?page='.$plugindir.'/cforms-options.php#autoconf">if configured</a>) is sent out!', 'cforms');?></li>
			<li><?php _e('Please see also see <em>check boxes</em> above.', 'cforms');?></li>			
		</ul>
		

		<br style="clear:both;"/>


		<a id="multirecipients"></a>
		<ul class="helpfields">
		  <strong><?php _e('Multiple Recipients: ', 'cforms'); ?></strong>
					<em style="color:red;font-size:10px;"><?php _e('Note: This requires corresponding email addresses <a href="?page='.$plugindir.'/cforms-options.php#anchoremail">here</a>!!', 'cforms'); ?></em>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>field name</em><span style="color:red; font-weight:bold;">#</span>'.
						'<em>Name1</em><span style="color:red; font-weight:bold;">#</span><em>Name2</em>'.
						'<span style="color:red; font-weight:bold;">#</span><em>Name3</em>...', 'cforms');?>
			</li>
			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-multi.png"  alt=""/>
			<li style="margin-top:25px;"><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Send to#Joe#Pete#Hillary</code>', 'cforms');?></li>
			<li><?php _e('The order of the names(1,2,3...) provided in the input field <strong>directly</strong> corresponds with the order of email '.
						'addresses configured <a href="?page='.$plugindir.'/cforms-options.php#anchoremail">here</a>.', 'cforms');?>
			</li>
		</ul>
		

		<br style="clear:both;"/>


		<ul class="helpfields">
		  <strong><?php _e('Visitor verification:', 'cforms'); ?></strong>
			<li style="margin-top:10px;">
				<?php _e('Format: --', 'cforms');?></li>
			<li><?php _e('No format required, the field has no configurable label per se, as it is determined at run-time from the list of '.
						 '<strong>Question & Answers</strong> provided <a href="?page='.$plugindir.'/cforms-global-settings.php#visitorv">here</a>.', 'cforms');?></li>
			<li><?php _e('It makes sense to encapsulate this field inside a FIELDSET, to do that simply add a '.
						 '<code style="background:#D8FFCC">New Fieldset</code> field in front of this one.', 'cforms');?></li>		  

			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-vv.png"  alt=""/>
			<ul style="margin:15px 0 0 0; padding:0;">
				  <li><?php _e('The example is made of 2 fields:', 'cforms');?></li>
				  <li><?php _e('<strong>Fieldset</strong>: <code style="background:#D8FFCC">Visitor Verification Question</code>', 'cforms');?></li>
				  <li><?php _e('<strong>Visitor verification</strong>: --', 'cforms');?></li>
			</ul>

		</ul>
		
		
		<br style="clear:both;"/>


		<a id="upload"></a>
		<ul class="helpfields">
		  <strong><?php _e('File Upload Box:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: &nbsp;&nbsp;&nbsp; <em>form label</em>', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Please select a file</code>', 'cforms');?></li>
			<li><?php _e('Please also double-check the <a href="?page='.$plugindir.'/cforms-global-settings.php#upload">general settings</a> for <code style="background:#D8FFCC">File Upload</code> fields.', 'cforms');?>
			</li>

			<img style="float:left; list-style:none;" src="<?php echo $cforms_root; ?>/images/example-upload.png"  alt=""/>
			<ul style="margin:15px 0 0 0; padding:0;">
				  <li><?php _e('The example is made of 3 fields:', 'cforms');?></li>
				  <li><?php _e('<strong>Fieldset</strong>: <code style="background:#D8FFCC">Upload a file</code>', 'cforms');?></li>
				  <li><?php _e('<b>Text only (no input)</b>: <code style="background:#D8FFCC">Note: only .txt and .doc files permitted & there is a file size limit of 200KB!||font-size:9px;</code>', 'cforms');?></li>
				  <li><?php _e('<strong>File Upload Box</strong>: <code style="background:#D8FFCC">Choose a file</code>', 'cforms');?>
						<br/>
						<br/>
						<a href="#top"><?php _e('Back to the top.', 'cforms') ?></a>
						</li>
			</ul>
		
		</ul>



		<a id="hfieldsets"></a>
	    <h3><?php _e('Fieldsets', 'cforms'); ?></h3>
	    
   		<p><?php _e('Fieldsets are definitely part of good form design, they are form elements that are used to create '.
			 						'individual sections of content within a given form.', 'cforms');?></p>

		<img src="<?php echo $cforms_root; ?>/images/example-fieldsets.png" style="float:left; margin-right:15px;"/>
		<ul style="width:32%; float:left; margin-top:20px;">

			<?php _e('The illustration to the left should be quite self-explanatory, here are a few tips around the use of '.
					'fieldsets in your forms:', 'cforms') ?>

			<li style="margin-top:5px;">
				<?php _e('<strong>Fieldsets</strong> can begin anywhere, simply add a <strong>New Fieldset</strong> field '.
						'between or before your form elements', 'cforms') ?></li>
			<li>
				<?php _e('<strong>Fieldsets</strong> do not need to explicitly be closed, a <strong>New Fieldset</strong> '.
						'entry will automatically close the existing (if there is one to close) and reopen a new one.'
						, 'cforms') ?></li>
			<li>
				<?php _e('<strong>End Fieldset</strong> can be used but it works without just as well', 'cforms') ?></li>
			<li>
				<?php _e('If there is no closing <strong>End Fieldset</strong> entry, the plugin assumes to close the set '.
						'just before the submit button', 'cforms') ?></li>

			<a href="#top"><?php _e('Back to the top.', 'cforms') ?></a>
		</ul>


		<br style="clear:both; "/>

			<a id="regexp"></a>
	    <h3><?php _e('Examples for Regular Expressions', 'cforms'); ?></h3>

		<p><?php _e('A regular expression (regex or regexp for short) is a special text string for describing a search pattern, '.
								' according to certain syntax rules. Many programming languages support regular expressions for string manipulation, '.
								' you can use them here to validate user input.', 'cforms');?></p>
		
		<ul class="helpfields">
		  <strong><?php _e('Single/Multi line input fields:', 'cforms'); ?></strong>
			<li style="margin-top:5px;">
				<?php _e('Format: <em>field name</em><span style="color:red; font-weight:bold;">|</span>'.
						'<em>default value</em><span style="color:red; font-weight:bold;">|</span><em>regular expression</em>', 'cforms');?>
			</li>
			<li><?php _e('Example: &nbsp;&nbsp;&nbsp; <code style="background:#D8FFCC">Your name|your full name please|^[a-zA-Z \.]+$</code>', 'cforms');?></li>
			<li><?php _e('Other examples:', 'cforms');?>
				<ul>
					<li><?php _e('US zip code: <code style="background:#D8FFCC">^\d{5}$)|(^\d{5}-\d{4}$</code>', 'cforms');?></li>
					<li><?php _e('US phone #: <code style="background:#D8FFCC">^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$</code>', 'cforms');?></li>
					<li><?php _e('<code style="background:#D8FFCC">^</code> and <code style="background:#D8FFCC">$</code> define the start and the end of the input', 'cforms');?></li>
					<li><?php _e('"<code style="background:#D8FFCC">ab*</code>": matches a string that has an "a" followed by zero or more "b\'s" ("a", "ab", "abbb", etc.);', 'cforms');?></li>
					<li><?php _e('"<code style="background:#D8FFCC">ab+</code>": same, but there\'s at least one b ("ab", "abbb", etc.);', 'cforms');?></li>
					<li><?php _e('"<code style="background:#D8FFCC">[a-d]</code>": a string that has lowercase letters "a" through "d"', 'cforms');?></li>
				</ul>
			</li>
			<li><?php _e('More information can be found <a href="http://weblogtoolscollection.com/regex/regex.php">here</a>, a great regexp repository <a href="http://regexlib.com">here</a>.', 'cforms');?></li>
		</ul>



		<a id="dynamicforms"></a>
	    <h3><?php _e('Deploying dynamic forms', 'cforms'); ?></h3>

		<p><?php _e('This is really for hard core deployments, where <em>real-time manipulation</em> of a form & fields are required.', 'cforms'); ?></p>

		<p><?php _e('<strong>A few things to note:</strong>', 'cforms'); ?></p>
		<ol>
			<li><?php _e('Dynamic forms only work in <strong>non-Ajax</strong> mode.', 'cforms');?></li>
			<li><?php _e('Each dynamic form references and thus requires <strong>a base form defined</strong> in the cforms form settings. All its settings will be used, except the form (&field) definition.', 'cforms');?></li>
			<li><?php _e('Any of the form fields described in the plugins\' <strong>HELP!</strong> section can be dynamically generated.', 'cforms');?></li>
			<li><?php _e('Function call to generate dynamic forms: <code style="background:#D8FFCC">insert_custom_cform($fields:array,$form-no:int);</code> with', 'cforms');?>

                <br/><br/>
                <code style="background:#D8FFCC">$form-no</code>: empty string for the first (default) form and <strong>2</strong>,3,4... for any subsequent form<br/>
                <code style="background:#D8FFCC">$fields</code> : 
    
                <code style="background:#D8FFCC"><pre>
            $fields['label'][n]   = 'label';                no default value: expected format described in plugin HELP! section 
            $fields['type'][n]    = 'input field type';     default: 'textfield';
            $fields['isreq'][n]   = true|false;             default: false;
            $fields['isemail'][n] = true|false;             default: false;
            
            n = 0,1,2...
                </pre></code></li>
    		</ol>


        <strong>Form input field types ('type'):</strong>
        <ul style="list-style:none;">
        <li>        
            <table>
                <tr><td>text paragraph:</td><td> <code style="background:#D8FFCC">textonly</code></td></tr>
                <tr><td>single input field:</td><td> <code style="background:#D8FFCC">textfield</code></td></tr>
                <tr><td>multi line field:</td><td> <code style="background:#D8FFCC">textarea</code></td></tr>
                <tr><td>check boxes:</td><td> <code style="background:#D8FFCC">checkbox</code></td></tr>
                <tr><td>drop down fields:</td><td> <code style="background:#D8FFCC">selectbox</code></td></tr>
                <tr><td>radio buttons:</td><td> <code style="background:#D8FFCC">radiobuttons</code></td></tr>
                <tr><td>'CC' check box <sup>*)</sup>:</td><td> <code style="background:#D8FFCC">ccbox</code></td></tr>
                <tr><td>Multi-recipients field <sup>*)</sup>:</td><td> <code style="background:#D8FFCC">emailtobox</code></td></tr>
                <tr><td>Spam/Visitor verification <sup>*)</sup>:</td><td> <code style="background:#D8FFCC">verification</code></td></tr>
                <tr><td>File Upload fields <sup>*)</sup>:</td><td> <code style="background:#D8FFCC">upload</code></td></tr>
                <tr><td>Begin of a fieldset:</td><td> <code style="background:#D8FFCC">fieldsetstart</code></td></tr>
                <tr><td>End of a fieldset:</td><td> <code style="background:#D8FFCC">fieldsetend</code></td></tr>
            </table>
        </li>
        <li><sup>*)</sup> <em>should only be used <strong>once</strong> per generated form!</em></li>
        </ul>

        <br/>

        <strong>Simple example:</strong>
        <ul style="list-style:none;">
        <li>
        <code style="background:#D8FFCC"><pre>
$fields = array();

$fields['label'][0]   ='Your Name|Your Name';
$fields['type'][0]    ='textfield';
$fields['isreq'][0]   ='1';
$fields['isemail'][0] ='0';

$fields['label'][1]   ='Your Email';
$fields['type'][1]    ='textfield';
$fields['isreq'][1]   ='0';
$fields['isemail'][1] ='1';

insert_custom_cform($fields,'');    //call default form with new fields (2)
        </pre></code>
        </li>
        </ul>

        <br/>

        <strong>More advanced example</strong> (file access)<strong>:</strong>
        <ul style="list-style:none;">
        <li>
        <code style="background:#D8FFCC"><pre>
$fields['label'][0]  ='Your Name|Your Name';
$fields['type'][0]   ='textfield';
$fields['isreq'][0]  ='1';
$fields['isemail'][0]='0';
$fields['label'][1]  ='Email';
$fields['type'][1]   ='textfield';
$fields['isreq'][1]  ='0';
$fields['isemail'][1]='1';
$fields['label'][2]  ='Please pick a month for delivery:||font-size:14px; padding-top:12px; text-align:left;';
$fields['type'][2]   ='textonly';

$fields['label'][3]='Deliver on#Please pick a month|-#';

$fp = fopen(dirname(__FILE__).'/months.txt', "r"); // need to put this file into your themes dir!

while ($nextitem = fgets($fp, 512))		
	$fields['label'][3] .= $nextitem.'#';

fclose ($fp);
$fields['label'][3] = substr( $fields['label'][3], 0, strlen($fields['label'][3])-1 );  //remove the last '#'

$fields['type'][3]='selectbox';
$fields['isreq'][3]='1';
$fields['isemail'][3]='0';

insert_custom_cform($fields,5);    //call form #5 with new fields (4)
</pre></code>
        </li>
        </ul>

        With <code style="background:#D8FFCC">month.txt</code> containing all months of a year:
        <ul style="list-style:none;">
        <li>
        <code style="background:#D8FFCC"><pre>
January
February
March
...
        </pre></code>
        </li>
        </ul>
                
		<a href="#top"><?php _e('Back to the top.', 'cforms') ?></a>



		<a id="CSS"></a>
	    <h3><?php _e('Styling Your Forms (cforms.css)', 'cforms'); ?></h3>

		<p><?php _e('Please see <code style="background:#D8FFCC">cforms.css</code> in your plugin directory for global '.
					'settings as well as individual configuration on a form, fieldset and even input field level.', 'cforms'); ?></p>

		<p><?php _e('I hope the <strong>CSS documentation & notes</strong> in this file are sufficient. The default configuration for all '.
					'forms should be compliant with most browsers, however, there are little (if any) limitations to completely adjust the layout '.
					'and branding of your forms, please see the plugin home page for examples and explanations.', 'cforms'); ?></p>

		<a href="#top"><?php _e('Back to the top.', 'cforms') ?></a>



		<a id="troubles"></a>
    <h3><?php _e('Having Troubles?', 'cforms'); ?></h3>

		<p><?php _e('For up-to-date information check the <a href="http://www.deliciousdays.com/cforms-plugin#faq">FAQs</a> '.
					' and comment section on the plugin homepage.', 'cforms'); ?></p>


	<?php cforms_footer(); ?>
</div>
