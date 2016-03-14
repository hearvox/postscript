<?php

/**
 * General functions for options and registered/selected scripts and styles.
 *
 * @link       http://hearingvoices.com/tools/
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Fire front-end hooks by loading a post.
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
 * @since    1.0.0
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
 * @since    1.0.0
 * @param integer $post_id ID of post to fetch
 * @return  mixed Either header array and body HTML or error object is URL not valid
 */
function postscript_load_post( $post_id ) {

    $latest_post_url = get_permalink( $post_id ) ?  : NULL;
    $response = wp_remote_get( $latest_post_url );

    return $response;
}

/* ------------------------------------------------------------------------ *
 * Functions to set/get transients with front-end script/style arrays.
 * ------------------------------------------------------------------------ */
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
add_action( 'wp_head', 'postscript_set_wp_scripts_transient' );

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
 * Functions for getting registered scripts and styles.
 * ------------------------------------------------------------------------ */

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_get_style_reg_handles() {
    global $wp_styles;

    // Array of registered scripts handles (from $wp_scripts object).
    $styles_reg = array_values( wp_list_pluck( $wp_styles->registered, 'handle' ) );
    sort( $styles_reg ); // Alphabetize.

    return $styles_reg;
}

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_get_script_reg_handles() {
    global $wp_scripts;

    // Array of registered scripts handles (from $wp_scripts object).
    $scripts_reg = array_values( wp_list_pluck( $wp_scripts->registered, 'handle' ) );
    sort( $scripts_reg ); // Alphabetize.

    return $scripts_reg;
}


/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_script_reg_handles() {
    global $wp_scripts;
    $script_handles = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $script_reg ) {
        $script_handles[] = $script_reg->handle;
    }

    sort( $script_handles ); // Alphabetize.

    return $script_handles;
}

/**
 * Makes an alphabetized array of registered style handles.
 */
function postscript_style_reg_handles() {
    global $wp_styles;
    $style_handles = array();

    // Make array to sort registered styles by handle (from $wp_styles object).
    foreach( $wp_styles->registered as $style_reg ) {
        $style_handles[] = $style_reg->handle;
    }

    sort( $style_handles ); // Alphabetize.

    return $style_handles;
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

function ps_script_is( $script, $list = 'registered') {
    if ( wp_script_is( $script, $list ) ) {
        echo 'yo';
    } else {
    echo 'no';
    }
}

/**
 * Outputs HTML select element populated with registered script handles (alphabetized).
 */
function postscript_scripts_reg_select() {
    global $wp_scripts;
    $scripts_data = '';
    $postscript_scripts_reg_handles = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $script_reg ) {
        $postscript_scripts_reg_handles[] = $script_reg->handle;
    }
    sort( $postscript_scripts_reg_handles );

    // $options = get_option( 'postscript_scripts_option' );
    ?>
    <select id="postscript_scripts" name="postscript[scripts]">
        <option value=''><?php _e( 'Select a script:', 'postscript' ); ?></option>
        <?php
        foreach( $postscript_scripts_reg_handles as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Functions for arrays and objects.
 * ------------------------------------------------------------------------ */

/**
 * Sanitizes array, object, or string values (from Jetpack Stats module).
 *
 * @since 1.0.0
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

/* ------------------------------------------------------------------------ *
 * Utility functions for options.
 * ------------------------------------------------------------------------ */

/**
 * Retrieves an option, and array of plugin settings, from database.
 *
 * Settings screen and option functions based on Jetpack Stats:
 * /jetpack/modules/stats.php
 *
 * @since 1.0.0
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
 * Retrieves a specific setting (an array item) from an option (an array).
 *
 * @since 1.0.0
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
 * @since 1.0.0
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

/**
 * Sets an option in database (an array of plugin settings).
 *
 * Note: update_option() adds option if it doesn't exist.
 *
 * @since 1.0.0
 *
 * @param array $option array of plugin settings
 */
function postscript_set_options( $options ) {
    update_option( 'postscript', $options );
}

/**
 * Makes array of plugin settings, merging default and new values.
 *
 * @since 1.0.0
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

    if ( is_array( $options ) && ! empty( $options ) )
        $new_options = array_merge( $defaults, $options );
    else
        $new_options = $defaults;

    $new_options['version'] = POSTSCRIPT_VERSION;

    postscript_set_options( $new_options );

    return $new_options;
}

/**
 * Sets and option (array) with form-submitted setting values (array items).
 *
 * @since 1.0.0
 *
 * @uses postscript_set_options()
 */
function postscript_configuration_load() {
    if ( isset( $_POST['action'] ) && $_POST['action'] == 'save_options' && $_POST['_wpnonce'] == wp_create_nonce( 'postscript' ) ) {
        $options = postscript_get_options();
        $options['script_url']  = isset( $_POST['script_url']  ) && $_POST['script_url'];
        $options['style_url'] = isset( $_POST['style_url'] ) && $_POST['style_url'];

        $options['roles'] = array( 'administrator' );
        foreach ( get_editable_roles() as $role => $details ) { // Get user roles.
            if ( isset( $_POST["role_$role"] ) && $_POST["role_$role"] ) { // Set only if valid user role.
                $options['roles'][] = $role;
            }
        }

        $options['post_types'] = array();
        foreach ( get_post_types() as $post_type ) { // Get post types.
            if ( isset( $_POST["post_type_$post_type"] ) && $_POST["post_type_$post_type"] ) { // Set only if valid post type.
                $options['post_types'][] = $post_type;
            }
        }

        $options['scripts'] = array();
        foreach ( postscript_reg_script_handles() as $script ) { // Get registered script handles.
            if ( isset( $_POST["script_$script"] ) && $_POST["script_$script"] ) { // Set only if valid handle.
                $options['scripts'][] = $script;
            }
        }

        $options['styles'] = array();
        foreach ( postscript_reg_style_handles() as $style ) { // Get registered style handles.
            if ( isset( $_POST["style_$style"] ) && $_POST["style_$style"] ) { // Set only if valid handle.
                $options['styles'][] = $style;
            }
        }

        postscript_set_options( $options );
        postscript_configuration_screen();
        // postscript_jp_update_blog();
        // Jetpack::state( 'message', 'module_configured' );
        // wp_safe_redirect( Jetpack::module_configuration_url( 'stats' ) );
        exit;
    }
}

/* ------------------------------------------------------------------------ *
 * Utility functions for post meta.
 * ------------------------------------------------------------------------ */

/**
 * Retrieves an option, and array of plugin settings, from database.
 *
 * Settings screen and option functions based on Jetpack Stats:
 * /jetpack/modules/stats.php
 *
 * @since 1.0.0
 *
 * @uses postscript_upgrade_options()
 * @return array $options array of plugin settings
 */
function postscript_get_post_meta_all() {
    $options = get_option( 'postscript' );

    // Set version if not the latest.
    if ( ! isset( $options['version'] ) || $options['version'] < POSTSCRIPT_VERSION ) {
        $options = postscript_upgrade_options( $options );
    }

    return $options;
}

/**
 * Retrieves a specific setting (an array item) from an option (an array).
 *
 * @since 1.0.0
 *
 * @uses postscript_get_options()
 * @param array|object|string $option array item key
 * @return array $options[$option] array item value
 */
function postscript_get_post_meta_one( $option ) {
    $options = postscript_get_options();

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return null;
}
