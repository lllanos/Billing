@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')

  <div class="titleLogin text-center">
    <h3>@trans('login.generar_contrasenia')</h3>
  </div>
  <div class="panel-body">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('users.finConfirmacionUsuario') }}">
      {{ csrf_field() }}
      <input type="hidden" name="token" value="{{ $confirmation_code }}">

      @if (Session::get('success'))
        <div class="col-md-10 col-md-offset-1 alert alert-success" id="msg_success">
          {!! Session::get('success') !!}
        </div>
      @endif
      @if (Session::get('error'))
       <div class="col-md-10 col-md-offset-1 alert alert-danger" id="msg_error">
         {!! Session::get('error') !!}
       </div>
      @endif

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('password_original') ? ' has-error' : '' }}">
          <label>@trans('login.introduce_password_original')</label>
          <input id="password" type="password" class="form-control bg-brigh" name="password_original" placeholder="@trans('login.introduce_password_original')" required autofocus>
          @if ($errors->has('password_original'))
          <span class="help-block" id="error_password_original">
            <strong>{{ $errors->first('password_original') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
          <label>@trans('login.introduce_contrasenia')</label>
          <input id="password" type="password" class="form-control" name="password" placeholder="@trans('login.introduce_contrasenia')" required>
          <small class="msg_sugerencia_input text-success">@trans('auth.formato_contrasenia')</small>
          @if ($errors->has('password'))
          <span class="help-block" id="error_password">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
          <label>@trans('login.introduce_contrasenia')</label>
          <input id="password-confirm" type="password" class="form-control enter_submit" name="password_confirmation" placeholder="@trans('login.introduce_contrasenia')" required>
          @if ($errors->has('password_confirmation'))
          <span class="help-block" id="error_password_confirmation">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
          </span>
          @endif
        </div>
      </div>

        <div class="col-md-10 col-md-offset-1">
          <div class="form-group formGroupZeroMargin">
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block" id="btn_generar">
                  @trans('login.generar_contrasenia')
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
@endsection
