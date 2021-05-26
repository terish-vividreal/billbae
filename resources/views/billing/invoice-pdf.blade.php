<!DOCTYPE html>
<html>
<head>
<!-- <link rel="stylesheet" href="{{ public_path('admin/css/adminlte.min.css') }}"> -->

<title>{{ $store->billing->company_name ?? '' }} - Invoice</title>
</head>
<body>

      <div class="invoice p-3 mb-3">
        <!-- title row -->
        <div class="row">
          <div class="col-12">
            <h4>
              <i class="fas fa-globe"></i> {{ $store->billing->company_name ?? '' }}
              <small class="float-right">Date: {{ date('d-m-Y')}}</small>
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
              {{ $store->billing->address ?? '' }}<br>
              {{ $store->state ?? '' }}, {{ $store->district ?? '' }} <br>
              {{ $store->location ?? '' }},  Pin - {{ $store->pincode ?? '' }}<br>
              Phone: {{ $store->contact ?? '' }} <br>
              Email: {{ $store->email ?? '' }}
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
                {{ $billing->customer->billingaddress->state->name }}, 
                {{ $billing->customer->billingaddress->district->name }},  Pin  - {{ $billing->customer->billingaddress->pincode ?? '' }} <br>
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

              @php //echo "<pre>" ;//print_r($billing_items->toArray()); exit; @endphp
                @foreach($billing_items->toArray() as $key => $item)
                  <tr id="{{$item['id'] }}">
                    <td>{{$loop->index + 1}}</td>
                    <td>{{ $item['name'] }} ( {{ $item['tax_array']['tax_method'] }} )</td>
                    <td>{{ $item['hsn_code'] }}</td>
                    <td>
                      Amount (Without Tax) - <br>
                      {{ $item['tax_array']['cgst_percentage'] }} % CGST - <br> 
                      {{ $item['tax_array']['sgst_percentage'] }} % SGST - <br> 
                      

                      @php if(count($item['tax_array']['additiona_array']) > 0) { @endphp
                        @foreach($item['tax_array']['additiona_array'] as $key => $additional)
                            {{ $additional['percentage'] }} % {{ $additional['name'] }} - <br> 
                        @endforeach
                      @php } @endphp

                      @if($item['tax_array']['discount_applied'] == 1)
                        Discount - <br>
                      @endif
                      <br><b> Total - <b>
                        

                    </td>
                    <td>
                        &#8377; @php echo number_format($item['tax_array']['amount'],2)  @endphp <br>                              
                        &#8377; {{ $item['tax_array']['cgst'] }} <br>
                        &#8377; {{ $item['tax_array']['sgst'] }} <br>
                        

                          @php if(count($item['tax_array']['additiona_array']) > 0) { @endphp
                            @foreach($item['tax_array']['additiona_array'] as $key => $additional)
                              &#8377; @php echo number_format($additional['amount'],2)  @endphp<br>
                            @endforeach
                          @php } @endphp
                          
                          @if($item['tax_array']['discount_applied'] == 1)
                              &#8377; @php echo number_format($item['tax_array']['discount_amount'],2) @endphp <br>

                              <br><b>&#8377; @php echo number_format(($item['tax_array']['total_amount'] - $item['tax_array']['discount_amount']),2) @endphp</b><br>
                          
                          @else
                            <br><b> &#8377; @php echo number_format($item['tax_array']['total_amount'],2)  @endphp </b><br>
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
          <!-- /.col -->
          <div class="col-6">

            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th style="width:50%">Subtotal:</th>
                  <td style="text-align:right;"><h3>&#8377; <span id="grandtTotal"> @php echo number_format($grand_total,2) @endphp </span></h3></td>
                  <td ></td>
                </tr>
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>



  
</body>
</html>