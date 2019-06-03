<?php

declare( strict_types = 1 );
namespace WaughJ\WPAdminMenu;

use WaughJ\HTMLAttributeList\HTMLAttributeList;
use WaughJ\HTMLLink\HTMLLink;

class WPAdminMenuItem
{
    public function __construct( array $data, HTMLAttributeList $item_attributes, HTMLAttributeList $link_attributes, ?WPAdminMenuList $children = null, bool $show_link = true )
    {
        $this->title = $data[ 'title' ];
        $this->url = $data[ 'url' ];
        $this->item_attributes = $item_attributes;
        $this->link_attributes = $link_attributes;
        $this->children = $children;
        $this->show_link = $show_link;
    }

	public function print() : void
	{
		?><li<?= $this->item_attributes->getAttributesText(); ?>><?php
			$this->printLink();
			if ( $this->children )
			{
				$this->children->print();
			}
		?></li><?php
	}

	private function printLink() : void
	{
		echo ( $this->show_link )
			? new HTMLLink( $this->url, $this->title, $this->link_attributes->getAttributeValuesMap() )
			: $this->title; // Title without link
	}

    private $title;
    private $url;
    private $item_attributes;
    private $link_attributes;
    private $children;
    private $show_link;
}
