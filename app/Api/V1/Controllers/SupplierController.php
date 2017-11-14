<?php

namespace App\Api\V1\Controllers;

use App\Http\Requests;
use JWTAuth;
use Validator;
use Cache;
use Log;
use Config;
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
            $suppliers = Supplier::Active()
                        ->whereHas('traits', function ($query) {
                                $query->where('sup_trait', '=', Config::get('ticket.supplier_trait'));
                            })
                        ->get()
                        ->toArray();
        } elseif ($this->user->getRoleId()) {
            $suppliers = Supplier::Active()->Where('supplier', $this->user->getRoleId())->get()->toArray();
        } else {
            return $this->respondNotFound('Supplier Not Found');
        }
        // dd(DB::getQueryLog());
        $data = $this->supplierTransformer->transformCollection($suppliers, false);

        Log::info('Supplier list retrieved by user  : '.$this->user->email);

        return  $this->respond(['data' => $data]);
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
                $data = $this->supplierTransformer->transformCollection($suppliers);
            } else {
                $data = [];
            }

            Log::info('supplier search by user  : '.$this->user->email);

            return  $this->respond(['data' => $data]);
        } else {
            return $this->respondForbidden('Forbidden from performing this action');
        }
    }
}
