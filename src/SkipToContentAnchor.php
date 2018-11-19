<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu
{
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
