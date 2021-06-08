@extends('layouts.app')

@section('content')
@push('page-css')
<style>
.profile-pic {
	position: relative;
	display: inline-block;
}

.profile-pic:hover .edit {
	display: block;
}

.edit {
	padding-top: 7px;	
	padding-right: 7px;
	position: absolute;
	right: 0;
	top: 0;
	display: none;
}

.edit a {
	color: #000;
}
</style>
@endpush

@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url('user/home') }}" class="nav-link">Home</a>
  </li>
  <!-- <li class="nav-item d-none d-sm-inline-block">
    <a href="#" class="nav-link">Contact</a>
  </li> -->
@endsection

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ $page->title ?? ''}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('user/home') }}">Home</a></li>
              <li class="breadcrumb-item active">{{ $page->title ?? ''}} Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            @if($store)
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                <!-- asset('storage/store/logo/0502b856ec0bd08f9d6b19a11cc264ccjpg') -->
                  <img class="profile-user-img img-fluid img-circle profile-pic" id="store_logo"
                       src="{{ $store->show_image }}" 
                       alt="User profile picture">
                       <div class=""><a href="javascript:" data-id="1" id="updateProfile">Change Logo<i class="fa fa-pencil"></i></a></div>
                      
                </div> 

                <h3 class="profile-username text-center">{{ $store->name ?? '' }}</h3>

                <p class="text-muted text-center">{{ $store->business_types->name ?? '' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Employees</b> <a class="float-right">{{ count($store->users) }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Customers</b> <a class="float-right">{{ count($store->customer) }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Services</b> <a class="float-right">{{ count($store->service) }}</a>
                  </li>
                </ul>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Store Profile</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Email</strong>

                <p class="text-muted">
                {{ $store->email ?? '' }}
                </p>

                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                <p class="text-muted">{{ $store->state ?? '' }} {{ $store->district ?? '' }} , {{ $store->location ?? '' }}</p>

                <hr>

                <!-- <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                <p class="text-muted">
                  <span class="tag tag-danger">UI Design</span>
                  <span class="tag tag-success">Coding</span>
                  <span class="tag tag-info">Javascript</span>
                  <span class="tag tag-warning">PHP</span>
                  <span class="tag tag-primary">Node.js</span>
                </p>

                <hr> -->

                <strong><i class="far fa-file-alt mr-1"></i> Address </strong>

                <p class="text-muted">{{ $store->address ?? '' }}</p>
                <p class="text-muted">{{ $store->pincode ?? '' }}</p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            @endif
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Store Profile</a></li>
                  <li class="nav-item"><a class="nav-link" href="#billingtab" data-toggle="tab">Billing Details</a></li>
                  <!-- <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li> -->
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="profile">
                    <h3>Store Profile Form</h3><br>

                    <form id="storeProfileForm" name="storeProfileForm" role="form" method="" action="" class="ajax-submit">
                      {{ csrf_field() }}
                      {!! Form::hidden('store_id', $store->id ?? '' , ['id' => 'store_id'] ); !!}
                      <div class="col-md-8 ">  

                          <div class="form-group">
                              {!! Form::label('name', 'Store Name*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                              {!! Form::text('name', $store->name ?? '' , array('placeholder' => 'Store Name','class' => 'form-control')) !!}                        
                              <div class="error" id="email_error"></div>
                          </div> 

                          <div class="form-group ">
                              {!! Form::label('address', 'Address. ', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                              {!! Form::textarea('address', $store->address ?? '', ['class' => 'form-control','placeholder'=>'Address', 'rows' => '4']) !!}                       
                          </div>

                          <div class="form-group">
                            {!! Form::label('state_id', 'State *', ['class' => '']) !!}
                            {!! Form::select('state_id', $variants->states , $store->state_id ?? '' , ['id' => 'state_id' ,'class' => 'form-control','placeholder'=>'Select a state']) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('district_id', 'District*', ['class' => '']) !!}
                            <div id="district_block">
                            @if($store->district_id)
                              {!! Form::select('district_id', $variants->districts , $store->district_id ?? '' , ['id' => 'district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @else  
                              {!! Form::select('district_id', [] , '' , ['id' => 'district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @endif

                             </div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('pincode', 'Pincode ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('pincode', $store->pincode ?? '' , array('placeholder' => 'Pincode','class' => 'form-control check_numeric')) !!}
                              <div class="error" id="email_error"></div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('pin', 'PIN ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('pin', $store->pin ?? '' , array('placeholder' => 'PIN','class' => 'form-control')) !!}
                              <div class="error" id="email_error"></div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('email', 'Email*', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('email', $store->email ?? '' , array('placeholder' => 'Email','class' => 'form-control')) !!}
                              <div class="error" id="email_error"></div>
                          </div>
                          <div class="form-group">
                              {!! Form::label('contact', 'Contact*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::text('contact', $store->contact ?? '', array('placeholder' => 'Contact Number','class' => 'form-control check_numeric')) !!}
                              <div class="error" id="name_error"></div>
                          </div> 
                          <div class="form-group">
                              {!! Form::label('location', 'Store location*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                              {!! Form::text('location', $store->location ?? '', array('placeholder' => 'Store location','class' => 'form-control')) !!}
                              <div class="error" id="name_error"></div>
                          </div>   
                          <div class="form-group">
                              {!! Form::label('about', 'About', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::textarea('about', $store->about ?? '', ['id' => 'about', 'rows' => 4, 'cols' => 10, 'class' => 'form-control']) !!}
                              <div class="error" id="name_error"></div>
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
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="billingtab">
                  <h3>Store Billing Form</h3><br>
                    <form id="storeBillingForm" name="storeBillingForm" role="form" method="" action="" class="ajax-submit">
                      {{ csrf_field() }}
                      {!! Form::hidden('billing_id', $billing->id ?? '' , ['id' => 'billing_id'] ); !!}
                      <div class="col-md-8 ">  

                          <div class="form-group">
                              {!! Form::label('company_name', 'Company Name*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                              {!! Form::text('company_name', $billing->company_name ?? '' , array('placeholder' => 'Company Name','class' => 'form-control')) !!}                        
                              <div class="error" id="email_error"></div>
                          </div> 

                          <div class="form-group ">
                              {!! Form::label('address', 'Address. ', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                              {!! Form::textarea('address', $billing->address ?? '', ['class' => 'form-control','placeholder'=>'Address', 'rows' => '4']) !!}                       
                          </div>

                          <div class="form-group">
                              {!! Form::label('pincode', 'Pincode ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('pincode', $billing->pincode ?? '' , array('placeholder' => 'Pincode','class' => 'form-control check_numeric')) !!}
                              <div class="error" id="email_error"></div>
                          </div>

                          <div class="form-group">
                            {!! Form::label('billing_state_id', 'State *', ['class' => '']) !!}
                            {!! Form::select('billing_state_id', $variants->states , $billing->state_id ?? '' , ['id' => 'billing_state_id' ,'class' => 'form-control','placeholder'=>'Select a state']) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('billing_district_id', 'District*', ['class' => '']) !!}
                            <div id="billing_district_block">
                            @if($billing->district_id)
                              {!! Form::select('billing_district_id', $variants->billing_districts , $billing->district_id ?? '' , ['id' => 'billing_district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @else  
                              {!! Form::select('billing_district_id', [] , '' , ['id' => 'billing_district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @endif

                             </div>
                          </div>                          

                          <div class="form-group">
                              {!! Form::label('in', 'PIN ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('pin', $billing->pin ?? '' , array('placeholder' => 'PIN','class' => 'form-control')) !!}
                              <div class="error" id="email_error"></div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('gst', 'GST ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::text('gst', $billing->gst ?? '' , array('placeholder' => 'GST','class' => 'form-control')) !!}
                              <div class="error" id="email_error"></div>
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
                  <!-- /.tab-pane -->

                  <!-- <div class="tab-pane" id="settings">
                    
                  </div> -->
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@include('store.image-manage') 
@endsection
@push('page-scripts')

<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
    $("#updateProfile").on("click", function(){
      profilrvalidator.resetForm();
      $("#profileForm .form-control").removeClass("error");
      $("#imgPreviewDiv").hide();
      $("#profile-modal").modal("show");
        
    });
});

image.onchange = evt => {
  const [file] = image.files
  if (file) {
    imgPreview.src = URL.createObjectURL(file)
    $("#imgPreviewDiv").show();
  }
}


if ($("#profileForm").length > 0) {
    var profilrvalidator = $("#profileForm").validate({ 
        rules: {
          image: {
                    required: true,
                    extension: "jpeg|jpg|png",
            },
        },
        messages: { 
          image: {
                required: "Please choose image",
                extension: "Invalid format",
                },
        },
        submitHandler: function (form) {
            var forms   = $("#profileForm");
            let formData = new FormData(form);

            $.ajax({ url: "{{ url('/store/update-logo') }}", type: "post", processData: false, contentType: false,
            data: formData,
            }).done(function (data) {
                // var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);                 
                    $("#store_logo").attr("src", data.logo);
                    $("#profile-modal").modal("hide");
                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
} 


if ($("#storeProfileForm").length > 0) {
    var validator = $("#storeProfileForm").validate({ 
        rules: {
            name: {
                    required: true,
                    maxlength: 200,
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
            shop_name: {
                required: "Please enter store name",
                maxlength: "Length cannot be more than 200 characters",
                },
            email: {
                email: "Please enter a valid email address.",
                remote: "Email already existing"
            },
        },
        submitHandler: function (form) {
            id = $("#store_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#storeProfileForm");
            $.ajax({ url: "{{ url('/store/update') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url('store/profile')}}";                    
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


if ($("#storeBillingForm").length > 0) {
    var validator = $("#storeBillingForm").validate({ 
        rules: {
          company_name: {
                    maxlength: 200,
            }
        },
        messages: { 
          company_name: {
                maxlength: "Length cannot be more than 200 characters",
                }
        },
        submitHandler: function (form) {
            id = $("#billing_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#storeBillingForm");
            $.ajax({ url: "{{ url('/store/update/billing') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url('store/profile')}}";                    
                    }, 3000);

                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
} 



$(document).on('change', '#state_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-districts') }}/",
          type: "POST",
          data:{'state_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#district_block").html(data);
      })
});

$(document).on('change', '#billing_state_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-districts') }}/",
          type: "POST",
          data:{'state_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#billing_district_block").html(data);
      })
});


</script>
@endpush