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

$router->get('/', 'Controller@index');
$router->get('users', 'Controller@users');
$router->get('users/{id}', 'Controller@users');

$router->get('/hello-lumen', function () use ($router) {
    return "<h1>Lumen</h1><p>Persib Nu aing!</p>";
});

$router->get('/scores', ['middleware' => 'login', function () use ($router) {
    return "<h1>Scores</h1><p>Persib 2-0 Arema</p>";
}]);

// Content Negotiation UI / View Studio
$router->get('/playground', function () {
    return view('playground');
});
$router->get('/posts-view', function () {
    return view('playground');
});

$router->get('/posts', 'PostsController@index');
$router->get('/post/{id}', 'PostsController@show');
$router->post('/posts', 'PostsController@store');
$router->put('/post/{id}', 'PostsController@update');
$router->delete('/post/{id}', 'PostsController@destroy');

// Products CRUD
$router->get('/products', 'ProductsController@index');
$router->get('/product/{id}', 'ProductsController@show');
$router->post('/products', 'ProductsController@store');
$router->put('/product/{id}', 'ProductsController@update');
$router->delete('/product/{id}', 'ProductsController@destroy');