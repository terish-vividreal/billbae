@extends('layouts.app')

@section('content')

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


              <form id="storeForm" name="storeForm" role="form" method="" action="" class="ajax-submit">
                {{ csrf_field() }}
                {!! Form::hidden('user_id', $user->id ?? '' , ['id' => 'user_id'] ); !!}
                <div class="col-md-8 ">  

                    <div class="form-group">
                        {!! Form::label('shop_name', 'Store Name*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::text('shop_name', $user->shop->name ?? '' , array('placeholder' => 'Store Name','class' => 'form-control')) !!}                        
                        <div class="error" id="email_error"></div>
                    </div>             
                    <div class="form-group">
                        {!! Form::label('name', 'Admin Name*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::text('name', $user->name ?? '', array('placeholder' => 'Admin Name','class' => 'form-control')) !!}
                        <div class="error" id="name_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', 'Email*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::text('email', $user->email ?? '' , array('placeholder' => 'Email','class' => 'form-control')) !!}
                        <div class="error" id="email_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mobile', 'Mobile Number*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::text('mobile', $user->mobile ?? '', array('placeholder' => 'Mobile','class' => 'form-control check_numeric')) !!}
                        <div class="error" id="name_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', 'Password*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                        <div class="error" id="password_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('confirm-password', 'Confirm Password*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                        <div class="error" id="password_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('roles', 'Role*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::select('roles[]', $roles, $userRole ??  [] , array('class' => 'form-control','multiple')) !!}
                        <div class="error" id="roles_error"></div>
                    </div>                  
                    
                </div>
                <div class="row">
                    <div class="col-12">
                    <a href="#" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success ajax-submit">Submit</button>
                    </div>
                </div>
              {!! Form::close() !!}              

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
<script type="text/javascript">

if ($("#storeForm").length > 0) {
    var validator = $("#storeForm").validate({ 
        rules: {
            shop_name: {
                    required: true,
                    maxlength: 200,
            }, 
            // email: {
            //     required: true,
            //     email: true,
            //     remote: { url: "{{ url('admin/users/unique') }}", type: "POST",
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
            department_id: {
                required: "Please choose department"
            },
        },
        submitHandler: function (form) {
            id = $("#user_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#storeForm");
            $.ajax({ url: "{{ url('admin/stores') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url('admin/stores')}}";                    
                    }, 3000);

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
