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
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/staffs') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">List</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/create/') }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Add<i class="material-icons right">add</i></a>
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
          <div id="button-trigger" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">{{ Str::plural($page->title) ?? ''}} Table</h4>
                <div class="row">
                  <div class="col s12">
                    <table id="data-table-billing" class="display data-tables">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Code </th>
                            <th>Customer Name</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Paid on </th>
                            <th width="280px">Action</th>
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
var link = '{{ $page->link }}';
  $(function () {

    table = $('#data-table-billing').DataTable({
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
            {data: 'billing_code', name: 'name', orderable: false},            
            {data: 'customer_id', name: 'name', orderable: false},            
            {data: 'bill_status', name: 'name', orderable: false},    
            {data: 'payment_status', name: 'name', orderable: false},            
            {data: 'updated_date', name: 'name', orderable: false},                   
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

  });

  function search(value) {
    value.name = $('input[type=search]').val();
  }

  function cancelBill(b) {

      swal({ title: "Are you sure?",icon: 'warning', dangerMode: true,
          buttons: {
            cancel: 'No, Please!',
            delete: 'Yes, Cancel Bill'
          }
      }).then(function (willDelete) {
        if (willDelete) {
          $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/cancel/') }}/" + b, type: "post", dataType: "html"})
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

      // Swal.fire({
      //   title: 'Are you sure want to cancel ?',
      //   text: "You won't be able to revert this!",
      //   type: 'warning',
      //   showCancelButton: true,
      //   confirmButtonColor: '#3085d6',
      //   cancelButtonColor: '#d33',
      //   confirmButtonText: 'Yes, cancel it!'
      // }).then(function(result) {
      //     if (result.value) {
      //         $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/cancel/') }}/" + b, type: "post", dataType: "html"})
      //             .done(function (a) {
      //                 var data = JSON.parse(a);
      //                 if(data.flagError == false){
      //                   showSuccessToaster(data.message);          
      //                   setTimeout(function () {
      //                     table.ajax.reload();
      //                     }, 2000);

      //               }else{
      //                 showErrorToaster(data.message);
      //                 printErrorMsg(data.error);
      //               }   
      //             }).fail(function () {
      //                     showErrorToaster("Somthing went wrong!");
      //             });
      //     }
      // });
  }

  function deleteBill(b) {

    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true, 
        buttons: {
          cancel: 'No, Please!',
          delete: 'Yes, Delete It'
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
  }

</script>
@endpush

