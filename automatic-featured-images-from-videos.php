<?php
/*
Plugin Name: Automatic Featured Images from YouTube / Vimeo
Plugin URI: http://webdevstudios.com
Description: If a YouTube or Vimeo video exists in the first few paragraphs of a post, automatically set the post's featured image to that video's thumbnail.
Version: 1.0.2
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

/**
 * If a YouTube or Vimeo video is added in the post content, grab its thumbnail and set it as the featured image.
 *
 * @since 1.0.0
 *
 * @param int    $post_id ID of the post being saved.
 * @param object $post Post object.
 */
function wds_set_media_as_featured_image( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$content = isset( $post->post_content ) ? $post->post_content : '';
	// Only check the first 800 characters of our post.
	$content = substr( $content, 0, 800 );

	// Allow developers to filter the content to allow for searching in postmeta or other places.
	$content = apply_filters( 'wds_featured_images_from_video_filter_content', $content );

	// Props to @rzen for lending his massive brain smarts to help with the regex.
	$do_video_thumbnail = (
		$post_id
		&& ! has_post_thumbnail( $post_id )
		&& $content
		// Get the video and thumb URLs if they exist.
		&& ( preg_match( '/\/\/(www\.)?(youtu|youtube)\.(com|be)\/(watch|embed)?\/?(\?v=)?([a-zA-Z0-9\-\_]+)/', $content, $youtube_matches )
			|| preg_match( '#https?://(.+\.)?vimeo\.com/.*#i', $content, $vimeo_matches ) )
	);

	if ( ! $do_video_thumbnail ) {
		return update_post_meta( $post_id, '_is_video', false );
	}

	$video_thumbnail_url = false;

	$youtube_id = ! empty( $youtube_matches ) ? $youtube_matches[6] : '';
	$vimeo_id = ! empty( $vimeo_matches ) ? preg_replace( "/[^0-9]/", "", $vimeo_matches[0] ) : '';

	if ( $youtube_id ) {
		// Check to see if our max-res image exists.
		$file_headers = get_headers( 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg' );
		$is_404 = $file_headers[0] == 'HTTP/1.0 404 Not Found' || false !== strpos( $file_headers[0], '404 Not Found' );
		$video_thumbnail_url = $is_404 ? 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg' : 'http://img.youtube.com/vi/' . $youtube_id . '/hqdefault.jpg';

	} elseif ( $vimeo_id ) {

		$vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . intval( $vimeo_id ) . '.php' );
		if ( isset( $vimeo_data['response']['code'] ) && '200' == $vimeo_data['response']['code'] ){
			$response = unserialize( $vimeo_data['body'] );
			$video_thumbnail_url = isset( $response[0]['thumbnail_large'] ) ? $response[0]['thumbnail_large'] : false;
		}

	}

	// If we found an image...
	$attachment_id = $video_thumbnail_url && ! is_wp_error( $video_thumbnail_url )
		// Then sideload it.
		? wds_ms_media_sideload_image_with_new_filename( $video_thumbnail_url, $post_id, sanitize_title( preg_replace( "/[^a-zA-Z0-9\s]/", "-", get_the_title() ) ) )
		// No thumbnail url found.
		: 0;

	// If attachment wasn't created, bail.
	if ( ! $attachment_id ) {
		return;
	}

	// Woot! we got an image, so set it as the post thumbnail.
	set_post_thumbnail( $post_id, $attachment_id );
	update_post_meta( $post_id, '_is_video', true );

}
add_action( 'save_post', 'wds_set_media_as_featured_image', 10, 2 );

/**
 * Handle the upload of a new image.
 *
 * @since 1.0.0
 *
 * @param string      $url      URL to sideload.
 * @param int         $post_id  Post ID to attach to.
 * @param string|null $filename Filename to use.
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
	$url_filename = basename($matches[0]);
	// Determine file type (ext and mime/type).
	$url_type = wp_check_filetype($url_filename);

	// Override filename if given, reconstruct server path.
	if ( !empty( $filename ) ) {
		$filename = sanitize_file_name( $filename );
		// Extract path parts.
		$tmppath = pathinfo( $tmp );
		// Build new path.
		$new = $tmppath['dirname'] . '/'. $filename . '.' . $tmppath['extension'];
		// Renames temp file on server.
		rename($tmp, $new);
		// Push new filename (in path) to be used in file array later.
		$tmp = $new;
	}

	/* Assemble file data (should be built like $_FILES since wp_handle_sideload() will be using). */

	// Full server path to temp file.
	$file_array['tmp_name'] = $tmp;

	if ( !empty( $filename ) ) {
		// User given filename for title, add original URL extension.
		$file_array['name'] = $filename . '.' . $url_type['ext'];
	} else {
		// Just use original URL filename.
		$file_array['name'] = $url_filename;
	}

	$post_data = array(
		// Just use the original filename (no extension).
		'post_title' => get_the_title( $post_id ),
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
