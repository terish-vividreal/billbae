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
    <a href="{{ url('/roles') }}" class="nav-link">Roles
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
                        @foreach($permission as $value)
      
                            <div class="form-group clearfix"> 
                                <div class="icheck-primary d-inline">
                                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                    
                                    {{ $value->name }}
                                    </label>
                                </div>
                            </div>
                     
                        @endforeach

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