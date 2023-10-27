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
