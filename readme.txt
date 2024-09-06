=== Automatic Featured Images from Videos ===

Contributors: webdevstudios, pluginize
Donate link: http://webdevstudios.com/
Tags: video, youtube, vimeo, featured image
Requires at least: 5.0
Tested up to: 6.6.1
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.6

If a YouTube or Vimeo video embed exists near the start of a post, we'll automatically set the post's featured image to a thumbnail of the video.

== Description ==

When placing a YouTube or Vimeo video within the first 4000 characters of a post, the thumbnail of that video will automatically be uploaded and set as the featured image for the post as long as the post does not already have a set featured image.

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

= 1.2.4 =
* Fixed: Better file naming of incoming images, based on youtube/video title value.
* Confirmed compatibility with WordPress 6.6.x

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
