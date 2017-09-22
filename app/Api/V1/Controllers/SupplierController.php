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
use Dingo\Api\Exception\ValidationHttpException;
use App\Fusion\Transformers\SupplierTransformer;

class SupplierController extends ApiController
{
    use Helpers;
    /**
     * [__constructor]
     */
    public function __construct(supplierTransformer $supplierTransformer)
    {
        $this->supplierTransformer = $supplierTransformer;
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->labelHelper = new LabelHelper();
    }

    public function index()
    {
        // DB::enableQueryLog();
        if ($this->user->isAdmin()) {
            $suppliers = Supplier::Active()->get()->toArray();
        } elseif ($this->user->getRoleId()) {
            $suppliers = Supplier::Active()->Where('supplier', $this->user->getRoleId())->get()->toArray();
        } else {
            return $this->respondNotFound('Supplier Not Found');
        }
        // dd(DB::getQueryLog());
        $data = $this->supplierTransformer->transformCollection($suppliers);

        Log::info('Supplier list retrieved by user  : '.$this->user->email);

        return  $this->respond(['data' => $data]);
    }

    /**
     *
     * [supplier details]
     * @param  [integer] $supplier [description]
     * @return [type]           [description]
     */
    public function supplier($supplier, $type = 'Tickets')
    {
        if (($supplier == $this->user->getRoleId()) || ($this->user->isAdmin()) || ($this->user->isWarehouse())) {
            if ($this->labelHelper->supplierCheck($supplier)) {
                $response = $this->labelHelper->OrderSupplier($supplier, $type);

                Log::info('Supplier retrieved by user  : '.$this->user->email);

                return $this->respond(['data' => $response]);
            } else {
                return $this->respondNotFound('Supplier Not Found');
            }
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }

    public function search($term)
    {
        if ($this->user->isAdmin()) {
            $supplier = new Supplier();
            $suppliers = $supplier::Active()->Search($term)->get(['supplier', 'sup_name','contact_email','contact_name','contact_phone']);
            $data = array();
            if (count($suppliers) > 0) {
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

            Log::info('supplier search by user  : '.$this->user->email);

            return  $this->respond(['data' => $data]);
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }
}
