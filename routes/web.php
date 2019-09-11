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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('foo', function() {
    return 'Hello World';
});

$router->get('user', 'UserController@index');

$router->get('user/{id}', 'UserController@get');

$router->post('user', 'UserController@add');

$router->post('user/login', 'UserController@login');

$router->put('user', 'UserController@edit');

$router->delete('user/{id}', 'UserController@delete');

/* == RESTAURANTS == */

$router->get('restaurants', 'RestaurantController@index');

$router->get('restaurants/{id}', 'RestaurantController@get');

$router->post('restaurants', 'RestaurantController@add');

$router->put('restaurants', 'RestaurantController@edit');

$router->delete('restaurants/{id}', 'RestaurantController@delete');

/* == FOODS == */

$router->get('foods', 'FoodController@index');

$router->get('food/{id}', 'FoodController@get');

$router->post('food', 'FoodController@add');

$router->put('food', 'FoodController@edit');

$router->delete('food/{id}', 'FoodController@delete');
