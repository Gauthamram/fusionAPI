<?php
namespace App\Fusion\Traits;

trait UserSetting {
	/**
	 * get usersetting from userSetting object
	 */
	public function getUserSetting(){
		$this->userSetting = $userSetting->getuserSetting();
	    $this->admin = $userSetting->isAdmin();
	    $this->warehouse = $userSetting->isWarehouse();	
	}
	
}