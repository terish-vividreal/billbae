@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')

@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Create</li>
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
              {!! Form::hidden('user_id', $user->id ?? '' , ['id' => 'user_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('name', $user->name ?? '',  ['id' => 'name']) !!}  
                  <label for="name" class="label-placeholder">Name <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::text('email', $user->email ?? '', array('id' => 'email', 'autocomplete' => 'off')) !!}  
                  <label for="email" class="label-placeholder">Email <span class="red-text">*</span></label> 
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('mobile', $user->mobile ?? '', array('id' => 'mobile', 'class' => 'check_numeric')) !!}
                  <label for="mobile" class="label-placeholder">Mobile <span class="red-text">*</span></label>   
                </div>              
                <div class="input-field col m6 s12">                
                  {!! Form::select('roles[]', $roles , $userRole ?? [] , ['id' => 'roles' ,'class' => 'select2 browser-default', 'multiple' => 'multiple' ]) !!}
                </div>             
              </div>
              <div class="row">
                <div class="input-field col m6 s12">    
                <p> 
                  @if(isset($user))  
                    <label>
                      <input value="1" id="male" name="gender" type="radio" @if($user->gender == 1) checked @endif/>
                      <span> Male </span>
                    </label>             
                    <label>
                      <input value="2" id="female" name="gender" type="radio" @if($user->gender == 2) checked @endif/>
                      <span> Female </span>
                    </label>     
                    <label>
                      <input value="3" id="others" name="gender" type="radio" @if($user->gender == 3) checked @endif/>
                      <span> Others </span>
                    </label>
                  @else
                    <label>
                      <input value="1" id="male" name="gender" type="radio" checked />
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
                  @endif
                  </p>
                  <!-- <label for="gender" class="label-placeholder">Gender </label> -->
                </div>      
                <div class="input-field col m6 s12">
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
$('#roles').select2({ placeholder: "Please select role", allowClear: true });
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
            "roles[]": {
                    required: true,
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
                required: "Please enter name",
                maxlength: "Length cannot be more than 200 characters",
                },
            mobile: {
                required: "Please enter mobile number",
                maxlength: "Length cannot be more than 10 numbers",
                minlength: "Length must be 10 numbers",
                },
            email: {
                required: "Please enter email",
                email: "Please enter a valid email address.",
                remote: "Email already existing"
            },
            "roles[]": {
                required: "Please choose role",
            },
        },
        submitHandler: function (form) {
            $('#submit-btn').html('Please Wait...');
            $("#submit-btn"). attr("disabled", true);
            id = $("#user_id").val();
            user_id      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms = $("#{{$page->entity}}Form");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + user_id, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
              $('#submit-btn').html('Submit <i class="material-icons right">send</i>');
              $("#submit-btn"). attr("disabled", false);
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
        },
    })
}

jQuery.validator.addMethod("lettersonly", function (value, element) {
    return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");
</script>
@endpush

