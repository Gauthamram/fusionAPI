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

Route::get('/', function() {
    return redirect('/portal/dashboard');
});

Route::get('portal/', function() {
    return redirect('/portal/dashboard');
});

Route::group(['prefix' => 'portal','middleware' => 'auth.portal'], function () {
    Route::get('/dashboard','HomeController@dashboard');
    
    Route::get('/setting','HomeController@setting');
    Route::post('/setting','HomeController@setting');
    
    Route::post('/label/create/ticket','LabelController@createticket');
    Route::get('/label/order/{order?}','OrderController@orderdetails');

    Route::get('/label/orders','OrderController@orderlist');
    Route::post('/label/orders/search','OrderController@orderlist');
    
    Route::get('/label/carton/search','LabelController@search');
    Route::post('/label/carton/search','LabelController@search');
    
    Route::get('/label/history','LabelController@history');
    Route::get('/label/reprint/{order_no}','LabelController@reprint');
    Route::get('/label/print/cartons/{order?}','LabelController@printcartons');
    Route::get('/label/print/stickies/{order?}','LabelController@printstickies');
    Route::get('/label/print/{cartontype}/{order_no}/{item}','LabelController@printcartontype');

    Route::get('/users','UserController@users');
    
    Route::get('/user/new','UserController@create');
    Route::post('/user/new','UserController@create');
    
    Route::post('/users/search','UserController@search');
    
    Route::get('/user/recovery/{id?}','UserController@recovery');
    Route::post('/user/recovery','UserController@recovery');
    
    Route::get('/user/logout','UserController@logout');

    Route::get('/suppliers','SupplierController@index');
    Route::post('/suppliers/search','SupplierController@search');
});

Route::post('login','AuthenticateController@login');
Route::get('login','AuthenticateController@login');

route::get('barcodes', function(){
    echo "C39 <br/>";
    echo DNS1D::getBarcodeHTML("4445645656", "C39"); echo "<br/>"; echo "C39+ <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C39+"); echo "<br/>";echo "C39E <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C39E"); echo "<br/>";echo "C39E+ <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C39E+"); echo "<br/>";echo "C93 <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C93"); echo "<br/>";echo "S25 <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "S25"); echo "<br/>";echo "S25+ <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "S25+"); echo "<br/>";echo "I25 <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "I25"); echo "<br/>";echo "I25+ <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "I25+"); echo "<br/>";echo "C128 <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C128"); echo "<br/>";echo "C128A <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C128A"); echo "<br/>";echo "C128B <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C128B"); echo "<br/>";echo "C128C <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "C128C"); echo "<br/>";echo "EAN2 <br/>";
echo DNS1D::getBarcodeHTML("44455656", "EAN2"); echo "<br/>";echo "EAN5 <br/>";
echo DNS1D::getBarcodeHTML("4445656", "EAN5"); echo "<br/>";echo "EAN8 <br/>";
echo DNS1D::getBarcodeHTML("4445", "EAN8"); echo "<br/>";echo "EAN13 <br/>";
echo DNS1D::getBarcodeHTML("4445", "EAN13"); echo "<br/>";echo "UPCA <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "UPCA"); echo "<br/>";echo "UPCE <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "UPCE"); echo "<br/>";echo "MSI <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "MSI"); echo "<br/>";echo "MSI+ <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "MSI+"); echo "<br/>";echo "POSTNET <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "POSTNET"); echo "<br/>";echo "PLANET <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "PLANET"); echo "<br/>";echo "RMS4CC <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "RMS4CC"); echo "<br/>";echo "KIX <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "KIX"); echo "<br/>";echo "IMB <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "IMB"); echo "<br/>";echo "CODABAR <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "CODABAR"); echo "<br/>";echo "CODE11 <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "CODE11"); echo "<br/>";echo "PHARMA <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "PHARMA"); echo "<br/>";echo "PHARMA2T <br/>";
echo DNS1D::getBarcodeHTML("4445645656", "PHARMA2T"); echo "<br/>";
});

Route::get('reset_password/{token}','UserController@reset')->name('password.reset');
Route::post('reset_password/{token}','UserController@reset');