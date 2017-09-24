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

Route::get('/', function () {
    return redirect('/portal/dashboard');
});

Route::get('portal/', function () {
    return redirect('/portal/dashboard');
});

Route::group(['prefix' => 'portal','middleware' => 'auth.portal'], function () {
    Route::get('/dashboard', 'HomeController@dashboard');
    
    Route::get('/setting', 'HomeController@setting');
    Route::post('/setting', 'HomeController@setting');
    
    Route::post('/label/create/ticket', 'LabelController@createticket');
    Route::get('/label/order/{order?}', 'OrderController@orderdetails');

    Route::get('/label/orders', 'OrderController@orderlist');
    Route::post('/label/orders/search', 'OrderController@orderlist');
    
    Route::get('/label/carton/search', 'LabelController@search');
    Route::post('/label/carton/search', 'LabelController@search');
    
    Route::get('/label/history', 'LabelController@history');
    Route::get('/label/reprint/{order_no}', 'LabelController@reprint');
    Route::get('/label/print/cartons/{order?}', 'LabelController@printcartons');
    Route::get('/label/print/stickies/{order?}', 'LabelController@printstickies');
    Route::get('/label/print/{cartontype}/{order_no}/{item}', 'LabelController@printcartontype');

    Route::get('/users', 'UserController@users');
    
    Route::get('/user/new', 'UserController@create');
    Route::post('/user/new', 'UserController@create');
    
    Route::post('/users/search', 'UserController@search');
    
    Route::get('/user/recovery/{id?}', 'UserController@recovery');
    Route::post('/user/recovery', 'UserController@recovery');
    
    Route::get('/user/logout', 'UserController@logout');

    Route::get('/suppliers', 'SupplierController@index');
    Route::post('/suppliers/search', 'SupplierController@search');
});

Route::post('login', 'AuthenticateController@login');
Route::get('login', 'AuthenticateController@login');

Route::get('reset_password/{token}', 'UserController@reset')->name('password.reset');
Route::post('reset_password/{token}', 'UserController@reset');
