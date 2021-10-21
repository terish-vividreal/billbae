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
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/customers') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">List</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  <!-- <a href="#" class="btn waves-effect waves-light cyan breadcrumbs-btn" type="submit" name="action">Add<i class="material-icons right">person_add</i></a> -->
  
  <a class="btn dropdown-settings waves-effect waves-light  light-blue darken-4 breadcrumbs-btn" href="#!" data-target="dropdown1" id="customerListBtn"><i class="material-icons hide-on-med-and-up">settings</i><span class="hide-on-small-onl">List</span><i class="material-icons right">arrow_drop_down</i></a>
  <ul class="dropdown-content" id="dropdown1" tabindex="0">
    <li tabindex="0"><a class="grey-text text-darken-2" href="{{ url(ROUTE_PREFIX.'/customers') }}">Active </a></li>
    <li tabindex="0"><a class="grey-text text-darken-2" href="{{ url(ROUTE_PREFIX.'/customers/deleted') }}">Deleted</a></li>

  </ul>



  <!-- <a href="" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Add<i class="material-icons right">person_add</i></a>
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Add<i class="material-icons right">person_add</i></a> -->
  
@endsection

<div class="section section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsume is used for the ...</p>
    </div>
  </div>
    <!-- DataTables example -->
    <div class="row">
      <div class="col s12 m12 l12">
        @include('layouts.success') 
        @include('layouts.error')
          <div id="button-trigger" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">{{ Str::plural($page->title) ?? ''}} Table</h4>
                <div class="row">
                  <div class="col s12">
                      <table id="data-table-simple-2" class="display data-tables">
                        <thead>
                            <tr>
                              <th>No</th>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Mobile</th>
                              <th>Action</th>
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

    table = $('#data-table-simple-2').DataTable({
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
          {data: 'DT_RowIndex', orderable: false, width:10},
          {data: 'name', name: 'name', orderable: false},            
          {data: 'email', name: 'name', orderable: false},               
          {data: 'mobile', name: 'name', orderable: false},               
          {data: 'action', name: 'action', orderable: false, searchable: false, width:75},
        ]
    });

  });

  function search(value) {
    value.name = $('input[type=search]').val();
  }

  $( "#customerListBtn" ).change(function() {
    alert( "Handler for .change() called." );
  });
  


  function softDelete(b) {

    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true,
          buttons: {
            cancel: 'No, Please!',
            delete: 'Yes, Delete'
          }
      }).then(function (willDelete) {
        if (willDelete) {
          $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/" + b, type: "DELETE", dataType: "html"})
            .done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                  showSuccessToaster(data.message);          
                  setTimeout(function () {
                    table.ajax.reload();
                    }, 2000);

              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }   
          }).fail(function () {
                  showErrorToaster("Something went wrong!");
          });
        } 
      });


          //  Swal.fire({
          //   title: 'Are you sure want to delete ?',
          //   text: "You won't be able to revert this!",
          //   type: 'warning',
          //   showCancelButton: true,
          //   confirmButtonColor: '#3085d6',
          //   cancelButtonColor: '#d33',
          //   confirmButtonText: 'Yes, delete it!'
          //   }).then(function(result) {
          //       if (result.value) {
          //           $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/" + b, type: "DELETE", dataType: "html"})
          //               .done(function (a) {
          //                   var data = JSON.parse(a);
          //                   if(data.flagError == false){
          //                     showSuccessToaster(data.message);          
          //                     setTimeout(function () {
          //                       table.ajax.reload();
          //                       }, 2000);
      
          //                 }else{
          //                   showErrorToaster(data.message);
          //                   printErrorMsg(data.error);
          //                 }   
          //               }).fail(function () {
          //                       showErrorToaster("Somthing went wrong!");
          //               });
          //       }
          //   });
  }



</script>
@endpush

