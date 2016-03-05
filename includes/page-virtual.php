<?php
// http://rji.local/wp-content/plugins/postscript/includes/page-virtual.php
/*
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
// define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */

// echo ABSPATH;

/** Loads the WordPress Environment and Template */
// require_once( '/srv/www/rji/htdocs/wp-blog-header.php');

// get_header();

// get_footer();

global $wp_styles;

set_transient( 'postscript_wp_styles', 'pg-virt', 60 * 60 * 4 );

/* WordPress Template Hierarchy as of WordPress 4.4
is_404() ---------------------------------------------------------------------------------------------------> 404.php
is_search() ------------------------------------------------------------------------------------------------> search.php
is_front_page() --------------------------------------------------------------------------------------------> front-page.php
is_home() --------------------------------------------------------------------------------------------------> home.php
is_attachment() ---------> {mime_type}.php ------------> attachment.php ----------------\
is_single() -------------> single-{post_type}.php -----> single-{post_type}-{slug}.php --> single.php -----\
is_page() ---------------> page-{slug}.php ------------> page-{id}.php ------------------> page.php --------> singular.php
is_post_type_archive() --> archive-{post_type}.php ------------------------------------------------------\
is_tax() ----------------> taxonomy-{tax}-{slug}.php --> taxonomy-{tax}.php -------------> taxonomy.php --\
is_category() -----------> category-{slug}.php --------> category-{id}.php --------------> category.php ---\
is_tag() ----------------> tag-{slug}.php -------------> tag-{id}.php -------------------> tag.php ---------> archive.php
is_author() -------------> author-{nicename}.php ------> author-{id}.php ----------------> author.php -----/
is_date() ---------------> date.php ----------------------------------------------------------------------/
is_archive() --------------------------------------------------------------------------------------------/

https://gist.github.com/johnbillion/e5b2f106c920276500d4
*/

