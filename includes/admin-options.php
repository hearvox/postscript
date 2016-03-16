<?php

/**
 * Admin Settings Page (Dashboard> Settings> Postscript)
 *
 * @link       http://hearingvoices.com/tools/
 * @since 0.1
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

    // Before rendering forms, add or remove any user-selected script and style.
    postscript_add_remove();
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
        </form>

        <?php postscript_meta_box_example(); ?>
    </div><!-- .wrap -->
    <?php
}

/**
 * Adds or removes any user-selected script/style in the form submission data.
 */
function postscript_add_remove() {
    $options = postscript_get_options();

    // Arrays of registered script handles.
    $script_handles = postscript_script_handles();
    $style_handles = postscript_style_handles();

    // Add new script or style custom tax term, if registered handle.
    if ( isset( $options['script_add'] ) && in_array( $options['script_add'], $script_handles )  ) {
        wp_insert_term( $options['script_add'], 'postscript_scripts' );
    }

    if ( isset( $options['style_add'] ) && in_array( $options['style_add'], $style_handles )  ) {
        wp_insert_term( $options['style_add'], 'postscript_styles' );
    }

    // Delete custom tax term for added script or style.
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

/* ------------------------------------------------------------------------ *
 * Setting Registrations
 * ------------------------------------------------------------------------ */

/**
 * Creates settings fields via WordPress Settings API.
 */
function postscript_options_init() {

    // Array to pass to $callback functions as add_settings_field() $args (last param).
    $options = postscript_get_options(); // Option: 'postscript'.

    add_settings_section(
        'postscript_settings_section',
        __( 'Postcript box visibility', 'postscript' ),
        'postscript_section_callback',
        'postscript'
    );

    add_settings_section(
        'postscript_scripts_styles_section',
        __( 'Add scripts and styles', 'postscript' ),
        'postscript_scripts_styles_section_callback',
        'postscript'
    );

    add_settings_section(
        'postscript_script_style_remove_section',
        __( 'Remove scripts and styles', 'postscript' ),
        'postscript_script_style_remove_section_callback',
        'postscript'
    );

    add_settings_field(
        'postscript_user_roles',
        __( 'User Roles', 'postscript' ),
        'postscript_user_roles_callback',
        'postscript',
        'postscript_settings_section',
        $args = $options
    );

    add_settings_field(
        'postscript_post_types',
        __( 'Post Types', 'postscript' ),
        'postscript_post_types_callback',
        'postscript',
        'postscript_settings_section',
        $args = $options
    );

    add_settings_field(
        'postscript_allow_fields',
        __( 'Allow URLs, Classes', 'postscript' ),
        'postscript_allow_fields_callback',
        'postscript',
        'postscript_settings_section',
        $args = $options
    );

    add_settings_field(
        'postscript_style_add',
        __( 'Add a Style', 'postscript' ),
        'postscript_style_add_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_script_add',
        __( 'Add a Script', 'postscript' ),
        'postscript_script_add_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_style_remove',
        __( 'Remove a Style', 'postscript' ),
        'postscript_style_remove_callback',
        'postscript',
        'postscript_script_style_remove_section'
    );

    add_settings_field(
        'postscript_script_remove',
        __( 'Remove a Script', 'postscript' ),
        'postscript_script_remove_callback',
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

/**
 * Outputs text for the top of the Settings screen.
 */
function postscript_section_callback() {
    ?>
    <p><?php _e('The Postscript meta box (in the Edit Post screen) lets users enqueue scripts and styles for a single post.', 'postscript' ); ?></p>
    <p><?php _e('Choose which post-types and user-roles display the Postscript box.', 'postscript' ); ?></p>
    <?php
}

/**
 * Outputs text for the top of the Add Scripts/Styles section.
 */
function postscript_scripts_styles_section_callback() {
    ?>
    <p><?php _e('Add registered script or style to be listed in the Postscript box.', 'postscript' ); ?></p>
    <?php
}

/**
 * Outputs text for the top of the Remove Scripts/Styles section.
 */
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
function postscript_user_roles_callback( $options ) {
    // Need WP_User class.
    if ( ! function_exists( 'get_editable_roles' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/user.php' );
    }

    // Note: $options[0] below is array of user-selected roles, from 'postscript' option.
    ?>
    <fieldset>
        <legend><?php _e( 'Select the roles allowed to use Postscript box:', 'postscript' ); ?></legend>
        <ul class="inside">
        <?php
        foreach ( get_editable_roles() as $role => $details ) {
        ?>
            <li><label><input type="checkbox" id="<?php echo $role; ?>" value="<?php echo $role ?>" name="postscript[user_roles][]"<?php checked( in_array( $role, $options['user_roles'] ) ); ?><?php disabled( 'administrator', $role ); ?> /> <?php echo translate_user_role( $details['name'] ); ?></label></li>
        <?php
        }
        ?>
            <input type="hidden" value="administrator" name="postscript[user_roles][]" />
        </ul>
    </fieldset>
    <?php
}

/**
 * Outputs HTML checkboxes of post types (used to determine if Postscript box displays).
 */
function postscript_post_types_callback( $options ) {
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
            <li><label><input type="checkbox" id="<?php echo $post_type; ?>" value="<?php echo $post_type; ?>" name="postscript[post_types][]"<?php checked( in_array( $post_type, $options['post_types'] ) ); ?> /> <?php echo $post_type_arr->labels->name; ?></label></li>
        <?php
        }
        ?>
        </ul>
    </fieldset>
    <?php
}

/**
 * Outputs HTML checkboxes (to allow text fields in Postscript box for entering URLs and classes).
 */
function postscript_allow_fields_callback( $options ) {
    $opt = $options['allow']; // User settings to permit URLs and classes.
    ?>
    <fieldset>
        <legend><?php _e( 'Add a text field in Postscript box for:', 'postscript' ); ?></legend>
        <ul class="inside">
            <li><label><input type="checkbox" id="" name="postscript[allow][url_style]" value="on"<?php checked( 'on', isset( $opt['url_style'] ) ? $opt['url_style'] : 'off' ); ?>/> <?php _e( 'Style URL', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][url_script]" value="on"<?php checked( 'on', isset( $opt['url_script'] ) ? $opt['url_script'] : 'off' ); ?>/> <?php _e( 'Script URL 2', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][url_script_2]" value="on"<?php checked( 'on', isset( $opt['url_script_2'] ) ? $opt['url_script_2'] : 'off' ); ?>/> <?php _e( 'Script URL 2', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][class_body]" value="on"<?php checked( 'on', isset( $opt['class_body'] ) ? $opt['class_body'] : 'off' ); ?>/> <?php _e( 'Body class*', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="" name="postscript[allow][class_post]" value="on"<?php checked( 'on', isset( $opt['class_post'] ) ? $opt['class_post'] : 'off' ); ?>/> <?php _e( 'Post class*', 'postscript' ); ?></label></li>
        </ul>
        <p class="wp-ui-text-icon"><?php _e( 'Requires <code>body_class()</code>/<code>post_class()</code> in theme.', 'postscript' ); ?></p>
    </fieldset>
    <?php
}

/**
 * Outputs HTML select menu of all registered styles.
 */
function postscript_style_add_callback() {
    // Array of registered script handles.
    $style_handles = postscript_style_handles();

    // Output HTML select menu of (sorted) handles.
    ?>
    <select id="postscript_styles_field" name="postscript[style_add]">
        <option value=''><?php _e( 'Select style to add:', 'postscript' ); ?></option>
        <?php
        foreach( $style_handles as $style_handle ) {
            echo "<option value=\"{$style_handle}\">{$style_handle}</option>";
        }
        ?>
    </select>
    <?php
    // Get user-selected style handles (stored as tax terms).
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $styles_added = get_terms( 'postscript_styles', $args );
    // Display table of selected handles (with $wp_styles data and term's post count).
    ?>
    <table class="wp-list-table widefat striped">
        <caption><strong>Styles added</strong></caption>
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
    // Array of registered styles' data (transient stores front-end $wp_styles).
    $postscript_styles_reg  = get_transient( 'postscript_styles_reg' );

    if ( ! empty( $styles_added ) ) {
        foreach ( $styles_added as $style_obj ) {
            if ( in_array( $style_obj->name, $style_handles ) ) {
                $style_name   = $style_obj->name;
                $style_arr    = $postscript_styles_reg[ $style_name ];
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
    <?php
}

/**
 * Outputs HTML select menu of all registered scripts.
 */
function postscript_script_add_callback() {
    // Array of registered script handles.
    $script_handles = postscript_script_handles();

    // Output HTML select menu of (sorted) registered script handles.
    ?>
    <select id="postscript_scripts_field" name="postscript[script_add]">
        <option value=''><?php _e( 'Select script to add:', 'postscript' ); ?></option>
        <?php
        foreach( $script_handles as $script_handle ) {
            echo "<option value=\"{$script_handle}\">{$script_handle}</option>";
        }
        ?>
    </select>
    <?php
    // Get user-selected script handles (stored as tax terms).
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $scripts_added = get_terms( 'postscript_scripts', $args );

    // Display table of selected handles (with $wp_scripts data and term's post count).
    ?>
    <table class="wp-list-table widefat striped">
        <caption><strong>Scripts added</strong></caption>
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
    // Array of registered scripts' data (transient stores front-end $wp_scripts).
    $postscript_scripts_reg  = get_transient( 'postscript_scripts_reg' );

    if ( ! empty( $scripts_added ) ) {
        foreach ( $scripts_added as $script_obj ) {
            if ( in_array( $script_obj->name, $script_handles ) ) {
                $script_name  = $script_obj->name;
                $script_arr   = $postscript_scripts_reg[ $script_name ];
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
    <p class="wp-ui-text-icon textright">
        <?php _e( '<strong>Status</strong> response code links to <code>src</code> file.', 'postscript' ); ?><br />
        <?php _e( '<strong>Posts</strong> count link lists posts enqueueing the file.', 'postscript' ); ?>
    </p>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Displays all allowed post-types in post lists for plugin's custom tax term.
 *
 * Term's post-count link displays in Settings page table and tax admin screens.
 *
 *
 * ------------------------------------------------------------------------ */
function postscript_pre_get_posts( $query ) {
    $options = postscript_get_options();
    if ( is_admin() ) {
        if ( get_query_var( 'postscript_scripts' ) || get_query_var( 'postscript_styles' ) ) {
            $query->set('post_type', '' ); // Hack: to get all post-type to display for term.

            // $query->set('post_type', $options['post_types'] ); // Use this when fixed:
            // https://core.trac.wordpress.org/ticket/30013
            // /wp-admin/edit.php doesn't accept array (Error: array to string conversion)
        }
    }
}
add_action( 'pre_get_posts', 'postscript_pre_get_posts' );

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

/**
 * Render example of Edit Post screen meta box, based on settings using post's meta box fn.
 *
 * @see   /includes/meta-box.php
 * @uses  postscript_meta_box_callback()
 */
function postscript_meta_box_example() {
    $options     = postscript_get_options( 'postscript' );
    $box['args'] = $options; // Meta box stores $options as an ['args'] array.
    $fake_post   = (object) array( 'ID' => '-1'); // Meta box needs a post object id.
    ?>
    <hr />
    <h2><?php _e('Postscript meta box example', 'postscript' ); ?></h2>
    <p>
        <?php _e('If user-role is: ', 'postscript' ); ?><?php echo implode( $options['user_roles'], ', ' ); ?><br />
        <?php _e('If post-type is: ', 'postscript' ); ?><?php echo implode( $options['post_types'], ', ' ); ?><br />
        <?php _e('This meta box displays on the Edit Post screen:', 'postscript' ); ?>
    <p>

    <div id="postscript-meta" class="postbox postbox-container">
        <div id="categorydiv" class="postbox ">
        <h2 class="hndle ui-sortable-handle"><span>Postscript</span></h2>
            <div class="inside">
                <?php postscript_meta_box_callback( $fake_post, $box ); ?>
            </div><!-- .inside -->
        </div><!-- .postbox -->
    </div><!-- .postbox-container -->

    <p class="clear wp-ui-text-icon"><?php echo get_num_queries(); ?><?php _e(" queries in ", 'postscript'); ?><?php timer_stop( 1 ); ?><?php _e(" seconds uses ", 'postscript'); ?><?php echo size_format( memory_get_peak_usage(), 2); ?> <?php _e(" peak memory", 'postscript'); ?></p>
    <?php
}
