<?php

class Youtube extends Video_Provider {

	public function match_content( $content ) {
		if ( preg_match( '#\/\/(www\.)?(youtu|youtube|youtube-nocookie)\.(com|be)\/(watch|embed)?\/?(\?v=)?([a-zA-Z0-9\-\_]+)#', $content, $youtube_matches ) ) {
			$this->id = $youtube_matches[6];

			return true;
		}

		return false;
	}

	public function get_video_thumbnail_url() {
		$video_thumbnail_url_string = 'http://img.youtube.com/vi/%s/%s';

		$video_check = wp_remote_head( 'https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $this->id );
		if ( 200 === wp_remote_retrieve_response_code( $video_check ) ) {
			$remote_headers               = wp_remote_head(
				sprintf(
					$video_thumbnail_url_string,
					$this->id,
					'maxresdefault.jpg'
				)
			);
			$video_thumbnail_url = ( 404 === wp_remote_retrieve_response_code( $remote_headers ) ) ?
				sprintf(
					$video_thumbnail_url_string,
					$this->id,
					'hqdefault.jpg'
				) :
				sprintf(
					$video_thumbnail_url_string,
					$this->id,
					'maxresdefault.jpg'
				);
			return $video_thumbnail_url;
		}

		return '';
	}

	public function get_video_url() {
		return 'https://www.youtube.com/watch?v=' . $this->id;
	}

	public function get_video_embed_url() {
		return 'https://www.youtube.com/embed/' . $this->id;
	}

	public function get_video_id() {
		return $this->id;
	}

}

add_action( 'wds_featured_images_from_video_providers', function( $providers ) {
	$providers->add_provider( new Youtube() );
}, 9, 1 );