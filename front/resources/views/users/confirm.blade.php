@extends('layout_login.app')

@section('title', config('app.name'))

@section('content')
<div id="login-section">
    <div class="login-section-data">
        <div class="login-section-data-content">
            <h2 class="header-title"><img src="{{asset('img/main-logo-eby-arg.png')}}"></h2>

                <div class="welcome-title">
                    <small>{!! trans('login.bienvenido') !!}</small>
                    <strong>{!! trans('login.vialidad') !!}</strong>
                    <span class="deco-line"></span>
                </div>

                <div class="info-text">
                    <span>{!! trans('forms.validar').' '.trans('login.usuario')!!}</span>
                </div>

                <form class="form-horizontal" role="form" method="POST" action="{{ route('register.confirmationmail') }}" id="formLogin">
                    {{ csrf_field() }}
                    <div class="form-group input-group-sm {{ $errors->has('email') ? ' has-error' : '' }}">
                      <div class="col-md-12">
                          <input id="email" type="text" class="form-control bg-bright" name="email" value="{{ old('email') }}" required placeholder="{!! trans('login.email') !!}" autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" id="confirmation_code" name="confirmation_code" value="{{ $confirmation_code }}">
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="glyphicon glyphicon-chevron-right pull-right"></i>
                                {!! trans('login.validar') !!}
                            </button>
                        </div>
                    </div>
                </form>
             </div>
         </div>
     </div>

@endsection
