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
                {!! Form::hidden('package_id', $package->id ?? '' , ['id' => 'package_id'] ); !!}
                <div class=""> 



                    <div class="form-group ">
                        {!! Form::label('name', 'Package Name*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                        {!! Form::text('name', $package->name ?? '' , array('placeholder' => 'Package Name','class' => 'form-control')) !!}                        
                    </div> 

                    <div class="form-group ">
                        {!! Form::label('name', 'Choose services*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                        <select class="form-control selec2" name="services[]" id="services" multiple="multiple"> </select>
                    </div> 

                    

                    <div class="form-group" id="usedServicesDiv">
                      <label for="name" class="col-sm-6 col-form-label text-alert"><span>Services</span></label>           
                        <table class="table table-hover" id="servicesTable" >
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

                    <div class="form-group">
                      <div class="col-md-12">
                          <div class="form-group row">
                              <label for="price" class="col-md-2 control-label">Package price*</label>
                              <div class="col-md-3">
                                <input class="form-control check_numeric" type="text" name="price" id="price" value="{{ $package->price ?? ''}}"/> 
                                <input class="form-control" type="hidden" name="totalPrice" id="totalPrice" value=""/>                     
                                <input class="form-control" type="hidden" name="discount" id="discount" value="" />                      
                        
                              </div>
                              
                          </div>
                      </div>
                  </div>

                  <div class="form-group ">
                          {!! Form::label('name', 'Package validity ', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          <select id="validity_mode" class="col-sm-6 form-control" name="validity_mode">
                          <option @if($package->validity_mode == 1) selected="selected" @endif value="1">Day</option>
                          <option @if($package->validity_mode == 2) selected="selected" @endif value="2">Month</option>
                          <option @if($package->validity_mode == 3) selected="selected" @endif value="3">Year</option>
                          
                          </select>
                      </div> 
                      <div class="form-group ">
                          {!! Form::text('validity', $package->validity ?? '' , array('placeholder' => 'Package validity','class' => 'col-sm-6 check_numeric form-control')) !!}                        
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

 var service_ids = <?php echo json_encode($service_ids); ?>; 

  $("#services").select2({ placeholder: "Please choose services", allowClear: false })
    .on('select2:select select2:unselect', function (e) { loadData() });

  getServices(service_ids);
});

function loadData(){
  var service_ids = $('#services').val();
  if(service_ids != ''){
    $.ajax({
        type: 'post',
        url: "{{ url(ROUTE_PREFIX.'/common/get-services') }}",
        dataType: 'json',
        data: { service_ids:service_ids},
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
            calculateDiscount();
          }
        }
    });
  }else{
    $('#usedServicesDiv').hide();
    $('#totalPrice').val('');
    $('#price').val('');
    $('#discount').val('');
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

function getServices(service_ids){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-services') }}",
      dataType: 'json',
      // data: { category_id:category_id},
      delay: 250,
      success: function(data) {
          var selectTerms = '<option value="">Please choose services</option>';
          $.each(data.data, function(key, value) {
            selected = '';
            if (jQuery.inArray(value.id, service_ids)!='-1') {
                selected = 'selected';
            } 
            selectTerms += '<option value="' + value.id + '" ' + selected + ' >' + value.name + '</option>';
          });

          var select = $('#services');
          select.empty().append(selectTerms);
          loadData();
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
        },
        messages: { 
            name: {
                required: "Please enter package name",
                maxlength: "Length cannot be more than 200 characters",
                },
              price: {
                required: "Please enter package price",
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
