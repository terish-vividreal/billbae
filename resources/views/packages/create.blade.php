@extends('layouts.app')

@section('content')
@push('page-css')
<style>

tfoot {font-weight: bold;}

</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
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
          </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
                <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="" action="" class="ajax-submit">
                  {{ csrf_field() }}
                  {!! Form::hidden('package_id', $package->id ?? '' , ['id' => 'package_id'] ); !!}
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group ">
                            {!! Form::label('name', 'Package Name*', ['class' => 'col-form-label text-alert']) !!}
                            {!! Form::text('name', $package->name ?? '' , array('placeholder' => 'Package Name','class' => 'form-control')) !!}                        
                        </div> 
                      </div>

                      <div class="col-md-6">
                        <div class="form-group ">
                            {!! Form::label('name', 'Choose services*', ['class' => 'col-form-label text-alert']) !!}
                            <select class="form-control selec2" name="services[]" id="services" multiple="multiple"> </select>
                        </div> 
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                      <div class="form-group" id="usedServicesDiv" style="display:none;">
                        <label for="name" class="col-sm-6 col-form-label text-alert"><span>Services</span></label>           
                          <table class="table table-hover text-nowrap" id="servicesTable" >
                            <thead>
                              <tr>
                                <th>Name</th>
                                <th>Hours</th>
                                <th>price</th>
                              </tr>
                            </thead>
                            <tbody>                         
                              
                            </tbody>
                          </table>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="price" class="control-label">Package price*</label>
                          <input class="form-control check_numeric" type="text" name="price" id="price" value="" disabled/> 
                          <input class="form-control" type="hidden" name="totalPrice" id="totalPrice" value=""/>                     
                          <input class="form-control" type="hidden" name="discount" id="discount" value="" /> 
                          <h6><i><span id="price_info_message">Please choose service to enable price !</i></h6>                     
                        </div>
                      </div>

                      
                      <div class="col-md-3">
                        <div class="form-group ">
                          {!! Form::label('name', 'Validity Type', ['class' => 'col-form-label text-alert']) !!}
                          <select id="validity_mode" class="form-control" name="validity_mode">
                          <option selected="selected" value="1">Day</option>
                          <option value="2">Month</option>
                          <option value="3">Year</option>
                          
                          </select>
                        </div> 
                      </div>
                      <div class="col-md-3">
                        <div class="form-group ">
                          {!! Form::label('name', 'Package validity ', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('validity', $package->validity ?? '' , array('placeholder' => 'Package validity','class' => 'check_numeric form-control')) !!}                        
                        </div> 
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-6">
                        <div class="form-group">
                            @php 
                              $checked = '';
                                if(isset($service)){
                                    $checked = ($service->tax_included == 1) ? 'checked' : '' ; 
                                }                      
                            @endphp                                            
                          <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="tax_included" id="tax_included" value="1" {{ $checked }} >
                            {!! Form::label('tax_included', 'Tax Included *', ['class' => 'custom-control-label']) !!}
                          </div>
                          <small class= 'col-sm-2'>Check if tax is included with price !</small>
                        </div>
                        <div class="form-group">
                          {!! Form::label('gst_tax', 'GST Tax %', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('gst_tax', $variants->tax_percentage, $service->gst_tax ?? '' , ['id' => 'gst_tax', 'class' => 'form-control','placeholder'=>'Select tax percentage']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>
                        <div class="form-group">
                          {!! Form::label('hsn_code', 'HSN Code', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                          {!! Form::text('hsn_code', $service->hsn_code ?? '' , array('placeholder' => 'HSN Code','class' => 'form-control')) !!}
                          <div class="error" id="email_error"></div>
                        </div>
                        <div class="form-group">
                          {!! Form::label('additional_tax', 'Additional Tax', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('additional_tax[]', $variants->additional_tax, $variants->additional_tax_ids , ['id' => 'additional_tax', 'multiple' => 'multiple' ,'class' => 'form-control col-sm-12 selec2']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>
                      </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript">


$(document).ready(function() {
  $('#additional_tax').select2({ placeholder: "Please choose services", allowClear: false });
  
  $("#services").select2({ placeholder: "Please choose services", allowClear: false })
    .on('select2:select select2:unselect', function (e) { 
      loadData() 
      $(this).valid()
      });

  getServices();
});

function loadData(){
  var service_ids = $('#services').val();
  if(service_ids != ''){
    $.ajax({
        type: 'post',
        url: "{{ url(ROUTE_PREFIX.'/common/get-services') }}",
        dataType: 'json',
        data: { data_ids:service_ids},
        delay: 250,
        success: function(data) {

          if(data.data.length > 0){
            html = '';
            $("#servicesTable").find("tr:gt(0)").remove();
            $.each(data.data, function(key, value) {
              html += '<tr><td>'+value.name+'</td><td>'+value.hours.name+'</td><td>'+value.price+'</td></tr>';
            });
            $('#servicesTable').append('<tfoot><tr><td></td><td>Total</td><td>'+data.totalPrice+'</td></tr></tfoot>');

            $('#totalPrice').val(data.totalPrice);
            $( "#price" ).prop( "disabled", false );
            $('#servicesTable').append(html);
            $('#usedServicesDiv').show();
            $('#price_info_message').hide();
            calculateDiscount();
          }
        }
    });
  }else{
    $('#usedServicesDiv').hide();
    $('#totalPrice').val('');
    $('#price').val('');
    $('#discount').val('');
    $('#price_info_message').show();
    $( "#price" ).prop( "disabled", true );
  }
}

function calculateDiscount(){
    var total = $('#totalPrice').val();
    var price = $('#price').val();

    if(price != ''){
        var discount = parseFloat(total) - parseFloat(price);
        if(discount < 0){
          showErrorToaster("Package price is greater than total price.");
        }else{
          $('#discount').val(discount);
        }
    }
      
}

function getServices(){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-services') }}",
      dataType: 'json',
      // data: { category_id:category_id},
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

$("#price").change(function(){
  calculateDiscount();
});

if ($("#{{$page->entity}}Form").length > 0) {
    var validator = $("#{{$page->entity}}Form").validate({ 
        rules: {
            name: {
                    required: true,
                    maxlength: 200,
            },
            price: {
                    required: true,
            },
            "services[]": {
                    required: true,
            },
        },
        messages: { 
            name: {
                required: "Please enter package name",
                maxlength: "Length cannot be more than 200 characters",
                },
            price: {
                required: "Please enter package price",
                },
            "services[]": {
                required: "Please choose services",
                },
        },
        submitHandler: function (form) {
            id = $("#package_id").val();
            package_id      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms = $("#{{$page->entity}}Form");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + package_id, type: formMethod, processData: false, 
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



</script>
@endpush
