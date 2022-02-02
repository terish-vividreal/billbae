@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/jquery.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/select.dataTables.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/data-tables.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/dashboard.css')}}">
@endsection

@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/reports/sales-report') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">List</li>
  </ol>
@endsection

@section('page-action')
 
@endsection


<div class="section section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsume is used for the ...</p>
    </div>
  </div>

  <div id="card-stats" class="pt-0">
    <div class="row">
        <div class="col s12 m6 l6 xl3">
          <div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft">
              <div class="padding-4">
                <div class="row">
                    <div class="col s7 m7">
                      <i class="material-icons background-round mt-5">add_shopping_cart</i>
                      <p>Total Cash</p>
                    </div>
                    <div class="col s5 m5 right-align">
                      <h5 class="mb-0 white-text">₹ <span id="total_cash"></span></h5>
                      <!-- <p class="no-margin">New</p> -->
                      <!-- <p></p> -->
                    </div>
                </div>
              </div>
          </div>
        </div>
        <div class="col s12 m6 l6 xl3">
          <div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft">
              <div class="padding-4">
                <div class="row">
                    <div class="col s7 m7">
                      <i class="material-icons background-round mt-5">perm_identity</i>
                      <p>Customer</p>
                    </div>
                    <div class="col s5 m5 right-align">
                      <h5 class="mb-0 white-text" id="no_of_customers"></h5>
                      <!-- <p class="no-margin">New</p>  -->
                      <!-- <p id="no_of_customers"></p> -->
                    </div>
                </div>
              </div>
          </div>
        </div>
        <div class="col s12 m6 l6 xl3">
          <div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight">
              <div class="padding-4">
                <div class="row">
                    <div class="col s7 m7">
                      <i class="material-icons background-round mt-5">timeline</i>
                      <p>Invoice</p>
                    </div>
                    <div class="col s5 m5 right-align">
                      <h5 class="mb-0 white-text" id="no_of_invoice"></h5>
                      <!-- <p class="no-margin">Growth</p> -->
                      <!-- <p id="no_of_invoice"></p> -->
                    </div>
                </div>
              </div>
          </div>
        </div>
        <div class="col s12 m6 l6 xl3">
          <div class="card gradient-45deg-green-teal gradient-shadow min-height-100 white-text animate fadeRight">
              <div class="padding-4">
                <div class="row">
                    <div class="col s7 m7">
                      <i class="material-icons background-round mt-5">attach_money</i>
                      <p>Payment Status</p>
                    </div>
                    <div class="col s5 m5 right-align">
                      <h5 class="mb-0 white-text">Paid: <span id="completed_status"></span></h5>
                      <p class="no-margin"></p>
                      <p>Pending: <span id="pending_status"></span></p>
                    </div>
                </div>
              </div>
          </div>
        </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12 m6 l12">
        <div id="button-trigger" class="card card card-default scrollspy">
          <div class="card-content">
              <div class="row">
                <div class="col s3 right">
                <form id="reportForm" name="reportForm" role="form" method="" action="" class="ajax-submit">
                    {{ csrf_field() }}
                    {!! Form::hidden('start_range', '' , ['id' => 'start_range'] ); !!}
                    {!! Form::hidden('end_range', '' , ['id' => 'end_range'] ); !!}
                    {!! Form::hidden('range_sort', '0' , ['id' => 'range_sort'] ); !!}
                      <div class="row">
                        <div class="col-md-5 ml-auto mr-3">
                          <div class="form-group ">
                                {!! Form::label('day_range', 'Report Dates', ['class' => 'col-form-label text-alert']) !!}
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                  <i class="fa fa-calendar"></i>&nbsp;
                                  <span></span> <i class="fa fa-caret-down"></i>
                              </div>
                          </div> 
                        </div>
                      </div>
                  </form>

                </div>
              </div>
          </div>
        </div>
    </div>
  </div>


  <div id="sales-chart-data">
    <div class="row">
        <div class="col s12 m12 l12">
          <div id="revenue-chart" class="card animate fadeUp">
              <div class="card-content">
                <h4 class="header mt-0">
                    SALES DATA
                    <span class="purple-text small text-darken-1 ml-1">
                      <i class="material-icons">keyboard_arrow_up</i> 25.58 %</span>
                </h4>
                <div class="row">
                    <div class="col s12">
                      <div class="yearly-revenue-chart">
                        <canvas id="salesReportchart" height="250"></canvas>
                      </div>
                    </div>
                </div>
              </div>
          </div>
        </div>
    </div>
  </div>



    <!-- DataTables example -->
    <div class="row">
      <div class="col s12 m12 l12">
          <div id="button-trigger" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">{{ Str::plural($page->title) ?? ''}} Table</h4>
                <div class="row">
                  <div class="col s12">
                      <table id="data-table-reports" class="display data-tables"
                            data-url="{{ $page->link.'/lists' }}" data-form="page" data-length="20">
                          <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Bill ID</th>
                                <th>Customer Name</th>
                                <th>In - Out Times</th>
                                <th>Amount</th>
                                <th>Payment Methods</th>
                                <th>Payment Status</th>       
                            </tr>
                          </thead>
                          <tfoot align="right">
                            <tr>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th></th>
                            </tr>
                          </tfoot>
                      </table>
                  </div>
                </div>
            </div>
          </div>
      </div>
    </div>

</div>

@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/vendors/chartjs/chart.min.js')}}"></script>
@endsection


@push('page-scripts')
<script src="{{asset('admin/js/scripts/data-tables.js')}}"></script>
<script src="{{asset('admin/js/scripts/sales-chart.js')}}"></script>
<script>

var table;
var load_count  = 0;
var chart_label = chart_label
var chart_data  = chart_data;
var mode        = 'index'
var intersect   = true
var link        = '{{ $page->link }}';
// load_count++;

$(function() {
  var start   = moment().subtract(29, 'days');
  var end     = moment();

  function cb(start, end) {
      $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      $("#start_range").val(start.format('YYYY-MM-DD MM:MM:MM'));
      $("#end_range").val(end.format('YYYY-MM-DD MM:MM:MM'));
      $("#range_sort").val(1);
      getData();

  }
  $('#reportrange').daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
  }, cb);
  cb(start, end);

});

$(function () {
    table = $('#data-table-reports').DataTable({
        bSearchable: true,
        pagination: true,
        pageLength: 10,
        // responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        deferLoading: 0,
        scrollX: true,
        ajax: {
          url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/get-sales-table-data') }}",
          data: search
        },
        dom: 'Bfrtip',
        buttons: [ 'excel','pdf'],
        select: true,
          columns: [
              {data: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'billed_date', name: 'name', orderable: false, searchable: false},            
              {data: 'billing_code', name: 'name', orderable: false, searchable: false},            
              {data: 'customer_id', name: 'name', orderable: false, searchable: false},            
              {data: 'in_out_time', name: 'name', orderable: false, searchable: false},            
              {data: 'amount', name: 'name', orderable: false, searchable: false},  
              {data: 'payment_method', name: 'name', orderable: false, searchable: false},              
              {data: 'payment_status', name: 'name', orderable: false, searchable: false}
          ],
          footerCallback: function ( row, data, start, end, display ) {

            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 :  typeof i === 'number' ? i : 0;
            };

            // Total over all pages
            total = api
              .column( 5 )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );


            // Update footer
            $( api.column( 5 ).footer() ).html('<strong> ₹ '+ total + '</strong>');



          }
    });
});

function search(value) {
  value.name        = $('input[type=search]').val();
  value.start_range = $("#start_range").val();
  value.end_range   = $("#end_range").val();
}


// Chart start
var salesReportchart = document.getElementById("salesReportchart").getContext("2d");

var salesReportchartOption = {
  legend: {
      display: false,
      position: "bottom"
  },
  scales: {
      xAxes: [
        {
          display: true,
            gridLines: {
            display: false,
          },
        }
      ],
      yAxes: [
        {
          display: true,
            ticks: {
              padding: 10,
              // stepSize: 20,
              // max: 100,
              // min: 0,
              fontColor: "#9e9e9e"
            },
            gridLines: {
              display: true,
              drawBorder: false,
              lineWidth: 1,
              zeroLineColor: "#e5e5e5"
            }
        }
      ]
  },
  title: {
      display: false,
      fontColor: "#FFF",
      fullWidth: false,
      fontSize: 40,
      text: "82%"
  },
  responsive: true,
  maintainAspectRatio: true,
  datasetStrokeWidth: 3,
  pointDotStrokeWidth: 4,
  tooltipFillColor: "rgba(0,0,0,0.6)",
  hover: {
      mode: "label"
  },
};

var salesReportchart = new Chart(salesReportchart, {
  type: 'LineAlt',
  data: [],
  data: {
      labels: [],
      datasets: [{
        data: [],
        label: 'Sales :',
        pointRadius: 3,
        borderColor: "#9C2E9D",
        borderWidth: 2.5,
        pointBorderColor: "#9C2E9D",
        pointHighlightFill: "#9C2E9D",
        pointHoverBackgroundColor: "#9C2E9D",
        pointHoverBorderWidth: 2.5,
        fill: false,
      },
      ]
    },
  options: salesReportchartOption
})
// Chart ends


var getData = function() {    
    var forms = $("#reportForm");
    $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/get-sales-chart-data') }}", type: 'post', processData: false, 
    data: forms.serialize(), dataType: "html",
    }).done(function (a) {
        var data = JSON.parse(a);
        if(data.flagError == false){

          $("#total_cash").text(data.total_cash);
          $("#no_of_invoice").text(data.invoice);
          $("#no_of_customers").text(data.customer);         
          $("#completed_status").text(data.completed);         
          $("#pending_status").text(data.pending);         

          salesReportchart.data.labels = data.chart_label;
          salesReportchart.data.datasets[0].data = data.chart_data;
         
          salesReportchart.update();
          table.ajax.reload();

        }
    });

};






</script>
@endpush

