<?php
/**
 * Load Scripts and Add Classes to Posts
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Enqueue Scripts and Styles
 * ------------------------------------------------------------------------ */

/**
 * Enqueue scripts and styles checked in the meta box form.
 *
 *
 *
 */
function postscript_enqueue_script_handles() {
    if ( is_singular() && is_main_query() ) {
        $scripts = get_the_terms( get_the_ID(), 'postscript_scripts' );
        $styles = get_the_terms( get_the_ID(), 'postscript_styles' );

        // If custom tax terms, check for registered handle, then enqueue.
        if ( $scripts ) {
            foreach ( $scripts as $script ) {
                if ( wp_script_is( $script->name, 'registered' ) ) {
                    wp_enqueue_style( $script->name );
                }
            }
        }

        if ( $styles ) {
            foreach ( $styles as $style ) {
                if ( wp_style_is( $style->name, 'registered' ) ) {
                    wp_enqueue_script( $style->name );
                }
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_script_handles' );


/**
 * Enqueue script and style URLs entered in the meta box text fields.
 *
 * get_post_meta( $post_id, 'postscript_meta', true )
 * returns:
 * Array
 * (
 *     [url_style]  => http://example.com/my-css-file.css
 *     [url_script] => http://example.com/my-js-file.js
 *     [url_data]  => http://example.com/my-data-file.js
 *     [class_body] => my-body-class
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
            wp_enqueue_style( "postscript-style-$post_id", $postscript_meta['url_style'], false );
        }

        if ( isset( $postscript_meta['url_script'] ) ) {
            wp_enqueue_script( "postscript-script-$post_id", $postscript_meta['url_script'], false, false, true );
        }

        if ( isset( $postscript_meta['url_data'] ) ) {
            wp_enqueue_script( "postscript-data-$post_id", $postscript_meta['url_data'], false, false, true );
        }

/*

        if ( has_filter( 'postscript_script_url' ) ) {
            $postscript_script_url = apply_filters( 'postscript_script_url', $postscript_script_url );
        }

*/
    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_script_urls' );

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
