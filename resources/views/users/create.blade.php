@extends('layouts.app')

@section('content')
@push('page-css')
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
              <form id="userForm" name="userForm" role="form" method="" action="" class="ajax-submit">
                {{ csrf_field() }}
                {!! Form::hidden('user_id', $user->id ?? '' , ['id' => 'user_id'] ); !!}
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                          {!! Form::label('shop_name', 'Store Name*', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('shop_name', $you->shop->name ?? '' , array('placeholder' => 'Store Name','class' => 'form-control', 'disabled')) !!}                        
                          <div class="error" id="email_error"></div>
                      </div>  
                      <div class="form-group">
                          {!! Form::label('email', 'Email*', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('email', $user->email ?? '' , array('placeholder' => 'Email','class' => 'form-control')) !!}
                          <div class="error" id="email_error"></div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('password', 'Password*', ['class' => 'col-form-label text-alert']) !!}
                        {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                        <div class="error" id="password_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('confirm-password', 'Confirm Password*', ['class' => 'col-form-label text-alert']) !!}
                        {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                        <div class="error" id="password_error"></div>
                    </div>
                      


                    </div>  
                    <div class="col-md-6">                      
                      <div class="form-group">
                        {!! Form::label('name', 'User Name*', ['class' => 'col-form-label text-alert']) !!}
                        {!! Form::text('name', $user->name ?? '', array('placeholder' => 'Admin Name','class' => 'form-control')) !!}
                        <div class="error" id="name_error"></div>
                      </div>
                      <div class="form-group">
                          {!! Form::label('mobile', 'Mobile Number*', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('mobile', $user->mobile ?? '', array('placeholder' => 'Mobile','class' => 'form-control check_numeric')) !!}
                          <div class="error" id="name_error"></div>
                      </div>
                      <div class="form-group">
                        {!! Form::label('roles', 'Role*', ['class' => 'col-form-label text-alert']) !!}
                        {!! Form::select('roles[]', $roles, $userRole ?? [] , ['id' => 'additional_tax', 'multiple' => 'multiple' ,'class' => 'form-control col-sm-12']) !!}
                        <div class="error" id="roles_error"></div>
                    </div>

                    </div>            
                  </div>
                
                <div class="row">
                    <div class="col-12">
                    <a href="#" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success ajax-submit" id="submit-btn">Submit</button>
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
$('#additional_tax').select2({ placeholder: "Please choose role", allowClear: false });

if ($("#userForm").length > 0) {
    var validator = $("#userForm").validate({ 
        rules: {
            shop_name: {
                    required: true,
                    maxlength: 200,
            }, 
            // email: {
            //     required: true,
            //     email: true,
            //     remote: { url: "{{ url(ROUTE_PREFIX.'/users/unique') }}", type: "POST",
            //         data: {
            //             user_id: function () {
            //                 return $('#user_id').val();
            //             }
            //         }
            //     },
            // },
            // password: {
            //     required: true,
            //     minlength: 6,
            //     maxlength: 10,
            // },
            // password_confirmation: {
            //     equalTo: "#password"
            // },
        },
        messages: { 
            shop_name: {
                required: "Please enter store name",
                maxlength: "Length cannot be more than 200 characters",
                },
            email: {
                required: "Please enter email",
                email: "Please enter a valid email address.",
                remote: "Email already existing"
            },
            password: {
                required: "Please enter password",
                minlength: "Passwords must be at least 6 characters in length",
                maxlength: "Length cannot be more than 10 characters",
            },
            password_confirmation: {
                equalTo: "Passwords are not matching",
            },
        },
        submitHandler: function (form) {
          $('#submit-btn').html('Please Wait...');
          $("#submit-btn"). attr("disabled", true);
            id = $("#user_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#userForm");
            $.ajax({ url: "{{ url(ROUTE_PREFIX.'/users') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {              
                $('#submit-btn').html('Submit');
                $("#submit-btn"). attr("disabled", false); 
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url(ROUTE_PREFIX.'/users')}}";                    
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
