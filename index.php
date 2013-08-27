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
function nsync_display_meta_box( $post, $args ) {
    wp_nonce_field( 'bye-line-save', 'nsync_bye_line_noncename' );
    $value = get_post_meta( $post->ID, 'byebyebye_lines', true );
?>
    <p>
        <label for="byeline">
            <?php _e( 'Bye Bye Bye Line', 'byebyebye_lines' ); ?>:&nbsp;
        </label>
            <input type="text" class="widefat" name="byeline" value="<?php echo esc_attr($value);?>"/>
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
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }

    if ( 'page' === $_POST[ 'post_type' ] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    if ( ! isset( $_POST['byeline'] ) ) {
        return;
    }

    //Help from Kyle Riemensnider
    if ( !isset($_POST['nysnc_bye_line_noncename']) || ! wp_verify_nonce( $_POST['nysnc_bye_line_noncename'], 'bye-line-save') ){
        return;
    }    


    //More Help from Kyle Riemensnider
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
/*
    Five Errors Found:
                        1. Function Names were not prefixed
                        2. Function nsync_save_meta_box does not check for autosave
                        3. Sanitizations were needed in the plugin
                        4. Added esc_attr to return value of byebyebye_lines
                        5. Added nonce to nsync_display_meta_box


Since I am not the best with PHP I did recieve help from Kyle Riemensnider on the errors that I could not find on my own. 
I will say that you do cover all of these best practices in each lecture, so I found your notes on github and the codex
to be extremely helpful.
*/
add_filter( 'the_content', 'nysnc_print_byebyebye_line' );