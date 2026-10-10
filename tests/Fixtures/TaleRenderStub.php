<?php

namespace ParserTests\Fixtures;

class TaleRenderStub
{
	public $lastPath;

	public $lastData;

	public function render($path, $data)
	{
		$this->lastPath = $path;
		$this->lastData = $data;

		return '<p>STUB</p>';
	}
}
