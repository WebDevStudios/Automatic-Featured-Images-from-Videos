= 1.2.3 =

* Confirmed compatibility with WordPress 6.5

= 1.2.2 =

* Confirmed compatibility with WordPress 6.4
* Fixed: PHP notices around video url variables
* Updated: removed `www.` from Vimeo endpoints that showed permanent redirect messages.

= 1.2.1 =

* Confirmed compatibility with WordPress 6.3

= 1.2.0 =

* Added: Support for potentially larger Vimeo images from API response.
* Fixed: Various PHP notices and errors.
* Updated: Minimum PHP version.
* Updated: bumped up default string length to 4000 characters, for URL searching in content.
* Updated: exclude user profile URLs from Youtube regex.
* Updated: Switched all endpoints to make sure we're using HTTPS.
* Updated: Vimeo endpoint switched to JSON responses.
* Updated: Plugin description.

= 1.1.2 =

* Fixed: Issues with Youtube HEAD request returning 40x errors.

= 1.1.1 =

* Fixed: Extra forward slash in YouTube URLs that was causing 404 errors when trying to add to media library.

= 1.1.0 =

* Added: Metabox that displays the found video URL and embed URL. Values saved as post meta.
* Added: Pass post ID for the `wds_check_if_content_contains_video` filter.
* Added: Filters that allow customization by developers to alter default values.
* Added: BETA: Bulk processing of posts for those missing thumbnails from videos. Please report issues found.
* Added: BETA: WP-CLI support.
* Fixed: Modified the way the vimeo embed URL is returned.
* Fixed: Prevent multiple instances of same found image from being uploaded to media library.
* 
= 1.0.5 =

* Added function wds_get_video_url when passed a post_id returns a video URL.
* Added function wds_get_embed_video_url when passed a post_id returns a video URL that is embeddable.
* Added function wds_post_has_video to check if a post_id has video.
* Deprecated wds_set_media_as_featured_image.
* Refactored the default save_post entry function to handle logic better.

= 1.0.4 =

* Store the full video url in post meta _video_url.
* Refactored checks for video ID.

= 1.0.3 =

* Switch to using WP HTTP API functions over get_headers(). Hopefully removes potential server config conflicts.
* Reverse originally incorrect logic in YouTube thumbnail selection based on header results.
* Return early if saving a revision.

= 1.0.2 =

* Add support for youtube short links,
  fixes [#3](https://github.com/WebDevStudios/Automatic-Featured-Images-from-Videos/issues/3)

= 1.0.1 =

* Fix bug with special characters in YouTube video titles
* Fix bug where duplicate images would be uploaded and set as featured image when editing a post

= 1.0 =

* Initial release
