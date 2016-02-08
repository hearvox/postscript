<?php

/*
Plugin Name:       Postscript
Plugin URI:        http://rjionline.org/
Description:       Add scripts and styles for individual posts.
Version:           1.0.0
Author:            Barrett Golding
Author URI:        http://rjionline.org/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       postscript
Domain Path:       /languages
Plugin Prefix:     postscript

Post Scripting is free software: you can redistribute it and/or modify
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


if( ! class_exists( 'Psing_Scripts_Table' ) ){
    require_once( plugin_dir_path( __FILE__ ) . 'includes/class-psing-scripts-table.php' );
}

if( ! class_exists( 'Paulund_Wp_List_Table_Copy' ) ){
    require_once( plugin_dir_path( __FILE__ ) . 'includes/paulund-wp-list-table-copy-copy.php' );
}



/**
 * Load the plugin text domain for translation.
 *
 * @since    1.0.0
 */
function postscript_load_textdomain() {
    load_plugin_textdomain( 'postscript', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
// add_action( 'plugins_loaded', 'postscript_load_textdomain' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-post-scripting-activator.php
 */
function activate_post_scripting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-scripting-activator.php';
	Post_Scripting_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-post-scripting-deactivator.php
 */
function deactivate_post_scripting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-scripting-deactivator.php';
	Post_Scripting_Deactivator::deactivate();
}

// register_activation_hook( __FILE__, 'activate_post_scripting' );
// register_deactivation_hook( __FILE__, 'deactivate_post_scripting' );

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
function run_post_scripting() {

	$plugin = new Post_Scripting();
	$plugin->run();

}
// run_post_scripting();

/* ------------------------------------------------------------------------ *
 * Required Plugin Files
 * ------------------------------------------------------------------------ */

require_once( 'includes/admin-options.php' );
require_once( 'includes/meta-box.php' );
// require_once( 'includes/functions.php' );




/* ------------------------------------------------------------------------ *
 * Enqueue Srripts and Styles
 * ------------------------------------------------------------------------ */

/**
 * Enqueue scripts and styles selected in the meta box form.
 *
 *
 */
function psing_enqueue_scripts() {
    if ( is_singular() && is_main_query() ) {
        global $post;
        $post_id = $post->ID;
        $psing_style_url = get_post_meta( $post_id, 'psing_style_url', true );
        $psing_script_url = get_post_meta( $post_id, 'psing_script_url', true );
        // $psing_style_handles = get_post_meta( $post_id, 'psing_style_handles', true );
        // $psing_script_handles = get_post_meta( $post_id, 'psing_script_handles', true );

        if ( has_filter( 'psing_post_style' ) ) {
            $style_handles = apply_filters( 'psing_post_style', $style_handles );
        }

        if ( ! empty( $psing_post_style ) ) {
            wp_enqueue_style( 'psing-style-' . $post_id, $psing_post_style, false );
        }

        if ( has_filter( 'psing_script_url' ) ) {
            $psing_script_url = apply_filters( 'psing_script_url', $psing_script_url );
        }

        if ( ! empty( $psing_script_url ) ) {
            wp_enqueue_script( 'psing-script-url' . $post_id, $psing_script_url );
        }
/*
        if ( has_filter( 'psing_style_handles' ) ) {
            $psing_style_handles = apply_filters( 'psing_style_handles', $psing_style_handles );
        }

        foreach ( $psing_style_handles as $handle ) {
            wp_enqueue_style( $handle );
        }

        if ( has_filter( 'psing_script_handles' ) ) {
            $url = apply_filters( 'psing_script_handles', $psing_script_handles );
        }

        foreach ( $psing_script_handles as $handle ) {
            wp_enqueue_script( $handle );
        }
*/
    }
}
add_action( 'wp_enqueue_scripts', 'psing_enqueue_scripts' );

/**
 * Checks if URL exists.
 */
function psing_url_exists( $url ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    if ( has_filter( 'psing_url_exists' ) ) {
        $url = apply_filters( 'psing_url_exists', $url );
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


/* ------------------------------------------------------------------------ *
 * Tests and Notes
 * ------------------------------------------------------------------------ */

/* Write test data in content of specified post */
// http://rji.local/?p=1740
function psing_test_nq( $content ) {
    if ( is_single( 1740 ) && is_main_query() ) {
        global $wp_scripts, $wp_styles;
        $data_content = '<h2>$wp_scripts->queue</h2>';
        $data_content .= psing_get_scripts();
        $data_content .= '';

        return $content . $data_content;
    } else {
        return make_clickable( $content ); // Make raw URLs links.
    }
}
add_filter('the_content', 'psing_test_nq');

/* Data for testing */
function psing_get_scripts() {
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
        $script_data .= '<li><code>' . $handle . ' (' . psing_url_exists( $wp_scripts->registered[$handle]->src ) . ')<br />';
        $script_data .= $wp_scripts->registered[$handle]->src . '</code></li>';
    }

    $script_data .= '</ol><h3>Queued styles</h3><ol>';
    foreach( $wp_styles->queue as $handle ) {
        $script_data .= '<li><code>' . $handle . ' (' . psing_url_exists( $wp_styles->registered[$handle]->src ) . ')<br />';
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
    $psing_allow_script_url = get_option( 'psing_allow_script_url', true );
    $psing_allow_style_url = get_option( 'psing_allow_style_url', true );
    $psing_post_types = get_option( 'psing_post_types', array() );

    $array = array();
    // delete_option( 'psing_added_scripts' );
    add_option( 'psing_added_scripts', $array );
    $psing_added_scripts = get_option( 'psing_added_scripts', $array );

    // array_walk( $test_arr, 'get_handles' )
    $script_data .= phpversion();
    $psing_reg_scripts_arr = psing_object_into_array( $wp_scripts->registered );
    sort( $psing_reg_scripts_arr );

    // $psing_reg_script_handles = array_column( $psing_reg_scripts_arr, 'handle' ); // PHP 5.5+
    $psing_reg_script_handles = array_map( function ( $arr ) { return $arr['handle']; }, $psing_reg_scripts_arr );

    $script_data .='<pre>';

    $psing_test = array_search( 'admin-widgets', $psing_reg_scripts_arr );

    $script_data .= 'psing_test- psing_test:<br />';
    $script_data .= print_r( $psing_test, true );
    // echo print_r( $psing_test );

    $script_data .= '</pre><pre>';

    $script_data .= 'psing_reg_script_handles:<br />';
    $script_data .= print_r( $psing_reg_script_handles, true );

    $script_data .= '</pre><pre>';

    $script_data .= 'psing_reg_scripts_arr:<br />';
    $script_data .= print_r( $psing_reg_scripts_arr, true );

    $script_data .= '</pre><pre>';

    $script_data .= '</pre>';

    $script_data .= 'psing_add_script: ';
    if ( isset( $_POST['psing_add_script'] ) ) {
        $script_data .= $_POST['psing_add_script'];
    }
    $script_data .= '<br />';
    $script_data .= 'psing_added_scripts:<br />';
    print_r( $psing_added_scripts, true );

    $script_data .= '<p>';

    foreach ( $psing_added_scripts as $handle ) {
        // $script_data .= 'handle: ' . $wp_scripts->registered[$handle]->handle . ', src: ' . $wp_scripts->registered[$handle]->src . ', deps: ' . implode( ',', $wp_scripts->registered[$handle]->deps ) . ', ver: ' . $wp_scripts->registered[$handle]->ver . ', args: ' . $wp_scripts->registered[$handle]->args . '<br />';
    }

    // $script_data .= '</p><pre>$wp_scripts->registered[$psing_added_scripts[1]]:';
    // print_r( $wp_scripts->registered[$psing_added_scripts[1]], true );

    $script_data .= '</pre>';


    return $script_data;
}




// Small array for testing registered scripts object converted into array:
$test_reg_scripts_arr =
array(
    array(
        'handle' => 'a8c-developer',
        'src' => 'http://rji.local/wp-content/plugins/developer/developer.js',
        'deps' => array( 'jquery' ),
        'ver' => '1.2.6',
        'args' => '',
    ),
    array(
        'handle' => 'accordion',
        'src' => '/wp-admin/js/accordion.min.js',
        'deps' => array( 'jquery' ),
        'ver' => '',
        'args' => '1',
    ),
    array(
        'handle' => 'admin-bar',
        'src' => '/wp-includes/js/admin-bar.min.js',
        'deps' => array(),
        'ver' => '',
        'args' => '1',
    ),
    array(
        'handle' => 'admin-comments',
        'src' => '/wp-admin/js/edit-comments.min.js',
        'deps' => array( 'wp-lists', 'quicktags', 'jquery-query' ),
        'ver' => '',
        'args' => '1',
    ),
    array(
        'handle' => 'admin-gallery',
        'src' => '/wp-admin/js/gallery.min.js',
        'deps' => array( 'jquery-ui-sortable' ),
        'ver' => '',
        'args' => '',
    )
);

/*

sanitize_text_field();

ID 1740

update_option( 'my_plugin_options', $array_of_options );

current_user_can( 'manage_options' );

escape late

https://codex.wordpress.org/Function_Reference/wp_script_is
wp_script_is( $handle, $list = 'enqueued' );

https://developer.wordpress.org/reference/functions/wp_register_script/

https://developer.wordpress.org/reference/functions/metadata_exists/
http://code.tutsplus.com/articles/the-ins-and-outs-of-the-enqueue-script-for-wordpress-themes-and-plugins--wp-22509
https://pippinsplugins.com/add-screen-options-tab-to-your-wordpress-plugin/
http://code.tutsplus.com/tutorials/how-to-create-custom-wordpress-writemeta-boxes--wp-20336
http://www.smashingmagazine.com/2011/10/create-custom-post-meta-boxes-wordpress/

http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/apps/non-labor-income/non-labor-income.css
http://dev.headwaterseconomics.org/wphw/wp-content/plugins/he-interactives/apps/non-labor-income/non-labor-income.js

function url_exists( $url ) {
    $response = wp_remote_get( $url );
    if ( is_wp_error( $response ) ) {
        //request can't performed
        return 1;
    }

    if ( wp_remote_retrieve_response_code( $response ) == '404' ) {
        //request succeed and link not found
        return 2;
    }

    //request succeed and link exist
   return 3;
}



function cd_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['my_meta_box_text'] ) ? $values['my_meta_box_text'] : '';
    $selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'] ) : '';
    $check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'] ) : '';

    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <p>
        <label for="my_meta_box_text">Text Label</label>
        <input type="text" name="my_meta_box_text" id="my_meta_box_text" value="<?php echo $text; ?>" />
    </p>

    <p>
        <label for="my_meta_box_select">Color</label>
        <select name="my_meta_box_select" id="my_meta_box_select">
            <option value="red" <?php selected( $selected, 'red' ); ?>>Red</option>
            <option value="blue" <?php selected( $selected, 'blue' ); ?>>Blue</option>
        </select>
    </p>

    <p>
        <input type="checkbox" id="my_meta_box_check" name="my_meta_box_check" <?php checked( $check, 'on' ); ?> />
        <label for="my_meta_box_check">Do not check this</label>
    </p>
    <?php
}

function music_meta_box( $post ) {
    // Get post meta value using the key from our save function in the second paramater.
    $custom_meta = get_post_meta($post->ID, '_custom-meta-box', true);

    ?>
        <input type="checkbox" name="custom-meta-box[]" value="huge" <?php echo (in_array('huge', $custom_meta)) ? 'checked="checked"' : ''; ?> />Huge
        <br>
        <input type="checkbox" name="custom-meta-box[]" value="house" <?php echo (in_array('house', $custom_meta)) ? 'checked="checked"' : ''; ?> />House
        <br>
        <input type="checkbox" name="custom-meta-box[]" value="techno" <?php echo (in_array('techno', $custom_meta)) ? 'checked="checked"' : ''; ?> />Techno<br>
    <?php
}
add_action( 'save_post', 'save_music_meta_box' );

function save_music_meta_box() {

    global $post;
    // Get our form field
    if ( isset ( $_POST['custom-meta-box'] ) ) {
        $custom = $_POST['custom-meta-box'];
        $old_meta = get_post_meta( $post->ID, '_custom-meta-box', true );
        // Update post meta
        if( ! empty( $old_meta ) ){
            update_post_meta( $post->ID, '_custom-meta-box', $custom );
        } else {
            add_post_meta( $post->ID, '_custom-meta-box', $custom, true );
        }
    }
}

delete_option( $option );

https://developer.wordpress.org/reference/functions/wp_register_style/

*/

/*
// $array = json_decode(json_encode($object), true);


function recursive_array_search($needle,$haystack) {
    foreach ($haystack as $key => $item) {
        if ($item['handle'] === $needle)
             return $key;
        } else {
        return false;
        }
    }
}


http://culttt.com/2012/06/25/functions-to-handle-multidimensional-arrays-in-php/


// https://coderwall.com/p/8mmicq/php-convert-mixed-array-objects-recursively
function object_to_array($d) {
    if (is_object($d))
        $d = get_object_vars($d);

    return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
}

function array_to_object($d) {
    return is_array($d) ? (object) array_map(__FUNCTION__, $d) : $d;
}

http://ben.lobaugh.net/blog/567/php-recursively-convert-an-object-to-an-array

http://stackoverflow.com/questions/7994497/how-to-get-an-array-of-specific-key-in-multidimensional-array-without-looping
$ids = array_column($users, 'id'); // 5.5+
$ids = array_map(function ($ar) {return $ar['id'];}, $users); // 5.3+
$ids = array_map(create_function('$ar', 'return $ar["id"];'), $users);

$input = array(
    array(
        'tag_name' => 'google'
    ),
    array(
        'tag_name' => 'technology'
    )
);

echo implode(', ', array_map( function ( $entry ) {
    return $entry['tag_name'];
}, $input ) );


http://ben.lobaugh.net/blog/567/php-recursively-convert-an-object-to-an-array
function object_to_array( $obj ) {
    if ( is_object($obj) ) {
        $obj = (array) $obj;
    }

    if ( is_array( $obj ) ) {
        $new = array();
        foreach ( $obj as $key => $val ) {
            $new[$key] = object_to_array( $val );
        }
    } else {
        $new = $obj;
    }
    return $new;
}
*/
