=== Postscript ===
Contributors: hearvox
Donate link: http://hearingvoices.com/tools
Tags: script, javascript, styles, stylesheet, css
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

For Data-visionaries and Multi-mediators: Make posts sing with scripts and styles, all from within WordPress.

== Description ==

= Enqueue Registered Styles and Scripts (by Handle) =

Use the Postscript meta box (Edit Post screen) to enqueue registered of styles and script, listed in checkboxes by handle.).

= Enqueue Unregistered Styles, Scripts, and Data Files (by URL) =

For each post, you can also enqueue unregistered files, by entering in the meta boxes text fields for:
* A stylesheet URL.
* A JavaScript URL.
* A data URL (e.g, JSON).

= Add Post and Body Classes =

For each post, you can also add:
* Body classes, to the HTML `<body>` tag (requires `body_class()` in theme).
* Post classes, to the WordPress inserted `class="post"` list (requires `post_class()` in theme).

== Installation ==

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Setting > Postscript options screen to select allowed scripts and styles.

== Frequently Asked Questions ==

= Question? =
Answer.

== Screenshots ==

1. Caption of 1st screenshot.
2. screenshot-1.png, sreenshot-2.png
3. This screen shot description corresponds to screenshot-3.png. Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`.

== Changelog ==

= 0.1.0 =
* Initial test version.

== Upgrade Notice ==

= 0.5.0 =
Visible on update notice in plugin or update page.

== Translations ==

* English - default, always included

*Note:* This plugin is built to be easily translatable. Please contribute a translation in your language. 

The WordPress.org Polyglots Team maintains a comprehensive [Translator’s Handbook](https://make.wordpress.org/polyglots/handbook/). All text strings in this plugin are localized, following the guidelines of the Wordpress.org Plugin Handbook's [Internationalization section](https://developer.wordpress.org/plugins/internationalization/).

(The additional sections display in "Other Notes" /other_notes/.)

== Tech Notes ==

= Default Scripts and Styles =

notes for this section...
[wp_enqueue_script()](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)
"Default Scripts Included and Registered by WordPress"
Complete list: inspect $GLOBALS['wp_scripts']. Registered scripts might change per requested page. 

Registrations are conditionally loaded and stored in memory, based on site section (admin, login, or front-end). 

Default scripts are not added via wp_register_script., and added to WP_Scripts class via [wp_default_scripts()](https://developer.wordpress.org/reference/functions/wp_default_scripts/) and [wp_default_styles()](https://developer.wordpress.org/reference/functions/wp_default_styles/).

[add_thickbox()](https://developer.wordpress.org/reference/functions/add_thickbox/)
[ThickBox](https://codex.wordpress.org/Javascript_Reference/ThickBox)

= E.g., Thickbox =
one of the points of this plugin.
WordPress makes use of a modified version of the ThickBox jQuery library

= Future Features =
/support/

== Credits ==
Thanks:
This plugin was developed during a fellowship at the [Reynolds Journalism Institute](https://www.rjionline.org).
