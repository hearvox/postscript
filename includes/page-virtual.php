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

