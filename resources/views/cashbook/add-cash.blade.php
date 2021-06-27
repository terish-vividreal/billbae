<div id="add-cash-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Cash Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-messages print-error-msg" style="display:none;"><ul></ul></div>
                <div class="alert alert-success fade alert-messages print-success-msg" style="display:none;"></div>
              <form id="addCashForm" name="addCashForm" role="form" method="POST" action="" class="ajax-submit">
                  {{ csrf_field() }}
                  <div class="card-body">
                    <div class="form-group">
                      {!! Form::label('cash_book', 'Add cash to *', ['class' => '']) !!}
                      {!! Form::select('cash_book', [1 => 'Business Cash', 2 => 'Petty Cash'] , '' , ['id' => 'add_cash_book' ,'class' => 'form-control','placeholder'=>'Please select']) !!}
                    </div>

                    <div class="col-sm-6 form-group col-sm-6 col-xs-12 display-none" id="cashOptionDiv" style="display:none;">
                    {!! Form::label('name', 'Choose Cash option', ['class' => '']) !!}
                      <!-- radio -->
                      <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="radioPrimary1" value="add_cash" name="transaction" checked="">
                          <label for="radioPrimary1">Add cash  </label>
                        </div>
                        <div class="icheck-primary d-inline">
                          <input type="radio" id="radioPrimary2" value="move_cash" name="transaction">
                          <label for="radioPrimary2"> Move from <span id="move_from"></span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      {!! Form::label('amount', 'Amount *', ['class' => '']) !!}
                      {!! Form::text('amount',  '' , ['id' => 'add_amount' ,'class' => 'form-control check_numeric']) !!}
                    </div>

                    <div class="form-group">
                      {!! Form::label('details', 'Details ', ['class' => '']) !!}
                      {!! Form::textarea('details',null,['class'=>'form-control', 'rows' => 2, 'cols' => 40]) !!}
                    </div>


                                                                                                                              


                  </div>

                  <div class="modal-footer">					
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button class="btn btn-success ajax-submit" id="submit">Submit</button>
                  </div>
              </form>
            </div>

          </div>
          <!-- /.modal-content -->
    </div>
</div>