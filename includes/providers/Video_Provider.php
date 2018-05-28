<?php

abstract class Video_Provider {

	protected $id;

	abstract public function match_content( $content );

	abstract public function get_video_id();

	abstract public function get_video_thumbnail_url();

	abstract public function get_video_url();

	abstract public function get_video_embed_url();

}