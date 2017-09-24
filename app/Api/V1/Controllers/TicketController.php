<?php

namespace App\Api\V1\Controllers;

use Log;
use Cache;
use Config;
use JWTAuth;
use Validator;
use App\Supplier;
use Carbon\Carbon;
use App\TicketPrinted;
use App\TicketRequest;
use App\TipsTicketPrinted;
use Illuminate\Http\Request;
use App\Helpers\TicketHelper;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use App\Fusion\Transformers\TicketTransformer;
use App\Fusion\Transformers\TicketPrintedTransformer;
use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Support\Facades\DB;

class TicketController extends ApiController
{
    protected $ticketTransformer;

    /**
     * [__construct]
     * @param ticketTransformer
     * @param ticketPrintedTransformer
     */
    public function __construct(ticketTransformer $ticketTransformer, ticketPrintedTransformer $ticketPrintedTransformer)
    {
        $this->ticketTransformer = $ticketTransformer;
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->ticketHelper = new ticketHelper();
        $this->ticketPrintedTransformer = $ticketPrintedTransformer;
    }

    /**
     * [list all ticket request]
     * @param  Request
     * @return
     */
    public function index(Request $request)
    {
        $data = TicketRequest::all()->toArray();
        $data = $this->ticketTransformer->transformCollection($data);
        return $this->respond(['data' => $data]);
    }

    /**
     * [printed - tickets printed based on user, change the tables]
     * warehouse user - cgl_tickets_printed
     * other users - cgl_tickets_tips_printed
     * @return
     */
    public function printed()
    {
        // DB::enableQueryLog();
        if ($this->user->isWarehouse()) {
            $ticket = new TipsTicketPrinted();
        } else {
            $ticket = new TicketPrinted();
        }
        $supplier = new Supplier();
        

        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $tickets = $ticket->take(10)->get()->toArray();
        } else {
            $tickets = Cache::remember("'".$this->supplierid."-tickets", Carbon::now()->addMinutes(60), function () {
                return Supplier::findOrFail($this->supplierid)->tickets()->OrderBy('createdate', 'asc')->take(10)->get()->toArray();
            });
        }

        Log::info('Ticket Printed data retrieved by user  : '.$this->user->email);
        
        $data = $this->ticketPrintedTransformer->transformCollection($tickets);
        return $this->respond(['data' => $data]);
    }

    /**
     * [create ticket printed]
     * @param  Request
     * @return
     */
    // public function create_tickets_printed($data)
    // {
    //  $validator = Validator::make($request->all(), [
    //         'orderno' => 'required|integer',
    //         'sticky' => 'required|integer|nullable',
    //         'swing' => 'required|integer|nullable',
    //         'packcartons' => 'required|integer|nullable',
    //         'loosesimplecartons' => 'required|integer|nullable',
    //         'mixedcartons' => 'required|integer|nullable'
    //     ]);

    //     if($validator->fails()) {
    //         throw new ValidationHttpException($validator->errors()->all());
    //     }

    //     TicketsPrinted::unguard();
    //     $id = TicketsPrinted::max('ticketrequestid');
    //     $ticket = New TicketsPrinted();
    //     $ticket->ticketrequestid = $id + 1;
    //     $ticket->order_no = $request->orderno;
    //     $ticket->createdate = Carbon::now();
    //     $ticket->filename = $request->has('filename') ? $request->filename : Config::get('ticket.filename');
    //     $ticket->reprint_required = Config::get('ticket.reprint');
    //     $ticket->sticky = $request->sticky;
    //     $ticket->swing = $request->swing;
    //     $ticket->packcartons = $request->packcartons;
    //     $ticket->loosesimplecartons = $request->loosesimplecartons;
    //     $ticket->mixedcartons = $request->mixedcartons;
    //     $insert = $ticket->save();
    //     $insertedId = $ticket->ticketrequestid;
    //     TicketsPrinted::reguard();
        
    //     if ($insert){
    //      return $this->respondSuccess('Update Successfull');
    //     } else {
    //      return $this->repondWithError('Ticket could not be create at this moment. Please try again later.');
    //     }
    // }

    /**
     * [create ticketrequest]
     * @param  Request
     * @return ticket object
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item' => 'required|integer',
            'qty'  => 'required|integer',
            'ticket_type' => 'required',
            'location_type' => 'required',
            'location' => 'required',
            'order_no' => 'required|integer',
            'retail' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ]);

        $currentuser = JWTAuth::parseToken()->authenticate();

        if ($validator->fails()) {
            dd($validator->errors()->all());
            throw new ValidationHttpException($validator->errors()->all());
        }
        
        TicketRequest::unguard();
        // $id = TicketRequest::max('ticketrequestid');
        $ticket = new TicketRequest();
        $ticket->ticket_type_id = Config::get("ticket.type.".$request->ticket_type);
        $ticket->item = $request->item;
        $ticket->qty = $request->qty + $request->over_print_qty;
        $ticket->loc_type = $request->location_type;
        $ticket->location = $request->location;
        $ticket->multi_units = Config::get('ticket.request_default.multi_units');
        $ticket->multi_unit_retail = Config::get('ticket.request_default.multi_unit_retail');
        $ticket->unit_retail = $request->retail;
        $ticket->country_of_origin = $request->country ? $request->country : '';
        $ticket->order_no = $request->order_no;
        $ticket->create_datetime = Carbon::now();
        $ticket->last_update_datetime = Carbon::now();
        $ticket->last_update_id = $currentuser['email'];
        $ticket->sort_order_type = $request->sort_order_type;
        $ticket->printer_type = $request->printer;
        $ticket->print_online_ind = Config::get('ticket.request_default.print_online_ind');
        $insert = $ticket->save();
        TicketRequest::reguard();
        
        Log::info('Ticket Request created by user  : '.$this->user->email);

        if ($insert) {
            return $this->respond(['data' => $ticket]);
        } else {
            return $this->repondWithError('Ticket could not be create at this moment. Please try again later.');
        }
    }

    //get data for label with order no and item_number
    public function tipsticketdata(Request $request, $order_no, $item_number)
    {
        //get label data
        $data = $this->ticketHelper->TipsTicketData($order_no, $item_number);

        //tips ticket printed creation
        Log::info('Tickets Data retrieved by user : '.$this->user->email);
        return $this->respond(['data' => $data]);
    }
}
