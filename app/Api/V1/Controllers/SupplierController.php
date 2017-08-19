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
	protected $admin = false;
	/**
	 * [__constructor]
	 */
	public function __construct(supplierTransformer $supplierTransformer,userSetting $userSetting)
	{
		$this->userSetting = $userSetting->getuserSetting();
        $this->labelHelper = New LabelHelper($userSetting);
        $this->admin = $userSetting->isAdmin();
        $this->warehouse = $userSetting->isWarehouse();
        $this->supplierTransformer = $supplierTransformer;
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

	/**
	 * [edit supplier]
	 * @param  [type] $supplierid [description]
	 * @return [type]             [description]
	 */
    public function edit(Request $request, $supplierid, $type = 'Tickets')
    {
    	if ($request->isMethod('post')) {
    		$validator = Validator::make($request->all(), [
	            'contact_name' => 'required',
	            'contact_phone' => 'required|integer',
	            'contact_email' => 'required|email',
	            'contact_fax' => 'required|nullable',
	            'address1' => 'required',
	            'address2' => 'required|nullable',
	            'post'		=> 'required|integer',
	            'city'		=> 'required'	
        	]);

	        if($validator->fails()) {
	            throw new ValidationHttpException($validator->errors()->all());
	        }

    		if (($supplierid == $this->userSetting['number']) || ($this->admin)) {
    			$addressTypes = config('supplier.address.type');
    			$supplier = Supplier::findOrFail($supplierid);
    			$sup_address = $supplier->addressType($addressTypes[$type])->get();
    			
    			//get address model
    			$address = Address::findOrFail($sup_address->first()->addr_key);

    			$supplier->sup_name = $request->name;
    			$update = $supplier->save();

    			if ($update) {
    				$address->contact_name = $request->contact_name;
    				$address->contact_phone = $request->contact_phone;
    				$address->contact_fax = $request->contact_fax;
    				$address->contact_email = $request->contact_email;
    				$address->add_1 = $request->address1;
    				$address->add_2 = $request->address2;
    				$address->add_3 = $request->address3;
    				$address->post = $request->post;
    				$address->city = $request->city;
    				//$address->state = $request->state;

    				try {
    					$address->save();
    					return $this->respondSuccess('Update Successfull');
    				} catch (Exception $e) {
    					return $this->repondWithError('Address could not be updated at this moment. Please try again later.');
    				}
    			}
    		} else {
	            return $this->respondForbidden('Forbidden from performing this action');
	        }
    	} else {
    		if ($supplierid == $this->userSetting['number']) {
    			if($this->labelHelper->supplierCheck($supplierid)){
                	$response = $this->labelHelper->OrderSupplier($supplierid, $type);
                	return $this->respond(['data' => $response]);
            	} else {
            		return $this->respondNotFound('Supplier Not Found');
            	}
	        } else {
	            return $this->respondForbidden('Forbidden from performing this action');
	        }
    	}
    	
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
