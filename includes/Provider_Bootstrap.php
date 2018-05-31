<?php

class Provider_Bootstrap {

	protected $providers = [];

	public function add_provider( $provider_name, $provider ) {
		if ( ! in_array( Video_Provider::class, class_implements( $provider ) ) ) {
			return new WP_Error( __( 'The passed provider does not implement Video_Provider.', 'automatic-featured-images-from-videos' ) );
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
