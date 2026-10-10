<?php

use Parser\View;
use ParserTests\Fixtures\StubJadeView;

class ViewJadeTest extends ParserTestCase
{
	public function testTaleRendersParagraph()
	{
		if (PHP_VERSION_ID >= 70200)
		{
			$this->markTestSkipped('DEFERRED: tale-jade 1.4.5 count() on null private Compiler::$files on PHP 7.2+');
		}
		$this->writeView('para.jade', 'p= $name');
		$html = View::forge('para.jade', array('name' => 'Ada'), false)->render();
		$this->assertIsString($html);
		$this->assertNotSame('', $html);
		$this->assertStringContainsString('Ada', $html);
		$this->assertStringContainsString('<p', $html);
	}

	public function testTaleParserReceivesPathAndData()
	{
		$this->writeView('stub.jade', 'p x');
		$viewPath = APPPATH.'views'.DS.'stub.jade';
		$view = new StubJadeView($viewPath, array('name' => 'Ada'), false);
		$html = $view->render();
		$this->assertSame('<p>STUB</p>', $html);
		$this->assertSame($viewPath, $view->taleStub->lastPath);
		$this->assertArrayHasKey('name', $view->taleStub->lastData);
		$this->assertSame('Ada', $view->taleStub->lastData['name']);
	}
}
