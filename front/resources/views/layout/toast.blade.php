<?php
  $alert = 'default';
  $icon = 'info';
  $toggle = 'hide';
  $msg = '';

  if(session('success') || isset($success)){
    $alert = 'success';
    $icon = 'check';
    $toggle = '';
    if(session('success')){
    $msg = session('success');
  }
    else{
    $msg = $success;
    }
  }

  if(session('error')){
    $alert = 'danger';
    $icon = 'times';
    $toggle = '';
    $msg = session('error');
  }
?>

@if(session('error') || session('success') || isset($success))
<div class="toast bck-{{$alert}} {{$toggle}}" id="toast">
  <div class="toast-content">
    <i class="fa fa-{{$icon}}-circle fa-2x"></i>
    <span>
      {{Log::info($msg.$toggle)}}
      {!! $msg !!}
    </span>
  </div>
</div>
@endif
