

@if (Session::has('success'))
<div class="card-alert card gradient-45deg-green-teal print-success-msg">
  <div class="card-content white-text">
    <p>
    <p><i class="material-icons">check</i> {!! Session::get('success') !!}</p>
  </div>
  <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </button>
</div>
@endif
 

                    