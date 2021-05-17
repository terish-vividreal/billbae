@extends('layouts.app')

@section('content')
@push('page-css')
<!-- daterange picker -->
<link rel="stylesheet" href="{{ asset('admin/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<style>

.typeahead dropdown-menu {
  width:85%;
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
                            <input type="search" name="search_customer" id="search_customer" class="typeahead form-control form-control-lg" placeholder="Enter Customer name" value="">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-plus"> New Customer</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>

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

                          <div class="form-group">
                            {!! Form::label('customer_name', 'Billing Name*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_name', $billing->name ?? '' , array('placeholder' => 'Customer Name', 'id' => 'customer_name' ,'class' => 'form-control')) !!}
                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            {!! Form::label('customer_mobile', 'Customer Mobile*', ['class' => 'col-form-label']) !!}
                            {!! Form::text('customer_mobile', $billing->name ?? '' , array('placeholder' => 'Customer Mobile', 'id' => 'customer_mobile' ,'class' => 'form-control')) !!}
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
                            <select class="form-control select2" name="service_type" id="service_type" style="width: 100%;">
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
                                <select class="form-control select2" name="services[]" id="services" multiple="multiple" style="width: 100%;"> </select>
                              </div>

                              <div id="packages_block" style="display:none;">
                                <select class="form-control select2" name="packages[]" id="packages" multiple="multiple" style="width: 100%;"> </select>
                              </div>
                          </div>
                          <!-- /.form-group -->
                        </div>
                        <!-- /.col -->
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
                    <button class="btn btn-success ajax-submit">Submit</button>
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

  var path = "{{ route('billing.autocomplete') }}";
  $('input.typeahead').typeahead({
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



function getCustomerDetails(customer_id){
  $.ajax({
      type: 'POST',
      url: "{{ url(ROUTE_PREFIX.'/common/get-customer-details') }}",
      dataType: 'json', data: { customer_id:customer_id},
      delay: 250,
      success: function(data) {
        $("#customer_name").val(data.data.name);
        $("#customer_mobile").val(data.data.mobile);
        $("#customer_details_div").show();
      }
  });
}   

$('#services').select2({ placeholder: "Please choose services", allowClear: false });
$('#packages').select2({ placeholder: "Please choose packages", allowClear: false });


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

    
//Date picker
$('.form_date').datetimepicker({
  format: "dd MM yyyy",
  language:  'fr',
  weekStart: 1,
  todayBtn:  1,
  autoclose: 1,
  todayHighlight: 1,
  startView: 2,
  minView: 2,
  forceParse: 0
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
            id = $("#customer_id").val();
            customer_id      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms = $("#{{$page->entity}}Form");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + customer_id, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                      window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
                    }, 2000);

                }else{
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
