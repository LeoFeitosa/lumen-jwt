<?php

use Illuminate\Support\Facades\Storage;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(
    ['prefix' => 'api'],
    function () use ($router) {
        $router->post(
            'user/login',
            'UserController@login'
        );
        $router->post(
            'user/reset/password',
            'UserController@resetPasswordEmail'
        );
        $router->post(
            'user/password',
            'UserController@resetPasswordCode'
        );
        $router->post(
            'user/password/code',
            'UserController@validateRememberCode'
        );

        $router->group(
            ['middleware' => ['auth', 'jwt.refresh']],
            function () use ($router) {
                $path = base_path('routes/');
                if ($handle = @opendir($path)) {
                    while ($entry = readdir($handle)) {
                        $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                        if (in_array($ext, array('php')) && $entry != 'web.php')
                            require_once $path . $entry;
                    }
                    closedir($handle);
                }
            }
        );
    }
);
