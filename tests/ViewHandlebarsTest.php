<?php

use Parser\View;

class ViewHandlebarsTest extends ParserTestCase
{
	public function testForgeRendersHandlebars()
	{
		$this->writeView('greet.handlebars', 'Hello {{name}}');
		$html = View::forge('greet.handlebars', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}

	public function testDoubleStashEscapesAndTripleDoesNot()
	{
		$this->writeView('esc.handlebars', '{{name}} {{{name}}}');
		$html = View::forge('esc.handlebars', array('name' => '<b>'), false)->render();
		$this->assertStringContainsString('&lt;b&gt;', $html);
		$this->assertStringContainsString('<b>', $html);
	}

	public function testPartialResolverUsesFinder()
	{
		$this->writeView('bit.handlebars', 'BIT');
		$this->writeView('main.handlebars', '{{> bit}}');
		$html = View::forge('main.handlebars', array(), false)->render();
		$this->assertStringContainsString('BIT', $html);
	}
}
