<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = realpath(__DIR__.'/..').DIRECTORY_SEPARATOR;

define('DOCROOT', $root.'tests'.DIRECTORY_SEPARATOR);
define('APPPATH', $root.'tests/app'.DIRECTORY_SEPARATOR);
define('PKGPATH', $root.'pkg-overlay'.DIRECTORY_SEPARATOR);
define('COREPATH', $root.'fuel/packages/core'.DIRECTORY_SEPARATOR);
define('VENDORPATH', $root.'vendor'.DIRECTORY_SEPARATOR);

$_SERVER['doc_root'] = 'tests/';
$_SERVER['app_path'] = 'tests/app';
$_SERVER['core_path'] = 'fuel/packages/core';
$_SERVER['package_path'] = 'tests/pkg';
$_SERVER['vendor_path'] = 'vendor';
$_SERVER['FUEL_ENV'] = 'test';

if (! is_file(VENDORPATH.'autoload.php'))
{
	fwrite(STDERR, "Run composer install before executing tests.\n");
	exit(1);
}

require VENDORPATH.'autoload.php';

if (! is_file(COREPATH.'bootstrap.php'))
{
	fwrite(STDERR, "fuel/core is missing at ".COREPATH."\n");
	exit(1);
}

$pkgLink = PKGPATH.'parser';
if (! is_dir(PKGPATH))
{
	mkdir(PKGPATH, 0777, true);
}
if (is_link($pkgLink) || file_exists($pkgLink))
{
	@unlink($pkgLink);
}
symlink($root, $pkgLink);

require COREPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

require APPPATH.'bootstrap.php';

\Fuel::$is_test = true;

\Package::load('parser');

foreach (array(
	'Parser\\View',
	'Parser\\View_Markdown',
	'Parser\\View_Mustache',
	'Parser\\View_Twig',
	'Parser\\View_Smarty',
	'Parser\\View_Lex',
	'Parser\\View_Plates',
	'Parser\\View_Handlebars',
	'Parser\\View_Jade',
	'Parser\\View_Phptal',
) as $parserClass)
{
	class_exists($parserClass);
}

require __DIR__.'/ParserTestCase.php';
