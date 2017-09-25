<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use Dingo\Api\Routing\Helpers;

class ApiController extends Controller
{
    use Helpers;
    
    protected $admin = false;
    
    /**
     * statuscode
     * @var integer
     */
    protected $statuscode = 200;

    /**
     * getStatusCode
     * @return
     */
    public function getStatusCode()
    {
        return $this->statuscode;
    }

    /**
     * setStatusCode
     * @param $value
     */
    public function setStatusCode(int $value)
    {
        $this->statuscode = $value;

        return $this;
    }

    /**
     * responseNotFound Forbidden
     * @param  $message
     * @return
     */
    public function respondForbidden(string $message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /**
     * responseNotFound
     * @param  $message
     * @return
     */
    public function respondNotFound(string $message = 'Not found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * [responsePreConditionFailed description]
     * @param   $message
     * @return
     */
    public function respondPreConditionFailed($message = 'Precondition failed')
    {
        return $this->setStatusCode(201)->respondWithError($message);
    }

    /**
     * @param  $data
     * @param  $headers
     * @return
     */
    public function respond($data, $headers=[])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param  $message
     * @return
     */
    public function respondSuccess(string $message)
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
     * @param  $message
     * @return
     */
    public function respondWithError(string $message)
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
