<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/toastr/toastr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2-materialize.css')}}">
<!-- BEGIN: VENDOR CSS-->
@yield('vendor-style')
<!-- END: VENDOR CSS-->
<!-- SweetAlert2 -->
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/sweetalert/sweetalert.css')}}">
<!-- BEGIN: Page Level CSS-->
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/themes/vertical-modern-menu-template/materialize.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/themes/vertical-modern-menu-template/style.css')}}">
@yield('page-style')
<!-- BEGIN: Custom CSS-->
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/custom/custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/data-tables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/date-range-picker.css')}}">
<!-- END: Custom CSS-->
@stack('page-css')