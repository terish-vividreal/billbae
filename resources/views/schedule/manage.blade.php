  <div id="manage-schedule-modal" class="modal">
    <form id="manageScheduleForm" name="manageScheduleForm" role="form" method="POST" action="" class="ajax-submit">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Schedule Form</h4> </div>
              {{ csrf_field() }}
              {!! Form::hidden('customer_id', '' , ['id' => 'customer_id'] ); !!}
              {!! Form::hidden('start_time', '' , ['id' => 'start_time'] ); !!}
              {!! Form::hidden('grand_total', '' , ['id' => 'grand_total'] ); !!}
              <div class="card-body">

              
                  <div class="row">
                    <div class="input-field col m4 s4 l4">
                      {!! Form::select('user_id', $variants->therapists , '' , ['id' => 'user_id' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select therapist']) !!}
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input type="text" name="start" value="" id="start" />
                    </div>

                    
                  </div>

                  <div class="row">
                    <div class="input-field col m4 s4 l4">
                      <input type="text" name="customer_name" id="customer_name" class="typeahead autocomplete" autocomplete="off" value="">
                      <label for="customer_name" class="label-placeholder active">Customer <span class="red-text">*</span></label>
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input id="mobile" name="mobile" type="text">
                      <label for="mobile" class="label-placeholder active"> Mobile </label>
                    </div>
                    <div class="input-field col m4 s4 l4">
                      <input id="email" type="email" name="email">
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
              </div>
        </div>
        <div class="modal-footer">
            <button class="btn waves-effect waves-light modal-action modal-close" type="reset" id="resetForm">Receive payment</button>
            <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="schedule-submit-btn">Submit <i class="material-icons right">send</i></button>
        </div>
    </form>
  </div>