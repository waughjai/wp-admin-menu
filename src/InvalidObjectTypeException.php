<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

class InvalidObjectTypeException extends WPAdminMenuException
{
    public function __construct( string $type )
    {
        parent::__construct( "Invalid object type “{$type}” WPAdminMenuItem." );
    }
}