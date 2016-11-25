<?php
/*
Plugin Name: Automatic Featured Images from YouTube / Vimeo
Plugin URI: http://webdevstudios.com
Description: If a YouTube or Vimeo video exists in the first few paragraphs of a post, automatically set the post's featured image to that video's thumbnail.
Version: 1.1.0
Author: WebDevStudios
Author URI: http://webdevstudios.com
License: GPLv2

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Check on save if content contains video.
add_action( 'save_post', 'wds_check_if_content_contains_video', 10, 2 );

// Add a meta box to the post types we are checking for video on.
add_action( 'add_meta_boxes', 'wds_register_display_video_metabox' );

// Create an endpoint that receives the params to start bulk processing.
add_action( 'wp_ajax_wds_queue_bulk_processing', 'wds_queue_bulk_processing' );

// Handle scheduled bulk request.
add_action( 'wds_bulk_process_video_query_init', 'wds_bulk_process_video_query' );

// Slip in the jquery to append the button for bulk processing.
add_action( 'admin_footer-edit.php', 'wds_customize_post_buttons' );

include ( plugin_dir_path( __FILE__ ) . 'includes/ajax.php' );
include ( plugin_dir_path( __FILE__ ) . 'includes/bulk-operations.php' );

/**
 * This function name is no longer accurate but it may be in use so we will leave it.
 *
 * @author     Gary Kovar
 *
 * @deprecated 1.0.5
 */
function wds_set_media_as_featured_image( $post_id, $post ) {
	wds_check_if_content_contains_video( $post_id, $post );
	_doing_it_wrong( 'wds_set_media_as_feature_image', 'This function has been replaced with wds_check_if_content_contains_video', '4.6' );
}

/**
 * Check if a post contains video.  Maybe set a thumbnail, store the video URL as post meta.
 *
 * @author Gary Kovar
 *
 * @since  1.0.5
 *
 * @param int    $post_id ID of the post being saved.
 * @param object $post    Post object.
 */
function wds_check_if_content_contains_video( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$content = isset( $post->post_content ) ? $post->post_content : '';
	// Only check the first 800 characters of our post.
	$content = substr( $content, 0, 800 );

	// Allow developers to filter the content to allow for searching in postmeta or other places.
	$content = apply_filters( 'wds_featured_images_from_video_filter_content', $content );

	// Set the video id.
	$youtube_id = wds_check_for_youtube( $content );
	$vimeo_id   = wds_check_for_vimeo( $content );

	if ( $youtube_id ) {
		$youtube_details     = wds_get_youtube_details( $youtube_id );
		$video_thumbnail_url = $youtube_details['video_thumbnail_url'];
		$video_url           = $youtube_details['video_url'];
		$video_embed_url     = $youtube_details['video_embed_url'];
	}

	if ( $vimeo_id ) {
		$vimeo_details       = wds_get_vimeo_details( $vimeo_id );
		$video_thumbnail_url = $vimeo_details['video_thumbnail_url'];
		$video_url           = $vimeo_details['video_url'];
		$video_embed_url     = $vimeo_details['video_embed_url'];
	}

	if ( $post_id
	     && ! has_post_thumbnail( $post_id )
	     && $content
	     && ( $youtube_id || $vimeo_id )
	) {
		if ( ! wp_is_post_revision( $post_id ) ) {
			wds_set_video_thumbnail_as_featured_image( $post_id, $video_thumbnail_url );
		}
	}

	if ( $post_id
	     && $content
	     && ( $youtube_id || $vimeo_id )
	) {
		update_post_meta( $post_id, '_is_video', true );
		update_post_meta( $post_id, '_video_url', $video_url );
		update_post_meta( $post_id, '_video_embed_url', $video_embed_url );
	} else {
		update_post_meta( $post_id, '_is_video', false );
		delete_post_meta( $post_id, '_video_url' );
		delete_post_meta( $post_id, '_video_embed_url' );
	}

}

/**
 * If a YouTube or Vimeo video is added in the post content, grab its thumbnail and set it as the featured image.
 *
 * @since 1.0.0
 *
 * @param string $video_thumbnail_url URL of the image thumbnail.
 */
function wds_set_video_thumbnail_as_featured_image( $post_id, $video_thumbnail_url ) {

	// If we found an image...
	$attachment_id = $video_thumbnail_url && ! is_wp_error( $video_thumbnail_url )
		// Then sideload it.
		? wds_ms_media_sideload_image_with_new_filename( $video_thumbnail_url, $post_id, sanitize_title( preg_replace( '/[^a-zA-Z0-9\s]/', '-', get_the_title() ) ) )
		// No thumbnail url found.
		: 0;

	// If attachment wasn't created, bail.
	if ( ! $attachment_id ) {
		return;
	}

	// Woot! we got an image, so set it as the post thumbnail.
	set_post_thumbnail( $post_id, $attachment_id );

}

/**
 * Check if the content contains a youtube url.
 *
 * Props to @rzen for lending his massive brain smarts to help with the regex.
 *
 * @author Gary Kovar
 *
 * @param $content
 *
 * @return string The value of the youtube id.
 *
 */
function wds_check_for_youtube( $content ) {
	if ( preg_match( '/\/\/(www\.)?(youtu|youtube)\.(com|be)\/(watch|embed)?\/?(\?v=)?([a-zA-Z0-9\-\_]+)/', $content, $youtube_matches ) ) {
		return $youtube_matches[6];
	}

	return false;
}

/**
 * Check if the content contains a vimeo url.
 *
 * Props to @rzen for lending his massive brain smarts to help with the regex.
 *
 * @author Gary Kovar
 *
 * @param $content
 *
 * @return string The value of the vimeo id.
 *
 */
function wds_check_for_vimeo( $content ) {
	if ( preg_match( '#https?://(.+\.)?vimeo\.com/.*#i', $content, $vimeo_matches ) ) {
		$id = preg_replace( "/[^0-9]/", '', $vimeo_matches[0] );

		return substr( $id, 0, 8 );
	}

	return false;
}

/**
 * Handle the upload of a new image.
 *
 * @since 1.0.0
 *
 * @param string      $url      URL to sideload.
 * @param int         $post_id  Post ID to attach to.
 * @param string|null $filename Filename to use.
 *
 * @return mixed
 */
function wds_ms_media_sideload_image_with_new_filename( $url, $post_id, $filename = null ) {

	if ( ! $url || ! $post_id ) {
		return new WP_Error( 'missing', __( 'Need a valid URL and post ID...', 'automatic-featured-images-from-videos' ) );
	}

	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	// Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp.
	$tmp = download_url( $url );

	// If error storing temporarily, unlink.
	if ( is_wp_error( $tmp ) ) {
		// Clean up.
		@unlink( $file_array['tmp_name'] );
		$file_array['tmp_name'] = '';

		// And output wp_error.
		return $tmp;
	}

	// Fix file filename for query strings.
	preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches );
	// Extract filename from url for title.
	$url_filename = basename( $matches[0] );
	// Determine file type (ext and mime/type).
	$url_type = wp_check_filetype( $url_filename );

	// Override filename if given, reconstruct server path.
	if ( ! empty( $filename ) ) {
		$filename = sanitize_file_name( $filename );
		// Extract path parts.
		$tmppath = pathinfo( $tmp );
		// Build new path.
		$new = $tmppath['dirname'] . '/' . $filename . '.' . $tmppath['extension'];
		// Renames temp file on server.
		rename( $tmp, $new );
		// Push new filename (in path) to be used in file array later.
		$tmp = $new;
	}

	/* Assemble file data (should be built like $_FILES since wp_handle_sideload() will be using). */

	// Full server path to temp file.
	$file_array['tmp_name'] = $tmp;

	if ( ! empty( $filename ) ) {
		// User given filename for title, add original URL extension.
		$file_array['name'] = $filename . '.' . $url_type['ext'];
	} else {
		// Just use original URL filename.
		$file_array['name'] = $url_filename;
	}

	$post_data = array(
		// Just use the original filename (no extension).
		'post_title'  => get_the_title( $post_id ),
		// Make sure gets tied to parent.
		'post_parent' => $post_id,
	);

	// Required libraries for media_handle_sideload.
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Do the validation and storage stuff.
	// $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status.
	$att_id = media_handle_sideload( $file_array, $post_id, null, $post_data );

	// If error storing permanently, unlink.
	if ( is_wp_error( $att_id ) ) {
		// Clean up.
		@unlink( $file_array['tmp_name'] );

		// And output wp_error.
		return $att_id;
	}

	return $att_id;
}

/*
 * Get the image thumbnail and the video url from a youtube id.
 *
 * @author Gary Kovar
 *
 * @since 1.0.5
 */
function wds_get_youtube_details( $youtube_id ) {
	$remote_headers               = wp_remote_head( 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg' );
	$is_404                       = ( 404 === wp_remote_retrieve_response_code( $remote_headers ) );
	$video['video_thumbnail_url'] = ( ! $is_404 ) ? 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg' : 'http://img.youtube.com/vi/' . $youtube_id . '/hqdefault.jpg';
	$video['video_url']           = 'https://www.youtube.com/watch?v=' . $youtube_id;
	$video['video_embed_url']     = 'https://www.youtube.com/embed/' . $youtube_id;

	return $video;
}

/*
 * Get the image thumbnail and the video url from a vimeo id.
 *
 * @author Gary Kovar
 *
 * @since 1.0.5
 */
function wds_get_vimeo_details( $vimeo_id ) {
	$vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . intval( $vimeo_id ) . '.php' );
	if ( isset( $vimeo_data['response']['code'] ) && '200' == $vimeo_data['response']['code'] ) {
		$response                     = unserialize( $vimeo_data['body'] );
		$video['video_thumbnail_url'] = isset( $response[0]['thumbnail_large'] ) ? $response[0]['thumbnail_large'] : false;
		$video['video_url']           = 'https://vimeo.com/' . $vimeo_id;
		$video['video_embed_url']     = 'https://player.vimeo.com/video/' . $vimeo_id;
	}

	return $video;
}

/*
 * Check if the post is a video.
 *
 * @author Gary Kovar
 *
 * @since 1.0.5
 */
function wds_post_has_video( $post_id ) {
	if ( ! metadata_exists( 'post', $post_id, '_is_video' ) ) {
		wds_check_if_content_contains_video( $post_id, get_post( $post_id ) );
	}

	return get_post_meta( $post_id, '_is_video', true );
}

/*
 * Get the URL for the video.
 *
 * @author Gary Kovar
 *
 * @since 1.0.5
 */
function wds_get_video_url( $post_id ) {
	if ( wds_post_has_video( $post_id ) ) {
		if ( ! metadata_exists( 'post', $post_id, '_video_url' ) ) {
			wds_check_if_content_contains_video( $post_id, get_post( $post_id ) );
		}

		return get_post_meta( $post_id, '_video_url', true );
	}
}

/*
 * Get the embeddable URL
 *
 * @author Gary Kovar
 *
 * @since 1.0.5
 */
function wds_get_embeddable_video_url( $post_id ) {
	if ( wds_post_has_video( $post_id ) ) {
		if ( ! metadata_exists( 'post', $post_id, '_video_embed_url' ) ) {
			wds_check_if_content_contains_video( $post_id, get_post( $post_id ) );
		}

		return get_post_meta( $post_id, '_video_embed_url', true );
	}
}

/**
 * Register a metabox to display the video on post edit view.
 * @author Gary Kovar
 * @since 1.1.0
 */
function wds_register_display_video_metabox() {
	global $post;

	if ( get_post_meta( $post->ID, '_is_video', true ) ) {
		add_meta_box(
			'wds_display_video_urls_metabox',
			__( 'Video Files found in Content', 'wds-automatic-featured-images-from-video' ),
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
	global $post;

	echo '<h3>Video URL</h3>';
	echo wds_get_video_url($post->ID);
	echo '<h3>Video Embed URL</h3>';
	echo wds_get_embeddable_video_url($post->ID);
}