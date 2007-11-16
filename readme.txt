=== cforms II - contact form ===
Contributors: olivers
Donate link: http://www.deliciousdays.com/cforms-forum?forum=2&topic=368&page=1
Tags: ajax, email, secure, visitor, input, order, form, contact
Requires at least: 1.6+
Tested up to: 2.3.1
Stable tag: 6.1

cforms II is an extremely customizable, flexible & powerful contact form plugin.

== Description ==

For **WHAT'S NEW** and a complete version history (new features, bug fixes etc.), see [here](http://www.deliciousdays.com/cforms-forum?forum=2&topic=2&page=1).

= Description =
cforms is a highly customizable, flexible and powerful contact form plugin, covering a variety of use cases and features from attachments to multi form management, you can even have multiple forms on the same page!

= Upgrading Notes: =
For all the new features, see the Help documentation. 

= New 6.0 Plugin Features =
* **NEW:** TinyMCE: enhanced visual appearance of form placeholder &amp; form insertion dialog
* **NEW:** completely revamped admin UI JS core for drag&amp;drop (fixing some IE issues)
* **NEW:** preset forms for &ldquo;quick starts&rdquo; (basic, T-A-F, WP Comment and custom err)
* **NEW:** if no &lsquo;required&rsquo; text is given (empty), then HTML will be entirely omitted

[Check out the cforms CSS Guide and webcast on layout customization](http://www.deliciousdays.com/cforms-forum?forum=1&topic=428&page=1)

[Please visit the cforms plugin page for a detailed features list](http://www.deliciousdays.com/cforms-plugin)

* **General Features**
	* Ajax supported form submission (incl. graceful fall-back)
	* Multiple forms on one or many pages / posts
	* WP Dashboard Support (showing last 5 entries)
	* &ldquo;WP Comment/Message to author&rdquo; Feature (!)
	* Full localization support (see section below)
	* Tell-A-Friend functionality, see Help documentation
	* [...]
* **Supported Form Fields**
	* Fancy Javascript date picker
	* Configurable text elements (non input fields)
	* Various standard input fields:
		* fieldsets
		* single and multi-line input fields
		* select boxes (drop down)
	* [...]
* **Styling*+
	* Fully customizable Look &amp; Feel (optional label ID&rsquo;s and list item ID&rsquo;s!)
	* Built in CSS theme selector and CSS editor
	* Set of predefined themes
	* [...]
* **Input &amp; Validation**
	* Default values for single/multi-line input field
	* Form validation &amp; regular expressions
	* [...]
* **Messaging**
	* Fully customizable auto confirmation message &amp; form email (TXT &amp; HTML)
	* Message(s) can contain system variables and form based variables!
	* [...]
* **Anti SPAM / Security**
	* SPAM protection Q &amp;A
	* SPAM protection using CAPTCHA NEW: + Ajax CAPTCHA reset
* **Extensibility**
	* Support for alternative SMTP server (in case you can&rsquo;t use PHP mail()
	* Generate dynamic forms in real-time
	* Filtering &amp; Download of recorded data
	* [...]

= Localization =
cforms has been revised to fully support WP localization. If you'd like to contribute a language translation, please get in touch. If you have suggestions or would like to point out typos etc, please contact the actual author (see list below) of the respective localization.

* Currently, these language packs are available:
	* **French** \* Author: [La maison de l&rsquo;informatiqu](http://serge-rauber.fr/27-traduction-de-cforms-v55)
	* **German** \* Author: [Sven Wappler](http://www.wappler.eu/cforms/)
	* **Hungarian** \* Original Author: [Ungv&aacute;ri B&eacute;la](http://www.deliciousdays.com/download/cformsII-hu_hu.zip) (discontinued as of v6.0, volunteers?)
	* **Spanish** \* Author: [Samuel Aguilera](http://agamum.net/blog/archivo/plugin-cforms-en-espanol-formularios-para-tu-wordpress.xhtml)

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

[Check out the cforms CSS Guide and webcast on layout customization](http://www.deliciousdays.com/cforms-forum?forum=1&topic=428&page=1)

= Inserting a form via the PHP function call =
Alternatively, you can specifically insert a form (into the sidebar, footer etc. ) per the PHP function call `insert_cform();` for the default (first) form and/or `insert_cform('X');` for any other, subsequent form.

_**Note**: "**X**" represents the number of the form, starting with **2**, 3,4 ..and so forth._

== Frequently Asked Questions ==

Please visit the [cforms plugin forum](http://www.deliciousdays.com/cforms-forum) for up-to-date [FAQs](http://www.deliciousdays.com/cforms-forum?forum=3&page=1) and more help.

== Screenshots ==

[Please visit the cforms plugin page for screenshots & sample](http://www.deliciousdays.com/cforms-plugin)

[Check out the cforms CSS Guide and webcast on layout customization](http://www.deliciousdays.com/cforms-forum?forum=1&topic=428&page=1)
