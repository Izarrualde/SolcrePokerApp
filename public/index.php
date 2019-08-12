<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dotenv\Dotenv;

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
$callableResolver = $app->getCallableResolver(); //@TODO: Se usa para algo esto?

// Set up settings
$settings = require __DIR__ . getenv('SETTINGS');
$settings($app);

// Set up dependencies
$dependencies = require __DIR__ . getenv('DEPENDENCIES');
$dependencies($app);

// Register routes
$routes = require __DIR__ . getenv('ROUTES');
$routes($app);

// Register middleware
$middleware = require __DIR__ . getenv('MIDDLEWARE');
$middleware($app);

$app->run();
