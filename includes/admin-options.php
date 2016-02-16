<?php

/**
 * Admin Settings Page (Dashboard> Settings> Postscript)
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Wordpress Settings API
 * ------------------------------------------------------------------------ */

/**
 * Adds submenu item to Settings dashboard menu.
 *
 */
function postscript_settings_menu() {
    add_options_page(
        __('Postscript: Enqueue Scripts and Style', 'postscript' ),
        __( 'Postscript', 'postscript' ),
        'manage_options',
        'postscript',
        'postscript_settings_display' );

}
add_action('admin_menu', 'postscript_settings_menu');

/**
 * Renders settings menu page.
 */
function postscript_settings_display() {
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <!-- Add the icon to the page -->
        <h2><?php _e('Postscript settings', 'postscript' ); ?></h2>

        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php settings_errors(); ?>

        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'postscript' ); ?>
            <?php do_settings_sections( 'postscript' ); ?>
            <?php submit_button(); ?>

            <?php // postscript_reg_scripts_select(); ?>
            <?php // postscript_render_script_list_form(); ?>

        </form>

<div id="postbox-container-1" class="postbox-container">
    <div id="categorydiv" class="postbox ">
        <h2 class="hndle"><span><?php _e('Postscript Styles and Scripts', 'postscript' ); ?></span></h2>
        <div class="inside">
            <div id="taxonomy-category" class="categorydiv">
                <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
                    <?php wp_terms_checklist( -1, array( 'taxonomy' => 'postscript' ) ); ?>
                </ul>
            </div><!-- .categorydiv -->
        </div><!-- .inside -->
    </div><!-- .postbox -->
</div><!-- .postbox-container -->

<?php
$args = array(
  'taxonomy'     => 'postscript',
  'show_count'   => 1,
  'pad_counts'   => 1,
  'hierarchical' => 1,
  'hide_empty '  => 0,
  'title_li'     => 'Postscript Styles and Scripts: List'
);
?>

<ul class="clear">
<?php wp_list_categories( $args ); ?>
</ul>


        <?php print_test_data(); ?>

    </div><!-- .wrap -->
<?php
}

/* ------------------------------------------------------------------------ *
 * Setting Registrations
 * ------------------------------------------------------------------------ */

/**
 * Creates settings fields via WordPress Settings API.
 */
function postscript_options_init() {

    if ( false == get_option( 'postscript' ) ) {
        add_option( 'postscript' );
    }

    add_settings_section(
        'postscript_settings_section',
        __( 'Postcript box visibility', 'postscript' ),
        'postscript_section_callback',
        'postscript'
    );

    add_settings_section(
        'postscript_scripts_styles_section',
        __( 'Allow Scripts and Styles', 'postscript' ),
        'postscript_scripts_styles_section_callback',
        'postscript'
    );

    add_settings_field(
        'postscript_user_roles',
        __( 'User Roles', 'postscript' ),
        'postscript_user_roles_callback',
        'postscript',
        'postscript_settings_section'
    );

    add_settings_field(
        'postscript_post_types',
        __( 'Post Types', 'postscript' ),
        'postscript_post_types_callback',
        'postscript',
        'postscript_settings_section'
    );

    add_settings_field(
        'postscript_allow_urls',
        __( 'Allow URLs', 'postscript' ),
        'postscript_allow_urls_callback',
        'postscript',
        'postscript_settings_section'
    );

    add_settings_field(
        'postscript_script_add',
        __( 'Add a Script', 'postscript' ),
        'postscript_script_add_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_scripts',
        __( 'Allowed Scripts', 'postscript' ),
        'postscript_scripts_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_style_add',
        __( 'Add a Style', 'postscript' ),
        'postscript_style_add_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_styles',
        __( 'Allowed Styles', 'postscript' ),
        'postscript_styles_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    register_setting(
        'postscript',
        'postscript'
    );

}
add_action('admin_init', 'postscript_options_init');

/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * ------------------------------------------------------------------------ */

function postscript_section_callback() {
?>
    <p><?php _e('The Postscript meta box (in the Edit Post screen) lets users enqueue scripts and styles for a single post.', 'postscript' ); ?></p>
    <p><?php _e('Choose which post-types and user-roles display the Postscript box.', 'postscript' ); ?></p>
<?php
}

function postscript_scripts_styles_section_callback() {
?>
    <p><?php _e('Add (or remove) the registered scripts and styles listed in the Postscript box.', 'postscript' ); ?></p>
<?php
}

/* ------------------------------------------------------------------------ *
 * Field Callbacks
 * ------------------------------------------------------------------------ */

/**
 * Outputs HTML checkboxes of user roles (used to determine if Postscript box displays).
 */
function postscript_user_roles_callback() {
    $options = get_option( 'postscript' );
    $options['user_role']['administrator'] = 'on';

    // Need WP_User class.
    if ( ! function_exists( 'get_editable_roles' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/user.php' );
    }
?>
    <fieldset>
        <legend><?php _e( 'Select the roles allowed to use Postscript box:', 'postscript' ); ?></legend>
        <ul class="inside">
        <?php
        foreach ( get_editable_roles() as $role => $details ) {
        ?>
            <li><label><input type="checkbox" id="<?php echo $role; ?>" value="on" name="postscript[user_role][<?php echo $role; ?>]"<?php checked( 'on', isset( $options['user_role'][$role] ) ? $options['user_role'][$role] : 'off' ); ?><?php disabled( 'administrator', $role ); ?> /> <?php echo translate_user_role( $details['name'] ); ?></label></li>
        <?php
        }
        ?>
            <input type="hidden" value="on" name="postscript[user_role][administrator]" />
        </ul>
    </fieldset>
<?php
}

/**
 * Outputs HTML checkboxes of post types (used to determine if Postscript box displays).
 */
function postscript_post_types_callback() {
    $options = get_option( 'postscript' );
?>
    <fieldset>
        <legend><?php _e( 'Select which post types display Postscript box:', 'postscript' ); ?></legend>
        <ul class="inside">
        <?php
        // Gets post types explicitly set 'public' (not those registered only with individual public options):
        // https://codex.wordpress.org/Function_Reference/get_post_types
        foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type_arr ) {
            $post_type = $post_type_arr->name;
        ?>
            <li><label><input type="checkbox" id="<?php echo $post_type; ?>" value="on" name="postscript[post_type][<?php echo $post_type; ?>]"<?php checked( 'on', isset( $options['post_type'][$post_type] ) ? $options['post_type'][$post_type] : 'off' ); ?> /> <?php echo $post_type_arr->labels->name; ?></label></li>
        <?php
        }
        ?>
        </ul>
    </fieldset>
<?php
}

/**
 * Outputs HTML checkboxes, used to allow URL fields in Postscript box.
 */
function postscript_allow_urls_callback() {
    $options = get_option( 'postscript' );
?>
    <fieldset>
        <legend><?php _e( 'Add a text field in Postscript box for:', 'postscript' ); ?></legend>
        <ul class="inside">
            <li><label><input type="checkbox" id="" name="postscript[allow_url][style]" value="on"<?php checked( 'on', isset( $options['allow_url']['style'] ) ? $options['allow_url']['style'] : 'off' ); ?>/> <?php _e( 'Style URL', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow_url][script]" value="on"<?php checked( 'on', isset( $options['allow_url']['script'] ) ? $options['allow_url']['script'] : 'off' ); ?>/> <?php _e( 'Script URL', 'postscript' ); ?></label></li>
        </ul>
    </fieldset>
<?php
}

/**
 * Outputs HTML select menu of all registered scripts.
 */
function postscript_script_add_callback() {
    $options = get_option( 'postscript' );

    global $scripts_arr;
    $scripts_arr = postscript_reg_script_handles();

    // Output select menu of (sorted) registered script handles.
?>
    <select id="postscript_scripts_field" name="postscript[script_add]">
        <option value=''><?php _e( 'Select a script:', 'postscript' ); ?></option>
        <?php
        foreach( $scripts_arr as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
<?php
}

/**
 * Outputs HTML checkboxes in a table of selected scripts and their params (used for Postscript box list).
 */
function postscript_scripts_callback() {
    $options = get_option( 'postscript' );

    global $scripts_arr;

    // Add script chosen with select menu.
    if ( isset( $options['script_add'] ) && in_array( $options['script_add'], $scripts_arr )  ) {
        $options['script'][] = $options['script_add'];
    }

    // Output select menu of (sorted) registered script handles.
    if ( isset( $options['script'] ) ) {
        $scripts_added = array_unique( $options['script'] );
        sort( $scripts_added );
?>
    <fieldset>
        <legend><?php _e( 'Uncheck to remove scripts:', 'postscript' ); ?></legend>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th scope="col" class="th-full" style="padding: 0.5em;">Handle</th>
                    <th scope="col" class="th-full" style="padding: 0.5em;">Ver</th>
                    <th scope="col" class="th-full" style="padding: 0.5em;">Deps</th>
                    <th scope="col" class="th-full" style="padding: 0.5em;">Footer</th>
                    <th scope="col" class="th-full" style="padding: 0.5em;">Src*</th>
                </tr>
            </thead>
            <tbody>
            <?php
            global $wp_scripts;
            foreach ( $scripts_added as $script ) {

                $ps_script_arr = $wp_scripts->registered[ $script ];
                $deps        = implode( ',', $ps_script_arr->deps );
                $footer      = ( ( $ps_script_arr->args == 1 ) ) ? 'footer' : 'head';
                $src         = postscript_core_full_urls( $ps_script_arr->src );
                $status_code = postscript_url_exists( $src );
            ?>
                <tr>
                    <th scope="row" class="th-full" style="padding: 0.5em;"><label><input type="checkbox" id="<?php echo $script; ?>" value="<?php echo $script; ?>" name="postscript[script][]" checked="checked" /> <?php echo $script; ?></label></th>
                    <td><?php echo $ps_script_arr->ver; ?></td>
                    <td><?php echo $deps; ?></td>
                    <td><?php echo $ps_script_arr->args; ?></td>
                    <td><a href="<?php echo $src; ?>"><?php echo $status_code; ?></a></td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </fieldset>
    <p class="wp-ui-text-icon textright">(* "Src" is URL status response code; link goes to file.)
<?php
    } else {
?>
    <p><?php _e( 'No scripts added yet.', 'postscript' ); ?></p>
<?php
    }
}


/*


global $wp_scripts;
// print_r($wp_scripts->registered['jquery']);

$ps_options = get_option('postscript');
$ps_scripts = $ps_options['script'];

echo '<table><thead><tr><th scope="col">handle</th><th scope="col">ver</th><th scope="col">deps</th><th scope="col">footer</th><th scope="col">src*</th></tr></thead><tbody>';

foreach ( $ps_scripts as $ps_script ) {
    // echo '<hr /><pre>';
    // print_r($wp_scripts->registered[ $ps_script ]);
    // echo '</pre>';
    $ps_script_arr = $wp_scripts->registered[ $ps_script ];
    $deps        = implode( ',', $ps_script_arr->deps );
    $footer      = ( ( $ps_script_arr->args == 1 ) ) ? 'footer' : 'head';
    $src         = postscript_core_full_urls( $ps_script_arr->src );
    $status_code = postscript_url_exists( $src );

    echo "<tr><th scope='row'><label><input type='checkbox'> $ps_script_arr->handle</label></th><td>$ps_script_arr->ver</td><td>$deps</td><td>$ps_script_arr->args</td><td><a href='$src'>$status_code</a></td></tr>";


}

echo '</tbody><tfoot><tr><td colspan="5" class="wp-ui-text-icon">* "src" is URL status response code, linked to file.</td></tr></tfoot></table>';


 */

/**
 * Outputs HTML select menu of all registered styles.
 */
function postscript_style_add_callback() {
    $options = get_option( 'postscript' );

    global $styles_arr;
    $styles_arr = postscript_reg_style_handles();

    // Output select menu of (sorted) registered style handles.
?>
    <select id="postscript_styles_field" name="postscript[style_add]">
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
 * Outputs HTML checkboxes of selected styles (used for Postscript box list).
 */
function postscript_styles_callback() {
    $options = get_option( 'postscript' );

    global $styles_arr;

    // Add script chosen with select menu.
    if ( isset( $options['style_add'] ) && in_array( $options['style_add'], $styles_arr )  ) {
        $options['style'][] = $options['style_add'];
    }

    // Output select menu of (sorted) registered script handles.
    if ( isset( $options['style'] ) ) {
        $styles_added = array_unique( $options['style'] );
        sort( $styles_added );
?>
    <fieldset>
        <legend><?php _e( 'Uncheck to remove styles:', 'postscript' ); ?></legend>
        <ul class="inside">
        <?php
        foreach ( $styles_added as $style ) {
        ?>
            <li><label><input type="checkbox" id="<?php echo $style; ?>" value="<?php echo $style; ?>" name="postscript[style][]" checked="checked" /> <?php echo $style; ?></label></li>
        <?php
        }
        ?>
        </ul>
    </fieldset>
<?php
    } else {
?>
    <p><?php _e( 'No styles added yet.', 'postscript' ); ?></p>
<?php
    }
}

/* ------------------------------------------------------------------------ *
 * Utility functions for scripts and styles
 * ------------------------------------------------------------------------ */

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
 * Makes an alphabetized array of registered script handles.
 */
function postscript_reg_scripts_arr() {
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
function postscript_reg_scripts_select() {
    global $wp_scripts;
    $scripts_data = '';
    $scripts_arr = array();

    // Make array to sort registered scripts by handle (from $wp_scripts object).
    foreach( $wp_scripts->registered as $script_reg ) {
        $scripts_arr[] = $script_reg->handle;
    }
    sort( $scripts_arr );

    // $options = get_option( 'postscript_scripts_option' );
    ?>
    <select id="postscript_scripts" name="postscript[scripts]">
        <option value=''><?php _e( 'Select a script:', 'postscript' ); ?></option>
        <?php
        foreach( $scripts_arr as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Utility functions for arrays and objects.
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

/*


global $wp_scripts;
// print_r($wp_scripts->registered['jquery']);

$ps_options = get_option('postscript');
$ps_scripts = $ps_options['script'];

echo '<table><thead><tr><th scope="col">handle</th><th scope="col">ver</th><th scope="col">deps</th><th scope="col">footer</th><th scope="col">src*</th></tr></thead><tbody>';

foreach ( $ps_scripts as $ps_script ) {
    // echo '<hr /><pre>';
    // print_r($wp_scripts->registered[ $ps_script ]);
    // echo '</pre>';
    $ps_script_arr = $wp_scripts->registered[ $ps_script ];
    $deps        = implode( ',', $ps_script_arr->deps );
    $footer      = ( ( $ps_script_arr->args == 1 ) ) ? 'footer' : 'head';
    $src         = postscript_core_full_urls( $ps_script_arr->src );
    $status_code = postscript_url_exists( $src );

    echo "<tr><th scope='row'><label><input type='checkbox'> $ps_script_arr->handle</label></th><td>$ps_script_arr->ver</td><td>$deps</td><td>$ps_script_arr->args</td><td><a href='$src'>$status_code</a></td></tr>";


}

echo '</tbody><tfoot><tr><td colspan="5" class="wp-ui-text-icon">* "src" is URL status response code, linked to file.</td></tr></tfoot></table>';


    <fieldset>
        <legend><?php _e( 'Uncheck to remove styles:', 'postscript' ); ?></legend>
        <ul class="inside">
        <?php
        foreach ( $styles_added as $style ) {
        ?>
            <li><label><input type="checkbox" id="<?php echo $style; ?>" value="<?php echo $style; ?>" name="postscript[style][]" checked="checked" /> <?php echo $style; ?></label></li>
        <?php
        }
        ?>
        </ul>
    </fieldset>

 */

/*
@TODO
Add/set version in options:
$new_options['version'] = POSTSCRIPT_VERSION;

<?php print_r( wp_load_alloptions() ); ?>
depecated: get_alloptions

Options:
http://rji.local/wp-admin/options.php
rm:
psing_allow_script_url
psing_allow_style_url
psing_added_scripts
psing_post_types
postscript_options
postscript_allow_style_url_field
postscript_allow_script_url_field
postscript_settings
postscript_display_options
postscript_scripts
postscript_styles

sandbox_*

Keep:
postscript

 */


function print_test_data() {
?>
<hr class="clear" />
<aside>

    <h2>Test items</h2>
    <pre>
        <?php
        $options = get_option( 'postscript' );
        echo '<br><code>$options = get_option( \'postscript\' )</code>:<br />';
        print_r( $options );
        if ( isset( $options['postscript_allow_url'] ) && is_array( $options['postscript_allow_url'] ) ) {
            echo '<br><code>array_keys( $options[\'postscript_allow_url\'] )</code>:<br />';
            print_r( array_keys( $options['postscript_allow_url'] ) ); } ?>
        <hr />
        <?php $screen = get_current_screen(); ?>
        <?php echo "{$screen->id}\n"; ?>
        <?php // print_r( $screen ); ?>
        <hr />
        <?php // global $wp_scripts; ?>
        <?php // print_r( $wp_scripts->registered ); ?>


        <?php // print_r( wp_load_alloptions() ); ?>
    </pre>
    <p><?php echo get_num_queries(); ?> queries in <?php timer_stop( 1 ); ?> seconds uses <?php echo round( memory_get_peak_usage() / 1024 / 1024, 3 ); ?> MB peak memory.</p>

</aside>
<?php
}
