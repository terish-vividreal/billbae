
  <div id="paymentType-modal" class="modal">
    <form id="paymentTypeForm" name="paymentTypeForm" role="form" method="POST" action="" class="ajax-submit">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Payment Type Form</h4> </div>
            {{ csrf_field() }}
                {!! Form::hidden('paymentType_id', '' , ['id' => 'paymentType_id'] ); !!}
                <div class="card-body">
                    <div class="row">
                      <div class="input-field col s12">
                        {!! Form::text('name', '', ['id' => 'paymentTypename']) !!}
                        <label for="paymentTypename" class="label-placeholder">Payment Type<span class="red-text">*</span></label>
                      </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <button class="btn waves-effect waves-light modal-action modal-close" type="reset" id="paymentTypenameResetForm">Close</button>
            <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="paymentTypename-submit-btn">Submit <i class="material-icons right">send</i></button>
        </div>
    </form>
  </div>