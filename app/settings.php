<?php

declare(strict_types=1);

use DI\Container;
use Monolog\Logger;
use Slim\App;

return function (App $app) {
    /** @var Container $container */
    $container = $app->getContainer();

    // Global Settings Object
    $container->set(
        'settings',
        [
            'displayErrorDetails' => true,
            'determineRouteBeforeAppMiddleware' => false,
            'number_format' => [
                'decimal_separator'  => ',',
                'thousand_separator' => '.',
                'decimals'           => '2'
            ],
            'doctrine'      =>  [
                // if true, metadata caching is forcefully disabled
                'dev_mode'  => true,

                // path where the compiled metadata info will be cached
                // make sure the path exists and it is writable
                'cache_dir'     => __DIR__ . '/../data/cache',

                // you should add any other path containing annotated entity classes
                'metadata_dirs' => [
                    __DIR__ . '/../src/Entity'
                ],
                'connection'    => [
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'port' => getenv('PORT'),
                    'dbname' => getenv('DB_NAME'),
                    'user' => getenv('USER'),
                    'password' => getenv('PASSWORD'),
                    'charset' => getenv('CHARSET')
                ]
            ]
        ]
    );
};
