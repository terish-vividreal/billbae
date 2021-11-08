@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')

@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
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
  <a href="javascript:" class="btn waves-effect waves-light orange darken-4 breadcrumbs-btn" onclick="importBrowseModal()" >Upload<i class="material-icons right">attach_file</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}" class="btn waves-effect waves-light light-blue darken-4 breadcrumbs-btn" type="submit" name="action">List<i class="material-icons right">list</i></a>
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
                  {!! Form::hidden('service_id', $service->id ?? '' , ['id' => 'service_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('name', $service->name ?? '', ['id' => 'name']) !!}  
                  <label for="name" class="label-placeholder">Service Name <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('service_category_id',$variants->service_category , $service->service_category_id ?? '' , ['id' => 'service_category_id', 'class' => 'select2 browser-default', 'placeholder'=>'Please select service category']) !!}
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12"> 
                  {!! Form::text('price',  $service->price ?? '' , ['id' => 'price' ,'class' => 'check_numeric']) !!}
                  <label for="price" class="label-placeholder">Price <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('hours_id',$variants->hours , $service->hours_id ?? '' , ['class' => 'select2 browser-default', 'id' => 'hours_id', 'placeholder'=>'Please select service hour']) !!}
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                    @php 
                      $checked = '';
                        if(isset($service)){
                            $checked = ($service->tax_included == 1) ? 'checked' : '' ; 
                        }                      
                    @endphp
                    <div class="col s12">
                      <label for="tax_included">Check if tax is included with price !</label>
                      <p><label>
                          <input class="validate" value="1" name="tax_included" id="tax_included" type="checkbox" {{ $checked }}>
                          <span>Tax Included</span>
                        </label> </p>
                      <div class="input-field">
                      </div>
                  </div>
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('gst_tax', $variants->tax_percentage , $service->gst_tax ?? '' , ['id' => 'gst_tax', 'class' => 'select2 browser-default', 'placeholder'=>'Select GST Tax %']) !!}
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::text('hsn_code', $service->hsn_code ?? '', ['id' => 'hsn_code']) !!}  
                  <label for="hsn_code" class="label-placeholder">HSN Code </label>
                </div>
                <div class="input-field col m6 s12">
                {!! Form::select('additional_tax[]', $variants->additional_tax, $variants->additional_tax_ids ?? [] , ['id' => 'additional_tax', 'multiple' => 'multiple' ,'class' => 'select2 browser-default']) !!}
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                  {!! Form::select('lead_before',$variants->hours , $service->lead_before ?? '' , ['id' => 'lead_before', 'placeholder'=>'Select Lead time before']) !!}
                </div>
                <div class="input-field col m6 s12">
                  {!! Form::select('lead_after',$variants->hours , $service->lead_after ?? '' , ['id' => 'lead_after', 'placeholder'=>'Select Lead time after']) !!}
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
@include('services.import-browse-modal')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')

@endsection

@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<!-- date-time-picker -->

<script>
$('#service_category_id').select2({ placeholder: "Please select service category", allowClear: true });
$('#gst_tax').select2({ placeholder: "Please select GST Tax %", allowClear: true });
$('#hours_id').select2({ placeholder: "Please select service hour", allowClear: true });
$('#additional_tax').select2({ placeholder: "Select additional tax", allowClear: true });

if ($("#{{$page->entity}}Form").length > 0) {
    var validator = $("#{{$page->entity}}Form").validate({ 
        ignore: ".ignore-validation",
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
            },
            gst_tax: {
                  // required: true,
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
            },
            gst_tax: {
              required: "Please select GST Tax percentage",
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
</script>
@endpush