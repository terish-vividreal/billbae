<div id="discount-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">{{ $page->title ?? ''}} Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-messages print-error-msg" style="display:none;"><ul></ul></div>
                <div class="alert alert-success fade alert-messages print-success-msg" style="display:none;"></div>
                <form id="discountForm" name="discountForm" role="form" method="POST" action="" class="ajax-submit">
                    {!! Form::hidden('billing_id', $billing->id , ['id' => 'billing_id'] ); !!}
                    {!! Form::hidden('billing_item_id', '' , ['id' => 'billing_item_id'] ); !!}
                    {!! Form::hidden('discount_action', '' , ['id' => 'discount_action'] ); !!}

                    {{ csrf_field() }}
                    <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('name', 'Discount Type', ['class' => '']) !!}
                        <select name="discount_type" id="discount_type" class="form-control">
                            <option value="amount"> Amount </option>
                            <option value="percentage"> Percentage </option>

                        </select>
                    </div>
                    <div class="form-group">
                        {!! Form::label('discount_value', 'Discount value', ['class' => '']) !!}
                        {!! Form::text('discount_value', '', ['class' => 'form-control check_numeric', 'id' => 'discount_value', 'placeholder'=>'Discount value']) !!}
                    </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="modal-footer">					
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success ajax-submit">Submit</button>
                    </div>
                </form>
            </div>

          </div>
          <!-- /.modal-content -->
    </div>
</div>