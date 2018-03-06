=== Automatic Featured Images from Videos ===

Contributors: bradparbs, coreymcollins, jtsternberg, webdevstudios, pluginize, binarygary
Donate link: http://webdevstudios.com/
Tags: video, youtube, vimeo, featured image
Requires at least: 3.7
Tested up to: 4.9.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

If a YouTube or Vimeo video exists in the first portion of a post, automatically set the post's featured image to that video's thumbnail.

== Description ==

When placing a YouTube or Vimeo video within the first 800 characters of a post, the thumbnail of that video will automatically be sideloaded and set as the featured image for the post as long as the post does not already have a featured image set.

In addition, after setting the video thumbnail as the featured image, an “is_video” post meta field is updated to allow for the use of conditional statements within your loop.

[Pluginize](https://pluginize.com/?utm_source=automatic-feat-images&utm_medium=text&utm_campaign=wporg) was launched in 2016 by [WebDevStudios](https://webdevstudios.com/) to promote, support, and house all of their [WordPress products](https://pluginize.com/shop/?utm_source=automatic-feat-images&utm_medium=text&utm_campaign=wporg). Pluginize is not only creating new products for WordPress all the time, but also provides [ongoing support and development for WordPress community favorites like CPTUI](https://wordpress.org/plugins/custom-post-type-ui/), [CMB2](https://wordpress.org/plugins/cmb2/), and more.

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New’.
2. Search for 'Automatic Featured Images from Videos’.
3. Activate Automatic Featured Images from Videos from your Plugins page.

= From WordPress.org =

1. Download Automatic Featured Images from Videos.
2. Upload the 'Automatic Featured Images from Videos' directory to your '/wp-content/plugins/' directory.
3. Activate Automatic Featured Images from Videos from your Plugins page.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

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
* Add support for youtube short links, fixes [#3](https://github.com/WebDevStudios/Automatic-Featured-Images-from-Videos/issues/3)

= 1.0.1 =
* Fix bug with special characters in YouTube video titles
* Fix bug where duplicate images would be uploaded and set as featured image when editing a post

= 1.0 =
* Initial release
