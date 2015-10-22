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

//Home related routes
Route::get(
    '/', ['as' => 'home', function () {
        return response()->view('index');
    }]
);

//Conference related routes
Route::get(
    '/conference',
    ['uses' => 'ConferenceController@index', 'as' => 'conference-index']
);
Route::get(
    '/conference/join',
    ['uses' => 'ConferenceController@showJoin', 'as' => 'conference-join']
);
Route::get(
    '/conference/connect',
    ['uses' => 'ConferenceController@showConnect', 'as' => 'conference-connect']
);

//Broadcast related routes
Route::get(
    '/broadcast',
    ['uses' => 'BroadcastController@index', 'as' => 'broadcast-index']
);

Route::post(
    '/broadcast/send',
    ['uses' => 'BroadcastController@send', 'as' => 'broadcast-send']
);

Route::post(
    '/broadcast/play',
    ['uses' => 'BroadcastController@showPlay', 'as' => 'broadcast-play']
);

//Recording related routes
Route::get(
    '/recordings',
    ['uses' => 'RecordingController@index', 'as' => 'recording-index']
);

Route::post(
    '/recording/create',
    ['uses' => 'RecordingController@create', 'as' => 'recording-create']
);

Route::post(
    '/recording/record',
    ['uses' => 'RecordingController@showRecord', 'as' => 'recording-record']
);

Route::get(
    '/recording/hangup',
    ['uses' => 'RecordingController@showHangup', 'as' => 'recording-hangup']
);
