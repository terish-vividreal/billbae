  <div id="manage-schedule-modal" class="modal">
    <form id="manageScheduleForm" name="manageScheduleForm" role="form" method="POST" action="" class="ajax-submit">
      <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
        <div class="modal-content">
            <div class="modal-header">
              <a class="btn-floating mb-1 waves-effect waves-light right modal-close"><i class="material-icons">clear</i></a>
              <h4 class="modal-title">Schedule Form</h4> </div>
              <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
              {{ csrf_field() }}
              {!! Form::hidden('schedule_id', '' , ['id' => 'schedule_id'] ); !!}
              {!! Form::hidden('customer_id', '' , ['id' => 'customer_id'] ); !!}
              {!! Form::hidden('start_time', '' , ['id' => 'start_time'] ); !!}
              {!! Form::hidden('grand_total', '' , ['id' => 'grand_total'] ); !!}
              {!! Form::hidden('total_minutes', '' , ['id' => 'total_minutes'] ); !!}
              {!! Form::hidden('receive_payment', '' , ['id' => 'receive_payment'] ); !!}
              <div class="card-body">              
                  <div class="row">
                    <div class="input-field col m4 s4 l4">
                      {!! Form::select('user_id', $variants->therapists , '' , ['id' => 'user_id' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select therapist']) !!}
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input type="text" name="start" value="" id="start" class="disabled"/>
                    </div>  
      
                    <div class="input-field col m4 s4 l4">
                      <p><label>
                          <input class="validate" name="checked_in" id="checked_in" value="1" type="checkbox">
                          <span>Customer Checked In</span>
                        </label> </p>
                      <div class="input-field">
                      </div>
                    </div> 
                  </div>

                  <div class="row">
                    <div class="input-field col m4 s4 l4">
                      <input type="text" name="customer_name" id="customer_name" class="typeahead autocomplete disabled" autocomplete="off" value="">
                      <label for="customer_name" class="label-placeholder active">Customer <span class="red-text">*</span></label>
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input id="mobile" name="mobile" type="text" class="check_numeric disabled">
                      <label for="mobile" class="label-placeholder active"> Mobile </label>
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input id="email" type="email" name="email" class="disabled">
                      <label for="email" class="label-placeholder active">Email</label>
                    </div>
                  </div>  

                  <div class="row">
                    <div class="input-field col m6 s6">
                      <div class="select-wrapper">
                        <select class="select2 browser-default" name="service_type" id="service_type">
                          <option selected="selected">Please select type</option>
                          <option value="1">Services</option>
                          <option value="2">Packages</option>
                        </select> 
                      </div>
                    </div>  
                    <div class="input-field col m6 s6">
                      <div class="select-wrapper">
                      <div id="services_block">
                        <select class="select2 browser-default service-type" data-type="services" name="bill_item[]" id="services" multiple="multiple"> </select>
                      </div>
                      <div id="packages_block" style="display:none;">
                        <select class="select2 browser-default service-type" data-type="packages" name="bill_item[]" id="packages" multiple="multiple"> </select>
                      </div>
                      </div>
                    </div>              
                  </div>
                  <div class="row" id="itemDetailsDiv" style="display:none;">
                    <div class="input-field col m12 s6"><ul class="collection" id="itemDetails"></ul></div>
                  </div>
              </div>
        </div>
        <div class="modal-footer">
            <button class="btn orange waves-effect waves-light modal-action" type="button" id="cancelSchedule" style="display:none;">Cancel Schedule</button>
            <button class="btn waves-effect waves-light modal-action form-action-btn" type="reset" id="receivePaymentBtn">Receive payment</button>
            <button class="btn cyan waves-effect waves-light form-action-btn" type="submit" name="action" id="schedule-submit-btn">Submit <i class="material-icons right">send</i></button>
        </div>
    </form>
  </div>