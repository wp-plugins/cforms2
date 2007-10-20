=== cforms II - contact form ===
Contributors: olivers
Donate link: http://www.deliciousdays.com/
Tags: ajax, email, secure, visitor, input, order, form, contact
Requires at least: 1.6+
Tested up to: 2.3
Stable tag: 5.51

cforms II is an extremely customizable, flexible & powerful contact form plugin.

== Description ==

For **WHAT'S NEW** and a complete version history (new features, bug fixes etc.), see [here](http://www.deliciousdays.com/cforms-forum?forum=2&topic=2&page=1).

= Description =
cforms is a highly customizable, flexible and powerful contact form plugin, covering a variety of use cases and features from attachments to multi form management, you can even have multiple forms on the same page!

= Upgrading Notes: =
For all the new features, see the Help documentation. 

= Plugin Features =
* **NEW: WP Comment / Note feature** 
* **NEW: Dashboard Support**
* **NEW: Fancy Javascript Date Picker**
* **NEW: Field Comparison (e.g., email verification)**
* Individual error messages (HTML enabled), see Help!
* Tell-A-Friend functionality, see Help!
* Support for alternative SMTP server (in case you can't use *PHP mail()*)
* Better non-HTML (TXT only) email support
* Post processing of submitted data (see documentation) 
* Full HTML formatting support for email messages
* Customizable auto confirmation message & form email (Variables!)
* Page redirection after successful form submission
* Creates robust XHTML form code (no floats)
* Alternative form action supported (please read config info!)
* Full localization support (currently: English, German, Spanish)
* Built-in CSS editor & theme chooser
* Additional predefined CSS themes
* Multiple file attachments (upload)
* Multiple forms on one or many pages / posts
* Ajax supported form submission (w/ graceful fall-back)
* Multiple recipients per form (optionally selectable by visitor)
* Fully customizable Look & Feel (** labels & fields **)
* Role Manager support
* Backup & Restore of individual forms
* Cloning, duplication of forms
* Tracking of submissions, searching/filtering & download of data
* Support for tracking unique form submission IDs
* BCC to copy additional admin(s)
* 3rd party email tracking support, e.g. readnotify & didtheyreadit
* SPAM protection (Q & A + Captcha/Ajax captcha reset)
* Submission status (success/failure) optionally via Alert Box
* HTML support for success/failure messages
* Form validation & *regular expressions*
* Configurable text elements
* Convenient handling of input field order, via drag and drop
* HTML support for field labels (*field names*), see examples on Help!
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
	* "CC me" check box for visitors
	* multiple form recipient (drop down list)
	* visitor verification Q&A
	* Tell a Friend fields
	* WP Comment fields
	* Captcha
	* Javascript popup calendar
* Default values for single/multi-line input fields (w/ auto clear,reset)
* Fully integrated with TinyMCE & std editor (buttons)
* Basic widget support
* Clean separation of CSS styling and form code
* Validates XHTML 1.0 Strict

= Localization =
cforms has been revised to fully support WP localization. If you'd like to contribute a language translation, please get in touch. If you have suggestions or would like to point out typos etc, please contact the actual author (see list below) of the respective localization.

* Currently, cforms includes the following language packs (besides English):
	* **French** \* Author: [La maison de l&rsquo;informatiqu](http://serge-rauber.fr/27-traduction-de-cforms-v55)
	* **German** \* Author: [Sven Wappler](http://www.wappler.eu/cforms/)
	* **Hungarian** \* Author: [Ungv&aacute;ri B&eacute;la](http://www.deliciousdays.com/download/cformsII-hu_hu.zip)
	* **Spanish** \* Author: [Samuel Aguilera](http://agamum.net/blog/archivo/plugin-cforms-en-espanol-formularios-para-tu-wordpress.xhtml)
	* **Turkish** \* Author: M.Yasin ERDOGAN, not yet completed

== Installation ==

= Upgrading =

When upgrading to a new release, please properly deactivate the running version before installing the new files.

= Installation =

1. Download & extract plugin using its default directory
2. Upload plugin directory `/contactforms/` to the wordpress `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. *Optionally check cforms.css for indvidual styling of your forms*

= Usage =
Of course you could just use the cforms button in either TinyMCE or the std editor to insert your forms, or disregard the possibility and do it manually:

To do so, please insert `<!--cforms-->` for the first form and/or `<!--cformsX-->` for your other forms in the code view/edit mode to include them in either your **pages** or **posts**.

= Inserting a form via the PHP function call =
Alternatively, you can specifically insert a form (into the sidebar, footer etc. ) per the PHP function call `insert_cform();` for the default (first) form and/or `insert_cform('X');` for any other, subsequent form.

_**Note**: "**X**" represents the number of the form, starting with **2**, 3,4 ..and so forth._

== Frequently Asked Questions ==

Please visit the [cforms plugin forum](http://www.deliciousdays.com/cforms-forum) for up-to-date [FAQs](http://www.deliciousdays.com/cforms-forum?forum=3&page=1) and more help.

== Screenshots ==

[Please visit the cforms plugin page for screenshots & sample](http://www.deliciousdays.com/cforms-plugin)
