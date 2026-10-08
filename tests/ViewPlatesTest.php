<?php

use Parser\View;
use ParserTests\Fixtures\ShoutPlatesExtension;

class ViewPlatesTest extends ParserTestCase
{
	public function testRendersExtractedData()
	{
		$this->writeView('page.tpl', 'Hello <?= $name ?>');
		$html = View::forge('page.plates', array('name' => 'Ada'), false)->render();
		$this->assertStringContainsString('Hello Ada', $html);
	}

	public function testEscapeHelper()
	{
		$this->writeView('esc.tpl', '<?= $this->e($name) ?>');
		$html = View::forge('esc.plates', array('name' => '<b>'), false)->render();
		$this->assertStringContainsString('&lt;b&gt;', $html);
		$this->assertStringNotContainsString('<b>', $html);
	}

	public function testCustomExtensionIsUsed()
	{
		\Config::set('parser.View_Plates.extensions', array(ShoutPlatesExtension::class));
		$this->resetParserStatics();
		$this->writeView('shout.tpl', '<?= $this->shout("ada") ?>');
		$html = View::forge('shout.plates', array(), false)->render();
		$this->assertSame('ADA', trim($html));
		\Config::set('parser.View_Plates.extensions', array());
	}

	public function testInvalidExtensionThrows()
	{
		\Config::set('parser.View_Plates.extensions', array('stdClass'));
		$this->resetParserStatics();
		$this->writeView('bad.tpl', 'x');
		$this->expectException(\FuelException::class);
		$this->expectExceptionMessage('stdClass');
		View::forge('bad.plates', array(), false)->render();
		\Config::set('parser.View_Plates.extensions', array());
	}
}
