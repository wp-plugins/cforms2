=== cforms ===
Contributors: olivers
Donate link: http://www.deliciousdays.com/
Tags: email, secure, custom, contact, form, visitor, input, order
Requires at least: 1.6+
Tested up to: 2.1.3
Stable tag: 3.5

cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of features from attachments to multi form management.

== Description ==

cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of use cases and features from attachments to multi form 
management, you can even have multiple forms on the same page!

= Plugin Features =
* Full localization support (currently: English, German)
* File attachments (upload)
* Multiple forms on one or many pages / posts
* Ajax supported form submission (w/ graceful fall-back)
* Multiple recipients per form (optionally selectable by visitor)
* Fully customizable Look & Feel
* Role Manager support
* Backup & Restore of individual forms
* Cloning, duplication of forms
* Tracking of submitted data per DB (unique form submission IDs)
* SPAM protection
* Submission status (success/failure) optionally via Alert Box
* Form validation & *regular expressions*
* Configurable text elements
* Convenient handling of input field order, via drag and drop
* Various standard form building blocks:
	* fieldsets
	* single and multi-line fields
	* select boxes (drop down)
	* multi select boxes
	* check boxes
	* radio buttons
* ...and a few special ones:
	* "CC me" check box for visitors
	* multiple form recipient (drop down list)
	* visitor verification
* Default values for single/multi-line input fields (w/ auto clear,reset)
* Fully integrated with TinyMCE & std editor (buttons)
* Clean separation of CSS styling and form code
* Validates

= Localization =
cforms has been revised to fully support WP localization. If you’d like to contribute a language translation, please get in touch.
If you have suggestions or would like to point out typos etc, please contact the actual author (see list below) of the respective localization.

* Currently, cforms include the following language packs (besides English):
	* --German-- \* Author: [Sven Wappler](http://www.wappler.eu)

== Installation ==

= Installation =

1. Download & extract plugin using its default directory
2. Upload plugin directory `/contactforms/` to the wordpress `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. *Optionally check cforms.css for indvidual styling of your forms*

= Usage =
Of course you could just use the cforms button in either TinyMCE or the std editor to insert your forms,
or disregard the option and do it manually.

---

= Inserting a form in a posts or page =
To do so, please insert `<!--cforms-->` for the first form and/or `<!--cforms**X**-->` for your other forms in the code view/edit
mode to include them in either your **pages** or **posts**.


= Inserting a form via the PHP function call =
Alternatively, you can specifically insert a form (into the sidebar, footer etc. ) per the PHP function call `insert_cform();`
for the default (first) form and/or `insert_cform('**X**');` for any other, subsequent form.

_**Note**: "**X**" represents the number of the form, starting with **2**, 3,4 ..and so forth._

= Inserting a dynamic form via PHP =

**Note:**

1. Dynamic forms only work in **non-Ajax** mode.

2. Each dynamic form references and thus requires **a base form** defined in the cforms form settings. All its settings will be used, except the form (&field) definition.

3. Any of the form fields described in the plugins' **HELP!** section can be dynamically generated. 

4. **Function call** to generate dynamic forms: `insert_custom_cform($fields:array,$form-no:int);` with 

    `$form-no`: '' for the first (default) form and **2**,3,4... for any subsequent form  
    `$fields` : 
    `
	$fields['label'][n]   = 'label';                no default value: expected format described in plugin HELP! section 
	$fields['type'][n]    = 'input field type';     default: 'textfield';
	$fields['isreq'][n]   = true|false;             default: false;
	$fields['isemail'][n] = true|false;             default: false;

    n = 0,1,2...
    `

5. Input field types ('type'):

    text paragraph: `textonly`  
    single input field: `textfield`  
    multi line field: `textarea`  
    check boxes: `checkbox`  
    drop down fields: `selectbox`  
    radio buttons: `radiobuttons`  
    'CC' check box: `ccbox` \*)  
    Multi-recipients field: `emailtobox` \*)  
    Spam/Visitor verification: `verification` \*)  
    File Upload fields: `upload` \*)  
    Begin of a fieldset: `fieldsetstart`  
    End of a fieldset: `fieldsetend`  

    *) should only be used **once** per generated form!

   
6. Simple example:

    `$fields = array();`
    
	`$fields['label'][0]   ='Your Name|Your Name';`
	`$fields['type'][0]    ='textfield';`
	`$fields['isreq'][0]   ='1';`
	`$fields['isemail'][0] ='0';`
	
	`$fields['label'][1]   ='Your Email';`
	`$fields['type'][1]    ='textfield';`
	`$fields['isreq'][1]   ='0';`
	`$fields['isemail'][1] ='1';`
	
    `insert_custom_cform($fields,'');`


See the "**Other Notes**" section for another example.

== Frequently Asked Questions ==

= The Submit Button is being pushed to the bottom of the page? =

Try changing the CSS style for the *send button* `.cform input.sendbutton {...` in this way:
Remove these attributes: `clear:both; float:right;`
And add these instead: `margin: 0 0 0 310px; float:none!important;`

= The form is being pushed to the bottom on my blog?! =

Check your blog template. 99% it's due to a *div container* (holding the post data) that's **not floated properly**! This often happens to 3-column layouts but could also happen to 2-column blog templates.

*Update*: Check your `cforms.css` files for the following line
`
.cformfieldsets { margin:10px 0; padding:8px 0 11px 0; border:1px solid #adadad; border-left-color:#ececec; border-top-color:#ececec; **clear:left;** }
`
and remove the last attribute: `clear:left;`. This should help fix many of the issues seen especially with 3 column WP themes that come with a non-floated middle column. 

If it still looks funny (and/or your sendbutton seems displaced) try also to remove the following two lines:

1. `* html .cformfieldsets { position: relative; margin-top:20px; padding-top:15px; } /*ie6 hack*/`
2. `* html .cform legend   { position:absolute; top: -10px; left: 10px; margin-left:0; } /*ie hack*/`

*The above 2 lines were meant to make **IE fieldsets** look like FF (nicer), but if they cause you headaches, simply remove them.* 

= My browser shows a TinyMCE error ("realtinyMCE" is undefined) ?! =

This might be a known Wordpress issue, check [here](http://trac.wordpress.org/ticket/3882) for more info and possible work-arounds, fixes until the next release of WP takes care of it.

= The general layout of my form doesn't look right!? =

Please check and tweak your cforms.css stylesheet for proper configuration. The default classes should provide reasonable formatting of the form & fields, but every WP theme / layout behaves a little different.

= The 2nd form I created has a different layout!? =

In previous plugin releases, the CSS information for the 2nd form had been customized for demo purposes, this has been removed as of release 3.3. In any event, feel free to edit `cforms.css` and remove the top block (indicated as demo).

[Please visit the cforms plugin forum for more help](http://www.deliciousdays.com/cforms-forum)

== Screenshots ==

[Please visit the cforms plugin page for screenshots & samples](http://www.deliciousdays.com/cforms-plugin)

== A custom form example ==

Please download [this zip](http://www.deliciousdays.com/download/custom-cform-example.zip) which includes the following files:

* cforms-extra.css
* fieldset_bg_sample2.jpg
* fieldset_bg_sample2b.jpg
* fieldset_bg_sample2c.jpg
* submitbtn2.jpg
* form_tshirt_order.txt

1. All images go into your `contactforms/images/` directory
2. The CSS code in `cforms-extra.css` has to be copy/pasted into your `cforms.css` file
3. Create form #4 and 'upload' using the **Restore** feature `form_tshirt_order.txt`
4. Done

**Note:** *If you'd like to use the above styling for any other form(s), change the ID's accordingly. E.g. change*

`#cforms4form p.subtitle`

*to for instance (form #2)*

`#cforms2form p.subtitle`

**Screenshot** [custom form](http://www.deliciousdays.com/wp-content/themes/dd/images/cforms/form_tshirt_order.jpg)

== A more advanced dynamic form example ==

This example shows you how to store certain input field (select/drop-down box) information in a file and load it dynamically at run-time.

File: [months.txt](http://www.deliciousdays.com/download/months.txt) *(to be copied into your WP `theme` directory)*

Code: *(to go into your page.php or whereever else you want it :)*

`
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

insert_custom_cform($fields,5); 
`

*Note: This is a very flexible way of introducing dynamically changing form contents, you can just a easily query a DB table and insert the results into the `$fields` array*
