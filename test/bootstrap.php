<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'AppWrapper.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dotenv\Dotenv;
use Test\AppWrapper;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Montevideo');

// Load dot env library
$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

// Instantiate PHP-DI Container
$container = new Container();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Set up settings
$settings = require __DIR__ . getenv('SETTINGS');
$settings($app);

// Set up dependencies
$dependencies = require __DIR__ . getenv('DEPENDENCIES');
$dependencies($app);

AppWrapper::setApp($app);