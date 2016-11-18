<?php
/**
 * Automatic Featured Images From Videos
 *
 * Meta Box Logic
 *
 * @since   1.1.0
 * @package Plugin Name: Automatic Featured Images from YouTube / Vimeo
 * @author  Gary Kovar
 */

die
add_action( 'add_meta_boxes', 'wds_register_display_video_metabox' );

/**
 * Register a metabox to display the video on post edit view.
 * @author Gary Kovar
 * @since 1.1.0
 */
function wds_register_display_video_metabox() {
	global $post;

	if ( get_post_meta( $post->ID, '_is_video', true ) ) {
		add_meta_box(
			'wds_display_video_metabox',
			__( 'My Meta Box', 'wds-automatic-featured-images-from-video' ),
			'wds_video_thumbnail_meta'
		);
	}
}

/**
 * Populate the metabox.
 * @author Gary Kovar
 * @since 1.1.0
 */
function wds_video_thumbnail_meta() {
	echo "I WOULD DIE FOR RILEY";
}