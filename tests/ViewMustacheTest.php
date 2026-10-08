<?php

use Parser\View;

class ViewMustacheTest extends ParserTestCase
{
	public function testRendersVariable()
	{
		$this->writeView('greet.mustache', 'Hello {{name}}');
		$html = View::forge('greet.mustache', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}

	public function testEngineEscapesHtmlWhenAutoEncodeIsOff()
	{
		$this->writeView('raw.mustache', 'Hello {{name}}');
		$html = View::forge('raw.mustache', array('name' => '<b>'), false)->render();
		$this->assertStringContainsString('&lt;b&gt;', $html);
		$this->assertStringNotContainsString('<b>', $html);
	}

	public function testPartialsLoaderReadsDirectory()
	{
		$partialDir = APPPATH.'views'.DS.'partials';
		$this->ensureDir($partialDir);
		file_put_contents($partialDir.DS.'greet.mustache', 'Hi {{name}}');
		\Config::set('parser.View_Mustache.environment.partials_loader', $partialDir);
		$this->resetParserStatics();
		$this->writeView('main.mustache', '{{> greet}}');
		$html = View::forge('main.mustache', array('name' => 'Ada'), false)->render();
		$this->assertStringContainsString('Hi Ada', $html);
		\Config::set('parser.View_Mustache.environment.partials_loader', array());
	}
}
