<?php

namespace App\Api\V1\Controllers;

use Cache;
use Config;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\ApiSetting;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Fusion\Transformers\TicketTransformer;
use Dingo\Api\Exception\ValidationHttpException;

class UserSettingController extends ApiController
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request, $id='')
    {
        if ($request->isMethod('post')) {
        } else {
            if (($this->user->isAdmin()) && (!$id)) {
                $users = User::all()->toArray();
            } elseif ((($this->admin) && ($id)) || ($this->user->id == $id)) {
                $id = $this->user->id;
                $users = User::findOrFail($id)->get()->toArray();
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        }
        $data = $users;
        return $this->respond(['data' => $data]);
    }
}
