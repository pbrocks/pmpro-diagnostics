<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


class Customizing_ImageFirst {

	public function __construct() {
		add_action( 'customize_controls_init', array( $this, 'set_customizer_preview_url' ) );
		add_action( 'wp_head', array( $this, 'enqueue_customizer_styles' ) );
		add_action( 'customize_register', array( $this, 'wp_customizer_manager' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_customizer_scripts' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'init', array( $this, 'disable_admin_bar' ), 9 );
		// add_action( 'customize_preview_init', array( $this, 'imagefirst_live_preview' ) );
	}

	public function set_customizer_preview_url() {
		global $wp_customize;
		if ( ! isset( $_GET['url'] ) ) {
			$wp_customize->set_preview_url( get_permalink( get_page_by_title( 'Launchpad' ) ) );
		}
	}

	public function enqueue_customizer_styles() {
		$site_accent_color = get_theme_mod( 'site_accent_color' );
		$footer_icon_color = get_theme_mod( 'footer_icon_color' );
		?>
		<style type="text/css">
		.well, .navbar-fixed-bottom {
			/*background-color: #182863;*/
			background-color: <?php echo $site_accent_color; ?>;
		}
		#colophon span.glyphicon {
			color: <?php echo $footer_icon_color; ?>;
		}
	</style>
	<?php
	}

	public function enqueue_customizer_scripts() {
		if ( true === self::return_theme_mod( 'imagefirst_css' ) ) {
			wp_enqueue_style( 'imagefirst', plugins_url( '/css/imagefirst.css', __FILE__ ) );
		}
	}

	public function enqueue_frontend_scripts() {
		wp_register_style( 'frontend-dev', plugins_url( '/css/frontend-dev.css', __FILE__ ) );
		wp_enqueue_style( 'frontend-dev' );
	}

	/**
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 */
	public function hide_admin_bar_in_edit_user_settings() {
		$current_screen = get_current_screen();
		if ( 'user-edit' === $current_screen->id || 'profile' === $current_screen->id ) {
			wp_enqueue_style( 'hide-profile-toolbar-option', plugins_url( '/css/hide-profile-toolbar-option.css', __FILE__ ) );
		}
	}
	/**
	 * [disable_admin_bar description]
	 *
	 * @param  [type] [description]
	 * @return [type]             [description]
	 */
	public function disable_admin_bar() {
		if ( current_user_can( 'manage_options' ) && true === get_option( 'show_admin_bar_on_frontend' ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
			add_action( 'admin_enqueue_scripts', array( $this, 'hide_admin_bar_in_edit_user_settings' ) );
		}
	}


	/**
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 */
	public function imagefirst_live_preview() {
		wp_enqueue_script( 'imagefirst-preview', plugins_url( '/js/imagefirst-preview.js', __FILE__ ), array( 'jquery', 'imagefirst-preview' ), '', true );
	}

	/**
	 * [wp_customizer_manager description]
	 *
	 * @param  [type] $customizer_additions [description]
	 * @return [type]             [description]
	 */
	public function wp_customizer_manager( $customizer_additions ) {
		self::create_panel( $customizer_additions );
		self::file_attachments_section( $customizer_additions );
		self::image_attachments_section( $customizer_additions );
		self::create_alert_section( $customizer_additions );
		self::default_section( $customizer_additions );
		self::create_admin_section( $customizer_additions );
	}

	/**
	 * A section to show how you use the default customizer controls in WordPress
	 *
	 * @param Obj $customizer_additions - WP Manager
	 *
	 * @return Void
	 */
	private function create_panel( $customizer_additions ) {
		$customizer_additions->add_panel(
			'imagefirst_panel', array(
				'title'       => 'ImageFirst Panel',
				'label'       => 'ImageFirst Panel',
				'description' => 'This is a description of this ImageFirst panel',
				'priority'    => 10,
			)
		);
	}

	/**
	 *
	 */
	private function default_section( $customizer_additions ) {
		$customizer_additions->add_setting(
			'show_footer_info', array(
				'default'   => true,
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_footer_info', array(
					'label'       => 'Show Footer Info',
					'description'   => 'Show Footer Info => slide to turn on setting.',
					'settings'   => 'show_footer_info',
					'section' => 'title_tagline',
					'type'    => 'ios',
					'priority' => 44,
				)
			)
		);

		/**
		 * Textbox control
		 */
		$customizer_additions->add_setting(
			'site_footer_info', array(
				'default'      => 'ImageFirst',
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			'site_footer_info', array(
				'section'  => 'title_tagline',
				'type'     => 'text',
				'settings' => 'site_footer_info',
				'label'       => 'Footer Info',
				'description' => 'Footer Info text =  \'site_footer_info\'.',
				'priority' => 45,
			)
		);

		/**
		 * Section Background color control
		 */
		$customizer_additions->add_setting(
			'site_accent_color', array(
				'default'      => '#182863',
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new \WP_Customize_Color_Control(
				$customizer_additions,
				'site_accent_color',
				array(
					'label'      => __( 'Section Background Color', 'imagefirst' ),
					'section'    => 'colors',
					'settings'   => 'site_accent_color',
				)
			)
		);

		/**
		 * Footer Icon color control
		 */
		$customizer_additions->add_setting(
			'footer_icon_color', array(
				'default'      => '#fff',
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new \WP_Customize_Color_Control(
				$customizer_additions,
				'footer_icon_color',
				array(
					'label'      => __( 'Footer Icon Color', 'imagefirst' ),
					'section'    => 'colors',
					'settings'   => 'footer_icon_color',
				)
			)
		);

	}

	/**
	 * A section to show how you use the default customizer controls in WordPress
	 *
	 * @param Obj $customizer_additions - WP Manager
	 *
	 * @return Void
	 */
	private function file_attachments_section( $customizer_additions ) {
		$customizer_additions->add_section(
			'imagefirst_attachments', array(
				'title'          => 'ImageFirst File Attachments',
				'description'    => 'Description of the Attachments Section of the ImageFirst panel',
				'priority'       => 30,
				'panel'          => 'imagefirst_panel',
			)
		);
		// Textbox control
		$customizer_additions->add_setting(
			'number_of_files', array(
				'default'        => 4,
				'type'           => 'option',
			)
		);
		$customizer_additions->add_control(
			'number_of_files', array(
				'label'              => 'Number of Files',
				'description'        => esc_html__( 'Number selected below indicates the available file slots for upload.' ),
				'section'            => 'imagefirst_attachments',
				'type'               => 'number',
				'number_of_images'   => 'images',
				'priority'           => 1,
			)
		);

		if ( ! empty( get_option( 'number_of_files' ) ) ) {
			$files = get_option( 'number_of_files' );
		} else {
			$files = 4;
		}
		// =============================
		// = File Upload               =
		// =============================
		$x = 1;
		while ( $x <= $files ) {
			$customizer_additions->add_setting(
				"if_attachment_file_$x", array(
					'default'           => '',
					'capability'        => 'edit_theme_options',
					'type'           => 'option',
				)
			);
			$customizer_additions->add_control(
				new \WP_Customize_Upload_Control(
					$customizer_additions, "if_attachment_file_$x", array(
						'label'    => __( "File $x Upload", 'imagefirst' ),
						'section'  => 'imagefirst_attachments',
						'settings' => "if_attachment_file_$x",
						'priority' => 15,
					)
				)
			);
			$x++;
		}
	}

	/**
	 * A section to show how you use the default customizer controls in WordPress
	 *
	 * @param Obj $customizer_additions - WP Manager
	 *
	 * @return Void
	 */
	private function image_attachments_section( $customizer_additions ) {
		$customizer_additions->add_section(
			'imagefirst_images', array(
				'title'          => 'ImageFirst Image Attachments',
				'description'    => 'Description of the Image Attachments Section of the ImageFirst panel',
				'priority'       => 30,
				'panel'          => 'imagefirst_panel',
			)
		);

		// Textbox control
		$customizer_additions->add_setting(
			'number_of_images', array(
				'default'        => 4,
				'type'           => 'option',
			)
		);
		$customizer_additions->add_control(
			'number_of_images', array(
				'label'              => 'Number of Images',
				'description'        => esc_html__( 'Number selected below indicates the available image slots for upload.' ),
				'section'            => 'imagefirst_images',
				'type'               => 'number',
				'number_of_images'   => 'images',
				'priority'           => 1,
			)
		);

		if ( ! empty( get_option( 'number_of_images' ) ) ) {
			$images = get_option( 'number_of_images' );
		} else {
			$images = 4;
		}

		// =============================
		// = Image Upload              =
		// =============================
		$x = 1;
		while ( $x <= $images ) {
			$customizer_additions->add_setting(
				"if_attachment_image_$x",
				array(
					'default'       => '',
					'capability'    => 'edit_theme_options',
					'type'          => 'option',
					'transport'     => 'refresh',
				)
			);

			$customizer_additions->add_control(
				new \WP_Customize_Image_Control(
					$customizer_additions, "if_attachment_image_$x",
					array(
						'label' => __( "Image $x Upload", 'imagefirst' ),
						'section' => 'imagefirst_images',
						'settings' => "if_attachment_image_$x",
						'button_labels' => array( // Optional.
							'select' => __( 'Select Image' ),
							'change' => __( 'Change Image' ),
							'remove' => __( 'Remove' ),
							'default' => __( 'Default' ),
							'placeholder' => __( 'No image selected' ),
							'frame_title' => __( 'Select Image' ),
							'frame_button' => __( 'Choose Image' ),
							'priority' => 5,
						),
					)
				)
			);
			$x++;
		}
	}

	/**
	 * A section to show how you use the default customizer controls in WordPress
	 *
	 * @param Obj $customizer_additions - WP Manager
	 *
	 * @return Void
	 */
	private function create_alert_section( $customizer_additions ) {
		$customizer_additions->add_section(
			'imagefirst_section', array(
				'title'          => 'ImageFirst Alert Section',
				'description'    => 'Description of the ImageFirst Section of the ImageFirst panel',
				'priority'       => 35,
				'panel'          => 'imagefirst_panel',
			)
		);

		/**
		 * Adding a Checkbox Toggle
		 */
		$customizer_additions->add_setting(
			'show_imagefirst_alert', array(
				'default'   => false,
				'transport' => 'refresh',
			)
		);

		if ( ! class_exists( 'Customizer_Toggle_Control' ) ) {
			require_once dirname( __FILE__ ) . '/controls/checkbox/toggle-control.php';
		}

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_imagefirst_alert', array(
					'label'       => 'Show Front Alert Message',
					'description'   => 'Show Front Alert Message',
					'settings'   => 'show_imagefirst_alert',
					'section' => 'imagefirst_section',
					'type'    => 'ios',
					'priority' => 1,
				)
			)
		);

		/**
		 * Textbox control
		 */
		$customizer_additions->add_setting(
			'date_of_alert', array(
				'default'      => date( 'l, jS F Y' ),
			)
		);

		$customizer_additions->add_control(
			'date_of_alert', array(
				'section'  => 'imagefirst_section',
				'type'     => 'text',
				'label'       => 'Date of Alert',
				'description' => 'Effective date for this alert or announcement.',
				'priority' => 1,
			)
		);

		/**
		 * Adding a text area control
		 */
		if ( ! class_exists( 'Textarea_Custom_Control' ) ) {
			include_once dirname( __FILE__ ) . '/controls/text/textarea-custom-control.php';
		}
		$customizer_additions->add_setting(
			'front_alert_textbox', array(
				'default'        => 'To add information here, you can edit this text box in Customize > ImageFirst Alert Section.',
			)
		);
		$customizer_additions->add_control(
			new controls\text\Textarea_Custom_Control(
				$customizer_additions, 'front_alert_textbox', array(
					'label'       => 'Front Alert Message',
					'section' => 'imagefirst_section',
					'settings'   => 'front_alert_textbox',
					'priority' => 11,
				)
			)
		);

		/**
		 * Adding a Checkbox Toggle
		 */
		$customizer_additions->add_setting(
			'second_imagefirst_alert', array(
				'default'   => false,
				'transport' => 'refresh',
				'capabilities' => 'manage_options',
			)
		);

		if ( ! class_exists( 'Customizer_Toggle_Control' ) ) {
			require_once dirname( __FILE__ ) . '/controls/checkbox/toggle-control.php';
		}

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'second_imagefirst_alert', array(
					'label'       => 'Show Some other Toggle',
					'description'   => 'Show Some other Toggle => slide to turn on setting. Toggle is equivalent to a checkbox.',
					'settings'   => 'second_imagefirst_alert',
					'section' => 'imagefirst_second_section',
					'type'    => 'ios',
					'priority' => 1,
				)
			)
		);
	}

	/**
	 *
	 */
	private function create_admin_section( $customizer_additions ) {
		$customizer_additions->add_section(
			'imagefirst_admin_section', array(
				'title'          => 'ImageFirst Admin Section',
				'description'    => 'Description of the ImageFirst Admin Section of the ImageFirst panel',
				'priority'       => 25,
				'panel'          => 'imagefirst_panel',
			)
		);

		// =============================
		// = Page Dropdown             =
		// =============================
		$customizer_additions->add_setting(
			'if_attachment[page_select_1]', array(
				'capability'     => 'edit_theme_options',
				'type'           => 'option',
			)
		);
		$customizer_additions->add_control(
			'page_select_1', array(
				'label'      => __( 'Page Select', 'imagefirst' ),
				'section'    => 'imagefirst_admin_section',
				'type'    => 'dropdown-pages',
				'settings'   => 'if_attachment[page_select_1]',
			)
		);

		if ( ! class_exists( 'Customizer_Toggle_Control' ) ) {
			require_once dirname( __FILE__ ) . '/controls/checkbox/toggle-control.php';
		}

		if ( ! class_exists( 'Page_Dropdown_Custom_Control' ) ) {
			require_once dirname( __FILE__ ) . '/controls/select/page-dropdown-custom-control.php';
		}
		$customizer_additions->add_setting(
			'select_page_for_app', array(
				// 'default'   => false,
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\select\Page_Dropdown_Custom_Control(
				$customizer_additions,
				'select_page_for_app', array(
					'label'       => 'Select Page App',
					'description'   => 'Select Page App => slide to turn on setting.',
					'settings'   => 'select_page_for_app',
					'section' => 'imagefirst_admin_section',
					// 'type'    => 'ios',
					'priority' => 12,
				)
			)
		);
		$customizer_additions->add_setting(
			'page_title_position', array(
				'default'        => 'header',
				'transport'      => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new \WP_Customize_Control(
				$customizer_additions,
				'page_title_position',
				array(
					'label'          => __( 'Page Title Position', 'theme_name' ),
					'section'        => 'imagefirst_admin_section',
					'settings'       => 'page_title_position',
					'type'           => 'radio',
					'priority'       => 1,
					'choices'        => array(
						'header'  => __( 'Header' ),
						'body'    => __( 'Body' ),
						'neither'    => __( 'Don\'t Show' ),
					),
				)
			)
		);

		$customizer_additions->add_setting(
			'show_progress_nav', array(
				'default'        => true,
				'transport'      => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_progress_nav', array(
					'label'       => 'Show Progress Nav',
					'description'   => 'Progress Nav is a header element that shows the steps in the process based on the pages set in the Sidebar menu ' . get_theme_mod( 'show_progress_nav' ) . ' option ' . get_option( 'show_progress_nav' ) . ' nope',
					'settings'   => 'show_progress_nav',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 3,
				)
			)
		);

		$customizer_additions->add_setting(
			'show_progress_bar', array(
				'default'        => false,
				'transport'      => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_progress_bar', array(
					'label'       => 'Show Progress Bar',
					'description'   => 'Progress Bar is currently under construction, but it is designed to help visualize where in process agent is.',
					'settings'   => 'show_progress_bar',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 4,
				)
			)
		);

		$customizer_additions->add_setting(
			'show_footer_menu', array(
				'default'   => false,
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_footer_menu', array(
					'label'       => 'Show Footer Directional Menu',
					'description'   => 'Show Footer Directional Menu => slide to turn on setting.',
					'settings'   => 'show_footer_menu',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 6,
				)
			)
		);

		$customizer_additions->add_setting(
			'show_admin_bar_on_frontend', array(
				'default'        => true,
				'transport'      => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_admin_bar_on_frontend', array(
					'label'       => 'Show Adminbar Toggle',
					'description'   => 'Show adminbar on frontend for non-admins theme_mod ' . get_theme_mod( 'show_admin_bar_on_frontend' ) . '  option => ' . get_option( 'show_admin_bar_on_frontend' ) . ' or nope',
					'settings'   => 'show_admin_bar_on_frontend',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 9,
				)
			)
		);

		$customizer_additions->add_setting(
			'show_admin_info', array(
				'default'   => false,
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_admin_info', array(
					'label'       => 'Show Admin Info',
					'description'   => 'Show Admin Info => slide to turn on setting.',
					'settings'   => 'show_admin_info',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 11,
				)
			)
		);

		$customizer_additions->add_setting(
			'show_dev_info', array(
				'default'   => false,
				'transport' => 'refresh',
			)
		);

		$customizer_additions->add_control(
			new controls\checkbox\Customizer_Toggle_Control(
				$customizer_additions,
				'show_dev_info', array(
					'label'       => 'Show Developer Info',
					'description'   => 'Show Developer Info => slide to turn on setting.',
					'settings'   => 'show_dev_info',
					'section' => 'imagefirst_admin_section',
					'type'    => 'ios',
					'priority' => 12,
				)
			)
		);

	}

	/**
	 *
	 */
	public function return_theme_mod( $mod ) {
		$return_mod = get_theme_mod( $mod );
		return $return_mod;
	}
}
