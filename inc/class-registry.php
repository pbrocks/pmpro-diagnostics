<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


/**
 * Registry
 */
class Registry {
	protected $properties = array();

	public function __set( $index, $value ) {
		$this->properties[ $index ] = $value;
	}

	public function __get( $index ) {
		return $this->properties[ $index ];
	}

	public function __isset( $name ) {
		return isset( $this->properties[ $name ] );
	}
}

// Start the registry in global space
// Not the best idea but hey - it's quick
// Should use dependency injection if possible?
$registry = new Registry();
