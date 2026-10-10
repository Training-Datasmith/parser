<?php

use Parser\View;

class ViewPhptalTest extends ParserTestCase
{
	protected function setUp(): void
	{
		if (PHP_VERSION_ID >= 80000)
		{
			$this->markTestSkipped('DEFERRED: phptal/phptal 1.4.0 is not installed on PHP 8+ in this harness.');
		}
		parent::setUp();
	}

	public function testRendersTalContent()
	{
		$this->ensureDir(\Config::get('parser.View_Phptal.cache_dir', APPPATH.'cache'.DS.'PHPTAL'.DS));
		$this->writeView('para.phptal', '<p tal:content="name">x</p>');
		$html = View::forge('para.phptal', array('name' => 'Ada'), false)->render();
		$this->assertStringContainsString('<p>Ada</p>', $html);
	}
}
