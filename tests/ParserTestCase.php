<?php

use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase
{
	protected $viewsDir;

	protected $routerRoutesSnapshot;

	protected $obLevel;

	protected function setUp(): void
	{
		parent::setUp();
		$this->obLevel = ob_get_level();
		$this->resetParserStatics();
		$this->viewsDir = APPPATH.'views'.DS;
		if (! is_dir($this->viewsDir))
		{
			mkdir($this->viewsDir, 0777, true);
		}
		$this->ensureDir(APPPATH.'cache'.DS.'twig');
		$this->clearDir(APPPATH.'cache'.DS.'twig');
		$this->ensureDir(APPPATH.'cache'.DS.'jade');
		$this->clearDir(APPPATH.'cache'.DS.'jade');
		$this->ensureDir(APPPATH.'tmp'.DS.'Smarty'.DS.'templates_c');
		$this->clearDir(APPPATH.'tmp'.DS.'Smarty'.DS.'templates_c');
		$this->routerRoutesSnapshot = \Router::$routes;
		\Config::load('parser', true);
		$parserDefaults = include PKGPATH.'parser'.DS.'config'.DS.'parser.php';
		\Config::set('parser.View_Twig.delimiters', $parserDefaults['View_Twig']['delimiters']);
		\Config::set('parser.View_Smarty.delimiters', $parserDefaults['View_Smarty']['delimiters']);
		\Config::set('parser.View_Plates.extensions', array());
	}

	protected function tearDown(): void
	{
		while (ob_get_level() > $this->obLevel)
		{
			ob_end_clean();
		}
		$this->resetParserStatics();
		\Router::$routes = $this->routerRoutesSnapshot;
		\Config::load('parser', true);
		parent::tearDown();
	}

	protected function resetParserStatics()
	{
		$this->bindStatic('Parser\\View', function () {
			static::$loaded_files = array();
		});
		$this->bindStatic('Parser\\View_Mustache', function () {
			static::$_parser = null;
		});
		$this->bindStatic('Parser\\View_Twig', function () {
			static::$_parser = null;
			static::$_parser_loader = null;
			static::$_twig_lexer_conf = null;
			static::$_version = null;
		});
		$this->bindStatic('Parser\\View_Smarty', function () {
			static::$_parser = null;
		});
		$this->bindStatic('Parser\\View_Dwoo', function () {
			static::$_parser = null;
			static::$_parser_compiler = null;
			static::$_parser_security = null;
		});
		$this->bindStatic('Parser\\View_Lex', function () {
			static::$_parser = null;
		});
		$this->bindStatic('Parser\\View_Plates', function () {
			static::$_parser = null;
		});
		$this->bindStatic('Parser\\View_Phptal', function () {
			static::$_parser = null;
		});
		$this->bindStatic('Parser\\View_Haml', function () {
			static::$_parser = null;
			static::$_cache = null;
		});
		$this->bindStatic('Parser\\View_HamlTwig', function () {
			static::$_parser = null;
			static::$_parser_loader = null;
			static::$_environment = null;
			static::$_twig_lexer_conf = null;
			static::$_version = null;
		});
		$this->bindStatic('Lex\\Parser', function () {
			static::$data = null;
			static::$extractions = array();
			static::$callbackData = null;
		});
		$this->bindStatic('Fuel\\Core\\View', function () {
			static::$global_data = array();
			static::$global_filter = array();
		});
	}

	protected function bindStatic($class, \Closure $closure)
	{
		if (! class_exists($class, false))
		{
			return;
		}
		$bound = \Closure::bind($closure, null, $class);
		$bound();
	}

	protected function writeView($relative, $contents)
	{
		$path = $this->viewsDir.$relative;
		$dir = dirname($path);
		if (! is_dir($dir))
		{
			mkdir($dir, 0777, true);
		}
		file_put_contents($path, $contents);

		return $path;
	}

	protected function ensureDir($path)
	{
		if (! is_dir($path))
		{
			mkdir($path, 0777, true);
		}
	}

	protected function clearDir($path)
	{
		if (! is_dir($path))
		{
			return;
		}
		foreach (scandir($path) as $entry)
		{
			if ($entry === '.' || $entry === '..')
			{
				continue;
			}
			$full = $path.DS.$entry;
			is_dir($full) ? $this->clearDir($full) : unlink($full);
		}
	}
}
