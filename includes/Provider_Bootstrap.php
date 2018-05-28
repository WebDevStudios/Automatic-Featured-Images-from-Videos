<?php

class Provider_Bootstrap {

	protected $providers = [];

	public function add_provider( $provider ) {
		if ( ! is_a( $provider, Video_Provider::class ) ) {
			return new WP_Error( __( 'The passed provider is not a Provider Object', 'automatic-featured-images-from-videos' ) );
		}

		$this->providers[] = $provider;
	}

	public function video_providers() {
		return apply_filters( 'wds_video_providers', $this->providers );
	}

}
