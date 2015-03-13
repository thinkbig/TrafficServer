<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');
Route::post('welcome/gzip', 'WelcomeController@postGzip');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController'
]);

Route::controller('traffic', 'TrafficController');
Route::controller('user', 'UserController');
Route::controller('parking', 'ParkingController');
Route::controller('trip', 'TripController');



