<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

use WaughJ\FlatToHierarchySorter\HierarchicalNode;

class WPAdminMenuItem extends HierarchicalNode
{
    public function __construct( int $id )
    {
        $menu_item = get_post( $id );
        $this->title = ( string )( $menu_item->post_title );
        $this->url = '#';

        $object_id = intval( get_post_meta( $id, '_menu_item_object_id', true ) );
        $object_type = self::getObjectType( $id );
        switch ( $object_type )
        {
            case ( 'post' ):
            {
                $post = get_post( $object_id );
                $this->url = ( string )( get_permalink( $post ) );
                if ( $this->title === '' )
                {
                    $this->title = ( string )( get_the_title( $post ) );
                }
            }
            break;
            case ( 'term' ):
            {
                $term_type = get_post_meta( $id, '_menu_item_object', true );
                $term = get_term( $object_id, $term_type );
                $url = get_term_link( $term );
                if ( is_wp_error( $url ) )
                {
                    throw "Invalid term for WPAdminMenuItem with ID ${$id}";
                }
                $this->url = ( string )( $url );
                if ( $this->title === '' )
                {
                    $this->title = ( string )( $term->name );
                }
            }
            break;
            default:
            {
                throw "Invalid Object Type for WPAdminMenuItem: {$object_type}";
            }
            break;
        }

        $parent = intval( get_post_meta( $id, '_menu_item_menu_item_parent', true ) );
        parent::__construct( $id, $parent );
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    private static function getObjectType( int $id ) : ?string
    {
        $type = get_post_meta( $id, '_menu_item_type', true );
        return ( $type === 'post_type' ) ? 'post' : ( ( $type === 'taxonomy' ) ? 'term' : null );
    }

    private $url;
    private $title;
}