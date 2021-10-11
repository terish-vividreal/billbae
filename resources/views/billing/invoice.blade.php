@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/app-invoice.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Create</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/invoice/edit/'.$billing->id) }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Edit Invoice<i class="material-icons right">mode_edit</i></a>
  @endsection
<section class="invoice-view-wrapper section">
  <div class="row">
    <!-- invoice view page -->
    <div class="col xl9 m8 s12">
      <div class="card">
        <div class="card-content invoice-print-area">
          <!-- header section -->
          <div class="row invoice-date-number">
            <div class="col xl4 s12">
              <span class="invoice-number mr-1">Invoice#</span>
            </div>
          </div>
          <!-- logo and title -->
          <div class="row mt-3 invoice-logo-title">
            <div class="col m6 s12 display-flex invoice-logo mt-1 push-m6">
              <img src="{{ $variants->store->show_image }}" alt="logo">
            </div>
            <div class="col m6 s12 pull-m6">
              <h4 class="indigo-text">Invoice</h4>
            </div>
          </div>
          <div class="divider mb-3 mt-3"></div>
          <!-- invoice address and contact -->
          <div class="row invoice-info">
            <div class="col m6 s12">
              <h6 class="invoice-from">Bill From</h6>
              <div class="invoice-address">
                <span>{{ $variants->store->billing->company_name ?? '' }}</span>
              </div>
              <div class="invoice-address">
              <span>{{ $variants->store->email ?? '' }}</span>
              </div>
              <div class="invoice-address">
                <span>{{ $variants->store->contact ?? '' }}</span>                
              </div>
              <div class="invoice-address">
                <span>{{ $variants->store->billing->address ?? '' }}</span>
                
              </div>
            </div>
            <div class="col m6 s12">
              <div class="divider show-on-small hide-on-med-and-up mb-3"></div>
              <h6 class="invoice-to">Bill To</h6>
              <div class="invoice-address">
                <span>{{ $billing->customer->name ?? '' }}.</span>
              </div>
              <div class="invoice-address">
                <span>{{ $billing->customer->mobile ?? '' }}</span>
              </div>
              <div class="invoice-address">
                <span>{{ $billing->customer->email ?? '' }}</span>
              </div>
              @if($billing->address_type == 'customer')
                <div class="invoice-address">                
                  <span>{{ $billing->customer->address ?? '' }}</span>
                </div>                
              @else
                <div class="invoice-address">                
                  <span>{{ $billing->customer->billingaddress->billing_name ?? '' }}</span>
                </div>
                <div class="invoice-address">
                  <span>{{ $billing->customer->billingaddress->address ?? '' }}</span>
                </div>
                <div class="invoice-address">
                  <span>
                  @if(!empty($billing->customer->billingaddress->pincode)) Pincode : {{ $billing->customer->billingaddress->pincode ?? ''}} ,  @endif
                  @if(!empty($billing->customer->billingaddress->gst)) GST : {{ $billing->customer->billingaddress->gst ?? ''}}  @endif
                  </span>
                </div>
                <div class="invoice-address">
                  <span>{{ $billing->customer->billingaddress->shopCountry->name ?? ''}},  {{ $billing->customer->billingaddress->ShopState->name ?? '' }}, {{ $billing->customer->billingaddress->ShopDistrict->name ?? '' }} </span>
                </div>
              @endif
            </div>
          </div>
          <div class="divider mb-3 mt-3"></div>


          <!-- product details table-->
          <div class="invoice-product-details" id="invoiceTable"></div>
          <!-- invoice subtotal -->

          <div class="invoice-subtotal">
            <div class="row">
              <div class="col m7 s12">
                <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
                  <h6 class="lead" style="padding-top:25px">Payment Methods:</h6>
                  <form id="paymentForm" name="paymentForm" role="form" method="POST" action="" class="ajax-submit">
                    {{ csrf_field() }}
                    {!! Form::hidden('billing_id', $billing->id ?? '' , ['id' => 'payment_billing_id'] ); !!}
                    {!! Form::hidden('grand_total', $billing->amount ?? '' , ['id' => 'grand_total'] ); !!}
                      <table class="table" id="dynamic_field"> 
                        <tr> 
                            
                            <td><div class="input-field">{!! Form::select('payment_type[]', $variants->payment_types , '' , ['id' => 'payment_type' , 'class' => 'select2 browser-default']) !!}  </div> </td>
                            <td><input name="payment_amount[]" type="text" placeholder="Amount" class="heck_numeric" value=""></td>  
                            <td> <button type="button" name="add" id="add" class="btn-floating mb-1 btn-flat waves-effect waves-light blue accent-2 white-text tooltipped" data-position="bottom" data-tooltip="Add Row"><i class="material-icons">add</i></button></td>  
                        
                          </tr>  
                      </table>
                  </form>
              </div>
              <div class="col m5 s12">
                <ul>
                  <li class="display-flex justify-content-between">
                    <h5><span class="invoice-subtotal-title">Grand Total</span></h5>
                    <h5 class="invoice-subtotal-value">₹ <span id="grandtTotal"> </span></h5>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="input-field col s12">
              <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="submitPayment">Submit Payment <i class="material-icons right">send</i></button>
            </div>
          </div>


        </div>
      </div>
    </div>
    <!-- invoice action  -->
    <div class="col xl3 m4 s12">
      <div class="card invoice-action-wrapper">
        <div class="card-content">
          <div class="invoice-action-btn">
            <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/invoice/edit/'.$billing->id) }}" class="btn-block btn btn-light-indigo waves-effect waves-light">
              <span>Edit Invoice</span>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
@include('billing.new-customer-manage')
@include('billing.discount-manage')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
@endsection


@push('page-scripts')
<script src="{{asset('admin/js/scripts/app-invoice.js')}}"></script>
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>

var bill_id       = {!! json_encode($variants->bill_id) !!};
var paymentTypes  = {!! json_encode($variants->payment_types) !!};
var i=1;
// $('.select2').select2({ placeholder: "Please select country", allowClear: true });
$(".select2").select2({ placeholder: "Please select payment type", allowClear: true });

$(document).ready(function(){
  getInvoiceDetails();
});

function getInvoiceDetails(discount = null){
  var item_ids = {!! json_encode($variants->item_ids) !!};
  $.ajax({ type: 'post',
      url: "{{ url(ROUTE_PREFIX.'/billings/get-invoice-data') }}", dataType: 'json',data: { bill_id:bill_id, item_ids:item_ids} , delay: 250,
      success: function(data) {
        if(data.flagError == false){
          $('#invoiceTable').html(data.html);
          $('.tooltipped').tooltip();
          $('#grandtTotal').html(data.grand_total.toFixed(2));
          $('#grand_total').val(data.grand_total);
        }else{
          showErrorToaster(data.message);
          printErrorMsg(data.error);
        }
      }
    });
}

function manageDiscount(e){
  var id      = $(e).data("id");
  var action  = $(e).data("action");
    $('#billing_item_id').val(id);
    $('#discount_action').val(action);
  if(action == 'add'){
    $('#discount_value').val('');
    $("#discount-modal").modal("open");
  }else{

    swal({ title: 'Are you sure you want to remove the discount added?', icon: 'warning', dangerMode: true,
			buttons: {
				cancel: 'No, Please!',
				delete: 'Yes, Remove It'
			}
		}).then(function (willDelete) {
			if (willDelete) {
        var forms       = $("#discountForm");
          $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
          data: forms.serialize(), dataType: "html",
          }).done(function (a) {
              var data = JSON.parse(a);
              if(data.flagError == false){
                  getInvoiceDetails();
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }
          });
			} 
		});   
  }

}

if ($("#discountForm").length > 0) {
  var validator = $("#discountForm").validate({ 
      rules: {
          discount_value: {
                  required: true,
          },
      },
      messages: { 
          discount_value: {
              required: "Please enter discount value",
          }
      },
      submitHandler: function (form) {
          var forms       = $("#discountForm");
          $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/manage-discount') }}", type: "POST", processData: false, 
          data: forms.serialize(), dataType: "html",
          }).done(function (a) {
              var data = JSON.parse(a);
              if(data.flagError == false){
                  getInvoiceDetails();
                  $("#discount-modal").modal("close");
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }
          });
      }
  })
}

$('#add').click(function(){  
  let html = '';
  i++;  
  html += '<tr id="row'+i+'" class="dynamic-added"><td><select id="payment_type" class="select2 browser-default" name="payment_type[]">'
  $.each( paymentTypes, function( key, value) {
    html += '<option value="'+key+'">'+value+'</option>'
  });
  html += '</select></td><td><input name="payment_amount[]" type="text" placeholder="Amount" class="form-control check_numeric" value=""></td><td>'
  html += '<button type="button" name="remove" id="'+i+'" class="dropdown-trigger btn-floating mb-1 btn-flat waves-effect waves-light red accent-2 white-text tooltipped btn_remove" data-position="bottom" data-tooltip="Remove Row"><i class="material-icons">clear</i></button>';
  html += '</td></tr>';
  $('#dynamic_field').append(html);  

});

$(document).on('click', '.btn_remove', function(){  
  var button_id = $(this).attr("id");   
  $('#row'+button_id+'').remove();  
});

$('#submitPayment').click(function(){ 
    $('#submitPayment').html('Please Wait...');
    $("#submitPayment"). attr("disabled", true);           
  var forms = $("#paymentForm");
  $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/store-payment') }}", type: "POST", processData: false, 
  data: forms.serialize(), dataType: "html",
  }).done(function (a) {
      var data = JSON.parse(a);
      if(data.flagError == false){
          showSuccessToaster(data.message);
          setTimeout(function () { 
            window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route.'/show/'.$billing->id) }}";                
          }, 2000);

      }else{
        $('#submitPayment').html('<i class="far fa-credit-card"></i> Submit Payment');
        $("#submitPayment"). attr("disabled", false);
        showErrorToaster(data.message);
        printErrorMsg(data.error);
      }
  });
});

</script>
@endpush

