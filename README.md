# Automatic Featured Images from Videos #

Contributors: [bradparbs](https://github.com/bradp), [coreymcollins](https://github.com/coreymcollins), [jtsternberg](https://github.com/jtsternberg), [webdevstudios](https://github.com/webdevstudios), [binarygary](https://github.com/binarygaryfi)   
Donate link: http://webdevstudios.com/
Tags: video, youtube, vimeo, featured image
Requires at least: 3.7
Tested up to: 4.6.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

If a YouTube or Vimeo video exists in the first portion of a post, automatically set the post's featured image to that video's thumbnail.

## Description ##

When placing a YouTube or Vimeo video within the first 1000 characters of a post, the thumbnail of that video will automatically be sideloaded and set as the featured image for the post as long as the post does not already have a featured image set.

In addition, after setting the video thumbnail as the featured image, an “is_video” post meta field is updated to allow for the use of conditional statements within your loop.

## Installation ##

### From your WordPress dashboard ###

1. Visit 'Plugins > Add New’.
2. Search for 'Automatic Featured Images from Videos’.
3. Activate Automatic Featured Images from Videos from your Plugins page.

### From WordPress.org ###

1. Download Automatic Featured Images from Videos.
2. Upload the 'Automatic Featured Images from Videos' directory to your '/wp-content/plugins/' directory.
3. Activate Automatic Featured Images from Videos from your Plugins page.

## Frequently Asked Questions ##

## Screenshots ##

## Changelog ##

### 1.1.0 ###
* Added a metabox that displays the video found.
* Fixed the way the vimeo embed URL is returned.

### 1.0.5 ###
* Added function wds_get_video_url when passed a post_id returns a video URL.
* Added function wds_get_embed_video_url when passed a post_id returns a video URL that is embeddable.
* Added function wds_post_has_video to check if a post_id has video.
* Deprecated wds_set_media_as_featured_image.
* Refactored the default save_post entry function to handle logic better.

### 1.0.4 ###
* Store the full video url in post meta _video_url. 
* Refactored checks for video ID.

### 1.0.3 ###
* Switch to using WP HTTP API functions over get_headers(). Hopefully removes potential server config conflicts.
* Reverse originally incorrect logic in YouTube thumbnail selection based on header results.
* Return early if saving a revision.

### 1.0.2 ###
* Add support for youtube short links, fixes [#3](https://github.com/WebDevStudios/Automatic-Featured-Images-from-Videos/issues/3)

### 1.0.1 ###
* Fix bug with special characters in YouTube video titles
* Fix bug where duplicate images would be uploaded and set as featured image when editing a post

### 1.0 ###
* Initial release
