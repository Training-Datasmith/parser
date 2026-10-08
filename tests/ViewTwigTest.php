<?php

use Parser\View;

class ViewTwigTest extends ParserTestCase
{
	public function testRendersVariable()
	{
		$this->writeView('greet.twig', 'Hello {{ name }}');
		$html = View::forge('greet.twig', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}

	public function testCustomVariableDelimiters()
	{
		\Config::set('parser.View_Twig.delimiters', array(
			'tag_block'    => array('left' => '{%', 'right' => '%}'),
			'tag_comment'  => array('left' => '{#', 'right' => '#}'),
			'tag_variable' => array('left' => '[[', 'right' => ']]'),
		));
		$this->resetParserStatics();
		$this->writeView('custom.twig', 'Hello [[ name ]]');
		$html = View::forge('custom.twig', array('name' => 'Ada'), false)->render();
		$this->assertSame('Hello Ada', $html);
	}

	public function testSyntaxErrorIsTheTwigError()
	{
		$this->writeView('broken.twig', '{{ ');
		$this->expectException(\Twig\Error\SyntaxError::class);
		View::forge('broken.twig', array(), false)->render();
	}
}
