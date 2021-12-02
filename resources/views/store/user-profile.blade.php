@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-account-settings.css')}}">
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
img {
  max-width: 100%; /* This rule is very important, please do not ignore this! */
}
.cropper-wrap-box, .cropper-canvas{
  transform: translateY(0) !important;
}
.cropper-container.cropper-bg{
  width: 766px !important;
  height: 766px !important;
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
<div class="section users-edit section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsum is used for the ...</p>
    </div>
  </div>
  <div class="card">
    <div class="card-content">
      <ul class="tabs mb-2 row">
        <li class="tab">
          <a class="display-flex align-items-center active" id="account-tab" href="#account">
            <i class="material-icons mr-1">account_circle</i><span> User Profile</span>
          </a>
        </li>
        <li class="tab">
          <a class="display-flex align-items-center" id="information-tab" href="#additionalTaxes">
            <i class="material-icons mr-2">lock_open</i><span>Change Password</span>
          </a>
        </li>
      </ul>

      <div class="divider mb-3"></div>
      <div class="row">
        @if($user)
          <div class="col s12" id="account">
            <div class="media display-flex align-items-center mb-2">
              @php
                $user_profile = ($user->profile != null) ? asset('storage/store/users/' . $user->profile) : asset('admin/images/user-icon.png');
              @endphp
              <a class="mr-2" href="#">
                <img src="{{$user_profile}}" class="border-radius-4" alt="profile image" id="user_profile" height="64" width="64">
              </a>
              <div class="media-body">
                <form id="storeAdminImageForm" name="storeAdminImageForm" action="" method="POST" enctype="multipart/form-data" class="ajax-submit">
                  {{ csrf_field() }}
                  {!! Form::hidden('user_id', $user->id ?? '' , ['id' => 'user_id'] ); !!}
                  {!! Form::hidden('user_photo_url', $user_profile, ['id' => 'user_photo_url'] ); !!}
                  <h5 class="media-heading mt-0">Admin Photo</h5>
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
                <!-- <h5 class="media-heading mt-0">Admin Photo</h5>
                <div class="user-edit-btns display-flex">
                  <button id="select-files" class="btn indigo mr-2"><span>Browse</span></button>
                  <a href="#" class="btn-small btn-light-pink">Remove</a>
                </div>
                <small>Allowed JPG, JPEG or PNG. Max size of 800kB</small>
                <div class="upfilewrapper" style="display:none;">
                  <input id="profile" type="file" accept="image/png, image/gif, image/jpeg" name="image" class="image" />
                </div> -->
              </div>
            </div>
            <!-- users edit account form start -->
            <h4 class="card-title">Store Admin {{ $page->title ?? ''}} Form</h4>
            <form id="userProfileForm" name="userProfileForm" role="form" method="" action="" class="ajax-submit">
              {{ csrf_field() }}
              {!! Form::hidden('user_id', $user->id ?? '' , ['id' => 'user_id'] ); !!}
              <div class="row">
                <div class="col s6">
                  <div class="input-field">
                    {!! Form::text('name', $user->name ?? '', array('id' => 'name')) !!} 
                    <label for="name" class="label-placeholder">Admin Name <span class="red-text">*</span></label>
                  </div>
                </div>
                <div class="col s6">
                  <div class="input-field">
                    {!! Form::text('email', $user->email ?? '') !!} 
                    <label for="email" class="label-placeholder">Store Email <span class="red-text">*</span></label>
                    <small class="errorTxt2"></small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col s6">
                  <div class="input-field">
                    {!! Form::text('mobile', $user->mobile ?? '',  ['class' => 'check_numeric']) !!} 
                    <label for="mobile" class="label-placeholder">Store Mobile <span class="red-text">*</span></label>
                    <small class="errorTxt2"></small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col s12 display-flex justify-content-end form-action">
                  <button type="submit" class="btn indigo waves-effect waves-light mr-2">Save changes</button>
                  <button type="button" class="btn btn-light-pink waves-effect waves-light mb-1">Cancel</button>
                </div>
              </div>
            </form>
            <!-- users edit account form ends -->
          </div>
          <div class="col s12" id="additionalTaxes">
            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
            <h4 class="card-title">Update Password </h4>
            {!! Form::open(['url' => $page->form_url, 'method' => $page->form_method, 'class'=>'ajax-submit','id'=>'changepasswordForm']) !!}
             <div class="row">
              <div class="col s12">
                <div class="input-field">
                  {!! Form::password('old_password',  ['class' => 'form-control', 'id' => 'old_password']) !!}
                  <label for="old_password" class="label-placeholder">Old Password<span class="red-text">*</span></label> 
                </div>
              </div>
              <div class="col s12">
                <div class="input-field">
                  {!! Form::password('new_password',  ['class' => 'form-control',  'id' => 'new_password']) !!}
                  <label for="new_password" class="label-placeholder">New Password<span class="red-text">*</span></label> 
                </div>
              </div>
              <div class="col s12">
                <div class="input-field">
                  {!! Form::password('new_password_confirmation', ['id' => 'new_password_confirmation', 'class' => 'form-control']) !!}
                  <label for="new_password_confirmation" class="label-placeholder">Confirm Password<span class="red-text">*</span></label>
                </div>
              </div>
              <div class="col s12 display-flex justify-content-end form-action">
                <button type="submit" class="btn indigo waves-effect waves-light mr-1">Save changes</button>
                <button type="reset" class="btn btn-light-pink waves-effect waves-light">Cancel</button>
              </div>
             </div>

             <div class="row">
                <div class="col s12 display-flex justify-content-end form-action">
                  <button type="submit" class="btn indigo waves-effect waves-light mr-2">Save changes</button>
                  <button type="button" class="btn btn-light-pink waves-effect waves-light mb-1">Cancel</button>
                </div>
              </div>
             {!! Form::close() !!}
          </div>
        @endif
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
@include('layouts.crop-modal') 

@endsection
{{-- vendor scripts --}}
@section('vendor-script')

@endsection

@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- <script src="{{asset('admin/js/scripts/page-users.js')}}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<script src="{{ asset('admin/js/cropper-script.js') }}"></script>
<script>

  $("#profileImageSubmitBtn").click(function(){
    canvas = cropper.getCroppedCanvas({
      width:400,
      height:700,
      viewport: {
        width: 600,
        height: 300,
        type:'circle'
      },
    });
    canvas.toBlob(function(blob) {
      url = URL.createObjectURL(blob);
      var reader = new FileReader();
      reader.readAsDataURL(blob); 
      reader.onloadend = function() {
        var base64data = reader.result; 
        id = $("#store_id").val();
        $.ajax({
          type: "POST",
          dataType: "json",
          url: "{{ url('/store/update-user-image') }}",
          data: {store_id : id , 'image': base64data},
          success: function(data) {
            if (data.flagError == false) {
              showSuccessToaster(data.message);                 
              $("#user_profile").attr("src", data.logo);
              $("#log_user_icon").attr("src", data.logo);
              $modal.modal('close');
            } else {
              showErrorToaster(data.message);
              printErrorMsg(data.error);
            }
          }
        });
      }
    });
  })

  if ($("#userProfileForm").length > 0) {
    var validator = $("#userProfileForm").validate({ 
      rules: {
        name: {
          required: true,
          maxlength: 200,
        }, 
        mobile: {
          required: true,
          maxlength: 10,
        },
        email: {
          required: true,
          email: true,
          remote: { url: "{{ url(ROUTE_PREFIX.'/users/unique') }}", type: "POST",
            data: {
              user_id: function () {
                return $('#user_id').val();
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
        mobile: {
          required: "Please enter mobile",
          maxlength: "Length cannot be more than 10 characters",
        },
        email: {
          email: "Please enter a valid email address.",
          remote: "Email already existing"
        },
      },
      submitHandler: function (form) {
        var forms   = $("#userProfileForm");
        $.ajax({ url: "{{ url('/store/user-profile') }}", type: "POST", processData: false, 
        data: forms.serialize(), dataType: "html",
        }).done(function (a) {
          var data = JSON.parse(a);
          if (data.flagError == false) {
            showSuccessToaster(data.message);
            setTimeout(function () { 
              window.location.href = "{{ url('store/user-profile')}}";                    
            }, 3000);
          } else {
            showErrorToaster(data.message);
            printErrorMsg(data.error);
          }
        });
      }
    })
  } 

  if ($("#changepasswordForm").length > 0) {
    var changePasswordFormValidator = $("#changepasswordForm").validate({ 
      rules: {
        old_password: {
          required: true,
        },
        new_password: {
          required: true,
          minlength: 6,
          maxlength: 10,
        },
        new_password_confirmation: {
          equalTo: "#new_password"
        },
      },
      messages: { 
        old_password: {
          required: "Please enter password",
        },
        new_password: {
          required: "Please enter password",
          minlength: "Passwords must be at least 6 characters in length",
          maxlength: "Length cannot be more than 10 characters",
        },
        new_password_confirmation: {
          equalTo: "Passwords are not matching",
        }
      },
      submitHandler: function (form) {
        var forms   = $("#changepasswordForm");
        $.ajax({ url: "{{ url(ROUTE_PREFIX.'/users/update-password') }}", type: 'POST', processData: false, 
        data: forms.serialize(), dataType: "html",
        }).done(function (a) {
          var data = JSON.parse(a);
          if (data.flagError == false) {
            showSuccessToaster(data.message);
            // setTimeout(function () { 
            //     window.location.href = "{{ url('admin/stores')}}";                    
            // }, 3000);
          } else {
            showErrorToaster(data.message);
            printErrorMsg(data.error);
          }
        });
      }
    })
  }

  // $("#removeLogoDisplayBtn").click(function(event){
  //   event.preventDefault();
  //   var old_logo = $("#user_photo_url").val();
  //   $("#user_profile").attr("src", old_logo); 
  //   $(".logo-action-btn").hide();
  // });

  // $('#storeLogoForm').submit(function(e) {
  //   var formData = new FormData(this);
  //   $.ajax({ type: "POST",url: "{{ url('/store/update-logo') }}", data: formData, cache:false, contentType: false, processData: false,
  //     success: function(data) {
  //       if(data.flagError == false) {
  //         showSuccessToaster(data.message);                 
  //         $("#store_logo").attr("src", data.logo);
  //       } else {
  //         showErrorToaster(data.message);
  //         printErrorMsg(data.error);
  //       }
  //     }
  //   });
  // });
  
</script>
@endpush

