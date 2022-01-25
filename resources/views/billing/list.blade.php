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

    <div class="row">
      <div class="col s12 m6 l12">
          <div id="button-trigger" class="card card card-default scrollspy"></div>
      </div>
    </div>
    <!-- DataTables example -->
    <div class="row">
      <div class="col s12 m12 l12">
          <div id="button-trigger" class="card card card-default scrollspy">
            <div class="card-content">
                <h4 class="card-title">{{ Str::plural($page->title) ?? ''}} Table</h4>
                <div class="row">
                  <div class="col s12 data-table-container">
                    <div class="card-content">
                        <div class="row">
                          <div class="col s12">
                            <form id="page-form" name="page-form">
                            {{ csrf_field() }}
                              <div class="row">
                                <div class="input-field col m3 s12">
                                  {!! Form::text('invoice', '' , ['id' => 'invoice']) !!} 
                                  <label for="invoice" class="label-placeholder active">Invoice ID</label>
                                </div>
                                <div class="input-field col m4 s12"> 
                                  {!! Form::select('customer_id', $variants->customers, '', ['id' => 'customer_id', 'class' => 'select2 browser-default', 'multiple' => 'multiple']) !!}
                                  <!-- <label for="customer_id" class="label-placeholder active">Customers</label> -->
                                </div>
                                <div class="input-field col m2 s12">
                                  {!! Form::select('payment_status', [1 => 'Paid', 0 => 'Unpaid'] , '' , ['id' => 'payment_status' ,'class' => 'select2 browser-default', 'placeholder'=>'Search by status']) !!}
                                  <!-- <label for="payment_status" class="label-placeholder active">Payment Status</label> -->
                                </div>
                                <div class="input-field col m3">
                                  <div class="input-field col m6">
                                    <button class="btn waves-effect waves-light" type="button" id="page-filter-button"> FILTER <i class="material-icons right">send</i></button>
                                  </div>
                                  <div class="input-field col m6">
                                    <button class="btn-floating waves-effect waves-light cyan" type="button" id="page-filter-button"> RESET <i class="material-icons right">refresh</i></button>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                    </div>
                    <table id="data-table-billing" class="display data-tables" data-url="{{ $page->link.'/lists' }}" data-form="page" data-length="10">
                        <thead>
                            <tr>
                                <th width="10px" data-orderable="false" data-column="DT_RowIndex">No</th>
                                <th width="100px" data-orderable="false" data-column="billing_code">Invoice ID</th>
                                <th data-orderable="false" data-column="customer_id">Customer Name</th>
                                <th width="70px" data-orderable="false" data-column="status">Status</th>
                                <th width="70px" data-orderable="false" data-column="payment_status">Payment Status</th>
                                <th width="150px" data-orderable="false" data-column="updated_date">Paid on</th>
                                <th width="250px" data-orderable="false" data-column="action">Action</th>
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
$('#customer_id').select2({ placeholder: "Search by customers", allowClear: true });
$('#payment_status').select2({ placeholder: "Search by status", allowClear: true });

  function cancelBill(b) {
    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true, buttons: {cancel: 'No, Please!', delete: 'Yes, Cancel Bill'}
    }).then(function (willDelete) {
      if (willDelete) {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/cancel/') }}/" + b, type: "post", dataType: "html"})
        .done(function (a) {
          var data = JSON.parse(a);
          if(data.flagError == false) {
            showSuccessToaster(data.message);          
            setTimeout(function () { table.ajax.reload(); }, 2000);
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

  function deleteBill(b) {
    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true, buttons:{cancel: 'No, Please!', delete: 'Yes, Delete It'}
    }).then(function (willDelete) {
      if (willDelete) {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}/" + b, type: "DELETE", dataType: "html"})
        .done(function (a) {
          var data = JSON.parse(a);
          if (data.flagError == false) {
            showSuccessToaster(data.message);          
            setTimeout(function () { table.ajax.reload(); }, 2000);
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

