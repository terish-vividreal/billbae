@if (count($errors) > 0)
  <div class="card-alert card red lighten-5 print-error-msg">
    <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
    <div class="card-content red-text">    
      <ul>
        @foreach($errors->all() as $error)
        <li><i class="material-icons">error</i>  {{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
  @endif

  @if (Session::has('error'))
  <div class="card-alert card red">
    <div class="card-content white-text">
      <p>
      <p><i class="material-icons">error</i> {!! Session::get('error') !!}</p>
    </div>
    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">×</span>
    </button>
  </div>
@endif


