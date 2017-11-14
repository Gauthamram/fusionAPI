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
        if ($request->isMethod('post')) {
        } else {
            if (($this->currentuser->isAdmin()) && (!$id)) {
                $users = User::all()->toArray();
                $data = $this->userTransformer->transformCollection($users, false);
            } elseif ($this->currentuser->isAdmin() || (($id) && ($this->currentuser->id == $id))) {
                //$id = $this->currentuser->id;
                $users = User::findOrFail($id)->toArray();
                $data = $this->userTransformer->transformCollection([$users], false);
            } else {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        }
        return $this->respond(['data' => $data]);
    }
}
