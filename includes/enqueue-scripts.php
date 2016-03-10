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
 * Enqueues script and style URLs entered in the meta box text fields.
 *
 * get_post_meta( $post_id, 'postscript_meta', true ) returns:
 * Array
 * (
 *     [user_roles] => Array
 *         (
 *             [0] => {role_key}
 *             [1] => {role_key}
 *         )
 *
 *     [post_types] => Array
 *         (
 *             [0] => {post_type_key}
 *             [1] => {post_type_key}
 *         )
 *
 *     [allow] => Array
 *         (
 *             [url_style]  => on
 *             [url_script] => on
 *             [url_data]   => on
 *             [class_body] => on
 *             [class_post] => on
 *         )
 *
 *     [style_add]     => {style_handle}
 *     [script_add]    => {script_handle}
 *     [style_remove]  => {style_handle}
 *     [script_remove] => {script_handle}
 *     [version]       => 1.0.0
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

/**
 * Enqueues scripts/styles checked in the meta box form (handles are custom tax terms).
 *
 * To get all registered script/styles handles registered for the front-end
 * this must after all other'wp_enqueue_scripts' hooks. So the action below
 * that calls this function fires last (high number = low priority).
 *
 */
function postscript_enqueue_handles() {
    if ( is_singular() && is_main_query() ) {

        // Set transients with arrays of registered scripts/styles.
        // postscript_get_wp_scripts_transient();

        // Custom tax term is the script/style handle.
        $scripts = get_the_terms( get_the_ID(), 'postscript_scripts' );
        $styles  = get_the_terms( get_the_ID(), 'postscript_styles' );

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

    return;
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_handles', 100000 );

/**
 * Sets transient with arrays of front-end registered scripts/styles.
 *
 * Must be run on front-end to pick up 'wp_enqueue_scripts' firings.
 * (In back-end $wp_scripts holds only 'admin_enqueue_scripts' registers.)
 * This must after all other'wp_enqueue_scripts' hooks. So the action below
 * that calls this function fires last (high number = low priority).
 *
 */
function postscript_set_wp_scripts_transient() {
    global $wp_scripts, $wp_styles;
    set_transient( 'postscript_wp_scripts', $wp_scripts->registered, 60 * 60 * 4 );
    set_transient( 'postscript_wp_styles', $wp_styles->registered, 60 * 60 * 4 );
}
add_action( 'wp_enqueue_scripts', 'postscript_enqueue_handles', 100001 );

/**
 * Gets transient with arrays of front-end registered scripts or styles.
 *
 * If transient doesn't exist, load a post (to fire front-end hooks)
 * then sets transient.
 *
 */
function postscript_get_wp_scripts_transient( $file_type = 'postscript_wp_scripts' ) {
    // If transient not set, run a post to trigger front-end hooks and globals.
    $scripts = get_transient( 'postscript_wp_scripts' );
    $styles  = get_transient( 'postscript_wp_styles' );

    if ( is_array( $scripts ) && is_array( $styles ) ) {
        $transient = get_transient( $file_type );
    } else {
        postscript_load_latest_post();
        $transient = get_transient( $file_type );
    }

    return $transient;
}

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
