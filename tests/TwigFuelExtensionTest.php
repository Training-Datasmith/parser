<?php

use Parser\View;

class TwigFuelExtensionTest extends ParserTestCase
{
	public function testFuelVersionFunction()
	{
		$this->writeView('fv.twig', '{{ fuel_version() }}');
		$html = View::forge('fv.twig', array(), false)->render();
		$this->assertSame(\Fuel::VERSION, trim($html));
	}

	public function testConfigFunction()
	{
		\Config::set('app.answer', '42');
		$this->writeView('cfg.twig', '{{ config("app.answer") }}');
		$html = View::forge('cfg.twig', array(), false)->render();
		$this->assertSame('42', trim($html));
	}

	public function testUrlUsesNamedRouteThenUri()
	{
		\Router::add('pages/hello/(:name)', array('name' => 'hello'));
		$this->writeView('named.twig', '{{ url("hello", {name: "Ada"}) }}');
		$named = View::forge('named.twig', array(), false)->render();
		$this->assertStringContainsString('pages/hello/Ada', $named);

		$this->writeView('plain.twig', '{{ url("welcome") }}');
		$plain = View::forge('plain.twig', array(), false)->render();
		$this->assertStringContainsString('http://localhost/welcome', $plain);
	}
}
