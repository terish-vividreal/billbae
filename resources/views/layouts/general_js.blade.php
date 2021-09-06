
<!-- BEGIN VENDOR JS-->
<script src="{{asset('admin/js/vendors.min.js')}}"></script>
<!-- BEGIN VENDOR JS-->
<!-- BEGIN PAGE VENDOR JS-->
@yield('vendor-script')
<!-- END PAGE VENDOR JS-->
<!-- BEGIN THEME  JS-->
<script src="{{asset('admin/js/plugins.js')}}"></script>
<script src="{{asset('admin/js/search.js')}}"></script>
<script src="{{asset('admin/js/custom/custom-script.js')}}"></script>
<script src="{{asset('admin/js/scripts/customizer.js')}}"></script>
<script src="{{asset('admin/vendors/toastr/toastr.min.js')}}"></script>
<script src="{{asset('admin/vendors/sweetalert/sweetalert.min.js')}}"></script>
<script src="{{asset('admin/vendors/select2/select2.full.min.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/flag-icon/css/flag-icon.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/select.dataTables.min.css')}}">

<!-- Success and error toasters -->
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<!-- BEGIN PAGE LEVEL JS-->
@yield('page-script')
<!-- END PAGE LEVEL JS-->
    
<script>
    $.ajaxSetup({headers: {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")}});

    //initialize all modals
    $('.modal').modal({
        dismissible: true
    });

    $("body").on("submit", ".ajax-submit", function (e) {
        e.preventDefault();         
    });
    
    // spinner version without timeout
    $('.submit-form').on('click', function () {
        var $this = $(this);
        $this.data("ohtml", $this.html());
        var nhtml = "<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading ...";
        $this.html(nhtml);
        $this.attr("disabled", true);
    });

    $(document).ready(function(){
        $('.navbar-list.billbae-list>li>a').click(function() {
            $('.navbar-list>li>a').removeClass('active');
            $(this).addClass('active');
            event.stopPropagation();
        });
        $('.tooltipped').tooltip()
    });
    $(document).click(function() {
        $('.navbar-list.billbae-list>li>a').removeClass('active');
    });

</script>
