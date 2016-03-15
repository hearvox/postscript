<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       http://example.com
 * @since 0.1
 *
 * @package    Postscript
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Removes plugin's data.
 *
 * @since 0.1
 */
if ( function_exists( 'delete_post_meta_by_key' ) ) {
    $meta_key = 'postscript_meta';
    postscript_remove_meta( $meta_key );
}

if ( function_exists( 'wp_delete_term' ) ) {
    $tax_styles  = 'postscript_styles';
    $tax_scripts = 'postscript_scripts';
    postscript_remove_tax_terms( $tax_styles );
    postscript_remove_tax_terms( $tax_scripts );
}

if ( function_exists( 'delete_option' ) ) {
    $option = 'postscript';
    postscript_remove_option( $option );
}

/**
 * Removes plugin's post meta from database.
 *
 * @since 0.1
 */
function postscript_remove_meta( $meta_key ) {
    delete_post_meta_by_key ( $meta_key );
}

/**
 * Removes plugin's custom taxonomy terms from database.
 *
 * @since 0.1
 */
function postscript_remove_tax_terms( $tax ) {
    $terms = get_terms( $tax );

    foreach ( $terms as $term ) {
        wp_delete_term( $term->term_id, $tax );
    }

    if ( function_exists( 'taxonomy_exists' ) ) {
        postscript_unregister_tax( $tax );
    }
}

/**
 * Unregisters plugin's custom taxonomy.
 *
 * @since 0.1
 */
function postscript_unregister_tax( $tax ) {
    global $wp_taxonomies;
    if ( taxonomy_exists( $tax ) ) {
        unset( $wp_taxonomies[ $tax ] );
    }

    return;
}

/**
 * Removes plugin's option from database.
 *
 * @since 0.1
 */
function postscript_remove_option( $option ) {
    delete_option( $option_name );
}
