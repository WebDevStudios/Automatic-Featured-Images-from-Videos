<?php

interface Video_Provider {

	public function match_content( $content );

	public function get_video_id();

	public function get_video_thumbnail_url();

	public function get_video_url();

	public function get_video_embed_url();

}