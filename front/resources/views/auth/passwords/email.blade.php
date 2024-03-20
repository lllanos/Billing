@extends('layout.app')

@section('title', config('app.name'))

@section('content')
<div id="login-section">
  <div class="row">
    <div class="col-md-6 col-sm-12 col-md-offset-3">
      <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">{!! trans('login.recuperar_contrasenia') !!}<h2></div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('contrasenia.email') }}" id="formLogin">
              {{ csrf_field() }}
              @if (Session::get('status'))
                <div class="alert alert-success">
                  {{ Session::get('status') }}
                </div>
              @endif

              <div class="col-md-10 col-md-offset-1">
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <label for="cuit">{!! trans('login.email') !!}</label>
                  <input id="email" type="email" class="form-control enter_submit" name="email" value="{{ old('email') }}" placeholder="{!! trans('login.email') !!}"  required>
                  @if ($errors->has('email'))
                    <span class="help-block" id="error_cuit">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                  @endif
                </div>
              </div>

              <div class="col-md-10 col-md-offset-1">
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-primary btn-block" id="btn_submit">
                        {!! trans('login.pedir_contrasenia') !!}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
