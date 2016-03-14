<?php

/**
 * The post editor functionality of the plugin.
 *
 * @link       http://hearingvoices.com/tools/
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Meta Box for the Edit screen.
 * ------------------------------------------------------------------------ */

/**
 * Removes default display of plugin's custom tax checkboxes.
 * (Replaced by plugin's custom meta box.)
 */
function postscript_remove_meta_boxes() {
    $options = postscript_get_options( 'postscript' );

    foreach ( $options['post_types'] as $post_type ) {
        remove_meta_box( 'postscript_scriptsdiv', $post_type, 'normal' );
        remove_meta_box( 'postscript_stylesdiv', $post_type, 'normal' );
    }
}
add_action( 'admin_menu' , 'postscript_remove_meta_boxes' );



// $postscript_options = get_option( 'postscript' );


/* ------------------------------------------------------------------------ *
 * Meta Box for Post Editor
 * ------------------------------------------------------------------------ */

/**
 * Displays meta box on post editor screen (both new and edit pages).
 */
function postscript_meta_box_setup() {
    $options = postscript_get_options( 'postscript' );

    $user = wp_get_current_user();
    $roles_allowed = $options['user_roles'];

    if ( array_intersect( $roles_allowed, $user->roles ) ) {
        /* Add meta boxes on the 'add_meta_boxes' hook. */
        add_action( 'add_meta_boxes', 'postscript_add_meta_box' );

        /* Save post meta on the 'save_post' hook. */
        add_action( 'save_post', 'postscript_save_post_meta', 10, 2 );
    }
}
add_action( 'load-post.php', 'postscript_meta_box_setup' );
add_action( 'load-post-new.php', 'postscript_meta_box_setup' );

/**
 * Creates meta box for the post editor screen.
 */
function postscript_add_meta_box() {
    $options = postscript_get_options( 'postscript' );

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
 * Callback function for add_meta_box() -- params below.
 * Used by postscript_add_post_meta_boxes().
 * @param object $post Object containing the current post
 * @param array $box Array of meta box id, title, callback, and args elements
 * @return string HTML of meta box
 */
function postscript_meta_box_callback( $post, $box ) {
    $post_id = $post->ID;
    // Print checklist of admin-settings selected styles and scripts (custom tax terms), with checked on top.
    ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'postscript_meta_nonce' ); ?>
    <?php if ( get_terms( 'postscript_scripts', array( 'hide_empty' => false ) ) ) { ?>
    <p>
        <h3 class="hndle"><span><?php _e('Load Scripts', 'postscript' ); ?></span></h3>
        <ul id="postscript_styleschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscript_scripts', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <?php } ?>
    <?php if ( get_terms( 'postscript_styles', array( 'hide_empty' => false ) ) ) { ?>
    <p>
        <h3 class="hndle"><span><?php _e('Load Styles', 'postscript' ); ?></span></h3>
        <ul id="postscript_scriptschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscript_styles', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <?php } ?>
    <?php
    // Display text fields for: URLs (style and script) and classes (body and post).
    $opt_allow = $box['args']['allow'];
    $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );
    ?>
    <?php if ( isset ( $opt_allow['url_style'] ) ) { ?>
    <p>
        <label for="postscript-url-style"><?php _e( 'CSS stylesheet URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_style]" id="postscript-url-style" value="<?php if ( isset ( $postscript_meta['url_style'] ) ) { echo esc_url_raw( $postscript_meta['url_style'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['url_script'] ) ) { ?>
    <p>
        <label for="postscript-url-script"><?php _e( 'JS file URL 1:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_script]" id="postscript-url-script" value="<?php if ( isset ( $postscript_meta['url_script'] ) ) { echo esc_url_raw( $postscript_meta['url_script'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['url_script_2'] ) ) { ?>
    <p>
        <label for="postscript-url-script-2"><?php _e( 'JS file URL 2:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_script_2]" id="postscript-url-script-2" value="<?php if ( isset ( $postscript_meta['url_script_2'] ) ) { echo esc_url_raw( $postscript_meta['url_script_2'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['class_body'] ) ) { ?>
    <p>
        <label for="postscript-class-body"><?php _e( 'Body class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_body]" id="postscript-class-body" value="<?php if ( isset ( $postscript_meta['class_body'] ) ) { echo sanitize_html_class( $postscript_meta['class_body'] ); } ?>" size="30" />
    </p>
    <?php } ?>
    <?php if ( isset ( $opt_allow['class_post'] ) ) { ?>
    <p>
        <label for="postscript-class-post"><?php _e( 'Post class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_post]" id="postscript-class-post" value="<?php if ( isset ( $postscript_meta['class_post'] ) ) { echo sanitize_html_class( $postscript_meta['class_post'] ); } ?>" size="30" />
    </p>
    <?php
    // print_r($box);
    }
}

/**
 * Saves the meta box form data.
 */
function postscript_save_post_meta( $post_id, $post ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'postscript_meta_nonce' ] ) && wp_verify_nonce( $_POST[ 'postscript_meta_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
        return $post_id;
    }

    /* Get and sanitize the posted data. */
    $new_meta_value = ( isset( $_POST['postscript_meta'] ) ?  $_POST['postscript_meta'] : '' );
    if ( $new_meta_value ) {
        $new_meta_value['url_style']  = esc_url_raw( $new_meta_value['url_style'] );
        $new_meta_value['url_script'] = esc_url_raw( $new_meta_value['url_script'] );
        $new_meta_value['url_script_2']   = esc_url_raw( $new_meta_value['url_script_2'] );
        $new_meta_value['class_body'] = sanitize_html_class( $new_meta_value['class_body'] );
        $new_meta_value['class_post'] = sanitize_html_class( $new_meta_value['class_post'] );
    }

    $meta_key = 'postscript_meta';
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && '' == $meta_value ) {
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    } elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
        update_post_meta( $post_id, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    } elseif ( '' == $new_meta_value && $meta_value ) {
        delete_post_meta( $post_id, $meta_key, $meta_value );
    }

    if ( isset( $_POST['tax_input'] ) ) {
    // Convert array values to integers.
    $style_ids  =  array_map ( 'intval', $_POST['tax_input']['postscript_ styles'] );
    $script_ids =  array_map ( 'intval', $_POST['tax_input']['postscript_scripts'] );
        wp_set_object_terms( $post_id, $style_ids, 'postscript_styles', false );
        wp_set_object_terms( $post_id, $script_ids, 'postscript_scripts', false );
    }

/*
    if ( isset( $_POST['tax_input'] ) ) {
        wp_set_object_terms( $post_id, $_POST['tax_input']['postscript_scripts'], 'postscript_scripts', false );
        wp_set_object_terms( $post_id, $_POST['tax_input']['postscript_styles'], 'postscript_styles', false );
    }
*/
}
