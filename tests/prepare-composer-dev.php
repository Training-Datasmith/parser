<?php

$source = $argv[1] ?? (__DIR__.'/../composer.json');
$target = $argv[2] ?? '/tmp/composer.dev.json';

$data = json_decode(file_get_contents($source), true);
unset($data['require-dev']['fuel/core']);
unset($data['repositories']);
file_put_contents($target, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
