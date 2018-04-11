<?php
namespace PMPro_Diagnostics\inc;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class Dev_Tools {

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'view_template_files' ) );
		add_action( 'wp_footer', array( __CLASS__, 'list_wp_hooks_in_play' ) );
	}

	public static function view_template_files() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			global $template;
			$template_name = basename( $template, '.php' );
			$template_dir  = basename( dirname( $template ) );
			$style_top     = ( is_admin_bar_showing() ) ? '5rem' : '0px';
			echo '<code style="position:fixed;bottom:' . $style_top . ';right:1rem;z-index:7777; background-color:rgba(255,255,255,0.75); padding:1rem;color:#85368e;border:solid 2px #85368e;">';
			echo 'theme directory：' . $template_dir;
			echo '　template：' . $template_name;
			echo "</code>\n";
		}
	}
	public static function dev_menu_page() {
		global $menu, $submenu, $wp_admin_bar;
		echo '<div class="wrap"><h3>function = ' . __FUNCTION__ . '</h3><h4>in ' . __FILE__ . '</h4>';
		$all_toolbar_nodes = $wp_admin_bar;
		echo '<h3>You are viewing this menu from a ';
		echo Setup_Functions::detect_mobile_device();
		echo ' device</h3>';
		$enddate = apply_filters( 'pmpro_checkout_end_date', $enddate, $user_id, $pmpro_level, $startdate );
		echo '<pre>';
		echo '<br>You can find this file in  <br>';
		echo plugins_url( basename( __FILE__ ), __FILE__ );
		echo '<br>';
		echo '</pre>';

		echo '<pre><h2>This is all_toolbar_nodes</h2>';
		// echo self::list_wp_hooks_in_play();
		print_r( $all_toolbar_nodes );
		echo '</pre>';

		echo '<pre><h2>This is the Submenu</h2>';
		print_r( $submenu );
		echo '</pre>';
		echo '</div>';

	}

	public static function domain_mapping_page() {
		echo '<div class="wrap"><h3>function = ' . __FUNCTION__ . '</h3><h4>in ' . __FILE__ . '</h4>';
		global $wp_filter, $pmp_login_redirect;
		// $class_object = new \PMPro_Redirect_After_Login();
		$class_object = $pmp_login_redirect;
		echo '$class_object->pmpro_filename()<pre>';
		print_r( $class_object->pmpro_filename() );
		echo '</pre>';
		// $plugin = plugin_dir_path( __FILE__ ) . 'wordpress-mu-domain-mapping/domain_mapping.php';
		// include( $plugin );
		// $functions = self::get_functions_in_file( $plugin );
		echo 'pmp_login_redirect = <pre>';
		var_dump( $pmp_login_redirect );
		echo '</pre>';

		echo '<pre>';
		// var_dump( self::return_proper_file() );
		echo '</pre>';
		// $class_object = new \PMPro_Redirect_After_Login();
		echo self::get_class_method( $pmp_login_redirect );
		echo '</div>';

	}

	public static function list_wp_hooks_in_play( $hook = '' ) {
		global $wp_filter;
		if ( empty( $hook ) || ! isset( $wp_filter[ $hook ] ) ) {
			return;
		}

		$ret = '';
		foreach ( $wp_filter[ $hook ] as $priority => $realhook ) {
			foreach ( $realhook as $hook_k => $hook_v ) {
				$hook_echo = ( is_array( $hook_v['function'] ) ? get_class( $hook_v['function'][0] ) . ':' . $hook_v['function'][1] : $hook_v['function'] );
				$ret      .= "\n$priority $hook_echo";
			}
		}
		return $ret;
	}

	public static function list_wp_hooks_in_play2( $hook = '' ) {
		global $wp_filter;
		if ( isset( $wp_filter[ $filterName ] ) ) {
			foreach ( $wp_filter[ $filterName ] as $priority => $hooks ) {
				foreach ( $hooks as $hook_k => $hook_v ) {
					$hook_echo = ( is_array( $hook_v['function'] ) ? get_class( $hook_v['function'][0] ) . ':' . $hook_v['function'][1] : $hook_v['function'] );
					if ( is_object( $hook_echo ) && ( $hook_echo instanceof Closure ) ) {
						$hook_echo = 'closure';
					}
					error_log( $filterName . ' HOOKED (' . serialize( $priority ) . '): ' . serialize( $hook_k ) . '' . serialize( $hook_echo ) );
				}
			}
		} else {
			error_log( $filterName . ' NO FILTERS HOOKED' );
		}
	}


	public static function get_class_method( $object ) {
		$info = '';

		$class_methods = get_class_methods( $object );
		foreach ( $class_methods as $method_name ) {
			$info .= '<span style="margin-left: 2rem;">' . $method_name . '</span><br>';
		}
		return $info;
	}

	public static function myrow( $id, $data ) {
		return "<tr><th>$id</th><td>$data</td></tr>\n";
	}

	// $arr = get_defined_functions();
	// print_r($arr);
	static function return_proper_file() {

		$plugin = plugin_dir_path( __FILE__ ) . 'wordpress-mu-domain-mapping/domain_mapping.php';
		// include_once
		// $arr = get_defined_functions( $plugin );
		return $arr;
		// include_once $plugin;
	}

	public function get_functions_in_file( $file, $sort = false ) {
		$file      = file( $file );
		$functions = array();
		foreach ( $file as $line ) {
			$line = trim( $line );
			if ( stripos( $line, ' function ' ) !== false ) {
				$function_name = str_ireplace(
					[
						'public',
						'private',
						'protected',
						'static',
					], '', $line
				);

				if ( ! in_array( $function_name, [ '__construct', '__destruct' ] ) ) {
					$functions[] = trim( substr( $function_name, 9, strpos( $function_name, '(' ) - 9 ) );
				}
			}
		}
		if ( $sort ) {
			asort( $functions );
			$functions = array_values( $functions );
		}
		return $functions;
	}
	/**
	 * Return an array of plugin names and versions
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_plugins() {
		$plugins = array();
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( is_plugin_active( $plugin_file ) ) {
				$plugins[ $plugin_data['Name'] ] = $plugin_data['Version'];
			}
		}

		return $plugins;
	}


	/**
	 * Debug Information
	 *
	 * @since 1.0.0
	 *
	 * @param bool $html Optional. Return as HTML or not
	 *
	 * @return string
	 */
	public static function debug_info( $html = true ) {
		global $wp_version, $wpdb, $wp_scripts;
		$wp            = $wp_version;
		$php           = phpversion();
		$mysql         = $wpdb->db_version();
		$plugins       = self::get_plugins();
		$stylesheet    = get_stylesheet();
		$theme         = wp_get_theme( $stylesheet );
		$theme_name    = $theme->get( 'Name' );
		$theme_version = $theme->get( 'Version' );
		$opcode_cache  = array(
			'Apc'       => function_exists( 'apc_cache_info' ) ? 'Yes' : 'No',
			'Memcached' => class_exists( 'eaccelerator_put' ) ? 'Yes' : 'No',
			'Redis'     => class_exists( 'xcache_set' ) ? 'Yes' : 'No',
		);
		$object_cache  = array(
			'Apc'       => function_exists( 'apc_cache_info' ) ? 'Yes' : 'No',
			'Apcu'      => function_exists( 'apcu_cache_info' ) ? 'Yes' : 'No',
			'Memcache'  => class_exists( 'Memcache' ) ? 'Yes' : 'No',
			'Memcached' => class_exists( 'Memcached' ) ? 'Yes' : 'No',
			'Redis'     => class_exists( 'Redis' ) ? 'Yes' : 'No',
		);
		$versions      = array(
			'WPDB Prefix'                 => $wpdb->prefix,
			'WP Multisite Mode'           => ( is_multisite() ? 'Yes' : 'No' ),
			'WP Memory Limit'             => WP_MEMORY_LIMIT,
			'Currently Active Theme'      => $theme_name . ': ' . $theme_version,
			'Parent Theme'                => $theme->template,
			'WordPress Version'           => $wp,
			'PHP Version'                 => $php,
			'MySQL Version'               => $mysql,
			'JQuery Version'              => $wp_scripts->registered['jquery']->ver,
			'Server Software'             => $_SERVER['SERVER_SOFTWARE'],
			'Your User Agent'             => $_SERVER['HTTP_USER_AGENT'],
			'Session Save Path'           => session_save_path(),
			'Session Save Path Exists'    => ( file_exists( session_save_path() ) ? 'Yes' : 'No' ),
			'Session Save Path Writeable' => ( is_writable( session_save_path() ) ? 'Yes' : 'No' ),
			'Session Max Lifetime'        => ini_get( 'session.gc_maxlifetime' ),
			'Opcode Cache'                => $opcode_cache,
			'Object Cache'                => $object_cache,
			'Currently Active Plugins'    => $plugins,
		);
		if ( $html ) {
			$debug = '';
			foreach ( $versions as $what => $version ) {
				$debug .= '<p><strong>' . $what . '</strong>: ';
				if ( is_array( $version ) ) {
					$debug .= '</p><ul class="ul-disc">';
					foreach ( $version as $what_v => $v ) {
						$debug .= '<li><strong>' . $what_v . '</strong>: ' . $v . '</li>';
					}
					$debug .= '</ul>';
				} else {
					$debug .= $version . '</p>';
				}
			}
			return $debug;
		} else {
			return $versions;
		}
	}

	public static function short_debug_info( $html = true ) {
		global $wp_version, $wpdb;

		$data = array(
			'WordPress Version:' => $wp_version,
			'PHP Version:'       => phpversion(),
			'MySQL Version:'     => $wpdb->db_version(),
			// ''                       => '<br><br>',
			'WPDB Prefix:'       => $wpdb->prefix,
			'WP_DEBUG:'          => ( WP_DEBUG ) ? WP_DEBUG : 'false',
		);
		if ( $html ) {
			$html = '';
			foreach ( $data as $what_v => $v ) {
				$html .= '<li style="display: inline;"><strong>' . $what_v . '</strong> ' . $v . ' </li>';
			}

			return '<ul>' . $html . '</ul>';
		} else {
			return $data;
		}
	}

	public static function build_download_url() {
		return add_query_arg( 'download', 'true', admin_url( 'tools.php?page=debug-my-site-info' ) );
	}

}
