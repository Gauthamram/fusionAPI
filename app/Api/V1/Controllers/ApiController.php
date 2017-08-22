<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use App\Fusion\Traits\UserSettingTrait;
use App\Fusion\Interfaces\iUserSetting;

class ApiController extends Controller implements iUserSetting
{
    use UserSettingTrait;
    protected $admin = false;
    
	/**
     * [$statuscode description]
     * @var integer
     */
    protected $statuscode = 200;

    /**
     * [getStatusCode description]
     * @return [integer] 
     */
    public function getStatusCode()
    {
    	return $this->statuscode;

    }

    /**
     * [setStatusCode description]
     * @param [integer] $value
     */
    public function setStatusCode($value)
    {
    	$this->statuscode = $value;

    	return $this;
    }

    /**
     * [responseNotFound Forbidden]
     * @param  string $message 
     * @return [type]          
     */
    public function respondForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /**
     * [responseNotFound description]
     * @param  string $message 
     * @return [type]          
     */
    public function respondNotFound($message = 'Not found')
    {
    	return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * [responsePreConditionFailed description]
     * @param  string $message 
     * @return [type]
     */
    public function respondPreConditionFailed($message = 'Precondition failed')
    {
        return $this->setStatusCode(201)->respondWithError($message);
    }

    /**
     * @param  array
     * @param  array
     * @return json
     */
    public function respond($data, $headers=[])
    {
    	return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param  string
     * @return object
     */
    public function respondSuccess($message)
    {
        return $this->respond([
            'data' => [
                'status' => 'success',
                'message' => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }
        /**
     * @param  string
     * @return object
     */
    public function respondWithError($message)
    {
    	return $this->respond([
    		'data' => [
                'status' => 'error',
    			'message' => $message,
    			'status_code' => $this->getStatusCode()
    		]
    	]);
    }
}
