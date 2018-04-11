<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


/**
 * Goodwill Customer Contact object
 *
 * @package WordPress
 * @subpackage Goodwill
 * @since Goodwill 1.0
 */

/**
 * Handles contacts in general (specifically used when dealing with customer contacts)
 */
class Goodwill_Customer_Contact {
	public $name; // Required for posting to TRACS.
	public $title;
	public $email; // Required for the end email.

	public $label;

	public function __construct(
		$name = null, $title = null, $email = null, $label = null
	) {
		$this->name = $name;
		$this->title = $title;
		$this->email = $email;
		$this->label = $label;
		add_action( 'admin_menu', array( $this, 'plugin_dev_menu' ) );
	}
	public function plugin_dev_menu() {
		add_dashboard_page( __FUNCTION__, __FUNCTION__, 'edit_posts', 'dev-subdash-2.php', array( $this, 'dev_submenu_page_2' ) );
	}

	public function dev_submenu_page_2() {
		global $menu, $submenu;

		echo '<h2>' . __CLASS__ . '</h2>';
		echo '<h3>' . ( $this->name ) ? 'name' : 'not-named' . '</h3>';
	}
}
