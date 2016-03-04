<?php
// http://rji.local/wp-content/plugins/postscript/includes/page-virtual.php
define( 'WP_USE_THEMES', true );


/** Loads the WordPress Environment and Template */
// require_once( ABSPATH . 'wp-blog-header.php');

// get_header();

// get_footer();

global $wp_styles;

set_transient( 'postscript_wp_styles', 'pg-virt', 60 * 60 * 4 );

