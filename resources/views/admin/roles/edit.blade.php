@extends('layouts.admin.app')

@section('content')

@section('breadcrumb')
<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/roles') }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Edit</li>
  </ol>
@endsection
@section('page-action')
  
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
                <h4 class="card-title">Manage {{ $role->name }} Permissions </h4>
              
                {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}
                  @csrf
                  <div class="row">
                    @include('layouts.success') 
                    @include('layouts.error')
                    <div class="col m6 s12">
                      {!! Form::text('name',  $role->name ?? '') !!} 
                      <!-- <label for="name" class="label-placeholder active">Role name <span class="red-text">*</span></label> -->
                    </div>
                    <div class="col m6 s12">
                      <p class=""><label><input type="checkbox" class="payment-types" name="checkAll"  id="checkAll" value=""><span></span></label></p>
 
                      <label for="checkAll" class="label-placeholder active">Select all permissions</label>
                    </div>
                    

                    <div class="col s12">                    
                      <ul class="collection with-header">
                        @foreach($permissions as $value)
                          <li class="collection-header"><h6>{{ $value->name }}</h6></li>
                            @php $permission = Spatie\Permission\Models\Permission::where('parent', '=', $value->id)->get();  @endphp
                            @foreach($permission as $row)
                              <li class="collection-item">
                                <div>{{ $row->name }}
                                  <a href="#!" class="secondary-content">
                                    @php 
                                      $checked  = '';
                                      $checked  = in_array($row->id, $rolePermissions) ? "checked" : "";
                                    @endphp
                                    <p class=""><label><input type="checkbox" class="payment-types" name="permission[]" data-type="{{$row->id}}" id="permission{{$row->id}}" {{in_array($row->id, $rolePermissions) ? "checked" : ""}} value="{{$row->id}}"><span></span></label></p>
                                  </a>
                                </div>
                              </li>
                            @endforeach

                        @endforeach
                      </ul>
                    </div>
                    <div class="col s12">
                      <button class="btn waves-effect waves-light" type="reset" name="reset">Reset <i class="material-icons right">refresh</i></button>
                      <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="submit-btn">Submit <i class="material-icons right">send</i></button>
                    </div>
                  </div>
                {!! Form::close() !!}
            </div>
          </div>
      </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
 $(document).ready(function(){
  $('.collapsible').collapsible({
    accordion:true
  });
});
$("#checkAll").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });
</script>
@endpush