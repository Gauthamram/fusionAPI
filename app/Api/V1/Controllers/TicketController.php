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
     * list all ticket request
     * @param  $request
     * @return
     */
    public function index(Request $request)
    {
        $data = TicketRequest::all()->toArray();
        $data = $this->ticketTransformer->transformCollection($data);
        return $this->respond(['data' => $data]);
    }

    /**
     * printed - tickets printed based on user, change the tables
     * warehouse user - cgl_tickets_printed
     * other users - cgl_tickets_tips_printed
     * @return
     */
    public function printed()
    {
        DB::enableQueryLog();
        if ($this->user->isWarehouse()) {
            $ticket = new TipsTicketPrinted();
        } else {
            $ticket = new TicketPrinted();
        }
        $supplier = new Supplier();
        

        if ($this->user->isAdmin() || $this->user->isWarehouse()) {
            $tickets = $ticket::Printedlastmonth()->paginate(20)->toArray();
            // dd(DB::getQueryLog());

        } else {
            $tickets = Cache::remember("'".$this->user->getRoleId()."-tickets", Carbon::now()->addMinutes(60), function () {
                return Supplier::findOrFail($this->user->getRoleId())->tickets()->latest('createdate')->paginate(20)->toArray();
            });
        }
        
        $data = $this->ticketPrintedTransformer->transformCollection($tickets, true);
        return $this->respond(['data' => $data]);
    }

    /**
     * create ticketrequest
     * @param  $request
     * @return
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item' => 'required|integer',
            'qty'  => 'required|integer',
            'ticket_type' => 'required',
            'location_type' => 'required',
            'location' => 'required',
            'order_no' => 'required|integer'
        ]);
        $currentuser = JWTAuth::parseToken()->authenticate();

        if ($validator->fails()) {
            log::info($validator->errors()->all());
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
        $ticket->unit_retail = $request->retail ? $request->retail : '';
        $ticket->country_of_origin = $request->country ? $request->country : '';
        $ticket->order_no = $request->order_no;
        $ticket->create_datetime = Carbon::now();
        $ticket->last_update_datetime = Carbon::now();
        $ticket->last_update_id = $currentuser['name'];
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
