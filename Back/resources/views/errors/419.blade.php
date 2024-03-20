@extends('layout.app')

@section('title', config('app.name'))

@section('custom_styles')
  @if(!Auth::check())
  <style>
    .canvas {
      margin-left: 0 !important;
      width: 100% !important;
    }
    .offcanvas-icon {
			display: none;
		}
  </style>
  @endif
@endsection

@section('content')
<div class="container grd-bck">
    <div class="row">
        <div class="col-md-12 text-center custom-error-content">
            <h1>
              {!! trans('index.error419')!!}.<br>
              <small>ERROR 419</small>
            </h1>
        </div>
    </div>
</div>
@endsection
