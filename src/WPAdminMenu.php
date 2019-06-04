<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

use WaughJ\WPPostListConverter\WPPostListConverter;
use WaughJ\HTMLLink\HTMLLink;
use WaughJ\TestHashItem\TestHashItem;
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
			$this->skip_to_content_anchor = new SkipToContentAnchor( $attributes[ 'skip-to-content' ] ?? null );
			$this->post_converter = new WPPostListConverter([ 'type' => 'menu' ]);
			$this->current_page = null;
			$function = function() use ( $slug, $title )
			{
				register_nav_menu( $this->slug, __( $this->title, $this->getThemeName() ) );
			};
			add_action( 'after_setup_theme', $function );
		}

		public function __toString()
		{
			return $this->getMenuContent();
		}

		public function printMenu( $custom_attributes = null ) : void
		{
			$attributes_list = $this->addCustomAttributesToDefaultAttributes( $custom_attributes );
			$menu_data = $this->GetMenu();
			$this->printMenuNav( $menu_data, $attributes_list );
		}

		public function getMenuContent( $custom_attributes = null ) : string
		{
			ob_start();
			$this->printMenu( $custom_attributes );
			return ob_get_clean();
		}

		public function setCurrentPage( int $post_id ) : void
		{
			$this->current_page = $post_id;
		}

		public function getMenu() : array
		{
			return $this->post_converter->getConvertedList( $this->getWordPressMenuData() );
		}




	//
	//  PRIVATE
	//
	/////////////////////////////////////////////////////////

		private function printMenuNav( array $menu_data, array $attributes_list ) : void
		{
			?><nav<?= $this->getElementClassValue( 'nav', $attributes_list ); ?><?= $this->getElementIDValue( 'nav', $attributes_list ); ?>><?php
				$this->printMenuList( $menu_data, $attributes_list );
			?></nav><?php
		}

		private function printMenuList( array $menu_list, array $attributes_list, string $list_key = 'ul', string $item_key = 'li', string $link_key = 'a', bool $is_topmost = true ) : void
		{
			?><ul<?= $this->getElementClassValue( $list_key, $attributes_list ); ?><?= $this->getElementIDValue( $list_key, $attributes_list ); ?>><?php
				if ( $is_topmost )
				{
					$this->printSkipToContentItem( $item_key, $link_key, $attributes_list );
				}

				foreach ( $menu_list as $menu_item )
				{
					$this->printMenuItem( $menu_item, $item_key, $link_key, $attributes_list );
				}
			?></ul><?php
		}

		private function printMenuItem( array $menu_item, string $item_key, string $link_key, array $attributes_list ) : void
		{
			$classes = $this->getElementAttribute( $item_key, 'class', $attributes_list );
			if ( $menu_item[ 'id' ] === $this->current_page )
			{
				$classes = array_merge( $classes, $this->getElementAttribute( 'current-item', 'class', $attributes_list ) );
			}
			$class_string = implode( ' ', $classes );
			?><li<?= ( $class_string === '' ) ? '' : " class=\"{$class_string}\""; ?>><?php
				$this->printMenuLink( $menu_item, $link_key, $attributes_list );
				if ( $this->testMenuItemHasChildren( $menu_item ) )
				{
					$this->printMenuList( $menu_item[ 'subnav' ], $attributes_list, 'sublist', 'subitem', 'sublink', false );
				}
			?></li><?php
		}

		private function printMenuLink( array $menu_item, string $link_key, array $attributes_list ) : void
		{
			$classes = $this->getElementAttribute( $link_key, 'class', $attributes_list );

			// Add "link-parent" class if parent o' submenu.
			if ( $this->testMenuItemHasChildren( $menu_item ) )
			{
				$classes = array_merge( $classes, $this->getElementAttribute( 'link-parent', 'class', $attributes_list ) );
			}

			// Add "current-link" class if link goes to current page.
			if ( $menu_item[ 'id' ] === $this->current_page )
			{
				$classes = array_merge( $classes, $this->getElementAttribute( 'current-link', 'class', $attributes_list ) );
			}

			$class_string = implode( ' ', $classes );
			// Only add class attribute if there are any classes.
			$other_attributes = ( $class_string === '' ) ? [] : [ 'class' => $class_string ];

			$dont_show_current_page_link_condition = TestHashItem::isTrue( $attributes_list, 'dont-show-current-link' ) && $this->current_page === $menu_item[ 'id' ];
			echo ( $dont_show_current_page_link_condition )
				? $menu_item[ 'title' ] // Title without link
				: new HTMLLink( $menu_item[ 'url' ], $menu_item[ 'title' ], $other_attributes );
		}

		// Skip to Content Item holds a link that goes to the main content anchor,
		// 'specially useful for people relying on screen readers.
		private function printSkipToContentItem( string $item_key, string $link_key, array $attributes_list ) : void
		{
			$anchor = $this->skip_to_content_anchor->GetAnchor();
			if ( $anchor !== null )
			{
				$item_classes_list = array_merge( [ 'skip-content-item' ], $this->getElementAttribute( $item_key, 'class', $attributes_list ) );
				$item_classes_string = implode( ' ', $item_classes_list );
				?><li class="<?= $item_classes_string; ?>"><?php
					echo new HTMLLink( '#main', 'Skip to Content', [ 'class' => $this->getElementAttributeString( $link_key, 'class', $attributes_list ) . ' skip-content-link' ]);
				?></li><?php
			}
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

		private function getElementClassValue( string $element, array $attributes_list ) : string
		{
			return $this->getElementAttributeValue( $element, 'class', $attributes_list );
		}

		private function getElementIDValue( string $element, array $attributes_list ) : string
		{
			return $this->getElementAttributeValue( $element, 'id', $attributes_list );
		}

		private function getElementAttributeValue( string $element, string $attribute, array $attributes_list ) : string
		{
			$attribute_value = $this->getElementAttributeString( $element, $attribute, $attributes_list );
			$text = '';
			if ( $attribute_value !== '' )
			{
				$text = ' ' . $attribute . '="' . $attribute_value . '"';
			}
			return $text;
		}

		private function getElementAttributeString( string $element, string $attribute, array $attributes_list ) : string
		{
			return implode( ' ', $this->getElementAttribute( $element, $attribute, $attributes_list ) );
		}

		private function getElementAttribute( string $element, string $attribute, array $attributes_list ) : array
		{
			$element_attributes = TestHashItem::getArray( $attributes_list, $element, [] );
			$attribute_values = $element_attributes[ $attribute ] ?? null;
			if ( $attribute_values !== null )
			{
				if ( is_string( $attribute_values ) )
				{
					return [ $attribute_values ];
				}
				else if ( is_array( $attribute_values ) )
				{
					// Ensure array is indexed, not associative / hash map.
					$flat_array = [];
					foreach ( $attribute_values as $item )
					{
						array_push( $flat_array, $item );
					}
					return $flat_array;
				}
			}

			return [];
		}

		private function testMenuItemHasChildren( array $menu_item ) : bool
		{
			return isset( $menu_item[ 'subnav' ] );
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

		private function addCustomAttributesToDefaultAttributes( $custom_attributes ) : array
		{
			$attributes_list = $this->attributes;
			if ( is_array( $custom_attributes ) )
			{
				foreach( $custom_attributes as $key => $value )
				{
					if ( is_array( $value ) )
					{
						if ( !array_key_exists( $key, $attributes_list ) )
						{
							$attributes_list[ $key ] = $value;
						}
						else
						{
							foreach ( $value as $subkey => $subvalue )
							{
								$attributes_list[ $key ][ $subkey ] = $subvalue;
							}
						}
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
