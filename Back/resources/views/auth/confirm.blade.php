@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')

  <div class="titleLogin text-center">
    <h3>{!! trans('forms.validar').' '.trans('login.usuario')!!}</h3>
  </div>
  <div class="panel-body">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('register.confirmationmail') }}" id="formLogin">
      {{ csrf_field() }}
      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email">@trans('login.email')</label>
          <input id="email" type="text" class="form-control enter_submit" name="email" value="{{ old('email') }}" required placeholder="@trans('login.email')" autofocus>
          @if ($errors->has('email'))
          <span class="help-block" id="error_email">
            <strong>{{ $errors->first('email') }}
              @if ($errors->has('email_confirm'))
              <a href="{{ $errors->first('email_confirm') }}">{!! trans('login.aqui') !!}</a>
              @endif
            </strong>
          </span>
          @endif
        </div>
      </div>

      <input type="hidden" id="confirmation_code" name="confirmation_code" value="{{ $confirmation_code }}">
        <div class="col-md-10 col-md-offset-1">
          <div class="form-group formGroupZeroMargin">
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block" id="btn_validar">
                  @trans('forms.validar')
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
@endsection
