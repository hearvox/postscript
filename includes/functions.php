<?php

/**
 * General functions for options and registered/selected scripts and styles.
 *
 * @link    http://hearingvoices.com/tools/
 * @since   0.1.0
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
 * @since   0.1.0
 *
 * @uses    postscript_upgrade_options()
 * @return  array   $options    Array of plugin settings
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
 * @since   0.1.0
 *
 * @param   array   $option     Array of plugin settings
 */
function postscript_set_options( $options ) {
    update_option( 'postscript', $options );
}

/**
 * Makes array of plugin settings, merging default and new values.
 *
 * @since   0.1.0
 *
 * @uses    postscript_set_options()
 * @param   array   $options        Array of plugin settings
 * @return  array   $new_options    Merged array of plugin settings
 */
function postscript_upgrade_options( $options ) {
    $defaults = array(
        'user_roles' => array( 'administrator' ),
        'post_types' => array( 'post' ),
        'allow'      => array(
            'urls_script'  => '1',
            'urls_style'   => '1',
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
 * @since   0.1.0
 *
 * @uses    postscript_get_options()
 * @param   array|string    $option     Array item key
 * @return  array           $option_key Array item value
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
 * @since   0.1.0
 *
 * @uses    postscript_set_options()
 *
 * @param   string  $option     Array item key of specified setting
 * @param   string  $value      Array item value of specified setting
 * @return  array   $options    Array of plugin settings
 */
function postscript_set_option( $option, $value ) {
    $options = postscript_get_options();

    $options[$option] = $value;

    postscript_set_options( $options );
}

/**
 * Sanitizes values in an one- and multi- dimensional arrays.
 *
 * Used by post meta-box form before writing post-meta to database
 * and by Settings API before writing option to database.
 *
 * @link https://tommcfarlin.com/input-sanitization-with-the-wordpress-settings-api/
 *
 * @since    0.4.0
 *
 * @param    array    $input        The address input.
 * @return   array    $input_clean  The sanitized input.
 */
function postscript_sanitize_data( $data = array() ) {
    // Initialize a new array to hold the sanitized values.
    $data_clean = array();

    // Traverse the array and sanitize each value.
    if ( ! is_array( $data ) || ! count( $data )) {
        return array();
    }

    // Traverse the array and sanitize each value.
    foreach ( $data as $key => $value) {
        // For one-dimensional array.
        if ( ! is_array( $value ) && ! is_object( $value ) ) {
            // Remove blank lines and leading/trailing whitespace.
            $value = preg_replace( '/^\h*\v+/m', '', trim( $value ) );

            $data_clean[ $key ] = sanitize_text_field( $value );
        }

        // For multidimensional array.
        if ( is_array( $value ) ) {
            $data_clean[ $key ] = postscript_sanitize_data( $value );
        }
    }

    return $data_clean;
}

/**
 * Sanitizes values in an one-dimensional array.
 * (Used by post meta-box form before writing post-meta to database.)
 *
 * @link https://tommcfarlin.com/input-sanitization-with-the-wordpress-settings-api/
 *
 * @since    0.4.0
 *
 * @param    array    $input        The address input.
 * @return   array    $input_clean  The sanitized input.
 */
function postscript_sanitize_array( $input ) {
    // Initialize a new array to hold the sanitized values.
    $input_clean = array();

    // Traverse the array and sanitize each value.
    foreach ( $input as $key => $val ) {
        $input_clean[ $key ] = sanitize_text_field( $val );
    }

    return $input_clean;
}

function postscript_remove_empty_lines( $string ) {
    return preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string );
    // preg_replace( '/^\h*\v+/m', '', $string );
}

/* ------------------------------------------------------------------------ *
 * Functions to set/get transients with front-end script/style arrays.
 * ------------------------------------------------------------------------ */

/**
 * Gets registered scripts and styles.
 *
 * Gets WordPress default scripts/styles, then those plugin/theme registered.
 * The 'shutdown' hook fires after wp_default_scripts()/_styles()
 * and after admin screen has rendered (so enqueued scripts don't affect admin display).
 *
 * @since   0.1.0
 */
function postscript_get_reg_handles() {

    if ( is_admin() ) {
        return;
    }

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

    // Set transients with scripts arrays (!= checks for new registrations).
    if ( $scripts_reg != $postscript_scripts_reg ) {
        set_transient( 'postscript_scripts_reg', $scripts_reg, 60 * 60 * 4 );
    }

    if ( $styles_reg != $postscript_styles_reg ) {
        set_transient( 'postscript_styles_reg', $styles_reg, 60 * 60 * 4 );
    }

    /* For future feature to separate defaults from plugin/theme scripts.
    // Get arrays of only back-end and only front-end scripts
    // by comparing before and after actions arrays.
    $wp_scripts_front = array_diff( $wp_scripts_reg, $wp_scripts_pre);
    $wp_scripts_back = array_intersect( $wp_scripts_reg, $wp_scripts_pre);

    // THIS GOES ABOVE AT TOP OF FN, ABOVE do_action().
    // Arrays with WordPress default and back-end scripts.
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

}
add_action( 'shutdown', 'postscript_get_reg_handles' );

/* ------------------------------------------------------------------------ *
 * Functions for returning arrays of registered script/style handles.
 * ------------------------------------------------------------------------ */

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_script_handles() {
    $postscript_scripts_reg = get_transient( 'postscript_scripts_reg' );

    if ( ! $postscript_scripts_reg ) { // If transient expired.
        postscript_get_reg_handles(); // Set transients.
        $postscript_scripts_reg = get_transient( 'postscript_scripts_reg' );
    }

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

    if ( ! $postscript_styles_reg ) { // If transient expired.
        postscript_get_reg_handles(); // Set transients.
        $postscript_styles_reg = get_transient( 'postscript_styles_reg' );
    }

    // Array of registered scripts handles (from $wp_scripts object).
    $styles_reg = array_values( wp_list_pluck( $postscript_styles_reg, 'handle' ) );
    sort( $styles_reg ); // Alphabetize.

    return $styles_reg;
}

/* ------------------------------------------------------------------------ *
 * Functions to check URLs.
 * ------------------------------------------------------------------------ */

/**
 * Gets URL src from registered scripts exists. (Not used yet.)
 * @todo Add status code as tax-meta upon settings wp_insert_term.
 *
 * @param  $url         URL to be checked.
 * @return int|string   URL Sstatus repsonse code number, or WP error on failure.
 */
function postscript_handle_url( $handle = '' ) {
    $postscript_scripts_reg  = get_transient( 'postscript_scripts_reg' );
    $url = $postscript_scripts_reg[ $handle ]->src;

    return $url;
}

/**
 * Checks if URL exists. (Not used yet.)
 * @todo Add status code as tax-meta upon settings wp_insert_term.
 *
 * @param  $url         URL to be checked.
 * @return int|string   URL Sstatus repsonse code number, or WP error on failure.
 */
function postscript_url_exists( $url = '' ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    // Make protocol-relative URLs absolute  (i.e., from "//example.com" to "https://example.com" )
    if ( substr( $url, 0, 2 ) === '//' ) {
        $url = 'https:' . $url;
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
 *
 * This function is included in the above, but
 *
 * @param  string   $url     URL to be checked for relative path (in WP core).
 * @return string   $url     Absolute path URL for WP core file, otherwise passed $url.
 */
function postscript_core_full_urls( $url ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    return $url;
}

/**
 * Checks enqueued URL extension against whitelist.
 *
 * @param  string   $url    URL to be checked.
 * @return bool             True if extension is in whitelist, false if not.
 */
function postscript_check_url_extension( $url ) {
    // Allowed file extensions.
    $extension_whitelist = array( 'js', 'css' );

    // Filter extensions whitelist.
    if ( has_filter( 'postscript_url_extensions' ) ) {
        $extension_whitelist = apply_filters( 'postscript_url_extensions', $extension_whitelist );
    }

    // Get URL components.
    $url_parts = parse_url( $url );
    if ( $url_parts ) {
        $extension = pathinfo( $url_parts['path'], PATHINFO_EXTENSION );
        if ( in_array( $extension, $extension_whitelist )  ) {
                return true;
        }
    }

    return false;
}

/**
 * Checks URL domain against whitelist.
 *
 * @link https://gist.github.com/mjangda/1623788
 * @param  string   $url    URL to be checked.
 * @return bool             True if extension matches whitelist, false if not.
 */
/*
function postscript_check_url_domain( $url ) {
    $options = postscript_get_options();
    // $domains_whitelist = $options['domains'];

    $domain = strtolower( parse_url( $url, PHP_URL_HOST ) );
    // Check if we match the domain exactly
    if ( in_array( $domain, $domains_whitelist ) )
        return true;

    $valid = false;

    //  Proceed only if subdomain or 'www.' (or 'www12.', etc.).
    $domain_split  = explode( '.', $domain );
    if ( count( $domain_split ) > 1 ) {

    $whitelisted_domain = '.' . $whitelisted_domain; // Prevent things like 'evilsitetime.com'
        if( strpos( $domain, $whitelisted_domain ) === ( strlen( $domain ) - strlen( $whitelisted_domain ) ) ) {
            $valid = true;

    }
    return $valid;
}

$WPLINKS_WHITELIST = str_replace(" ", "", WPLINKS_WHITELIST);
$WPLINKS_WHITELIST_ARRAY = explode(",", $WPLINKS_WHITELIST);

<p><textarea rows="6" cols="80" name="WPLINKS-whitelist" id="WPLINKS-whitelist" placeholder="watchworthy.com, cnn.com, wired.com"><?php echo WPLINKS_WHITELIST;?></textarea><br /><code><strong>Seperate each top level domain name by a comma.</strong></code></p>
<p><code><strong>Example</strong>: watchworthy.com, cnn.com, wired.com</code></p>

/ **
 *
 * Get the whitelisted script domains for the plugin
 * Whitelist domains using `add_filter` on this hook to return array of your site's whitelisted domaiins.
 *
 * @return array of whitelisted domains, e.g. 'ajax.googleapis.com'
 * /
function get_whitelisted_script_domains() {
    return apply_filters( 'shortcake_bakery_whitelisted_script_domains', array() );
}

$whitelisted_script_domains = get_whitelisted_script_domains();

$host = parse_url( $url, PHP_URL_HOST );
if ( ! in_array( $host, $whitelisted_script_domains ) ) {
    continue;
}

$domains_whitelist = trim( $options['domains_whitelist'] );
$domains_allowed = preg_split( '/\r\n|\n\r|\r|\n/', $options['domains_whitelist'] );

function wp_xapi_network_lrs_whitelist_render() {
        ?>
        <textarea name='wpxapi_network_settings[wpxapi_network_lrs_whitelist]'  rows="10" cols="50"><?php echo esc_textarea( $this->options['wpxapi_network_lrs_whitelist'] ); ?></textarea>
        <div class="help-div">We are checking only if the beginning of the url starts with the url that you provided.  So for example: <em>http://lrs.example.org/</em> would work but <em>http://statements.lrs.example.org/</em> will not work</div>
        <p><strong>Currently allowed urls:</strong><br />
        <?php
        if ( ! isset( $this->options['wpxapi_network_lrs_whitelist'] ) || empty( $this->options['wpxapi_network_lrs_whitelist'] ) ) {
            echo '<em>' . esc_html__( 'No currently whitelisted URLs', 'wpxapi' ) . '</em>';
        } else {
            foreach ( preg_split( '/\r\n|\r|\n/', $this->options['wpxapi_network_lrs_whitelist'] ) as $link ) {
                echo esc_url( $link ) . '<br />';
            }
        }
    }

function postscript_remove_empty_lines( $string ) {
    return preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string );
    // preg_replace('/^\h*\v+/m', '', $str);
}

*/
