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
    <a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}" class="nav-link">{{ $page->title ?? ''}} </a>
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

          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            
            </ol>
          </div>

        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
     <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ $page->title ?? ''}} Table</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="form-group">
                        {!! Form::select('service_category', $variants->service_category, '' , ['id' => 'service_category', 'class' => 'col-sm-4 form-control','placeholder'=>'Select Service Category']) !!}
                    </div> 
                        <table class="table table-hover table-striped table-bordered data-tables"
                               data-url="{{ $page->link.'/lists' }}" data-form="page" data-length="20">
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
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
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
<script>

  var table;
  var role        = '{{ROUTE_PREFIX}}';
  var link        = '{{$page->link}}';
  var entity      = '{{$page->entity}}';

  $(function () {
    table = $('.data-tables').DataTable({
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
            {data: 'DT_RowIndex', orderable: false, searchable: false},            
            {data: 'name', name: 'name'},  
            {data: 'service_category', name: 'name'},          
            {data: 'price', name: 'name'},            
            {data: 'hours', name: 'name'},            
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
  });


  function search(value) {
    value.name = $('input[type=search]').val();
    value.service_category  = $('#service_category').val();
  }

  $('#service_category').on('change', function() {
    table.ajax.reload();
  });
  

  function softDelete(b) {
           
    Swal.fire({
      title: 'Are you sure want to delete ?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
      }).then(function(result) {
          if (result.value) {
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
                          showErrorToaster("Somthing went wrong!");
                  });
          }
      });
  }

</script>
@endpush


