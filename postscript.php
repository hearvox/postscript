<?php

/*
Plugin Name:       Postscript
Plugin URI:        http://rjionline.org/
Description:       Allows users to enqueue scripts and styles for individual posts. Use <a href="options-general.php?page=postscript">Settings</a> to select registered JS/CSS files, also to dis/allow unregistered URLs and <code>body_class()</code>/<code>post_class()</code> classes.
Version:           1.0.0
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

Post Scripting is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Post Scripting. If not, see:
http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

/* ------------------------------------------------------------------------ *
 * Plugin init and uninstall
 * ------------------------------------------------------------------------ */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'POSTSCRIPT_VERSION', '1.0.0' );

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

/*

if ( defined( 'STATS_VERSION' ) ) {
    return;
}

define( 'STATS_VERSION', '1.0.0' );

 */

/**
 * Sets transient value to array of $wp_scripts global.
 *
 * 'admin_bar_init' hook files after 'wp_default_scripts' and 'wp_default_styles'.
 */
function postscript_wp_default_scripts() {
    $wp_scripts = wp_scripts();

    set_transient( 'postscript_wp_default_scripts', $wp_scripts->registered, 60 * 60 * 4 );
}
// add_action( 'admin_bar_init', 'postscript_wp_default_scripts' );

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
        'rewrite'           => array( 'slug' => 'postscript_styles' ),
    );

    // Filter params for Styles custom taxonomy.
    if ( has_filter( 'postscript_tax_styles' ) ) {
        $args_styles = apply_filters( 'postscript_tax_styles', $args_styles );
    }

    register_taxonomy( 'postscript_styles', $post_types, $args_styles );
}
add_action( 'init', 'postscript_create_taxonomies', 0 );

function postscript_check_tax_term( $term, $taxonomy) {
    if ( $taxonomy == 'postscript_scripts' ) {
        if ( wp_script_is( $term, 'registered' ) ) {
            return $term;
        } else {
        return new WP_Error( 'invalid_term', __('The script handle you entered is <strong>not</strong> <a href="https://developer.wordpress.org/reference/functions/wp_register_script/">registered</a>.') );
        }
    }

    if ( $taxonomy == 'postscript_styles' ) {
        if (wp_style_is( $term, 'registered' ) ) {
            return $term;
        } else {
        return new WP_Error( 'invalid_term', __('The style handle you entered is <strong>not</strong> <a href="https://developer.wordpress.org/reference/functions/wp_register_style/">registered</a>.') );
        }
    }

    return $term;
}
// add_filter('pre_insert_term', 'postscript_check_tax_term', 20, 2);

/* ------------------------------------------------------------------------ *
 * Tests and Notes
 * ------------------------------------------------------------------------ */

/**
 * Write to wp-content/debub.log
 */
function ps_log( $message ) {
    if ( WP_DEBUG === true ) {
        if ( is_array($message ) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}

/* Write test data in content of specified post */
// http://rji.local/?p=1740
function postscript_test_nq( $content ) {
    if ( is_single( 1740 ) && is_main_query() ) {
        global $wp_scripts, $wp_styles;
        $scripts = get_the_terms( get_the_ID(), 'postscript_scripts' );
        $scripts_names = array_values( wp_list_pluck( $scripts, 'name' ) );

        $data_content = '<h2>$wp_scripts->queue</h2>';
        // $data_content .= postscript_get_scripts();
        $data_content .= '<pre>';
        $data_content .= print_r( $scripts_names, true );
        $data_content .= '<hr />';
        foreach ( $scripts as $script ) {
            $data_content .= "<br>$script->name: ";
            if ( wp_script_is( $script->name, 'registered' ) ) {
                $data_content .= 'yo';
            } else {
                $data_content .= 'no';
            }
        }
        $data_content .= '<hr /><b>done:</b><br>';
        $data_content .= print_r( $wp_scripts->done, true );

        $data_content .= '<hr /><b>reg:</b><br>';
        $data_content .= print_r( array_values( wp_list_pluck( $GLOBALS['wp_scripts']->registered, 'handle' ) ), true );
        $data_content .= '</pre>';

        return $content . $data_content;
    } else {
        return make_clickable( $content ); // Make raw URLs links.
    }
}
add_filter('the_content', 'postscript_test_nq');

/* Data for testing */
function postscript_get_scripts() {
    global $wp_scripts, $wp_styles;
    $script_data = '';
    $style_arr = array();
    $script_arr = array();

    // Make array to sort registered styles by handle (from $wp_styles object).
    foreach( $wp_styles->registered as $style_reg ) {
        $style_arr[] = $style_reg->handle;
    }
    sort( $style_arr );

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $scripts_reg ) {
        // $deps = ( ! empty ( $script_reg->deps ) ) ? ' (' . join(', ', $script_reg->deps) . ')' : '';
        $scripts_arr[] = $scripts_reg->handle;
    }
    sort( $scripts_arr );

    $script_data .= '<h3>Registered styles</h3><ol>';
    foreach( $style_arr as $style ) {
        $script_data .= '<li><code>' . $style . '</code></li>';
    }

    $script_data .= '</ol><h3>Registered scripts</h3><ol>';
    foreach( $scripts_arr as $script ) {
        $script_data .= '<li><code>' . $script . '</code></li>';
    }

    $script_data .= '</ol><h3>Queued scripts</h3><ol>';
    foreach( $wp_scripts->queue as $handle ) {
        $script_data .= '<li><code>' . $handle . ' (' . postscript_url_exists( $wp_scripts->registered[$handle]->src ) . ')<br />';
        $script_data .= $wp_scripts->registered[$handle]->src . '</code></li>';
    }

    $script_data .= '</ol><h3>Queued styles</h3><ol>';
    foreach( $wp_styles->queue as $handle ) {
        $script_data .= '<li><code>' . $handle . ' (' . postscript_url_exists( $wp_styles->registered[$handle]->src ) . ')<br />';
        $script_data .= $wp_styles->registered[$handle]->src . '</code></li>';
    }

    $script_data .= '</ol>';

    $script_data .= '<hr /><h3>Scripts object</h3><pre>' . print_r( $wp_scripts, true ) . '</pre>';
    // $script_data .= '<hr /><h3>Styles object</h3><pre>' . print_r( $wp_styles, true ) . '</pre>';

/*
    echo '<pre>:<br />';
    echo ;
    echo '</pre>';
*/

    // Get settings option; set default values.
    $postscript_allow_script_url = get_option( 'postscript_allow_script_url', true );
    $postscript_allow_style_url = get_option( 'postscript_allow_style_url', true );
    $postscript_post_types = get_option( 'postscript_post_types', array() );

    $array = array();
    // delete_option( 'postscript_added_scripts' );
    add_option( 'postscript_added_scripts', $array );
    $postscript_added_scripts = get_option( 'postscript_added_scripts', $array );

    // array_walk( $test_arr, 'get_handles' )
    $script_data .= phpversion();
    $postscript_reg_scripts_arr = postscript_object_into_array( $wp_scripts->registered );
    sort( $postscript_reg_scripts_arr );

    // $postscript_reg_script_handles = array_column( $postscript_reg_scripts_arr, 'handle' ); // PHP 5.5+
    $postscript_reg_script_handles = array_map( function ( $arr ) { return $arr['handle']; }, $postscript_reg_scripts_arr );

    $script_data .='<pre>';

    $postscript_test = array_search( 'admin-widgets', $postscript_reg_scripts_arr );

    $script_data .= 'postscript_test- postscript_test:<br />';
    $script_data .= print_r( $postscript_test, true );
    // echo print_r( $postscript_test );

    $script_data .= '</pre><pre>';

    $script_data .= 'postscript_reg_script_handles:<br />';
    $script_data .= print_r( $postscript_reg_script_handles, true );

    $script_data .= '</pre><pre>';

    $script_data .= 'postscript_reg_scripts_arr:<br />';
    $script_data .= print_r( $postscript_reg_scripts_arr, true );

    $script_data .= '</pre><pre>';

    $script_data .= '</pre>';

    $script_data .= 'postscript_add_script: ';
    if ( isset( $_POST['postscript_add_script'] ) ) {
        $script_data .= $_POST['postscript_add_script'];
    }
    $script_data .= '<br />';
    $script_data .= 'postscript_added_scripts:<br />';
    print_r( $postscript_added_scripts, true );

    $script_data .= '<p>';

    $script_data .= '</pre>';


    return $script_data;
}





/*

http://rji.local/wp-admin/options-general.php?page=postscript

http://rji.local/plugin-postscript/
http://rji.local/?p=1740
<?php print_r( wp_load_alloptions() ); ?>
http://rji.local/wp-admin/options.php

current_user_can( 'manage_options' );

http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/apps/non-labor-income/non-labor-income.css
http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/apps/non-labor-income/non-labor-income.js


$x = wp_insert_term('Scripts','postscript');
print_r( $x->error_data['term_exists'] );

@TODO
* Abstract load post fn for use in per-post enqueued list.
* Add Activate functions.
* Add Deactivate functions.
* Uninstall - rm option, post meta, tax terms.
* Check if script still registered (i.e., if dereg, or reg removed).
* Drag drop table row order
* Run posts in all allowed post-types then merge $wp_scripts (if some reg is post-type only.)
* Page template.
* Register scripts.
* File-mod is vers number.
* Sanitize (escape late).
* Export settings, post meta, and tax terms.
* List enqueues on Post screen?
* Add settings notices.
* Check if script still registered
* Add/set version in options: $new_options['version'] = POSTSCRIPT_VERSION;
* Test Activate- init option defaults.
* Test from install.
* Test uninstall (rm 'postscript' option, 'postscript_meta' meta, and tax terms.)
* rm tax terms not in reg array.
* rm unused options (keep: postscript)
* rm unsued meta (keep: postscript_meta)
* rm notes and tests.


*/
