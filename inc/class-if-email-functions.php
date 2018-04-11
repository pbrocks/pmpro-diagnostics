<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


/**
 * Handles contacts in general (specifically used when dealing with customer contacts)
 */
class IF_Email_Functions {


	function __construct() {
		// add_action( 'init', array( $this, 'goodwill_start_session', 1 ) );
		// add_action( 'send_headers', array( $this, 'goodwill_headers_no_cache' ) );
		add_action( 'send_headers', array( $this, 'goodwill_headers_no_cache' ) );
		// add_action( 'wp_logout', array( $this, 'goodwill_end_session' ) );
		// add_action( 'wp_login', array( $this, 'goodwill_end_session' ) );
		// add_action( 'pre_get_posts', array( $this, 'goodwill_add_editor_styles' ) );
		add_action( 'init', array( $this, 'goodwill_email_template_init' ) );
		add_filter( 'post_updated_messages', array( $this, 'goodwill_email_template_updated_messages' ) );

		add_action( 'transition_post_status', array( $this, 'goodwill_email_template_post_status' ), 10, 3 );
		add_action( 'add_meta_boxes', array( $this, 'goodwill_add_meta_box' ) );
		add_action( 'edit_form_after_title', array( $this, 'goodwill_show_subject_meta_box' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'goodwill_admin_scripts_styles' ) );
		add_action( 'save_post', array( $this, 'goodwill_save_meta_box_data' ) );
		add_shortcode( 'if-email-form', array( $this, 'if_pbrx_email_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_ajax_scripts_styles' ) );
		add_action( 'wp_ajax_get_email_results', array( $this, 'process_email_script_ajax' ) );

	}

	function goodwill_start_session() {
		if ( ! session_id() ) {
			session_set_cookie_params( GOODWILL_SESSION_DURATION );
			session_start();
		}
	}


	function wp_ajax_scripts_styles() {
		wp_enqueue_script( 'pbrx-ajax', plugin_dir_url( __FILE__ ) . 'js/pbrx-ajax.js', array( 'jquery' ) );
		wp_localize_script(
			'pbrx-ajax', 'if_pbrx_vars', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'pbrx_nonce' => wp_create_nonce( 'pbrx-nonce' ),
			)
		);
	}

	function if_pbrx_email_form() {
		?>
		<h2><?php esc_attr_e( 'Send Emails', 'pbrx' ); ?></h2>
		<form id="pbrx-form" action="" method="POST">
			Name: <input type="text" name="name" value="My name"><br>
			E-mail: <input type="text" name="email" value="may@email.com"><br>
					<!-- <select name="site1" id="site1"> -->
			</select>
			<div>
				<input type="submit" name="pbrx-submit" id="pbrx_submit" class="btn btn-success" value="<?php esc_attr_e( 'Send Email', 'pbrx' ); ?>" />
				<img src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" class="waiting" id-"ptb_loading" style="display: none;" />
			</div>
			Stuff here
		</form>
		<div id="pbrx-results"></div>
		<?php
			echo '<h5 class="pbrocks">' . basename( __CLASS__ ) . '::' . __FUNCTION__ . ' on Line ' . __LINE__ . '</h5>';
			?>
	<?php
	}

	function if_ptb_email_form() {
		?>
		<h2><?php esc_attr_e( 'Send Emails', 'ptb' ); ?></h2>
		<form id="ptb-form" action="" method="post">
		<?php
			echo '<h5 class="pbrocks">' . basename( __CLASS__ ) . '::' . __FUNCTION__ . ' on Line ' . __LINE__ . '</h5>';
			?>
		 <p>
			Name: <input type="text" name="name"><br>
			E-mail: <input type="text" name="email"><br>
					<!-- <select name="site1" id="site1"> -->
			</select>
			<div>
				<input type="submit" name="ptb-submit" id="ptb_submit" class="button-primary" value="<?php esc_attr_e( 'Send Email', 'ptb' ); ?>" onClick="submit_me();" />
			</div>
		<div id="ptb-results"></div>
		</form>
	<?php
	}

	function process_email_script_ajax() {

		$array = array( $_POST );
		// $array['blog_id'] = $_POST['site1'];
		// $array['time'] = $_POST['time'];
		// $array['owner_email'] = get_blog_option( $array['blog_id'], 'admin_email' );
		// $email = $array['owner_email'];
		// $array['blogname'] = get_blog_option( $array['blog_id'], 'blogname' );
		// $user = get_user_by( 'email', $array['owner_email'] );
		// $array['user_id'] = $user->ID;
		// $array['user_name'] = $user->display_name;
		// $info = $user;
		echo '<pre>';
		print_r( $array );
		echo '</pre>';
		$array['to'] = 'emailsendto@example.com';
		$array['subject'] = 'The subject';
		$array['body'] = 'The email body content';
		$array['headers'] = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: My Site Name &lt;support@example.com',
		);
		echo '<pre>';
		print_r( $array );
		echo '</pre>';
		// wp_mail( $to, $subject, $body, $headers );
		die();
	}

	function goodwill_end_session() {
		$goodwill_user = ImageFirst_User::get_instance();
		$goodwill_user->clear_customer(); // Removes viewed cookie

		session_destroy();
	}

	/**
	 * Common function to connect to the CCS database
	 *
	 * @return [type] [description]
	 */
	function goodwill_connect_ccs() {
		return new wpdb(
			CCS_DB_USER,
			CCS_DB_PASSWORD,
			'tracs_common', // Database name
			CCS_DB_HOST
		);
	}

	/**
	 * Register editor styles per post type before an editor appears
	 *
	 * @since Goodwill 1.0
	 *
	 * @return void
	 */
	// function goodwill_add_editor_styles() {
	// global $post;
	// if ( isset( $post ) ) {
	// $post_type = get_post_type( $post->ID );
	// $editor_style = 'editor-style-' . $post_type . '.css';
	// add_editor_style( $editor_style );
	// }
	// }
	/**
	 * Register an email template type
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function goodwill_email_template_init() {
		$labels = array(
			'name' => 'Email Templates',
			'singular_name' => 'Email Template',
			'all_items' => 'All Email Templates',
			'add_new_item' => 'Add New Email Template',
			'edit_item' => 'Edit Email Template',
			'new_item' => 'New Email Template',
			'view_item' => 'View Email Template',
			'search_items' => 'Search Email Templates',
			'not_found' => 'No email templates found',
			'not_found_in_trash' => 'No email templates found in Trash',
		);

		$args = array(
			'label' => 'Email Templates',
			'labels' => $labels,
			'description' => 'Email templates used when delivering the promise to customer contacts',
			'public' => true,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'menu_icon' => 'dashicons-email',
			'supports' => array(
				'title',
				'editor',
				'page-attributes',
			),
		);
		register_post_type( 'email-template', $args );
	}

	/**
	 * Email template update messages
	 *
	 * See /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages
	 */
	function goodwill_email_template_updated_messages( $messages ) {
		$post = get_post();
		$post_type = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['email-template'] = array(
			0 => '',
			1 => 'Email template updated.',
			2 => 'Custom field updated.',
			3 => 'Custom field deleted.',
			4 => 'Email template updated.',
			5  => isset( $_GET['revision'] ) ? sprintf( 'Email template restored to revision from %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => 'Email template published.',
			7  => 'Email template saved.',
			8  => 'Email template submitted.',
			'your-plugin-textdomain',
			9  => sprintf( 'Email template scheduled for: <strong>%1$s</strong>.', date_i18n( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
			10 => 'Email template draft updated.',
		);

		return $messages;
	}

	/**
	 * Handle status transitions for email-template post types
	 *
	 * @link http://codex.wordpress.org/Post_Status_Transitions
	 */
	function goodwill_email_template_post_status( $new_status, $old_status, $post ) {
		if ( $post->post_type === 'email-template' && $new_status === 'publish' && $old_status !== $new_status ) {
			$post->post_status = 'private';
			wp_update_post( $post );
		}
	}

	function goodwill_add_meta_box() {

		$screens = array( 'email-template' );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'goodwill_email_template_subject',
				'Subject',
				'goodwill_meta_box_subject_callback',
				$screen,
				'normal',
				'default'
			);

			add_meta_box(
				'goodwill_email_template_help',
				'Shortcodes',
				'goodwill_meta_box_shortcodes_callback',
				$screen,
				'side',
				'core'
			);
		}
	}

	function goodwill_show_subject_meta_box() {
		if ( 'email-template' !== get_post_type() ) {
			return;
		}

		global $post, $wp_meta_boxes;

		do_meta_boxes( get_current_screen(), 'normal', $post );

		unset( $wp_meta_boxes['email-template']['normal'] );
	}


	function goodwill_admin_scripts_styles() {
		wp_register_style(
			'goodwill_admin_css',
			get_template_directory_uri() . '/admin-style.css',
			false,
			'1.0'
		);
		wp_enqueue_style( 'goodwill_admin_css' );

		wp_enqueue_script(
			'goodwill_admin_script',
			get_template_directory_uri() . '/js/goodwill-admin.js'
		);
	}

	function goodwill_meta_box_shortcodes_callback( $post ) {
		echo '<dl>';
		echo '<dt><code>[template-contact-name]</code></dt><dd>The contact\'s name</dd>';
		echo '<dt><code>[template-attached-materials]</code></dt><dd>List of attached materials with links</dd>';
		echo '<dt><code>[template-notes]</code></dt><dd>The notes</dd>';
		echo '<dt><code>[template-user-full-name]</code></dt><dd>The logged in user\'s name</dd>';
		echo '</dl>';
	}

	/**
	 * Prints the box content.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	function goodwill_meta_box_subject_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'goodwill_email_template_meta_box', 'goodwill_email_template_subject_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_subject', true );

		$text_placeholder_classes = 'text-placeholder';
		if ( $value !== '' ) {
			$text_placeholder_classes .= ' screen-reader-text';
		}

		echo '<label class="' . $text_placeholder_classes . '" for="subject">Enter subject here</label>';
		echo '<input type="text" id="subject" name="subject" class="text-block text" value="' . esc_attr( $value ) . '" size="25" />';
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function goodwill_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['goodwill_email_template_subject_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['goodwill_email_template_subject_nonce'], 'goodwill_email_template_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'email-template' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, its safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['subject'] ) ) {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['subject'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_subject', $my_data );
	}


	function goodwill_fetch_contacts() {
		$ccs1 = goodwill_connect_ccs();
		$gwUser = ImageFirst_User::get_instance();
		$dbName = $gwUser->customer_database_name;

		$contacts = array();
		// Fetch contacts
		$contactRows = $ccs1->get_results(
			$ccs1->prepare(
				'SELECT p.personnum, p.name, p.title, p.email, pc.abctype as label
			  FROM ' . $dbName . '.person p
			  LEFT JOIN ' . $dbName . '.personincust pc
					 ON pc.personnum=p.personnum
			  WHERE UPPER(pc.custnum) = UPPER(%s)
			  ORDER BY ifnull(pc.abctype, "ZZZZZ") ASC',
				$gwUser->customer_number
			)
		);

		// Parse the database contacts removing invalid or empty contacts
		foreach ( $contactRows as $contactRow ) {
			if ( $contactRow->name !== null ) {
				$contacts[] = new Goodwill_Customer_Contact(
					$contactRow->name,
					$contactRow->title,
					$contactRow->email,
					$contactRow->label
				);
			}
		}

		return $contacts;
	}

	function goodwill_fetch_notes() {
		$gw_user = ImageFirst_User::get_instance();
		return $gw_user->notes;
	}

	/**
	 * Gets the anchor attachments from the position remarkables page and returns
	 * them as an array of url => post objects
	 *
	 * @since Goodwill 1.0
	 *
	 * @uses $wpdb
	 *
	 * @return array Array of url => post objects representing the attachments
	 */
	function goodwill_get_position_remarkable_attachments() {
		$attachments = array();

		// Fetch the page content and find all of the anchor tags
		$position_page = get_page_by_title( 'Position Remarkables and Promise' );
		preg_match_all(
			'/href="([^"]+)"/',
			$position_page->post_content,
			$matches,
			PREG_SET_ORDER
		);

		// Loop over the anchor tags finding the WordPress Post objects
		foreach ( $matches as $match ) {
			$post_id = url_to_postid( $match[1] );

			// If the post id is 0 then the URL is probably a direct link
			// instead of a permalink, so check the database also
			if ( $post_id === 0 ) {
				global $wpdb;
				$post_id = $wpdb->get_var(
					$wpdb->prepare(
						'
						SELECT ID
						FROM ' . $wpdb->posts . '
						WHERE guid = %s
					',
						$match[1]
					)
				);
			}

			$post = get_post( $post_id );
			$attachments[ $match[1] ] = $post;
		}

		return $attachments;
	}

	// Given a current page, get the next from the sidebar menu
	function goodwill_get_next_url( $default = null ) {
		$locations = get_nav_menu_locations();
		if ( ! $locations || ! isset( $locations[ SIDEBAR_MENU_NAME ] ) ) {
			return $default;
		}

		$menu = wp_get_nav_menu_object( $locations[ SIDEBAR_MENU_NAME ] );
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

		// Find the current menu item and fetch the next one
		$current_key = null;
		foreach ( (array) $menu_items as $key => $menu_item ) {
			if ( $menu_item->url === wp_get_referer() ) {
				$current_key = $key;
				break;
			}
		}

		// If the current key exists and the next key exists
		if ( null !== $current_key && array_key_exists( $current_key + 1, $menu_items ) ) {
			return $menu_items[ $current_key + 1 ]->url;
		} else {
			return $default;
		}
	}

	function goodwill_is_authenticated() {
		$is_authenticated = false;

		$goodwill_user = ImageFirst_User::get_instance();

		if ( isset( $_SESSION['goodwill-user'] ) && isset( $goodwill_user->username ) ) {
			$is_authenticated = true;
		}

		return $is_authenticated;
	}

	/**
	 * @see http://codex.wordpress.org/Template_Tags/get_posts
	 */
	function goodwill_get_email_templates() {
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'post_type' => 'email-template',
			'post_status' => 'private',
		);
		$raw_email_templates = get_posts( $args );

		$email_templates = array();
		foreach ( $raw_email_templates as $raw_email_template ) {
			$template = new IF_Email_Functions();
			$template->name = $raw_email_template->post_title;
			$template->subject = do_shortcode( get_post_meta( $raw_email_template->ID, '_subject', true ) );
			$template->body = wpautop( do_shortcode( $raw_email_template->post_content ) );
			$email_templates[] = $template;
		}

		return $email_templates;
	}

	function goodwill_headers_no_cache() {
		// if ( GOODWILL_BROWSER_CACHE === true ) {
		// return;
		// }
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

}
