@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')

  <div class="titleLogin text-center">
    <h3>{!! trans('login.iniciar_sesion') !!}</h3>
  </div>
  <div class="panel-body">
    <form class="form-horizontal" role="form" method="POST" action="{{ asset('/login') }}" id="formLogin">
      {{ csrf_field() }}
      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email">{!!trans('login.email')!!}</label>
          <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="@trans('login.introduce_email')" autofocus>
          @if ($errors->has('email'))
            <span class="help-block" id="error_email">
              <strong>{{ $errors->first('email') }}
                @if ($errors->has('email_confirm'))
                  <a class='loadingToggle' href="{{ $errors->first('email_confirm') }}">{!! trans('login.aqui') !!}</a>
                @endif
              </strong>
            </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password">{!! trans('login.introduce_contrasenia') !!}</label>
          <input id="password" type="password" class="form-control enter_submit" name="password" required placeholder="@trans('login.introduce_contrasenia')" >
          @if ($errors->has('password'))
            <span class="help-block" id="error_password">
              <strong>{{ $errors->first('password') }}</strong>
            </span>
          @endif
        </div>
      </div>

      <div class="col-md-10 col-md-offset-1">
        <div class="form-group formGroupZeroMargin">
          <div class="row">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary btn-block" id="btn_login">
                {!! trans('login.iniciar_sesion') !!}
              </button>
            </div>
            <div class="col-md-12">
              <a class="linkNextToBtn" href="{{ route('recuperar.contrasenia') }}" id="btn_olvide_pass"> @trans('login.olvido_contrasenia')</a>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection
