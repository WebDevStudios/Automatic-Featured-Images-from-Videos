<?php
/**
 * Created by PhpStorm.
 * User: garykovar
 * Date: 11/21/16
 * Time: 10:51 PM
 */

/**
 * Add a bulk-process button.
 *
 * @author Gary Kovar
 *
 * @since  1.1.0
 */
function wds_customize_post_buttons() {

	// Register the script we might use.
	wp_register_script( 'wds_featured_images_from_video_script', WDSAFI_DIR . 'js/button.js' );

	global $post_type;

	// Allow developers to pass in custom CPTs to process.
	$type_array = apply_filters( 'wds_featured_images_from_video_post_types', array( 'post', 'page' ) );

	if ( is_array( $type_array ) && in_array( $post_type, $type_array ) ) {
		$args = array(
			'post_type'       => $post_type,
			'status'          => wds_featured_images_from_video_processing_status( $post_type ),
			'processing_text' => wds_featured_images_from_video_processing_current_disposition(),
			'bulk_text'       => esc_html__( 'Bulk add video thumbnails', 'wds_automatic_featured_images_from_videos' ),
		);

		wp_localize_script( 'wds_featured_images_from_video_script', 'wds_featured_image_from_vid_args', $args );
		wp_enqueue_script( 'wds_featured_images_from_video_script' );

	}
}

/**
 * Return a status on what to do about the button.
 *
 * @since 1.1.0
 *
 * @param string $post_type Post type to check process for.
 * @return string
 */
function wds_featured_images_from_video_processing_status( $post_type ) {

	// Check if the bulk task has already been scheduled.
	if ( wp_next_scheduled( 'wds_bulk_process_video_query_init', array( $post_type ) ) ) {
		return 'running';
	}

	// Check if we have any to process.
	$query = wds_automatic_featured_images_from_videos_wp_query( $post_type, apply_filters( 'wds_featured_images_from_video_posts_bulk_quantity', 10 ) );

	if ( $query->post_count > 1 ) {
		return 'ready_to_process';
	}

	return 'do_not_process';

}

/**
 * Return actual processing for specific post.
 *
 * @return string|void
 */
function wds_featured_images_from_video_processing_current_disposition() {
	return esc_html__( 'Processing...', 'wds_automatic_featured_images_from_videos' );
}
