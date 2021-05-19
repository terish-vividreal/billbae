@extends('layouts.app')

@section('content')
@push('page-css')
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('admin/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<style>

.dropdown-menu {
	position:relative;
	width:100%;
	top: 0px !important;
  left: 0px !important;
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

            <div class="alert alert-danger print-error-msg" style="display:none">

            <ul></ul>

            </div>
            


              <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="" action="" class="ajax-submit">
                {{ csrf_field() }}
                {!! Form::hidden('billing_id', $billing->id ?? '' , ['id' => 'billing_id'] ); !!}
                {!! Form::hidden('customer_id', $billing->customer_id ?? '' , ['id' => 'customer_id'] ); !!}
                <div class=""> 

                    <div class="form-group">
                        <div class="input-group input-group-lg">
                            <input type="text" name="search_customer" id="search_customer" class="typeahead form-control form-control-lg" placeholder="Enter Customer name" autocomplete="off" value="">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-plus"> New Customer</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!--  -->
                <div class="container-fluid" id="customer_details_div" style="display:none;">
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
                            {!! Form::text('customer_name', $billing->name ?? '' , array('placeholder' => 'Customer Name', 'id' => 'customer_name' ,'class' => 'form-control')) !!}
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
                            {!! Form::text('customer_mobile', $billing->name ?? '' , array('placeholder' => 'Customer Mobile', 'id' => 'customer_mobile' ,'class' => 'form-control')) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('customer_email', 'Customer Email*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_email', $billing->customer_email ?? '' , array('placeholder' => 'Customer Email', 'id' => 'customer_email' ,'class' => 'form-control')) !!}
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
                                <select class="form-control select2" name="bill_item[]" id="services" multiple="multiple" style="width: 100%;"> </select>
                              </div>

                              <div id="packages_block" style="display:none;">
                                <select class="form-control select2" name="bill_item[]" id="packages" multiple="multiple" style="width: 100%;"> </select>
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
                              
                              <h3>Grand Total : <span id="grandTotal"></span></h3>

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
                    <button class="btn btn-success ajax-submit">Generate Bill</button>
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
@endsection
@push('page-scripts')

<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="{{ asset('admin/plugins/datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>


<script type="text/javascript">

$('#services').select2({ placeholder: "Please choose services", allowClear: false }).on('select2:select select2:unselect', function (e) { 
  listItemDetails('services') 
  $(this).valid()
});

$('#packages').select2({ placeholder: "Please choose packages", allowClear: false }).on('select2:select select2:unselect', function (e) { 
  listItemDetails('packages') 
  $(this).valid()
});

function listItemDetails(type){
  var data_ids = $('#'+type).val();
  // if(type == 'services'){
  //   var url         =  "{{ url(ROUTE_PREFIX.'/common/calculate-tax') }}";
  // }else{
  //   var url         =  "{{ url(ROUTE_PREFIX.'/common/get-packages') }}";
  // }
  if(data_ids != ''){
    $.ajax({
        type: 'post',
        url: "{{ url(ROUTE_PREFIX.'/common/get-taxdetails') }}",
        dataType: 'json',data: { data_ids:data_ids, type : type},delay: 250,
        success: function(data) {
          // if(data.data.length > 0){
            $("#servicesTable").find("tr:gt(0)").remove();
            // $.each(data.data, function(key, value) {
            //   html += '<tr><td>'+parseFloat(key+1)+'</td><td>'+value.name+'</td><td>₹ '+value.price+'</td></tr>';
            // });

            $('#servicesTable').append(data.html);
            // $('#servicesTable').append(data.table_footer);
            $('#grandTotal').text(data.grand_total);
            $('#usedServicesDiv').show();
          // }
        }
    });
  }else{
    $('#usedServicesDiv').hide();
  }
}


$('#packages').select2({ placeholder: "Please choose packages", allowClear: false });

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

// 
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
        $("#customer_name").val(data.data.name);
        $("#customer_mobile").val(data.data.mobile);
        $("#customer_email").val(data.data.email);
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
            var forms = $("#{{$page->entity}}Form");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}", type: "POST", processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    // setTimeout(function () { 
                    //   window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
                    // }, 2000);

                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
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



</script>
@endpush
