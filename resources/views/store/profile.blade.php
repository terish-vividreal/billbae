@extends('layouts.app')

@section('content')

@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url('user/home') }}" class="nav-link">Home</a>
  </li>
  <!-- <li class="nav-item d-none d-sm-inline-block">
    <a href="#" class="nav-link">Contact</a>
  </li> -->
@endsection

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ $page->title ?? ''}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('user/home') }}">Home</a></li>
              <li class="breadcrumb-item active">{{ $page->title ?? ''}} Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            @if($store)
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src=" {{ asset('admin/img/user4-128x128.jpg') }}"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ $store->name ?? '' }}</h3>

                <p class="text-muted text-center">Business Type</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Employees</b> <a class="float-right">{{ count($store->users) }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Customers</b> <a class="float-right">0</a>
                  </li>
                  <li class="list-group-item">
                    <b>Enquiries</b> <a class="float-right">19</a>
                  </li>
                </ul>

                <a href="#" class="btn btn-primary btn-block"><b>Orders</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">About Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Email</strong>

                <p class="text-muted">
                {{ $store->email ?? '' }}
                </p>

                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                <p class="text-muted">{{ $store->location ?? '' }}</p>

                <hr>

                <!-- <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                <p class="text-muted">
                  <span class="tag tag-danger">UI Design</span>
                  <span class="tag tag-success">Coding</span>
                  <span class="tag tag-info">Javascript</span>
                  <span class="tag tag-warning">PHP</span>
                  <span class="tag tag-primary">Node.js</span>
                </p>

                <hr> -->

                <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

                <p class="text-muted">{{ $store->about ?? '' }}</p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            @endif
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Profile</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="profile">
                    <form id="storeProfileForm" name="storeProfileForm" role="form" method="" action="" class="ajax-submit">
                      {{ csrf_field() }}
                      {!! Form::hidden('store_id', $store->id ?? '' , ['id' => 'store_id'] ); !!}
                      <div class="col-md-8 ">  

                          <div class="form-group">
                              {!! Form::label('name', 'Store Name*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::text('name', $store->name ?? '' , array('placeholder' => 'Store Name','class' => 'form-control')) !!}                        
                              <div class="error" id="email_error"></div>
                          </div> 
                          <div class="form-group">
                              {!! Form::label('email', 'Email*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::text('email', $store->email ?? '' , array('placeholder' => 'Email','class' => 'form-control')) !!}
                              <div class="error" id="email_error"></div>
                          </div>
                          <div class="form-group">
                              {!! Form::label('contact', 'Contact*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::text('contact', $store->contact ?? '', array('placeholder' => 'Contact Number','class' => 'form-control check_numeric')) !!}
                              <div class="error" id="name_error"></div>
                          </div> 
                          <div class="form-group">
                              {!! Form::label('location', 'Store location*', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::text('location', $store->location ?? '', array('placeholder' => 'Store location','class' => 'form-control')) !!}
                              <div class="error" id="name_error"></div>
                          </div>   
                          <div class="form-group">
                              {!! Form::label('about', 'About', ['class' => 'col-sm-2 col-form-label text-alert']) !!}
                              {!! Form::textarea('about', $store->about ?? '', ['id' => 'about', 'rows' => 4, 'cols' => 10, 'class' => 'form-control']) !!}
                              <div class="error" id="name_error"></div>
                          </div>            
                          
                      </div>
                      <div class="row">
                          <div class="col-12">
                          <a href="#" class="btn btn-secondary">Cancel</a>
                          <button class="btn btn-success ajax-submit">Submit</button>
                          </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                     time line
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
@push('page-scripts')

<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script type="text/javascript">

if ($("#storeProfileForm").length > 0) {
    var validator = $("#storeProfileForm").validate({ 
        rules: {
            name: {
                    required: true,
                    maxlength: 200,
            }, 
            email: {
                email: true,
                remote: { url: "{{ url('/store/unique') }}", type: "POST",
                    data: {
                        store_id: function () {
                            return $('#store_id').val();
                        }
                    }
                },
            },
        },
        messages: { 
            shop_name: {
                required: "Please enter store name",
                maxlength: "Length cannot be more than 200 characters",
                },
            email: {
                email: "Please enter a valid email address.",
                remote: "Email already existing"
            },
        },
        submitHandler: function (form) {
            id = $("#store_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#storeProfileForm");
            $.ajax({ url: "{{ url('/store/update') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url('store/profile')}}";                    
                    }, 3000);

                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
} 

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");


</script>
@endpush