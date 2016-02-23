<?php

/**
 * The post editor functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Postscript
 * @subpackage Postscript/includes
 */

/* ------------------------------------------------------------------------ *
 * Get Settings Options
 * ------------------------------------------------------------------------ */

// $postscript_options = get_option( 'postscript' );


/* ------------------------------------------------------------------------ *
 * Meta Box for Post Editor
 * ------------------------------------------------------------------------ */

/**
 * Displays meta box on post editor screen (both new and edit pages).
 */
function postscript_meta_box_setup() {

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'postscript_add_meta_box' );

    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'postscript_save_post_meta', 10, 2 );
}
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'postscript_meta_box_setup' );
add_action( 'load-post-new.php', 'postscript_meta_box_setup' );

/**
 * Creates meta box for the post editor screen.
 */
function postscript_add_meta_box() {
    add_meta_box(
        'postscript-meta',
        esc_html__( 'Postscript', 'postscript' ),
        'postscript_meta_box_callback',
        'post',
        'side',
        'default'
    );
}

function remove_post_custom_fields() {
        remove_meta_box( 'postscript_scriptsdiv', 'post', 'normal' );
        remove_meta_box( 'postscript_stylesdiv', 'post', 'normal' );
}
add_action( 'admin_menu' , 'remove_post_custom_fields' );

/**
 * Builds HTML form for the post meta box.
 *
 * Callback function for add_meta_box() -- params below.
 * Used by postscript_add_post_meta_boxes().
 * @param object $post Object containing the current post
 * @param array $box Array of metabox id, title, callback, and args elements
 * @return string HTML of meta box
 */
function postscript_meta_box_callback( $post, $box ) {

    $post_id = get_the_ID();
    $postscript_meta = get_post_meta( $post_id, 'postscript_meta', true );
    $postscript_meta_class_post = get_post_meta( $post_id, 'postscript_meta_class_post', true );
    ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'postscript_meta_nonce' ); ?>
    <p>
        <h3 class="hndle"><span><?php _e('Load Scripts', 'postscript' ); ?></span></h3>
        <ul id="postscript_styleschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscript_scripts', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <p>
        <h3 class="hndle"><span><?php _e('Load Styles', 'postscript' ); ?></span></h3>
        <ul id="postscript_scriptschecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscript_styles', 'selected_cats' => true, 'checked_ontop' => true ) ); ?>
        </ul>
    </p>
    <p>
        <label for="postscript-url-style"><?php _e( 'CSS stylesheet URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_style]" id="postscript-url-style" value="<?php if ( isset ( $postscript_meta['url_style'] ) ) { echo esc_url_raw( $postscript_meta['url_style'] ); } ?>" size="30" />
    </p>
    <p>
        <label for="postscript-url-script"><?php _e( 'JS file URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_script]" id="postscript-url-script" value="<?php if ( isset ( $postscript_meta['url_script'] ) ) { echo esc_url_raw( $postscript_meta['url_script'] ); } ?>" size="30" />
    </p>
    <p>
        <label for="postscript-url-data"><?php _e( 'JSON/data file URL:', 'postscript' ); ?></label><br />
        <input class="widefat" type="url" name="postscript_meta[url_data]" id="postscript-url-data" value="<?php if ( isset ( $postscript_meta['url_data'] ) ) { echo esc_url_raw( $postscript_meta['url_data'] ); } ?>" size="30" />
    </p>
    <p>
        <label for="postscript-class-body"><?php _e( 'Body class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_body]" id="postscript-class-body" value="<?php if ( isset ( $postscript_meta['class_body'] ) ) { echo sanitize_html_class( $postscript_meta['class_body'] ); } ?>" size="30" />
    </p>
    <p>
        <label for="postscript-class-post"><?php _e( 'Post class:', 'postscript' ); ?></label><br />
        <input class="widefat" type="text" name="postscript_meta[class_post]" id="postscript-class-post" value="<?php if ( isset ( $postscript_meta['class_post'] ) ) { echo sanitize_html_class( $postscript_meta['class_post'] ); } ?>" size="30" />
    </p>
<?php
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
    $new_meta_value['url_style']  = esc_url_raw( $new_meta_value['url_style'] );
    $new_meta_value['url_script'] = esc_url_raw( $new_meta_value['url_script'] );
    $new_meta_value['url_data']   = esc_url_raw( $new_meta_value['url_data'] );
    $new_meta_value['class_body'] = sanitize_html_class( $new_meta_value['class_body'] );
    $new_meta_value['class_post'] = sanitize_html_class( $new_meta_value['class_post'] );

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
        wp_set_object_terms( $post_id, $_POST['tax_input']['postscript_scripts'], $postscript_scripts, false );
        wp_set_object_terms( $post_id, $_POST['tax_input']['postscript_styles'], $postscript_styles, false );
    }

}
