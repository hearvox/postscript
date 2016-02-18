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
function postscript_post_meta_box_setup() {

    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'postscript_add_post_meta_box' );

    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'postscript_save_post_meta', 10, 2 );
}
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'postscript_post_meta_box_setup' );
add_action( 'load-post-new.php', 'postscript_post_meta_box_setup' );

/**
 * Creates meta box for the post editor screen.
 */
function postscript_add_post_meta_box() {

    add_meta_box(
        'postscript-post-class',      // Unique ID
        esc_html__( 'Add Script and Style', 'post-scripting' ),    // Title
        'postscript_post_meta_box_callback',   // Callback function
        'post',         // Admin page (or post type)
        'side',         // Context
        'default'         // Priority
    );
}

/**
 * Builds HTML form for the post meta box.
 *
 * Callback function for add_meta_box() -- params below.
 * Used by postscript_add_post_meta_boxes().
 * @param object $post Object containing the current post
 * @param array $box Array of metabox id, title, callback, and args elements
 * @return string HTML of meta box
 */
function postscript_post_meta_box_callback( $post, $box ) {
    $post_id = $post->ID;
    // $postscript_style_handles = get_post_meta( $post_id, 'postscript_style_handles', true );
    $postscript_style_url = get_post_meta( $post_id, 'postscript_style_url', true );
    // $postscript_script_handles = get_post_meta( $post_id, 'postscript_script_handles', true );
    $postscript_script_url = get_post_meta( $post_id, 'postscript_script_url', true );

?>
    <?php wp_nonce_field( basename( __FILE__ ), 'postscript_post_enqueue_nonce' ); ?>
    <p>
        <h3 class="hndle"><span><?php _e('Styles', 'postscript' ); ?></span></h3>
        <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">
        <?php
        $term_list = wp_get_post_terms( $post_id, 'postscript_styles', array( 'fields' => 'ids' ) );
        ?>
            <?php wp_terms_checklist( $post_id, array( 'taxonomy' => 'postscript_styles', 'selected_cats' => array( $term_list ) ) ); ?>
        </ul>
        <label for="postscript-style-url"><?php _e( 'Enter URL of CSS stylesheet:', 'post-scripting' ); ?></label><br />
        <input class="widefat" type="url" name="postscript-style-url" id="postscript-style-url" value="<?php if ( isset ( $postscript_style_url ) ) { echo esc_attr( $postscript_style_url ); } ?>" size="30" />
    </p>
    <p>
        <label for="postscript-script-url"><?php _e( 'Enter URL of JS file:', 'post-scripting' ); ?></label><br />
        <input class="widefat" type="url" name="postscript-script-url" id="postscript-script-url" value="<?php if ( isset ( $postscript_script_url ) ) { echo esc_attr( $postscript_script_url ); } ?>" size="30" />
    </p>

    <pre>
        <code>$term_list</code>:
        <?php print_r( $term_list );  ?>

    </pre>
    <hr />
<?php
}

/**
 * Saves the meta box form data.
 */
function postscript_save_post_meta( $post_id, $post ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'postscript_post_enqueue_nonce' ] ) && wp_verify_nonce( $_POST[ 'postscript_post_enqueue_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

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

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value_style = ( isset ( $_POST['postscript-style-url'] ) ? esc_url_raw( $_POST['postscript-style-url'] ) : '' );
    $new_meta_value_script = ( isset ( $_POST['postscript-script-url'] ) ? esc_url_raw( $_POST['postscript-script-url'] ) : '' );

    /* Get the meta key. */
    $meta_key_style = 'postscript_style_url';
    $meta_key_script = 'postscript_script_url';

    /* Get the meta value of the custom field key. */
    $meta_value_style = get_post_meta( $post_id, $meta_key_style, true );
    $meta_value_script = get_post_meta( $post_id, $meta_key_script, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value_style && '' == $meta_value_style ) {
        add_post_meta( $post_id, $meta_key_style, $new_meta_value_style, true );
    } /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value_style && $new_meta_value_style != $meta_value_style ){
        update_post_meta( $post_id, $meta_key_style, $new_meta_value_style );
    } /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value_style && $meta_value_style ) {
        delete_post_meta( $post_id, $meta_key_style, $meta_value_style );
    }

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value_script && '' == $meta_value_script ) {
        add_post_meta( $post_id, $meta_key_script, $new_meta_value_script, true );
    } /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value_script && $new_meta_value_script != $meta_value_script ){
        update_post_meta( $post_id, $meta_key_script, $new_meta_value_script );
    } /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value_script && $meta_value_script ) {
        delete_post_meta( $post_id, $meta_key_script, $meta_value_script );
    }

}
