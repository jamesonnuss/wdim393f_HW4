<?php
/*
Plugin Name: Bye Bye Bye Lines
Plugin URI:  http://www.nsync.com/
Description: Display a byline at the end of a post, making it a Bye bye bye line.
Version:     1.0
Author:      'N Sync
Author URI:  http://www.nsync.com/
License:     GPLv2 or later
*/

/**
 * Set up the metabox.
 *
 * @param  string    $post_type    The post type.
 * @param  object    $post         The current post object.
 * @return void
 */
function nysnc_call_meta_box( $post_type, $post ) {
    //Registers the metabox for display
    add_meta_box(
        'byebyebye_line',
        __( 'Bye Bye Bye Line', 'byebyebye_lines' ),
        'nsync_display_meta_box',
        'post',
        'side',
        'high'
    );
}

add_action( 'add_meta_boxes', 'nysnc_call_meta_box', 10, 2 );

/**
 * Display the HTML for the metabox.
 *
 * @param  object    $post    The current post object
 * @param  array     $args    Additional arguments for the metabox.
 * @return void
 */
function nysnc_display_meta_box( $post, $args ) {
wp_nonce_field( 'bye-line-save', 'nsync_bye_line_noncename' );
$value = get_post_meta( $post->ID, 'byebyebye_lines', true );
?>
    <p>
        <label for="byeline">
            <?php _e( 'Bye Bye Bye Line', 'byebyebye_lines' ); ?>:&nbsp;
        </label>
        <input type="text" class="widefat" name="byeline" value="<?php echo esc_attr($value); ?>"/>
        <em>
            <?php _e( 'HTML is not allowed', 'byebyebye_lines' ); ?>
        </em>
    </p>
<?php
}

/**
 * Save the metabox.
 *
 * @param  int       $post_id    The ID for the current post.
 * @param  object    $post       The current post object.
 */
function nysnc_save_meta_box( $post_id, $post ) {
    
    //Help from Kyle Riemensnider
    if ( ! wp_verify_nonce( $_POST['nsync_bye_line_noncename'], 'bye-line-save' ) ){
        return;
    }

    if ( ! isset( $_POST['byeline'] ) ) {
        return;
    }

    $byeline = sanitize_text_field($_POST['byeline']);
    update_post_meta( $post_id, 'byebyebye-line', $byeline );
}

add_action( 'save_post', 'nysnc_save_meta_box', 10, 2 );

/**
 * Append the Bye Bye Bye Line to the content.
 *
 * @param  string    $content    The original content.
 * @return string                The altered content.
 */
function nysnc_print_byebyebye_line( $content ) {
    $byebyebye_line = get_post_meta( get_the_ID(), 'byebyebye-line', true );
    return $content . $byebyebye_line;
}

add_filter( 'the_content', 'nysnc_print_byebyebye_line' );