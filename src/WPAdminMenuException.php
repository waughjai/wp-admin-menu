<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

class WPAdminMenuException extends \Exception
{
    public function __construct( string $message )
    {
        parent::__construct( $message );
    }

    public function isInvalidTermException() : bool
    {
        return get_class( $this ) === InvalidTermException::class;
    }

    public function isInvalidObjectTypeException() : bool
    {
        return get_class( $this ) === InvalidObjectTypeException::class;
    }
}