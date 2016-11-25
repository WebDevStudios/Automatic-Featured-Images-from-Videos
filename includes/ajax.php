<?php
/**
 * Created by PhpStorm.
 * User: garykovar
 * Date: 11/21/16
 * Time: 10:51 PM
 */

/**
 * Add a bulk-process button.
 *
 * @author Gary Kovar
 *
 * @since  1.1.0
 */
function wds_customize_post_buttons() {
	global $post_type;

	// Allow developers to pass in custom CPTs to process.
	$type_array = apply_filters( 'wds_featured_images_from_video_post_types', array( 'post', 'page' ) );

	if ( in_array( $post_type, $type_array ) ) {

		$status =

		$args = array(
			'post_type' => $post_type,
			'status'    => wds_featured_images_from_video_processing_status( $post_type ),
		);

		wp_register_script( 'wds_featured_images_from_video_script', plugin_dir_path( __FILE__ ) . 'js/button.js' );
		wp_localize_script( 'wds_featured_images_from_video_script', 'args', $args );
		wp_enqueue_script( 'wds_featured_images_from_video_script' );

		}
	}
}