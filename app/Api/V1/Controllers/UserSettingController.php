<?php

namespace App\Api\V1\Controllers;

use Cache;
use Config;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Fusion\Transformers\UserTransformer;
use Dingo\Api\Exception\ValidationHttpException;

class UserSettingController extends ApiController
{
    public function __construct(UserTransformer $userTransformer)
    {
        $this->currentuser = JWTAuth::parseToken()->authenticate();
        $this->userTransformer = $userTransformer;
    }

    public function index(Request $request, $id='')
    {
        if (($id) || ($this->currentuser->id == $id)) {
            $user = User::findOrFail($id)->toArray();
            dd($user);
            $data = $this->userTransformer->transformCollection($users, false);
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
        
        return $this->respond(['data' => $data]);
    }
}
