# Postscript

Data visionaries and multi-mediators, make any post sing with scripts and splendid with styles, all from within WordPress.

## Description

No more shoehorning stylesheets and JavaScripts into the post content box. No more loading scripts on every post that only a few  use. Postscript lets you easily add libraries or single-post script and style files post-by-post.

The plugin uses the WordPress "enqueue" methods, which means you can control dependencies (when registering scripts), improve site performance by putting styles in the head and scripts in the footer, and eliminate loading multiple copies of the same library (jQuery, I'm looking at you).

### Enqueue registered styles and scripts (by handle)

Use the Postscript meta box (Edit Post screen) to enqueue registered of styles and scripts (listed in checkboxes by handle.).

### Enqueue unregistered styles, scripts, and data files (by URL)

For each post, you can also enqueue unregistered files, by entering in the meta boxes text fields for:
* A stylesheet URL.
* Two JavaScript URLs, e.g, one JSON data file and one script file.

### Add post and body classes

And for each post, you can add:
* Body class(es), to the HTML `&lt;body&lt;` tag (requires `body_class()` in theme).
* Post class(es), to `class="post"` list (inserted by WordPress, requires `post_class()` in theme).

See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

## Installation

To install the use the Postscript plugin:

1. Upload the `postscript` directory and content to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Setting: Postscript options screen.

### Credits

Thanks:This plugin was developed as part of a [Reynolds Journalism Institute](https://www.rjionline.org) fellowship.

## Frequently asked questions

### What might be some future features?

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

## Screenshots

1. Edit Post screen **Postscript** meta box
2. Settings Page: User Roles, Post Types, URls, and Classes
3. Settings Page: Tables of Added Scripts and Styles
4. Settings Page: Remove Scripts and Styles

## Changelog

### 0.3.1
* Beta version.
* Test upgrade option function based on version number.

### 0.1.0
* Alpha version.

## Upgrade Notice

### 0.1.0
* Alpha version.

## Registration scripts/styles in WordPress

### Your scripts and styles
You can register your own CSS/JS file *handles* with the [wp_register_script()](https://developer.wordpress.org/reference/functions/wp_register_script/) and the [wp_register_style()](https://developer.wordpress.org/reference/functions/wp_register_style/) functions.

Only handles you register via the [`wp_enqueue_scripts` hook](https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/) list on Postscript's Settings screen. This list also has handles registered by your active theme and plugins and the defaults registered by WordPress itself.

### Default scripts and styles
WordPress registers numerous styles and scripts via its core functions: [wp_default_scripts()](https://developer.wordpress.org/reference/functions/wp_default_scripts/) and [wp_default_styles()](https://developer.wordpress.org/reference/functions/wp_default_styles/). Each file gets its own unique handle: see the [list of defaults](https://developer.wordpress.org/reference/functions/wp_enqueue_script/#defaults).

### An example: Thickbox
WordPress ships with a modified [ThickBox jQuery library](https://codex.wordpress.org/Javascript_Reference/ThickBox), used to make modal lightbox windows. The [add_thickbox()](https://developer.wordpress.org/reference/functions/add_thickbox/) function enables this feature, but it also loads Thickbox's CSS and JS files on every Post, whether or not the post needs it.

This plugin improves site performance by only enqueuing scripts only when speficially requested for an individual post, by checking the Thickbox Script and Styles handles in the **Postscript** box. See [the screenshots](https://wordpress.org/plugins/postscript/screenshots/).

## Tech Notes: Settings

Admin settings are in `/includes/admin-options.php` and use the WordPress Settings API.

### Choose post-types and User-roles
Admins (`manage-options`) use checkboxes to choose which user-roles and post-types and display the Postscript meta box on their *Edit Post* screens. Choices are pulled from `get_editable_roles()` and `get_post_types( 'public' => true )`.

Defaults: user-role "Administrator" and post-type "Post". (Post-types are used by `add_meta_box()` and `register_taxonomy()`.)

### Permit URLs and classes
Admins use checkboxes to allow text fields in the meta box for entering:
* An URL to enqueue 1 stylesheet.
* URLs to enqueue 1–2 JavaScript files.
* A class name for `body_class()`.
* A class name for `post_class()`.

Defaults: stylesheet, JavaScript (1), post and body classes allowed.

### Options
Selected post-types, user-roles, allowed URLs and class are saved as arrays in a single site-option, named `'postscript'. Custom functions (`/includes/functions.php`) get, set, upgrade, and create defaults for this option.

## Select allowable registered script/style handles


## Transients: Store registered scripts/styles

Front-end memory.

## Custom taxonomies: Store selected handles
pre_insert_term
post_class() class="...postscripts-thickbox poststyles-thickbox"


## Post-meta: Save URLs and classes
URLs esc_url_raw()


## Enqueue: Load selected handles and URLs


### Classes

### Contribute
Postscript is now on [GitHub](https://github.com/hearvox/postscript). Pull Requests welcome.

(``)
(``)
(``)
(``)
(``)
(``)
(``)
(``)
(``)
(``)
(``)
(``)

