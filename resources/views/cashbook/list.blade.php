@extends('layouts.app')

@section('content')
@push('page-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">


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
        </div><!-- /.col id="addCash" -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <div class="text-right">
                <a href="javascript:" data-modalname="add-cash-modal"  data-form="addCashForm" class="btn btn-sm btn-primary loadModal">
                  <i class="fa fa-plus" aria-hidden="true"></i> Add Cash
                </a>

                <a href="javascript:" data-modalname="withdraw-cash-modal" data-form="withdrawCashForm" class="btn btn-sm btn-warning loadModal">
                  <i class="fa fa-credit-card" aria-hidden="true"></i> Withdraw Cash
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
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="business_cash">{{ number_format($variants->business_cash, 2) ?? ''}}</h3>

                <p>Business Cash</p>
              </div>
              <div class="icon">
                <i class="fas fa-briefcase"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id="petty_cash">{{ number_format($variants->petty_cash, 2) ?? ''}}</h3>

                <p>Petty Cash</p>
              </div>
              <div class="icon">
                <i class="far fa-money-bill-alt"></i>
              </div>
            </div>
          </div>
        </div>
      
      <div class="row">
        <div class="col-12">
          <div class="card ">
              <div class="card-header">
                <h3 class="card-title">{{ $page->title ?? ''}} Filter Form</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
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
                      <div class="col-md-3 ml-auto mr-3">
                        <div class="form-group ">
                              {!! Form::label('day_range', 'Cash book ', ['class' => 'col-form-label text-alert']) !!}
                              {!! Form::select('cash_book', [1 => 'Business Cash', 2 => 'Petty Cash'] , '' , ['id' => 'cash_book' ,'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                        </div> 
                      </div>                       
                      <div class="col-md-3 ml-auto mr-3">
                        <div class="form-group ">
                              {!! Form::label('day_range', 'Cash From', ['class' => 'col-form-label text-alert']) !!}
                              {!! Form::select('cash_from', [0 => 'Cash Deposit', 1 => 'From Sales'] , '' , ['id' => 'cash_from' ,'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                        </div> 
                      </div>
                      <div class="col-md-2 ml-auto mr-3">
                        <div class="form-group ">
                              {!! Form::label('day_range', 'Transaction Type', ['class' => 'col-form-label text-alert']) !!}
                              {!! Form::select('transaction_type', [1 => 'Credit', 2 => 'Debit'] , '' , ['id' => 'transaction_type' ,'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                        </div> 
                      </div>

                      <div class="col-md-3 ml-auto mr-3">
                        <div class="form-group ">
                              {!! Form::label('day_range', 'Transaction Dates', ['class' => 'col-form-label text-alert']) !!}
                              <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div> 
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2 ml-auto mr-3">
                        <div class="form-group ">
                        <button type="button" class="btn btn-info btn-block btn-flat" id="resetSelection"><i class="fa fa-refresh"></i> Reset All</button>
                        </div> 
                      </div> 
                    </div>
                </form>
              </div>
              <!-- /.card-body -->

              <!-- Values list Section -->
          </div>
          <div class="card">

              <!-- /.card-header -->
              <div class="card-header">
                <h3 class="card-title">{{ $page->title ?? ''}} Transaction Table</h3>
              </div>
              <div class="card-body">
                <table class="table table-hover table-striped table-bordered data-tables"
                      data-url="{{ $page->link.'/lists' }}" data-form="page" data-length="20">
                      <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Cash Book name</th>                            
                            <th>Amount</th> 
                            <th>Transaction Type</th>   
                            <th>Transaction Done by</th>   
                            <th>Message</th>  
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
@include('cashbook.add-cash')
@include('cashbook.withdraw-cash')
@include('cashbook.full-message')
@endsection
@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script>
var table;
var link = '{{ $page->link }}';

$('.select2').select2({ placeholder: "Please select ", allowClear: false }).on('select2:select select2:unselect', function (e) { 
  table.ajax.reload();
});



  $(".loadModal").on("click", function(){
    var modalname 	= $(this).data("modalname");
    var form   	    = $(this).data("form");


    addvalidator.resetForm();
    validator.resetForm();
    $(".display-none").hide();
    $('input').removeClass('error');
    $('select').removeClass('error');
    $('#'+form).trigger("reset");
    $("#"+modalname).modal("show");
  });

  $(function () {
      $("#add_cash_book").change(function () {
        (this.value != '')?$("#cashOptionDiv").show():$("#cashOptionDiv").hide();
        var other_option = (this.value == 1)?2:1;
        $("#move_from").text($("#add_cash_book option[value='"+other_option+"']").text());
      });
  });

  $(function() {

    var start   = moment().subtract(1, 'days');
    var end     = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $("#start_range").val(start.format('YYYY-MM-DD MM:MM:MM'));
        $("#end_range").val(end.format('YYYY-MM-DD MM:MM:MM'));
        $("#range_sort").val(1);
        table.ajax.reload();

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

  table = $('.data-tables').DataTable({
        bSearchable: true,
        pagination: true,
        pageLength: 10,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
                url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/lists') }}",
                data: search
            },
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'created_at', name: 'name'},            
            {data: 'cash_book', name: 'name'},            
            {data: 'amount', name: 'name'},            
            {data: 'transaction_type', name: 'name'},  
            {data: 'transaction_by', name: 'name'},
            {data: 'message', name: 'name'},
        ]
  });

  function search(value) {
    value.name              = $('input[type=search]').val();
    value.start_range       = $("#start_range").val();
    value.end_range         = $("#end_range").val();
    value.transaction_type  = $("#transaction_type").val();
    value.cash_from         = $("#cash_from").val();
    value.cash_book         = $("#cash_book").val();
  }

  $("#resetSelection").on("click", function(){
    $(".select2").val('').trigger('change');
    table.ajax.reload();
  });
  

  if ($("#addCashForm").length > 0) {
    var addvalidator = $("#addCashForm").validate({ 
        rules: {
          cash_book: {
                  required: true,
          },
          amount: {
            required: true,
          }
        },
        messages: { 
          cash_book: {
            required: "Please select cash book",
            },
          amount: {
            required: "Please enter amount",

            }
        },
        submitHandler: function (form) {
          var forms = $("#addCashForm");
          $('#submit').html('Please Wait...');
          $("#submit"). attr("disabled", true);
          $.ajax({
              url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}",
              type: "POST",
              data: forms.serialize(),
              success: function( response ) {
                    $('#submit').html('Submit');
                    $("#submit"). attr("disabled", false); 
                  if(response.flagError == false){
                      showSuccessToaster(response.message);                
                      
                      $("#add-cash-modal").modal("hide");
                      $("#business_cash").text(response.business_cash);
                      $("#petty_cash").text(response.petty_cash);
                      setTimeout(function () {
                        table.ajax.reload();
                      }, 2000);
                  }else{
                    showErrorToaster(response.message);
                    printErrorMsg(response.error);
                  }
              }
              });
      }
    })
  }

  if ($("#withdrawCashForm").length > 0) {
    var validator = $("#withdrawCashForm").validate({ 
        rules: {
          cash_book: {
                  required: true,
          },
          amount: {
            required: true,
          }
        },
        messages: { 
          cash_book: {
            required: "Please select cash book",
            },
          amount: {
            required: "Please enter amount",

            }
        },
        submitHandler: function (form) {
          var forms = $("#withdrawCashForm");
          $('#submit').html('Please Wait...');
          $("#submit"). attr("disabled", true);
          $.ajax({
              url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/withdraw') }}",
              type: "POST",
              data: forms.serialize(),
              success: function( response ) {
                $('#withdraw_submit').html('Submit');
                $("#withdraw_submit"). attr("disabled", false);
                  if(response.flagError == false){
                      showSuccessToaster(response.message);                
                      
                      $("#withdraw-cash-modal").modal("hide");
                      $("#business_cash").text(response.business_cash);
                      $("#petty_cash").text(response.petty_cash);
                      setTimeout(function () {
                        table.ajax.reload();
                      }, 2000);
                  }else{
                    showErrorToaster(response.message);
                    printErrorMsg(response.error);
                  }
              }
              });
        }
    })
  }


  
  showMessage = function(message) {
    $("#fullMessage").text(message)
    $("#full-message-modal").modal("show");
  } 




  // jQuery.validator.addMethod("lettersonly", function (value, element) {

  //   console.log(value)
  //   // console.log(element)
  //   // return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
  // }, "Letters only please");

</script>
@endpush

