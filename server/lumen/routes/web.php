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
    $router->get('', 'CommandController@index');
    $router->post('', 'CommandController@store');
    $router->get('next', 'CommandController@next');

    $router->group(['prefix' => '{id}'], function () use ($router) {
        $router->get('', 'CommandController@show');
        $router->put('', 'CommandController@replace');
        $router->delete('', 'CommandController@destroy');
        $router->patch('', 'CommandController@update');
    });
});
