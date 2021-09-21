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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) { 
            $data = Schedule::whereDate('start', '>=', $request->start)
                            ->whereDate('end',   '<=', $request->end)
                            ->get(['id', 'user_id as resourceId', 'start', 'end', 'name as title', 'description']);                
            return response()->json($data);
        }

        $page                   = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        $page->entity           = $this->entity;      
        return view($this->viewPath . '.list', compact('page'));
    }


    public function storeSchedule(Request $request)
    {
        
        // echo "<pre>"; print_r($request->all()); exit;
        // Generate Bill
        $billing    = Billing::generateBill($request->all());

        // Calculate Total time for each packages
        $items_details  = array();
        $total_time     = 0;
        if($request->service_type == 1){
            $items_details      = Service::totalTime($request->bill_item);
            $total_time         = $items_details['total_hours'];
            $description        = $items_details['description'];
        }else{
            $total_time = Package::serviceTotalTime($request->bill_item);
        }


        // Calculating end time
        $formatted_start_date       = new Carbon\Carbon($request->start_time);
        $start_date                 = new Carbon\Carbon($request->start_time);
        $end_time                   = $formatted_start_date->addMinutes($total_time);


        // Create Schedule
        $schedule   = new Schedule();
        $schedule->name             = $billing->customer->name . ' : '. $start_date->format('h:i:s A') . ' - ' . $end_time->format('h:i:s A') ;
        $schedule->start            = $request->start_time;
        $schedule->end              = $end_time;
        $schedule->user_id          = $request->user_id;
        $schedule->customer_id      = $billing->customer->id;
        $schedule->billing_id       = $billing->id;
        $schedule->description      = $description;
        $schedule->total_minutes    = $total_time;
        
        $schedule->save();

        if($schedule){
            return response()->json($schedule);
        }
        // switch ($request->type) {
        //    case 'create':
        //       $event = new Schedule();
        //       $event->name      = $request->name;
        //       $event->start     = $request->start;
        //       $event->end       = $request->end;
        //       $event->user_id   = $request->user_id;
        //       $event->save();
 
        //       return response()->json($event);
        //      break;
        //    case 'edit':
        //       $event = Schedule::find($request->id)->update([
        //           'name' => $request->name,
        //           'start' => $request->start,
        //           'end' => $request->end,
        //       ]);
        //       return response()->json($event);
        //      break;
        //    case 'delete':
        //       $event = Schedule::find($request->id)->delete();
        //       return response()->json($event);
        //      break;
        //    default:
        //      # ...
        //      break;
        // }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        return view($this->viewPath . '.create', compact('page', 'variants'));
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

        if($schedule)
            return response()->json(['flagError' => false, 'data' => $schedule]);
        else
            return response()->json(['flagError' => true, 'data' => null]);

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
        //
    }
}
