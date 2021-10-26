<div id="import-browse-modal" class="modal">
    <form id="importServicesForm" name="importServicesForm" role="form" method="POST" action="" enctype="multipart/form-data" class="ajax-submit">
        <div class="modal-content">
          <a class="btn-floating mb-1 waves-effect waves-light right modal-close"><i class="material-icons">clear</i></a>
          <div class="modal-header"><h4 class="modal-title">Upload Services</h4> </div> 
          <a href="{{ url('/') }}/sample/services.xlsx" class="">Download sample CSV file.</p></a>
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

  
@push('page-scripts')
<!-- date-time-picker -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>

function importBrowseModal() {
  $("#import-browse-modal").modal("open");
}

if ($("#importServicesForm").length > 0) {
  var validator = $("#importServicesForm").validate({ 
      rules: {
        file: {
          required: true,
          extension: "csv|xlsx",
        }
      },
      messages: { 
        file: {
          required: "Please select a file.",
          extension: "Please upload a file with .csv, xlsx extension.",
        }
      },
      submitHandler: function (form) {
        var forms = $("#{{$page->entity}}Form");
        $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route.'/import/data') }}", type: "POST", processData: false, 
        data: forms.serialize(), dataType: "html",
        }).done(function (a) {
          var data = JSON.parse(a);
          if (data.flagError == false) {
              showSuccessToaster(data.message);                
              $("#import-browse-modal").modal("hide");
              setTimeout(function () {
                window.location.href = "{{ url('services') }}";
              }, 2000);

          } else {
            showErrorToaster(data.message);
            printErrorMsg(data.error);
          }
        });
    },
    errorPlacement: function(error, element) {
        if (element.is("file")) {
            error.insertAfter(element.next('.errorDiv'));
        }else {
            error.insertAfter(element);
        }
    }
  })
}

</script>
@endpush