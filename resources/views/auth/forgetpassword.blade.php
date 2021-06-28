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
            <div class="card-body">
                <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
                <form action="recover-password.html" method="post">
                    <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                    </div>
                    <!-- /.col -->
                    </div>
                </form>
                <p class="mt-3 mb-1">
                    <a href="{{ route('login') }}">Back to Login</a>
                </p>
            </div>

    </div>
@endsection

@push('page-scripts')
<script type="text/javascript">

$(".alert-danger").delay(1000).addClass("in").toggle(true).fadeOut(3000);

</script>
@endpush


 
