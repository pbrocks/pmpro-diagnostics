<?php
namespace CSC_Diagnostics\inc\classes;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class Admin_Menus {

	public static function init() {
		// add_action( 'admin_menu', array( __CLASS__, 'dev_menu' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'move_spacer1_admin_menu_item' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'move_options_page_admin_menu' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'change_pages_label_to_something_else' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'change_posts_label_to_updates' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'remove_some_admin_menus' ), 990 );
	}

	public static function dev_menu_page() {
		echo '<h2>Dev Admin Menu</h2><h3>' . basename( __FUNCTION__ ). '</h3>';
		echo do_shortcode( '[info-message content="something will go here"]' );
		echo '<p>You can find this file in ';
		echo '<span style="font-weight: 700;color:salmon;">' . esc_url( plugins_url( '/', __FILE__ ) ) . '</span>';

		echo '<pre>';
		echo '<p>get_stylesheet_directory => ' . get_stylesheet_directory();
		echo '<p>get_stylesheet_directory_uri => ' . get_stylesheet_directory_uri();
		echo '<p>get_stylesheet_uri => ' . get_stylesheet_uri();

		echo '<br>';
		echo '</pre>';

		$mods = get_theme_mods();
		echo '<pre>';
		var_dump($mods);
		echo '</pre>';
	}


	public static function sample_menu_page() {

			echo '<h2>Sample Admin Menu</h2>';
			echo '<pre>';
			echo '<p>You can find this file in ';
			echo plugins_url( '/', __FILE__ );
			echo '<br>';
			echo '</pre>';
	}

	public static function move_spacer1_admin_menu_item( $menu_order ) {
		global $menu;

		$spacer_admin_menu = $menu[4];

		if ( ! empty( $spacer_admin_menu ) ) {

			// Add 'woocommerce' to bottom of menu
			 $menu[15] = $spacer_admin_menu;

			// Remove initial 'woocommerce' appearance
			unset( $menu[4] );
		}
		return $menu_order;
	}

	public static function move_options_page_admin_menu( $menu_order ) {
		global $menu;

		$infra_admin_menu = $menu[81];

		if ( ! empty( $infra_admin_menu ) ) {

			// Add 'woocommerce' to bottom of menu
			 $menu[37] = $infra_admin_menu;

			// Remove initial 'woocommerce' appearance
			unset( $menu[81] );
		}
		return $menu_order;
	}


	public static function change_pages_label_to_something_else() {
		global $menu;

		$menu[20][0] = 'Home Panels';
	}

	public static function change_s_label_to_something_else() {
		global $menu;

		$menu[20][0] = 'Home ';
	}

	public static function change_posts_label_to_updates() {
		global $menu;

		$menu[5][0] = 'Current Updates';

	}


	public static function remove_some_admin_menus() {
		global $menu, $submenu;
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'edit.php' );
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
	}


	public static function dev_menu() {
		// if ( current_user_can( 'manage_network' ) ) { // for multisite
		if ( current_user_can( 'manage_options' ) ) {
			add_menu_page( 'Dev Admin', 'Dev Admin', 'manage_options', 'dev-admin-menu.php',  array( __CLASS__, 'dev_menu_page' ), 'dashicons-tickets', 13 );
			add_submenu_page(  'dev-admin-menu.php', 'RAIN Taxonomy', 'RAIN Taxonomy', 'manage_options', 'rain-taxonomy.php',  array( __CLASS__, 'rain_taxonomy_page' ) );
			add_submenu_page(  'dev-admin-menu.php', 'Dev Arrays', 'Arrays', 'manage_options', 'dev-arrays.php',  array( __CLASS__, 'dev_arrays_page' ) );
			add_submenu_page(  'dev-admin-menu.php', 'Dev Submenu', 'Menus/Submenus', 'manage_options', 'dev-submenu.php',  array( __CLASS__, 'dev_submenu_page' ) );
			add_submenu_page(  'dev-admin-menu.php', 'Admin Page HTML', 'Admin Page HTML', 'manage_options', 'dev-taxonomy.php',  array( __CLASS__, 'sample_html_admin_page' ) );
		}
	}

	public static function sample_html_admin_page() {
	?>
		<h3><?php esc_attr_e( '2 Columns Layout: relative (%)', 'wp_admin_style' ); ?></h3>

		<div class="wrap">

			<h2><?php esc_attr_e( 'Heading String', 'wp_admin_style' ); ?></h2>
			<div id="col-container">

				<div id="col-right">

					<div class="col-wrap">
						<?php esc_attr_e( 'Content Header', 'wp_admin_style' ); ?>
						<div class="inside">
							<p><?php esc_attr_e( 'WordPress started in 2003 with a single bit of code to enhance the typography of everyday writing and with fewer users than you can count on your fingers and toes. Since then it has grown to be the largest self-hosted blogging tool in the world, used on millions of sites and seen by tens of millions of people every day.', 'wp_admin_style' ); ?></p>
						</div>

					</div>
					<!-- /col-wrap -->

				</div>
				<!-- /col-right -->

				<div id="col-left">

					<div class="col-wrap">
						<?php esc_attr_e( 'Content Header', 'wp_admin_style' ); ?>
						<div class="inside">
							<p>
								<?php esc_attr_e( 'info coming from <a href="https://github.com/bueltge/WordPress-Admin-Style">https://github.com/bueltge/WordPress-Admin-Style</a>', 'wp_admin_style' ); ?>
							</p>
							<p><?php esc_attr_e( 'Everything you see here, from the documentation to the code itself, was created by and for the community. WordPress is an Open Source project, which means there are hundreds of people all over the world working on it. (More than most commercial platforms.) It also means you are free to use it for anything from your cat’s home page to a Fortune 500 web site without paying anyone a license fee and a number of other important freedoms.', 'wp_admin_style' ); ?></p>
						</div>
					</div>
					<!-- /col-wrap -->

				</div>
				<!-- /col-left -->

			</div>
			<!-- /col-container -->

		</div> <!-- .wrap -->
	<?php
	}

	public static function dev_arrays_page() {
		echo '<h2>' . basename( __FUNCTION__ ). '</h2>';
		echo '<pre>';
		echo 'You can find this file in  <br>';
		echo plugins_url( '/', __FILE__ );
		$cpts = get_post_types();
		print_r( $cpts );
		$cpt = get_post_type_object( 'new_relic_use_cases' );
		$cpt = get_post( 205 );
		print_r( $cpt );

		echo '<br>';
		echo '</pre>';
		$use_cases = new \WP_Query( array( 'post_type' => 'use_cases' ) );
		echo '<pre> Use Cases'; print_r( $use_cases ); echo '</pre>';
		echo '<pre>';
		    print_r( get_field('use_cases')  );
		echo '</pre>';
		die;
	}

	public static function dev_submenu_page() {
		global $menu;
		echo '<h2>' . basename( __FUNCTION__ ). '</h2>';
		echo '<pre>';
		echo 'You can find this file in  <br>';
		echo plugins_url( '/', __FILE__ );
		print_r( $menu );
		echo '<br>';
		echo '<br>ACF path =>' .  plugin_dir_path( __FILE__ ) . 'acf-json<br>';
		echo '</pre>';

	}
}
