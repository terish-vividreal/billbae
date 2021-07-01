<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use DataTables;
use Carbon;
use DB;

class ReportController extends Controller
{
    protected $title    = 'Report';
    protected $viewPath = 'report';
    protected $link     = 'reports';
    protected $route    = 'reports';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:store-profile-update', ['only' => ['index','update']]);
    }

    public function salesReport(Request $request)
    {
        $page                   = collect();
        $variants               = collect();
        $page->title            = $this->title;
        $page->link             = url($this->link);
        $page->route            = $this->route;
        
        $variants->start_range  = Carbon\Carbon::now()->startOfMonth()->format('m-d-Y');
        $variants->end_range    = Carbon\Carbon::now()->format('m-d-Y');

        // $chart_reports      = Billing::where('shop_id', SHOP_ID)->get();
        // foreach($chart_reports as $row){
        //     echo $row->id. ' - ' .$row->created_at->format('Y-m-d'). '<br>';

        //     $bill = Billing::findOrFail($row->id);
        //     $bill->checkin_time = $row->created_at->format('Y-m-d'). ' 09:30 AM'; 
        //     $bill->checkout_time = $row->created_at->format('Y-m-d'). ' 10:30 AM'; 
        //     $bill->save();
        //     // 18-06-2021 09:30 AM 
        // }
        // exit;
        return view($this->viewPath . '.sales-report', compact('page', 'variants'));

        

    }

    public function getSalesReportChartData(Request $request)
    {
        $total_cash     = 0;
        $day_range      = $request->day_range;

            // $datetime       = new Carbon\Carbon($request->end_range);
            // $to             = $datetime->addDays(1);

            // $from           = $request->start_range;      
            // $to             = $request->end_range;  


            $from   = Carbon\Carbon::parse($request->start_range)->startOfDay();  
            $to     = Carbon\Carbon::parse($request->end_range)->endOfDay();
 
            $chart_reports      = Billing::select(DB::raw("DATE_FORMAT(created_at, '%d %M') as day"), DB::raw("SUM(amount) as amount"),'id as row_id', 'customer_id', 'payment_status')
                                    
                                    ->where('shop_id', SHOP_ID)
                                    ->groupBy(DB::raw("day(created_at)"))
                                    // ->whereDate('created_at', '=', $your_date)
                                    ->whereBetween('created_at', [$from,$to])
                                    ->orderBy('created_at', 'ASC')->get();                             

            $chart_data         = array();
            $chart_label        = array();
            $customer_array     = array();
            $pending            = 0;
            $completed          = 0;


            foreach ($chart_reports as $key => $value) {
                $chart_label[]      = $value->day;
                $chart_data[]       = (int)$value->amount;
                $total_cash         =  $total_cash+(int)$value->amount;

                if (!array_key_exists($value->customer_id,$customer_array))
                {
                    $customer_array[$value->customer_id] = 1;
                }
                else
                {
                    $customer_array[$value->customer_id] = $customer_array[$value->customer_id]+1;
                }

                if($value->payment_status == 0){
                    $pending    = $pending+1;
                }else{  
                    $completed = $completed+1;                              
                }

            }
            return ['flagError' => false, 'chart_label' => $chart_label,  'chart_data'=> $chart_data, 'start_date' => $from, 'end_date' => $to, 
                                    'total_cash' => number_format($total_cash,2), 
                                    'invoice' => count($chart_reports), 
                                    'customer' => count($customer_array),
                                    'completed' => $completed,
                                    'pending' => $pending,
                                ];

    }

    public function getSalesReportTableData(Request $request)
    {
        // $from           = $request->start_range;
        // $datetime       = new Carbon\Carbon($request->end_range);
        // $to             = $datetime->addDays(1);

        $from   = Carbon\Carbon::parse($request->start_range)->startOfDay();  
        $to     = Carbon\Carbon::parse($request->end_range)->endOfDay();

        $detail =  Billing::select( DB::raw("DATE_FORMAT(created_at, '%d %M') as day"), 
                                    DB::raw("SUM(amount) as amount"),
                                    'id', 'payment_status', 'billing_code', 'billed_date',
                                    'checkin_time', 'checkout_time',
                                    'customer_id')
                                ->where('shop_id', SHOP_ID)
                                // ->groupBy(DB::raw("day(created_at)"));
                                ->groupBy('billings.id');
       
                                // echo "<pre>"; print_r($detail); exit;


        if( ($from != '') && ($to != '') ){
            $detail->Where(function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            });
        }
        $detail = $detail->orderBy('created_at', 'DESC')->get();

        return Datatables::of($detail)
            ->addIndexColumn()
            ->editColumn('billed_date', function($detail){
                $billed_date = new Carbon\Carbon($detail->billed_date);
                return $billed_date->toFormattedDateString();
            })
            ->editColumn('customer_id', function($detail){
                $customer = $detail->customer->name;
                return $customer;
            })
            ->editColumn('amount', function($detail){
                $amount = '₹ '. $detail->amount;
                return $amount;
            })
            ->editColumn('payment_status', function($detail){
                $status = '';
                if($detail->payment_status == 0){
                    $status = '<span class="badge badge-warning">Pending</span>';
                }else{  
                    $status = '<span class="badge badge-success">Paid</span>';                                
                }
                return $status;
            })
            ->addColumn('in_out_time', function($detail){
                $in_out_time = date('h:m A', strtotime($detail->checkin_time)) . ' - ' . date('h:m A', strtotime($detail->checkout_time));
                return $in_out_time;
            })
            ->addColumn('payment_method', function($detail){
                $methods = '';
                foreach($detail->paymentMethods as $row){
                    $methods .= $row->paymentype->name. ', '; 
                }
                return rtrim($methods, ', ');
            })
            

            ->removeColumn('id')
            ->escapeColumns([])
            ->make(true); 
    }
}
