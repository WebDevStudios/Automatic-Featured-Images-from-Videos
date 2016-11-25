/**
 * Created by garykovar on 11/21/16.
 */

	jQuery( function () {
		jQuery( "body.post-type-<?php echo $post_type; ?> .wrap h1" ).append( '<a href="#" class="page-title-action bulk-add-video">Bulk add Video Thumbnails</a>' );
		jQuery( ".bulk-add-video" ).click( function () {
			jQuery( ".bulk-add-video" ).hide();
			jQuery( "body.post-type-<?php echo $post_type; ?> .wrap h1" ).append( '<a class="page-title-action bulk-add-video-status">Processing...</a>' );
			jQuery.ajax( {
				type: "POST",
				url:  ajaxurl,
				data: { action: 'wds_queue_bulk_processing', posttype: '<?php echo $post_type; ?>' }
			} );
		} );
	} );

	jQuery( function () {
		jQuery( "body.post-type-<?php echo $post_type; ?> .wrap h1" ).append( '<a class="page-title-action bulk-add-video-status">Processing...</a>' );
	} );