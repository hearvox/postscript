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
 * Enqueue scripts and styles selected in the meta box form.
 *
 *
 */
function postscript_enqueue_scripts() {
    if ( is_singular() && is_main_query() ) {
        global $post;
        $post_id = $post->ID;
        $postscript_style_url = get_post_meta( $post_id, 'postscript_style_url', true );
        $postscript_script_url = get_post_meta( $post_id, 'postscript_script_url', true );
        // $postscript_style_handles = get_post_meta( $post_id, 'postscript_style_handles', true );
        // $postscript_script_handles = get_post_meta( $post_id, 'postscript_script_handles', true );

        if ( has_filter( 'postscript_post_style' ) ) {
            $style_handles = apply_filters( 'postscript_post_style', $style_handles );
        }

        if ( ! empty( $postscript_post_style ) ) {
            wp_enqueue_style( 'postscript-style-' . $post_id, $postscript_post_style, false );
        }

        if ( has_filter( 'postscript_script_url' ) ) {
            $postscript_script_url = apply_filters( 'postscript_script_url', $postscript_script_url );
        }

        if ( ! empty( $postscript_script_url ) ) {
            wp_enqueue_script( 'postscript-script-url' . $post_id, $postscript_script_url );
        }
/*
        if ( has_filter( 'postscript_style_handles' ) ) {
            $postscript_style_handles = apply_filters( 'postscript_style_handles', $postscript_style_handles );
        }

        foreach ( $postscript_style_handles as $handle ) {
            wp_enqueue_style( $handle );
        }

        if ( has_filter( 'postscript_script_handles' ) ) {
            $url = apply_filters( 'postscript_script_handles', $postscript_script_handles );
        }

        foreach ( $postscript_script_handles as $handle ) {
            wp_enqueue_script( $handle );
        }
*/
    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_scripts' );

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
