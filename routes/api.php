<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('telegram', 'TelegramController@receive');

Route::post('amo.php', 'AmoController@receive');
Route::post('amo-new-lead', 'AmoController@newLeadManager');
Route::post('amo-new-lead-bot', 'AmoController@newLeadBot');
Route::post('amo-completed-lead', 'AmoController@completedLead');