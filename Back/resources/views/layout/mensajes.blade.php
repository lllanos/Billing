  @if ($message = Session::get('success'))
    <div class="col-md-12">
      <div class="alert alert-success" id="msg_succes">
        <p>{{ $message }}</p>
      </div>
    </div>
  @endif

  @if ($message = Session::get('error'))
    <div class="col-md-12">
      <div class="alert alert-error" id="msg_error">
        <p>{{ $message }}</p>
      </div>
    </div>
  @endif
