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
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/export', 'HomeController@index')->name('export');
Route::get('/settings', 'HomeController@index')->name('settings');
Route::get('/switch', 'HomeController@switch')->name('switch');
Route::get('/switch/athlete/{id}', 'HomeController@switchAthlete');
Route::get('/team', function() {
	return Auth::user()->athlete->team->athletes;
})->middleware('auth');


// RFID
Route::get('/rfid', 'HomeController@rfidListener')->name('rfid.listener');
Route::post('/rfid/login', 'HomeController@rfidLogin')->name('rfid.login');

// Lifts
Route::resource('lifts', 'LiftController');
Route::get('/lift', 'LiftController@create')->name('lift');
Route::get('/lift2', 'LiftController@test');
Route::post('/lift/store', 'LiftController@store')->name('lift.store');
Route::post('/lift/stop', 'LiftController@endLift')->name('lift.stop');
Route::get('/lift/summary/{id}', 'LiftController@show')->name('lift.summary');
Route::patch('/lift/update', 'LiftController@update')->name('lift.update');
Route::get('/lift/kill/{id}', 'LiftController@killLift');
Route::post('/lift/get-type', 'LiftController@getTypeData');
Route::get('/lift/last/{athlete_id?}', 'LiftController@getLastLift');
Route::get('/lift/next/{athlete_id?}', 'LiftController@getNextLift');
Route::get('/lift/schedule', 'LiftController@schedule')->name('lift.schedule');
Route::post('/lift/schedule', 'LiftController@storeSchedule')->name('lift.schedule.store');

// Admin
Route::get('/admin', 'AdminController@index')->name('admin');
Route::get('/admin/watch', 'AdminController@watch')->name('admin.watch');
Route::get('/admin/athlete-rest', 'AdminController@athleteRest')->name('admin.athlete.rest');

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

