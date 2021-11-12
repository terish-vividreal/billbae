@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/app-invoice.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/data-tables.css')}}">
@endsection

@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/customers') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Create</li>
  </ol>
@endsection

@section('page-action')
  <!-- <a href="javascript:" class="btn waves-effect waves-light orange darken-4 breadcrumbs-btn" onclick="importBrowseModal()" >Upload<i class="material-icons right">attach_file</i></a> -->
  <!-- <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a> -->
  <a href="{{ url(ROUTE_PREFIX.'/customers') }}" class="btn waves-effect waves-light light-blue darken-4 breadcrumbs-btn" type="submit" name="action">List<i class="material-icons right">list</i></a>
@endsection
<!-- users view start -->
<div class="section users-view section-data-tables invoice-list-wrapper">
  <!-- users view media object start -->
  <div class="card-panel">
    <div class="row">
      <div class="col s12 m7">
        <div class="display-flex media">
          <div class="media-body">
            <h6 class="media-heading">
              <span class="users-view-name">{{$customer->name}}</span>
              @if($customer->email != '')
              <span class="grey-text">@</span>
              <span class="users-view-username grey-text">{{$customer->email}}</span>
              @endif
            </h6>
            <span>CODE:</span>
            <span class="users-view-id">{{$customer->customer_code}}</span>
          </div>
        </div>
      </div>
      <div class="col s12 m5 quick-action-btns display-flex justify-content-end align-items-center pt-2">
        <a href="{{ url()->previous() }}" class="btn-small btn-light-indigo">Back</a>
        <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/'.$customer->id.'/edit') }}" class="btn-small indigo">Edit</a>
      </div>
    </div>
  </div>
  <!-- users view media object ends -->
  <!-- users view card data start -->
  <div class="card">
    <div class="card-content">
      <div class="row">
        <div class="col s12 m6">
          <table class="striped">
            <tbody>
              <tr>
                <td>Registered:</td>
                <td>{{$customer->created_at}}</td>
              </tr>
              <tr>
                <td>Latest Activity:</td>
                <td class="users-view-latest-activity">{{$last_activity->billed_date}}</td>
              </tr>
              
              <tr>
                <td>DOB:</td>
                <td class="users-view-role">{{ $customer->dob->format('Y-m-d') }}</td>
              </tr>
              <tr>
                <td>Gender:</td>
                <td>
                  <span class=" users-view-status chip green lighten-5">
                    @if($customer->gender == 1) Male  @endif
                    @if($customer->gender == 2) Female @endif
                    @if($customer->gender == 3) Others @endif
                  </span>
                  </td>
              </tr>
              <tr>
                <td>Status:</td>
                <td class="users-view-verified">
                  @if ($customer->deleted_at == null) 
                    <span class="chip lighten-5 green green-text">Active</span>
                  @else
                    <span class="chip lighten-5 red red-text">Banned</span>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col s12 m6">
          <table class="striped">
            <tbody>
              <tr>
                <td>Email:</td>
                <td>{{ $customer->email ?? ''}}</td>
              </tr>
              <tr>
                <td>Mobile:</td>
                <td>{{ $customer->mobile ?? ''}}</td>
              </tr>
              <tr>
                <td>Country:</td>
                <td>{{ $customer->country->name ?? ''}}
                  @if(!empty($customer->state->name)), {{ $customer->state->name ?? ''}} @endif
                  @if(!empty($customer->district->name)), {{ $customer->district->name ?? ''}} @endif
                </td>
              </tr>
              <tr>
                <td>GST:</td>
                <td class="users-view-role">{{ $customer->gst ?? ''}}</td>
              </tr>
              <tr>
                <td>Address:</td>
                <td>{{ $customer->address ?? ''}} @if(!empty($customer->pincode)), {{ $customer->pincode ?? ''}} @endif</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- users view card data ends -->

  <!-- users view card details start -->
  <div class="card">
    <div class="card-content">
      <div class="row indigo lighten-5 border-radius-4 mb-2">
        <div class="col s12 m4 users-view-timeline">
          <h6 class="indigo-text m-0">Total Bills: <span>{{ count($customer->billings) }}</span></h6>
        </div>
        <div class="col s12 m4 users-view-timeline">
          <h6 class="indigo-text m-0">Pending Bills: <span>{{ count($pending_bills) }}</span></h6>
        </div>
        <div class="col s12 m4 users-view-timeline">
          <h6 class="indigo-text m-0">Completed Payments: <span> {{ CURRENCY }} {{ $completed_bills->sum('amount') }}</span></h6>
        </div>
        
      </div>
      <!-- </div> -->
    </div>
  </div>
  <!-- users view card details ends -->

  <div class="card card-default scrollspy">
    <div class="card-content">
      <h4 class="card-title">{{ $customer->name }} Billing Table</h4>
      <div class="row">
        <div class="col s12">
          <form id="customerBillingTableForm" name="customerBillingTableForm" role="form" method="" action="" class="ajax-submit">
            {{ csrf_field() }}
            {!! Form::hidden('customer_id', $customer->id ?? '' , ['id' => 'customer_id'] ); !!}
            {!! Form::hidden('payment_status', '' , ['id' => 'payment_status'] ); !!}
            <div class="responsive-table">
              <div class="top display-flex  mb-2">
                <div class="action-filters">
                  <div id="DataTables_Table_0_filter" class="dataTables_filter">
                    <label>
                      <input type="search" class="" name="billing_code" id="billing_code" placeholder="Search Invoice" aria-controls="DataTables_Table_0">
                      <div class="filter-btn">
                          <a class="dropdown-trigger btn waves-effect waves-light purple darken-1 border-round" href="#" data-target="btn-filter">
                            <span class="hide-on-small-only">Filter Data</span>
                            <i class="material-icons">keyboard_arrow_down</i>
                          </a>
                          <ul id="btn-filter" class="dropdown-content" tabindex="0" style="">
                            <li tabindex="0"><a class="billing-payment-status" href="javascript:" data-status="">All</a></li>
                            <li tabindex="0"><a class="billing-payment-status" href="javascript:" data-status="1">Paid</a></li>
                            <li tabindex="0"><a class="billing-payment-status" href="javascript:" data-status="0" >Unpaid</a></li>
                          </ul>
                      </div>
                    </label>
                  </div>
                </div>
                <div class="actions action-btns display-flex align-items-center">
                  <div class="invoice-filter-action mr-3">
                    <a href="#" class="btn waves-effect waves-light invoice-export border-round z-depth-4">
                      <i class="material-icons">picture_as_pdf</i>
                      <span class="hide-on-small-only">Export to PDF</span>
                    </a>
                  </div>
                  <div class="invoice-create-btn">
                    <a href="{{ url(ROUTE_PREFIX.'/customers/create-bill/'.$customer->id) }}" class="btn waves-effect waves-light invoice-create border-round z-depth-4 cyan">
                      <i class="material-icons">add</i>
                      <span class="hide-on-small-only">Create New Bill</span>
                    </a>
                  </div>
                </div>
              </div>
              <div class="clear"></div>
            </div>
          </form>
          <div class="clear"></div>
          <div class="row">
                <div class="col s12">
                  <table id="data-table-customer-data" class="display data-tables">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Bill ID</th>
                        <th>Date</th>
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
<!-- users view ends -->

@include('customer.import-browse-modal')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/toastr/toastr.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
@endsection

@push('page-scripts')
<script src="{{asset('admin/js/scripts/data-tables.js')}}"></script>
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- date-time-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>

var table;
var customer_id = "{{$customer->id}}";

$(function () {
    table = $('#data-table-customer-data').DataTable({
        bSearchable: true,
        pagination: true,
        pageLength: 10,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        language: {
          processing: '<div class="progress"><div class="indeterminate"></div></div>'
          },
          // processing: '<div class="preloader-wrapper big active"><div class="spinner-layer spinner-blue-only"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>'
        ajax: {
            url: "{{ url(ROUTE_PREFIX.'/customers/billing-report') }}/"+customer_id,
            data: search
        },
          columns: [
              {data: 'DT_RowIndex', orderable: false, searchable: false, 'width': '2%'},
              {data: 'billing_code', name: 'name', orderable: false, searchable: false},
              {data: 'billed_date', name: 'name', orderable: false, searchable: false},   
              {data: 'in_out_time', name: 'name', orderable: false, searchable: false},            
              {data: 'amount', name: 'name', orderable: false, searchable: false},  
              {data: 'payment_method', name: 'name', orderable: false, searchable: false},              
              {data: 'payment_status', name: 'name', orderable: false, searchable: false}
          ],
          footerCallback: function ( row, data, start, end, display ) {

            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            total = api
              .column( 4 )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );


            // Update footer
            $( api.column( 4 ).footer() ).html('<strong> ₹ '+ total + '</strong>');



          }
    });
});

function search(value) {
  value.customer_id     = $("#customer_id").val();
  value.payment_status  = $("#payment_status").val();
  value.billing_code    = $("#billing_code").val();
}

$('.billing-payment-status').click(function(event) {
  $(".billing-payment-status").removeClass("active");
  $(this).addClass("active");
  $("#payment_status").val($(this).attr("data-status"));
  table.ajax.reload();
});

$("#billing_code").keyup(function(){
  table.ajax.reload();
});


$(document).ready(function(){
  $('input[name="dob"]').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    maxYear: parseInt(moment().format('YYYY'),10)
  }, function(start, end, label) {
    var years = moment().diff(start, 'years');
  });
});

if ($("#{{$page->entity}}Form").length > 0) {
  var validator = $("#{{$page->entity}}Form").validate({ 
    rules: {
      name: {
        required: true,
        maxlength: 200,
        lettersonly: true,
      },
      mobile:{
        required:true,
        minlength:10,
        maxlength:10
      },
    },
    messages: { 
      name: {
        required: "Please enter customer name",
        maxlength: "Length cannot be more than 200 characters",
      },
      mobile: {
        required: "Please enter mobile number",
        maxlength: "Length cannot be more than 10 numbers",
        minlength: "Length must be 10 numbers",
      },
    },
    submitHandler: function (form) {
      $('#submit-btn').html('Please Wait...');
      $("#submit-btn"). attr("disabled", true);
      id = $("#customer_id").val();
      customer_id      = "" == id ? "" : "/" + id;
      formMethod  = "" == id ? "POST" : "PUT";
      var forms = $("#{{$page->entity}}Form");
      $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + customer_id, type: formMethod, processData: false, 
      data: forms.serialize(), dataType: "html",
      }).done(function (a) {
        $('#submit-btn').html('Submit');
        $("#submit-btn"). attr("disabled", false);
        var data = JSON.parse(a);
        if (data.flagError == false) {
          showSuccessToaster(data.message);
          setTimeout(function () { 
            window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
          }, 2000);
        } else {
          showErrorToaster(data.message);
          printErrorMsg(data.error);
        }
      });
    },
  })
}

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");

</script>
@endpush