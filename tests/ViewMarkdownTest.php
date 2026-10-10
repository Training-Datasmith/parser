<?php

use Parser\View;

class ViewMarkdownTest extends ParserTestCase
{
	public function testTransformsMarkdown()
	{
		$this->writeView('title.md', '# Title');
		$html = View::forge('title.md', array(), false)->render();
		$this->assertIsString($html);
		$this->assertStringContainsString('<h1>Title</h1>', $html);
	}

	public function testParserReturnsMarkdownExtraSingleton()
	{
		$a = \Parser\View_Markdown::parser();
		$b = \Parser\View_Markdown::parser();
		$this->assertInstanceOf(\Michelf\MarkdownExtra::class, $a);
		$this->assertSame($a, $b);
	}

	public function testAllowPhpExecutesView()
	{
		\Config::set('parser.View_Markdown.allow_php', true);
		$this->writeView('php.md', '<?php echo $name; ?>');
		$html = View::forge('php.md', array('name' => 'Ada'), false)->render();
		$this->assertStringContainsString('Ada', $html);
		\Config::set('parser.View_Markdown.allow_php', true);
	}

	public function testAllowPhpFalseDoesNotExecute()
	{
		$sentinel = APPPATH.'tmp'.DS.'markdown_sentinel_'.uniqid('', true);
		\Config::set('parser.View_Markdown.allow_php', false);
		$this->writeView('safe.md', '<?php file_put_contents("'.$sentinel.'", "1"); ?>');
		$html = View::forge('safe.md', array(), false)->render();
		$this->assertFalse(file_exists($sentinel));
		$this->assertStringContainsString('<?php', $html);
	}
}
