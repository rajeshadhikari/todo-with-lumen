<?php
use Illuminate\Support\Facades\Route;
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

$router->group(['prefix' => 'api/v1/'], function () use ($router) {
    $router->post('login', 'UserController@login');
});
$router->group(['prefix' => 'api/v1/', 'middleware' => 'auth'], function () use ($router) {
    Route::get('todo', 'TodoController@index');
    Route::get('todo/{id}', 'TodoController@show');
    Route::post('todo', 'TodoController@store');
    Route::post('todo/{id}', 'TodoController@update');
    Route::delete('todo/{id}', 'TodoController@delete');
    Route::put('todo-update-status/{id}', 'TodoController@updateStatus');
    Route::post('todo-add-reminder', 'TodoController@addReminder');

});

