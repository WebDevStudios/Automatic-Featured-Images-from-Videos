'use strict';

(() => {
	const h1edit = document.querySelector('body.edit-php.post-type-' + wds_featured_image_from_vid_args.post_type + ' .wrap h1');

	const processLink = document.createElement('a');
	processLink.href = '#';
	processLink.classList.add('page-title-action', 'bulk-add-video');
	processLink.innerHTML = wds_featured_image_from_vid_args.bulk_text;

	const runningLink = document.createElement('a');
	runningLink.classList.add('page-title-action', 'bulk-add-video-status');
	runningLink.innerHTML = wds_featured_image_from_vid_args.processing_text;

	function wds_ajax_maybe_update_post_thumbnail() {
		h1edit.append(processLink);
		processLink.addEventListener('click', (e) => {
			e.preventDefault();
			e.target.style.display = 'none';
			h1edit.append(runningLink);

			const data = new FormData();
			data.append('action', 'wds_queue_bulk_processing');
			data.append('posttype', wds_featured_image_from_vid_args.post_type);
			const options = {
				method: 'POST', body: data,
			}

			fetch(window.ajaxurl, options)
				.then((response) => response.json())
				.then((response) => {
					console.log(response)
				}).catch((error) => {
				console.log(error);
			});
		});
	}

	if ('running' === wds_featured_image_from_vid_args.status) {
		h1edit.append(runningLink);
	}

	if ('ready_to_process' === wds_featured_image_from_vid_args.status) {
		wds_ajax_maybe_update_post_thumbnail();
	}
})();
