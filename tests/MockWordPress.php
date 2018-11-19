<?php

	global $post_list;
	$post_list =
	[
		new \WP_Post( 1, "Some Post", 'https://www.jaimeson-waugh.com' ),
		new \WP_Post( 2, "Some Post Child", 'https://www.jaimeson-waugh.com', 1, 1 )
	];

	class WP_Post
	{
		public function __construct( int $id, string $title, string $url, int $menu_item_parent = 0, int $post_parent = 0 )
		{
			$this->ID = $id;
			$this->title = $title;
			$this->post_title = $title;
			$this->url = $url;
			$this->menu_item_parent = $menu_item_parent;
			$this->post_parent = $post_parent;
		}

		public $ID;
		public $title;
		public $post_title;
		public $url;
		public $menu_item_parent;
		public $post_parent;
	}

	function add_action( $name, $function )
	{
		// This doesn't need to do anything now.
	}

	function get_nav_menu_locations()
	{
		return [ 'header-nav' => null ];
	}

	function wp_get_nav_menu_object( $thing )
	{
		return ( object )( [ 'name' => 'Header Nav' ] );
	}

	function wp_get_nav_menu_items( $thing1, $thing2 )
	{
		global $post_list;
		return $post_list;
	}
