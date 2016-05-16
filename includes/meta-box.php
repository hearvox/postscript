<?php

/**
 * The post editor functionality of the plugin.
 *
 * @link    http://hearingvoices.com/tools/
 * @since   0.1.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Meta Box for the Post Edit screen.
 * ------------------------------------------------------------------------ */

/**
 * Displays meta box on post editor screen (both new and edit pages).
 */
function postscript_meta_box_setup() {
    $options = postscript_get_options();
    $user    = wp_get_current_user();
    $roles   = $options['user_roles'];

    // Add meta boxes only for allowed user roles.
    if ( array_intersect( $roles, $user->roles ) ) {
        // Add meta box.
        add_action( 'add_meta_boxes', 'postscript_add_meta_box' );

        // Save post meta.
        add_action( 'save_post', 'postscript_save_post_meta', 10, 2 );
    }
}
add_action( 'load-post.php', 'postscript_meta_box_setup' );
add_action( 'load-post-new.php', 'postscript_meta_box_setup' );

/**
 * Creates meta box for the post editor screen.
 *
 * Passes array of user-setting options to callback.
 *
 * @uses postscript_get_options()   Safely gets option from database.
 */
function postscript_add_meta_box() {
    $options = postscript_get_options();

    add_meta_box(
        'postscript-meta',
        esc_html__( 'Postscript', 'postscript' ),
        'postscript_meta_box_callback',
        $options['post_types'],
        'side',
        'default',
        $options
    );
}

/**
 * Builds HTML form for the post meta box.
 *
 * Form elements are checkboxes to select script/style handles (stored as tax terms),
 * and text fields for entering body/post classes (stored in same post-meta array).
 *
 * Form elements are printed only if allowed on Setting page.
 * Callback function passes array of settings-options in args ($box):
 *
 * postscript_get_options() returns:
 * Array
 * (   // Settings used by meta-box:
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
 *     // Not used by meta-box:
 *     [add_script]    => {style_handle}
 *     [add_style]     => {script_handle}
 *     [remove_script] => {style_handle}
 *     [remove_style]  => {script_handle}
 *     [version]       => 1.0.0
 * )
 *
 * get_post_meta( $post_id, 'postscript_meta', true ) returns:
 * Array
 * (
 *     [url_style] => http://example.com/my-post-style.css
 *     [url_script] => http://example.com/my-post-script.js
 *     [url_script_2] => http://example.com/my-post-script-2.js
 *     [class_body] => my-post-body-class
 *     [class_post] => my-post-class
 * )
 * @param  Object $post Object containing the current post.
 * @param  array  $box  Array of meta box id, title, callback, and args elements.
 */
function postscript_meta_box_callback( $post, $box ) {
    $post_id = $post->ID;
    // Print checklist of selected styles and scripts (custom tax terms), with checked on top.
    ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'postscript_meta_nonce' ); ?>
    <?php if ( get_terms( 'poststyles', array( 'hide_empty' => false ) ) ) { ?>
    <p>
        <h3 class="hndle"><span><?php _e('Load Styles', 'postscript' ); ?></span></h3>
        <ul id="poststyleschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'poststyles', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <hr />
    <?php } ?>
    <?php if ( get_terms( 'postscripts', array( 'hide_empty' => false ) ) ) { ?>
    <p>
        <h3 class="hndle"><span><?php _e('Load Scripts', 'postscript' ); ?></span></h3>
        <ul id="postscriptschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscripts', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <hr />
    <?php } ?>
    <?php
    // Display text fields for: URLs (style/script) and classes (body/post).
    $opt_allow = $box['args']['allow'];
    $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );
    ?>
    <?php if ( isset ( $opt_allow['urls_style'] ) && 1 === intval( $opt_allow['urls_style'] )  ) { // Admin setting allows style URL text field. ?>
    <p>
        <label for="postscript-url-style"><?php _e( 'CSS stylesheet URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_style]" id="postscript-url-style" value="<?php if ( isset ( $postscript_meta['url_style'] ) ) { echo esc_url_raw( $postscript_meta['url_style'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['urls_script'] ) ) { // Admin setting allows script URL text field. ?>
        <?php $urls_script = intval( $opt_allow['urls_script'] ); ?>
        <?php if ( $urls_script ) { ?>
    <p>
        <label for="postscript-url-script"><?php _e( 'JavaScript URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_script]" id="postscript-url-script" value="<?php if ( isset ( $postscript_meta['url_script'] ) ) { echo esc_url_raw( $postscript_meta['url_script'] ); } ?>" size="30" />
    </p>
        <?php } ?>
        <?php if ( 2 === $urls_script ) { // Admin setting allows second script URL text field. ?>
    <p>
        <label for="postscript-url-script-2"><?php _e( 'JavaScript URL 2:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_script_2]" id="postscript-url-script-2" value="<?php if ( isset ( $postscript_meta['url_script_2'] ) ) { echo esc_url_raw( $postscript_meta['url_script_2'] ); } ?>" size="30" />
    </p>
        <?php } ?>
    <?php } ?>
    <?php if ( isset ( $opt_allow['class_body'] ) || isset ( $opt_allow['class_post'] ) ) { // Whether to print <hr>.?>
    <hr />
    <?php } ?>
    <?php if ( isset ( $opt_allow['class_body'] ) ) { // Admin setting allows body_class() text field. ?>
    <p>
        <label for="postscript-class-body"><?php _e( 'Body class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_body]" id="postscript-class-body" value="<?php if ( isset ( $postscript_meta['class_body'] ) ) { echo sanitize_html_class( $postscript_meta['class_body'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['class_post'] ) ) { // Admin setting allows post_class() text field. ?>
    <p>
        <label for="postscript-class-post"><?php _e( 'Post class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_post]" id="postscript-class-post" value="<?php if ( isset ( $postscript_meta['class_post'] ) ) { echo sanitize_html_class( $postscript_meta['class_post'] ); } ?>" size="30" />
    </p>
    <?php
    }
}

/**
 * Saves the meta box form data upon submission.
 *
 * @uses  postscript_sanitize_array()    Sanitizes $_POST array.
 *
 * @param int     $post_id    Post ID.
 * @param WP_Post $post       Post object.
 */
function postscript_save_post_meta( $post_id, $post ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'postscript_meta_nonce' ] ) && wp_verify_nonce( $_POST[ 'postscript_meta_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
        return;
    }

    // Get the post type object (to match with current user capability).
    $post_type = get_post_type_object( $post->post_type );

    // Check if the current user has permission to edit the post.
    if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
        return $post_id;
    }

    $meta_key   = 'postscript_meta';
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    // If any user-submitted form fields have a value.
    // implode() reduces array to string of values, a check for any values.
    if  ( isset( $_POST['postscript_meta'] ) && implode( $_POST['postscript_meta'] ) ) {
        $form_data  = postscript_sanitize_array( $_POST['postscript_meta'] );
    } else {
        $form_data  = null;
    }

    // $form_data  = ( isset( $_POST['postscript_meta'] ) && implode( $_POST['postscript_meta'] ) ) ? $_POST['postscript_meta'] : null;

    // Add post-meta, if none exists, and if user entered new form data.
    if ( $form_data && '' == $meta_value ) {
        add_post_meta( $post_id, $meta_key, $form_data, true );

    // Update post-meta if user changed existing post-meta values in form.
    } elseif ( $form_data && $form_data != $meta_value ) {
        update_post_meta( $post_id, $meta_key, $form_data );

    // Delete existing post-meta if user cleared all post-meta values from form.
    } elseif ( null == $form_data && $meta_value ) {
        delete_post_meta( $post_id, $meta_key );

    // Any other possibilities?
    } else {
        return;
    }

    if ( isset( $_POST['tax_input'] ) ) {
    // Convert array values (term IDs) from number strings to integers.
        if ( isset( $_POST['tax_input']['postscript_styles'] ) && is_array( $_POST['tax_input']['postscript_styles'] ) ) {
            $style_ids  =  array_map ( 'intval', $_POST['tax_input']['postscript_styles'] );
            wp_set_object_terms( $post_id, $style_ids, 'postscripts', false );
        }

        if ( isset( $_POST['tax_input']['postscript_scripts'] ) && is_array( $_POST['tax_input']['postscript_scripts'] ) ) {
            $script_ids  =  array_map ( 'intval', $_POST['tax_input']['postscript_scripts'] );
            wp_set_object_terms( $post_id, $script_ids, 'postscripts', false );
        }
    }

}
