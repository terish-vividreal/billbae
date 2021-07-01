<!DOCTYPE html>
<html lang=en>
<head>
    <base href="{{ url('/') }}">
    <meta charset="utf-8"/>
    <title>Login | {{ config('app.name') }} </title>
    <meta name=description content="Login page config('app.name')"/>
    <meta name=viewport content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel=canonical href="{{ url('/') }}"/>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}" rel=stylesheet type="text/css"/>

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/css/adminlte.min.css') }} ">

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="{{ url('login') }}"><b>{{ config('app.name') }} </b> Admin </a>
  </div>
  <!-- /.login-logo -->
  @yield('content')

</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<!-- <script src="{{ asset('admin/dist/js/adminlte.min.js') }}"></script> -->
@stack('page-scripts')
</body>
</html>
