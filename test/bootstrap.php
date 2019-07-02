<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'ContainerAwareTrait.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'AppWrapper.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Test\AppWrapper;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Montevideo');

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

// Instantiate App
$app = AppFactory::create();

$bootstrap = new AppWrapper($app);