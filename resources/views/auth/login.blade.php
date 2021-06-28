@extends('auth.auth_app')

@section('content')

<div class="card">
        <div class="card-body login-card-body">
        <p class="login-box-msg">Login in to start your session</p>


            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif




            
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                
            <div class="input-group-append">
                <div class="input-group-text">
                <span class="fas fa-envelope"></span>
                </div>
            </div>
            
            </div>
            <div class="input-group mb-3">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            <div class="input-group-append">
                <div class="input-group-text">
                <span class="fas fa-lock"></span>
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">
                    Remember Me
                </label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            </div>
        </form>

        <div class="social-auth-links text-center mb-3">
            <p>- OR -</p>
            <a href="javascript:" class="btn btn-block btn-primary">
            <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
            </a>
            <a href="javascript:" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
            </a>
        </div>

        <p class="mb-1">
            <a href="{{ url('/forget-password') }}">I forgot my password</a>
        </p>
        <!-- <p class="mb-0">
            <a href="register.html" class="text-center">Register a new membership</a>
        </p> -->
        </div>

    </div>
@endsection

@push('page-scripts')
<script type="text/javascript">

$(".alert-danger").delay(1000).addClass("in").toggle(true).fadeOut(3000);

</script>
@endpush


 
