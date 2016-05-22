=== Postscript ===
Contributors: hearvox
Donate link: http://hearingvoices.com/tools
Tags: script, javascript, style, styles, stylesheet, css, class
Requires at least: 3.3
Tested up to: 4.4
Stable tag: 0.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

For Data-visionaries and Multi-mediators: Make posts sing with scripts and styles, all from within WordPress.

== Description ==

No more shoehorning stylesheets and JavaScripts into the post content box. No more custom code for each post. No more loading scripts on every page you just need for a few. Postscript lets you easily add libraries or single-post script and style files post-by-post.

The plugin uses the WordPress "enqueue" methods, which means you can control dependencies (when registering scripts), improve site performance by putting styles in the head and scripts in the footer, and eliminate loading multiple copies of the same library (jQuery, I'm looking at you).

= Enqueue Registered Styles and Scripts (by Handle) =

Use the Postscript meta box (Edit Post screen) to enqueue registered of styles and scripts (listed in checkboxes by handle.).

= Enqueue Unregistered Styles, Scripts, and Data Files (by URL) =

For each post, you can also enqueue unregistered files, by entering in the meta boxes text fields for:
* A stylesheet URL.
* Two JavaScript URLs, e.g, one JSON data file and one script file.

= Add Post and Body Classes =

And for each post, you can add:
* Body class(es), to the HTML `<body>` tag (requires `body_class()` in theme).
* Post class(es), to `class="post"` list (inserted by WordPress, requires `post_class()` in theme).

== Installation ==

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Setting > Postscript options screen to select allowed scripts and styles.

== Frequently Asked Questions ==

= What might be some future features? =

Tell us in the [support fourm](https://wordpress.org/support/plugin/postscript) about new features you'd like in future releases. For instance:
* Pass parameters to registered scripts (via [`wp_localize_script()`](https://developer.wordpress.org/reference/functions/wp_localize_script/)).
* List all post's enqueues in the Edit Post screen.
* Live preview of Draft posts in the Customizer, with its new phone and tablet views.
* Add custom <style> in the document <head> (via [`wp_add_inline_style()`](https://developer.wordpress.org/reference/functions/wp_add_inline_style/)).
* Add custom <script> in the document <head> (`wp_add_inline_script()` coming in WordPress 4.5).
* Add Page Templete dropdown to Posts (and CPTs).
* Add file-modification timestamp as script/style's version number (as cache buster).
* AJAX check for file-exists for user-entered URLs.
* Export/import settings, taxonomy terms, and post meta.
* In Settings page make separate lists for default and plugin/theme-registrations.
* Add filter for...?

= How does this work? =
The [GitHub Dev Notes](https://github.com/hearvox/postscript#dev-notes) details on the inner workings of this plugin, including custom fields and taxonomies, transients, options, and filters.

== Screenshots ==

1. Edit Post screen **Postscript** meta box
2. Settings Page: User Roles, Post Types, URls, and Classes
3. Settings Page: Tables of Added Scripts and Styles
4. Settings Page: Remove Scripts and Styles

== Changelog ==
= 0.4.0 =
* Change custom taxonomy slugs to 'postscripts' and poststyles'.

= 0.3.0 =
* Beta version.
* Test upgrade option function based on version number.

= 0.1.0 =
* Initial test version.

== Upgrade Notice ==

= 0.1.0 =
Alpha version.

== Translations ==

* English - default, always included

*Note:* This plugin is built to be easily translatable. Please contribute a translation in your language.

The WordPress.org Polyglots Team maintains a comprehensive [Translator’s Handbook](https://make.wordpress.org/polyglots/handbook/). All text strings in this plugin are localized, following the guidelines of the Wordpress.org Plugin Handbook's [Internationalization section](https://developer.wordpress.org/plugins/internationalization/).

(The additional sections display in "Other Notes" /other_notes/.)

== Tech Notes ==

= Your Scripts and Styles =
You can register your own CSS/JS file *handles* with the [wp_register_script()](https://developer.wordpress.org/reference/functions/wp_register_script/) and the [wp_register_style()](https://developer.wordpress.org/reference/functions/wp_register_style/) functions.

Only handles you register via the [`wp_enqueue_scripts` hook])https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/} list on Postscript's Settings screen. This list also has handles registered by your active theme and plugins and the defaults registered by WordPress itself.

= Default Scripts and Styles =
WordPress registers numerous styles and scripts via its core functions: [wp_default_scripts()](https://developer.wordpress.org/reference/functions/wp_default_scripts/) and [wp_default_styles()](https://developer.wordpress.org/reference/functions/wp_default_styles/). Each file gets its own unique handle: see the [list of defaults](https://developer.wordpress.org/reference/functions/wp_enqueue_script/#defaults).

= An Example: Thickbox =
WordPress ships with a modified [ThickBox jQuery library](https://codex.wordpress.org/Javascript_Reference/ThickBox), used to make modal lightbox windows. The [add_thickbox()](https://developer.wordpress.org/reference/functions/add_thickbox/) function enables this feature, but it also loads Thickbox's CSS and JS files on every Post, whether or not the post needs it.

This plugin improves site performance by only enqueuing scripts only when speficially requested for an individual post, by checking the Thickbox Script and Styles handles in the **Postscript** box. See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

= Contribute =
Postscript is now on [GitHub](https://github.com/hearvox/postscript). Pull Requests welcome.

== Credits ==
Thanks:
This plugin was developed as part of a [Reynolds Journalism Institute](https://www.rjionline.org) fellowship.
