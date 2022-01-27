@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/flag-icon/css/flag-icon.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/jquery.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/select.dataTables.min.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/data-tables.css')}}">
@endsection

@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/services') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">List</li>
  </ol>
@endsection

@section('page-action')
  @can('service-create')
  <a href="javascript:" class="btn waves-effect waves-light orange darken-4 breadcrumbs-btn" onclick="importBrowseModal()" >Upload<i class="material-icons right">attach_file</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  @endcan
  @can('service-list')
  <a class="btn dropdown-settings waves-effect waves-light  light-blue darken-4 breadcrumbs-btn" href="#!" data-target="dropdown1"><i class="material-icons hide-on-med-and-up">settings</i><span class="hide-on-small-onl">List</span><i class="material-icons right">arrow_drop_down</i></a>
    <ul class="dropdown-content" id="dropdown1" tabindex="0">
      <li tabindex="0"><a class="grey-text text-darken-2 listBtn" href="javascript:" data-type="active">Active </a></li>
      <li tabindex="0"><a class="grey-text text-darken-2 listBtn" data-type="deleted" href="javascript:">Deactivated</a></li>
    </ul>
  @endcan
@endsection
<form id="customerListForm" name="serviceListForm" role="form" method="" action="" class="ajax-submit">
  {{ csrf_field() }}
  {!! Form::hidden('service_type', '', ['id' => 'service_type'] ); !!}
</form>

<div class="section section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsume is used for the ...</p>
    </div>
  </div>
    <!-- DataTables example -->
    <div class="row">
      <div class="col s12 m12 l12">
          <div id="button-trigger" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">{{ Str::plural($page->title) ?? ''}} Table</h4>
                <div class="row">
                  <div class="col s12">
                      <table id="data-table-simple-services" class="display data-tables">
                        <thead>
                            <tr>
                              <th>No</th>
                              <th>Name</th>
                              <th>Service Category</th>                                    
                              <th>Price</th>
                              <th>Hours</th>
                              <th width="100px">Action</th>
                            </tr>
                        </thead>
                      </table>
                  </div>
                </div>
            </div>
          </div>
      </div>
    </div>
</div>
@include('services.import-browse-modal')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
@endsection

@push('page-scripts')
<script src="{{asset('admin/js/scripts/data-tables.js')}}"></script>

<script>
  $(function () {
    table = $('#data-table-simple-services').DataTable({
        bSearchable: true,
        pagination: true,
        pageLength: 10,
        responsive: true,
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/lists') }}",
          data: search
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false, width:20},            
            {data: 'name', name: 'name', orderable: false},  
            {data: 'service_category', name: 'name', orderable: false},          
            {data: 'price', name: 'name', orderable: false},            
            {data: 'hours', name: 'name', orderable: false},            
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
  });

  function search(value) {
    value.name = $('input[type=search]').val();
    value.service_type = $("#service_type").val();
  }

  $(".listBtn").on("click", function(){
    $("#service_type").val($(this).attr('data-type'));
    table.ajax.reload();
  });

  function softDelete(b) {
    swal({ title: "Are you sure ?",icon: 'warning', dangerMode: true, buttons: { cancel: 'No, Please!',  delete: 'Yes, Deactivate' }
    }).then(function (willDelete) {
      if (willDelete) {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/" + b, type: "DELETE", dataType: "html"})
          .done(function (a) {
              var data = JSON.parse(a);
              if (data.flagError == false) {
                showSuccessToaster(data.message);          
                setTimeout(function () {
                  table.ajax.reload();
                }, 2000);
            } else {
              showErrorToaster(data.message);
              printErrorMsg(data.error);
            }   
        }).fail(function () {
          showErrorToaster("Something went wrong!");
        });
      } 
    });
  }

  function restore(b) {
    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true, buttons: { cancel: 'No, Please!', delete: 'Yes, Restore' }
    }).then(function (willDelete) {
      if (willDelete) {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/restore/" + b, type: "POST", dataType: "html"})
          .done(function (a) {
              var data = JSON.parse(a);
              if(data.flagError == false){
                showSuccessToaster(data.message);          
                setTimeout(function () {
                  table.ajax.reload();
                }, 2000);

            } else {
              showErrorToaster(data.message);
              printErrorMsg(data.error);
            }   
        }).fail(function () {
          showErrorToaster("Something went wrong!");
        });
      } 
    });
  }
</script>
@endpush

