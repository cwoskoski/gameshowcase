<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['uses' => 'HomeController@index', 'as' => 'home']);
Route::get('/dashboard', ['uses' => 'SubmissionController@index', 'as' => 'dashboard']);
Route::get('auth/login', ['uses' => 'Auth\AuthController@getLogin', 'as' => 'auth.login']);
Route::post('auth/login', ['uses' => 'Auth\AuthController@postLogin', 'as' => 'auth.login.post']);
Route::get('auth/logout', ['uses' => 'Auth\AuthController@getLogout', 'as' => 'auth.logout']);

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');

// Submission
Route::resource('submission.media', 'SubmissionMediaController');
get('submissions/{id}/media/{rid}/media', 'SubmissionMediaController@media');
get('submissions/{id}/media/{rid}/remove', 'SubmissionMediaController@destroy');

get('submission', [
    'uses' => 'SubmissionController@index',
    'as' => 'submissions'
]);
post('submission', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@store',
    'as' => 'submission.store'
]);
get('submission/pick', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@pick',
    'as' => 'submission.pick'
]);
get('submission/create/{type}', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@create',
    'as' => 'submission.create'
]);
get('submission/edit/{id}', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@modify',
    'as' => 'submission.edit'
]);
post('submission/edit/{id}', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@edit',
    'as' => 'submission.edit.post'
]);
get('submission/{id}', [
    'uses' => 'SubmissionController@show',
    'as' => 'submission'
]);
get('submission/{id}/play', [
    'uses' => 'SubmissionController@play',
    'as' => 'submission.play'
]);
get('submission/{id}/rate', [
    'middleware' => 'auth',
    'uses' => 'SubmissionController@rate',
    'as' => 'submission.rate'
]);
get('submission/{id}/delete', [
    'uses' => 'SubmissionController@destroy',
    'as' => 'submission.delete'
]);

get('admin', [
    'middleware' => 'admin',
    'uses' => 'AdminController@index',
    'as' => 'admin.dashboard'
]);
get('admin/moderate', [
    'middleware' => 'admin',
    'uses' => 'AdminController@moderate',
    'as' => 'admin.moderate'
]);
get('admin/approve/{id}', [
    'middleware' => 'admin',
    'uses' => 'AdminController@approve',
    'as' => 'admin.approve'
]);
post('admin/deny/{id}', [
    'middleware' => 'admin',
    'uses' => 'AdminController@deny',
    'as' => 'admin.deny'
]);
get('admin/suspend/{id}', [
    'middleware' => 'admin',
    'uses' => 'AdminController@suspend',
    'as' => 'admin.suspend'
]);