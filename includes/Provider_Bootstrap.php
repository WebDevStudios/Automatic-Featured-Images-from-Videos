<?php

class Provider_Bootstrap {

	protected $providers = [];

	public function add_provider( $provider_name, $provider ) {
		if ( ! is_a( $provider, Video_Provider::class ) ) {
			return new WP_Error( __( 'The passed provider is not a Provider Object', 'automatic-featured-images-from-videos' ) );
		}

		$this->providers[ $provider_name ] = $provider;
	}

	public function remove_provider( $provider_name ) {
		unset( $this->providers[ $provider_name ] );
	}

	public function video_providers() {
		return $this->providers;
	}

}
