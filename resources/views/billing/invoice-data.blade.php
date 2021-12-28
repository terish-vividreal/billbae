
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
      @foreach($billing_items->toArray() as $key => $item)
          <tr id="{{$item['id'] }}">
            <td>{{$loop->index + 1}}</td>
            <td>{{ $item['name'] }} ( {{ $item['tax_array']['tax_method'] }} )</td>
            <td>{{ $item['hsn_code'] }}</td>            
            <td class="right-align">

              <ul>
                  <li class="display-flex justify-content-between">
                    <span class="invoice-subtotal-title">Amount (Without Tax)</span>
                    <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($item['tax_array']['amount'],2)  @endphp</h6>
                  </li>
                  @if($item['tax_array']['cgst'] > 0)
                  <li class="display-flex justify-content-between">
                    <span class="invoice-subtotal-title">{{ $item['tax_array']['cgst_percentage'] }} % CGST </span>
                    <h6 class="invoice-subtotal-value indigo-text">₹ {{ $item['tax_array']['cgst'] }}</h6>
                  </li>
                  @endif
                  @if($item['tax_array']['sgst'] > 0)
                  <li class="display-flex justify-content-between">
                    <span class="invoice-subtotal-title">{{ $item['tax_array']['sgst_percentage'] }} % SGST</span>
                    <h6 class="invoice-subtotal-value indigo-text">₹ {{ $item['tax_array']['sgst'] }}</h6>
                  </li>
                  @endif
                  @php if(count($item['tax_array']['additiona_array']) > 0) { @endphp
                      <li class="divider mt-2 mb-2"></li>
                    @foreach($item['tax_array']['additiona_array'] as $key => $additional)                    
                      <li class="display-flex justify-content-between">
                        <span class="invoice-subtotal-title">{{ $additional['percentage'] }} % {{ $additional['name'] }}</span>
                        <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($additional['amount'],2) @endphp</h6>
                      </li>
                    @endforeach
                  @php } @endphp

                  @php 
                    $grand_total_amount = $item['tax_array']['total_amount'];
                  @endphp


                  @if($item['tax_array']['discount_applied'] == 1)
                    @php 
                      $grand_total_amount = $item['tax_array']['total_amount'] - $item['tax_array']['discount_amount'];
                    @endphp
                    <li class="divider mt-2 mb-2"></li>
                    <li class="display-flex justify-content-between">
                      <span class="invoice-subtotal-title">Discount @if($item['tax_array']['discount_type'] == 'percentage') ({{$item['tax_array']['discount_value']}}%) @endif </span>
                      <h6 class="invoice-subtotal-value indigo-text">- ₹ @php echo number_format($item['tax_array']['discount_amount'],2) @endphp</h6>
                    </li>
                  @endif

                  <div id="discountDiv">
                    @if($item['tax_array']['discount_applied'] == 0)

                    <!-- <a class="btn-flat mb-1 waves-effect tooltipped" data-position="bottom" data-tooltip="Add Discount" >Discount<i class="material-icons right">add</i></a> -->
                    <span class="new badge" data-badge-caption="Discount" data-id="{{$item['billingItemsId']}}" data-action="add" onClick="manageDiscount(this)"><i class="material-icons right">add</i></span>
                    
                    @else
                    <!-- <a class="btn-flat mb-1 waves-effect tooltipped" data-position="bottom" data-tooltip="Remove Discount" >Discount<i class="material-icons right">remove_circle</i></a> -->
                      <!-- <button class="btn btn-sm btn-primary" data-id="{{$item['billingItemsId']}}" data-action="remove" onClick="manageDiscount(this)" >Remove Discount</button> -->
                      <span class="new badge" data-badge-caption="Discount" data-id="{{$item['billingItemsId']}}" data-action="remove" onClick="manageDiscount(this)"><i class="material-icons right">remove</i></span>
                    @endif
                  </div>

                  

                  <li class="divider mt-2 mb-2"></li>
                  <li class="display-flex justify-content-between">
                    <span class="invoice-subtotal-title">Total</span>
                    <h6 class="invoice-subtotal-value indigo-text">₹ @php echo number_format($grand_total_amount,2)  @endphp</h6>
                  </li>

                </ul>
            </td>
        </tr>
        @php 

        @endphp
      @endforeach
    @endif

    </tbody>
  </table>

                