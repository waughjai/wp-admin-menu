<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

class InvalidTermException extends WPAdminMenuException
{
    public function __construct( int $id )
    {
        parent::__construct( "Invalid term with ID {$id} used in WPAdminMenu." );
    }
}