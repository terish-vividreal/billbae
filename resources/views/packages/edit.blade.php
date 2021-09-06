@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')

@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Edit</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">List<i class="material-icons right">list</i></a>
@endsection


<div class="section">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsum is used for the ...</p>
    </div>
  </div>

  
  <!--Basic Form-->
  <div class="row">
    <!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
            <h4 class="card-title">{{ $page->title ?? ''}} Form</h4>

            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
            <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="" action="" class="ajax-submit">
                {{ csrf_field() }}
                {!! Form::hidden('package_id', $package->id ?? '' , ['id' => 'package_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('name', $package->name ?? '', ['id' => 'name']) !!}             
                  <label for="name" class="label-placeholder">Package Name <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                  <select class="select2 browser-default" name="services[]" id="services" multiple="multiple"> </select>
                </div>
              </div>


              <div class="row">
                <div class="input-field col m12 s12"> 
                      <div class="form-group" id="usedServicesDiv" style="display:none;">
                      <h5 class="card-title">Services </h5>           
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
                <div class="input-field col m6 s12">                 
                  <input class="form-control check_numeric" type="text" name="price" id="price" value="{{ $package->price ?? ''}}" /> 
                  <input class="form-control" type="hidden" name="totalPrice" id="totalPrice" value=""/>                     
                  <input class="form-control" type="hidden" name="discount" id="discount" value="" />
                  <label for="price" class="label-placeholder">Package Price <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m3 s12">
                  <select id="validity_mode" class="form-control" name="validity_mode">
                    <option @if($package->validity_mode == 1) selected="selected" @endif value="1">Day</option>
                    <option @if($package->validity_mode == 2) selected="selected" @endif value="2">Month</option>
                    <option @if($package->validity_mode == 3) selected="selected" @endif value="3">Year</option>   
                  </select>
                  <label for="validity_mode" class="label-placeholder">Validity Type</label>
                </div>
                <div class="input-field col m3 s12">                
                  {!! Form::text('validity', $package->validity ?? '' , array( 'id' => 'validity','class' => 'check_numeric')) !!}
                  <label for="validity" class="label-placeholder">Validity No.</label>
                </div>
              </div>

              <div class="row">
                <div class="input-field col m6 s12">
                    <div class="col s12">
                      @php 
                        $checked = '';
                          if(isset($package)){
                              $checked = ($package->tax_included == 1) ? 'checked' : '' ; 
                          }                      
                      @endphp
                      <label for="tax_included">Check if tax is included with price !</label>
                      <p><label><input class="custom-control-input" type="checkbox" name="tax_included" id="tax_included" value="1" {{ $checked }} >
                          <span>Tax Included</span>
                        </label> </p>
                      <div class="input-field">
                      </div>
                  </div>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('gst_tax', $variants->tax_percentage , $package->gst_tax ?? '' , ['id' => 'gst_tax', 'class' => 'select2 browser-default', 'placeholder'=>'Select GST Tax %']) !!}
                </div>
              </div>

              

              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('hsn_code', $package->hsn_code ?? '', ['id' => 'hsn_code']) !!}  
                  <label for="hsn_code" class="label-placeholder">HSN Code </label>
                </div>
                <div class="input-field col m6 s12">
                {!! Form::select('additional_tax[]', $variants->additional_tax, $variants->additional_tax_ids ?? [] , ['id' => 'additional_tax', 'multiple' => 'multiple' ,'class' => 'select2 browser-default']) !!}
                </div>
              </div>





              <div class="row">
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light" type="reset" name="reset">Reset <i class="material-icons right">refresh</i></button>
                  <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="submit-btn">Submit <i class="material-icons right">send</i></button>
                </div>
              </div>


            </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

{{-- vendor scripts --}}
@section('vendor-script')

@endsection


@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- date-time-picker -->

<script>
$(document).ready(function() {

var service_ids = <?php echo json_encode($service_ids); ?>; 

$('#additional_tax').select2({ placeholder: "Please choose services", allowClear: false });
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

</script>
@endpush

