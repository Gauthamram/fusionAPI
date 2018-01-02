<?php

$api = app('Dingo\Api\Routing\Router');

//include middleware only if it is not test
$middleware = [];

if (!App::runningUnitTests()) {
    $middleware[] = 'api';
}

$api->version('v1', function ($api) use ($middleware) {
    $api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
    $api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');
    // $api->get('label/order/{orderno}', 'App\Api\V1\Controllers\LabelController@create');
    
    //protected routes and throttled
    $api->group(['middleware' => $middleware], function ($api) {
        $api->get('orders/{status?}', 'App\Api\V1\Controllers\OrderController@index');
        $api->get('order/{order_no}', 'App\Api\V1\Controllers\OrderController@order');
        $api->get('order/details/{order_no}', 'App\Api\V1\Controllers\OrderController@orderdetails');
        $api->get('order/{order_no}/cartonpack/{item_number?}', 'App\Api\V1\Controllers\OrderController@cartonpack');
        $api->get('order/{order_no}/cartonloose/{item_number?}', 'App\Api\V1\Controllers\OrderController@cartonloose');
        $api->get('order/{order_no}/ratiopack', 'App\Api\V1\Controllers\OrderController@ratiopack');
        $api->get('order/{order_no}/simplepack', 'App\Api\V1\Controllers\OrderController@simplepack');
        $api->get('order/{order_no}/looseitem', 'App\Api\V1\Controllers\OrderController@looseitem');
        
        //this is only to get listing of carton details for the order - it will return all the list from the databse - not label data
        $api->post('order/cartonpack', 'App\Api\V1\Controllers\OrderController@cartonpack');
        $api->post('order/cartonloose', 'App\Api\V1\Controllers\OrderController@cartonloose');

        $api->post('ticket/create/tips/request', 'App\Api\V1\Controllers\TicketController@create');
        $api->get('tickets', 'App\Api\V1\Controllers\TicketController@index');
        $api->get('ticket/tips/printed', 'App\Api\V1\Controllers\TicketController@printed');
        $api->get('ticket/tips/{order_no}/{item_number}', 'App\Api\V1\Controllers\TicketController@tipsticketdata');
        // $api->post('ticket/create/printed','App\Api\V1\Controllers\TicketController@create_tickets_printed');
        // $api->post('ticket/create/tips/printed','App\Api\V1\Controllers\TicketController@create_tickets_tips_printed');

        $api->get('users/{id?}', 'App\Api\V1\Controllers\UserSettingController@index');
        $api->post('users/{id}', 'App\Api\V1\Controllers\UserSettingController@index');
        
        //Password reset and recovery
        $api->get('auth/user', 'App\Api\V1\Controllers\AuthController@user');

        // $api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');

        $api->get('suppliers', 'App\Api\V1\Controllers\SupplierController@index');
        $api->get('supplier/search/{term}', 'App\Api\V1\Controllers\SupplierController@search');
    });

    // example of protected route
    // $api->get('protected', ['middleware' => ['api.auth'], function () {
    //     return \App\User::all();
    // }]);
    $api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
    // example of free route
    	$api->get('free', function () {
        	return \App\User::all();
    	});

    // $api->get('reset_password/{token}',['as' => 'password.reset', 'uses' => 'App\Api\V1\Controllers\AuthController@reset']);
    //recover using email first before reset
    $api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
});
