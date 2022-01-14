@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2-materialize.css')}}">
@endsection

@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/form-select2.css')}}">
@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/customers') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Edit</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/customers') }}" class="btn waves-effect waves-light light-blue darken-4 breadcrumbs-btn" type="submit" name="action">List<i class="material-icons right">list</i></a>
@endsection


<div class="seaction">
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
                  <label for="email" class="label-placeholder active">Email </label>
                </div>
              </div>

              <div class="row">
                <div class="col s2">
                  <div class="input-field">                  
                  {!! Form::select('phone_code', $variants->phonecode , $customer->phone_code ? : $store->country_id ?? '' , ['id' => 'phone_code', 'class' => 'select2 browser-default', 'placeholder'=>'Please select phone code']) !!}
                  <label for="phone_code" class="label-placeholder active">Phone code </label>
                  </div>
                </div>
                <div class="input-field col m4 s12">
                  {!! Form::text('mobile', $customer->mobile ?? '', array('id' => 'mobile')) !!}  
                  <label for="mobile" class="label-placeholder active">Mobile </label>
                </div>              
                <div class="input-field col m6 s12">                
                <label for="gender" class="label-placeholder active">DOB </label>              
                  <p style="margin-top: 23px;">
                    <label>
                      <input value="1" id="male" name="gender" type="radio"  @if($customer->gender == 1) checked @endif/>
                      <span> Male </span>
                    </label>             
                    <label>
                      <input value="2" id="female" name="gender" type="radio"  @if($customer->gender == 2) checked @endif/>
                      <span> Female </span>
                    </label>     
                    <label>
                      <input value="3" id="others" name="gender" type="radio" @if($customer->gender == 3) checked @endif />
                      <span> Others </span>
                    </label>
                  </p>
                </div>             
              </div>

              <div class="row">
                <div class="input-field col m6 s12">
                  @php                             
                    $dob = ($customer->dob != '') ? $customer->dob->format('d/m/Y') : Carbon\Carbon::now()->format('d/m/Y');
                  @endphp
                  <input type='text' name="dob" id="dob" onkeydown="return false" class="" value="{{$dob}}" autocomplete="off" />
                  <label for="dob" class="label-placeholder active">DOB </label>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('country_id', $variants->countries , $customer->country_id ?: $store->country_id ?? '' , ['id' => 'country_id' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select country']) !!}
                  <label for="country_id" class="label-placeholder active">Country </label>
                </div>
              </div>

              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('pincode', $customer->pincode ?? '' , array('placeholder' => 'Pincode', 'class' => 'check_numeric')) !!}  
                  <label for="pincode" class="label-placeholder active">Pincode </label>  
                </div>
                <div class="input-field col m6 s12">
                  <div id="state_block">
                    @if(!empty($variants->states))
                      {!! Form::select('state_id', $variants->states , $customer->state_id ?? '' , ['id' => 'state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                    @else
                      {!! Form::select('state_id', [] , '' , ['id' => 'state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                    @endif
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="input-field col m6 s12">  
                  {!! Form::text('gst', $customer->gst ?? '' , array('placeholder' => 'GST No')) !!} 
                  <label for="gst" class="label-placeholder active">GST No </label>  
                </div>
                <div class="input-field col m6 s12">
                  <div id="state_block">
                    @if(!empty($variants->districts))
                      {!! Form::select('district_id', $variants->districts , $customer->district_id ?? '' , ['id' => 'district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                    @else
                      {!! Form::select('district_id', [] , '' , ['id' => 'district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                    @endif
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="input-field col m12 s12">
                  {!! Form::textarea('address', $customer->address ?? '', ['class' => 'materialize-textarea', 'placeholder'=>'Address','rows'=>3]) !!}
                  <label for="address" class="label-placeholder active">Address</label>  
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light" type="button" id="reset-btn" name="reset">Reset <i class="material-icons right">refresh</i></button>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{asset('admin/js/scripts/form-select2.js')}}"></script>
<script>


$(document).ready(function(){

  $('#country_id').select2({ placeholder: "Please select country", allowClear: true });
  $('#state_id').select2({ placeholder: "Please select state", allowClear: true });
  $('#district_id').select2({ placeholder: "Please select district", allowClear: true });

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
      phone_code: {
        // required: true,
      },
      mobile:{
        // required:true,
        minlength:3,
        maxlength:15,
        digits:true,
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
        if(data.flagError == false) {
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


$(document).on('change', '#country_id', function () {
  $.ajax({ type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-states-of-country') }}", data:{'country_id':this.value }, dataType: 'json',
    success: function(data) {
      var selectTerms = '<option value="">Please select state</option>';
      $.each(data.data, function(key, value) {
        selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
      });
      var select = $('#state_id');
      select.empty().append(selectTerms);
      $('#district_id').empty().trigger("change");
    }
  });
});

$(document).on('change', '#state_id', function () {
  $.ajax({ type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-districts-of-state') }}", data:{'state_id':this.value }, dataType: 'json',
    success: function(data) {
      var selectTerms = '<option value="">Please select district</option>';
      $.each(data.data, function(key, value) {
        selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
      });
      var select = $('#district_id');
      select.empty().append(selectTerms);
    }
  });
});

$("#reset-btn").click(function(e) {
  validator.resetForm();
  $('#{{$page->entity}}Form').find("input[type=text], textarea, radio").val("");
  $("#male").prop("checked", true);
  $("#country_id").val('').trigger('change');
  $("#phone_code").val('').trigger('change');
});

</script>
@endpush

