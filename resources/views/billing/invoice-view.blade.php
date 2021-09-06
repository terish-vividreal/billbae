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
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route.'/invoice/edit/'.$billing->id) }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Edit Bill<i class="material-icons right">mode_edit</i></a>
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
              <span>{{$billing->billing_code}}</span>
            </div>
            <div class="col xl8 s12">
              <div class="invoice-date display-flex align-items-center flex-wrap">
                <div class="mr-3">
                  <small>Billed Date:</small>
                  <span>{{$billing->billed_date}}</span>
                </div>
                <!-- <div>
                  <small>Date Due:</small>
                  <span>08/10/2019</span>
                </div> -->
              </div>
            </div>
          </div>


          <!-- logo and title -->
          <div class="row mt-3 invoice-logo-title">
            <div class="col m6 s12 display-flex invoice-logo mt-1 push-m6">
              <img src="{{ $variants->store->show_image }}" alt="logo" height="100">
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
                  <span>Pincode : {{ $billing->customer->billingaddress->pincode ?? ''}}, GST: {{ $billing->customer->billingaddress->gst ?? '' }}</span>
                </div>
                <div class="invoice-address">
                  <span>{{ $billing->customer->billingaddress->shopCountry->name ?? ''}},  {{ $billing->customer->billingaddress->ShopState->name ?? '' }}, {{ $billing->customer->billingaddress->ShopDistrict->name ?? '' }} </span>
                </div>
              @endif
            </div>
          </div>
          <div class="divider mb-3 mt-3"></div>




          <!-- product details table-->
          <div class="invoice-product-details">
          <table class="striped responsive-table">
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
            </table>
          </div>
          <!-- invoice subtotal -->
          <div class="divider mt-3 mb-3"></div>



          <div class="invoice-subtotal">
            <div class="row">
              <div class="col m5 s12">
                <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
                  <p class="lead">Payment Details:</p>
                    @if($billing->paymentMethods)
                      <div class="striped responsive-table">
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
              <!-- <div class="col xl4 m4 s12 offset-xl3">
                <ul>
                  <li class="display-flex justify-content-between">
                    <span class="invoice-subtotal-title">Subtotal</span>
                    <h5 class="invoice-subtotal-value">₹ <span id="grandtTotal"> </span></h5>
                  </li>
                </ul>
              </div> -->
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
            <a href="#"
              class="btn indigo waves-effect waves-light display-flex align-items-center justify-content-center">
              <i class="material-icons mr-4">check</i>
              <span class="text-nowrap">Send Invoice</span>
            </a>
          </div>
          <div class="invoice-action-btn">
            <a href="#" class="btn-block btn btn-light-indigo waves-effect waves-light invoice-print">
              <span>Print</span>
            </a>
          </div>
          <div class="invoice-action-btn">
            <a href="{{asset('app-invoice-edit')}}" class="btn-block btn btn-light-indigo waves-effect waves-light">
              <span>Edit Invoice</span>
            </a>
          </div>
          <div class="invoice-action-btn">
            <a href="#" class="btn waves-effect waves-light display-flex align-items-center justify-content-center">
              <i class="material-icons mr-3">attach_money</i>
              <span class="text-nowrap">Add Payment</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

{{-- vendor scripts --}}
@section('vendor-script')
@endsection


@push('page-scripts')
<script src="{{asset('admin/js/scripts/app-invoice.js')}}"></script>
<script src="{{ asset('admin/js/common-script.js') }}"></script>

<script>


</script>
@endpush

