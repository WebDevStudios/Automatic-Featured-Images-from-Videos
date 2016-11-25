/**
 * Created by garykovar on 11/21/16.
 */

jQuery( document ).ready(function(){

	if ('running' == wds_featured_image_from_vid_args.status ) {
		jQuery( "body.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1" ).append( '<a class="page-title-action bulk-add-video-status">Processing...</a>' );
	}

	if ('ready_to_process' == wds_featured_image_from_vid_args.status ) {
		jQuery( "body.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1" ).append( '<a href="#" class="page-title-action bulk-add-video">Bulk add Video Thumbnails</a>' );
		jQuery( ".bulk-add-video" ).click( function () {
			jQuery( ".bulk-add-video" ).hide();
			jQuery( "body.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1" ).append( '<a class="page-title-action bulk-add-video-status">Processing...</a>' );
			jQuery.ajax( {
				type: "POST",
				url:  ajaxurl,
				data: { action: 'wds_queue_bulk_processing', posttype: wds_featured_image_from_vid_args.post_type }
			} );
		});
	}
});