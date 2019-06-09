<?php

// settings.php

define('APP_ROOT', __DIR__);

return [
    'displayErrorDetails' => true,
    'determineRouteBeforeAppMiddleware' => false,

    'doctrine' => [
        // if true, metadata caching is forcefully disabled
        'dev_mode' => true,

        // path where the compiled metadata info will be cached
        // make sure the path exists and it is writable
        'cache_dir' => APP_ROOT . '/data/cache',

        // you should add any other path containing annotated entity classes
        'metadata_dirs' => [APP_ROOT . '/src/Entity'],

        'connection' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'port' => 3306,
            'dbname' => 'lmsuy_db',
            'user' => 'root',
            'password' => '',
            'charset' => 'utf8'
        ]
    ]
];