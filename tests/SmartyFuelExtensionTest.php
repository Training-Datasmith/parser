<?php

use Parser\View;
use Parser\Smarty_Fuel_Extension;

class SmartyFuelExtensionTest extends ParserTestCase
{
	public function testFuelVersionPlugin()
	{
		$this->writeView('fv.smarty', '{fuel_version}');
		$html = View::forge('fv.smarty', array(), false)->render();
		$this->assertSame(\Fuel::VERSION, trim($html));
	}

	public function testFormRadioEmitsRadio()
	{
		$this->writeView('radio.smarty', '{form_radio field="color" value="red" checked=true}');
		$html = View::forge('radio.smarty', array(), false)->render();
		$this->assertStringContainsString('type="radio"', $html);
	}

	public function testConfigDefaultIsUsed()
	{
		$this->writeView('cfgdef.smarty', '{config item="parser.missing_key" default="from-default"}');
		$withDefault = View::forge('cfgdef.smarty', array(), false)->render();
		$this->assertSame('from-default', trim($withDefault));

		$this->writeView('cfgempty.smarty', '{config item="parser.missing_key"}');
		$empty = View::forge('cfgempty.smarty', array(), false)->render();
		$this->assertSame('', trim($empty));
	}
}
