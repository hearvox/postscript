<?php
/**
 * Load Scripts and Adds Classes to Posts
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Enqueue User-selected Scripts/Styles and Add Classes
 * ------------------------------------------------------------------------ */

/**
 * Enqueues scripts/styles checked in the meta box form.
 *
 * The form's checkboxes are registered handles, stored as custom tax terms.
 *
 * All front-end handles must be registered before this runs,
 * via the same 'wp_enqueue_scripts' action as this function is hooked.
 * So this action fires late by getting a large number as its priority param.
 *
 */
function postscript_enqueue_handles() {
    if ( is_singular() && is_main_query() ) { // Run only on front-end post.

        // Custom tax term is the script/style handle.
        $scripts = get_the_terms( get_the_ID(), 'postscript_scripts' );
        $styles  = get_the_terms( get_the_ID(), 'postscript_styles' );

        // If custom tax terms, check for registered handle, then enqueue.
        if ( $scripts ) {
            foreach ( $scripts as $script ) {
                    $handle = sanitize_key( $script->name );
                    wp_enqueue_script( sanitize_key( $script->name ) );
            }
        }

        if ( $styles ) {
            foreach ( $styles as $style ) {
                    wp_enqueue_style( sanitize_key( $style->name ) );
            }
        }

    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_handles', 100000 );

/**
 * Enqueues script and style URLs entered in the meta box text fields.
 *
 * URLs load after registered files above (larger action priority param).
 *
 * get_post_meta( $post_id, 'postscript_meta', true ) returns:
 * Array
 * (
 *     [url_style] => http://example.com/my-post-style.css
 *     [url_script] => http://example.com/my-post-script.js
 *     [url_script_2] => http://example.com/my-post-script-2.js
 *     [class_body] => my-post-body-class
 *     [class_post] => my-post-class
 * )
 *
 *
 */
function postscript_enqueue_script_urls() {
    if ( is_singular() && is_main_query() ) {
        $post_id = get_the_id();
        $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );

        // Style/script handles made from string: "postscript-style-{$post_id}".
        if ( isset( $postscript_meta['url_style'] ) ) {
            wp_enqueue_style( "postscript-style-$post_id", $postscript_meta['url_style'], array() );
        }

        if ( isset( $postscript_meta['url_script'] ) ) {
            wp_enqueue_script( "postscript-script-$post_id", $postscript_meta['url_script'], array(), false, true );
        }

        if ( isset( $postscript_meta['url_script_2'] ) ) {
            // Load second JS last (via dependency param).
            $dep = ( isset( $postscript_meta['url_script_2'] ) ) ? "postscript-script-$post_id" : '';
            wp_enqueue_script( "postscript-script-2-$post_id", $postscript_meta['url_script_2'], array( $dep ), false, true );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_script_urls', 100010 );

/* Filter the post class hook with our custom post class function. */
function postscript_class_post( $classes ) {

    $post_id = get_the_ID();

    if ( ! empty( $post_id ) ) {

    /* Get the custom post class. */
    $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );

    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( ! empty( $postscript_meta['class_post'] ) )
        $classes[] = sanitize_html_class( $postscript_meta['class_post'] );
    }

    return $classes;
}
add_filter( 'post_class', 'postscript_class_post' );

/* Filter the post class hook with our custom post class function. */
function postscript_class_body( $classes ) {

    $post_id = get_the_ID();

    if ( ! empty( $post_id ) ) {

    /* Get the custom post class. */
    $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );

    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( ! empty( $postscript_meta['class_body'] ) )
        $classes[] = sanitize_html_class( $postscript_meta['class_body'] );
    }

    return $classes;
}
add_filter( 'body_class', 'postscript_class_body' );
