<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{{ url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <!-- <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}"/> -->
    <title>{{ config('app.name') }}
    @if(!Request::is('/'))
     | @yield('seo_title', '')
    @endif
    </title>
    <meta name="description" content="@yield('seo_keyword', '')">
    <meta name="keyword" content="@yield('seo_description', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @include('layouts.general_css')
    @stack('page-css')
    @stack('page-styles')
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
    @include('layouts.header')
    @include('layouts.nav')    
    @yield('content')
    @include('layouts.footer')
</div>
<!-- <a id="scrollTop"><i class="icon-chevron-up"></i><i class="icon-chevron-up"></i></a> -->
@include('layouts.general_js')
@stack('page-js')
<!-- <script src="{{ asset('js/ajax-crud.js') }}"></script> -->
@stack('page-scripts')
</body>
</html>
