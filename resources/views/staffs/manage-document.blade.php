@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css"/>

<!-- Dropzone -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">


@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-account-settings.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
@endsection
@section('page-css')
<style type="text/css">

</style>
@endsection

@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Manage Documents</li>
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
            <h4 class="card-title">{{ $page->title ?? ''}} Documents</h4>
            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
            <div class="row" id="user_documents_div">
              
            </div>
        </div>
      </div>
    </div>
  </div>

  <!--Basic Form-->
  <div class="row">
    <!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
            <h4 class="card-title">{{ $page->title ?? ''}} Documents Form</h4>
            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
            <div class="row">
                <div class="input-field col s12">
                    <form action="{{ url(ROUTE_PREFIX.'/'.$page->route.'/upload-id-proof') }}" method="post" enctype="multipart/form-data" id="id-proof-upload" class="dropzone">
                        {!! Form::hidden('staff_id', $staff->id ?? '' , ['id' => 'staff_id'] ); !!}
                        @csrf
                        <h5 class="card-title"><span>Upload id proof(s) and Certificates/documents:</span></h5>
                    </form>
                </div>
            </div>
        </div>
      </div>
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
<!-- date-time-picker -->


<!-- Dropzone -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>
<script>

var staff_id = $("#staff_id").val();


  $(document).ready(function(){
    getUserDocuments();
  });

  Dropzone.autoDiscover = false;
  $(".dropzone").dropzone({
        maxFilesize: 12,
        addRemoveLinks: true,
        acceptedFiles: ".jpeg,.jpg,.png,.pdf,.txt",
        renameFile: function(file) {
          var dt = new Date();
          var time = dt.getTime();
          return staff_id+time+file.name;
        },
        init : function() {
          this.on("sending", function(file, xhr, formData){
              formData.append("staff_id", $("#staff_id").val());
          });
        },
        removedfile: function(file) {
            var name = file.upload.filename;
            $.ajax({ type: 'POST', url: '{{ url(ROUTE_PREFIX.'/staffs/remove-id-proof') }}',
                data: {filename: name, staff_id: staff_id},
                success: function(data){
                  getUserDocuments();
                }
            });
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            
        },
        success: function(file, response) 
        {
          getUserDocuments();
        },
        error: function(file, response)
        {
            return false;
        }
  });

  function getUserDocuments(){
    $.ajax({ type: 'POST', url: '{{ url(ROUTE_PREFIX.'/staffs/get-document') }}', data: {staff_id: staff_id},
      success: function(data){
        if(data.flagError == false){
          $("#user_documents_div").html(data.html);          
        }
      }
    });
  }

  function deleteDocument(filename){

    var data_return_type = 'html';

    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true,
			buttons: {
				cancel: 'No, Please!',
				delete: 'Yes, Delete It'
			}
		}).then(function (willDelete) {
			if (willDelete) {
			  $.ajax({url: "{{ url(ROUTE_PREFIX.'/staffs/remove-id-proof') }}" , type: "POST", data: {filename: filename, staff_id: staff_id, data_return_type:data_return_type}, })
            .done(function (data) {
              if(data.flagError == false){
                showSuccessToaster(data.message);          
                getUserDocuments();
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }   
            }).fail(function () {
                    showErrorToaster("Something went wrong!");
            });
			} 
		});
  }

  function updateDetails(document_id){
    $(".document-error").html("");
    var details   = $("#details-"+document_id).val();

    if(details != ''){
      $.ajax({ type: 'POST', url: '{{ url(ROUTE_PREFIX.'/staffs/update/document-details') }}', data: {staff_id: staff_id, document_id:document_id, details:details},
        success: function(data){
          if(data.flagError == false){
              if(data.flagError == false){
                showSuccessToaster(data.message);          
                getUserDocuments();
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }        
          }
        }
      });
    }else{
      $("#document-div-"+document_id).html("Please enter document details");
    }
  }

  $('.detail-input').keyup(function(event){
    $(".document-error").html("");
    alert(1)
  });


// Dropzone.options.dropzone =
// {
//     maxFilesize: 12,
//     renameFile: function(file) {
//         var dt = new Date();
//         var time = dt.getTime();
//         return time+file.name;
//     },
//     acceptedFiles: ".jpeg,.jpg,.png,.gif",
//     addRemoveLinks: true,
//     timeout: 50000,
//     removedfile: function(file) 
//     {
//         var name = file.upload.filename;
//         $.ajax({
//             headers: {
//                         'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
//                     },
//             type: 'POST',
//             url: '{{ url("image/delete") }}',
//             data: {filename: name},
//             success: function (data){
//                 console.log("File has been successfully removed!!");
//             },
//             error: function(e) {
//                 console.log(e);
//             }});
//             var fileRef;
//             return (fileRef = file.previewElement) != null ? 
//             fileRef.parentNode.removeChild(file.previewElement) : void 0;
//     },

//     success: function(file, response) 
//     {
//         console.log(response);
//     },
//     error: function(file, response)
//     {
//         return false;
//     }
// };


</script>
@endpush

