<?php

namespace App\Api\V1\Controllers;

use App\Http\Requests;
use JWTAuth;
use Validator;
use Cache;
use Log;
use Carbon\Carbon;
use App\Supplier;
use App\Address;
use Illuminate\Http\Request;
use App\Helpers\LabelHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Dingo\Api\Exception\ValidationHttpException;
use App\Fusion\Transformers\SupplierTransformer;

class SupplierController extends ApiController
{
    /**
     * [__constructor]
     */
    public function __construct(supplierTransformer $supplierTransformer)
    {
        $this->supplierTransformer = $supplierTransformer;
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->labelHelper = new LabelHelper();
    }

    /**
     * Returns list of suppliers
     * @return
     */
    public function index()
    {
        // DB::enableQueryLog();
        if ($this->user->isAdmin()) {
            $suppliers = Supplier::Active()->paginate(10)->toArray();
        } elseif ($this->user->getRoleId()) {
            $suppliers = Supplier::Active()->paginate()->Where('supplier', $this->user->getRoleId())->get()->toArray();
        } else {
            return $this->respondNotFound('Supplier Not Found');
        }
        // dd(DB::getQueryLog());
        $data = $this->supplierTransformer->transformCollection($suppliers, true);

        Log::info('Supplier list retrieved by user  : '.$this->user->email);

        return  $this->respond(['data' => $data]);
    }

    /**
     *
     * Returns supplier details
     * @param $supplier
     * @return
     */
    public function supplier(int $supplier, $type = 'Tickets')
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

    /**
     * Returns suppier list based on search term
     * @param  $term
     * @return
     */
    public function search($term)
    {
        if ($this->user->isAdmin()) {
            $supplier = new Supplier();
            $suppliers = $supplier::Active()->Search($term)->get(['supplier', 'sup_name','contact_email','contact_name','contact_phone'])->toArray();
            
            if (count($suppliers) > 0) {
                $data = $this->supplierTransformer->transformCollection($suppliers, false);
            } else {
                $data = ['data'];
            }

            Log::info('supplier search by user  : '.$this->user->email);

            return  $this->respond(['data' => $data]);
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }
}
