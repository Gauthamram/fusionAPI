<?php

namespace app\Fusion;

use ApiSetting;
use JWTAuth;
use Cache;
use Carbon\Carbon;
use Zizaco\Entrust\EntrustFacade as Entrust;

class UserSetting 
{
	protected $user_setting;
	
	public function __construct()
	{
		$this->currentuser = JWTAuth::parseToken()->authenticate();
	}

    /**
     * Gets the value of userSetting.
     *
     * @return mixed
     */
    public function getUsersetting()
    {
        $settings = Cache::remember("'".$this->currentuser->id."-userSetting'", Carbon::now()->addMinutes(60), function() {
            return $this->currentuser->apiSettings()->get();
        });
        foreach ($settings as $setting) {
            $user_setting[$setting->keys] = $setting->val;
        }
        
        return $this->user_setting;
    }

    public function getSupplierId()
    {
        return $this->getUsersetting()['number'];
    }

    /**
     * Sets the value of userSetting.
     *
     * @param mixed $userSetting the userSetting
     *
     * @return self
     */
    protected function setUsersetting($user_setting)
    {
        $this->usersetting = $user_setting;

        return $this;
    }

    public function isAdmin()
    {
    	return Entrust::hasRole('admin');
    }

    public function isWarehouse()
    {
        return Entrust::hasRole('warehouse');
    }
}