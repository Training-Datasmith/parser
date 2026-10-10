<?php

use Parser\View;
use Parser\View_Lex;

class ViewLexTest extends ParserTestCase
{
	protected function setUp(): void
	{
		if (PHP_VERSION_ID >= 80200)
		{
			$this->markTestSkipped('DEFERRED: Lex dynamic-property deprecation on PHP 8.2+');
		}
		parent::setUp();
	}

	public function testRendersVariableAndScope()
	{
		$this->writeView('greet.lex', 'Hello {{ name }}');
		$html = View::forge('greet.lex', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);

		$this->writeView('scoped.lex', 'Hello {{ user.name }}');
		$scoped = View::forge('scoped.lex', array('user' => array('name' => 'Ada')), false)->render();
		$this->assertSame('Hello Ada', $scoped);
	}

	public function testAllowPhpFalseEscapesPhpTags()
	{
		$sentinel = APPPATH.'tmp'.DS.'lex_sentinel_'.uniqid('', true);
		$this->writeView('safe.lex', '<?php file_put_contents("'.$sentinel.'", "1"); ?>');
		$html = View::forge('safe.lex', array(), false)->render();
		$this->assertFalse(file_exists($sentinel));
		$this->assertStringContainsString('&lt;?', $html);
	}

	public function testCallbackIsInvoked()
	{
		$this->writeView('cb.lex', '{{ shout }}');
		$view = View::forge('cb.lex', array(), false);
		$view->setCallback(function () {
			return 'CB';
		});
		$html = $view->render();
		$this->assertStringContainsString('CB', $html);
	}

	public function testInjectNoparseReturnsInjectedString()
	{
		$parser = View_Lex::parser();
		$parser->cumulativeNoparse(true);
		$parsed = $parser->parse('{{ noparse }}{{ name }}{{ /noparse }}', array('name' => 'Ada'));
		$result = View_Lex::injectNoparse($parsed);
		$this->assertIsString($result);
		$this->assertStringContainsString('{{ name }}', $result);
	}
}
