<?php
namespace PMPro_Diagnostics\inc;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


class Dev_Dashboard {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'main_dev_menu' ) );
		// add_action( 'wp_head', array( __CLASS__, 'admin_dev_scripts' ) );
		// add_shortcode( 'info-balloon-shortcode', array( __CLASS__, 'info_balloon_shortcode' ) );
		// add_action( 'all', array( __CLASS__, 'debug_tags' ) );
	}

	public static function admin_dev_scripts() {
		$script = plugins_url( 'js/window-size.js', __FILE__ );
		wp_register_script( 'window-size', $script, array( 'jquery', 'jquery-ui' ), '' );
		wp_enqueue_script( 'window-size' );
			// echo '<a href="' . $script . '" target="_blank">' . $script . '</a>';
	}

	public static function dmq_load_scripts() {
		wp_enqueue_style( 'diagnostic', plugins_url( '../css/diagnostic.css', __FILE__ ) );
		wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js' );
		wp_enqueue_script( 'diagnostic', plugins_url( '../js/window-size.js', __FILE__ ), array( 'jquery' ) );

		$script = plugins_url( '../js/hide-show.js', __FILE__ );
		wp_enqueue_script( 'hide-show', $script, array( 'jquery', 'jquery-ui' ), false, false );
	}

	public static function info_balloon_shortcode() {
	?><div class="info-balloon">
		<img src="<?php echo plugins_url( '../images/info-balloon.png', __FILE__ ); ?>" id="pbrx-trigger" class="white-bg"/>
		</div>
	<?php
	}

	public static function one2_show_template() {
		echo '<div class="the-debug-bar"><div class="salmon-js" id="the-debug-bar"></div></div>';
	}

	public static function debug_tags( $tag ) {
		global $debug_tags;
		$debug_tags = array();

		if ( in_array( $tag, $debug_tags ) ) {
			return;
		}
		echo '<pre>' . $tag . '</pre>';
		$debug_tags[] = $tag;
	}
	public static function print_filters_for( $hook = '' ) {
		global $wp_filter;
		if ( empty( $hook ) || ! isset( $wp_filter[ $hook ] ) ) {
			return;
		}

		$ret = '';
		foreach ( $wp_filter[ $hook ] as $priority => $realhook ) {
			foreach ( $realhook as $hook_k => $hook_v ) {
				$hook_echo = ( is_array( $hook_v['static function'] ) ? get_class( $hook_v['static function'][0] ) . ':' . $hook_v['static function'][1] : $hook_v['static function'] );
				$ret      .= "\n$priority $hook_echo";
			}
		}
		 return $ret;
	}
	public static function logo_message_shortcode() {
		echo do_shortcode( '[info-message title="Text vs Logo" content="Select whether you want to show the logo image or header text in the Customizer."]' );
	}
	public static function main_dev_menu() {
		add_menu_page( 'Dev Dashboard', 'Dev Dashboard', 'edit_posts', 'dev-dashboard.php', array( __CLASS__, 'dev_menu_page' ), 'dashicons-tickets', 11 );
		add_submenu_page( 'dev-dashboard.php', 'SubDash 1', 'SubDash 1', 'edit_posts', 'dev-subdash-1.php', array( __CLASS__, 'dev_submenu_page_1' ) );
		add_submenu_page( 'dev-dashboard.php', 'SubDash 2', 'Search Item', 'edit_posts', 'dev-subdash-2.php', array( __CLASS__, 'dev_submenu_page_2' ) );
		add_submenu_page( 'dev-dashboard.php', 'SubDash 3', 'Sort Return', 'edit_posts', 'dev-subdash-3.php', array( __CLASS__, 'dev_submenu_page_3' ) );
		add_submenu_page( 'dev-dashboard.php', 'SubDash 4', 'SubDash 4', 'edit_posts', 'dev-subdash-4.php', array( __CLASS__, 'dev_submenu_page_4' ) );
		add_submenu_page( 'dev-dashboard.php', '$get_bio', '$get_bio', 'edit_posts', 'dev-subdash-5.php', array( __CLASS__, 'dev_submenu_page_5' ) );
	}

	public static function dev_menu_page() {
		global $menu, $submenu, $wpdb;
		echo '<div class="wrap">';
		echo '<h2>' . __NAMESPACE__ . ' || ' . __CLASS__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';
		new \PMPro_Diagnostics\inc\PMPro_Notification( 'Testing Error Notification', 'error' );

		echo '<h3>You are viewing this menu from a ';
		echo Setup_Functions::detect_mobile_device();
		echo ' device</h3>';
		$get_post_types = get_post_types();
		echo ' <pre> $get_post_types ';
		print_r( $get_post_types );
		echo '</pre>';

		// new PMPro_Notification( 'Testing Error Notification', 'error' );
		echo '</div>';
	}

	public static function dev_submenu_page_1() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';

		echo ' <pre> $customer_info ';
		// print_r( $customer_info );
		echo '</pre>';

		echo '</div>';
	}

	public static function dev_submenu_page_2() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';

		echo ' <pre> $customer_info ';
		// print_r( $customer_info );
		echo '</pre>';

		echo '</div>';
	}

	public static function dev_submenu_page_3() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';

		echo '<pre>';
		// print_r( $customer_info[0] );
		echo '</pre>';

		echo '</div>';
	}

	public static function dev_submenu_page_4() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';

		echo ' <pre> $customer_info ';
		// print_r( $customer_info );
		echo '</pre>';

		echo '</div>';
	}

	public static function dev_submenu_page_5() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo '<h2>' . __FILE__ . ' line ' . __LINE__ . '</h2>';

		echo ' <pre> $get_bio ';
		print_r( $get_bio );
		echo '</pre> admin_bar_menu ';

		// $nodes = return_node_ids_to_toolbar();
		echo '</div>';
	}

	public static function progress_bar_list( $menu_key ) {
		// $plucked_menu = Setup_Functions::pluck_menu_items( $menu_key );
		$count = count( $plucked_menu );
		?>
		   <div class="progress" style="clear: both;width: 100%;">
		 <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="7" style="width: <?php echo 6 * 100 / 7; ?>%;">
		   Step 6 of 7
		 </div>
		   </div>
		<?php
		$show_progress = '<ul>';
		foreach ( $plucked_menu as $key => $page_title ) {
			$info = Setup_Functions::filtered_menu_items( $menu_key, $page_title );

			// echo '<li>' . $info['url'] . '</li>';
			$show_progress .= '<li><a href="' . $info['url'] . '">Step ' . ( $key + 1 ) . '</a></li>';
		}
		$show_progress .= '</ul>';

		// return $show_progress;
	}

	public static function wp_get_menu_array( $current_menu ) {

		$array_menu = wp_get_nav_menu_items( $current_menu );
		$menu       = array();
		$menu_count = 0;
		foreach ( $array_menu as $m ) {
			if ( empty( $m->menu_item_parent ) ) {
				$menu[ $menu_count ]             = array();
				$menu[ $menu_count ]['ID']       = $m->ID;
				$menu[ $menu_count ]['title']    = $m->title;
				$menu[ $menu_count ]['url']      = $m->url;
				$menu[ $menu_count ]['children'] = array();
			}
			$menu_count++;
		}
		$submenu       = array();
		$submenu_count = 0;
		foreach ( $array_menu as $m ) {
			if ( $m->menu_item_parent ) {
				$submenu[ $submenu_count ]                          = array();
				$submenu[ $submenu_count ]['ID']                    = $m->ID;
				$submenu[ $submenu_count ]['title']                 = $m->title;
				$submenu[ $submenu_count ]['url']                   = $m->url;
				$menu[ $m->menu_item_parent ]['children'][ $m->ID ] = $submenu[ $m->ID ];
			}
			$submenu_count++;
		}
		return $menu;
	}
}
