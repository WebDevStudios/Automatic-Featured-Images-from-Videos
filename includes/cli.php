<?php

/**
 * Add a WP_CLI command to process all of post_type in bulk.
 *
 * @since 1.1.0
 */

WP_CLI::add_command( 'video-thumbnail', 'wds_video_thumnbail_cli' );

/**
 * Bulk processes a post type for videos included in the post content and adds a thumbnail.
 *
 * ## OPTIONS
 *
 * <post-type>
 * : Which post type to process (default is post).
 *
 * ## EXAMPLES
 *
 *     wp video-thumbnail page
 *
 * @when after_wp_load
 */
function wds_video_thumnbail_cli( $args, $assoc_args ) {
	if ( isset( $args[0] ) ) {
		$post_type = $args[0];
	} else {
		$post_type = 'post';
	}

	$query = wds_automatic_featured_images_from_videos_wp_query( $post_type, -1 );

	// Process these jokers.
	$progress = \WP_CLI\Utils\make_progress_bar( 'Processing post type: ' . $post_type , $query->post_count );
	foreach ( $query->posts as $post_id ) {
		wds_check_if_content_contains_video( $post_id, get_post( $post_id ) );
		$progress->tick();
	}
	$progress->finish();
}