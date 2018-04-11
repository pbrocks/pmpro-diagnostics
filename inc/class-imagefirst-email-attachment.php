<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


class ImageFirst_Email_Attachment {
	protected $path;
	protected $url;
	protected $name;
	protected $encoding;
	protected $type;

	function __construct( $path = null, $url = null, $name = null, $encoding = null, $type = null ) {
		$this->path = $path;
		$this->url = $url;
		$this->name = $name;
		$this->encoding = $encoding;
		$this->type = $type;
	}

	public function get_type() {
		if ( ! isset( $this->type ) ) {
			$this->type = 'application/octet-stream';
		}

		return $this->type;
	}

	public function get_encoding() {
		if ( ! isset( $this->encoding ) ) {
			$this->encoding = 'base64';
		}
		return $this->encoding;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_path() {
		return $this->path;
	}
}
