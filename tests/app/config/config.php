<?php

return array(
	'base_url' => 'http://localhost/',
	'index_file' => false,
	'profiling' => false,
	'caching' => false,
	'default_timezone' => 'UTC',
	'locale' => '',
	'security' => array(
		'auto_filter_output' => true,
		'output_filter' => 'Security::htmlentities',
	),
	'errors' => array(
		'continue_on' => array(),
	),
	'package_paths' => array(
		PKGPATH,
	),
);
