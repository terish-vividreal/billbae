<html>
<head>
  <style>
    body {
        font-family: sans-serif;
        font-size: 10pt;
    }

    p {
        margin: 0pt;
    }

    table.items {
        border: 0.1mm solid #e7e7e7;
    }

    td {
        vertical-align: top;
    }

    .items td {
        border-left: 0.1mm solid #e7e7e7;
        border-right: 0.1mm solid #e7e7e7;
    }

    table thead td {
        text-align: center;
        border: 0.1mm solid #e7e7e7;
    }

    .items td.blanktotal {
        background-color: #EEEEEE;
        border: 0.1mm solid #e7e7e7;
        background-color: #FFFFFF;
        border: 0mm none #e7e7e7;
        border-top: 0.1mm solid #e7e7e7;
        border-right: 0.1mm solid #e7e7e7;
    }

    .items td.totals {
        text-align: right;
        border: 0.1mm solid #e7e7e7;
    }

    .items td.cost {
        text-align: "."center;
    }
  </style>
</head>

<body>
      <table width="100%" style="font-family: sans-serif;" cellpadding="10">
          <tr>
              <td width="100%" style="padding: 0px; text-align: center;">
                <a href="#" target="_blank"><img src="{{ storage_path('app/public/store/logo/'.$store->image) }}" width="264" height="110" alt="Logo" align="center" border="0"></a>
              </td>
          </tr>
          <tr>
              <td width="100%" style="color:red; text-align: center; font-size: 20px; font-weight: bold; padding: 0px;">
                INVOICE #{{$billing->billing_code}} 
              </td>
          </tr>
          <tr>
            <td height="10" style="font-size: 0px; line-height: 10px; height: 10px; padding: 0px;">&nbsp;</td>
          </tr>
      </table>
      <table width="100%" style="font-family: sans-serif;" cellpadding="10">
          <tr>
              <td width="49%" style="border: 0.1mm solid #eee;">
              <strong>Name<br>{{ $billing->customer->name ?? '' }}</strong><br>
                {{ $billing->customer->mobile ?? '' }}<br>
                {{ $billing->customer->email ?? '' }}<br>

                @if($billing->address_type == 'customer')
                  {!! $billing->customer_address ?? '' !!}             
                @else
                  {{ $billing->customer->billingaddress->billing_name ?? '' }}<br>
                  {{ $billing->customer->billingaddress->address ?? '' }}<br>
                  @if(!empty($billing->customer->billingaddress->pincode)) Pincode : {{ $billing->customer->billingaddress->pincode ?? ''}} ,  @endif <br>
                  @if(!empty($billing->customer->billingaddress->gst)) GST : {{ $billing->customer->billingaddress->gst ?? ''}}  @endif<br>
                  {{ $billing->customer->billingaddress->shopCountry->name ?? ''}},  {{ $billing->customer->billingaddress->ShopState->name ?? '' }}, {{ $billing->customer->billingaddress->ShopDistrict->name ?? '' }}<br>
                @endif
              </td>
              <td width="2%">&nbsp;</td>
              <td width="49%" style="border: 0.1mm solid #eee; text-align: right;"><strong>{{ $store->billing->company_name ?? '' }}</strong><br>
              <strong>Email:</strong> {{ $store->email ?? '' }}<br>
              <strong>Telephone:</strong> {{ $store->contact ?? '' }}<br>
              @if($store->billing->gst != '') <strong>GST:</strong>  {{ $store->billing->gst ?? '' }} @endif <br>
              <strong>Address:</strong> {{ $store->billing->address ?? '' }}<br>
              @if($store->country->name != '') {{ $store->country->name ?? '' }} @endif ,
              @if($store->state != '') {{ $store->state ?? '' }} @endif ,  @if($store->district != '') {{ $store->district ?? '' }} @endif 

            </td>
          </tr>
      </table>

      <div class="divider mb-3 mt-3"></div>
      <!-- product details table-->
      <div class="invoice-product-details">
        <table class="responsive-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Items</th>
              <th>SAC Code #</th>
              <th class="right-align">Details</th>
            </tr>
            </thead>
            <tbody>
              @if($billing_items)
                @foreach($billing_items as $key => $item)
                  <tr id="{{$item['id'] }}">
                    <td>{{$loop->index + 1}}</td>
                    <td>{{ $item->item_details }} </td>
                    <td>{{ $item->hsn_code }}</td>
                    <td class="right-align">
                      <ul>
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">Amount (Without Tax)</span>
                          <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($item->tax_amount ,2)  @endphp</h6>
                        </li>
                        @if($item->cgst_amount > 0)
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">{{ $item->cgst_percentage }} % CGST </span>
                          <h6 class="invoice-subtotal-value indigo-text">₹{{ $item->cgst_amount }}</h6>
                        </li>
                        @endif
                        @if($item->sgst_amount > 0)
                        <li class="display-flex justify-content-between">
                          <span class="invoice-subtotal-title">{{$item->sgst_percentage }} % SGST</span>
                          <h6 class="invoice-subtotal-value indigo-text">₹ {{ $item->sgst_amount }}</h6>
                        </li>
                        @endif
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
  
</body>
</html>