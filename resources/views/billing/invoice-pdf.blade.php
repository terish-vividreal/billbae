<!DOCTYPE html>
<html>
<head>
<!-- <link rel="stylesheet" href="{{ public_path('admin/css/adminlte.min.css') }}"> -->
<link rel="stylesheet" type="text/css" href="{{public_path('admin/vendors/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{public_path('admin/css/custom/custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{public_path('admin/css/pages/app-invoice.css')}}">
<link rel="stylesheet" type="text/css" href="{{public_path('admin/css/pages/page-users.css')}}">
<style>

</style>
<title>{{ $store->billing->company_name ?? '' }} - Invoice</title>
</head>
<body>
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
                  <span>{{$billing->formatted_billed_date}}</span>
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
              <img src="" alt="Billbae">
            </div>
            <div class="col m6 s12 pull-m6">
              <h4 class="indigo-text">Invoice</h4>
            </div>
          </div>
          <div class="divider mb-3 mt-3"></div>
          <!-- invoice address and contact -->
          <!-- invoice address and contact -->
          <div class="row invoice-info">
            <div class="col m6 s12">
              <h6 class="invoice-from">Bill From</h6>
              <div class="invoice-address">
                <span>{{ $store->billing->company_name ?? '' }}</span>
              </div>
              <div class="invoice-address">
              <span>{{ $store->email ?? '' }}</span>
              </div>
              <div class="invoice-address">
                <span>{{ $store->contact ?? '' }}</span>                
              </div>
              <div class="invoice-address">
                <span>{{ $store->billing->address ?? '' }}</span>
                
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
            <table class="responsive-table">
              <thead>
              <tr>
                <th>No</th>
                <th>Items</th>
                <th>HSN Code #</th>
                <th class="right-align">Details</th>
              </tr>
              </thead>
              <tbody>
              @if($billing_items)
                @foreach($billing_items as $key => $item)
                  <tr id="{{$item['id'] }}">
                    <td>{{$loop->index + 1}}</td>
                    <td>{{ $item->name }} </td>
                    <td>{{ $item->hsn_code }}</td>
                    <td class="right-align">
                      <ul>
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">Amount (Without Tax)</span>
                          <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($item->tax_amount ,2)  @endphp</h6>
                        </li>
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">{{ $item->cgst_percentage }} % CGST </span>
                          <h6 class="invoice-subtotal-value indigo-text">₹{{ $item->cgst_amount }}</h6>
                        </li>
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">{{$item->sgst_percentage }} % SGST</span>
                          <h6 class="invoice-subtotal-value indigo-text">₹ {{ $item->sgst_amount }}</h6>
                        </li>
                        @php if(count($item->additionalTax) > 0) { @endphp
                          <li class="divider mt-2 mb-2"></li>
                          @foreach($item->additionalTax as $key => $additional)
                            <li class="display-flex justify-content-between">
                              <span class="invoice-subtotal-title"> {{ $additional->percentage }} % {{ $additional->tax_name }}</span>
                              <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($additional->amount,2)  @endphp</h6>
                            </li>
                          @endforeach
                        @php } @endphp

                        

                        @if($item->is_discount_used == 1)
                          <li class="divider mt-2 mb-2"></li>
                          <li class="display-flex justify-content-between">
                            <span class="invoice-subtotal-title">Discount @if($item->discount_type == 'percentage') ({{$item->discount_value}}%) @endif </span>
                            <h6 class="invoice-subtotal-value indigo-text">

                            @if($item->discount_type == 'percentage') 
                              @php $discount_value = $item->grand_total * (($item->discount_value/100)) @endphp
                            @else 
                              @php $discount_value = $item->discount_value; @endphp                
                            @endif

                            @php 
                              $grand_total = $grand_total - $discount_value;
                            @endphp

                            - ₹ @php echo number_format($discount_value,2)  @endphp

                            </h6>
                          </li>

                        @else
                          @php 
                            $discount_value = 0;
                          @endphp
                        @endif

                        <li class="divider mt-2 mb-2"></li>
                        <li class="display-flex justify-content-between">
          
                        <span class="invoice-subtotal-title">Total</span>
                          <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format(($item->grand_total - $discount_value),2) @endphp</h6>
                        </li>



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
                <p>Thanks for your business.</p>
              </div>
              <div class="col xl4 m7 s12 offset-xl3">
                <h6 class="lead">Payment Details:</h6>
                @if($billing->paymentMethods)
                <ul>
                  @foreach($billing->paymentMethods as $row)
                    <li class="display-flex justify-content-between">
                      <span class="invoice-subtotal-title">{{ $row->paymentype->name }}</span>
                      <h6 class="invoice-subtotal-value">₹ @php echo number_format($row->amount,2) @endphp</h6>
                    </li>
                  @endforeach
                  <li class="divider mt-2 mb-2"></li>
                  <li class="display-flex justify-content-between">
                    <h5><span class="invoice-subtotal-title">Grand Total</span></h5>
                    <h5 class="invoice-subtotal-value">₹ @php echo number_format($grand_total,2) @endphp</h5>
                  </li>
                </ul>
                @endif
              </div>
            </div>
          </div>






          




        </div>
      </div>
    </div>
  </div>
</section>
  
</body>
</html>