# Postscript #
**Contributors:** [hearvox](https://profiles.wordpress.org/hearvox)
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T4YWRA5FZC5PC
**Tags:** script, javascript, style, styles, stylesheet, css, class, enqueue
**Author URI:** http://hearingvoices.com/tools/postscript/
**Plugin URI:** http://hearingvoices.com/
**Requires at least:** 3.5
**Tested up to:** 4.9.2
**Stable tag:** 0.4.7
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

Data visionaries and multi-mediators, make any post sing with scripts and splendid with styles, all from within WordPress.

## Description ##

No more shoehorning stylesheets and JavaScripts into the post content box. No more loading scripts on every post that only a few use. Postscript lets you easily add libraries or single-post script and style files post-by-post.

The plugin uses the WordPress "enqueue" methods, which means you can control dependencies (when registering scripts), improve site performance by putting styles in the head and scripts in the footer, and eliminate loading multiple copies of the same library (jQuery, I'm looking at you).

### Enqueue Registered Styles and Scripts (by Handle) ###

Use the Postscript meta box (Edit Post screen) to enqueue registered styles and scripts (listed by handle in checkboxes).

### Enqueue Unregistered Styles, Scripts, and Data Files (by URL) ###

For each post, you can also enqueue unregistered files, by entering URLs in the meta box text fields for:
* 1 CSS stylesheet.
* 2 JavaScript URLs, e.g, one JSON data file and one script file.

### Add Post and Body Classes ###

And for each post, you can add:
* A classname, to the HTML body tag (requires `body_class()` in theme).
* A classname, to `class="post"` list (inserted by WordPress, requires `post_class()` in theme).

### Settings and Security ###

The Settings screen lets you control which user-roles and post-types display the Postscript meta box and which script/style handles users are allowed to enqueue.

## Installation ##

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Setting: Postscript options screen.

## Frequently Asked Questions ##

### How do add registered script/style handles to the Postscript meta box? ###
The Settings &gt; Postscript screen lists all available handles, those registered via the [`wp_enqueue_scripts` hook])https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/} in your active theme and plugins and the defaults registered by WordPress itself.

You can add any registered script or stylesheet handle to the checkboxes in the Postscript meta box. The [GitHub Dev Notes](https://github.com/hearvox/postscript#dev-notes) details on the inner workings of this plugin, including custom fields and taxonomies, transients, options, and filters.

### How do I register scripts? ###
**Your Scripts and Styles:** You can register your own CSS/JS file *handles* with the [wp_register_script()](https://developer.wordpress.org/reference/functions/wp_register_script/) and [wp_register_style()](https://developer.wordpress.org/reference/functions/wp_register_style/) functions.

**Default Scripts and Styles:** WordPress auto-registers numerous styles and scripts via its core functions: [wp_default_scripts()](https://developer.wordpress.org/reference/functions/wp_default_scripts/) and [wp_default_styles()](https://developer.wordpress.org/reference/functions/wp_default_styles/). Each file gets its own unique handle: see the [list of defaults](https://developer.wordpress.org/reference/functions/wp_enqueue_script/#defaults).

### What is a use case for this plugin? ###
Adding Thickbox to a post is an example of what this plugin does. WordPress ships with a modified [ThickBox jQuery library](https://codex.wordpress.org/Javascript_Reference/ThickBox), used to make modal lightbox windows. The [add_thickbox()](https://developer.wordpress.org/reference/functions/add_thickbox/) function enables this feature. When enabled, though, Thickbox's CSS and JS files load on every Post, whether the post needs it or not.

This plugin improves site performance by enqueuing scripts only when specifically requested for an individual post, via the **Postscript** meta box. See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

### How can I contribute to Postscript? ###

Postscript is now on [GitHub](https://github.com/hearvox/postscript). Pull Requests welcome.

### How can I translate Postscript? ###
This plugin is internationalized (default: English). Please contribute a translation in your language.

The WordPress.org Polyglots Team maintains a comprehensive [Translator’s Handbook](https://make.wordpress.org/polyglots/handbook/). All text strings in this plugin are localized, following the guidelines of the Wordpress.org Plugin Handbook's [Internationalization section](https://developer.wordpress.org/plugins/internationalization/).

### Credits ###
This plugin was developed as part of a [Reynolds Journalism Institute](https://www.rjionline.org) fellowship.

## Screenshots ##

1. Edit Post screen **Postscript** meta box
2. Settings Page: User Roles, Post Types, URls, and Classes
3. Settings Page: Tables of Added Scripts and Styles
4. Settings Page: Remove Scripts and Styles

## Changelog ##

### 1.0.0 ###
* Fix taxonomy term deletion when no terms checked.
* Remove Postscript taxonomies from Dashboard menu links.
* Remove Postscript taxonomies from Dashboard: Appearance: Menu checkboxes.
* Remove Postscript taxonomies from Quick Edit checkboxes.
* Remove Yoast SEO plugin "Make Primary" button on Postscript taxonomies.

### 0.4.7 ###
* Beta version in WordPress Directory.
* Add whitelists for hostnames and extensions of unregistered URLs.
* Change custom taxonomy slugs to 'postscripts' and poststyles'.Fix
* Test upgrade option function based on version number.
