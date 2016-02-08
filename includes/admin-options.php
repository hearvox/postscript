<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Post_Scripting
 * @subpackage Post_Scripting/includes
 */

/* ------------------------------------------------------------------------ *
 * Admin Settings Page (Dashboard> Settings> Post Scripting)
 * ------------------------------------------------------------------------ */

/**
 * Creates an admin menu item under Settings, and a settings page.
 *
 * Var $psing_settings set to Page Hook Suffix -- return value of add_options_page().
 * Var used by psing_load_scripts() (as $hook_suffix value), called by 'admin_enqueue_scripts' action.
 *
 * @uses psing_admin_page() Called in filter
 */
function psing_admin_page() {

    global $psing_settings;
    $psing_settings = add_options_page( __('Post Scripting: Scripts and Style', 'postscript' ), __( 'Post Scripting', 'postscript' ), 'manage_options', 'psing-settings', 'postscript_load' );

    add_action( 'admin_init', 'psing_register_settings' );

}
add_action( 'admin_menu', 'psing_admin_page' );

/**
 * Builds HTML for admin settings page.
 */
function psing_settings_page() {
    global $wp_scripts, $wp_styles;

    $exampleListTable = new Pser_Scripts_Table();
    $exampleListTable->prepare_items();

    // echo psing_update_settings();

    // Get settings option; set default values.
    $psing_allow_script_url = get_option( 'psing_allow_script_url', true );
    $psing_allow_style_url = get_option( 'psing_allow_style_url', true );
    $psing_post_types = get_option( 'psing_post_types', array() );

    $array = array();
    // delete_option( 'psing_added_scripts' );
    add_option( 'psing_added_scripts', $array );
    $psing_added_scripts = get_option( 'psing_added_scripts', $array );

    ?>
    <div class="wrap">
        <h2><?php _e( 'Post Scripting: Settings', 'postscript'); ?></h2>
        <div id="psing_settings_boxes" class="postbox-container">
            <div class="metabox-holder">
                    <pre><?php // print_r( $_REQUEST['script'] ); ?></pre>
                    <pre><?php // echo $found = ( in_array( $_REQUEST['script'][0], $psing_added_scripts ) ) ? 'yo' : 'no' ?></pre>
                <div id="psing-allow-urls" class="postbox">
                    <h3><?php _e( 'Heading level 3', 'postscript' ); ?></h3>
                    <div class="inside">
                        <p>
                            <?php _e( 'Paragraph text.', 'postscript' ); ?><?php echo " (psing_allow_script_url: $psing_allow_script_url, psing_allow_style_url: $psing_allow_style_url)";

                            // checked( get_option( 'psing_allow_script_url', 'on' ) );
                            ?>
                            ?>
                        </p>
                        <form method="post" action="<?php echo admin_url( 'options-general.php?page=psing-settings' ); ?>">
                            <fieldset>
                                <legend><?php _e( 'Allow users to enqueue URLs in post meta box:', 'postscript' ); ?></legend>
                                <ul>
                                    <li><input type="checkbox" id="psing_allow_script_url" name="psing_allow_script_url" <?php checked( get_option( 'psing_allow_script_url', true ) ); ?> /> <label for="psing_allow_script_url"><?php _e( 'Allow script URL', 'postscript' ); ?></label>
                                    </li>
                                    <li><input type="checkbox" id="psing_allow_style_url" name="psing_allow_style_url" <?php checked( get_option( 'psing_allow_style_url', true ) ); ?> /> <label for="psing_allow_style_url"><?php _e( 'Allow style URL', 'postscript' ); ?></label>
                                    </li>
                                </ul>
                            </fieldset>
                            <p>
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'psing-nonce' ); ?>" />
                                <input type="hidden" name="action" value="psing-allow-urls" />
                            </p>

                            <p><input type="submit" name="psing-allow-urls-submit" class="button-primary" value="<?php _e( 'Update Allowed URLs', 'postscript' ) ?>"/></p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <div id="psing-post-types" class="postbox">
                    <h3><?php _e( 'Allowed Post Types', 'postscript' ); ?></h3>
                    <div class="inside">
                        <p>
                            <?php _e( 'The Post Scripting box displays on the edit screen for the post types you choose.', 'postscript' ); ?><?php echo '<br />psing_post_types: '; print_r( $psing_post_types ); ?>
                        </p>
                        <form method="post" action="<?php echo admin_url( 'options-general.php?page=psing-settings' ); ?>">
                            <fieldset>
                                <legend><?php _e( 'Display Post Scripting box for these post types:', 'postscript' ); ?></legend>
                                <ul>
                                    <?php
                                    // Gets post types explicitly set 'public' (but not registered only with individual public options):
                                    // https://codex.wordpress.org/Function_Reference/get_post_types
                                    $args = array(
                                       'public'     => true,
                                    );
                                    $post_types = get_post_types( $args, 'objects' );
                                    // $psing_post_types = isset( $_POST['psing_post_types'] ) ? $_POST[ 'psing_post_types' ] : array(); // Initialize

                                    foreach ( $post_types as $post_type ) {
                                        $post_type_name = $post_type->labels->name;
                                        $post_type = $post_type->name;
                                    ?>
                                    <li><input type="checkbox" id="cb-<?php echo $post_type; ?>" value="<?php echo $post_type; ?>" name="psing_post_types[]" <?php checked( in_array( $post_type, $psing_post_types ) ); ?> /> <label for="cb-<?php echo $post_type; ?>"><?php echo $post_type_name; ?></label></li>
                                    <?php // replace with:  <?php checked( isset( $psing_post_types[$post_type] ) ); ?>
                                    <?php } ?>
                                </ul>
                            </fieldset>
                            <p>
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'psing-nonce' ); ?>" />
                                <input type="hidden" name="action" value="psing-post-types" />
                            </p>

                            <p><input type="submit" name="psing-post-types-submit" class="button-primary" value="<?php _e( 'Update Allowed Post Types', 'postscript' ) ?>"/></p>
                        </form>
                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <div id="psing-add-script" class="postbox">
                    <h3><?php _e( 'Add a Registered Script', 'postscript' ); ?></h3>
                    <div class="inside">
                        <p>
                            <?php _e( 'You can only add scripts that are already registered (using the <code><a href="https://developer.wordpress.org/reference/functions/wp_register_script/">wp_register_script()</a></code> function).', 'postscript' ); ?><?php echo '<br />psing_added_scripts: '; print_r( $psing_added_scripts ); ?>
                        </p>
                        <form method="post" action="<?php echo admin_url( 'options-general.php?page=psing-settings' ); ?>">
                                <label for="psing_add_script"><?php _e( 'Add a registered script', 'postscript' ); ?></label>
                                <select name='psing_add_script' id='psing_add_script'>
                                    <option value=''><?php _e( 'Select script', 'postscript' ); ?></option>
                                    <?php
                                    $script_handles = psing_reg_scripts_arr();
                                    foreach( $script_handles as $handle ) {
                                        echo "<option value=\"$handle\">$handle</option>";
                                    }
                                    ?>
                                </select>
                            <p>
                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'psing-nonce' ); ?>" />
                                <input type="hidden" name="action" value="psing-add-script" />
                            </p>
                            <p><input type="submit" name="psing-add-script" class="button-primary" value="<?php _e( 'Add script', 'postscript' ) ?>"/></p>
                        </form>
                        <?php
                        $psing_reg_scripts_arr = psing_object_into_array( $wp_scripts->registered );
                        sort( $psing_reg_scripts_arr );

                        $psing_added_script_keys = array();

                        function psing_arr_value_keys( $arr_multi, $field, $value ) {
                           foreach( $arr_multi as $key => $arr ) {
                              if ( $arr[$field] === $value )
                                 return $arr;
                           }
                           return false;
                        }

                        foreach ( $psing_added_scripts as $handle) {
                            $psing_added_script_keys[] = psing_arr_value_keys( $psing_reg_scripts_arr, 'handle', $handle );
                        }

                        /**
                         * Get the table data
                         *
                         * @return Array
                         */
                        function psing_table_data() {
                            global $wp_scripts, $wp_styles; $psing_added_script_keys;

                            $psing_added_scripts = get_option( 'psing_added_scripts' );

                            $psing_reg_scripts_arr = psing_object_into_array( $wp_scripts->registered );
                            sort( $psing_reg_scripts_arr );

                            foreach ( $psing_added_scripts as $handle) {
                                $psing_added_script_keys[] = psing_arr_value_keys( $psing_reg_scripts_arr, 'handle', $handle );
                            }

                            return $psing_added_script_keys;
                        }



                        // echo '<p>psing_added_script_keys:<br />';
                        // print_r( $psing_added_script_keys );
                        echo '</pre>';
                        ?>


                    </div><!-- .inside -->
                </div><!-- .postbox -->

                <?php postscript_load(); ?>


            </div><!-- .metabox-holder -->
        </div><!-- .postbox-container -->

        <section class="clear">
        <div id="psing-added-scripts-table" class="icon32"><br/></div>
            <h2>Selected scripts</h2>

            <form method="get" action="<?php echo admin_url( 'options-general.php?page=psing-settings' ); ?>">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $exampleListTable->display(); ?>
            </form>
            <?php // psing_render_script_list_form(); ?>
        </section>
    </div><!-- .wrap -->

    <aside class="clear">
        <?php // echo psing_get_scripts(); ?>
        <p><?php echo get_num_queries(); ?> queries in <?php timer_stop( 1 ); ?> seconds uses <?php echo round( memory_get_peak_usage() / 1024 / 1024, 3 ); ?> MB peak memory.</p>
    </aside>

    <!-- form id="psing-form" method="POST" action="">
        <p>
            <input type="text" name="psing-handle" id="psing_handle" value="<?php _e('Get Results', 'postscript'); ?>"/>
            <input type="submit" name="psing-submit" id="psing_submit" class="button-primary" value="<?php _e('Get Results', 'postscript'); ?>"/>
            <img src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting" id="psing_loading" style="display:none;" />
        </p>
    </form>
    <div id="psing_results"></div -->
    <?php

}

function psing_update_settings() {

    if ( ! empty( $_POST ) ) {

        $nonce = $_REQUEST['_wpnonce'];
        if ( ! wp_verify_nonce( $nonce, 'psing-nonce' ) ) {
            // die( "Psing: Security check failed." );
        }

        // Settings to allow post editor field for users to enter script/style URLs (defualt: true).
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'psing-allow-urls' ) {
            $psing_allow_script_url = ( isset( $_POST['psing_allow_script_url'] ) ) ? true : false;
            $psing_allow_style_url = ( isset( $_POST['psing_allow_style_url'] ) ) ? true : false;
            update_option( 'psing_allow_script_url', (bool) $psing_allow_script_url );
            update_option( 'psing_allow_style_url', (bool) $psing_allow_style_url );
            $message = __( "Allow URLs settings updated.", 'postscript' );

            return "<div class='updated'><p>" . $message . "</p></div>";
        }

        // Settings for user to chose the post type(s) (to display meta box on editor screen).
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'psing-post-types' ) {
            $psing_post_types = ( isset( $_POST['psing_post_types'] ) ) ? $_POST['psing_post_types'] : array();
            update_option( 'psing_post_types', (array) $psing_post_types );
            $message = __( "Post type settings updated.", 'postscript' );

            return "<div class='updated'><p>" . $message . "</p></div>";
        }

        // Setting that builds array of allowed script handles.
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'psing-add-script' ) {
            $psing_add_script = sanitize_text_field( $_POST['psing_add_script'] );
            $psing_added_scripts = get_option( 'psing_added_scripts' );

            if ( is_array( $psing_added_scripts ) && ! in_array( $psing_add_script, $psing_added_scripts ) ) {
                $psing_added_scripts[] = $psing_add_script;
                update_option( 'psing_added_scripts', (array) $psing_added_scripts );
                $message = __( "Script added to list.", 'postscript' );
                $class = 'updated';
            } else {
                $message = __( "Error: Script already in list.", 'postscript' );
                $class = 'error';
            }

            return "<div class=\"$class\"><p>$message</p></div>";
        }

        return "<div class=\"$class\"><p>$message</p></div>";

    } else {

        return;

    }

}

/**
 * Makes an alphabetized array of registered script handles.
 */
function psing_reg_scripts_arr() {
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
 * Return only matching array elements..
 */
function psing_filter_array() {

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
 * Convert an object to an array, recursively.
 *
 * https://coderwall.com/p/8mmicq/php-convert-mixed-array-objects-recursively
 */
function psing_object_into_array( $obj ) {
    if (is_object( $obj ) )
        $obj = get_object_vars( $obj );

    return is_array( $obj ) ? array_map( __FUNCTION__, $obj ) : $obj;
}

function postscript_admin_notice() {
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'remove' ) {
        if ( isset( $_GET['script'] ) ) {
                $message = __( 'Items to be removed: ' . print_r( $_GET['script'], true ), 'postscript' );
                $class = 'updated settings-updated';
        ?>
        <div class="<?php echo $class; ?> is-dismissible"><p><?php echo $message; ?> / action: <?php echo $_GET['action']; ?></p></div>
        <?php
        }
    }
}
// add_action( 'admin_notices', 'postscript_admin_notice' );


















/**
 * Creates settings fields via WordPress Settings API.
 */
function psing_register_settings() {

    // $psing_added_scripts[] = 'yo2';


    register_setting( 'psing-settings-url-group', 'psing_options', 'sanitize_text_field' );
    register_setting( 'psing-settings-url-group', 'psing_options', 'sanitize_text_field' );
    register_setting( 'psing-settings-scripts-group', 'psing_options', 'sanitize_text_field' );
    register_setting( 'psing-settings-styles-group', 'psing_options', 'sanitize_text_field' );
    // add_settings_section('psing_settings_section', 'Post Scripting Settings', 'psing_section_callback', 'psing-settings');
    // add_settings_field('psing_scripts_field', sprintf( __( '%1$sAdd a Registered Script%2$s', 'postscript' ), '<label for"psing_scripts_field">', '</label>' ), 'psing_scripts_field_callback', 'psing-settings', 'psing_settings_section');

}
// add_action('admin_init', 'psing_register_settings');

function psing_section_callback() {
    echo '<p>psing_section_text()</p>';
    settings_fields( 'psing-settings-scripts-group' );
}



/**
 * Outputs HTML select element populated with registered script handles (alphabetized).
 */
function psing_scripts_field_callback() {
    global $wp_scripts;
    $scripts_data = '';
    $scripts_arr = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $script_reg ) {
        $scripts_arr[] = $script_reg->handle;
    }
    sort( $scripts_arr );

    // $options = get_option( 'psing_scripts_option' );
    ?>
    <select id="psing_scripts_field" name="psing_options[psing_scripts_option]">
        <option value=''><?php _e( 'Select a script:', 'postscript' ); ?></option>
        <?php
        foreach( $scripts_arr as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
    <?php
}

function psing_styles_field_callback() {
    global $wp_styles;
    $styles_data = '';
    $styles_arr = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_styles->registered as $styles_reg ) {
        $styles_arr[] = $styles_reg->handle;
    }
    sort( $styles_arr );

    $options = get_option( 'psing_styles_option' );
    ?>
    <select id="psing_styles_field" name="psing_options[psing_styles_option]">
        <option value=''><?php _e( 'Select a style:', 'postscript' ); ?></option>
        <?php
        foreach( $styles_arr as $style_handle ) {
            echo "<option value=\"{$style_handle}\">{$style_handle}</option>";
        }
        ?>
    </select>
    <?php
}

/**
 * Loads AJAX scripts for processing admin settings page form.
 */
function psing_load_scripts( $hook_suffix ) {

    global $psing_settings;

    if( $hook_suffix != $psing_settings )
        return;

    wp_enqueue_script( 'psing-ajax', plugin_dir_url( __FILE__ ) . 'psing-ajax.js', array( 'jquery' ) );
    wp_localize_script( 'psing-ajax', 'psing_vars', array(
            'psing_nonce' => wp_create_nonce( 'psing-nonce' )
        )
    );

}
// add_action( 'admin_enqueue_scripts', 'psing_load_scripts' );

/**
 * Check for script registration.
 */
function psing_process_ajax() {

    if( ! isset( $_POST['psing_nonce'] ) || ! wp_verify_nonce( $_POST['psing_nonce'], 'psing-nonce' ) ) {
        die( 'Permissions check failed' );
    }

    $downloads = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 7 ) );
    if ( $downloads ) :
        echo '<ul>';
            foreach ($downloads as $download ) {
                echo '<li>' . get_the_title( $download->ID ) . ' - <a href="' . get_permalink( $download->ID ) . '">' . __( 'View Download', 'postscript' ) . '</a></li>';
            }
        echo '</ul>';
        echo '<p>' . get_num_queries() . ' queries in ' . timer_stop() . ' seconds uses ' . round( memory_get_peak_usage() / 1024 / 1024, 3 ) . ' MB peak memory.</p>';
    else :
        echo '<p>' . __( 'No results found', 'postscript' ) . '</p>';
    endif;

    die();

}
// add_action( 'wp_ajax_psing_get_results', 'psing_process_ajax' );




















/* */
if ( defined( 'POSTSCRIPT_VERSION' ) ) {
    return;
}

define( 'POSTSCRIPT_VERSION', '1' );

function postscript_load() {
    global $wp_roles;

    postscript_configuration_load();
    postscript_configuration_screen();

    // Map stats caps
    // add_filter( 'map_meta_cap', 'postscript_jp_map_meta_caps', 10, 4 );
}


/**
 * Maps view_stats cap to read cap as needed
 *
 * @return array Possibly mapped capabilities for meta capability
 */
/* */
function postscript_jp_map_meta_caps( $caps, $cap, $user_id, $args ) {
    // Map view_stats to exists
    if ( 'view_stats' == $cap ) {
        $user        = new WP_User( $user_id );
        $user_role   = array_shift( $user->roles );
        $stats_roles = stats_get_option( 'roles' );

        // Is the users role in the available stats roles?
        if ( is_array( $stats_roles ) && in_array( $user_role, $stats_roles ) ) {
            $caps = array( 'read' );
        }
    }

    return $caps;
}

/**
 * Retrieves an option, and array of plugin settings, from database.
 *
 * Settings screen and option functions based on Jetpack Stats:
 * /plugins/jetpack/modules/stats.php
 *
 * @since 1.0.0
 *
 * @uses postscript_upgrade_options()
 * @return array $options array of plugin settings
 */
function postscript_get_options() {
    $options = get_option( 'postscript_options' );

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
function postscript_get_option( $option ) {
    $options = postscript_get_options();

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return null;
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
    update_option( 'postscript_options', $options );
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
        'roles'         => array( 'administrator' ),
        'post_types'    => array( 'post' ),
        'script_url'    => true,
        'style_url'     => true,
        'scripts'       => array(),
        'styles'        => array(),
    );

    if ( is_array( $options ) && ! empty( $options ) )
        $new_options = array_merge( $defaults, $options );
    else
        $new_options = $defaults;

    $new_options['version'] = POSTSCRIPT_VERSION;

    postscript_set_options( $new_options );

    // postscript_jp_update_blog();

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

        if ( ! function_exists( 'get_editable_roles' ) ) { // Need WP_User class.
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
        }

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

/**
 * Builds the HTML for the settings screen.
 *
 * @since 1.0.0
 *
 * @uses postscript_get_options()
 */
function postscript_configuration_screen() {
    $options = postscript_get_options();
    ?>

    <h2><?php _e( 'Configure Postscript Box Display', 'postscript' ); ?></h2>
    <p><?php _e( 'This plugin lets users enqueue scripts and styles for individual posts. The <strong>Postscript meta-box</strong> displays a checkbox for each registered script or style you add to the lists below. You can also add a text-field for users to insert a script or style URL. The following settings determine the user roles and post types for which the meta-box displays. Below that are the settings to add or remove scripts and styles from the Postscript meta-box checkboxes.', 'postscript' ); ?></p>
    <div class="postbox">
        <form method="post" class="inside">
            <input type="hidden" name="action" value="save_options" />
            <?php wp_nonce_field( 'postscript' ); ?>
            <h3><?php _e( 'User Roles', 'postscript' ); ?></h3>
            <fieldset>
                <legend><?php _e( 'Select users roles allowed to use Postscript box:', 'postscript' ); ?></legend>
                <ul class="inside">
                <?php

                $postscript_roles = postscript_get_option( 'roles' );

                foreach ( get_editable_roles() as $role => $details ) {
                ?>
                    <li><label><input type="checkbox"<?php checked( $role == 'administrator' || in_array( $role, $postscript_roles ) ); ?> <?php if ( $role == 'administrator' ) echo "disabled='disabled' "; ?>name="role_<?php echo $role; ?>" /> <?php echo translate_user_role( $details['name'] ); ?></label></li>
                <?php
                }
                ?>
                </ul>
            </fieldset>
            <h3><?php _e( 'Post Types', 'postscript' ); ?></h3>
            <fieldset>
                <legend><?php _e( 'Select post types that display Postscript box:', 'postscript' ); ?></legend>
                <ul class="inside">
                <?php
                $postscript_post_types = postscript_get_option( 'post_types' );

                // Gets post types explicitly set 'public' (not those registered only with individual public options):
                // https://codex.wordpress.org/Function_Reference/get_post_types
                foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
                    $post_type_label = $post_type->labels->name;
                    $post_type_name = $post_type->name;
                ?>
                    <li><label><input type="checkbox"<?php checked( in_array( $post_type_name, $postscript_post_types ) ); ?> name="post_type_<?php echo $post_type_name; ?>" /> <?php echo $post_type_label; ?></label></li>
                <?php
                }
                ?>
                </ul>
            </fieldset>
            <h3><?php _e( 'Allow URLs', 'postscript' ); ?></h3>
            <fieldset>
                <legend><?php _e( 'Allow URL fields in Postscript box:', 'postscript' ); ?></legend>
                <ul class="inside">
                    <li><input type="checkbox"<?php checked( $options['script_url'] ); ?> id="script_url" name="script_url" /> <label for="script_url"><?php _e( 'Script URL', 'postscript' ); ?></label>
                    </li>
                    <li><input type="checkbox"<?php checked( $options['style_url'] ); ?> id="style_url" name="style_url" /> <label for="style_url"><?php _e( 'Style URL', 'postscript' ); ?></label>
                    </li>
                </ul>
            </fieldset>
            <p class="submit"><input type='submit' class='button-primary' value='<?php echo esc_attr( __( 'Save configuration', 'postscript' ) ); ?>' /></p>
        </form>
        <pre>
            <?php
            print_r( $options );
            ?>
        </pre>
    </div>
    <?php
}

/**
 * Makes an alphabetized array of registered script handles.
 */
function postscript_reg_script_handles() {
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
function postscript_reg_style_handles() {
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
