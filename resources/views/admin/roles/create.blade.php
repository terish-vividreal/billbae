@extends('layouts.app')

@section('content')

@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url('/home') }}" class="nav-link">Home</a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url('/roles') }}" class="nav-link">Roles</a>
  </li>
@endsection

@push('page-css')
<style>
.table-box{
  width: 100%;
  display:flex;
  flex-wrap: wrap;
  margin: 0px;
  padding: 0px;
}
.table-box li{
  padding: 0.5rem;
  display: flex;
  width: 100%;
  border-bottom: 1px solid #DDD;
}
.sm-box{
  width: 25%;
}
.lg-box{
  width: 75%;
}
.lg-box-list{
  width: 100%;
  display: flex;
  margin: 0;
  padding: 0;
}
.lg-box-list li{
  border-bottom:inherit;
}
</style>

@endpush


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

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
            <!-- /.card-header -->
            <div class="card-body">
              {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
              @csrf

                <div class="col-md-8 ">               
                    <div class="form-group">
                        {!! Form::label('name', 'Name*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                        <div class="error" id="name_error"></div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('permission', 'Permission*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                        <div class="form-group clearfix"> 
    
                            </div>

                            <ul class="table-box">
                              @foreach($permissions as $value)
                                <li>
                                  <div class="sm-box"> 
                                    <h4>{{ $value->name }}</h4>
                                  </div>

                                  <div class="lg-box"> 
                                    @php $permission = Spatie\Permission\Models\Permission::where('parent', '=', $value->id)->get();  @endphp

                                    <ul class="lg-box-list">
                                      @foreach($permission as $row)
                                        <li>
                                        <label>{{ Form::checkbox('permission[]', $row->id, false, array('class' => 'name')) }} {{ $row->name }}</label
                                        </li>
                                      @endforeach
                                    </ul>                                
                                  </div>


                                </li>
                              @endforeach
                            </ul>


                    </div>            
                    
                </div>

                <div class="row">
                    <div class="col-12">
                    <a href="javascript:" class="btn btn-secondary">Cancel</a>
                    <input type="submit" value="Submit" class="btn btn-success">
                    </div>
                </div>
              {!! Form::close() !!}              

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