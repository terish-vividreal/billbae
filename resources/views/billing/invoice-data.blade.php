
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
              ₹ @php echo number_format($item['tax_array']['amount'],2)  @endphp <br>                              
              ₹ {{ $item['tax_array']['cgst'] }} <br>
              ₹ {{ $item['tax_array']['sgst'] }} <br>
              

                @php if(count($item['tax_array']['additiona_array']) > 0) { @endphp
                  @foreach($item['tax_array']['additiona_array'] as $key => $additional)
                    ₹ @php echo number_format($additional['amount'],2)  @endphp<br>
                  @endforeach
                @php } @endphp
                
                @if($item['tax_array']['discount_applied'] == 1)
                    ₹ @php echo number_format($item['tax_array']['discount_amount'],2) @endphp <br>

                    <br><b>₹ @php echo number_format(($item['tax_array']['total_amount'] - $item['tax_array']['discount_amount']),2) @endphp</b><br>
                
                @else
                  <br><b>₹ @php echo number_format($item['tax_array']['total_amount'],2)  @endphp </b><br>
                @endif

              
              <div id="discountDiv">
                @if($item['tax_array']['discount_applied'] == 0)
                  <button  class="btn btn-sm btn-primary" data-id="{{$item['billingItemsId']}}" data-action="add" onClick="manageDiscount(this)" >Add Discount</button>
                @else
                  <button class="btn btn-sm btn-primary" data-id="{{$item['billingItemsId']}}" data-action="remove" onClick="manageDiscount(this)" >Remove Discount</button>
                @endif
              </div>
          </td>
        </tr>
        @php 

        @endphp
      @endforeach
    @endif

    </tbody>
  </table>

                