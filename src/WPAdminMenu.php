<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu
{
	use WaughJ\WPPostListConverter\WPPostListConverter;
	use WaughJ\HTMLLink\HTMLLink;
	use function WaughJ\TestHashItem\TestHashItemArray;
	use function WaughJ\TestHashItem\TestHashItemExists;
	use function WaughJ\TestHashItem\TestHashItemString;
	use function WaughJ\TestHashItem\TestHashItemIsTrue;
	use WaughJ\HTMLAttributeList\HTMLAttributeList;

	class WPAdminMenu
	{
		//
		//  PUBLIC
		//
		/////////////////////////////////////////////////////////

			public function __construct( string $slug, string $title, array $attributes = [] )
			{
				$this->slug = $slug;
				$this->title = $title;
				$this->attributes = $attributes;
				$this->skip_to_content_anchor = ( array_key_exists( 'skip-to-content', $attributes ) )
					? ( is_string( $attributes[ 'skip-to-content' ] ) ) ? $attributes[ 'skip-to-content' ] : self::DEFAULT_ANCHOR
					: false;
				$this->post_converter = new WPPostListConverter([ 'type' => 'menu' ]);
				$this->current_page = null;
				$this->data = $this->post_converter->getConvertedList( $this->getWordPressMenuData() );
				add_action
				(
					'after_setup_theme',
					function() use ( $slug, $title )
					{
						register_nav_menu( $this->slug, __( $this->title, $this->getThemeName() ) );
					}
				);
			}

			public function __toString()
			{
				return $this->getMenuContent();
			}

			public function printMenu( array $custom_attributes = [] ) : void
			{
				$attributes_list = $this->addCustomAttributesToDefaultAttributes( $custom_attributes );
				$menu_data = $this->getMenu();
				$this->printMenuNav( $menu_data, $attributes_list );
			}

			public function getMenuContent( array $custom_attributes = [] ) : string
			{
				ob_start();
				$this->printMenu( $custom_attributes );
				return ob_get_clean();
			}

			public function setCurrentPage( int $post_id ) : void
			{
				$this->current_page = $post_id;
			}

			public function getMenu( array $attributes_list = [] ) : WPAdminMenuList
			{
				$attributes_list = $this->addCustomAttributesToDefaultAttributes( $attributes_list );
				$list_attributes = $this->getElementAttributes( 'ul', $attributes_list );
				$item_attributes = $this->getElementAttributes( 'li', $attributes_list );
				$link_attributes = $this->getElementAttributes( 'a', $attributes_list );
				$dont_show_current_link = TestHashItemIsTrue( $attributes_list, 'dont-show-current-link' );
				$skip_anchor = ( $this->skip_to_content_anchor !== false );
				return $this->generateMenuList( $this->data, $attributes_list, $list_attributes, $item_attributes, $link_attributes, $dont_show_current_link, $skip_anchor );
			}




		//
		//  PRIVATE
		//
		/////////////////////////////////////////////////////////

			private function printMenuNav( WPAdminMenuList $list, array $attributes_list ) : void
			{
				?><nav<?= $this->getElementAttributes( 'nav', $attributes_list )->getAttributesText(); ?>><?php
					$list->print();
				?></nav><?php
			}

			private function generateMenuList( array $data, array $all_attributes, HTMLAttributeList $list_attributes, HTMLAttributeList $item_attributes, HTMLAttributeList $link_attributes, bool $dont_show_current_link, bool $skip_link ) : WPAdminMenuList
			{
				$list = [];
				if ( $skip_link )
				{
					$list[] = new WPAdminMenuItem( [ 'url' => "#{$this->skip_to_content_anchor}", 'title' => "Skip to Content" ], $item_attributes->appendToAttribute( 'class', 'skip-content-item' ), $link_attributes->appendToAttribute( 'class', 'skip-content-link' ), null, true );
				}
				foreach ( $data as $data_item )
				{
					$current_page = $this->current_page === $data_item[ 'id' ];
					$show_link = !$dont_show_current_link || !$current_page;
					$children = ( TestHashItemArray( $data_item, 'subnav', false ) )
						? $this->generateMenuList
						(
							$data_item[ 'subnav' ],
							$all_attributes,
							$this->getElementAttributes( 'sublist', $all_attributes ),
							$this->getElementAttributes( 'subitem', $all_attributes ),
							$this->getElementAttributes( 'sublink', $all_attributes ),
							$dont_show_current_link,
							false
						)
						: null;

					$current_item_attributes = ( $current_page ) ? new HTMLAttributeList( $this->addAttributesToOtherAttributes( TestHashItemArray( $all_attributes, 'current-item', [] ), $item_attributes->getAttributeValuesMap() ) ) : $item_attributes;
					$current_link_attributes = ( $current_page ) ? $this->addAttributesToOtherAttributes( TestHashItemArray( $all_attributes, 'current-link', [] ), $link_attributes->getAttributeValuesMap() ) : $link_attributes->getAttributeValuesMap();
					$current_link_attributes = ( $children !== null ) ? new HTMLAttributeList( $this->addAttributesToOtherAttributes( TestHashItemArray( $all_attributes, 'link-parent', [] ), $current_link_attributes, true ) ) : new HTMLAttributeList( $current_link_attributes );

					$list[] = new WPAdminMenuItem( $data_item, $current_item_attributes, $current_link_attributes, $children, $show_link );
				}
				return new WPAdminMenuList( $list, $list_attributes );
			}

			private function getElementAttributes( string $element, array $attributes ) : HTMLAttributeList
			{
				$list = TestHashItemArray( $attributes, $element, [] );
				return new HTMLAttributeList( $list );
			}

			private function getWordPressMenuData() : array
			{
			    // Get all locations
			    $locations = get_nav_menu_locations();
			    // Get object id by slug
			    $object = wp_get_nav_menu_object( $locations[ $this->slug ] );
			    // Get menu items by menu name
			    $menu_items = wp_get_nav_menu_items( $object->name, [] );
			    // Return menu post objects
			    return $menu_items;
			}

			private function getThemeName() : string
			{
				$theme = wp_get_theme();
				if ( is_a( $theme, '\WP_Theme' ) && $theme->exists() )
				{
					$name = $theme->get( 'TextDomain' );
					if ( is_string( $name ) )
					{
						return $name;
					}
				}
				return 'waugh';
			}

			private function addCustomAttributesToDefaultAttributes( array $custom_attributes = [] ) : array
			{
				return $this->addAttributesToOtherAttributes( $custom_attributes, $this->attributes );
			}

			private function addAttributesToOtherAttributes( array $custom_attributes = [], array $default_attributes = [], bool $test = false ) : array
			{
				if ( empty( $custom_attributes ) )
				{
					return $default_attributes;
				}

				$attributes_list = $default_attributes;
				foreach( $custom_attributes as $key => $value )
				{
					if ( is_array( $value ) )
					{
						if ( TestHashItemArray( $attributes_list, $key, false ) )
						{
							$attributes_list[ $key ] = $this->addAttributesToOtherAttributes( $value, $attributes_list[ $key ] );
						}
						else
						{
							$attributes_list[ $key ] = $value;
						}
					}
					else
					{
						if ( $key === 'class' && array_key_exists( $key, $attributes_list ) )
						{
							$attributes_list[ $key ] .= " {$value}";
						}
						else
						{
							$attributes_list[ $key ] = $value;
						}
					}
				}
				return $attributes_list;
			}

			private $slug;
			private $title;
			private $attributes;
			private $skip_to_content_anchor;
			private $post_converter;
			private $current_page;
	}
}
