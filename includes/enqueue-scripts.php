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

/*******************************
 =REGISTER SCRIPTS
 ******************************/

function headecon_register_scripts() {

    // $file_he_common_js = WP_PLUGIN_DIR . '/he-interactives/js/he-inter-common.js';
    $file_he_common_js = 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/he-inter-common.js';

    if ( file_exists( $file_he_common_js ) ) {
        wp_register_script( 'he-common', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/he-inter-common.js', 'jquery', filemtime( $file_eps_js ), true );
    }

    wp_register_script( 'he-tableau', 'http://headwaterseconomics.org:8000/javascripts/api/viz_v1.js', array( '' ), '1', true );
    wp_register_script( 'he-d3', 'http://dev.headwaterseconomics.org//wphw/wp-content/plugins/he-interactives/js/d3/d3.min.js', array( 'jquery' ), '3.4.2', true );
    wp_register_script( 'he-open-layers-2', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/openlayers/OpenLayers.js', array( 'jquery' ), '2.0', true );
    wp_register_script( 'he-open-layers-3', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/openlayers-3.13.0/ol.js', array( 'jquery' ), '3.13.0', true );
    wp_register_script( 'he-spin', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/spin/spin.min.js', array( 'jquery' ), '1', true );
    wp_register_script( 'he-proj4js', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/proj4js/lib/proj4js-combined.js', array( '' ), '1.1.0', true );
    wp_register_script( 'he-queue', 'http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/js/queue/queue.v1.min.js', array( '' ), '1', true );

    wp_register_style( 'he-font-opensans', 'http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,300,600,700,800' );
    wp_register_style( 'he-font-vollkorn', 'http://fonts.googleapis.com/css?family=Vollkorn:400italic,700italic,400,700' );
    wp_register_style( 'he-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );

    // wp_enqueue_style( 'he-font-opensans' );
}
add_action( 'wp_enqueue_scripts', 'headecon_register_scripts' );

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
                    wp_enqueue_script( $script->name );
                }
            }
        }

        if ( $styles ) {
            foreach ( $styles as $style ) {
                if ( wp_style_is( $style->name, 'registered' ) ) {
                    wp_enqueue_style( $style->name );
                }
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_script_handles', 10000 );


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
