@extends('layout.app')

@section('title', config('app.name'))

@section('content')
<div id="login-section">
  <div class="row">
    <div class="col-md-6 col-sm-12 col-md-offset-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">@trans('login.generar_contrasenia')</h2>
        </div>
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="POST" action="{{ route('users.finConfirmacionUsuario') }}">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $confirmation_code }}">

            <div class="col-md-10 col-md-offset-1">
              <div class="form-group{{ isset($errors['password_original']) ? ' has-error' : '' }}">
                <label for="password_original">@trans('login.introduce_password_original')</label>
                  <input id="password_original" type="password" class="form-control" name="password_original" value="{{ old('password_original') }}" placeholder="@trans('login.introduce_password_original')"  required>
                  @if (isset($errors['password_original']))
                  <span class="help-block" id="error_password_original">
                    <strong>{{ $errors['password_original']['password_original'] }}</strong>
                  </span>
                  @endif
              </div>
            </div>

            <div class="col-md-10 col-md-offset-1">
              <div class="form-group{{ isset($errors['password']) ? ' has-error' : '' }}">
                <label for="password">@trans('login.introduce_contrasenia')</label>
                <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="@trans('login.introduce_contrasenia')" required>
                <small class="msg_sugerencia_input text-success">@trans('auth.formato_contrasenia')</small>

                @if (isset($errors['password']))
                <span class="help-block" id="error_password">
                  <ul>
                  @foreach($errors['password'] as $keyError => $valueError)
                  <li>
                    <strong>{{ $valueError }}</strong>
                  </li>
                  @endforeach
                  </ul>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-10 col-md-offset-1">
              <div class="form-group{{ isset($errors['password_confirmation']) ? ' has-error' : '' }}">
                <label for="password_confirmation">@trans('login.introduce_confirmacion_contrasenia')</label>
                <input id="password_confirmation" type="password" class="form-control enter_submit" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="@trans('login.introduce_confirmacion_contrasenia')"  required>
                @if (isset($errors['password_confirmation']))
                  <span class="help-block" id="error_error_password_confirmation">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                  </span>
                @endif
              </div>
            </div>

            <div class="col-md-10 col-md-offset-1 text-center">
              <div class="form-group{{ isset($errors['terminos_y_condiciones']) ? ' has-error' : '' }} mb-0" >

                <div class="btn-group chk-group-btn outCheck" data-toggle="buttons">
                  <label class="btn btn-primary btn-sm" id="chk-terminos">
                    <input autocomplete="off" class="chk-terminos" type="checkbox" name="terminos_y_condiciones" id="terminos_y_condiciones">
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                  @trans('login.terminos_los')
                  <a href="#" data-target="#modal_terminos" data-toggle="modal" class="modal-terminos">@trans('login.terminos_y_condiciones')</a>
                </div>

                @if(isset($errors['terminos_y_condiciones']))
                  <span class="help-block" id="error_terminos">
                    @foreach ($errors['terminos_y_condiciones'] as $keyError => $valueError)
                      <strong>{{ $valueError }}</strong>
                    @endforeach
                  </span>
                @endif
              </div>
            </div>

            <div class="col-md-10 col-md-offset-1">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block" disabled id="btn_generar">
                  @trans('login.generar_contrasenia')
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('modals')
<div id="modal_terminos" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      @include('auth.terminos_y_condiciones')
    </div>
  </div>
</div>
@endsection

@section('scripts')
  $(() => {
    $('#chk-terminos').on('click', function () {
      if($('#btn_generar').is(':disabled')) {
        $('#btn_generar').prop('disabled', false);
      } else {
        $('#btn_generar').prop('disabled', true);
      }
    });
  });
  @endsection
