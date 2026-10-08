<?php

use Parser\View;

class ViewSmartyTest extends ParserTestCase
{
	public function testRendersVariable()
	{
		$this->writeView('greet.smarty', 'Hello {$name}');
		$html = View::forge('greet.smarty', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}

	public function testDelimitersFromConfig()
	{
		\Config::set('parser.View_Smarty.delimiters', array('left' => '<%', 'right' => '%>'));
		$this->resetParserStatics();
		$this->writeView('custom.smarty', 'Hello <%$name%>');
		$html = View::forge('custom.smarty', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}
}
