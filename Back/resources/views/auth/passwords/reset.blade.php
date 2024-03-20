@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')

  <div class="titleLogin text-center">
    <h3>{!! trans('login.recuperar_contrasenia') !!}</h3>
  </div>
  <div class="panel-body">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}
        @if (Session::get('success'))
          <div class="alert alert-success">
            {{ Session::get('success') }}
         </div>
         @endif
         @if (Session::get('error'))
           <div class="alert alert-error">
             {{ Session::get('error') }}
          </div>
          @endif
        <input type="hidden" name="token" value="{{ $token }}">

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group input-group-sm {{ $errors->has('email') ? ' has-error' : '' }}">
          <lable>{!! trans('login.email') !!}</label>
          <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" placeholder="{!! trans('login.email') !!}" required autofocus>
          @if ($errors->has('email'))
          <span class="help-block" id="error_email">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group input-group-sm {{ $errors->has('password') ? ' has-error' : '' }}">
          <lable>{!! trans('login.introduce_contrasenia') !!}</label>
          <input id="password" type="password" class="form-control bg-brigh" name="password" placeholder="{!! trans('login.introduce_contrasenia') !!}" required>
          <small class="msg_sugerencia_input text-success">{{trans('auth.formato_contrasenia')}}</small>
          @if ($errors->has('password'))
          <span class="help-block" id="error_password">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
          <lable>{!! trans('login.introduce_confirmacion_contrasenia') !!}</label>
          <input id="password-confirm" type="password" class="form-control enter_submit" name="password_confirmation" placeholder="{!! trans('login.introduce_confirmacion_contrasenia') !!}" required>
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
                <button type="submit" class="btn btn-primary btn-block" id="btn_submit">
                  {!! trans('login.recuperar_contrasenia')!!}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

@endsection
