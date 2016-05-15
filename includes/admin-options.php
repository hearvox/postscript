<?php

/**
 * Admin Settings Page (Dashboard> Settings> Postscript)
 *
 * @link    http://hearingvoices.com/tools/
 * @since   0.1.0
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
 * Sets Settings page screen ID: 'settings_page_postscript'.
 */
function postscript_settings_menu() {
    $postscript_options_page = add_options_page(
        __('Postscript: Enqueue Scripts and Style', 'postscript' ),
        __( 'Postscript', 'postscript' ),
        'manage_options',
        'postscript',
        'postscript_settings_display'
    );

    // Adds contextual Help tab on Settings page.
    add_action( "load-$postscript_options_page", 'postscript_help_tab');
}
add_action('admin_menu', 'postscript_settings_menu');

/**
 * Adds tabs, sidebar, and content to contextual Help tab on Settings page.
 *
 * Sets Settings page screen ID: 'settings_page_postscript'.
 */
function postscript_help_tab() {
    $current_screen = get_current_screen();

    // Default tab.
    $current_screen->add_help_tab(
        array(
            'id'        => 'settings',
            'title'     => __( 'Settings', 'postscript' ),
            'content'   =>
                '<p><strong>' . __( 'Postcript: Enqueue Scripts and Styles Per-Post', 'postscript' ) . '</strong></p>' .
                '<p>' . __( 'Use these settings to add <em>registered</em> script and style handles that users can enqueue from the Edit Post screen. You can also enqueue URLs of <em>unregistered</em> files (one stylesheet and up to two scripts.', 'postscript' ) . '</p>' .
                '<p>' . __( 'And you can allow users to add classes to <code>post_class()</code> and <code>body_class()</code>.', 'postscript' ) . '</p>',
        )
    );

    // Second tab.
    if ( current_user_can( 'manage_options' ) ) {
        $current_screen->add_help_tab(
            array(
                'id'        => 'tech',
                'title'     => __( 'Tech Notes', 'postscript' ),
                'content'   =>
                    '<ul>' .
                        '<li>' . __( 'Handles are stored in custom taxonomies.', 'postscript' ) . '</li>' .
                        '<li>' . __( 'Only registered handles can be added as taxonomy terms.', 'postscript' ) . '</li>' .
                        '<li>' . __( 'Post-counts for terms track which posts enqueue which handles.', 'postscript' ) . '</li>' .
                        '<li>' . __( 'Post-counts for terms track which posts enqueue which handles.', 'postscript' ) . '</li>' .
                        '<li>' . __( 'The <a href="#metabox">example</a> below shows how your settings effect the Edit Post screen\'s meta box.', 'postscript' ) . '</li>' .
                    '</ul>',
            )
        );
    }

    // Sidebar.
    $current_screen->set_help_sidebar(
        '<p><strong>' . __( 'Reference:', 'postscript' ) . '</strong></p>' .
        '<p><a href="https://codex.wordpress.org/Function_Reference/wp_register_script">'     . __( 'Register scripts',     'postscript' ) . '</a></p>' .
        '<p><a href="https://developer.wordpress.org/reference/functions/wp_enqueue_script/#defaults" target="_blank">' . __( 'Default scripts', 'postscript' ) . '</a></p>' .
        '<p><a href="http://hearingvoices.com/tools/postscript/">' . __( 'Postscript plugin', 'jetpack' ) . '</a></p>'
    );
}

/**
 * Renders settings menu page.
 */
function postscript_settings_display() {

    // Before rendering forms, add or remove any user-selected script and style.
    postscript_add_remove();
    ?>
    <div class="wrap">
        <h2>Postscript (v <?php echo POSTSCRIPT_VERSION; ?>) <?php _e('Settings', 'postscript' ); ?></h2>
        <!-- Create the form that will be used to render our options. -->
        <form method="post" action="options.php">
            <?php settings_fields( 'postscript' ); ?>
            <?php do_settings_sections( 'postscript' ); ?>
            <?php submit_button(); ?>
        </form>
        <!-- Render post meta box as it displays in Edit Post, based on settings. -->
        <?php postscript_meta_box_example(); ?>
    </div><!-- .wrap -->
    <?php
}

/**
 * Adds or removes any user-selected script/style in the form submission data.
 *
 * @uses  postscript_get_options() Safely gets site option.
 */
function postscript_add_remove() {
    $options = postscript_get_options();

    // Arrays of registered script handles.
    $script_handles = postscript_script_handles();
    $style_handles = postscript_style_handles();

    // Add new script or style custom tax term, if registered handle.
    if ( isset( $options['add_script'] ) && in_array( $options['add_script'], $script_handles )  ) {
        wp_insert_term( $options['add_script'], 'postscripts' );
    }

    if ( isset( $options['add_style'] ) && in_array( $options['add_style'], $style_handles )  ) {
        wp_insert_term( $options['add_style'], 'poststyles' );
    }

    // Delete custom tax term for added script or style.
    if ( ! empty( $options['remove_script'] ) && term_exists( $options['remove_script'], 'postscripts') ) {
        $script_slug = get_term_by('slug', $options['remove_script'], 'postscripts');
        $script_id = $script_slug->term_id;
        wp_delete_term( $script_id, 'postscripts' );
    }

    if ( ! empty( $options['remove_style'] ) && term_exists( $options['remove_style'], 'poststyles') ) {
        $style_slug = get_term_by( 'slug', $options['remove_style'], 'poststyles');
        $style_id = $style_slug->term_id;
        wp_delete_term( $style_id, 'poststyles' );
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
        __( 'Postcript meta box', 'postscript' ),
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
        'postscript_remove_script_style_section',
        __( 'Remove scripts and styles', 'postscript' ),
        'postscript_remove_script_style_section_callback',
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
        'postscript_add_script',
        __( '<label for="postscript-add-script">Add a Script</label>', 'postscript' ),
        'postscript_add_script_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_add_style',
        __( '<label for="postscript-add-style">Add a Style</label>', 'postscript' ),
        'postscript_add_style_callback',
        'postscript',
        'postscript_scripts_styles_section'
    );

    add_settings_field(
        'postscript_remove_script',
        __( '<label for="postscript-remove-script">Remove a Script</label>', 'postscript' ),
        'postscript_remove_script_callback',
        'postscript',
        'postscript_remove_script_style_section'
    );

    add_settings_field(
        'postscript_remove_style',
        __( '<label for="postscript-remove-style">Remove a Style</label>', 'postscript' ),
        'postscript_remove_style_callback',
        'postscript',
        'postscript_remove_script_style_section'
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
    <p><?php _e('Postscript lets you enqueue scripts and styles for a single post in the Edit Post screen.', 'postscript' ); ?></p>
    <p><?php _e('Choose the post-types and user-roles that display the Postscript box:', 'postscript' ); ?></p>
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
function postscript_remove_script_style_section_callback() {
    ?>
    <p><?php _e('Remove script or style from the Postscript box.', 'postscript' ); ?></p>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Field Callbacks (Get/Set Admin Option Array)
 * ------------------------------------------------------------------------ */
/**
 * postscript_get_options() returns:
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
 *             [urls_script] => 1
 *             [urls_style]  => 1
 *             [class_body] => on
 *             [class_post] => on
 *         )
 *
 *     [add_script]    => {style_handle}
 *     [add_style]     => {script_handle}
 *     [remove_script] => {style_handle}
 *     [remove_style]  => {script_handle}
 *     [version]       => 1.0.0
 * )
 */

/**
 * Outputs HTML checkboxes of user roles (to choose which roles display Postscript meta box).
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
            <li><label><input type="checkbox" id="<?php echo esc_attr( $role ); ?>" value="<?php echo esc_attr( $role ); ?>" name="postscript[user_roles][]"<?php checked( in_array( $role, $options['user_roles'] ) ); ?><?php disabled( 'administrator', $role ); ?> /> <?php echo esc_html( translate_user_role( $details['name'] ) ); ?></label></li>
        <?php
        }
        ?>
            <input type="hidden" value="administrator" name="postscript[user_roles][]" />
        </ul>
    </fieldset>
    <?php
}

/**
 * Outputs HTML checkboxes of post types (to choose which post-types display Postscript meta box).
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
            <li><label><input type="checkbox" id="<?php echo esc_attr( $post_type ); ?>" value="<?php echo esc_attr( $post_type ); ?>" name="postscript[post_types][]"<?php checked( in_array( $post_type, $options['post_types'] ) ); ?> /> <?php echo esc_html( $post_type_arr->labels->name ); ?></label></li>
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
            <li>
                <select id="postscript-urls-script" name="postscript[allow][urls_script]">
                    <option value="0"<?php selected( '0', isset( $opt['urls_script'] ) ? $opt['urls_script'] : '' ); ?>>0</option>
                    <option value="1"<?php selected( '1', isset( $opt['urls_script'] ) ? $opt['urls_script'] : '' ); ?>>1</option>
                    <option value="2"<?php selected( '2', isset( $opt['urls_script'] ) ? $opt['urls_script'] : '' ); ?>>2</option>
                </select> <label for="postscript-urls-script"><?php _e( 'Script URL(s)', 'postscript' ); ?></label>
            </li>
            <li>
                <select id="postscript-urls-style" name="postscript[allow][urls_style]">
                    <option value="0"<?php selected( '0', isset( $opt['urls_style'] ) ? $opt['urls_style'] : '' ); ?>>0</option>
                    <option value="1"<?php selected( '1', isset( $opt['urls_style'] ) ? $opt['urls_style'] : '' ); ?>>1</option>
                </select> <label for="postscript-urls-style"><?php _e( 'Style URL', 'postscript' ); ?></label>
            </li>
            <li><label><input type="checkbox" id="postscript-allow-class-body" name="postscript[allow][class_body]" value="on"<?php checked( 'on', isset( $opt['class_body'] ) ? $opt['class_body'] : 'off' ); ?>/> <?php _e( 'Body class*', 'postscript' ); ?></label></li>
            <li><label><input type="checkbox" id="postscript-allow-class-post" name="postscript[allow][class_post]" value="on"<?php checked( 'on', isset( $opt['class_post'] ) ? $opt['class_post'] : 'off' ); ?>/> <?php _e( 'Post class*', 'postscript' ); ?></label></li>
        </ul>
        <p class="wp-ui-text-icon"><?php _e( 'Requires <code>body_class()</code>/<code>post_class()</code> in theme.', 'postscript' ); ?></p>
    </fieldset>
    <?php
}

/**
 * Outputs HTML select menu of all registered scripts.
 *
 * @uses    postscript_script_handles() Array of registered style handles
 */
function postscript_add_script_callback() {
    // Array of alphabetized front-end registered script handles.
    $script_handles = postscript_script_handles();

    // Output HTML select menu of (sorted) registered script handles.
    ?>
    <select id="postscript-add-script" name="postscript[add_script]">
        <option value=''><?php _e( 'Select script to add:', 'postscript' ); ?></option>
        <?php
        foreach( $script_handles as $script_handle ) {
            $handle =  esc_attr( $script_handle );
            echo "<option value=\"{$handle}\">{$handle}</option>";
        }
        ?>
    </select>
    <?php
    // Get user-selected script handles (stored as tax terms).
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $scripts_added = get_terms( 'postscripts', $args );

    // Display table of selected handles (with $wp_scripts data and term's post count).
    ?>
    <table class="wp-list-table widefat striped">
        <caption><strong><?php _e( 'Scripts added', 'postscript' ); ?></strong></caption>
        <thead>
            <tr>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Handle', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Ver', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Deps', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Footer', 'postscript' ); ?></th>
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
                $script_name  = esc_html( $script_obj->name );
                $script_arr   = $postscript_scripts_reg[ $script_name ];

                // Comma-separated list of style dependencies.
                $deps = implode( ', ', $script_arr->deps );

                // Make relative URLs full (for core registered scripts in '/wp-admin' or '/wp-includes').
                $src = ( $script_arr->src ) ? postscript_core_full_urls( $script_arr->src ) : '';

                // Add link to handle name, if registered source file.
                $handle = ( $src ) ? "<a href=\"$src\">" . $script_name . '</a>' : $script_name;

                // @todo Add status-code column. Check URL when adding script, add status to term meta.
                // Check URL status response code, if script has a 'src' set.
                // $status_code  = ( $src ) ? "<a href='$src'>" . postscript_url_exists( $src ) . '</a>' : '--';

                // Tax term post count, linked to list of posts (if count>0).
                $count  = $script_obj->count;
                if ( $count ) {
                    $posts_count_url = admin_url() . "edit.php?postscripts=$script_name\"";
                    $posts_count     = '<a href="' . esc_url_raw( $posts_count_url ) . "\">$count</a>";
                } else {
                    $posts_count = $count;
                }

                // For wp_kses() sanitization of HTML.
                $allowed_html = array(
                    'a'     => array(
                        'href' => array()
                    )
                );

                $allowed_protocols = array( 'http', 'https' );

            ?>
            <tr>
                <th scope="row" class="th-full" style="padding: 0.5em;">
                    <label><?php echo wp_kses( $handle, $allowed_html, $allowed_protocols ); ?></label>
                </th>
                <td><?php echo esc_html( $script_arr->ver ); ?></td>
                <td><?php echo esc_html( $deps ); ?></td>
                <td><?php echo esc_html( $script_arr->args ); // Style media: 'screen', 'print', or 'all'.  ?></td>
                <td><?php echo wp_kses( $posts_count, $allowed_html, $allowed_protocols ); ?></td>
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
    <p class="wp-ui-text-icon">
        <?php _e( '<strong>Handle</strong> name links to <code>src</code> file. <strong>Posts</strong> count links to a list of posts that enqueue the file.', 'postscript' ); ?><br />
    </p>
    <?php
}

/**
 * Outputs HTML select menu of all registered styles.
 *
 * @uses    postscript_style_handles()  Array of registered style handles
 */
function postscript_add_style_callback() {
    // Array of alphabetized, front-end registered script handles.
    $style_handles = postscript_style_handles();

    // Output HTML select menu of (sorted) handles.
    ?>
    <select id="postscript-add-style" name="postscript[add_style]">
        <option value=''><?php _e( 'Select style to add:', 'postscript' ); ?></option>
        <?php
        foreach( $style_handles as $style_handle ) {
            $handle =  esc_attr( $style_handle );
            echo "<option value=\"{$handle}\">{$handle}</option>";
        }
        ?>
    </select>
    <?php
    // Get user-selected style handles (stored as tax terms).
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'all',
    );
    $styles_added = get_terms( 'poststyles', $args );
    // Display table of selected handles (with $wp_styles data and term's post count).
    ?>
    <table class="wp-list-table widefat striped">
        <caption><strong><?php _e( 'Styles added', 'postscript' ); ?></strong></caption>
        <thead>
            <tr>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Handle', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Ver', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Deps', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Media', 'postscript' ); ?></th>
                <th scope="col" class="th-full" style="padding: 0.5em;"><?php _e( 'Posts', 'postscript' ); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Array of registered styles' data (the transient holds front-end $wp_styles).
    $postscript_styles_reg  = get_transient( 'postscript_styles_reg' );

    if ( ! empty( $styles_added ) ) {
        foreach ( $styles_added as $style_obj ) {
            if ( in_array( $style_obj->name, $style_handles ) ) {
                $style_name   = esc_html( $style_obj->name );
                $style_arr    = $postscript_styles_reg[ $style_name ];

                // Comma-separated list of style dependencies.
                $deps = implode( ',', $style_arr->deps );

                // Does script load in footer?
                $footer = ( isset(  $script_arr->extra['group'] ) ) ? $script_arr->extra['group'] : '';

                // Make relative URLs full (for core registered scripts in '/wp-admin' or '/wp-includes').
                $src = ( $style_arr->src ) ? postscript_core_full_urls( $style_arr->src ) : '';

                // Add link to handle name, if registered source file.
                $handle = ( $src ) ? "<a href=\"$src\">" . $style_name . '</a>' : $style_name;

                // Tax term post count, linked to list of posts (if count>0).
                $count = $style_obj->count;
                if ( $count ) {
                    $posts_count_url = admin_url() . "edit.php?poststyles=$style_name\"";
                    $posts_count     = '<a href="' . esc_url( $posts_count_url ) . "\">$count</a>";
                } else {
                    $posts_count = $count;
                }

                // For wp_kses() sanitization of HTML.
                $allowed_html = array(
                    'a'     => array(
                        'href' => array()
                    )
                );

                $allowed_protocols = array( 'http', 'https' );
            ?>
            <tr>
                <th scope="row" class="th-full" style="padding: 0.5em;">
                    <label><?php echo wp_kses( $handle, $allowed_html, $allowed_protocols ); ?></label>
                </th>
                <td><?php echo esc_html( $style_arr->ver ); ?></td>
                <td><?php echo esc_html( $deps ); ?></td>
                <td><?php echo esc_html( $footer ); ?></td>
                <td><?php echo wp_kses( $posts_count, $allowed_html, $allowed_protocols ); ?></td>
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
 * Displays all allowed post-types in post lists for plugin's custom tax term.
 *
 * Term's post-count link displays in Settings page table and tax admin screens.
 *
 * @uses  postscript_get_options()  Safely gets site option.
 */
function postscript_pre_get_posts( $query ) {
    $options = postscript_get_options();
    if ( is_admin() ) {
        if ( get_query_var( 'postscripts' ) || get_query_var( 'postscripts' ) ) {
            $query->set('post_type', 'any' ); // Hack: to get all post-type to display for term.

            // $query->set('post_type', $options['post_types'] ); // Use this when fixed:
            // https://core.trac.wordpress.org/ticket/30013
            // /wp-admin/edit.php doesn't accept array (Error: array to string conversion)
        }
    }
}
add_action( 'pre_get_posts', 'postscript_pre_get_posts' );

/**
 * Adds an Admin Notice to the tax-term post list screen with the term.
 * (I.e., screen: /edit.php?postscripts={term})
 */
function postscript_tax_term_screen( $query ) {
    if ( is_admin() ) {
        if ( get_query_var( 'postscripts' ) || get_query_var( 'postscripts' ) ) {
            if ( get_query_var( 'postscripts' ) ) {
                $term = get_query_var( 'postscripts' );
            } elseif ( get_query_var( 'poststyles' ) ) {
                $term = get_query_var( 'poststyles' );
            } else {
                $term = '';
            }
        ?>
        <div class="notice notice-info is-dismissible">
            <p><?php _e( 'These posts use Postscript to enqueue the handle: "', 'postscript' ); ?><?php echo esc_html( $term ); ?>".</p>
        </div>
        <?php
        }
    }
}
add_action( 'admin_notices', 'postscript_tax_term_screen' );

/**
 * Outputs HTML select menu of all registered scripts.
 */
function postscript_remove_script_callback() {
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'ids',
    );
    $scripts_added = get_terms( 'postscripts', $args );

    if ($scripts_added ) {
        $args = array(
            'taxonomy'          => 'postscripts',
            'orderby'           => 'name',
            'name'              => 'postscript[remove_script]',
            'option_none_value' => '',
            'show_option_none'  => __( 'Select script to remove:', 'postscript' ),
            'show_count'        => 1,
            'hide_empty'        => 0,
            'value_field'       => 'name',
            'id'                => 'postscript-remove-script',
        );
        ?>
        <ul class="clear">
            <?php wp_dropdown_categories( $args ); ?>
        </ul>
        <?php
    } else {
        ?>
        <p><?php _e( 'Use <strong>Add a Script</strong> dropdown above to add registered scripts.', 'postscript' ); ?></p>
        <?php
    }
}


/**
 * Outputs HTML select menu of all registered style (tax term).
 */
function postscript_remove_style_callback() {
    $args = array(
        'hide_empty'             => false,
        'fields'                 => 'ids',
    );
    $styles_added = get_terms( 'poststyles', $args );

    if ($styles_added ) {
        $args = array(
            'taxonomy'          => 'poststyles',
            'orderby'           => 'name',
            'name'              => 'postscript[remove_style]',
            'option_none_value' => '',
            'show_option_none'  => __( 'Select style to remove:', 'postscript' ),
            'show_count'        => 1,
            'hide_empty'        => 0,
            'value_field'       => 'name',
            'id'                => 'postscript-remove-style',
        );
        ?>
        <ul class="clear">
            <?php wp_dropdown_categories( $args ); ?>
        </ul>
        <?php
    } else {
        ?>
        <p><?php _e( 'Use <strong>Add a Style</strong> dropdown above to add registered styles.', 'postscript' ); ?></p>
        <?php
    }
}

/**
 * Render example of Edit Post screen meta box, based on settings using post's meta box fn.
 *
 * @see   /includes/meta-box.php
 * @uses  postscript_get_options()  Safely gets site option
 */
function postscript_meta_box_example() {
    $options     = postscript_get_options( 'postscript' );
    $box['args'] = $options; // Meta box stores $options as an ['args'] array.
    $fake_post   = (object) array( 'ID' => '-1'); // Meta box needs a post object id.
    ?>
    <hr />
    <h2 id="metabox"><?php _e('Postscript box example', 'postscript' ); ?></h2>
    <p>
        <?php _e('This meta box displays on the Edit Post screen:', 'postscript' ); ?><br />
        <?php _e('&bull; For user-role(s): ', 'postscript' ); ?><?php echo implode( $options['user_roles'], ', ' ); ?><br />
        <?php _e('&bull; For post-type(s): ', 'postscript' ); ?><?php echo implode( $options['post_types'], ', ' ); ?>
    <p>

    <div id="postscript-meta" class="postbox-container">
        <div id="categorydiv" class="postbox ">
        <h2 class="hndle ui-sortable-handle"><span>Postscript</span></h2>
            <div class="inside">
                <?php postscript_meta_box_callback( $fake_post, $box ); ?>
            </div><!-- .inside -->
        </div><!-- .postbox -->
    </div><!-- .postbox-container -->

    <p class="clear wp-ui-text-icon"><?php echo get_num_queries(); ?><?php _e(" queries in ", 'postscript'); ?><?php timer_stop( 1 ); ?><?php _e(" seconds uses ", 'postscript'); ?><?php echo size_format( memory_get_peak_usage(), 2); ?> <?php _e(" peak memory", 'postscript'); ?>. The top-right <a href="#contextual-help-link">Help tab</a> has details on Postscript features. <?php _e( 'This plugin created as part of a <a href="https://www.rjionline.org/stories/series/storytelling-tools/">Reynold Journalism Institute</a> fellowship.', 'mexpplus' ); ?></p>
    <?php
}
