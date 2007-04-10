=== cforms ===
Contributors: olivers
Donate link: http://www.deliciousdays.com/
Tags: email, secure, custom, contact, form, visitor, input, order
Requires at least: 1.6+
Tested up to: 2.1.2
Stable tag: 3.3

cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of features from attachments to multi form management.

== Description ==

cforms is a highly customizable, flexible and powerful contact form plugin, 
covering a variety of use cases and features from attachments to multi form 
management, you can even have multiple forms on the same page!

# Plugin Features
* File attachments (upload)
* Multiple forms on one or many pages / posts
* Ajax supported form submission (w/ graceful fall-back)
* Multiple recipients per form (optionally selectable by visitor)
* Fully customizable Look & Feel
* Role Manager support
* Backup & Restore of individual forms
* Cloning, duplication of forms
* Tracking of submitted data per DB
* SPAM protection
* Submission status (success/failure) optionally via Alert Box
* Form validation & *regular expressions*
* Configurable text elements
* Convenient handling of input field order, via drag and drop
* Various standard form building blocks:
	* single and multi-line fields
	* select boxes (drop down)
	* check boxes
	* radio buttons
* ...and a few special ones:
	* "CC me" check box for visitors
	* fieldsets
	* multiple form recipient (drop down list)
	* visitor verification
* Default values for single/multi-line input fields
* Fully integrated with TinyMCE & std editor (buttons)
* Clean separation of CSS styling and form code
* Validates

== Installation ==

= Installation =

1. Download & extract plugin using its default directory
2. Upload plugin directory `/contactforms/` to the wordpress `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. *Optionally check cforms.css for indvidual styling of your forms*

= Usage =
Of course you could just use the cforms button in either TinyMCE or the std editor to insert your forms,
or disregard the option and do it manually.

= Inserting a form in a posts or page =
To do so, please insert `<!--cforms-->` for the first form and/or `<!--cforms**X**-->` for your other forms in the code view/edit
mode to include them in either your **pages** or **posts**.

= Inserting a form via the PHP function call =
Alternatively, you can specifically insert a form (into the sidebar, footer etc. ) per the PHP function call `insert_cform();`
for the default (first) form and/or `insert_cform('**X**');` for any other, subsequent form.

_**Note**: "**X**" represents the number of the form, starting with **2**, 3,4 ..and so forth._

== Frequently Asked Questions ==

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

= Screenshot =

[custom form](http://www.deliciousdays.com/wp-content/themes/dd/images/cforms/form_tshirt_order.jpg)


