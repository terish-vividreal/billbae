@extends('layouts.app')

@section('content')
@push('page-css')
<!-- daterange picker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />





<style>
  .btn {
    svg {
      vertical-align: inherit;
      margin-bottom: -0.15em;
    }
    rect {
      fill: currentcolor;
    }
  }

</style>

@endpush

@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="nav-link">Home</a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/users') }}" class="nav-link">{{ $page->title ?? ''}}</a>
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
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">{{ $page->title ?? ''}} Form</h3>


            
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
            <!-- /.card-header -->
            <div class="card-body">

            <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
            
              <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="post" action="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">
                {{ csrf_field() }}
                {!! Form::hidden('billing_id', $billing->id ?? '' , ['id' => 'billing_id'] ); !!}
                {!! Form::hidden('customer_id', $billing->customer_id ?? '' , ['id' => 'customer_id'] ); !!}
                <div class=""> 

                    <!-- <div class="form-group ">
                      <label class="col-form-label font-weight-bolder">Customer DOB</label>
                      <div class='input-group date' id='customerdob'>
                          <input type='text' name="dob" id="dob" onkeydown="return false" class="form-control" autocomplete="off" />
                          <div class="input-group-append">
                              <span class="input-group-text">
                                  <i class="fa fa-calendar"></i>
                              </span>
                          </div>
                      </div>
                    </div> -->

                    <div class="form-group">
                        <div class="input-group input-group-lg">
                            <input type="text" name="search_customer" id="search_customer" class="typeahead form-control form-control-lg" placeholder="Enter Customer name" autocomplete="off" value="">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-lg btn-default" onClick="addNewCustomer()">
                                    <i class="fa fa-plus"> New Customer</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- style="display:none;" -->
                <div class="container-fluid" id="customer_details_div">
                  <!-- SELECT2 EXAMPLE -->
                  <div class="card card-default">
                    <div class="card-header">
                      <h3 class="card-title">Customer Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            {!! Form::label('customer_name', 'Customer Name*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_name', $billing->name ?? '' , array('placeholder' => 'Customer Name', 'id' => 'customer_name' ,'class' => 'form-control', 'disabled' => 'disabled')) !!}
                          </div> 
                          <div class="form-group">
                            {!! Form::label('billed_date', 'Bill Date', ['class' => 'col-form-label']) !!}
                            <input type="text" name="billed_date" id="billed_date" class="form-control" onkeydown="return false" autocomplete="off" value="" />
                          </div> 
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                {!! Form::label('checkin_time', 'Checkin time', ['class' => 'col-form-label']) !!}
                                <input type="text" name="checkin_time" id="checkin_time" class="form-control" onkeydown="return false" autocomplete="off" value="17-06-2021 09:15 AM" />
                              </div> 
                            </div> 
                            <div class="col-md-6">
                              <div class="form-group">
                                {!! Form::label('checkout_time', 'Checkout time', ['class' => 'col-form-label']) !!}
                                <input type="text" name="checkout_time" id="checkout_time" class="form-control" onkeydown="return false" autocomplete="off" value="" />
                              </div> 
                            </div> 
                          </div> 
                                                    

                          <div class="form-group" style="margin-top: 45px;">                                                 
                              <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" name="billing_address_checkbox" id="billing_address_checkbox" value="1" checked="checked">
                                <label for="billing_address_checkbox" class="custom-control-label">Billing address and customer address is same.</label>
                              </div>
                              <small class="col-sm-2 ">Uncheck to add new billing address !</small>
                          </div>

                          <div class="billing-address-section" style="display:none;">

                            <div class="form-group billing-address-section">
                                {!! Form::label('customer_billing_name', 'Billing Name/ Company Name*', ['class' => 'col-form-label']) !!}
                                {!! Form::text('customer_billing_name', $billing->customer_billing_name ?? '' , array('placeholder' => 'Billing Name', 'id' => 'customer_billing_name' ,'class' => 'form-control')) !!}
                            </div>

                            <div class="form-group ">
                                {!! Form::label('customer_gst', 'GST No. ', ['class' => 'col-form-label text-alert']) !!}
                                {!! Form::text('customer_gst', $billing->gst ?? '' , array('placeholder' => 'GST No.','class' => 'form-control')) !!}                        
                            </div>

                            <div class="form-group" >
                              {!! Form::label('country_id', 'country*', ['class' => '']) !!} <br>
                              {!! Form::select('country_id', $variants->country , $billing->country_id ?? '' , ['id' => 'country_id' ,'class' => 'form-control','placeholder'=>'Select A Country']) !!}
                            </div>

                            <div class="form-group">
                              {!! Form::label('state_id', 'State*', ['class' => '']) !!} <br>
                              <div id="state_block">
                                  {!! Form::select('state_id', [] , '' , ['id' => 'state_id' ,'class' => 'form-control','placeholder'=>'Select a state']) !!}
                              </div>
                            </div>

                            <div class="form-group">
                              {!! Form::label('state_id', 'District*', ['class' => '']) !!} <br>
                              <div id="district_block"> 
                                {!! Form::select('district_id', [] , '' , ['id' => 'district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                              </div>
                            </div>

                          </div>
                          

                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            {!! Form::label('customer_mobile', 'Customer Mobile*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_mobile', $billing->name ?? '' , array('placeholder' => 'Customer Mobile', 'id' => 'customer_mobile' ,'class' => 'form-control', 'disabled' => 'disabled')) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('customer_email', 'Customer Email*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_email', $billing->customer_email ?? '' , array('placeholder' => 'Customer Email', 'id' => 'customer_email' ,'class' => 'form-control', 'disabled' => 'disabled')) !!}
                          </div>

                          <div class="billing-address-section" style="display:none;">

                            <div class="form-group ">
                                {!! Form::label('pincode', 'Pincode ', ['class' => 'col-form-label text-alert']) !!}
                                {!! Form::text('pincode', $billing->pincode ?? '' , array('placeholder' => 'Pincode','class' => 'form-control check_numeric')) !!}                        
                            </div>

                            <div class="form-group ">
                                {!! Form::label('address', 'Address. ', ['class' => 'col-form-label text-alert']) !!}
                                {!! Form::textarea('address', $billing->address ?? '', ['class' => 'form-control','placeholder'=>'Address','rows'=>3]) !!}                       
                            </div>

                          </div>

                        </div>
                      </div>

                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>

                <div class="container-fluid">
                  <!-- SELECT2 EXAMPLE -->
                  <div class="card card-default">
                    <div class="card-header">
                      <h3 class="card-title">Service Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Select Type</label>
                            <select class="form-control select2" name="service_type" id="service_type" onchange="$('#usedServicesDiv').hide();" style="width: 100%;">
                              <option selected="selected">Select Type</option>
                              <option value="1">Services</option>
                              <option value="2">Packages</option>
                            </select>
                          </div>



                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                          <!-- /.form-group -->
                          <div class="form-group">
                            <label>Select Details</label>
                            
                              <div id="services_block">
                                <select class="form-control service-type" data-type="services" name="bill_item[]" id="services" multiple="multiple" style="width: 100%;"> </select>
                              </div>

                              <div id="packages_block" style="display:none;">
                                <select class="form-control service-type" data-type="packages" name="bill_item[]" id="packages" multiple="multiple" style="width: 100%;"> </select>
                              </div>
                          </div>
                          <!-- /.form-group  style="display:none;"-->

                          
                        </div>
                        <!-- /.col -->
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          

                          <div class="form-group" id="usedServicesDiv" style="display:none">         
                            <table class="table table-bordered table-hover" id="servicesTable">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Name</th>
                                  <th>Amount</th>
                                </tr>
                              </thead>
                              <tbody>                         
                                
                              </tbody>
                            </table>

                            <div class="float-right" id="total">
                              {!! Form::hidden('grand_total', '' , ['id' => 'grand_total'] ); !!}
                              
                              <h4>Grand Total : <span id="grandTotal"></span></h4>
                              <!-- <h3><span id="discountAmount"></span></h3>
                              <h2><span id="afterdiscount"></span></h2> -->
                              <!-- <div id="discountDiv"><button class="btn btn-sm btn-primary" id="discount_btn">Discount</button></div> -->

                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.row -->

                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <div class="row">
                    <div class="col-12">
                    <a href="#" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success" id="continue"> Continue </button>
                    </div>
                </div>
              </form>              

            </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@include('billing.new-customer-manage')
@endsection
@push('page-scripts')

<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<!-- date-time-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>



<script type="text/javascript">

var timePicker = {!! json_encode($variants->time_picker) !!};
var timeFormat = {!! json_encode($variants->time_format) !!};


$(function() {
  $('input[name="billed_date"]').daterangepicker({
    singleDatePicker: true,
    startDate: new Date(),
    showDropdowns: true,
    autoApply: true,
    timePicker: true,
    timePicker24Hour: timePicker,
    locale: { format: 'DD-MM-YYYY '+timeFormat+':mm A' },
  }, function(ev, picker) {
      // console.log(picker.format('DD-MM-YYYY'));
  });

  $('input[name="checkin_time"]').daterangepicker({
    singleDatePicker: true,
    startDate: new Date(),
    showDropdowns: true,
    autoApply: true,
    timePicker: true,
    timePicker24Hour: timePicker,
    locale: { format: 'DD-MM-YYYY '+timeFormat+':mm A' },
  }, function(ev, picker) {
    // console.log(picker.format('DD-MM-YYYY'));
  });

  $('input[name="checkout_time"]').daterangepicker({
    singleDatePicker: true,
    startDate: new Date(),
    showDropdowns: true,
    autoApply: true,
    timePicker: true,
    timePicker24Hour: timePicker,
    locale: { format: 'DD-MM-YYYY '+timeFormat+':mm A' },
  }, function(ev, picker) {
    // console.log(picker.format('DD-MM-YYYY'));
  });

  // $('input[name="billed_date"]').daterangepicker({
  //   singleDatePicker: true,
  //   startDate: moment().startOf('hour'),
  //   endDate: moment().startOf('hour').add(32, 'hour'),
  //   timePickerIncrement: 5,
  //   autoApply: true,
  //   timePicker: true,
  //   locale: { format: 'DD-MM-YYYY hh:mm'}
  // });

// $('input[name="billed_date"]').daterangepicker({
//   singleDatePicker: true,
//   startDate: new Date(),
//   showDropdowns: true,
//   timePicker: true,
//   timePicker24Hour: true,
//   timePickerIncrement: 10,
//   autoUpdateInput: true,
//   locale: {
//     format: 'DD-MM-YYYY hh:mm A'
//   },
// });

  // function(start, end, label) {
  //   // console.log(end.format('DD-MM-YYYY hh:mm A'));
  // }

  
  // $('input[name="checkin_time"]').daterangepicker({
  //   singleDatePicker: true,
  //   startDate: new Date(),
  //   timePickerIncrement: 5,
  //   autoApply: true,
  //   timePicker: true,
  //   locale: { format: 'DD-MM-YYYY hh:mm A'}
  // }, function(start, end, label) {
  //   console.log(end.format('DD-MM-YYYY hh:mm A'));
  // });
  

  // $('input[name="checkout_time"]').daterangepicker({
  //   singleDatePicker: true,
  //   startDate: new Date(),
  //   // endDate: moment().startOf('hour').add(32, 'hour'),
  //   timePickerIncrement: 5,
  //   autoApply: true,
  //   timePicker: true,
  //   locale: {
  //     format: 'DD-MM-YYYY hh:mm A' }
  //   });
      // }, function(start, end, label) {
      //   console.log(end.format('DD-MM-YYYY hh:mm A'));
      // // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
      // }
  
  


  $('input[name="dob"]').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    drops: "up",
    maxYear: parseInt(moment().format('YYYY'),10),
    autoApply: true,

    }, function(ev, picker) {
      console.log(picker.format('DD-MM-YYYY'));

  });
  
  // $('input[name="dob"]').data('daterangepicker').setStartDate('15-06-2021');

});


// $('#customerdob').datepicker({
//   format: 'dd-mm-yyyy',
//   todayHighlight: true,
//   autoclose: true
// });

function addNewCustomer(){
  customervalidator.resetForm();
  $("#newCustomerForm .form-control").removeClass("error");
  $('#newCustomerForm').trigger("reset");
  $('#newCustomerForm').find("input[type=text], textarea").val("");
  $("#new-customer-modal").modal("show");
}

$('.service-type').select2({ placeholder: "Please select ", allowClear: false }).on('select2:select select2:unselect', function (e) { 
  var type = $(this).data("type");
  listItemDetails(type) 
  $(this).valid()
});

function listItemDetails(type){
  var data_ids = $('#'+type).val();
  if(data_ids != ''){
    $.ajax({
        type: 'post',
        url: "{{ url(ROUTE_PREFIX.'/common/get-taxdetails') }}",
        dataType: 'json',data: { data_ids:data_ids, type : type},delay: 250,
        success: function(data) {
            $("#servicesTable").find("tr:gt(0)").remove();
            $('#servicesTable').append(data.html);
            $('#grandTotal').text(data.grand_total);
            $('#grand_total').val(data.grand_total);
            $('#usedServicesDiv').show();
        }
    });
  }else{
    $('#usedServicesDiv').hide();
  }
}


var path = "{{ route('billing.autocomplete') }}";
$('input.typeahead').typeahead({
    autoSelect: true,
    source:  function (query, process) {
    return $.get(path, { search: query }, function (data) {
            return process(data);
        });
    },
    updater: function (item) {
      $('#customer_id').val(item.id);
      getCustomerDetails(item.id);
      return item;
    }
});

 
$('#billing_address_checkbox').change(function() {
    if($(this).is(":unchecked")) 
        $('.billing-address-section').show();
    else
        $('.billing-address-section').hide();         
});


function getCustomerDetails(customer_id){
  $.ajax({
      type: 'POST',
      url: "{{ url(ROUTE_PREFIX.'/common/get-customer-details') }}",
      dataType: 'json', data: { customer_id:customer_id},
      delay: 250,
      success: function(data) {
        $("#search_customer").val(data.data.name + ' - ' + data.data.mobile);
        $("#customer_name").val(data.data.name);
        $("#customer_mobile").val(data.data.mobile);
        $("#customer_email").val(data.data.email);
        $("#customer_id").val(customer_id);
        $("#customer_details_div").show();
      }
  });
}   

$(document).on('change', '#service_type', function () {
  if( this.value == 1 ){
    
    $("#services_block").show();
    $("#packages_block").hide();
    getServices();
  }else{
    
    $("#services_block").hide();
    $("#packages_block").show();
    getPackages();
  }
});

function getServices(){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-services') }}",
      dataType: 'json',
      delay: 250,
      success: function(data) {
          var selectTerms = '<option value="">Please choose services</option>';
          $.each(data.data, function(key, value) {
            selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
          });

          var select = $('#services');
          select.empty().append(selectTerms);
      }
  });
}

function getPackages(){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-packages') }}",
      dataType: 'json',
      delay: 250,
      success: function(data) {
          var selectTerms = '<option value="">Please choose packages</option>';
          $.each(data.data, function(key, value) {
            selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
          });

          var select = $('#packages');
          select.empty().append(selectTerms);
      }
  });
}


if ($("#newCustomerForm").length > 0) {
    var customervalidator = $("#newCustomerForm").validate({ 
        rules: {
            qnew_customer_name: {
                    required: true,
                    maxlength: 200,
                    lettersonly: true,
            },
            qnew_customer_mobile:{
                  required:true,
                  minlength:10,
                  maxlength:10
            },
        },
        messages: { 
            new_customer_name: {
                required: "Please enter customer name",
                maxlength: "Length cannot be more than 200 characters",
                },
            new_customer_mobile: {
                required: "Please enter mobile number",
                maxlength: "Length cannot be more than 10 numbers",
                minlength: "Length must be 10 numbers",
                },
        },
        submitHandler: function (form) {
            
            var forms = $("#newCustomerForm");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/add-new-customer') }}", type: "POST", processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    getCustomerDetails(data.customer_id)
                    $("#new-customer-modal").modal("hide");
                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
}

if ($("#{{$page->entity}}Form").length > 0) {
    var validator = $("#{{$page->entity}}Form").validate({ 
        rules: {
            customer_name: {
                    required: true,
            },
            search_customer: {
                    required: true,
            },
            "bill_item[]": {
                    required: true,
            },
        },
        messages: { 
            customer_name: {
                required: "Please select a customer",
            },
            search_customer: {
                required: "Please select a customer",
            },
            "bill_item[]": {
                required: "Please select an item",
            },
        },
        submitHandler: function (form) {
            // var forms = $("#{{$page->entity}}Form");
            // $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}", type: "POST", processData: false, 
            // data: forms.serialize(), dataType: "html",
            // }).done(function (a) {
            //     var data = JSON.parse(a);
            //     if(data.flagError == false){
            //         showSuccessToaster(data.message);
            //         // setTimeout(function () { 
            //         //   window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
            //         // }, 2000);

            //     }else{
            //       showErrorToaster(data.message);
            //       printErrorMsg(data.error);
            //     }
            // });
            $('#continue').html('Please Wait...');
            $("#continue"). attr("disabled", true);
            form.submit();
        },
        errorPlacement: function(error, element) {
            if (element.is("select")) {
                error.insertAfter(element.next('.select2'));
            }else {
                error.insertAfter(element);
            }
        }
    })
} 

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");

$("body").on("submit", ".ajax-submit", function (e) {
    e.preventDefault();         
});

$(document).on('change', '#country_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-states') }}/",
          type: "GET",
          data:{'country_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#state_block").html(data);
      })
});

$(document).on('change', '#state_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-districts') }}/",
          type: "GET",
          data:{'state_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#district_block").html(data);
      })
});

$("#discount_btn").click(function(){
  $("#discount-modal").modal("show");
});

if ($("#discountForm").length > 0) {
    var validator = $("#discountForm").validate({ 
        rules: {
            discount_value: {
                    required: true,
            },
        },
        messages: { 
            discount_value: {
                required: "Please enter discount value",
            }
        },
        submitHandler: function (form) {
            var forms       = $("#discountForm");
            var grand_total = $("#grand_total").val();
            $input          = $('<input type="hidden" name="grand_total"/>').val(grand_total);
            forms.append($input);
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    $('#discountAmount').text('Discount Amount : ' + data.discount_value);
                    $('#afterdiscount').text('After discount : ' + data.amount);
                    $('#grand_total').val(data.amount);
                    $("#discount-modal").modal("hide");
                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
} 



</script>
@endpush
