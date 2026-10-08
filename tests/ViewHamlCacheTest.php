<?php

use Parser\View_Haml;

class ViewHamlCacheTest extends ParserTestCase
{
	public function testCacheInitCreatesDirectoryAndSetsCache()
	{
		$cacheRoot = APPPATH.'cache'.DS.'haml-test-'.uniqid('', true).DS;
		\Config::set('parser.View_Haml.cache_dir', $cacheRoot);
		$file = APPPATH.'views'.DS.'sample.haml';
		$this->writeView('sample.haml', '%p x');

		$view = new View_Haml(null, array(), false);
		$view->cache_init($file);

		$key = md5($file);
		$expected = $cacheRoot.substr($key, 0, 2).DS.substr($key, 2, 2);
		$this->assertDirectoryExists($expected);

		$ref = new ReflectionClass(View_Haml::class);
		$prop = $ref->getProperty('_cache');
		$prop->setAccessible(true);
		$this->assertSame($expected, $prop->getValue());
	}
}
