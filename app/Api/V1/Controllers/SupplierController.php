<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use JWTAuth;
use Validator;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Cache;
use Carbon\Carbon;
use App\Supplier;
use App\Address;
use App\Helpers\LabelHelper;
use App\Fusion\UserSetting;
use Dingo\Api\Exception\ValidationHttpException;
use App\Fusion\Transformers\SupplierTransformer;

class SupplierController extends ApiController
{
	use Helpers;
	/**
	 * [__constructor]
	 */
	public function __construct(supplierTransformer $supplierTransformer,userSetting $userSetting)
	{
        $this->supplierTransformer = $supplierTransformer;
        $this->setUserSetting($userSetting);
	}

    public function index()
    {
        // DB::enableQueryLog();
        if ($this->admin) {
            $suppliers = Supplier::Active()->get()->toArray();
        } elseif ($this->userSetting['number']) {
            $suppliers = Supplier::Active()->Where('supplier',$this->userSetting['number'])->get()->toArray();
        } else {
            return $this->respondNotFound('Supplier Not Found');
        }
// dd(DB::getQueryLog());
        $data = $this->supplierTransformer->transformCollection($suppliers);
        return  $this->respond(['data' => $data]);
    }

    public function search($term)
    {
        if($this->admin) {
            $supplier = New Supplier();
            $suppliers = $supplier::Active()->Search($term)->get(['supplier', 'sup_name','contact_email','contact_name','contact_phone']);
            $data = array();
            if(count($suppliers) > 0){
                foreach ($suppliers as $supplier) {
                    $data[] = array(
                        'id' => $supplier->supplier,
                        'name' => $supplier->sup_name,
                        'email' => $supplier->contact_email,
                        'contact' => $supplier->contact_name,
                        'phone' => $supplier->contact_phone
                        );
                }
            } 
            return  $this->respond(['data' => $data]);
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }
}
