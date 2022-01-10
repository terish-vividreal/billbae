<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionHelper;
use Illuminate\Http\Request;
use App\Models\BillingItem;
use App\Models\Schedule;
use App\Models\Customer;
use App\Models\Billing;
use App\Models\Package;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use DataTables;
use Validator;
use Carbon;
use Auth;
use DB;

class ScheduleController extends Controller
{
    protected $title        = 'Schedule';
    protected $viewPath     = 'schedule';
    protected $link         = 'schedules';
    protected $route        = 'schedules';
    protected $entity       = 'Schedule';
    protected $timezone     = '';
    protected $time_format  = '';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->timezone     = Shop::where('user_id', Auth::user()->id)->value('timezone');
            $this->time_format  = (Shop::where('user_id', Auth::user()->id)->value('time_format') == 1)?'h':'H';
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * 2569
     * 2348
     * 221
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        if ($request->ajax()) {     
            $data = Schedule::whereDate('start', '>=', $request->start)->whereDate('end', '<=', $request->end)->get(['id', 'user_id as resourceId', 'start', 'end', 'name as title', 'description', 'schedule_color as color']);   
            return response()->json($data);
        }
        $user_id                = Auth::user()->id;
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;   
        $variants->time_picker  = ($this->time_format === 'h')?false:true;
        $variants->time_format  = $this->time_format;   
        $variants->timezone     = $this->timezone;   
        $variants->customers    = Customer::where('shop_id', SHOP_ID)->pluck('name', 'id'); 
        $variants->therapists   = User::role('Staffs')->leftjoin('staff_profiles', 'staff_profiles.user_id', '=', 'users.id')->where('users.parent_id', $user_id)->whereIn('staff_profiles.designation', [1, 2])->where('users.is_active', '!=',  2)->pluck('users.name', 'users.id'); 
        $schedule_data          = Schedule::whereDate('start', Carbon\Carbon::today())->get(['id', 'user_id as resourceId', 'start', 'end', 'name as title', 'description']);
        $sales_report           = Billing::select( DB::raw("SUM(amount) as amount"), 'id as row_id', 'payment_status')->where('shop_id', SHOP_ID)->where('payment_status', 1)->whereDate('created_at', Carbon\Carbon::today())->groupBy(DB::raw("day(created_at)"))->get()->toArray(); 
        return view($this->viewPath . '.create', compact('page', 'variants', 'schedule_data', 'sales_report'));
    }

    public function storeSchedule(Request $request)
    {
        $is_available  = Schedule::checkTimeAvailability($request->all()); 
        if (count($is_available) > 0) {
            return ['flagError' => true, 'message' => "Slot is already booked. Please select another time slot !"];
        }
        $action = '';
        if ($request->customer_id == null) {
            $validator = Validator::make($request->all(), ['customer_name' => 'required',]);
            if ($validator->passes()) {
                $customer                   = new Customer();
                $customer->shop_id          = SHOP_ID;
                $customer->name             = $request->customer_name;
                $customer->mobile           = $request->mobile;
                $customer->email            = $request->email;
                $customer->save();
                $request['customer_id']     = $customer->id;
            } else {
                return ['flagError' => true, 'message' => "Errors occurred Please check !",  'error'=>$validator->errors()->all()];
            }
        }
        // Generate Bill
        if ($request->schedule_id == null) {
            $billing    = Billing::generateBill($request->all());
            $schedule   = new Schedule();
            $action     = 'created';
        } else {
            $schedule   = Schedule::find($request->schedule_id);
            $billing    = Billing::updateBill($request->all(), $schedule->billing_id);
            $action     = 'updated';
        }
        // Calculate Total time for each packages
        $items_details  = array();
        $total_time     = 0;
        if ($request->service_type == 1) {
            $items_details      = Service::getScheduleDetails($request->bill_item);
            $total_time         = $items_details['total_hours'];
            $description        = $items_details['description'];
        } else {
            $items_details      = Package::getScheduleDetails($request->bill_item);
            $total_time         = $items_details['total_hours'];
            $description        = $items_details['description'];
        }

        // Calculating end time
        $formatted_start_date       = new Carbon\Carbon($request->start_time);
        $start_date                 = new Carbon\Carbon($request->start_time);
        $end_time                   = $formatted_start_date->addMinutes($total_time);
        // Create Schedule
        $schedule->name             = $billing->customer->name . ' - '. $billing->customer->mobile .' : '. $start_date->format('h:i:s A') . ' - ' . $end_time->format('h:i:s A') ;
        $schedule->start            = $request->start_time;
        $schedule->end              = $end_time;
        $schedule->user_id          = $request->user_id;
        $schedule->customer_id      = $billing->customer->id;
        $schedule->billing_id       = $billing->id;
        $schedule->description      = $description;
        $schedule->total_minutes    = $total_time;
        $schedule->shop_id          = SHOP_ID;
        $schedule->schedule_color   = ($request->checked_in == 1) ? "orange" : "red" ;
        $schedule->checked_in       = ($request->checked_in == 1) ? 1 : 0 ;   
        $schedule->save();
        $redirect = ($request->receive_payment == 1 )?'redirect':'reload';
        if ($schedule) {
            return ['flagError' => false, 'redirect' => $redirect, 'billing_id' => $billing->id,  'message' => "Schedule " . $action . " successfully"];
        }
        return ['flagError' => true, 'message' => "Something went wrong, Please try again!"];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {           

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        if ($schedule) {
            $start_time     = new Carbon\Carbon($schedule->start);
            $item_ids       = [];
            foreach($schedule->billing->items as $item) {
                $item_ids[] = $item->item_id;
            }
            return response()->json(['flagError' => false, 'data' => $schedule, 'customer_name' => $schedule->billing->customer->name, 'type' => $schedule->billing->items[0]->item_type, 'start_formatted' => $start_time->format($this->time_format. ':m A'), 'item_ids' => $item_ids]);
        } else {
            return response()->json(['flagError' => true, 'data' => null]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    public function reSchedule(Request $request)
    {
        $schedule = Schedule::find($request->schedule_id);
        $customer = Customer::find($schedule->customer_id);
        // Calculating end time
        $formatted_start_date       = new Carbon\Carbon($request->start_time);
        $start_date                 = new Carbon\Carbon($request->start_time);
        $end_time                   = $formatted_start_date->addMinutes($schedule->total_minutes);
        $schedule->name             = $customer->name . ' - '. $customer->mobile . ' : '. $start_date->format('h:i:s A') . ' - ' . $end_time->format('h:i:s A') ;
        $schedule->start            = $request->start_time;
        $schedule->end              = $end_time;
        $schedule->user_id          = $request->user_id;
        $schedule->save();
        if ($schedule) {
            return ['flagError' => false, 'message' => "Schedule updated successfully"];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        // Delete Bill details
        $bill = Billing::deleteBill($schedule->billing_id);
        if($bill) {
            if($schedule->delete())
                return ['flagError' => false, 'message' => "Schedule deleted successfully"];
        }  
    }
}
