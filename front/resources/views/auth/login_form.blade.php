<div class="row">
  <div class="col-md-6 col-sm-12 col-md-offset-3 col-inModal">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h2 class="panel-title">@trans('login.iniciar_sesion')</h2>
    </div>
    <div class="panel-body">
      <form class="form-horizontal" id="form_login" name="form_login" role="form" method="POST" action="{{ url('/ingresar') }}">
        {{ csrf_field() }}
        <div class="col-md-10 col-md-offset-1">
          <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">@trans('login.email')</label>
              <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="@trans('login.introduce_email')" autofocus tabindex="0">
             @if ($errors->has('email'))
              <span class="help-block" id="error_email">
                 <strong>{{ $errors->first('email') }}
                   @if ($errors->has('email_confirm'))
                    <a href="{{ $errors->first('email_confirm') }}">@trans('login.aqui')</a>
                   @endif
                 </strong>
               </span>
             @endif
           </div>
          </div>

          <div class="col-md-10 col-md-offset-1">
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
              <label for="password">@trans('login.contrasenia')</label>
              <input id="password" type="password" class="form-control enter_submit" name="password" placeholder="@trans('login.introduce_contrasenia')">
              @if ($errors->has('password'))
              <span class="help-block" id="error_password">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
              @endif
            </div>
          </div>

          <div class="col-md-10 col-md-offset-1">
            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary btn-block" id="btn_login">
                    @trans('login.iniciar_sesion')
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>

        <div class="col-md-12">
          <a class="linkNextToBtn" href="{{ url('/recuperar/contrasenia') }}" id="btn_olvide_pass"> @trans('login.olvido_contrasenia')</a>
        </div>
      </div>
    </div>
  </div>

</div>
