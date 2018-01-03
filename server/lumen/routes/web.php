<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    return env('APP_NAME');
});

$router->group(['prefix' => '/commands/{name}'], function () use ($router) {
    $router->get('', function (string $name) {
        return "command list for $name";
    });

    $router->get('next', function (string $name) {
        return "next command for $name";
    });

    $router->post('', function (string $name) {
        return "adding a command for $name";
    });

    $router->group(['prefix' => '{id}'], function () use ($router) {
        $router->get('', function (string $name, int $id) {
            return "getting command $id for $name";
        });

        $router->put('', function (string $name, int $id) {
            return "replacing command $id for $name";
        });

        $router->delete('', function (string $name, int $id) {
            return "deleting command $id for $name";
        });

        $router->patch('', function (string $name, int $id) {
            return "$name is updating command $id";
        });
    });
});
