<?php
/*
Plugin Name: Automatic Featured Images from YouTube / Vimeo
Plugin URI: http://webdevstudios.com
Description: If a YouTube or Vimeo video exists in the first few paragraphs of a post, automatically set the post's featured image to that vidoe's thumbnail.
Version: 1.0.0
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
 * If a YouTube or Vimeo video is added in the post content, grab its thumbnail and set it as the featured image
 */
function wds_set_media_as_featured_image( $post_id, $post ) {

    $content = isset( $post->post_content ) ? $post->post_content : '';

    $content = substr( $content, 0, 800 ); //Only check the first 1000 characters of our post.

    if (
        isset( $post->ID )
        && ! has_post_thumbnail( $post_id )
        && $content
        // Get the video and thumb URLs if they exist
        && ( preg_match( '/\/\/(www\.)?youtube\.com\/(watch|embed)\/?(\?v=)?([a-zA-Z0-9\-\_]+)/', $content, $youtube_matches ) ||
             preg_match( '#https?://(.+\.)?vimeo\.com/.*#i', $content, $vimeo_matches ) )
    ) {
        $youtube_id = $youtube_matches[4];
        $vimeo_id = preg_replace("/[^0-9]/","", $vimeo_matches[0] );

        if( $youtube_id ){
            // Check to see if our max-res image exists
            $file_headers = get_headers( 'http://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg' );
            $youtube_thumb_url = $file_headers[0] !== 'HTTP/1.0 404 Not Found' ? 'http://img.youtube.com/vi/'. $youtube_id .'/maxresdefault.jpg' : 'http://img.youtube.com/vi/'. $youtube_id .'/hqdefault.jpg';
            // next, download the URL of the youtube image
            //media_sideload_image( $youtube_thumb_url, $post_id, get_the_title() . '.' . mt_rand(0,100000) );
            $attachment_id = wds_ms_media_sideload_image_with_new_filename( $youtube_thumb_url, $post_id, null, sanitize_title( get_the_title() ) );
        } elseif ( $vimeo_id ){
            $vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . intval( $vimeo_id ) . '.php' );
            if ( '200' == $vimeo_data['response']['code'] ){
                $response = unserialize( $vimeo_data['body'] );
                $vimeo_thumb_url = $response[0]['thumbnail_large'];

                $attachment_id = wds_ms_media_sideload_image_with_new_filename( $vimeo_thumb_url, $post_id, null, sanitize_title( get_the_title() ) );
            }
        }

        // find the most recent attachment for the given post
        $attachments = get_posts( array(
            'post_type'   => 'attachment',
            'numberposts' => 1,
            'order'       => 'ASC',
            'post_parent' => $post_id
        ) );

        // If attachments exist, set 'em!
        if ( $attachments ) {
            $attachment = $attachments[0];
            // and set it as the post thumbnail
            set_post_thumbnail( $post_id, $attachment_id );
            update_post_meta( $post_id, '_is_video', true );
        }
    } else {
            update_post_meta( $post_id, '_is_video', false );
            return;
    }

}
add_action( 'save_post', 'wds_set_media_as_featured_image', 10, 2 );

function wds_ms_media_sideload_image_with_new_filename( $url = null, $post_id = null, $thumb = null, $filename = null, $post_data = array() ) {
    if ( !$url || !$post_id ) return new WP_Error('missing', "Need a valid URL and post ID...");
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp
    $tmp = download_url( $url );

    // If error storing temporarily, unlink
    if ( is_wp_error( $tmp ) ) {
        @unlink($file_array['tmp_name']);   // clean up
        $file_array['tmp_name'] = '';
        return $tmp; // output wp_error
    }

    preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);    // fix file filename for query strings
    $url_filename = basename($matches[0]);                                                  // extract filename from url for title
    $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)

    // override filename if given, reconstruct server path
    if ( !empty( $filename ) ) {
        $filename = sanitize_file_name($filename);
        $tmppath = pathinfo( $tmp );                                                        // extract path parts
        $new = $tmppath['dirname'] . "/". $filename . "." . $tmppath['extension'];          // build new path
        rename($tmp, $new);                                                                 // renames temp file on server
        $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
    }

    // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
    $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file

    if ( !empty( $filename ) ) {
        $file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
    } else {
        $file_array['name'] = $url_filename;                                                // just use original URL filename
    }

    // set additional wp_posts columns
    if ( empty( $post_data['post_title'] ) ) {
        $post_data['post_title'] = basename($url_filename, "." . $url_type['ext']);         // just use the original filename (no extension)
    }

    // make sure gets tied to parent
    if ( empty( $post_data['post_parent'] ) ) {
        $post_data['post_parent'] = $post_id;
    }

    // required libraries for media_handle_sideload
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // do the validation and storage stuff
    $att_id = media_handle_sideload( $file_array, $post_id, null, $post_data );             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status

    // If error storing permanently, unlink
    if ( is_wp_error($att_id) ) {
        @unlink($file_array['tmp_name']);   // clean up
        return $att_id; // output wp_error
    }

    // set as post thumbnail if desired
    if ($thumb) {
        set_post_thumbnail($post_id, $att_id);
    }

    return $att_id;
}
