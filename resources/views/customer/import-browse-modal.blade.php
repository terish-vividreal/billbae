<div id="import-browse-modal" class="modal">
    <form id="importCustomerForm" name="importCustomerForm" role="form" method="POST" action="{{ route('customer.import') }}" enctype="multipart/form-data">
        <div class="modal-content">
          <a class="btn-floating mb-1 waves-effect waves-light right modal-close"><i class="material-icons">clear</i></a>
          <div class="modal-header"><h4 class="modal-title">Additional Tax Form</h4> </div> 
          <a href="{{ url('/') }}/sample/customers.csv" class="">Download sample CSV file.</p></a>
            {{ csrf_field() }}
              <div class="card-body" id="formFields">
                <div class="row">                      
                  <!-- <div class="input-field file-field col s6">
                    <div class="btn float-right"> <span>Browse</span>
                    <input type="file" name="file">
                    <span class="helper-text" data-error="wrong" data-success="right">Please upload a file with .csv extension.</span>
                    </div>
                  </div> -->
                  <div class="input-field">
                    <!-- <div class="btn"> -->
                      <span>File</span>
                      <input class="errorDiv" type="file" name="file">
                    <!-- </div> -->
                    <!-- <div class="file-path-wrapper">
                      <input class="file-path validate" type="text">
                    </div> -->
                  </div>
                </div>
              </div>
        </div>
        <div class="modal-footer">
          <button class="btn waves-effect waves-light modal-action modal-close" type="reset" id="resetForm">Close</button>
          <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="additional-submit-btn">Submit <i class="material-icons right">send</i></button>
        </div>
    </form>
  </div>