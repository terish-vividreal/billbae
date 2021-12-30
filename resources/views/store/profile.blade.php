@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2-materialize.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
@endsection
@section('page-css')
<style type="text/css">
  /* img {
    display: block;
    max-width: 100%;
  } */
  .preview {
    overflow: hidden;
    width: 160px; 
    height: 160px;
    margin: 10px;
    border: 1px solid red;
  }
  .modal-box{
    max-width: 1000px !important;
  }
</style>
@endsection

@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/store/profile') }}">{{ $page->title ?? ''}}</a></li>
    <li class="breadcrumb-item active">Update</li>
  </ol>
@endsection

<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsum is used for the ...</p>
    </div>
  </div>
  <div class="card">
    <div class="card-content">
      <div class="row">
        @if($store)
          <div class="col s12" id="account">
            <h4 class="card-title">Store {{ $page->title ?? ''}} Form</h4>
            <div class="media display-flex align-items-center mb-2">
              <a class="mr-2 storlogo" href="javascript:"><img src="{{ $store->show_image }}" alt="users avatar" class="z-depth-4 circle" id="store_logo"></a>
              <div class="media-body">
                <form id="storeLogoForm" name="storeLogoForm" action="" method="POST" enctype="multipart/form-data" class="ajax-submit">
                  {{ csrf_field() }}
                  {!! Form::hidden('store_id', $store->id ?? '' , ['id' => 'store_id'] ); !!}
                  {!! Form::hidden('log_url', $store->show_image, ['id' => 'log_url'] ); !!}
                  <h5 class="media-heading mt-0">Logo</h5>
                  <div class="user-edit-btns display-flex">
                    <a id="select-files" class="btn indigo mr-2"><span>Browse</span></a>
                    <a href="#" class="btn-small btn-light-pink logo-action-btn" id="removeLogoDisplayBtn" style="display:none;">Remove</a>
                    <button type="submit" class="btn btn-success logo-action-btn" id="uploadLogoBtn" style="display:none;">Upload</button>
                  </div>
                  <small>Allowed JPG, JPEG or PNG. Max size of 800kB</small>
                  <div class="upfilewrapper" style="display:none;">
                    <input id="profile" type="file" accept="image/png, image/gif, image/jpeg" name="image" class="image" />
                  </div>
                </form>
              </div>
            </div>
            <!-- users edit media object ends -->
            <!-- users edit account form start -->
            <div class="card-alert card red lighten-5 print-error-msg" style="display:none">
              <div class="card-content red-text">I am sorry, this service is currently not supported in your selected country. In case you wish to use this service in any country other than India, please leave a message in the contact us page, and we shall respond to you at the earliest.</div>
            </div>
            @if (Session::has('error'))
            <div class="card-alert card red lighten-5 print-error-msg">
              <div class="card-content red-text">Few mandatory store details are missing</div>
              <div class="card-content red-text">{!! Session::get('error') !!}</div>
            </div>
            @endif
            <form id="storeProfileForm" name="storeProfileForm" role="form" method="" action="" class="ajax-submit">
              {{ csrf_field() }}
              {!! Form::hidden('store_id', $store->id ?? '' , ['id' => 'store_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('name', $store->name ?? '', array('id' => 'name')) !!} 
                  <label for="name" class="label-placeholder">Store Name <span class="red-text">*</span></label> 
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::text('email', $store->email ?? '', array('id' => 'email')) !!} 
                  <label for="email" class="label-placeholder">Store Email</label> 
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::textarea('address', $store->address ?? '', ['class' => 'materialize-textarea', 'placeholder'=>'Address','rows'=>3]) !!}
                  <label for="address" class="label-placeholder">Address</label> 
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('country_id', $variants->countries , $store->country_id ?? '' , ['id' => 'country_id' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select country']) !!}
                  <label for="country_id" class="label-placeholder active">Store country</label> 
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  <div id="timezone_block"> 
                    @if(!empty($variants->timezone))
                      {!! Form::select('timezone', $variants->timezone , $store->timezone ?? '' , ['id' => 'timezone' ,'class' => 'select2 browser-default','placeholder'=>'Please select timezone']) !!}
                    @else
                      {!! Form::select('timezone', [] , '', ['id' => 'timezone' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select timezone']) !!}
                    @endif
                    <label for="timezone" class="label-placeholder active">Store timezone</label> 
                  </div>
                </div>
                <div class="input-field col m6 s12">
                  @if(!empty($variants->states))
                    {!! Form::select('state_id', $variants->states , $store->state_id ?? '' , ['id' => 'state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                  @else
                    {!! Form::select('state_id', [] , '' , ['id' => 'state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                  @endif
                  <label for="state_id" class="label-placeholder active">Store state</label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::select('time_format', [1 => '12 Format', 2 => '24 Format'] , $store->time_format ?? '' , ['id' => 'time_format']) !!}
                  <label for="time_format" class="label-placeholder active">Time Format</label> 
                </div>
                <div class="input-field col m6 s12">
                  @if(!empty($variants->districts))
                    {!! Form::select('district_id', $variants->districts , $store->district_id ?? '' , ['id' => 'district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                  @else
                    {!! Form::select('district_id', [] , '' , ['id' => 'district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                  @endif
                  <label for="district_id" class="label-placeholder active">Store district</label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('pincode', $store->pincode ?? '' , array('id' => 'pincode', 'placeholder' => 'Pincode','class' => 'check_numeric')) !!}
                  <label for="pincode" class="label-placeholder">Pincode</label> 
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::text('contact', $store->contact ?? '', array('id' => 'contact', 'placeholder' => 'Contact Number','class' => 'form-control check_numeric')) !!}
                  <label for="contact" class="label-placeholder">Contact</label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                {!! Form::text('location', $store->location ?? '', array('placeholder' => 'Store location','id' => 'location')) !!}
                <label for="location" class="label-placeholder">Location</label> 
                </div>
                <div class="input-field col m6 s12">
                {!! Form::text('map_location', $store->map_location ?? '', array('placeholder' => 'Map location','id' => 'map_location')) !!}
                <label for="map_location" class="label-placeholder">Map location</label> 
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                {!! Form::textarea('about', $store->about ?? '', ['id'=>'about', 'class' => 'materialize-textarea', 'placeholder'=>'About']) !!}
                <label for="about" class="label-placeholder">About</label> 
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light" type="reset" name="reset">Reset <i class="material-icons right">refresh</i></button>
                  <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="store-profile-submit-btn">Submit <i class="material-icons right">send</i></button>
                </div>
              </div>
            </form>
            <!-- users edit account form ends -->
          </div>
        @endif
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@include('layouts.crop-modal') 
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/toastr/toastr.min.js')}}"></script>
<script src="{{asset('admin/vendors/select2/select2.full.min.js')}}"></script>
@endsection

@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- date-time-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<!-- <script src="{{ asset('admin/js/cropper-script.js') }}"></script> -->
<script src="{{asset('admin/js/scripts/page-users.js')}}"></script>
<script>
  $('#profile').change(function() {   
    var ext = $('#profile').val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['png','jpg','jpeg']) == -1) {
      showErrorToaster("Invalid format. Allowed JPG, JPEG or PNG.");
    } else {
      let reader = new FileReader();
      reader.onload = (e) => { 
        $('#store_logo').attr('src', e.target.result); 
        $(".logo-action-btn").show();
      }
      reader.readAsDataURL(this.files[0]); 
    }    
  });

  $("#removeLogoDisplayBtn").click(function(event) {
    event.preventDefault();
    var old_logo = $("#log_url").val();
    $("#store_logo").attr("src", old_logo); 
    $(".logo-action-btn").hide();
  });

  $('#storeLogoForm').submit(function(e) {
    var formData = new FormData(this);
    $.ajax({ type: "POST",url: "{{ url('/store/update-logo') }}", data: formData, cache:false, contentType: false, processData: false,
      success: function(data) {
        if (data.flagError == false) {
          showSuccessToaster(data.message);                 
          $("#store_logo").attr("src", data.logo);
        } else {
          showErrorToaster(data.message);
          printErrorMsg(data.error);
        }
      }
    });
  });

  $('#country_id').select2({ placeholder: "Please select country", allowClear: true });
  $('#state_id').select2({ placeholder: "Please select state", allowClear: true });
  $('#district_id').select2({ placeholder: "Please select district", allowClear: true });
  $('#timezone').select2({ placeholder: "Please select timezone", allowClear: true });
  $('#currency').select2();

  $(document).on('change', '#country_id', function () {
    if (this.value != 101) {
      $("#submit-btn").prop('disabled', true);
      showErrorToaster("Currently not supported in your selected country!");
      $(".print-error-msg").show();
    } else {
      $("#submit-btn").prop('disabled', false);
      $(".print-error-msg").hide();
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

      $.ajax({ type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-timezone') }}", data:{'country_id':this.value }, dataType: 'json',
        success: function(data) {
          var selectTerms = '<option value="">Please select timezone</option>';
          $.each(data.data, function(key, value) {
            selectTerms += '<option value="' + value.zone_name + '" >' + value.zone_name + '</option>';
          });
          var select = $('#timezone');
          select.empty().append(selectTerms);
        }
      });
    }
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

  if ($("#storeProfileForm").length > 0) {
    var validator = $("#storeProfileForm").validate({ 
      rules: {
        name: {
          required: true,
          maxlength: 200,
        }, 
        country_id: {
          required: true,
        },
        timezone: {
          required: true,
        },
        email: {
          email: true,
          remote: { url: "{{ url('/store/unique') }}", type: "POST",
            data: {
              store_id: function () {
                return $('#store_id').val();
              }
            }
          },
        },
      },
      messages: { 
        name: {
          required: "Please enter store name",
          maxlength: "Length cannot be more than 200 characters",
        },
        country_id: {        
          required: "Please choose country",
        },
        timezone: {
          required: "Please choose timezone",
        },
        email: {
          email: "Please enter a valid email address.",
          remote: "Email already existing"
        },
      },
      submitHandler: function (form) {
        disableBtn('store-profile-submit-btn');
        id          = $("#store_id").val();
        userId      = "" == id ? "" : "/" + id;
        formMethod  = "" == id ? "POST" : "PUT";
        var forms   = $("#storeProfileForm");
        $.ajax({ url: "{{ url('/store/update') }}" + userId, type: formMethod, processData: false, data: forms.serialize(), dataType: "html",
        }).done(function (a) {
          var data = JSON.parse(a);
          // enableBtn('store-profile-submit-btn');
          if(data.flagError == false) {
            showSuccessToaster(data.message);
            setTimeout(function () { 
              window.location.href = "{{ url('store/profile')}}";                    
            }, 3000);
          } else {
            showErrorToaster(data.message);
            printErrorMsg(data.error);
          }
        });
      },
      errorPlacement: function(error, element) {
        if (element.is("select")) {
          error.insertAfter(element.next('.select2'));
        } else {
          error.insertAfter(element);
        }
      }
    })
  } 

  $("#select-files").on("click", function () {
    $("#profile").click();
  })
</script>
@endpush