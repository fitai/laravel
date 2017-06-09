<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');


Auth::routes();
Route::get('register/verify/{token}', 'Auth\RegisterController@verify');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/lift', 'LiftController@create')->name('lift');
Route::post('/lift/store', 'LiftController@store')->name('lift.store');
Route::post('/lift/stop', 'LiftController@endLift')->name('lift.stop');
Route::get('/lift/summary/{id}', 'LiftController@show')->name('lift.summary');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/export', 'HomeController@index')->name('export');
Route::get('/settings', 'HomeController@index')->name('settings');
Route::get('/switch', 'HomeController@switch')->name('switch');
Route::get('/switch/athlete/{id}', 'HomeController@switchAthlete');
Route::get('/team', function() {
	return Auth::user()->athlete->team->athletes;
})->middleware('auth');

// Lifts
Route::resource('lifts', 'LiftController');

// Admin
Route::get('/admin', 'AdminController@index')->name('admin');

// Test Redis
Route::get('/redis', function() {

	// Publish event with Redis
	$data = [
		'event' => 'UserSignedUp',
		'data' => [
			'username' => Auth::user()->name
		]
	];

	// Redis::publish('lifts', json_encode($data));
	Redis::publish('lifts', 'success');

	return 'Redis event published';
});
