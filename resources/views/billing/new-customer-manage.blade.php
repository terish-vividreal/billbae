
<div id="new-customer-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add new customer Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-messages print-error-msg" style="display:none;"><ul></ul></div>
                <div class="alert alert-success fade alert-messages print-success-msg" style="display:none;"></div>
                <form id="newCustomerForm" name="newCustomerForm" role="form" method="POST" action="" class="ajax-submit">
                  {{ csrf_field() }}
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group ">
                            {!! Form::label('new_customer_name', 'Customer Name*', ['class' => 'col-form-label text-alert']) !!}
                            {!! Form::text('new_customer_name', '' , array('placeholder' => 'Customer Name','class' => 'form-control')) !!}                        
                        </div>
                        <div class="form-group ">
                          {!! Form::label('new_customer_mobile', 'Mobile*', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('new_customer_mobile', '' , array('placeholder' => 'Mobile','class' => 'form-control check_numeric')) !!}                        
                        </div>
                        <div class="form-group ">
                          <label class="col-form-label font-weight-bolder">Customer DOB</label>
                          <div class='input-group date' id='customerdob'>
                              <input type='text' name="dob" id="dob" onkeydown="return false" class="form-control" autocomplete="off" />
                              <div class="input-group-append">
                                  <span class="input-group-text">
                                      <i class="fa fa-calendar"></i>
                                  </span>
                              </div>
                          </div>
                        </div>                    


                      </div>  
                      <div class="col-md-6">                      
                        <div class="form-group ">
                          {!! Form::label('new_customer_email', 'E mail ', ['class' => 'col-form-label text-alert']) !!}
                          {!! Form::text('new_customer_email', '' , array('placeholder' => 'E mail','class' => 'form-control')) !!}                        
                        </div>
                        <div class="form-group">
                          {!! Form::label('name', 'Gender', ['class' => 'col-form-label text-alert']) !!} <br>
                          <input type="radio" value="1" id="male" name="gender" checked>
                          <label for="male">Male</label>
                          <input type="radio" value="2" id="female" name="gender">
                          <label for="female">Female</label>
                          <input type="radio" value="3" id="others" name="gender">
                          <label for="others">Others</label>
                          </div> 

                      </div>            
                    </div>
                  </div>
                    <div class="modal-footer">					
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success ajax-submit ">Submit</button>
                    </div>
                </form>
            </div>

          </div>
          <!-- /.modal-content -->
    </div>
</div>
