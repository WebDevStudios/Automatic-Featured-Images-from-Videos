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
 *     wp video-thumbnail --post-type=post
 *
 * @when after_wp_load
 */
function wds_video_thumnbail_cli( $args, $assoc_args ) {
	if ( isset( $args[0] ) ) {
		$post_type = $args[0];
	} else {
		WP_CLI::error( 'post type must be set', true );
	}

	$query = wds_automatic_featured_images_from_videos_wp_query( $post_type, -1 );

	// Process these jokers.
	foreach ( $query->posts as $post_id ) {
		wds_check_if_content_contains_video( $post_id, get_post( $post_id ) );
	}
}