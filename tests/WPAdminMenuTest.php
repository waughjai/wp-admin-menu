<?php

use PHPUnit\Framework\TestCase;
use WaughJ\WPAdminMenu\WPAdminMenu;

require_once( 'MockWordPress.php' );

class WPAdminMenuTest extends TestCase
{
	public function testAdminContent()
	{
		$menu = new WPAdminMenu
		(
			'header-nav',
			'Header Nav',
			self::ATTRIBUTES
		);
		$menu->setCurrentPage( 1 );
		$this->assertEquals( '<nav class="' . self::ATTRIBUTES[ 'nav' ][ 'class' ] . '" id="' . self::ATTRIBUTES[ 'nav' ][ 'id' ] . '"><ul class="' . self::ATTRIBUTES[ 'ul' ][ 'class' ] . '" id="' . self::ATTRIBUTES[ 'ul' ][ 'id' ] . '"><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . ' skip-content-item"><a class="' . self::ATTRIBUTES[ 'a' ][ 'class' ] . ' skip-content-link" href="#' . self::ATTRIBUTES[ 'skip-to-content' ] . '">Skip to Content</a></li><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . ' ' . self::ATTRIBUTES[ 'current-item' ][ 'class' ] . '"><a class="' . self::ATTRIBUTES[ 'a' ][ 'class' ] . ' ' . self::ATTRIBUTES[ 'current-link' ][ 'class' ] . ' ' . self::ATTRIBUTES[ 'link-parent' ][ 'class' ] . '" href="https://www.jaimeson-waugh.com">Some Post</a><ul class="' . self::ATTRIBUTES[ 'sublist' ][ 'class' ] . '"><li class="' . self::ATTRIBUTES[ 'subitem' ][ 'class' ] . '"><a class="' . self::ATTRIBUTES[ 'sublink' ][ 'class' ] . '" href="https://www.jaimeson-waugh.com">Some Post Child</a></li></ul></li></ul></nav>', $menu->getMenuContent() );
	}

	public function testAdminContentWithoutLinkForCurrentPage()
	{
		$atts = self::ATTRIBUTES;
		$atts[ 'dont-show-current-link' ] = true;
		$menu = new WPAdminMenu
		(
			'header-nav',
			'Header Nav',
			$atts
		);
		$menu->setCurrentPage( 1 );
		$this->assertEquals( '<nav class="' . self::ATTRIBUTES[ 'nav' ][ 'class' ] . '" id="' . self::ATTRIBUTES[ 'nav' ][ 'id' ] . '"><ul class="' . self::ATTRIBUTES[ 'ul' ][ 'class' ] . '" id="' . self::ATTRIBUTES[ 'ul' ][ 'id' ] . '"><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . ' skip-content-item"><a class="' . self::ATTRIBUTES[ 'a' ][ 'class' ] . ' skip-content-link" href="#' . self::ATTRIBUTES[ 'skip-to-content' ] . '">Skip to Content</a></li><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . ' ' . self::ATTRIBUTES[ 'current-item' ][ 'class' ] . '">Some Post<ul class="' . self::ATTRIBUTES[ 'sublist' ][ 'class' ] . '"><li class="' . self::ATTRIBUTES[ 'subitem' ][ 'class' ] . '"><a class="' . self::ATTRIBUTES[ 'sublink' ][ 'class' ] . '" href="https://www.jaimeson-waugh.com">Some Post Child</a></li></ul></li></ul></nav>', $menu->getMenuContent() );
	}

	public function testMenuWithoutAttributes()
	{
		$menu = new WPAdminMenu
		(
			'header-nav',
			'Header Nav'
		);
		$this->assertEquals( '<nav><ul><li><a href="https://www.jaimeson-waugh.com">Some Post</a><ul><li><a href="https://www.jaimeson-waugh.com">Some Post Child</a></li></ul></li></ul></nav>', $menu->getMenuContent() );
	}

	public function testAlternateAttributes()
	{
		$attributes = self::ATTRIBUTES;
		$attributes[ 'dont-show-current-link' ] = true;
		$menu = new WPAdminMenu
		(
			'header-nav',
			'Header Nav',
			$attributes
		);
		$this->assertEquals
		(
			'<nav class="header-nav alt-nav-class" id="' . self::ATTRIBUTES[ 'nav' ][ 'id' ] . '"><ul class="' . self::ATTRIBUTES[ 'ul' ][ 'class' ] . '" id="' . self::ATTRIBUTES[ 'ul' ][ 'id' ] . '"><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . ' skip-content-item"><a class="' . self::ATTRIBUTES[ 'a' ][ 'class' ] . ' skip-content-link" href="#' . self::ATTRIBUTES[ 'skip-to-content' ] . '">Skip to Content</a></li><li class="' . self::ATTRIBUTES[ 'li' ][ 'class' ] . '"><a class="' . self::ATTRIBUTES[ 'a' ][ 'class' ] . ' ' . self::ATTRIBUTES[ 'link-parent' ][ 'class' ] . ' link-owner" href="https://www.jaimeson-waugh.com">Some Post</a><ul class="' . self::ATTRIBUTES[ 'sublist' ][ 'class' ] . '"><li class="' . self::ATTRIBUTES[ 'subitem' ][ 'class' ] . '"><a class="' . self::ATTRIBUTES[ 'sublink' ][ 'class' ] . '" href="https://www.jaimeson-waugh.com">Some Post Child</a></li></ul></li></ul></nav>',
			$menu->getMenuContent
			([
				'nav' =>
				[
					'class' => 'alt-nav-class'
				],
				'link-parent' =>
				[
					'class' => 'link-owner'
				]
			])
		);
	}

	const ATTRIBUTES =
	[
		'nav' =>
		[
			'class' => 'header-nav',
			'id' => 'header-nav-1'
		],
		'ul' =>
		[
			'class' => 'header-nav-list',
			'id' => 'header-nav-list-1'
		],
		'li' =>
		[
			'class' => 'header-nav-item'
		],
		'a' =>
		[
			'class' => 'header-nav-link'
		],
		'sublist' =>
		[
			'class' => 'header-nav-sublist'
		],
		'subitem' =>
		[
			'class' => 'header-nav-subitem'
		],
		'sublink' =>
		[
			'class' => 'header-nav-sublink'
		],
		'current-item' =>
		[
			'class' => 'header-nav-current-item'
		],
		'current-link' =>
		[
			'class' => 'header-nav-current-link'
		],
		'link-parent' =>
		[
			'class' => 'header-nav-link-parent'
		],
		'skip-to-content' => 'main'
	];
}
