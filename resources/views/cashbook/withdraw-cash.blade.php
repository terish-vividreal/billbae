<div id="withdraw-cash-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Withdraw Cash Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger alert-messages print-error-msg" style="display:none;"><ul></ul></div>
          <div class="alert alert-success fade alert-messages print-success-msg" style="display:none;"></div>
          <form id="withdrawCashForm" name="withdrawCashForm" role="form" method="POST" action="" class="ajax-submit">
            {{ csrf_field() }}
            <div class="card-body">
              <div class="form-group">
                {!! Form::label('cash_book', 'Withdraw cash from *', ['class' => '']) !!}
                {!! Form::select('cash_book', [1 => 'Business Cash', 2 => 'Petty Cash'] , '' , ['id' => 'withdraw_cash_book' ,'class' => 'form-control','placeholder'=>'Please select']) !!}
              </div>
              <div class="form-group">
                {!! Form::label('amount', 'Amount *', ['class' => '']) !!}
                {!! Form::text('amount',  '' , ['id' => 'withdraw_amount' ,'class' => 'form-control check_numeric']) !!}
              </div>
              <div class="form-group">
                {!! Form::label('details', 'Details ', ['class' => '']) !!}
                {!! Form::textarea('withdraw_details',null,['class'=>'form-control', 'rows' => 2, 'cols' => 40]) !!}
              </div>                
            </div>
            <div class="modal-footer">					
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-success ajax-submit" id="withdraw-submit-btn">Submit</button>
            </div>
          </form>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
</div>