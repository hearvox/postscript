<?php

/**
 * General functions for options and registered/selected scripts and styles.
 *
 * @link       http://hearingvoices.com/tools/
 * @since 0.1
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Functions to get/set options array.
 * ------------------------------------------------------------------------ */

/**
 * Retrieves an option, and array of plugin settings, from database.
 *
 * Option functions based on Jetpack Stats:
 * @link https://github.com/Automattic/jetpack/blob/master/modules/stats.php
 *
 * @since 0.1
 *
 * @uses postscript_upgrade_options()
 * @return array $options array of plugin settings
 */
function postscript_get_options() {
    $options = get_option( 'postscript' );

    /* Set version if not the latest. */
    if ( ! isset( $options['version'] ) || $options['version'] < POSTSCRIPT_VERSION ) {
        $options = postscript_upgrade_options( $options );
    }

    return $options;
}

/**
 * Sets an option in database (an array of plugin settings).
 *
 * Note: update_option() adds option if it doesn't exist.
 *
 * @since 0.1
 *
 * @param array $option array of plugin settings
 */
function postscript_set_options( $options ) {
    update_option( 'postscript', $options );
}

/**
 * Makes array of plugin settings, merging default and new values.
 *
 * @since 0.1
 *
 * @uses postscript_set_options()
 * @param array $options array of plugin settings
 * @return array $new_options merged array of plugin settings
 */
function postscript_upgrade_options( $options ) {
    $defaults = array(
        'user_roles' => array( 'administrator' ),
        'post_types' => array( 'post' ),
        'allow'      => array(
            'url_style'    => 'on',
            'url_script'   => 'on',
            'url_script_2' => 'on',
            'class_body'   => 'on',
            'class_post'   => 'on',
        )
    );

    if ( is_array( $options ) && ! empty( $options ) ) {
        $new_options = array_merge( $defaults, $options );
    } else {
        $new_options = $defaults;
    }

    $new_options['version'] = POSTSCRIPT_VERSION;

    postscript_set_options( $new_options );

    return $new_options;
}

/* ------------------------------------------------------------------------ *
 * Functions to get/set a specific options array item.
 * ------------------------------------------------------------------------ */

/**
 * Retrieves a specific setting (an array item) from an option (an array).
 *
 * @since 0.1
 *
 * @uses postscript_get_options()
 * @param array|string $option array item key
 * @return array $options[$option] array item value (or $options[$option][$option_key])
 */
function postscript_get_option( $option_key = NULL ) {
    $options = postscript_get_options();

    // Returns valid inner array key ($options[$option_key]).
    if ( isset( $options ) && $option_key != NULL && isset( $options[ $option_key ] ) ) {
            return $options[ $option_key ];
    } else { // Inner array key not valid.
    return NULL;
    }
}

/**
 * Sets a specified setting (array item) in the option (array of plugin settings).
 *
 * @since 0.1
 *
 * @uses postscript_get_options()
 * @uses postscript_set_options()
 * @param string $option array item key of specified setting
 * @param string $value array item value of specified setting
 * @return array $options array of plugin settings
 */
function postscript_set_option( $option, $value ) {
    $options = postscript_get_options();

    $options[$option] = $value;

    postscript_set_options( $options );
}

/* ------------------------------------------------------------------------ *
 * Functions to set/get transients with front-end script/style arrays.
 * ------------------------------------------------------------------------ */

/**
 * Gets registered scripts and styles.
 *
 * Gets WordPress default scripts/styles, then those plugin/theme registered.
 * The'shutdown' hook fires after wp_default_scripts()/_styles()
 * and after admin had rendered (so enqueued scripts don't affect admin display).
 *
 *
 * @since 0.1
 */
function postscript_get_reg_scripts() {
    /* For future feature to separate defaults from plugin/theme scripts.
    // Arrays with WordPress default back-end scripts.
    $wp_scripts_pre = wp_scripts();
    $wp_styles_pre  = wp_styles();

    // Default scripts array.
    $scripts_pre            = $wp_scripts_pre->registered;
    $postscript_scripts_pre = get_transient( 'postscript_scripts_pre' );

    $styles_pre             = $wp_styles_pre->registered;
    $postscript_styles_pre  = get_transient( 'postscript_styles_pre' );

    // Set transients with defaults scripts.
    if ( $scripts_pre != $postscript_scripts_pre ) {
        set_transient( 'postscript_scripts_pre', $scripts_pre, 60 * 60 * 4 );
    }

    if ( $styles_pre != $postscript_styles_pre ) {
        set_transient( 'postscript_styles_pre', $styles_pre, 60 * 60 * 4 );
    }
    */

    // Hack to get front-end scripts into memory, from here in the back-end
    // (in $wp_scripts, $wp_styles) by firing the front-end registration hook.
    do_action( 'wp_enqueue_scripts' );

    // Arrays now have front-end registered scripts.
    $wp_scripts_reg = wp_scripts();
    $wp_styles_reg  = wp_styles();

    // Default and plugin/theme scripts array.
    $scripts_reg            = $wp_scripts_reg->registered;
    $postscript_scripts_reg = get_transient( 'postscript_scripts_reg' );

    $styles_reg             = $wp_styles_reg->registered;
    $postscript_styles_reg  = get_transient( 'postscript_styles_reg' );

    // Set transients with defaults scripts.
    if ( $scripts_reg != $postscript_scripts_reg ) {
        set_transient( 'postscript_scripts_reg', $scripts_reg, 60 * 60 * 4 );
    }

    if ( $styles_reg != $postscript_styles_reg ) {
        set_transient( 'postscript_styles_reg', $styles_reg, 60 * 60 * 4 );
    }
}
add_action( 'shutdown', 'postscript_get_reg_scripts' );

/**
 * Sets transient with arrays of front-end registered scripts/styles.
 *
 * The 'wp_head' hook fires after 'wp_enqueue_scripts', so all scripts registered.
 *
 */
function postscript_set_wp_scripts_transient() {
    global $wp_scripts, $wp_styles;

    $postscript_wp_scripts = get_transient( 'postscript_wp_scripts' );
    $postscript_wp_styles = get_transient( 'postscript_wp_styles' );

    if ( $wp_scripts != $postscript_wp_scripts ) {
        set_transient( 'postscript_wp_scripts', $wp_scripts->registered, 60 * 60 * 4 );
    }

    if ( $wp_styles != $postscript_wp_styles ) {
        set_transient( 'postscript_wp_styles', $wp_styles->registered, 60 * 60 * 4 );
    }
}
// add_action( 'wp_head', 'postscript_set_wp_scripts_transient' );

/**
 * Gets transient with arrays of front-end registered scripts or styles.
 *
 * If transient doesn't exist, load a post (to fire front-end hooks)
 * then sets transient.
 *
 */
function postscript_check_wp_scripts_transient( $file_type ) {
    // If transient not set, run a post to trigger front-end hooks and globals.
    $scripts = get_transient( 'postscript_wp_scripts' );
    $styles  = get_transient( 'postscript_wp_styles' );

    if ( ! is_array( $scripts ) || ! is_array( $styles ) ) {
        delete_transient( 'postscript_wp_scripts' );
        delete_transient( 'postscript_wp_styles' );
        postscript_load_latest_post();
    }

    $transient = get_transient( $file_type );
    return $transient;
}

/**
 * Gets transient with arrays of front-end registered scripts or styles.
 *
 * @uses postscript_load_latest_post() Loads post
 * @param string $file_type Name of transient
 */
function postscript_get_wp_scripts_transient( $file_type = 'postscript_wp_scripts' ) {
    // Load a post to fire 'wp_head' and all 'wp_enqueue_scripts' hooks.
    postscript_load_latest_post();

    $transient = get_transient( $file_type );

    return $transient;
}

/* ------------------------------------------------------------------------ *
 * Functions for returning arrays of registered script/style handles.
 * ------------------------------------------------------------------------ */

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_script_handles() {
    $postscript_scripts_reg = get_transient( 'postscript_scripts_reg' );

    // Array of registered scripts handles (from $wp_scripts object).
    $scripts_reg = array_values( wp_list_pluck( $postscript_scripts_reg, 'handle' ) );
    sort( $scripts_reg ); // Alphabetize.

    return $scripts_reg;
}

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_style_handles() {
    $postscript_styles_reg = get_transient( 'postscript_styles_reg' );

    // Array of registered scripts handles (from $wp_scripts object).
    $styles_reg = array_values( wp_list_pluck( $postscript_styles_reg, 'handle' ) );
    sort( $styles_reg ); // Alphabetize.

    return $styles_reg;
}


/* ------------------------------------------------------------------------ *
 * Fire front-end hooks by loading a post (for future features).
 * ------------------------------------------------------------------------ */

/**
 * Retrieves the latest post (to set transients with registered scripts/styles) .
 *
 * We need to get all scripts/styles registered on the front-end.
 * To do that we need to fire all the 'wp_enqueue_scripts' hooks.
 * So we get any post, which runs a plugin function, which sets
 * the globals $wp_scripts and $wp_styles globals as transients.
 * See function: postscript_wp_scripts_styles_transient()
 * In plugin file: /includes/enqueue_scripts.php
 *
 * @since 0.1
 *
 * @return  mixed Array of header items; HTML of body content.
 */
function postscript_load_latest_post() {
    $args = array(
        'posts_per_page' => 1,
        'cache_results'  => false,
        'fields'         => 'ids',
        'post_status'    => 'publish',
    );
    $latest_post = new WP_Query( $args );
    $latest_post_id = $latest_post->posts[0];

    $response = postscript_load_post( $latest_post_id );

    return $response;
}

/**
 * Runs post (to fire 'wp_enqueue_scripts' hooks).
 *
 * @since 0.1
 * @param integer $post_id ID of post to fetch
 * @return  mixed Either header array and body HTML or error object is URL not valid
 */
function postscript_load_post( $post_id ) {

    $latest_post_url = get_permalink( $post_id ) ?  : NULL;
    $response = wp_remote_get( $latest_post_url );

    return $response;
}

/**
 * Return only matching array elements..
 */
function postscript_filter_array() {

    global $wp_scripts;
    $script_handles = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $script_reg ) {
        $script_handles[] = $script_reg->handle;
    }

    sort( $script_handles ); // Alphabetize.

    return $script_handles;

}



/* ------------------------------------------------------------------------ *
 * Functions for post meta.
 * ------------------------------------------------------------------------ */


/* ------------------------------------------------------------------------ *
 * Functions for arrays and objects.
 * ------------------------------------------------------------------------ */

/**
 * Sanitizes array, object, or string values (from Jetpack Stats module).
 *
 * @since 0.1
 *
 * @param array|object|string $value value to be sanitized
 * @return array|object|string $value sanitized value
 */
function postscript_esc_html_deep( $value ) {
    if ( is_array( $value ) ) {
        $value = array_map( 'stats_esc_html_deep', $value );
    } elseif ( is_object( $value ) ) {
        $vars = get_object_vars( $value );
        foreach ( $vars as $key => $data ) {
            $value->{$key} = postscript_esc_html_deep( $data );
        }
    } elseif ( is_string( $value ) ) {
        $value = esc_html( $value );
    }

    return $value;
}

/**
 * Convert an object to an array, recursively.
 *
 * https://coderwall.com/p/8mmicq/php-convert-mixed-array-objects-recursively
 */
function postscript_object_into_array( $obj ) {
    if (is_object( $obj ) )
        $obj = get_object_vars( $obj );

    return is_array( $obj ) ? array_map( __FUNCTION__, $obj ) : $obj;
}


