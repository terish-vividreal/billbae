@extends('layouts.app')

@section('content')
@push('page-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="nav-link">Home</a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/users') }}" class="nav-link">Users</a>
  </li>
@endsection

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">{{ $page->title ?? ''}}</h1>
        </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <div class="text-right">
                <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn btn-sm btn-primary">
                  <i class="fa fa-plus" aria-hidden="true"></i> Add  {{ $page->title ?? ''}}
                </a>
              </div>
            </ol>
          </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
      
      <div class="row">

      

        <div class="col-12">

          <div class="card">

              <div class="card-header">
                <h3 class="card-title">{{ $page->title ?? ''}} Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- Form Section -->
              <div class="card-body">
                <form id="reportForm" name="reportForm" role="form" method="" action="" class="ajax-submit">
                  {{ csrf_field() }}
                  {!! Form::hidden('start_range', '' , ['id' => 'start_range'] ); !!}
                  {!! Form::hidden('end_range', '' , ['id' => 'end_range'] ); !!}
                  {!! Form::hidden('range_sort', '0' , ['id' => 'range_sort'] ); !!}
                    <div class="row">
                      <!-- <div class="col-md-6">
                        <div class="form-group ">
                            
                            <select id="day_range" class="form-control" name="day_range">
                              <option value="1">Today</option>
                              <option value="2">Yesterday</option>
                              <option value="3">Last 7 Days</option>
                              <option value="4">Last 30 Days</option>
                              <option selected="selected" value="5">This Month</option>
                              <option value="6">Last Month</option>                              
                            </select>
                        </div> 
                      </div> -->

                      <!-- <div class="col-md-6">
                        <div class="form-group ">{{$variants->start_range}} - {{$variants->end_range}}
                            {!! Form::label('daterange', 'Choose date range*', ['class' => 'col-form-label text-alert']) !!}
                            <input type="text" class="form-control" name="daterange" value="{{$variants->start_range}} - {{$variants->end_range}}" />
                        </div> 
                      </div> -->

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
              <!-- /.card-body -->

              <!-- Values list Section -->
              <div class="row">
                
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fa fa-rupee"></i></span>
                    <h5 class="description-header">TOTAL CASH</h5>
                    <span class="description-text">₹<span id="total_cash"></span></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-warning"><i class="fas fa-user"></i></span>
                    <h5 class="description-header">Customer</h5>
                    <span class="description-text" id="no_of_customers"></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-success"><i class="fas fa-calendar"></i> </span>
                    <h5 class="description-header">Invoice</h5>
                    <span class="description-text" id="no_of_invoice"></span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-6">
                  <div class="description-block">
                    <span class="description-percentage text-danger"><i class="fas fa-bell"></i></span>
                    <h5 class="description-header">Payment Status</h5>
                    <span class="description-text text-success">Completed: <span id="completed_status"></span></span> - 
                    <span class="description-text text-warning">Pending: <span id="pending_status"></span> </span>
                  </div>
                  <!-- /.description-block -->
                </div>
              </div>
            
              <!-- Chart Section -->
              <div class="card-header">
                <h3 class="card-title">Sales {{ $page->title ?? ''}} Chart</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="card">
                  <!-- <div id="linechart" style="height: 500px;"></div>     -->
                  <canvas id="linechart" height="250"></canvas>
                </div>
              </div>
              <!-- /.card-body -->

              <!-- /.card-header -->
              <div class="card-header">
                <h3 class="card-title">{{ $page->title ?? ''}} Table</h3>
              </div>
              <div class="card-body">
                <table class="table table-hover table-striped table-bordered data-tables"
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
                </table>                          
              </div>
              <!-- /.card-body -->
          </div>
          <!-- /.card -->

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('admin/js/pages/dashboard3.js') }}"></script>
<script>

var load_count = 0;
$(document).ready(function(){
  // getData()
});



  // Date Range
  // $(function() {
  //   $('input[name="daterange"]').daterangepicker({
  //     opens: 'left'
  //   }, function(start, end, label) {
  //     console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  //     $("#start_range").val(start.format('YYYY-MM-DD MM:MM:MM'));
  //     $("#end_range").val(end.format('YYYY-MM-DD MM:MM:MM'));
  //     $("#range_sort").val(1);
  //     getData();
  //   });
  // });

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


  var chart_label = chart_label
  var chart_data  = chart_data;
  var mode = 'index'
  var intersect = true
  load_count++;

  var visitorsChart = $('#linechart');
  var visitorsChart = new Chart(visitorsChart, {
    data: {
      labels: [],
      datasets: [{
        type: 'line',
        data: [],
        label: 'Sales :',
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1,
        backgroundColor: 'transparent',
        pointBorderColor: '#007bff',
        pointBackgroundColor: '#007bff',
        fill: false,
      },
      ]
    },
    options: {
      legend: {
        display: true
      },
      scales: {
        yAxes: [{
          display: true,
          gridLines: {
            display: true,
            lineWidth: '4px',
            color: 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
        }],
        xAxes: [{
          display: true,
          gridLines: {
            display: false
          },
        }]
      }
    }
  })
    
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

          visitorsChart.data.labels = data.chart_label;
          visitorsChart.data.datasets[0].data = data.chart_data;
          visitorsChart.update();
          table.ajax.reload();

        }
    });

  };


  var table;
  var link = '{{ $page->link }}';
  $(function () {

    table = $('.data-tables').DataTable({
        bSearchable: true,
        pagination: true,
        pageLength: 10,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
                url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/get-sales-table-data') }}",
                data: search
            },
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'billed_date', name: 'name'},            
            {data: 'billing_code', name: 'name'},            
            {data: 'customer_id', name: 'name'},            
            {data: 'in_out_time', name: 'name'},            
            {data: 'amount', name: 'name'},  
            {data: 'payment_method', name: 'name'},              
            {data: 'payment_status', name: 'name'}
        ]
    });

  });

  function search(value) {
    value.name        = $('input[type=search]').val();
    value.start_range = $("#start_range").val();
    value.end_range   = $("#end_range").val();
  }


  function softDelete(b) 
  {
    Swal.fire({
      title: 'Are you sure want to delete ?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/" + b, type: "DELETE", dataType: "html"})
                .done(function (a) {
                    var data = JSON.parse(a);
                    if(data.flagError == false){
                      showSuccessToaster(data.message);          
                      setTimeout(function () {
                        table.ajax.reload();
                        }, 2000);

                  }else{
                    showErrorToaster(data.message);
                    printErrorMsg(data.error);
                  }   
                }).fail(function () {
                        showErrorToaster("Somthing went wrong!");
                });
        }
    });
  }



</script>
@endpush

