<?php

class Vimeo implements Video_Provider {

	protected $id;
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

add_action( 'wds_featured_images_from_video_providers', function( $providers ) {
	$providers->add_provider( 'vimeo', new Vimeo() );
}, 9, 1 );