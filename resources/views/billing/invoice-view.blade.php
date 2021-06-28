@extends('layouts.app')

@section('content')
@push('page-css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@endpush

@section('breadcrumb')
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/home') }}" class="nav-link">Home</a>
  </li>
  <li class="nav-item d-none d-sm-inline-block">
    <a href="{{ url(ROUTE_PREFIX.'/users') }}" class="nav-link">{{ $page->title ?? ''}}</a>
  </li>
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
        <div class="row">
          <div class="col-12">
            <div class="callout callout-info">
              <h5><i class="fas fa-info"></i> Note:</h5>
              This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
            </div>


            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fas fa-globe"></i> {{ $variants->store->billing->company_name ?? '' }}
                    <small class="float-right">Paid Date: {{ date('d-m-Y')}}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong>Admin, Inc.</strong><br>
                    {{ $variants->store->billing->address ?? '' }}<br>
                    {{ $variants->store->state ?? '' }}, {{ $variants->store->district ?? '' }} <br>
                    {{ $variants->store->location ?? '' }},  Pin - {{ $variants->store->pincode ?? '' }}<br>
                    Phone: {{ $variants->store->contact ?? '' }} <br>
                    Email: {{ $variants->store->email ?? '' }}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  To
                  <address>
                    <strong>{{ $billing->customer->name ?? '' }}</strong><br>
                    @if($billing->address_type == 'customer')
                      {{ $billing->customer->address ?? '' }}<br>
                      Phone: {{ $billing->customer->mobile ?? '' }}<br>
                      Email: {{ $billing->customer->email ?? '' }}

                    @else
                      {{ $billing->customer->billingaddress->billing_name ?? '' }}<br>
                      {{ $billing->customer->billingaddress->address ?? '' }}<br>
                      {{ $billing->customer->billingaddress->state->name ?? ''}}, 
                      {{ $billing->customer->billingaddress->district->name ?? '' }},  Pin  - {{ $billing->customer->billingaddress->pincode ?? '' }} <br>
                      GST  - {{ $billing->customer->billingaddress->gst ?? '' }} <br>
                      Phone: {{ $billing->customer->mobile ?? '' }}<br>
                      Email: {{ $billing->customer->email ?? '' }}
                    @endif
                    
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <b>Invoice #{{$billing->billing_code}}</b><br>
                  <br>
                  <b>Order ID:</b> {{$billing->billing_code}}<br>
                  <b>Payment Status:</b> Pending <br>
                  <!-- <b>Account:</b> 968-34567 -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row Billitems-->
              <div class="row">
                <div class="col-12 table-responsive" id="invoiceTable">
                 
  @php //exit; @endphp
                 
  <table class="table table-striped">
    <thead>
    <tr>
      <th>No</th>
      <th>Items</th>
      <th>HSN Code #</th>
      <th>Tax Details</th>
      <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @if($billing_items)

      @foreach($billing_items as $key => $item)
        <tr id="{{$item['id'] }}">
          <td>{{$loop->index + 1}}</td>
          <td>{{ $item->name }} </td>
          <td>{{ $item->hsn_code }}</td>
          <td>
            Amount (Without Tax) - <br>
            {{ $item->cgst_percentage }} % CGST - <br> 
            {{$item->sgst_percentage }} % SGST - <br> 

            @php if(count($item->additionalTax) > 0) { @endphp
              @foreach($item->additionalTax as $key => $additional)
                  {{ $additional->percentage }} % {{ $additional->tax_name }} - <br> 
              @endforeach
            @php } @endphp

            @if($item->is_discount_used == 1)
              Discount @if($item->discount_type == 'percentage') {{$item->discount_value }} '%' @endif- <br>
            @endif

            <br><b> Total - <b>

          </td>
          <td>

          ₹ @php echo number_format($item->tax_amount ,2)  @endphp<br> 
          ₹ {{ $item->cgst_amount }}<br> 
          ₹ {{ $item->sgst_amount }}<br> 

          @php if(count($item->additionalTax) > 0) { @endphp
            @foreach($item->additionalTax as $key => $additional)
              ₹ @php echo number_format($additional->amount,2)  @endphp<br>
            @endforeach
          @php } @endphp

            @if($item->is_discount_used == 1)

                @if($item->discount_type == 'percentage') 
                  @php $discount_value = $item->grand_total * (($item->discount_value/100)) @endphp
                @else 
                  @php $discount_value = $item->discount_value; @endphp                
                @endif

                          ₹ @php echo number_format($discount_value,2)  @endphp  <br>

                          <br><b>₹ @php echo number_format(($item->grand_total - $discount_value),2) @endphp</b><br>

                          @php $grand_total = $grand_total- $discount_value @endphp

            @else

                <br><b> ₹ @php echo number_format($item->grand_total,2)  @endphp<br> 

            @endif

          </td>
        </tr>
        @php 

        @endphp
      @endforeach

    @endif

    </tbody>
  </table>

                
                </div>
                <!-- /.col -->
              </div>
              <!-- /.rowthis -->
              
              <div class="row">
                <!-- accepted payments column -->
                

                <div class="col-6">
                <div class="alert alert-danger print-error-msg" style="display:none"><ul></ul></div>
                  <p class="lead">Payment Details:</p>
                    @if($billing->paymentMethods)
                      <div class="table-responsive">
                        <table class="table">
                          @foreach($billing->paymentMethods as $row)
                          <tr>
                            <td style="width:50%">{{ $row->paymentype->name }}</td>
                            <td style="align:right">₹ @php echo number_format($row->amount,2) @endphp</td>
                          </tr>
                          @endforeach
                          <tr>
                            <th>Grand Total:</th>
                            <th><h4>₹ @php echo number_format($grand_total,2) @endphp<h4></th>
                          </tr>
                        </table>
                      </div>
                    @endif
 
                </div>
                <!-- /.col -->
                <div class="col-6">

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%">Subtotal:</th>
                        <td align="right"><h4>₹ <span id="grandtTotal"> @php echo number_format($grand_total,2) @endphp</span></h4></td>
                        <td ></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
  
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@include('billing.discount-manage')
@endsection
@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="{{ asset('admin/plugins/datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>

<script type="text/javascript">
// var i=1;  

// $('#add').click(function(){  
//   i++;  
//   $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><select id="payment_type" class="form-control" name="payment_type[]"><option value="1">Cash</option><option value="2">Card</option></select></td><td><input name="payment_amount[]" type="text" placeholder="Amount" class="form-control check_numeric" value=""></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
// });

// $(document).on('click', '.btn_remove', function(){  
//   var button_id = $(this).attr("id");   
//   $('#row'+button_id+'').remove();  
// });


// $('#submitPayment').click(function(){            
//   var forms = $("#paymentForm");
//   $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/store-payment') }}", type: "POST", processData: false, 
//   data: forms.serialize(), dataType: "html",
//   }).done(function (a) {
//       var data = JSON.parse(a);
//       if(data.flagError == false){
//           showSuccessToaster(data.message);
//           setTimeout(function () { 
//             window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
//           }, 2000);

//       }else{
//         showErrorToaster(data.message);
//         printErrorMsg(data.error);
//       }
//   });
// });




// $(document).ready(function(){
//   getInvoiceDetails();
// });



// function getInvoiceDetails(discount = null){
//   // var discount = discount;

//   // var discount = {item_id:"1", discount_type: "amount", value:100 };

//   $.ajax({
//       type: 'post',
//       url: "{{ url(ROUTE_PREFIX.'/billings/get-invoice-data') }}",
//       dataType: 'json',data: { bill_id:bill_id, item_ids:item_ids} , delay: 250,
//       success: function(data) {
//         if(data.flagError == false){
//           $('#invoiceTable').html(data.html);
//           $('#grandtTotal').html(data.grand_total.toFixed(2));
//           $('#grand_total').val(data.grand_total);
          
          
//         }else{
//           showErrorToaster(data.message);
//           printErrorMsg(data.error);
//         }
//       }
//     });
// }

// function manageDiscount(e){
//   var id      = $(e).data("id");
//   var action  = $(e).data("action");
//     $('#billing_item_id').val(id);
//     $('#discount_action').val(action);
//     alert(id)
//   if(action == 'add'){
//     $('#discount_value').val('');
//     $("#discount-modal").modal("show");
//   }else{

//     Swal.fire({
//       title: 'Are you sure want to remove added tax ?',
//       type: 'warning', showCancelButton: true,   confirmButtonText: 'Yes, remove it!'
//       }).then(function(result) {
//           if (result.value) {
//             var forms       = $("#discountForm");
//             $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
//             data: forms.serialize(), dataType: "html",
//             }).done(function (a) {
//                 var data = JSON.parse(a);
//                 if(data.flagError == false){
//                     getInvoiceDetails();
//                 }else{
//                   showErrorToaster(data.message);
//                   printErrorMsg(data.error);
//                 }
//             });
//           }
//       });



      
//   }

// }
  

// if ($("#discountForm").length > 0) {
//     var validator = $("#discountForm").validate({ 
//         rules: {
//             discount_value: {
//                     required: true,
//             },
//         },
//         messages: { 
//             discount_value: {
//                 required: "Please enter discount value",
//             }
//         },
//         submitHandler: function (form) {
//             var forms       = $("#discountForm");
//             $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
//             data: forms.serialize(), dataType: "html",
//             }).done(function (a) {
//                 var data = JSON.parse(a);
//                 if(data.flagError == false){
//                     getInvoiceDetails();
//                     $("#discount-modal").modal("hide");
//                 }else{
//                   showErrorToaster(data.message);
//                   printErrorMsg(data.error);
//                 }
//             });
//         }
//     })
// }

// $('.service-type').select2({ placeholder: "Please choose packages", allowClear: false }).on('select2:select select2:unselect', function (e) { 
//   var type = $(this).data("type");
//   listItemDetails(type) 
//   $(this).valid()
// });

// function listItemDetails(type){
//   var data_ids = $('#'+type).val();
//   if(data_ids != ''){
//     $.ajax({
//         type: 'post',
//         url: "{{ url(ROUTE_PREFIX.'/common/get-taxdetails') }}",
//         dataType: 'json',data: { data_ids:data_ids, type : type},delay: 250,
//         success: function(data) {
//             $("#servicesTable").find("tr:gt(0)").remove();
//             $('#servicesTable').append(data.html);
//             $('#grandTotal').text(data.grand_total);
//             $('#grand_total').val(data.grand_total);
//             $('#usedServicesDiv').show();
//         }
//     });
//   }else{
//     $('#usedServicesDiv').hide();
//   }
// }




//   var path = "{{ route('billing.autocomplete') }}";
//   $('input.typeahead').typeahead({
//       autoSelect: true,
//       source:  function (query, process) {
//       return $.get(path, { search: query }, function (data) {
//               return process(data);
//           });
//       },
//       updater: function (item) {
//         $('#customer_id').val(item.id);
//         getCustomerDetails(item.id);
//         return item;
//       }
//   });

// // 
// $('#billing_address_checkbox').change(function() {
//     if($(this).is(":unchecked")) 
//         $('.billing-address-section').show();
//     else
//         $('.billing-address-section').hide();         
// });


// function getCustomerDetails(customer_id){
//   $.ajax({
//       type: 'POST',
//       url: "{{ url(ROUTE_PREFIX.'/common/get-customer-details') }}",
//       dataType: 'json', data: { customer_id:customer_id},
//       delay: 250,
//       success: function(data) {
//         $("#customer_name").val(data.data.name);
//         $("#customer_mobile").val(data.data.mobile);
//         $("#customer_email").val(data.data.email);
//         $("#customer_details_div").show();
//       }
//   });
// }   


// $(document).on('change', '#service_type', function () {
//   if( this.value == 1 ){
    
//     $("#services_block").show();
//     $("#packages_block").hide();
//     getServices();
//   }else{
    
//     $("#services_block").hide();
//     $("#packages_block").show();
//     getPackages();
//   }
// });

// function getServices(){
//   $.ajax({
//       type: 'GET',
//       url: "{{ url(ROUTE_PREFIX.'/common/get-all-services') }}",
//       dataType: 'json',
//       delay: 250,
//       success: function(data) {
//           var selectTerms = '<option value="">Please choose services</option>';
//           $.each(data.data, function(key, value) {
//             selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
//           });

//           var select = $('#services');
//           select.empty().append(selectTerms);
//       }
//   });
// }

// function getPackages(){
//   $.ajax({
//       type: 'GET',
//       url: "{{ url(ROUTE_PREFIX.'/common/get-all-packages') }}",
//       dataType: 'json',
//       delay: 250,
//       success: function(data) {
//           var selectTerms = '<option value="">Please choose packages</option>';
//           $.each(data.data, function(key, value) {
//             selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
//           });

//           var select = $('#packages');
//           select.empty().append(selectTerms);
//       }
//   });
// }


// if ($("#{{$page->entity}}Form").length > 0) {
//     var validator = $("#{{$page->entity}}Form").validate({ 
//         rules: {
//             customer_name: {
//                     required: true,
//             },
//             search_customer: {
//                     required: true,
//             },
//             "bill_item[]": {
//                     required: true,
//             },
//         },
//         messages: { 
//             customer_name: {
//                 required: "Please select a customer",
//             },
//             search_customer: {
//                 required: "Please select a customer",
//             },
//             "bill_item[]": {
//                 required: "Please select an item",
//             },
//         },
//         submitHandler: function (form) {
//             // var forms = $("#{{$page->entity}}Form");
//             // $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}", type: "POST", processData: false, 
//             // data: forms.serialize(), dataType: "html",
//             // }).done(function (a) {
//             //     var data = JSON.parse(a);
//             //     if(data.flagError == false){
//             //         showSuccessToaster(data.message);
//             //         // setTimeout(function () { 
//             //         //   window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
//             //         // }, 2000);

//             //     }else{
//             //       showErrorToaster(data.message);
//             //       printErrorMsg(data.error);
//             //     }
//             // });
//             form.submit();
//         },
//         errorPlacement: function(error, element) {
//             if (element.is("select")) {
//                 error.insertAfter(element.next('.select2'));
//             }else {
//                 error.insertAfter(element);
//             }
//         }
//     })
// } 

// jQuery.validator.addMethod("lettersonly", function (value, element) {
//   return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
// }, "Letters only please");

// $("body").on("submit", ".ajax-submit", function (e) {
//     e.preventDefault();         
// });

// $(document).on('change', '#country_id', function () {
//     $.ajax({
//           url: "{{ url(ROUTE_PREFIX.'/common/get-states') }}/",
//           type: "GET",
//           data:{'country_id':this.value },
//           dataType: "html"
//       }).done(function (data) {
//       console.log(data);
//         $("#state_block").html(data);
//       })
// });

// $(document).on('change', '#state_id', function () {
//     $.ajax({
//           url: "{{ url(ROUTE_PREFIX.'/common/get-districts') }}/",
//           type: "GET",
//           data:{'state_id':this.value },
//           dataType: "html"
//       }).done(function (data) {
//       console.log(data);
//         $("#district_block").html(data);
//       })
// });

// $("#discount_btn").click(function(){
//   $("#discount-modal").modal("show");
// });

// if ($("#discountForm").length > 0) {
//     var validator = $("#discountForm").validate({ 
//         rules: {
//             discount_value: {
//                     required: true,
//             },
//         },
//         messages: { 
//             discount_value: {
//                 required: "Please enter discount value",
//             }
//         },
//         submitHandler: function (form) {
//             var forms       = $("#discountForm");
//             var grand_total = $("#grand_total").val();
//             $input          = $('<input type="hidden" name="grand_total"/>').val(grand_total);
//             forms.append($input);
//             $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
//             data: forms.serialize(), dataType: "html",
//             }).done(function (a) {
//                 var data = JSON.parse(a);
//                 if(data.flagError == false){
//                     $('#discountAmount').text('Discount Amount : ' + data.discount_value);
//                     $('#afterdiscount').text('After discount : ' + data.amount);
//                     $('#grand_total').val(data.amount);
//                     $("#discount-modal").modal("hide");
//                 }else{
//                   showErrorToaster(data.message);
//                   printErrorMsg(data.error);
//                 }
//             });
//         }
//     })
// } 



</script>
@endpush
