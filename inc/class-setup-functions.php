<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


class Setup_Functions {
	public static function init() {
		add_shortcode( 'category', array( __CLASS__, 'panel_shortcode' ) );
		add_shortcode( 'info-message', array( __CLASS__, 'info_message_toggle' ) );
		// add_shortcode( 'bartag',  array( __CLASS__, 'bartag_func' ) );
	}

	public static function wp_admin_style() {
		// wp_register_style( 'admin', plugins_url( '../css/admin.css', __FILE__ ) );
		// wp_enqueue_style( 'admin' );
		// wp_register_script( 'admin-js', plugins_url( '../js/admin.js',  __FILE__ ) );
		// wp_enqueue_script( 'admin-js' );
		wp_enqueue_script( 'diagnostic', plugins_url( '../js/window-size.js',  __FILE__ ), array( 'jquery' ) );
	}

	public static function detect_mobile_device() {
		$detect_device = '';
		if ( wp_is_mobile() ) {
			$detect_device = 'mobile';
		} else {
			$detect_device = 'desktop';
		}
		return $detect_device;
	}
	public static function bartag_func( $atts ) {
		$a = shortcode_atts(
			array(
				'foo' => 'something',
				'bar' => 'something else',
			), $atts
		);

		return "foo = {$a['foo']}";
	}
	public static function info_message_toggle( $message ) {
		// Attributes.
		$message = shortcode_atts(
			array(
				'title' => '',
				'content' => '',
				'placement' => 'auto',
				'wide' => '35%',
			),
			$message
		);
		sanitize_text_field( $message['content'] );

		$style = ( '' !== $message['wide'] ? ' style="width:' . $message['wide'] . '"' : '' );

		$info_message = '<div class="info-tip"' . $style . '><a href="#" data-toggle="popover" title="' . $message['title'] . '" data-content="' . $message['content'] . '" data-placement="' . $message['placement'] . '"><img class="info-tip" src="' . plugins_url( 'images/info.png', __FILE__ ) . '" /></a></div>';
		return $info_message;
	}

	public static function frontend_menu_object( $menu ) {
		$menu_object = wp_get_nav_menu_object( $menu );
		return $menu_object;
	}

	public static function frontend_menu_items( $menu ) {
		$menu_items = wp_get_nav_menu_items( $menu );
		return $menu_items;
	}

	public static function pluck_menu_items( $menu_key ) {
		$menu_pluck = self::frontend_menu_items( 'sidebar' );
		$plucked_menu = wp_list_pluck( $menu_pluck, $menu_key );
		return $plucked_menu;
	}

	public static function return_menu_item_key( $menu_key, $page_name ) {
		$menu_pluck = self::frontend_menu_items( 'sidebar' );
		$plucked_menu = wp_list_pluck( $menu_pluck, $menu_key );
		$count = count( $plucked_menu );
		foreach ( $plucked_menu as $key => $value ) {
			if ( $page_name === $value ) {
				return $key;
			}
		}
	}

	public static function filter_menu_item_object( $menu_key, $page_title ) {
		$menu_filter = self::frontend_menu_items( 'sidebar' );
		$criteria = array(
			$menu_key => $page_title,
		);

		$filtered_menu = wp_list_filter( $menu_filter, $criteria );
		return $filtered_menu;
	}


	public static function filtered_menu_items( $menu_key, $page_title ) {
		$menu_item_key = self::return_menu_item_key( $menu_key, $page_title );
		$menu_filter = self::frontend_menu_items( 'sidebar' );
		$criteria = array(
			$menu_key => $page_title,
		);
		$filtered_menu = wp_list_filter( $menu_filter, $criteria );
		$filtered_menu_items = array();

		$filtered_menu_items['ID'] = $filtered_menu[ $menu_item_key ]->ID;
		$filtered_menu_items['title'] = $filtered_menu[ $menu_item_key ]->title;
		$filtered_menu_items['url'] = $filtered_menu[ $menu_item_key ]->url;

		return $filtered_menu_items;
	}

	public static function filtered_menu_item_link( $menu_key, $page_title ) {
		$menu_item_key = self::return_menu_item_key( $menu_key, $page_title );
		$menu_filter = self::frontend_menu_items( 'sidebar' );
		$criteria = array(
			$menu_key => $page_title,
		);

		$filtered_menu = wp_list_filter( $menu_filter, $criteria );
		$filtered_menu_link = $filtered_menu[ $menu_item_key ]->url;
		return $filtered_menu_link;
	}

	public static function menu_item_object( $menu_item_key ) {

		$menu_filter = self::frontend_menu_items( 'sidebar' );
		$menu_item_object = $menu_filter[ $menu_item_key ];
		return $menu_item_object;
	}

	public static function menu_item_link( $menu_item_key ) {

		$menu_filter = self::frontend_menu_items( 'sidebar' );
		$menu_item_link = $menu_filter[ $menu_item_key ]->url;
		return $menu_item_link;
	}

	public static function get_stepped_menu( $menu_key, $current_title ) {
		$plucked_menu = self::pluck_menu_items( $menu_key );
		$deliver_menu = '<div class="navbar">
		<div class="navbar-inner"><ul class="nav nav-pills">';
		foreach ( $plucked_menu as $key => $page_title ) {
			$info = self::filtered_menu_items( $menu_key, $page_title );

			if ( 0 === $key ) {
				$deliver_menu .= '<li><a href="' . $info['url'] . '">Start</a></li>';
			} elseif ( $page_title === $current_title ) {
				$deliver_menu .= '<li class="active"><a href="' . $info['url'] . '">' . $info['title'] . '</a><i class="right"></i></li>';
			} else {
				$deliver_menu .= '<li><a href="' . $info['url'] . '">Step ' . ($key + 1) . '</a></li>';
			}
		}
		$deliver_menu .= '</ul></div></div>';

		return $deliver_menu;
	}

	public static function show_progress_bar( $menu_key, $page_name ) {
		$menu_pluck = self::frontend_menu_items( 'sidebar' );
		$plucked_menu = wp_list_pluck( $menu_pluck, $menu_key );
		$count = count( $plucked_menu );
		foreach ( $plucked_menu as $key => $value ) {
			if ( $page_name === $value ) {
				$step = ( $key + 1 );
			}
		}
		// $plucked_menu = self::pluck_menu_items( 'title' );
		// $count = count( $plucked_menu );
?>
   <div class="progress" style="clear: both;width: 100%;">
	 <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="<?php echo $count; ?>" style="width: <?php echo intval( $step ) * 100 / $count; ?>%;">
	   Step <?php echo $step; ?> of <?php echo $count; ?>
	 </div>
   </div>
<?php
	}

	public static function panel_shortcode( $atts, $content = null ) {
		$panelId = uniqid( 'panel' );

		$heading = '';
		if ( array_key_exists( 'heading', $atts ) ) {
			$heading = $atts['heading'];
		}

		$content = do_shortcode( $content );

		return '
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a class="btn-block" data-toggle="collapse" href="#' . $panelId . '">' . $heading . '</a>
				</h4>
			</div>
			<div id="' . $panelId . '" class="panel-collapse collapse">
				<div class="panel-body">' . $content . '</div>
			</div>
		</div>
		';
	}
}
