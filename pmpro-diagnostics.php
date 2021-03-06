<?php
/**
 * Plugin Name: PMPro Diagnostics
 * Plugin URI: https://github.com/pbrocks/pmpro-diagnostics
 *
 * Description: Jump start your interaction with the WordPress Diagnostics.
 * Author: pbrocks
 * Author URI: https://github.com/pbrocks
 * Version: 1.0.3
 * License: GPLv2
 * Text Domain: pmpro-diagnostics
 *
 * @link  http://codex.wordpress.org/Theme_Customization_API
 * @since MyTheme 1.0
 */

namespace PMPro_Diagnostics;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

require_once 'autoload.php';

new inc\PMPro_Notification( 'Testing Error Notification', 'error' );
// new inc\Customizing_ImageFirst();
inc\Dev_Dashboard::init();
// inc\Customizing_ImageFirst::init();
inc\Setup_Functions::init();
// inc\Goodwill_User::get_instance();
// new inc\Customizing_ImageFirst();
