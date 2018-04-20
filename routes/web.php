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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function()
{
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/customers', 'CustomersController@index')->name('customers');
    Route::get('/customers/list', 'CustomersController@indexList')->name('customers-list');
    Route::get('/customers/vip', 'CustomersController@indexVip')->name('vip');
    Route::get('/customers/list-data', 'CustomersController@getListData')->name('customers-list-data');
    Route::get('/customers/vip/list-data', 'CustomersController@getVipListData')->name('customers-vip-list-data');
    Route::get('/customers/get-customer-details/{id}', 'CustomersController@getCustomerDetails')->name('customers-get-details');
    Route::get('/customers/get-vip-details/{id}', 'CustomersController@getVipDetails')->name('vip-get-details');
    Route::post('/customers/save-vip', 'CustomersController@saveVip')->name('save-vip');
    Route::post('/customers/get-vip-till', 'CustomersController@getVipTill')->name('get-vip-till');
    Route::post('/customers/cancel-vip/{id}', 'CustomersController@cancelVip')->name('cancel-vip');
    Route::resource('users', 'UsersController');
    Route::resource('scheduled-messages', 'ScheduledMessagesController');
    Route::get('/scheduled-messages/get-condition-edit-html/{id}', 'ScheduledMessagesController@getConditionEditHtml')->name('get-condition-edit-html');
    Route::get('/scheduled-messages/{id}/preview', 'ScheduledMessagesController@preview')->name('message-preview');
    Route::resource('flows', 'FlowsController');
    Route::get('/flows/{id}/preview', 'FlowsController@previewFlow')->name('preview-flow');
    Route::post('/flows/{id}/preview/save-position/{answerId}', 'FlowsController@previewSavePosition')->name('preview-save-position');
    Route::post('/flows/{id}/preview/remove-button-target/{buttonId}', 'FlowsController@previewRemoveButtonTarget')->name('preview-remove-button-target');
    Route::post('/flows/{id}/preview/add-button-target/{buttonId}/{answerId}', 'FlowsController@previewAddButtonTarget')->name('preview-add-button-target');
    Route::post('/flows/{id}/preview/add-answer', 'FlowsController@previewAddAnswer')->name('preview-add-answer');
    Route::post('/flows/{id}/preview/remove-answer/{answerId}', 'FlowsController@previewRemoveAnswer')->name('preview-remove-answer');
    Route::post('/flows/{id}/preview/edit-answer-form/{answerId}', 'FlowsController@previewEditAnswerForm')->name('preview-edit-answer-form');
    Route::post('/flows/{id}/preview/update-answer', 'FlowsController@previewUpdateAnswer')->name('preview-update-answer');
    Route::resource('flows-answers', 'FlowsAnswersController');
    Route::resource('flows-answers-messages', 'FlowsAnswersMessagesController');
    Route::resource('flows-buttons', 'FlowsButtonsController');


    //Route::get('/migration20180328', 'TestController@migration20180328')->name('migration20180328');
    //Route::get('/migration20180403', 'TestController@migration20180403')->name('migration20180403');
    //Route::get('/linkTelegramVipContacts20180409', 'TestController@linkTelegramVipContacts20180409')->name('linkTelegramVipContacts20180409');
    //Route::get('/linkTelegramVipLeads20180409', 'TestController@linkTelegramVipLeads20180409')->name('linkTelegramVipLeads20180409');
});

Route::get('/test', 'TestController@test')->name('test');
Auth::routes();

