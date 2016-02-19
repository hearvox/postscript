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

    // Make global vars with arrays of registered script and style handles.
    postscript_script_style_reg_handles();

    // Add or remove user-selected scripts and styles (custom taxonomy terms).
    postscript_script_styles_add_remove();
    ?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
        <!-- Add the icon to the page -->
        <h2><?php _e('Postscript settings', 'postscript' ); ?></h2>

        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php // settings_errors(); ?>

        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'postscript' ); ?>
            <?php do_settings_sections( 'postscript' ); ?>
            <?php submit_button(); ?>

            <?php // postscript_reg_scripts_select(); ?>
            <?php // postscript_render_script_list_form(); ?>

        </form>

        <?php postscript_meta_box_example(); ?>
        <?php print_test_data(); ?>

    </div><!-- .wrap -->
    <?php
}

/**
 * Makes global vars with arrays of registered script and style handles.
 */
function postscript_script_style_reg_handles() {
    // Make global vars with arrays of registered script and style handles.
    global $postscript_scripts_reg_handles;
    $postscript_scripts_reg_handles = postscript_script_reg_handles();

    global $postscript_styles_reg_handles;
    $postscript_styles_reg_handles = postscript_style_reg_handles();
}

/**
 * Adds or removes user-selected scripts and styles as custom taxonomy terms.
 */
function postscript_script_styles_add_remove() {
    $options = get_option( 'postscript' );
    global $postscript_scripts_reg_handles;
    global $postscript_styles_reg_handles;

    // Add new script or style tax term, if registered handle.
    if ( isset( $options['script_add'] ) && in_array( $options['script_add'], $postscript_scripts_reg_handles )  ) {
        wp_insert_term( $options['script_add'], 'postscript_scripts' );
    }

    if ( isset( $options['style_add'] ) && in_array( $options['style_add'], $postscript_styles_reg_handles )  ) {
        wp_insert_term( $options['style_add'], 'postscript_styles' );
    }

    // De script or style, if registered.
    if ( ! empty( $options['script_remove'] ) && term_exists( $options['script_remove'], 'postscript_scripts') ) {
        $script_slug = get_term_by('slug', $options['script_remove'], 'postscript_scripts');
        $script_id = $script_slug->term_id;
        wp_delete_term( $script_id, 'postscript_scripts' );
    }

    if ( ! empty( $options['style_remove'] ) && term_exists( $options['style_remove'], 'postscript_styles') ) {
        $style_slug = get_term_by( 'slug', $options['style_remove'], 'postscript_styles');
        $style_id = $style_slug->term_id;
        wp_delete_term( $style_id, 'postscript_styles' );
    }
}

/**
 * Outputs HTML table add user-added registered scripts (with metadata and term posts count).
 */
function postscript_scripts_added_table() {
    global $postscript_scripts_reg_handles;
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $scripts_added = get_terms( 'postscript_scripts', $args );
    ?>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Handle', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Ver', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Deps', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Footer', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Status', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Posts', 'postscript' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    if ( ! empty( $scripts_added ) ) {
        global $wp_scripts;
        foreach ( $scripts_added as $script_obj ) {
            if ( in_array( $script_obj->name, $postscript_scripts_reg_handles ) ) {
                $script_name  = $script_obj->name;
                $script_arr   = $wp_scripts->registered[ $script_name ];
                // Comma-separated list of style dependencies.
                $deps         = implode( ',', $script_arr->deps );
                // Make relative URLs full (for core registered scripts in '/wp-admin' or '/wp-includes').
                $src          = ( $script_arr->src ) ? postscript_core_full_urls( $script_arr->src ) : '';
                // Check URL status response code, if script has a 'src' set.
                $status_code  = ( $src ) ? "<a href='$src'>" . postscript_url_exists( $src ) . '</a>' : '--';
                // Tax term post count, linked to list of posts (if count>0).
                $count  = $script_obj->count;
                $posts_count  = ( $count ) ? '<a href="' . admin_url() . "edit.php?postscript_styles=$script_name\">$count</a>" : $count;
            ?>
            <tr>
                <th scope="row" class="th-full" style="padding: 0.5em;"><label><?php echo $script_name; ?></label></th>
                <td><?php echo $script_arr->ver; ?></td>
                <td><?php echo $deps; ?></td>
                <td><?php echo $script_arr->args; ?></td>
                <td><?php echo $status_code; ?></td>
                <td><?php echo $posts_count; ?></td>
            </tr>
            <?php
            } // if
        } // foreach
    } else { ?>
            <tr><td><p><?php _e( 'You have not added any scripts yet.', 'postscript' ); ?></p></td></tr>
    <?php
    }
    ?>
        </tbody>
    </table>
    <?php
}

/**
 * Outputs HTML table add user-added registered scripts (with metadata and term posts count).
 */
function postscript_styles_added_table() {
    global $postscript_styles_reg_handles;
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $styles_added = get_terms( 'postscript_styles', $args );
    ?>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Handle', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Ver', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Deps', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Media', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Status', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Posts', 'postscript' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    if ( ! empty( $styles_added ) ) {
        global $wp_styles;
        foreach ( $styles_added as $style_obj ) {
            if ( in_array( $style_obj->name, $postscript_styles_reg_handles ) ) {
                $style_name   = $style_obj->name;
                $style_arr    = $wp_styles->registered[ $style_name ];
                // Comma-separated list of style dependencies.
                $deps         = implode( ',', $style_arr->deps );
                // Make relative URLs full (for core registered scripts in '/wp-admin' or '/wp-includes').
                $src          = ( $style_arr->src ) ? postscript_core_full_urls( $style_arr->src ) : '';
                // Check URL status response code, if script has a 'src' set.
                $status_code  = ( $src ) ? "<a href='$src'>" . postscript_url_exists( $src ) . '</a>' : '--';
                // Tax term post count, linked to list of posts (if count>0).
                $count  = $style_obj->count;
                $posts_count  = ( $count ) ? '<a href="' . admin_url() . "edit.php?postscript_styles=$style_name\">$count</a>" : $count;
            ?>
            <tr>
                <th scope="row" class="th-full" style="padding: 0.5em;"><label><?php echo $style_name; ?></label></th>
                <td><?php echo $style_arr->ver; ?></td>
                <td><?php echo $deps; ?></td>
                <td><?php echo $style_arr->args; ?></td>
                <td><?php echo $status_code; ?></td>
                <td><?php echo $posts_count; ?></td>
            </tr>
            <?php
            } // if
        } // foreach
    } else { ?>
            <tr><td><p><?php _e( 'You have not added any styles yet.', 'postscript' ); ?></p></td></tr>
    <?php
    }
    ?>
        </tbody>
    </table>
    <p class="wp-ui-text-icon textright"><?php _e( '* <strong>Status</strong> response code links to <code>src</code> file. <strong>Posts</strong> count link lists posts enqueueing the file.', 'postscript' ); ?></p>
    <?php
}

/**
 * Render example of Edit Post screen meta box, based on settings.
 */
function postscript_meta_box_example() {
    $options = get_option( 'postscript' );
    ?>
    <div id="postbox-container-1" class="postbox-container">
        <div id="categorydiv" class="postbox ">
            <h2 class="hndle"><span><?php _e('Postscript', 'postscript' ); ?></span></h2>
            <div class="inside">
                <div id="taxonomy-postscript_scripts" class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Scripts', 'postscript' ); ?></span></h3>
                    <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
                        <?php wp_terms_checklist( -1, array( 'taxonomy' => 'postscript_scripts' ) ); ?>
                    </ul>
                </div><!-- .categorydiv -->
                <div id="taxonomy-postscript_styles" class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Styles', 'postscript' ); ?></span></h3>
                    <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
                        <?php wp_terms_checklist( -1, array( 'taxonomy' => 'postscript_styles' ) ); ?>
                    </ul>
                </div><!-- .categorydiv -->
                <div class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Script URL', 'postscript' ); ?></span></h3>
                    <input type="url" placeholder="https:" class="regular-text">
                </div><!-- .categorydiv -->
                <div class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Style URL', 'postscript' ); ?></span></h3>
                    <input type="url" placeholder="https:" class="regular-text">
                </div><!-- .categorydiv -->
                <div class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Body class', 'postscript' ); ?></span></h3>
                    <input type="text" placeholder="CSS class" class="regular-text">
                </div><!-- .categorydiv -->
                <div class="categorydiv">
                    <h3 class="hndle"><span><?php _e('Post class', 'postscript' ); ?></span></h3>
                    <input type="text" placeholder="CSS class" class="regular-text">
                </div><!-- .categorydiv -->
            </div><!-- .inside -->
        </div><!-- .postbox -->
    </div><!-- .postbox-container -->
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
        __( 'Add Scripts and Styles', 'postscript' ),
        'postscript_scripts_styles_section_callback',
        'postscript'
    );

    add_settings_section(
        'postscript_script_style_remove_section',
        __( 'Remove Scripts and Styles', 'postscript' ),
        'postscript_script_style_remove_section_callback',
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
        'postscript_allow_fields',
        __( 'Allow URLs, Classes', 'postscript' ),
        'postscript_allow_fields_callback',
        'postscript',
        'postscript_settings_section'
    );
/*
    add_settings_field(
        'postscript_allow_urls',
        __( 'Allow URLs', 'postscript' ),
        'postscript_allow_urls_callback',
        'postscript',
        'postscript_settings_section'
    );

    add_settings_field(
        'postscript_allow_classes',
        __( 'Allow Classes', 'postscript' ),
        'postscript_allow_classes_callback',
        'postscript',
        'postscript_settings_section'
    );
*/
    add_settings_field(
        'postscript_script_add',
        __( 'Add a Script', 'postscript' ),
        'postscript_script_add_callback',
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
        'postscript_scripts_added_table',
        __( 'Scripts Added*', 'postscript' ),
        'postscript_scripts_added_table_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_styles_added_table',
        __( 'Styles Added*', 'postscript' ),
        'postscript_styles_added_table_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_script_remove',
        __( 'Remove a Script', 'postscript' ),
        'postscript_script_remove_callback',
        'postscript',
        'postscript_script_style_remove_section'
    );

    add_settings_field(
        'postscript_style_remove',
        __( 'Remove a Style', 'postscript' ),
        'postscript_style_remove_callback',
        'postscript',
        'postscript_script_style_remove_section'
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
    <p><?php _e('Add registered script or style to be listed in the Postscript box.', 'postscript' ); ?></p>
    <?php
}

function postscript_script_style_remove_section_callback() {
    ?>
    <p><?php _e('Remove script or style from the Postscript box.', 'postscript' ); ?></p>
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
 * Outputs HTML checkboxes, settings used to allow URL fields in Postscript box.
 */
function postscript_allow_fields_callback() {
    $options = get_option( 'postscript' );
    ?>
    <fieldset>
        <legend><?php _e( 'Add a text field in Postscript box for:', 'postscript' ); ?></legend>
        <ul class="inside">
            <li><label><input type="checkbox" id="" name="postscript[allow][style_url]" value="on"<?php checked( 'on', isset( $options['allow_url']['style'] ) ? $options['allow_url']['style'] : 'off' ); ?>/> <?php _e( 'Style URL', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][script_url]" value="on"<?php checked( 'on', isset( $options['allow_url']['script'] ) ? $options['allow_url']['script'] : 'off' ); ?>/> <?php _e( 'Script URL', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][body_class]" value="on"<?php checked( 'on', isset( $options['allow_url']['style'] ) ? $options['allow_url']['style'] : 'off' ); ?>/> <?php _e( 'Body class*', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][post_class]" value="on"<?php checked( 'on', isset( $options['allow_url']['script'] ) ? $options['allow_url']['script'] : 'off' ); ?>/> <?php _e( 'Post class*', 'postscript' ); ?></label></li>
        </ul>
        <p class="wp-ui-text-icon"><?php _e( 'Requires <code>body_class()</code>/<code>post_class()</code> in theme.', 'postscript' ); ?></p>
    </fieldset>
    <?php
}


/**
 * Outputs HTML checkboxes, settings used to allow URL fields in Postscript box.
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
 * Outputs HTML checkboxes, used to allow URL fields in Postscript box.
 */
function postscript_allow_classes_callback() {
    $options = get_option( 'postscript' );
    ?>
    <fieldset>
        <legend><?php _e( 'Add a text field in Postscript box for:', 'postscript' ); ?></legend>
        <ul class="inside">
            <li><label><input type="checkbox" id="" name="postscript[allow_class][body]" value="on"<?php checked( 'on', isset( $options['allow_url']['style'] ) ? $options['allow_url']['style'] : 'off' ); ?>/> <?php _e( 'Body class', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow_class][post]" value="on"<?php checked( 'on', isset( $options['allow_url']['script'] ) ? $options['allow_url']['script'] : 'off' ); ?>/> <?php _e( 'Post class', 'postscript' ); ?></label></li>
        </ul>
        <p class="wp-ui-text-icon"><?php _e( 'Requires <code>body_class()</code>/<code>post_class()</code> in theme.', 'postscript' ); ?></p>
    </fieldset>
    <?php
}

/**
 * Outputs HTML select menu of all registered scripts.
 */
function postscript_script_add_callback() {
    $options = get_option( 'postscript' );
    global $postscript_scripts_reg_handles;

    // Output select menu of (sorted) registered script handles.
    ?>
    <select id="postscript_scripts_field" name="postscript[script_add]">
        <option value=''><?php _e( 'Select script to add:', 'postscript' ); ?></option>
        <?php
        foreach( $postscript_scripts_reg_handles as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
    <?php
}

/**
 * Outputs HTML select menu of all registered styles.
 */
function postscript_style_add_callback() {
    $options = get_option( 'postscript' );
    global $postscript_styles_reg_handles;

    // Output select menu of (sorted) registered style handles.
    ?>
    <select id="postscript_styles_field" name="postscript[style_add]">
        <option value=''><?php _e( 'Select style to add:', 'postscript' ); ?></option>
        <?php
        foreach( $postscript_styles_reg_handles as $style_handle ) {
            echo "<option value=\"{$style_handle}\">{$style_handle}</option>";
        }
        ?>
    </select>
    <?php
}

function postscript_scripts_added_table_callback() {
    postscript_scripts_added_table();
}

function postscript_styles_added_table_callback() {
    postscript_styles_added_table();
}


/**
 * Outputs HTML select menu of all registered scripts.
 */
function postscript_script_remove_callback() {
    $args = array(
        'taxonomy'          => 'postscript_scripts',
        'orderby'           => 'name',
        'name'              => 'postscript[script_remove]',
        'option_none_value' => '',
        'show_option_none'  => 'Select script to remove:',
        'show_count'        => 1,
        'hide_empty'        => 0,
        'value_field'       => 'name',
    );
    ?>
    <ul class="clear">
        <?php wp_dropdown_categories( $args ); ?>
    </ul>
    <?php
}

/**
 * Outputs HTML select menu of all registered style (tax term).
 */
function postscript_style_remove_callback() {
    $args = array(
        'taxonomy'          => 'postscript_styles',
        'orderby'           => 'name',
        'name'              => 'postscript[style_remove]',
        'option_none_value' => '',
        'show_option_none'  => 'Select style to remove:',
        'show_count'        => 1,
        'hide_empty'        => 0,
        'value_field'       => 'name',
    );
    ?>
    <ul class="clear">
        <?php wp_dropdown_categories( $args ); ?>
    </ul>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Utility functions for scripts and styles
 * ------------------------------------------------------------------------ */

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
        'script_url'    => 'on',
        'style_url'     => 'on',
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
        <?php // $screen = get_current_screen(); ?>
        <?php // echo "{$screen->id}\n"; ?>
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
