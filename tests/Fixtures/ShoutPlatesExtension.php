<?php

namespace ParserTests\Fixtures;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;

class ShoutPlatesExtension implements ExtensionInterface
{
	public function register(Engine $engine)
	{
		$engine->registerFunction('shout', function ($text) {
			return strtoupper($text);
		});
	}
}
