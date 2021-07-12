@extends('auth.auth_app')

@section('content')

<div class="card">
        <div class="card-body login-card-body">
        <p class="login-box-msg">Reset Password</p>


                    @if (Session::has('message'))
                         <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                    @endif



            
        <form method="POST" action="{{ route('forget.password.post') }}" id="resetPasswordmailForm" >
            @csrf
            <div class="input-group mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                
            <div class="input-group-append">
                <div class="input-group-text">
                <span class="fas fa-envelope"></span>
                </div>
            </div>
                                
            
            </div>
            @if ($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
            @endif

            <div class="row">

            <div class="col-8">
            <button type="submit" class="btn btn-primary" id="submit-btn">Send Password Reset Link </button>
            </div>
            </div>
        </form>

        <!-- <div class="social-auth-links text-center mb-3">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-primary">
            <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
            </a>
            <a href="#" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
            </a>
        </div> -->
            <br>
         <p class="mb-1">
            <a href="{{url('login')}}">Login</a>
        </p>
       <!-- <p class="mb-0">
            <a href="register.html" class="text-center">Register a new membership</a>
        </p> -->
        </div>

    </div>
@endsection

@push('page-scripts')
<script type="text/javascript">

$("#submit-btn").on("click", function(event){
    event.preventDefault();
    $('#submit-btn').html('Please Wait...');
    $("#submit-btn"). attr("disabled", true);
    $( "#resetPasswordmailForm" ).submit();
});

$(".alert-danger").delay(1000).addClass("in").toggle(true).fadeOut(3000);

</script>
@endpush


 
