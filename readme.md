# Postscript #
**Contributors:** hearvox
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T4YWRA5FZC5PC
**Tags:** script, javascript, style, styles, stylesheet, css, class, enqueue
**Author URI:** http://hearingvoices.com/tools/postscript/
**Plugin URI:** http://hearingvoices.com/
**Requires at least:** 3.5
**Tested up to:** 4.9.2
**Stable tag:** 1.0.0
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

Data visionaries and multi-mediators, make any post sing with scripts and splendid with styles, all from within WordPress.

## Description ##

No more shoehorning stylesheets and JavaScripts into the post content box. No more loading scripts on every post that only a few use. Postscript lets you easily add libraries or single-post script and style files post-by-post.

The plugin uses the WordPress "enqueue" methods, which means you can control dependencies (when registering scripts), improve site performance by putting styles in the head and scripts in the footer, and eliminate loading multiple copies of the same library (jQuery, I'm looking at you).

### Enqueue registered styles and scripts (by handle) ###

Use the Postscript meta box (Edit Post screen) to enqueue registered of styles and scripts (listed in checkboxes by handle.).

### Enqueue unregistered styles, scripts, and data files (by URL) ###

For each post, you can also enqueue unregistered files, by entering in the meta boxes text fields for:
* A stylesheet URL.
* Two JavaScript URLs, e.g, one JSON data file and one script file.

### Add post and body classes ###

And for each post, you can add:
* Body class(es), to the HTML `&lt;body&lt;` tag (requires `body_class()` in theme).
* Post class(es), to `class="post"` list (inserted by WordPress, requires `post_class()` in theme).

See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

## Installation ##

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Setting: Postscript options screen.

### Credits ###

Thanks:This plugin was developed as part of a [Reynolds Journalism Institute](https://www.rjionline.org) fellowship.

## Frequently asked questions ##

### What might be some future features? ###

Tell us in the [support fourm](https://wordpress.org/support/plugin/postscript) about new features you'd like in future releases. For instance:

* Pass parameters to registered scripts (via [`wp_localize_script()`](https://developer.wordpress.org/reference/functions/wp_localize_script/)).
* List all post's enqueues in the Edit Post screen.
* Live preview of Draft posts in the Customizer, with its new phone and tablet views.
* Add custom &lt;style&gt; in the document &lt;head&gt; (via [`wp_add_inline_style()`](https://developer.wordpress.org/reference/functions/wp_add_inline_style/)).
* Add custom &lt;script&gt; in the document &lt;head&gt; (`wp_add_inline_script()` coming in WordPress 4.5).
* Add Page Templete dropdown to Posts (and CPTs).
* Add file-modification timestamp as script/style's version number (as cache buster).
* AJAX check for file-exists for user-entered URLs.
* Export/import settings, taxonomy terms, and post meta.
* In Settings page make separate lists for default and plugin/theme-registrations.
* Add filter for...?

## Screenshots ##

1. Edit Post screen **Postscript** meta box
2. Settings Page: User Roles, Post Types, URls, and Classes
3. Settings Page: Tables of Added Scripts and Styles
4. Settings Page: Remove Scripts and Styles

## Changelog ##

### 1.0.0
* Fix taxonomy term removal when posyt has only one term.
* Remove Postscript taxonomies from Dashboard menu links.
* Remove Postscript taxonomies from Dashboard: Appearance: Menu checkboxes.
* Remove Postscript taxonomies from Quick Edit checkboxes.
* Prevent Yoast SEO plugin "Make Primary" button display for Postscript taxonomies.

### 0.4.7
* Beta version in WordPress Directory.
* Add whitelists for hostnames and extensions of unregistered URLs.
* Change custom taxonomy slugs to 'postscripts' and poststyles'.Fix
* Test upgrade option function based on version number.

## Registrating scripts/styles in WordPress

### Your scripts and styles
You can register your own CSS/JS file *handles* with the [wp_register_script()](https://developer.wordpress.org/reference/functions/wp_register_script/) and the [wp_register_style()](https://developer.wordpress.org/reference/functions/wp_register_style/) functions.

Only handles you register via the [`wp_enqueue_scripts` hook](https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/) list on Postscript's Settings screen. This list also has handles registered by your active theme and plugins and the defaults registered by WordPress itself.

### Default scripts and styles
WordPress registers numerous styles and scripts via its core functions: [wp_default_scripts()](https://developer.wordpress.org/reference/functions/wp_default_scripts/) and [wp_default_styles()](https://developer.wordpress.org/reference/functions/wp_default_styles/). Each file gets its own unique handle: see the [list of defaults](https://developer.wordpress.org/reference/functions/wp_enqueue_script/#defaults).

### An example: Thickbox
WordPress ships with a modified [ThickBox jQuery library](https://codex.wordpress.org/Javascript_Reference/ThickBox), used to make modal lightbox windows. The [add_thickbox()](https://developer.wordpress.org/reference/functions/add_thickbox/) function enables this feature, but it also loads Thickbox's CSS and JS files on every Post, whether or not the post needs it.

This plugin improves site performance by enqueuing scripts only when specifically requested for an individual post, by checking the Thickbox Script and Styles handles in the **Postscript** box. See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

## Dev Notes

### Site-option saves meta box settings
Admin settings use the WordPress Settings API. Settings page and "Help" tab functions are in `/includes/admin-options.php`.

**Choose post-types and User-roles:** Admins (`manage-options`) have checkboxes to choose which user-roles and post-types display the Postscript meta box in their *Edit Post* screens. Choices are pulled from `get_editable_roles()` and `get_post_types( 'public' => true )`. Post-types are passed to `add_meta_box()` and `register_taxonomy()`.

Defaults: user-role "Administrator" and post-type "Post".

**Permit URLs and classes:** Admins use checkboxes to allow text fields in the meta box for entering:
* 1 CSS stylesheet URL to enqueue.
* 1–2 JavaScript URL(s) to enqueue.
* A class name for `body_class()`.
* A class name for `post_class()`.

Defaults allow: 1 CSS and 1 JS URL, post and body classes.

**Option functions:** A single site-option, named `'postscript', stores the settings above in arrays. Custom functions get, set, add defaults to, and upgrade this option (`/includes/functions.php`).

**Select registered script/style handles:** Admins use select-menus on the settings pages to add or remove registered handles from the meta-box. A table displays each selected handle's dependencies, footer-setting, post-count, and status response code of its URL.

The select-menu gets the handles from transients which store front-end registrations.

### Transients stores site-wide registered scripts/styles
The `wp_enqueue_scripts` hook registers front-end scripts/styles. The `$wp_scripts` and `$wp_styles` variables store this registration data in memory.

So this plugin needs to access front-end memory from the back-end. To do that a function fires the hook -- `do_action( 'wp_enqueue_scripts' )` -- then stores the variables as transients, `'postscript_scripts_reg'` and `'postscript_styles_reg'`.

This function hooks on `shutdown` (earlier hooks affect the admin display). The transients contain all the WordPress defaults, registered via `wp_default_scripts()` and `wp_default_styles()`, but do not have registrations via the `admin_enqueue_scripts` or `login_enqueue_scripts` hooks. Transient functions are in: `/includes/functions.php`.

### Custom taxonomies stores admin-allowed scripts/styles
Two custom taxonomies, `'postscripts'` and `'poststyles'`, store admin-selected handles. As a sanity check, a function, hooked to `'pre_insert_term'`, adds new terms only if they match a front-end registered handle (in one the above transients).

On the settings screen, WordPress taxonomy display a term's post-count linked to a list all posts that use a particular term/handle. The term posts-list screen displays all allowed post types, by applying the site-option's `'post_types'` array to the `'pre_get_posts'` hook. Taxonomy functions are in the main plugin file: `postscript.php`.

### Post-meta adds per-post scripts, styles, and classes
The Postscript meta box displays admin-allowed handles as checkboxes using `wp_terms_checklist()`. These are the scripts/styles that can be enqueued. Checked boxes are saved as the post's taxonomy terms. (The default taxonomy meta boxes do not display, so the Edit Post form would doesn't have two checkbox sets for the same taxonomy.)

The meta box also has text fields for script/style URLs to be enqueued and for body/post classes. URLs and class names get saved as post-meta as an array in a single custom field, named `'postscript_meta'`. Meta box and post-meta functions are in: `/includes/meta-box.php`.

### Hooks enqueue post's scripts/styles and add classes
Functions hooked to `wp_enqueue_scripts` enqueue a post's handles (custom taxonomy terms) and URLs (from post-meta), after `sanitize_key()` and `esc_url_raw()` sanity checks. Functions hooked to `body_class` and `post_class` add any class names (from post-meta), after a `sanitize_html_class()` sanity check.

WordPress also auto-adds taxonomy terms to `post_class()`, e.g., with class names `.postscripts-thickbox` and  `.postscripts-thickbox`.

Enqueue and CSS-class functions are in: `/includes/enqueue-scripts.php`.

### Contribute
Postscript is now on [GitHub](https://github.com/hearvox/postscript). Pull Requests welcome.
