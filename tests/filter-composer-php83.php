<?php

$data = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
unset($data['require-dev']['dwoo/dwoo'], $data['require-dev']['phptal/phptal'], $data['require-dev']['fuel/core']);
unset($data['repositories']);
file_put_contents('/tmp/composer.php83.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
