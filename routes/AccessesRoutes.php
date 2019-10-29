<?php

$router->group(
    ['prefix' => 'accesses'],
    function () use ($router) {

        $router->group(
            ['middleware' => 'role:super-admin, admin'],
            function () use ($router) {
                $router->group(
                    ['middleware' => 'permission:post, delete'],
                    function () use ($router) {
                        $router->post("/roles", "AccessesController@storeRoles");

                        $router->delete(
                            'roles/{id}',
                            "AccessesController@destroyRoles"
                        );

                        $router->post("/permissions", "AccessesController@storePermissions");

                        $router->delete(
                            'permissions/{id}',
                            "AccessesController@destroyPermissions"
                        );
                    }
                );

                $router->group(
                    ['middleware' => 'permission:get'],
                    function () use ($router) {

                        $router->get('/roles', "AccessesController@roles");
                        $router->get('/permissions', "AccessesController@permissions");
                    }
                );

                $router->get(
                    '{id}',
                    [
                        'middleware' => 'permission:get',
                        'uses' => "AccessesController@show"
                    ]
                );
            }
        );
    }
);
