<?php

use Parser\View;
use Parser\View_Lex;
use Parser\View_Markdown;
use Parser\View_Mustache;
use Parser\View_Smarty;
use Parser\View_Twig;

class ViewForgeTest extends ParserTestCase
{
	public function testForgeDispatchesByExtension()
	{
		$this->writeView('hello.md', '# Hi');
		$this->writeView('hello.mustache', 'x');
		$this->writeView('hello.twig', 'x');
		$this->writeView('hello.smarty', 'x');
		$this->writeView('hello.lex', 'x');
		$this->writeView('hello.php', '<?php echo 1;');

		$this->assertInstanceOf(View_Markdown::class, View::forge('hello.md'));
		$this->assertInstanceOf(View_Mustache::class, View::forge('hello.mustache'));
		$this->assertInstanceOf(View_Twig::class, View::forge('hello.twig'));
		$this->assertInstanceOf(View_Smarty::class, View::forge('hello.smarty'));
		$this->assertInstanceOf(View_Lex::class, View::forge('hello.lex'));
		$this->assertInstanceOf(View::class, View::forge('hello.php'));
	}

	public function testArrayExtensionOverridesFileExtension()
	{
		$this->writeView('page.tpl', 'Hello <?= $name ?>');
		$view = View::forge('page.plates', array('name' => 'Ada'), false);
		$html = $view->render();
		$this->assertIsString($html);
		$this->assertStringContainsString('Hello Ada', $html);
	}

	public function testPhpExtensionRendersAssignedData()
	{
		$this->writeView('hello.php', '<?php echo $name; ?>');
		$html = View::forge('hello.php', array('name' => 'Ada'), false)->render();
		$this->assertSame('Ada', $html);
	}

	public function testAutoEncodeComesFromConfigAndCanBeOverridden()
	{
		$this->writeView('hello.md', 'x');
		$fromConfig = View::forge('hello.md');
		$this->assertTrue($fromConfig->auto_filter());
		$overridden = View::forge('hello.md', array(), false);
		$this->assertFalse($overridden->auto_filter());
	}

	public function testMissingViewThrows()
	{
		$this->expectException(\FuelException::class);
		View::forge('missing.mustache');
	}

	public function testUnknownExtensionThrows()
	{
		$this->expectException(\FuelException::class);
		View::forge('hello.unknown');
	}

	public function testAbsolutePathKeepsExtension()
	{
		$dir = sys_get_temp_dir().DS.'parser-abs-'.uniqid('', true);
		mkdir($dir);
		$file = $dir.DS.'abs.mustache';
		file_put_contents($file, 'Hello {{name}}');
		try
		{
			$html = View::forge($file, array('name' => 'Ada'), false)->render();
			$this->assertStringContainsString('Hello Ada', $html);
		}
		finally
		{
			@unlink($file);
			@rmdir($dir);
		}
	}

	public function testIncludeIsRequiredOnce()
	{
		$counterFile = APPPATH.'tmp'.DS.'mustache_include_counter.php';
		$this->ensureDir(dirname($counterFile));
		file_put_contents($counterFile, '<?php $GLOBALS["parser_include_count"] = isset($GLOBALS["parser_include_count"]) ? $GLOBALS["parser_include_count"] + 1 : 1;');
		\Config::set('parser.View_Mustache.include', array($counterFile));
		$this->resetParserStatics();
		$this->writeView('hello.mustache', 'x');
		View::forge('hello.mustache');
		View::forge('hello.mustache');
		$this->assertSame(1, $GLOBALS['parser_include_count']);
		\Config::set('parser.View_Mustache.include', array());
	}
}
