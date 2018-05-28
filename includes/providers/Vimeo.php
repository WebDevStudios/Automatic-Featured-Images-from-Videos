<?php

class Vimeo extends Video_Provider {

	private $thumbnail_url;
	private $url;
	private $embed_url;


	public function match_content( $content ) {
		if ( preg_match( '#\/\/(.+\.)?(vimeo\.com)\/(\d*)#', $content, $vimeo_matches ) ) {
			$this->id = $vimeo_matches[3];
			$this->get_vimeo();

			return true;
		}

		return false;
	}

	private function get_vimeo() {
		$vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . (int) $this->id . '.php' );
		if ( 200 === wp_remote_retrieve_response_code( $vimeo_data ) ) {
			$response            = unserialize( $vimeo_data['body'] );
			$this->thumbnail_url = isset( $response[0]['thumbnail_large'] ) ? $response[0]['thumbnail_large'] : false;
			$this->url           = $response[0]['url'];
			$this->embed_url     = 'https://player.vimeo.com/video/' . $this->id;
		}
	}

	public function get_video_thumbnail_url() {
		return $this->thumbnail_url;
	}

	public function get_video_url() {
		return $this->url;
	}

	public function get_video_embed_url() {
		return $this->embed_url;
	}

	public function get_video_id() {
		return $this->id;
	}

}


/**
 * Get the image thumbnail and the video url from a vimeo id.
 *
 * @author Gary Kovar
 *
 * @since  1.0.5
 *
 * @param string $vimeo_id Vimeo video ID.
 *
 * @return array Video information.
 */
function wds_get_vimeo_details( $vimeo_id ) {
	$video = array();

	// @todo Get remote checking matching with wds_get_youtube_details.
	$vimeo_data = wp_remote_get( 'http://www.vimeo.com/api/v2/video/' . intval( $vimeo_id ) . '.php' );
	if ( 200 === wp_remote_retrieve_response_code( $vimeo_data ) ) {
		$response                     = unserialize( $vimeo_data['body'] );
		$video['video_thumbnail_url'] = isset( $response[0]['thumbnail_large'] ) ? $response[0]['thumbnail_large'] : false;
		$video['video_url']           = $response[0]['url'];
		$video['video_embed_url']     = 'https://player.vimeo.com/video/' . $vimeo_id;
	}

	return $video;
}