<?php

require COREPATH.'bootstrap.php';

\Autoloader::add_classes(array());

\Autoloader::register();

\Fuel::$env = \Arr::get($_SERVER, 'FUEL_ENV', \Arr::get($_ENV, 'FUEL_ENV', getenv('FUEL_ENV') ?: \Fuel::TEST));

\Fuel::init('config.php');
