@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/toastr/toastr.min.css') }}">
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
  <a href="javascript:" class="btn waves-effect waves-light orange darken-4 breadcrumbs-btn" onclick="importBrowseModal()" >Upload<i class="material-icons right">attach_file</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/customers') }}" class="btn waves-effect waves-light light-blue darken-4 breadcrumbs-btn" type="submit" name="action">List<i class="material-icons right">list</i></a>
@endsection

<div class="section">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsume is used for the ...</p>
    </div>
  </div>
  <!--Basic Form-->
  <div class="row">
    <!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          @include('layouts.success') 
          @include('layouts.error')
          <h4 class="card-title">{{ $page->title ?? ''}} Form</h4>
            <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="" action="" class="ajax-submit">
              {{ csrf_field() }}
              {!! Form::hidden('customer_id', $customer->id ?? '' , ['id' => 'customer_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('name', $customer->name ?? '', array('id' => 'name')) !!}  
                  <label for="name" class="label-placeholder active">Customer Name <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::text('email', $customer->email ?? '', array('autocomplete' => 'off', 'id' => 'email')) !!}
                  <label for="email" class="label-placeholder active">Email</label>
                </div>
              </div>
              <div class="row">
                <div class="col s2">
                  <div class="input-field">                  
                  {!! Form::select('phone_code', $variants->phonecode , $store->country_id ?? '' , ['id' => 'phone_code', 'class' => 'select2 browser-default', 'placeholder'=>'Please select phone code']) !!}
                  <label for="phone_code" class="label-placeholder active">Phone code </label>
                  </div>
                </div>
                <div class="input-field col m4 s12">
                  {!! Form::text('mobile', $customer->mobile ?? '', array('id' => 'mobile', 'class' => 'check_numeric')) !!}  
                  <label for="mobile" class="label-placeholder active">Mobile </label>
                </div>              
                <div class="input-field col m6 s12">  
                <label for="gender" class="label-placeholder active">DOB </label>              
                  <p style="margin-top: 23px;">
                    <label>
                      <input value="1" id="male" name="gender" type="radio" checked/>
                      <span> Male </span>
                    </label>             
                    <label>
                      <input value="2" id="female" name="gender" type="radio" />
                      <span> Female </span>
                    </label>     
                    <label>
                      <input value="3" id="others" name="gender" type="radio" />
                      <span> Others </span>
                    </label>
                    
                  </p>
                </div>             
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  <input type='text' name="dob" id="dob" onkeydown="return false" class="" autocomplete="off" />
                  <label for="dob" class="label-placeholder active">DOB </label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light" type="button" name="reset" id="reset-btn">Reset <i class="material-icons right">refresh</i></button>
                  <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="submit-btn">Submit <i class="material-icons right">send</i></button>
                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@include('customer.import-browse-modal')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/toastr/toastr.min.js')}}"></script>
@endsection

@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- date-time-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>

$('#phone_code').select2({ placeholder: "Please select country"});

$(document).ready(function(){
  $('input[name="dob"]').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    // autoApply: true,
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
        // required:true,
        minlength:3,
        maxlength:15,
        digits:true,
      },
      phone_code: {
        // required: true,
      },
      email: {
        email: true,
        emailFormat:true,
        remote: { url: "{{ url(ROUTE_PREFIX.'/users/unique') }}", type: "POST",
          data: {
            user_id: function () {
              return $('#customer_id').val();
            }
          }
        },
      },
    },
    messages: { 
      name: {
        required: "Please enter customer name",
        maxlength: "Length cannot be more than 200 characters",
      },
      phone_code: {
        required: "Please select phone code",
      },
      mobile: {
        required: "Please enter mobile number",
        maxlength: "Length cannot be more than 15 numbers",
        minlength: "Length must be 3 numbers",
        digits: "Please enter a valid mobile number",
      },
      email: {
        required: "Please enter store email",
        email: "Please enter a valid email address",
        remote: "Email already existing"
      },
    },
    submitHandler: function (form) {
      disableBtn("submit-btn");
      id = $("#customer_id").val();
      customer_id      = "" == id ? "" : "/" + id;
      formMethod  = "" == id ? "POST" : "PUT";
      var forms = $("#{{$page->entity}}Form");
      $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + customer_id, type: formMethod, processData: false, 
      data: forms.serialize(), dataType: "html",
      }).done(function (a) {
        enableBtn("submit-btn");
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

jQuery.validator.addMethod("emailFormat", function (value, element) {
    return this.optional(element) || /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm.test(value);
  }, "Please enter a valid email address");  

jQuery.validator.addMethod("lettersonly", function (value, element) {
    return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");

$("#reset-btn").click(function(e) {
  validator.resetForm();
  $('#{{$page->entity}}Form').find("input[type=text], textarea, radio").val("");
  $("#male").prop("checked", true);
});


</script>
@endpush