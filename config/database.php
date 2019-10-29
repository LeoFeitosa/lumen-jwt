<?php

return [

    'default' => env('DB_CONNECTION', 'sqlsrv'),

    'connections' => [

        'sqlsrv' => [
            'driver' => env('DB_CONNECTION', 'sqlsrv'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        'sqlsrvRM' => [
            'driver' => env('DB_CONNECTION_SQLSRV', 'sqlsrv'),
            'host' => env('DB_HOST_SQLSRV', 'localhost'),
            'port' => env('DB_PORT_SQLSRV', '1433'),
            'database' => env('DB_DATABASE_SQLSRV', 'forge'),
            'username' => env('DB_USERNAME_SQLSRV', 'forge'),
            'password' => env('DB_PASSWORD_SQLSRV', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    'migrations' => 'migrations',

];
