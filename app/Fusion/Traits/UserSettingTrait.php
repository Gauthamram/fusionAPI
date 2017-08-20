<?php
namespace App\Fusion\Traits;
use App\Fusion\userSetting;
trait UserSettingTrait {
	/**
	 * set usersetting from userSetting object
	 */
	public function setUserSetting(){
		$userSetting = New UserSetting();
		$this->supplierid = $userSetting->getSupplierId();
	    $this->admin = $userSetting->isAdmin();
	    $this->warehouse = $userSetting->isWarehouse();	
	}
	
}