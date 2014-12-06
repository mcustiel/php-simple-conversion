<?php

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Mcustiel\\', __DIR__ . '/../src/Mcustiel');
$loader->addPsr4('Integration\\', __DIR__ . '/integration/Mcustiel');
$loader->addPsr4('Fixtures\\', __DIR__ . '/fixtures');

define('FIXTURES_PATH', __DIR__ . '/fixtures');
