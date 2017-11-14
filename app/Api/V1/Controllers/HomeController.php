<?php

namespace App\Api\V1\Controllers;

use Artisan;
use App\Http\Controllers\Controller;

class HomeController extends ApiController
{
   public function showroutes() {
    	$routes = Artisan::call('route:list', ['--path' => 'api']);
    	dd($routes);
    	// return view('your_view', compact('routes'));  
	}
}
