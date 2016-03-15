<?php

/*
Plugin Name:       Postscript
Plugin URI:        http://hearingvoices.com/tools/
Plugin URI:        http://hearingvoices.com/tools/
Description:       For data visionaries and multi-mediators. Allows users to enqueue scripts and styles for individual posts. Use <a href="options-general.php?page=postscript">Settings</a> select the registered JS/CSS files to allow, and to permit adding unregistered JS/CSS URLs and <code>body_class()</code>/<code>post_class()</code> classes.
Version:           0.1
Author:            Barrett Golding
Author URI:        http://rjionline.org/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       postscript
Domain Path:       /languages
Plugin Prefix:     postscript

Postscript is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Postscript is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Postscript. If not, see:
http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

/* ------------------------------------------------------------------------ *
 * Plugin init and uninstall
 * ------------------------------------------------------------------------ */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( defined( 'POSTSCRIPT_VERSION' ) ) {
    return;
}

define( 'POSTSCRIPT_VERSION', '0.1' );

/**
 * Adds "Settings" link on plugin page (next to "Activate").
 */
//
function postscript_plugin_settings_link( $links ) {
  $settings_link = '<a href="options-general.php?page=postscript">Settings</a>';
  array_unshift( $links, $settings_link );
  return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'postscript_plugin_settings_link' );

/**
 * Load the plugin text domain for translation.
 *
 * @since    1.0.0
 */
function postscript_load_textdomain() {
    load_plugin_textdomain( 'postscript', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'postscript_load_textdomain' );

/**
 * Sets default settings option upon activation, if options doesn't exist.
 * This action is documented in includes/class-post-scripting-activator.php
 */
function postscript_activate() {

}

/**
 * The code that runs during plugin deactivation.
 */
function postscript_deactivate() {
}

// register_activation_hook( __FILE__, 'postscript_activate' );
// register_deactivation_hook( __FILE__, 'postscript_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
// require plugin_dir_path( __FILE__ ) . 'includes/class-post-scripting.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_postscript() {

	$plugin = new Postscript();
	$plugin->run();

}
// run_postscript();

/* ------------------------------------------------------------------------ *
 * Required Plugin Files
 * ------------------------------------------------------------------------ */
include_once( plugin_dir_path( __FILE__ ) . 'includes/admin-options.php' );
include_once( plugin_dir_path( __FILE__ ) . 'includes/meta-box.php' );
include_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php' );
include_once( plugin_dir_path( __FILE__ ) . 'includes/enqueue-scripts.php' );

/* ------------------------------------------------------------------------ *
 * Required WordPress Files
 * ------------------------------------------------------------------------ */
if ( ! function_exists( 'wp_terms_checklist' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/template.php' );
}

if ( ! function_exists( 'get_editable_roles' ) ) { // Need WP_User class.
    require_once( ABSPATH . 'wp-admin/includes/user.php' );
}

/**
 * Checks if URL exists.
 */
function postscript_url_exists( $url = '' ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    if ( has_filter( 'postscript_url_exists' ) ) {
        $url = apply_filters( 'postscript_url_exists', $url );
    }

    // Sanitize
    $url = esc_url_raw( $url );

    // Get URL header
    $response = wp_remote_head( $url );
    if ( is_wp_error( $response ) ) {
        return 'Error: ' . is_wp_error( $response );
    }

    // Request success, return header response code
    return wp_remote_retrieve_response_code( $response );
}

/**
 * Makes full URL from relative /wp-includes and /wp-admin URLs.
 */
function postscript_core_full_urls( $url ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    return $url;
}

/**
 * Adds new hierarchical, private taxonomies (for Scripts and Styles).
 *
 * Terms can be any registered script/style handle.
 */
function postscript_create_taxonomies() {
    //Settings option for allowed post types.
    $post_types = postscript_get_option( 'post_types' );

    $labels_scripts = array(
        'name'              => _x( 'Scripts', 'taxonomy general name' ),
        'singular_name'     => _x( 'Script', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Scripts' ),
        'all_items'         => __( 'All Scripts' ),
        'parent_item'       => __( 'Parent Script' ),
        'parent_item_colon' => __( 'Parent Script:' ),
        'edit_item'         => __( 'Edit Script' ),
        'update_item'       => __( 'Update Script' ),
        'add_new_item'      => __( 'Add New Script' ),
        'new_item_name'     => __( 'New Scripts Name' ),
        'menu_name'         => __( 'Scripts' ),
    );

    $args_scripts = array(
        'hierarchical'      => true,
        'labels'            => $labels_scripts,
        'public'            => true,
        'query_var'         => true,
        'capabilities' => array(
            'manage_terms' => 'manage_options',
            'edit_terms'   => 'manage_options',
            'delete_terms' => 'manage_options',
            'assign_terms' => 'edit_posts'

        ),
        'rewrite'           => array( 'slug' => 'postscript_scripts' ),
    );

    // Filter params for Styles custom taxonomy.
    if ( has_filter( 'postscript_tax_scripts' ) ) {
        $args_scripts = apply_filters( 'postscript_tax_scripts', $args_scripts );
    }

    register_taxonomy( 'postscript_scripts', $post_types, $args_scripts );

    $labels_styles = array(
        'name'              => _x( 'Styles', 'taxonomy general name' ),
        'singular_name'     => _x( 'Style', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Styles' ),
        'all_items'         => __( 'All Styles' ),
        'parent_item'       => __( 'Parent Style' ),
        'parent_item_colon' => __( 'Parent Style:' ),
        'edit_item'         => __( 'Edit Style' ),
        'update_item'       => __( 'Update Style' ),
        'add_new_item'      => __( 'Add New Style' ),
        'new_item_name'     => __( 'New Style Name' ),
        'menu_name'         => __( 'Styles' ),
    );

    $args_styles = array(
        'hierarchical'      => true,
        'labels'            => $labels_styles,
        'public'            => true,
        'query_var'         => true,
        'capabilities' => array(
            'manage_terms' => 'manage_options',
            'edit_terms'   => 'manage_options',
            'delete_terms' => 'manage_options',
            'assign_terms' => 'edit_posts'

        ),
        'rewrite'           => array( 'slug' => 'postscript_styles' ),
    );

    // Filter params for Styles custom taxonomy.
    if ( has_filter( 'postscript_tax_styles' ) ) {
        $args_styles = apply_filters( 'postscript_tax_styles', $args_styles );
    }

    register_taxonomy( 'postscript_styles', $post_types, $args_styles );
}
add_action( 'init', 'postscript_create_taxonomies', 0 );

/**
 * Allows only registered handles as custom tax terms.
 *
 * @uses  postscript_script_handles() Returns array of registered scripts.
 * @uses  postscript_style_handles() Returns array of registered styles.
 * @return  string $term Submitted taxonomy term (or WP_Error admin-notice).
 */
function postscript_check_tax_term( $term, $taxonomy) {
    if ( $taxonomy == 'postscript_scripts' ) {
        $script_handles = postscript_script_handles();
        if ( in_array( $term, $script_handles ) ) {
            return $term;
        } else {
        return new WP_Error( 'invalid_term', __('The script handle you entered is <strong>not</strong> <a href="https://developer.wordpress.org/reference/functions/wp_register_script/">registered</a>.') );
        }
    }

    if ( $taxonomy == 'postscript_styles' ) {
        $style_handles  = postscript_style_handles();
        if ( in_array( $term, $style_handles ) ) {
            return $term;
        } else {
        return new WP_Error( 'invalid_term', __('The style handle you entered is <strong>not</strong> <a href="https://developer.wordpress.org/reference/functions/wp_register_style/">registered</a>.') );
        }
    }

    return $term;
}
add_filter('pre_insert_term', 'postscript_check_tax_term', 20, 2);
