<?php

// settings.php

return [
    'displayErrorDetails' => true,
    'determineRouteBeforeAppMiddleware' => false,
    'number_format' => [
        'decimal_separator' => ',',
        'thousand_separator' => '.',
        'decimals' => '2'
    ],
    'doctrine' => [
        // if true, metadata caching is forcefully disabled
        'dev_mode' => true,

        // path where the compiled metadata info will be cached
        // make sure the path exists and it is writable
        'cache_dir' => __DIR__ . '/data/cache',

        // you should add any other path containing annotated entity classes
        'metadata_dirs' => [__DIR__ . '/src/Entity'],
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
