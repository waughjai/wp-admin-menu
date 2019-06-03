<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

use WaughJ\HTMLAttributeList\HTMLAttributeList;

class WPAdminMenuList
{
    public function __construct( array $list, HTMLAttributeList $attributes )
    {
        $this->list = $list;
        $this->attributes = $attributes;
    }

    public function print() : void
    {
		?><ul<?= $this->attributes->getAttributesText(); ?>><?php
			foreach ( $this->list as $item )
			{
				$item->print();
			}
		?></ul><?php
    }

    private $list;
    private $attributes;
}
