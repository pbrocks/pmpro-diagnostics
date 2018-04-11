<?php
namespace PMPro_Diagnostics\inc;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Post_Types {
	/*
			[add_new_item] => Add New Page
			[edit_item] => Edit Page
			[new_item] => New Page
			[view_item] => View Page
			[view_items] => View Pages
			[search_items] => Search Page
	*/
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_customer' ) );
		add_action( 'init', array( __CLASS__, 'register_wins' ) );
		// add_action( 'init', array( __CLASS__, 'categories_to_pages' ) );
	}

	public static function register_customer() {
		$labels                   = Post_Types::get_label_defaults();
		$labels['name']           = _x( 'Customers', 'Post Type General Name', 'pbrx-site-customizer' );
		$labels['singular_name']  = _x( 'Customer', 'Post Type Singular Name', 'pbrx-site-customizer' );
		$labels['all_items']      = __( 'All Customers', 'pbrx-site-customizer' );
		$labels['menu_name']      = __( 'Customers', 'pbrx-site-customizer' );
		$labels['name_admin_bar'] = __( 'Customers', 'pbrx-site-customizer' );
		$labels['add_new_item']   = __( 'Add New Customer', 'pbrx-site-customizer' );

		$args                = Post_Types::get_args_defaults();
		$args['label']       = __( 'Customers', 'pbrx-site-customizer' );
		$args['description'] = __( 'Customers Types', 'pbrx-site-customizer' );
		$args['labels']      = $labels;
		$args['menu_icon']   = 'dashicons-id';
		$args['taxonomies']  = array();
		$args['rewrite']     = array(
			'with_front' => false,
			'slug'       => 'customer',
		);
		$args['rest_base']   = __( 'customer', 'pbrx-site-customizer' );

		register_post_type( 'pbrx_customer', $args );
	}

	public static function register_wins() {
		$labels                   = Post_Types::get_label_defaults();
		$labels['name']           = _x( 'Customer Win', 'Post Type General Name', 'pbrx-site-customizer' );
		$labels['singular_name']  = _x( 'Customer Win', 'Post Type Singular Name', 'pbrx-site-customizer' );
		$labels['all_items']      = __( 'All Customer Wins', 'pbrx-site-customizer' );
		$labels['menu_name']      = __( 'Customer Wins', 'pbrx-site-customizer' );
		$labels['name_admin_bar'] = __( 'Customer Win', 'pbrx-site-customizer' );

		$args                = Post_Types::get_args_defaults();
		$args['label']       = __( 'Customer Win', 'pbrx-site-customizer' );
		$args['description'] = __( 'Customer Win post-type', 'pbrx-site-customizer' );
		$args['labels']      = $labels;
		$args['menu_icon']   = 'dashicons-filter';
		$args['rewrite']     = array(
			'with_front' => false,
			'slug'       => 'wins',
		);
		$args['rest_base']   = __( 'wins', 'pbrx-site-customizer' );

		register_post_type( 'pbrx_wins', $args );
	}

	private static function get_label_defaults() {
		return array(
			'name'                  => _x( 'Pages', 'Post Type General Name', 'pbrx-site-customizer' ),
			'singular_name'         => _x( 'Page', 'Post Type Singular Name', 'pbrx-site-customizer' ),
			'menu_name'             => __( 'Pages', 'pbrx-site-customizer' ),
			'name_admin_bar'        => __( 'Page', 'pbrx-site-customizer' ),
			'archives'              => __( 'Page Archives', 'pbrx-site-customizer' ),
			'parent_item_colon'     => __( 'Parent Page:', 'pbrx-site-customizer' ),
			'all_items'             => __( 'All Pages', 'pbrx-site-customizer' ),
			'add_new_item'          => __( 'Add New Page', 'pbrx-site-customizer' ),
			'add_new'               => __( 'Add New', 'pbrx-site-customizer' ),
			'new_item'              => __( 'New Page', 'pbrx-site-customizer' ),
			'edit_item'             => __( 'Edit Page', 'pbrx-site-customizer' ),
			'update_item'           => __( 'Update Page', 'pbrx-site-customizer' ),
			'view_item'             => __( 'View Page', 'pbrx-site-customizer' ),
			'search_items'          => __( 'Search Page', 'pbrx-site-customizer' ),
			'not_found'             => __( 'Not found', 'pbrx-site-customizer' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'pbrx-site-customizer' ),
			'featured_image'        => __( 'Featured Image', 'pbrx-site-customizer' ),
			'set_featured_image'    => __( 'Set featured image', 'pbrx-site-customizer' ),
			'remove_featured_image' => __( 'Remove featured image', 'pbrx-site-customizer' ),
			'use_featured_image'    => __( 'Use as featured image', 'pbrx-site-customizer' ),
			'insert_into_item'      => __( 'Insert into page', 'pbrx-site-customizer' ),
			'uploaded_to_this_item' => __( 'Uploaded to this page', 'pbrx-site-customizer' ),
			'items_list'            => __( 'Pages list', 'pbrx-site-customizer' ),
			'items_list_navigation' => __( 'Pages list navigation', 'pbrx-site-customizer' ),
			'filter_items_list'     => __( 'Filter pages list', 'pbrx-site-customizer' ),
		);
	}

	private static function get_args_defaults() {
		return array(
			'label'                 => __( 'Page', 'pbrx-site-customizer' ),
			'description'           => __( 'Page Description', 'pbrx-site-customizer' ),
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'            => array( 'category' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-admin-page',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			// 'has_archive'           => true,
			'has_archive'           => false,
			'rewrite'               => array(
				'with_front' => false,
				'slug'       => 'page',
			),
			'exclude_from_search'   => false,
			'query_var'             => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
			'rest_base'             => 'pages',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
	}


	private static function unset_element_from_array( $element, $array ) {
		$comments_key = array_search( $element, $array );
		if ( false !== $comments_key ) {
			unset( $array[ $comments_key ] );
		}
		return $array;
	}
	public static function categories_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
		add_post_type_support( 'page', 'excerpt' );
	}
}
