=== cforms ===
Contributors: olivers
Donate link: http://www.deliciousdays.com/
Tags: ajax, email, secure, visitor, input, order, form, contact
Requires at least: 1.6+
Tested up to: 2.2
Stable tag: 4.5

cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of features from attachments to multi form management!

== Description ==

= Please Note, May 10th  =
When updating from any version prior to v4.5, please make backup copies of your 
own customized theme files (.css). The form structure and CSS has changed,
in favour of much cleaner and more robust code base!

If you'd like to customize the default **cforms** CSS theme and are not a CSS expert,
you may find [this document](http://www.deliciousdays.com/download/cforms-css-guide.pdf) helpful!	

= Description =
cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of use cases and features from attachments to multi form 
management, you can even have multiple forms on the same page!

= Plugin Features =
* Creates robust XHTML form code (no floats)
* Full localization support (currently: English, German, Spanish)
* Built-in CSS editor & theme chooser
* File attachments (upload)
* Multiple forms on one or many pages / posts
* Ajax supported form submission (w/ graceful fall-back)
* Multiple recipients per form (optionally selectable by visitor)
* Fully customizable Look & Feel
* Role Manager support
* Backup & Restore of individual forms
* Cloning, duplication of forms
* Tracking of submitted data per DB (unique form submission IDs)
* SPAM protection (Q & A + Captcha)
* Submission status (success/failure) optionally via Alert Box
* Form validation & *regular expressions*
* Configurable text elements
* Convenient handling of input field order, via drag and drop
* Supporting DISABLED form fields for better usability
* Various standard form building blocks:
	* fieldsets
	* single and multi-line fields
	* select boxes (drop down)
	* multi select boxes
	* check boxes
	* grouped check boxes
	* radio buttons
* ...and a few special ones:
	* subject input field (determins the subject of the form email)
	* "CC me" check box for visitors
	* multiple form recipient (drop down list)
	* visitor verification Q&A
	* Captcha
* Default values for single/multi-line input fields (w/ auto clear,reset)
* Fully integrated with TinyMCE & std editor (buttons)
* Clean separation of CSS styling and form code
* Validates

= Localization =
cforms has been revised to fully support WP localization. If you'd like to contribute a language translation, please get in touch.
If you have suggestions or would like to point out typos etc, please contact the actual author (see list below) of the respective localization.

* Currently, cforms includes the following language packs (besides English):
	* **German** \* Author: [Sven Wappler](http://www.wordpressbox.de/plugins/cforms/)
	* **Spanish** \* Author: [Samuel Aguilera](http://agamum.net/blog/archivo/plugin-cforms-en-espanol-formularios-para-tu-wordpress.xhtml)
	* **Italian** \* Author: webbite.it (pending)

== Installation ==

= Installation =

1. Download & extract plugin using its default directory
2. Upload plugin directory `/contactforms/` to the wordpress `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. *Optionally check cforms.css for indvidual styling of your forms*

= Usage =
Of course you could just use the cforms button in either TinyMCE or the std editor to insert your forms,
or disregard the possibility and do it manually.

---

= Inserting a form in a posts or page =
To do so, please insert `<!--cforms-->` for the first form and/or `<!--cformsX-->` for your other forms in the code view/edit
mode to include them in either your **pages** or **posts**.


= Inserting a form via the PHP function call =
Alternatively, you can specifically insert a form (into the sidebar, footer etc. ) per the PHP function call `insert_cform();`
for the default (first) form and/or `insert_cform('X');` for any other, subsequent form.

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
	$fields['label'][n]      = 'label';                expected format described in plugin HELP! section 
	$fields['type'][n]       = 'input field type';     default: 'textfield';
	$fields['isreq'][n]      = true|false;             default: false;
	$fields['isemail'][n]    = true|false;             default: false;
	$fields['isclear'][n]    = true|false;             default: false;
	$fields['isdisabled'][n] = true|false;             default: false;

    n = 0,1,2...
    `

5. Input field types ('type'):

    text paragraph: `textonly`  
    single input field: `textfield`  
    multi line field: `textarea`  
    check boxes: `checkbox`  
    check boxes groups: `checkboxgroup`  
    drop down fields: `selectbox`  
    multi-select boxes: `multiselect`  
    radio buttons: `radiobuttons`  
    'CC' check box: `ccbox` \*)  
    Multi-recipients field: `emailtobox` \*)  
    Spam/Visitor verification (Q&A): `verification` \*)  
    Spam/Captcha: `captcha` \*)  
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
	`$fields['isclear'][0] ='0';`
	
	`$fields['label'][1]   ='Your Email';`
	`$fields['type'][1]    ='textfield';`
	`$fields['isreq'][1]   ='0';`
	`$fields['isemail'][1] ='1';`
	
    `insert_custom_cform($fields,'');`


See the "**Other Notes**" section for another example.

== Frequently Asked Questions ==

Please visit the [cforms plugin forum](http://www.deliciousdays.com/cforms-forum)
for up-to-date [FAQs](http://www.deliciousdays.com/cforms-forum?forum=3&page=1) 
and more help.

== Screenshots ==

[Please visit the cforms plugin page for screenshots & samples](http://www.deliciousdays.com/cforms-plugin)

== A more advanced dynamic form example ==

This example shows you how to store certain input field (select/drop-down box) information in a file and load it dynamically at run-time.

File: [months.txt](http://www.deliciousdays.com/download/months.txt) *(to be copied into your WP `theme` directory)*

Code: *(to go into your page.php or whereever else you want it :)*

`
$fields['label'][0]  ='Your Name|Your Name';
$fields['type'][0]   ='textfield';
$fields['isreq'][0]  ='1';
$fields['isemail'][0]='0';
$fields['isclear'][0]='1';
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
