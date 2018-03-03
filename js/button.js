/**
 * Created by garykovar on 11/21/16.
 */

jQuery(document).ready(function () {

	function wds_ajax_maybe_update_post_thumbnail() {
		jQuery("body.edit-php.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1").append('<a href="#" class="page-title-action bulk-add-video">' + wds_featured_image_from_vid_args.bulk_text + '</a>');
		jQuery(".bulk-add-video").on('click',function (e) {
			e.preventDefault();
			jQuery(".bulk-add-video").hide();
			jQuery("body.edit-php.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1").append('<a class="page-title-action bulk-add-video-status">' + wds_featured_image_from_vid_args.processing_text + '</a>');
			jQuery.ajax({
				type: "POST",
				url : ajaxurl,
				data: {action: 'wds_queue_bulk_processing', posttype: wds_featured_image_from_vid_args.post_type}
			});
		});
	}

	if ('running' == wds_featured_image_from_vid_args.status) {
		jQuery("body.edit-php.post-type-" + wds_featured_image_from_vid_args.post_type + " .wrap h1").append('<a class="page-title-action bulk-add-video-status">' + wds_featured_image_from_vid_args.processing_text + '</a>');
	}

	if ('ready_to_process' == wds_featured_image_from_vid_args.status) {
		wds_ajax_maybe_update_post_thumbnail();
	}
});
