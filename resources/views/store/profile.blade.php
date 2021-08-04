@extends('layouts.app')

@section('content')
@push('page-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>
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

  img {
  display: block;
  max-width: 100%;
  }
  .preview {
  overflow: hidden;
  width: 160px; 
  height: 160px;
  margin: 10px;
  border: 1px solid red;
  }

  .modal-lg{
  max-width: 1000px !important;
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
                      <div class="form-group">
                        <div class="custom-file">                        
                          <!-- <a href="javascript:" data-id="1" id="updateProfile">Change Logo<i class="fa fa-pencil"></i></a> -->
                          <label class="custom-file-label" for="customFile">Choose Logo</label>
                          <input type="file" name="image" accept="image/png, image/gif, image/jpeg" class="image custom-file-input">
                        </div>
                        <!-- <label for="customFile">Custom File</label> -->
                      </div>
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
                <strong><i class="far fa-file-alt mr-1"></i> Address </strong>
                <p class="text-muted">{{ $store->address ?? '' }}</p>
                <p class="text-muted">{{ $store->pincode ?? '' }}</p>
              </div>
            </div>

            @endif
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Store Profile</a></li>
                  <li class="nav-item"><a class="nav-link" href="#billingtab" data-toggle="tab">Billing Details</a></li>
                  <li class="nav-item"><a class="nav-link" href="#billformat" data-toggle="tab">Billing Format</a></li>
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
                            {!! Form::label('country_id', 'Country *', ['class' => '']) !!}
                            {!! Form::select('country_id', $variants->countries , $store->country_id ?? '' , ['id' => 'country_id' ,'class' => 'form-control','placeholder'=>'Select a country']) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('state_id', 'State *', ['class' => '']) !!}
                            <div id="state_block">
                            @if(isset($variants->states))
                              {!! Form::select('state_id', $variants->states , $store->state_id ?? '' , ['id' => 'state_id' ,'class' => 'form-control','placeholder'=>'Select a state']) !!}
                            @else  
                              {!! Form::select('state_id', [] , '' , ['id' => 'state_id' ,'class' => 'form-control','placeholder'=>'Select a State']) !!}
                            @endif
                             </div>
                          </div>

                          <div class="form-group">
                            {!! Form::label('district_id', 'District*', ['class' => '']) !!}
                            <div id="district_block">
                            @if(isset($variants->districts))
                              {!! Form::select('district_id', $variants->districts , $store->district_id ?? '' , ['id' => 'district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @else  
                              {!! Form::select('district_id', [] , '' , ['id' => 'district_id' ,'class' => 'form-control','placeholder'=>'Select a district']) !!}
                            @endif
                             </div>
                          </div>

                          <div class="form-group">
                            {!! Form::label('district_id', 'Timezone *', ['class' => '']) !!}
                            <div id="timezone_block">
                            @if(isset($variants->timezone))
                              {!! Form::select('timezone', $variants->timezone , $store->timezone ?? '' , ['id' => 'timezone' ,'class' => 'form-control','placeholder'=>'Select a timezone']) !!}
                            @else  
                              {!! Form::select('timezone', [] , '' , ['id' => 'timezone' ,'class' => 'form-control','placeholder'=>'Select a timezone']) !!}
                            @endif
                             </div>
                          </div>

                          <div class="form-group">
                              {!! Form::label('time_format', 'Time Format ', ['class' => 'col-sm-4 col-form-label text-alert']) !!}
                              {!! Form::select('time_format', [1 => '12 Format', 2 => '24 Format'] , $store->time_format ?? '' , ['id' => 'time_format' ,'class' => 'form-control']) !!}
                              <div class="error" id="time_format"></div>
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
                            {!! Form::label('billing_country_id', 'Country *', ['class' => '']) !!}
                            {!! Form::select('billing_country_id', $variants->countries , $billing->country_id ?? '' , ['id' => 'billing_country_id' ,'class' => 'form-control','placeholder'=>'Select a country']) !!}
                          </div>

                          <div class="form-group">
                            {!! Form::label('billing_state_id', 'State *', ['class' => '']) !!}
                            <div id="billing_state_block">
                            @if(isset($variants->billing_states))
                              {!! Form::select('billing_state_id', $variants->billing_states , $billing->state_id ?? '' , ['id' => 'billing_state_id' ,'class' => 'form-control','placeholder'=>'Select a state']) !!}
                            @else  
                              {!! Form::select('billing_state_id', [] , '' , ['id' => 'billing_state_id' ,'class' => 'form-control','placeholder'=>'Select a State']) !!}
                            @endif
                             </div>
                          </div>

                          <div class="form-group">
                            {!! Form::label('billing_district_id', 'District*', ['class' => '']) !!}
                            <div id="billing_district_block">
                            @if(isset($variants->billing_districts))
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

                  <div class="tab-pane" id="billformat">
                    <div class="card-body">
                      <form id="billFormatForm" name="billFormatForm" role="form" method="" action="" class="ajax-submit">
                        {{ csrf_field() }}
                        {!! Form::hidden('bill_format_id', $variants->billing_formats->id , ['id' => 'bill_format_id'] ); !!}

                        <!-- style="display:none;" -->
                        <div class="container-fluid" id="customer_details_div">
                          <!-- SELECT2 EXAMPLE -->
                          <div class="card card-default">
                            <div class="card-header">
                              <h3 class="card-title">Billing Formats</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">

                              <div class="row">
                                <div class="col-md-12">
                                <div class="callout callout-info">
                                  Auto generated Billing ID format : <span id="mainFormatID" style="font-weight: bold;"> {{$variants->billing_formats->bill_format}} </span>
                                </div>
                                </div>
                              </div>
            
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="bill_prefix" class="col-form-label">Starts with *</label>
                                    {!! Form::text('bill_prefix', $variants->billing_formats->prefix ?? '' , array('class' => 'form-control')) !!}
                                    <small class="col-sm-2 ">3 Alphabetic characters only.</small>
                                  </div> 
                      
                                  <div class="form-group" style="margin-top: 1px;">                                                 
                                      <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="applied_to_all" id="applied_to_all" @if($variants->billing_formats->applied_to_all == 0) checked="checked" @endif >
                                        <label for="applied_to_all" class="custom-control-label">Use same format for all bills.</label>
                                      </div>
                                  </div>

                                </div>

                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="bill_suffix" class="col-form-label">Starts From *</label>
                                    {!! Form::text('bill_suffix', $variants->billing_formats->suffix ?? '' , array('class' => 'form-control')) !!}
                                    <small class="col-sm-2">Starting from number</small>
                                  </div>
                                </div>

                              </div>
                              <div class="payment-types-section" @if($variants->billing_formats->applied_to_all == 0) style="display:none;" @endif>

                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="lead">Payment Details:</p>
                                        <div class="table-responsive">
                                            <table class="table"><tbody>
                                              @foreach($variants->payment_types as $type)
                                              @php $checked='';  $prefix=''; $suffix='';   
                                              
                                                if($variants->billing_formats_all->contains('payment_type', $type->id))
                                                {
                                                  $checked="checked";
                                                  $prefix= $variants->billing_formats_all->where('payment_type', $type->id)->pluck('prefix')->first();
                                                  $suffix= $variants->billing_formats_all->where('payment_type', $type->id)->pluck('suffix')->first();
                                                }

                                              @endphp 
                                              <tr>
                                                <td style="align:right"><input class="payment-types" type="checkbox" name="payment_types[]" data-type="{{$type->id}}" id="payment_types_{{$type->id}}" {{$checked}} value="{{$type->id}}"></td>
                                                <td style="width:20%">
  
                                                
                                                {{$type->name}} </td>
                                                <td style="align:right">
                                                  <input placeholder="{{$type->name}} Prefix starts with" id="bill_prefix_{{$type->id}}" class="form-control"  name="bill_prefix_type[{{$type->id}}]"  type="text" value="{{$prefix}}" >
                                                  <label id="bill_prefix-error_{{$type->id}}" class="error"></label>
                                                </td>
                                                <td style="align:right"><input placeholder="{{$type->name}} Starts from" id="bill_suffix_{{$type->id}}" class="form-control check_numeric" name="bill_suffix_type[{{$type->id}}]" type="text" value="{{$suffix}}" ></td>
                                              <tr>
                                              @endforeach
                                            </tbody>
                                            </table>
                                        </div>
                                            
                                    </div>
                                </div>


                              </div>


                            </div>
                            <!-- +/.card-body -->
                          </div>
                          <!-- /.card -->
                        </div>

                        <div class="row">
                            <div class="col-12">
                            <a href="#" class="btn btn-secondary">Cancel</a>
                            <button class="btn btn-success" id="continue"> Continue </button>
                            </div>
                        </div>
                      </form>              
                    </div>
            
                  </div>
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

  <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="img-container">
            <div class="row">
              <div class="col-md-8">
                <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
              </div>
              <div class="col-md-4">
                <div class="preview"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="crop">Crop</button>
        </div>
      </div>
    </div>
  </div>
@include('store.image-manage') 
@endsection
@push('page-scripts')

<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
<script type="text/javascript">


var $modal = $('#modal');
var image = document.getElementById('image');
var cropper;
var isFormValid = false;
var typeId  = '';



$("body").on("change", ".image", function(e){
  var files = e.target.files;
  var done = function (url) {
    image.src = url;
    $modal.modal('show');
  };
  var reader;
  var file;
  var url;
  if (files && files.length > 0) {
    file = files[0];
    if (URL) {
      done(URL.createObjectURL(file));
    } else if (FileReader) {
    reader = new FileReader();
    reader.onload = function (e) {
      done(reader.result);
    };
    reader.readAsDataURL(file);
    }
  }
});
$modal.on('shown.bs.modal', function () {
  cropper = new Cropper(image, {
    aspectRatio: 1,
    viewMode: 3,
    preview: '.preview'
  });

  }).on('hidden.bs.modal', function () {
    cropper.destroy();
    cropper = null;
});

$("#crop").click(function(){
  canvas = cropper.getCroppedCanvas({
    width: 160,
    height: 160,
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
          url: "{{ url('/store/update-logo') }}",
          data: {store_id : id , 'image': base64data},
          success: function(data){

                if(data.flagError == false){
                    showSuccessToaster(data.message);                 
                    $("#store_logo").attr("src", data.logo);
                    $modal.modal('hide');
                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
          }
        });
    }
  });
})

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

if ($("#billFormatForm").length > 0) {
    var validator = $("#billFormatForm").validate({ 
        rules: {
            bill_prefix: {
                    required: true,
                    maxlength: 5,
                    lettersonly:true,
            }, 
            bill_suffix: {
                  required: true,
                  maxlength: 5,
                  number: true,
            },
            "bill_prefix_type[]": {
                maxlength: 3,
                lettersonly:true,
            },
            "bill_suffix_type[]": {
                maxlength: 3,
                number: true,
            },
        },
        messages: { 
          bill_prefix: {
              required: "Please enter bill prefix",
              maxlength: "Length cannot be more than 5 characters",
          },
          bill_suffix: {
            required: "Please enter bill suffix",
            maxlength: "Length cannot be more than 5 digits",
            number: "Accept only numeric values",
          },
          "bill_prefix_type[]": {
            maxlength: "Length cannot be more than 3 characters",
          },
          "bill_suffix_type[]": {
            maxlength: "Length cannot be more than 5 characters",
            number: "Accept only numeric values",
          },
        },
        submitHandler: function (form) {

            formMethod    = "POST";
            var forms     = $("#billFormatForm");
            var isSubmit  = true;
            var regex     = /^[A-Za-z]+$/;

            if($("#applied_to_all").is(":not(:checked)")){
                $('input:checkbox.payment-types').each(function () {
                  if(this.checked){
                      typeId = $(this).data("type");
                      if($('#bill_prefix_'+typeId).val() == ''){
                        $("#bill_prefix-error_"+typeId).text("Please enter bill prefix");
                        $("#bill_prefix-error_"+typeId).addClass("error");
                        $("#bill_prefix-error_"+typeId).attr("style", "display:block");
                        isSubmit = false;
                      }else if($('#bill_prefix_'+typeId).val().length > 5){
                        $("#bill_prefix-error_"+typeId).text("Length cannot be more than 5 characters");
                        $("#bill_prefix-error_"+typeId).addClass("error");
                        $("#bill_prefix-error_"+typeId).attr("style", "display:block");
                        isSubmit = false;
                      }else if(!$('#bill_prefix_'+typeId).val().match(regex)){
                        $("#bill_prefix-error_"+typeId).text("Letters only please");
                        $("#bill_prefix-error_"+typeId).addClass("error");
                        $("#bill_prefix-error_"+typeId).attr("style", "display:block");
                        isSubmit = false;
                      }
                  }
                });
            } 

            if(isSubmit === true){

              $.ajax({ url: "{{ url('/store/update/bill-format') }}", type: formMethod, processData: false, 
                data: forms.serialize(), dataType: "html",
              }).done(function (a) {
                  var data = JSON.parse(a);
                  if(data.flagError == false){
                      showSuccessToaster(data.message);
                      $("#mainFormatID").html(data.bill_format);
                      // $("#bill_prefix").val('');
                      // $("#bill_suffix").val('');
                  }else{
                    showErrorToaster(data.message);
                    printErrorMsg(data.error);
                  }
              });
            }
        }
    })
}

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");


$('#applied_to_all').change(function() {
    if($(this).is(":unchecked")) 
        $('.payment-types-section').show();
    else
        $('.payment-types-section').hide();         
});


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

$(document).on('change', '#country_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-states') }}",
          type: "POST",
          data:{'country_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#state_block").html(data);
      });

      $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-timezone') }}",
          type: "POST",
          data:{'country_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#timezone_block").html(data);
      })


});

$(document).on('change', '#state_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-districts') }}",
          type: "POST",
          data:{'state_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#district_block").html(data);
      })
});

$(document).on('change', '#billing_country_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-states') }}",
          type: "POST",
          data:{'country_id':this.value },
          dataType: "html"
      }).done(function (data) {
      console.log(data);
        $("#billing_state_block").html(data);
      })
});

$(document).on('change', '#billing_state_id', function () {
    $.ajax({
          url: "{{ url(ROUTE_PREFIX.'/common/get-shop-districts') }}",
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