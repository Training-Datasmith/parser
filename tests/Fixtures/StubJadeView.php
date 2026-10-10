<?php

namespace ParserTests\Fixtures;

class StubJadeView extends \Parser\View_Jade
{
	/** @var TaleRenderStub */
	public $taleStub;

	protected function tale_parser($cachepath)
	{
		$this->taleStub = new TaleRenderStub();

		return $this->taleStub;
	}
}
