<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\FunctionHelper;
use App\Models\Billing;
use App\Models\Shop;
use DataTables;
use Auth;
use Carbon;
use DB;

class ReportController extends Controller
{
    protected $title        = 'Report';
    protected $viewPath     = 'report';
    protected $link         = 'reports';
    protected $route        = 'reports';
    protected $timezone     = '';
    protected $time_format  = 'H';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:store-profile-update', ['only' => ['index','update']]);
        $this->middleware(function ($request, $next) {
            $this->timezone     = Shop::where('user_id', Auth::user()->id)->value('timezone');
            $this->time_format  = (Shop::where('user_id', Auth::user()->id)->value('time_format') == 1)?'h':'H';
            return $next($request);
        });
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
        return view($this->viewPath . '.sales-report', compact('page', 'variants'));
    }

    public function getSalesReportChartData(Request $request)
    {
        $total_cash         = 0;
        $day_range          = $request->day_range;
        $from               = Carbon\Carbon::parse($request->start_range)->startOfDay();  
        $to                 = Carbon\Carbon::parse($request->end_range)->endOfDay();
        $chart_reports      = Billing::select( DB::raw("DATE_FORMAT(created_at, '%d %M') as day"), DB::raw("SUM(amount) as amount"), 'id as row_id', 'customer_id', 'payment_status')->where('shop_id', SHOP_ID)->groupBy(DB::raw("day(created_at)"))->whereBetween('created_at', [$from,$to])->orderBy('created_at', 'ASC')->get();                             
        $chart_data         = array();
        $chart_label        = array();
        $customer_array     = array();
        $pending            = 0;
        $completed          = 0;

        foreach ($chart_reports as $key => $value) {
            $chart_label[]      = $value->day;
            $chart_data[]       = (int)$value->amount;
            $total_cash         =  $total_cash+(int)$value->amount;
        }
        $report_data            = Billing::select( DB::raw("DATE_FORMAT(created_at, '%d %M') as day"), DB::raw("SUM(amount) as amount"), 'id', 'payment_status', 'billing_code', 'billed_date', 'checkin_time', 'checkout_time', 'customer_id')->where('shop_id', SHOP_ID)->whereBetween('created_at', [$from,$to])->groupBy('billings.id')->orderBy('created_at', 'ASC')->get();
        foreach ($report_data as $data) {
            if (!array_key_exists($data->customer_id,$customer_array)) {
                $customer_array[$data->customer_id] = 1;
            } else {
                $customer_array[$data->customer_id] = $customer_array[$data->customer_id]+1;
            }
            if ($data->payment_status == 0) {
                $pending    = $pending+1;
            } else {  
                $completed  = $completed+1;                              
            }
        }
        return ['flagError' => false, 'chart_label' => $chart_label,  'chart_data'=> $chart_data, 'start_date' => $from, 'end_date' => $to, 'total_cash' => number_format($total_cash,2), 'invoice' => count($report_data), 'customer' => count($customer_array), 'completed' => $completed, 'pending' => $pending,];
    }

    public function getSalesReportTableData(Request $request)
    {
        $from       = Carbon\Carbon::parse($request->start_range)->startOfDay();  
        $to         = Carbon\Carbon::parse($request->end_range)->endOfDay();
        $detail     =  Billing::select( DB::raw("DATE_FORMAT(created_at, '%d %M') as day"), DB::raw("SUM(amount) as amount"), 'id', 'payment_status', 'billing_code', 'billed_date', 'checkin_time', 'checkout_time', 'customer_id')->where('shop_id', SHOP_ID)->groupBy('billings.id')->orderBy('created_at', 'ASC');
        if( ($from != '') && ($to != '') ) {
            $detail->Where(function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            });
        }
        $detail = $detail->orderBy('created_at', 'DESC')->get();
        return Datatables::of($detail)
            ->addIndexColumn()
            ->editColumn('billed_date', function($detail){
                return FunctionHelper::dateToTimeZone($detail->billed_date, 'd-M-Y '.$this->time_format.':i a');
            })
            ->editColumn('billing_code', function($detail){
                $billing_code = '';
                $billing_code .=' <a href="' . url(ROUTE_PREFIX.'/billings/show/' . $detail->id) . '" target="_blank">'.$detail->billing_code.'</a>';
                return $billing_code;
            })
            ->editColumn('customer_id', function($detail){
                $customer = $detail->customer->name;
                return $customer;
            })
            ->editColumn('amount', function($detail){
                $amount = $detail->amount;
                return $amount;
            })
            ->editColumn('payment_status', function($detail){
                $status = '';
                if ($detail->payment_status == 0) {
                    $status = '<span class="chip lighten-5 red red-text">UNPAID</span>';
                } else {  
                    $status = '<span class="chip lighten-5 green green-text">PAID</span>';                                
                }
                return $status;
            })
            ->addColumn('in_out_time', function($detail){
                $checkin_time   =  FunctionHelper::dateToTimeZone($detail->checkin_time, 'd-M-Y '.$this->time_format.':i a');
                $checkout_time  =  FunctionHelper::dateToTimeZone($detail->checkout_time, 'd-M-Y '.$this->time_format.':i a');
                $in_out_time    = $checkin_time . ' - ' . $checkout_time;
                return $in_out_time;
            })
            ->addColumn('payment_method', function($detail){
                $methods         = '';
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
