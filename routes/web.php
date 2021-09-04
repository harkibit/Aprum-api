<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '/api/v1'], function () use ($router) {
    $router->get('/languages', function () {
        return \App\Models\Language::with('versions')->get();
    });
    $router->post('/execute', 'SnippetController@execute');
    $router->group(['prefix' => '/auth'], function () use ($router) {
        $router->post('/login', 'AuthController@login');
        $router->post('/register', 'AuthController@register');
    });

    $router->group(['prefix' => '/user'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->put('/', 'UserController@update');
        $router->get('/snippets', 'UserController@snippets');
    });

    $router->group(['prefix' => '/snippets'], function () use ($router) {
        $router->get('/', 'SnippetController@index');
        $router->get('/{slug}', 'SnippetController@show');
        $router->post('/', 'SnippetController@store');
        $router->put('/{slug}', 'SnippetController@update');
        $router->delete('/{slug}', 'SnippetController@destroy');
    });
});
