<?php

namespace ImageFirst_Customizer\inc;


defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );


class Post_Types {

	public function __construct() {
		add_action( 'init', array( $this, 'register_emails' ) );
	}

	public function register_customer() {
		$labels = $this->get_label_defaults();
		$labels['name']                  = _x( 'Customers', 'Post Type General Name', 'imagefirst-site-customizer' );
		$labels['singular_name']         = _x( 'Customer', 'Post Type Singular Name', 'imagefirst-site-customizer' );
		$labels['all_items']             = __( 'All Customers', 'imagefirst-site-customizer' );
		$labels['menu_name']             = __( 'Customers', 'imagefirst-site-customizer' );
		$labels['name_admin_bar']        = __( 'Customers', 'imagefirst-site-customizer' );
		$labels['add_new_item']        = __( 'Add New Customer', 'imagefirst-site-customizer' );

		$args = $this->get_args_defaults();
		$args['label']               = __( 'Customers', 'imagefirst-site-customizer' );
		$args['description']         = __( 'Customers Types', 'imagefirst-site-customizer' );
		$args['labels']              = $labels;
		$args['menu_icon']           = 'dashicons-id';
		$args['taxonomies']           = array();
		$args['rewrite']             = array(
			'with_front' => false,
			'slug' => 'customer',
		);
		$args['rest_base']           = __( 'customer', 'imagefirst-site-customizer' );

		register_post_type( 'pbrx_customer', $args );
	}

	public function register_emails() {
		$labels = $this->get_label_defaults();
		$labels['name']                  = _x( 'Email Templates', 'Post Type General Name', 'imagefirst-site-customizer' );
		$labels['singular_name']         = _x( 'Email Template', 'Post Type Singular Name', 'imagefirst-site-customizer' );
		$labels['all_items']             = __( 'All Email Templates', 'imagefirst-site-customizer' );
		$labels['menu_name']             = __( 'Email Templates', 'imagefirst-site-customizer' );
		$labels['name_admin_bar']        = __( 'Email Template', 'imagefirst-site-customizer' );

		$args = $this->get_args_defaults();
		$args['label']               = __( 'Email Template', 'imagefirst-site-customizer' );
		$args['description']         = __( 'Email Template post-type', 'imagefirst-site-customizer' );
		$args['labels']              = $labels;
		$args['menu_icon']           = 'dashicons-filter';
		$args['rewrite']             = array(
			'with_front' => false,
			'slug' => 'emails',
		);
		$args['rest_base']           = __( 'emails', 'imagefirst-site-customizer' );

		register_post_type( 'imagefirst_emails', $args );
	}

	private function get_label_defaults() {
		return array(
			'name'                  => _x( 'Pages', 'Post Type General Name', 'imagefirst-site-customizer' ),
			'singular_name'         => _x( 'Page', 'Post Type Singular Name', 'imagefirst-site-customizer' ),
			'menu_name'             => __( 'Pages', 'imagefirst-site-customizer' ),
			'name_admin_bar'        => __( 'Page', 'imagefirst-site-customizer' ),
			'archives'              => __( 'Page Archives', 'imagefirst-site-customizer' ),
			'parent_item_colon'     => __( 'Parent Page:', 'imagefirst-site-customizer' ),
			'all_items'             => __( 'All Pages', 'imagefirst-site-customizer' ),
			'add_new_item'          => __( 'Add New Page', 'imagefirst-site-customizer' ),
			'add_new'               => __( 'Add New', 'imagefirst-site-customizer' ),
			'new_item'              => __( 'New Page', 'imagefirst-site-customizer' ),
			'edit_item'             => __( 'Edit Page', 'imagefirst-site-customizer' ),
			'update_item'           => __( 'Update Page', 'imagefirst-site-customizer' ),
			'view_item'             => __( 'View Page', 'imagefirst-site-customizer' ),
			'search_items'          => __( 'Search Page', 'imagefirst-site-customizer' ),
			'not_found'             => __( 'Not found', 'imagefirst-site-customizer' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'imagefirst-site-customizer' ),
			'featured_image'        => __( 'Featured Image', 'imagefirst-site-customizer' ),
			'set_featured_image'    => __( 'Set featured image', 'imagefirst-site-customizer' ),
			'remove_featured_image' => __( 'Remove featured image', 'imagefirst-site-customizer' ),
			'use_featured_image'    => __( 'Use as featured image', 'imagefirst-site-customizer' ),
			'insert_into_item'      => __( 'Insert into page', 'imagefirst-site-customizer' ),
			'uploaded_to_this_item' => __( 'Uploaded to this page', 'imagefirst-site-customizer' ),
			'items_list'            => __( 'Pages list', 'imagefirst-site-customizer' ),
			'items_list_navigation' => __( 'Pages list navigation', 'imagefirst-site-customizer' ),
			'filter_items_list'     => __( 'Filter pages list', 'imagefirst-site-customizer' ),
		);
	}

	private function get_args_defaults() {
		return array(
			'label'                 => __( 'Page', 'imagefirst-site-customizer' ),
			'description'           => __( 'Page Description', 'imagefirst-site-customizer' ),
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
				'slug' => 'page',
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


	private function unset_element_from_array( $element, $array ) {
		$comments_key = array_search( $element, $array );
		if ( false !== $comments_key ) {
			unset( $array[ $comments_key ] );
		}
		return $array;
	}
	public function categories_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
		add_post_type_support( 'page', 'excerpt' );
	}
}
