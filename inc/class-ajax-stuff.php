<?php
namespace PMPro_Diagnostics\inc;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class AJAX_Stuff {

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'single_fetch_query' ) );
		add_action( 'wp_ajax_get_urls_get_results', array( __CLASS__, 'get_all_published_urls' ) );
		add_action( 'admin_menu', array( __CLASS__, 'get_urls_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_get_urls_scripts' ) );
	}


	public static function single_fetch_query() {

	}


	public static function add_acf_json_load_point( $paths ) {
		$paths[] = plugin_dir_path( __FILE__ ) . 'acf-json';

		return $paths;
	}


	public static function get_urls_admin_page() {
		global $get_urls_settings;
		$get_urls_settings = add_options_page( __( 'URLs Ajax Demo', 'get-urls' ), __( 'URLs Ajax', 'get-urls' ), 'manage_options', 'urls-admin-ajax.php', array( __CLASS__, 'get_urls_render_admin' ) );
	}

	public static function get_urls_render_admin() {
		?>
		<div class="wrap">
		<h2><?php esc_attr_e( 'URLs Ajax Demo', 'get-urls' ); ?></h2>
		<form id="get-urls-form" action="" method="POST">
		<div>
		<input type="submit" name="get-urls-submit" id="get_urls_submit" class="button-primary" value="<?php esc_attr_e( 'Get URLs', 'get-urls' ); ?>"/>
		<img src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" class="waiting" id="get_urls_loading" style="display:none;"/>
		</div>
		</form>
		<div id="get_urls_results"></div>
		</div>
		<?php
	}

	public static function load_get_urls_scripts( $hook ) {
		global $get_urls_settings;
		if ( $hook !== $get_urls_settings ) {
			return;
		}

		wp_enqueue_script( 'get-urls-ajax', plugin_dir_url( __FILE__ ) . '../js/get-urls-ajax.js', array( 'jquery' ) );
		wp_localize_script(
			'get-urls-ajax', 'get_urls_vars', array(
				'get_urls_nonce' => wp_create_nonce( 'get-urls-nonce' ),
			)
		);

	}

	public static function get_urls_process_ajax() {
		if ( ! isset( $_POST['get_urls_nonce'] ) || ! wp_verify_nonce( $_POST['get_urls_nonce'], 'get-urls-nonce' ) ) {
			die( 'Permissions check failed' );
		}
		$downloads = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 5,
			)
		);
		if ( $downloads ) :
			echo '<ul>';
			foreach ( $downloads as $download ) {
				echo '<li>' . get_the_title( $download->ID ) . ' - <a href="' . get_permalink( $download->ID ) . '">' . esc_attr( 'View Download', 'get-urls' ) . '</a></li>';
			}
			echo '</ul>';
			else :
				echo '<p>' . esc_attr_e( 'No results found', 'get-urls' ) . '</p>';
				endif;
			die();
	}

	public static function get_all_published_urls() {
		if ( ! isset( $_POST['get_urls_nonce'] ) || ! wp_verify_nonce( $_POST['get_urls_nonce'], 'get-urls-nonce' ) ) {
			die( 'Permissions check failed' );
		}
		$postTypes = get_post_types(
			[
				'public'              => true,
				'exclude_from_search' => false,
			]
		);

		$query = new \WP_Query(
			[
				'post_type'      => array_values( $postTypes ),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			]
		);

		$array = $query->get_posts();

		foreach ( $array as $key => $value ) {
			echo '<li><a href="' . $value->guid . '" target="_blank">' . $value->guid . '</li>';
		}

		echo '<pre>';
		// var_dump( $var );print_r( $query->get_posts() );
		// print_r( $query->get_posts() );
		echo '</pre>';
	}


	public static function bp_emails_process_ajax() {
		if ( ! isset( $_POST['ptb_nonce'] ) || ! wp_verify_nonce( $_POST['ptb_nonce'], 'ptb-nonce' ) ) {
			die( 'Permissions check failed' );
		}
		$downloads = get_posts(
			array(
				'post_type'      => 'bp-email',
				'posts_per_page' => 5,
			)
		);
		if ( $downloads ) :
			echo '<ul>';
			foreach ( $downloads as $download ) {
				echo '<li>' . get_the_title( $download->ID ) . ' - <a href="' . get_permalink( $download->ID ) . '">' . esc_attr( 'View Download', 'ptb' ) . '</a></li>';
			}
			echo '</ul>';
			else :
				echo '<p>' . esc_attr_e( 'No results found', 'ptb' ) . '</p>';
			endif;
			die();
	}
}
