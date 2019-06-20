<?php

	global $post_list;
	$post_list =
	[
		new \WP_Post( 1, "Google", 'https://www.google.com', 'post' ),
		new \WP_Post( 2, "Some Post", 'https://www.jaimeson-waugh.com', 'post' ),
		new \WP_Post( 3, "Amazon", 'https://www.amazon.com', 'post' ),
		new \WP_Post( 4, "Some Post Child", 'https://www.jaimeson-waugh.com', 'post', 1, 1 )
	];

	global $menu;
	$menu =
	[
		new \WP_Post( 1, "Some Post", 'https://www.jaimeson-waugh.com', 'nav_menu_item', 0, 0, 2 ),
		new \WP_Post( 2, "Some Post Child", 'https://www.jaimeson-waugh.com', 'nav_menu_item', 1, 1, 4 )
	];

	class WP_Post
	{
		public function __construct( int $id, string $title, string $url, string $type, int $menu_item_parent = 0, int $post_parent = 0, int $object_id = -1 )
		{
			$this->ID = $id;
			$this->title = $title;
			$this->post_title = $title;
			$this->url = $url;
			$this->post_type = $type;
			$this->menu_item_parent = $menu_item_parent;
			$this->post_parent = $post_parent;
			$this->description = '';
			$this->attr_title = '';
			$this->object_id = ( $object_id === -1 ) ? $id : $object_id;
			$this->xfn = null;
			$this->object = 'post';
			$this->classes = [];
			$this->target = '';
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
		global $menu;
		$posts = [];
		foreach ( $menu as $menu_item )
		{
			$posts[] = $post_list[ $menu_item->object_id - 1 ];
		}
		return $posts;
	}
