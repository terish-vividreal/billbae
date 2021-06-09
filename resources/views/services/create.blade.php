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

                  <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="" action="" class="ajax-submit">            
                    {{ csrf_field() }}
                    {!! Form::hidden('service_id', $service->id ?? '' , ['id' => 'service_id'] ); !!}
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Service Name*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                            {!! Form::text('name', $service->name ?? '' , array('placeholder' => 'Service Name','class' => 'form-control')) !!}                        
                            <div class="error" id="email_error"></div>
                        </div> 
                        <div class="form-group">
                          {!! Form::label('price', 'Price*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                          {!! Form::text('price', $service->price ?? '' , array('placeholder' => 'Price','class' => 'form-control check_numeric')) !!}
                          <div class="error" id="email_error"></div>
                        </div>                      
                        <div class="form-group">
                            @php 
                              $checked = '';
                                if(isset($service)){
                                    $checked = ($service->tax_included == 1) ? 'checked' : '' ; 
                                }                      
                            @endphp                                            
                          <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="tax_included" id="tax_included" value="1" {{ $checked }} >
                            {!! Form::label('tax_included', 'Tax Included *', ['class' => 'custom-control-label']) !!}
                          </div>
                          <small class= 'col-sm-2'>Check if tax is included with price !</small>
                        </div>
                        <div class="form-group">
                          {!! Form::label('gst_tax', 'GST Tax %', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('gst_tax', $variants->tax_percentage, $service->gst_tax ?? '' , ['id' => 'gst_tax', 'class' => 'form-control','placeholder'=>'Select tax percentage']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>
                        <div class="form-group">
                          {!! Form::label('hsn_code', 'HSN Code', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                          {!! Form::text('hsn_code', $service->hsn_code ?? '' , array('placeholder' => 'HSN Code','class' => 'form-control')) !!}
                          <div class="error" id="email_error"></div>
                        </div>

                        <div class="form-group">
                          {!! Form::label('additional_tax', 'Additional Tax', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('additional_tax[]', $variants->additional_tax, $variants->additional_tax_ids , ['id' => 'additional_tax', 'multiple' => 'multiple' ,'class' => 'form-control col-sm-12 selec2']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>


                      </div>  
                      <div class="col-md-6">                      
                        <div class="form-group">
                            {!! Form::label('service_category_id', 'Service Category*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                            {!! Form::select('service_category_id', $variants->service_category, $service->service_category_id ?? '' , ['id' => 'service_category_id', 'class' => 'form-control','placeholder'=>'Select Service Category']) !!}
                            <div class="error" id="roles_error"></div>
                        </div>
                        <div class="form-group">
                          {!! Form::label('hours_id', 'Service hours*', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('hours_id', $variants->hours, $service->hours_id ?? '' , ['id' => 'hours_id', 'class' => 'form-control','placeholder'=>'Select Service hours']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>                   
                        <div class="form-group">
                          {!! Form::label('lead_before', 'Lead time before', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('lead_before', $variants->hours, $service->lead_before ?? '' , ['id' => 'lead_before', 'class' => 'form-control','placeholder'=>'Select Lead time before']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>
                        <div class="form-group">
                          {!! Form::label('lead_after', 'Lead time after', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                          {!! Form::select('lead_after', $variants->hours, $service->lead_after ?? '' , ['id' => 'lead_after', 'class' => 'form-control','placeholder'=>'Select Lead time after']) !!}
                          <div class="error" id="roles_error"></div>
                        </div>

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

$('#additional_tax').select2({ placeholder: "Please choose services", allowClear: false });

// $("#services").select2({ placeholder: "Please choose services", allowClear: false })
//     .on('select2:select select2:unselect', function (e) { 
//       loadData() 
//       $(this).valid()
//       });

//   getServices();
// });

if ($("#{{$page->entity}}Form").length > 0) {
    var validator = $("#{{$page->entity}}Form").validate({ 
        rules: {
            name: {
                  required: true,
                  maxlength: 200,
            },
            service_category_id: {
                  required: true,
            },
            hours_id: {
                  required: true,
            },
            price: {
                  required: true,
            }
        },
        messages: { 
          name: {
            required: "Please enter service name",
            maxlength: "Length cannot be more than 200 characters",
            },
            service_category_id: {
              required: "Please select service category",
            },
            hours_id: {
              required: "Please select service hours",
            },
            price: {
              required: "Please enter service price",
            }
        },
        submitHandler: function (form) {
          id = $("#service_id").val();
          service_id   = "" == id ? "" : "/" + id;
          formMethod  = "" == id ? "POST" : "PUT";
          var forms = $("#{{$page->entity}}Form");
          $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + service_id, type: formMethod, processData: false, 
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

$("body").on("submit", ".ajax-submit", function (e) {
    e.preventDefault();         
});



</script>
@endpush