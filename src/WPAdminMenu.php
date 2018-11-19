<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu
{
	use WaughJ\WPPostListConverter\WPPostListConverter;
	use WaughJ\HTMLLink\HTMLLink;
	use function WaughJ\TestHashItem\TestHashItemExists;

	class WPAdminMenu
	{
		//
		//  PUBLIC
		//
		/////////////////////////////////////////////////////////

			public function __construct( string $slug, string $title, array $attributes )
			{
				$this->slug = $slug;
				$this->title = $title;
				$this->attributes = $attributes;
				$this->skip_to_content_anchor = new SkipToContentAnchor( TestHashItemExists( $attributes, 'skip-to-content', null ) );
				$this->post_converter = new WPPostListConverter([ 'type' => 'menu' ]);
				$function = function() use ( $slug, $title )
				{
					register_nav_menu( $this->slug, __( $this->title, 'northwest' ) );
				};
				add_action( 'after_setup_theme', $function );
			}

			public function printMenu() : void
			{
				$menu_data = $this->GetMenu();
				$this->printMenuNav( $menu_data );
			}

			public function getMenuContent() : string
			{
				ob_start();
				$this->printMenu();
				return ob_get_clean();
			}

			public function getMenu() : array
			{
				return $this->post_converter->getConvertedList( $this->getWordPressMenuData() );
			}




		//
		//  PRIVATE
		//
		/////////////////////////////////////////////////////////

			private function printMenuNav( array $menu_data ) : void
			{
				?><nav<?= $this->getElementClassValue( 'nav' ); ?><?= $this->getElementIDValue( 'nav' ); ?>><?php
					$this->printMenuList( $menu_data );
				?></nav><?php
			}

			private function printMenuList( array $menu_list, string $list_key = 'ul', string $item_key = 'li', string $link_key = 'a', bool $is_topmost = true ) : void
			{
				?><ul<?= $this->getElementClassValue( $list_key ); ?><?= $this->getElementIDValue( $list_key ); ?>><?php
					if ( $is_topmost )
					{
						$this->printSkipToContentItem( $item_key, $link_key );
					}

					foreach ( $menu_list as $menu_item )
					{
						$this->printMenuItem( $menu_item, $item_key, $link_key );
					}
				?></ul><?php
			}

			private function printMenuItem( array $menu_item, string $item_key, string $link_key ) : void
			{
				?><li<?= $this->getElementClassValue( $item_key ); ?>><?php
					$this->printMenuLink( $menu_item, $link_key );
					if ( $this->testMenuItemHasChildren( $menu_item ) )
					{
						$this->printMenuList( $menu_item[ 'subnav' ], 'sublist', 'subitem', 'sublink', false );
					}
				?></li><?php
			}

			private function printMenuLink( array $menu_item, string $link_key ) : void
			{
				$classes = $this->getElementAttribute( $link_key, 'class' );
				if ( $this->testMenuItemHasChildren( $menu_item ) )
				{
					$classes = array_merge( $classes, $this->getElementAttribute( 'link-parent', 'class' ) );
				}
				$class_string = implode( ' ', $classes );
				echo new HTMLLink( $menu_item[ 'url' ], $menu_item[ 'title' ], [ 'class' => $class_string ]);
			}

			// Skip to Content Item holds a link that goes to the main content anchor,
			// 'specially useful for people relying on screen readers.
			private function printSkipToContentItem( string $item_key, string $link_key ) : void
			{
				$anchor = $this->skip_to_content_anchor->getAnchor();
				if ( $anchor !== null )
				{
					$item_classes_list = array_merge( [ 'skip-content-item' ], $this->getElementAttribute( $item_key, 'class' ) );
					$item_classes_string = implode( ' ', $item_classes_list );
					?><li class="<?= $item_classes_string; ?>"><?php
						echo new HTMLLink( '#main', 'Skip to Content', [ 'class' => $this->getElementAttributeString( $link_key, 'class' ) . ' skip-content-link' ]);
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

			private function getElementClassValue( string $element ) : string
			{
				return $this->getElementAttributeValue( $element, 'class' );
			}

			private function getElementIDValue( string $element ) : string
			{
				return $this->getElementAttributeValue( $element, 'id' );
			}

			private function getElementAttributeValue( string $element, string $attribute ) : string
			{
				$attribute_value = $this->getElementAttributeString( $element, $attribute );
				$text = '';
				if ( $attribute_value !== '' )
				{
					$text = ' ' . $attribute . '="' . $attribute_value . '"';
				}
				return $text;
			}

			private function getElementAttributeString( string $element, string $attribute ) : string
			{
				return implode( ' ', $this->getElementAttribute( $element, $attribute ) );
			}

			private function getElementAttribute( string $element, string $attribute ) : array
			{
				$object = TestHashItemExists( $this->attributes, $element, [] );
				if ( !empty( $object ) )
				{
					if ( is_string( $object ) )
					{
						return [ $object ];
					}
					else if ( is_array( $object ) )
					{
						// Ensure array is indexed, not associative / hash map.
						$flat_array = [];
						foreach ( $object as $item )
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

			private $slug;
			private $title;
			private $attributes;
	}

	class SkipToContentAnchor
	{
		public function __construct( $anchor )
		{
			if ( is_string( $anchor ) )
			{
				$this->anchor = $anchor;
			}
			else if ( is_numeric( $anchor ) )
			{
				$this->anchor = ( string )( $anchor );
			}
			else if ( is_bool( $anchor ) )
			{
				$this->anchor = self::DEFAULT_ANCHOR;
			}
			else
			{
				$this->anchor = null;
			}
		}

		public function getAnchor()
		{
			return $this->anchor;
		}

		private $anchor;
		private $post_converter;
		private const DEFAULT_ANCHOR = 'main';
	}
}
