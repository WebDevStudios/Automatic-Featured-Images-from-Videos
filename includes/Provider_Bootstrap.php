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
		/**
		 * Allow developers to pass in custom video providers.
		 * Video providers should extend the Video_Provider class.
		 *
		 * @since 1.1.1
		 *
		 * @param Video_Provider $value An object that is of a class that extends Video_Provider.
		 */
		return apply_filters( 'wds_video_providers', $this->providers );
	}

}
