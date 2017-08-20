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
    	$this->currentuser = JWTAuth::parseToken()->authenticate();
    	$this->setUserSetting();
    }

    public function index(Request $request,$id='')
    {
        if($request->isMethod('post')){

        } else {
            if (($this->admin) && (!$id)){
                $users = User::all()->toArray();
            } elseif ((($this->admin) && ($id)) || ($this->currentuser->id == $id)) {
                $id = $this->currentuser->id;
                $users = User::findOrFail($id)->get()->toArray();
            } else  {
                return $this->respondForbidden('Forbidden from performing this action');
            }
        }     
        $data = array_map([$this, "transformSettingsToArray"], $users);
        return $this->respond(['data' => $data]);
    }

    public function transformSettingsToArray($user)
    {
    	$data = $user;
		$settings = User::findOrFail($user['id'])->apiSettings()->get();
		foreach ($settings as $setting) {
			$data[$setting->keys] = $setting->val;
		}
		return $data;
    }
}
