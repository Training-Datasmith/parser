<?php

use Parser\View;

class ViewJadeTest extends ParserTestCase
{
	public function testTaleRendersParagraph()
	{
		$this->writeView('para.jade', 'p= $name');
		$html = View::forge('para.jade', array('name' => 'Ada'), false)->render();
		$this->assertIsString($html);
		$this->assertNotSame('', $html);
		$this->assertStringContainsString('Ada', $html);
		$this->assertStringContainsString('<p', $html);
	}
}
