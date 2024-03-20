@extends('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">      
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratistas.usuarios.index')}}">@trans('index.usuarios') @trans('index.contratistas')</a></li>
      <li class="active">@trans('forms.crear') @trans('index.usuario') @trans('index.contratista')</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.crear') @trans('forms.user') @trans('index.contratista')
      </h3>
    </div>
      @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="panel panel-default">
        <div class="panel-body">        
          <form class="form" role="form" method="POST" action="{{ route('contratistas.usuarios.store') }}">
            {{ csrf_field() }}
            <div class="col-md-4 col-sm-12">
              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                <label for="nombre">{!! trans('login.nombre') !!}</label>
                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required placeholder="{!! trans('login.nombre') !!}" autofocus>
                @if ($errors->has('nombre'))
                <span class="help-block" id="error_nombre">
                  <strong>{{ $errors->first('nombre') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group{{ $errors->has('apellido') ? ' has-error' : '' }}">
                <label for="apellido">{!! trans('login.apellido') !!}</label>
                <input id="apellido" type="text" class="form-control" name="apellido" value="{{ old('apellido') }}" required placeholder="{!! trans('login.apellido') !!}" autofocus>
                @if ($errors->has('apellido'))
                <span class="help-block" id="error_apellido">
                  <strong>{{ $errors->first('apellido') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group container_documento_registro">
                <label>{{trans('login.documento')}}</label>
                <select class="form-control no-chosen" name="tipo_documento" id="tipo_documento" required>
                  <option disabled selected value>{{trans('forms.tipo')}}</option>
                  @foreach($tipos_documento as $id => $opcion)
                    <option value="{{$id}}" >{{$opcion}} </option>
                  @endforeach
                </select>
                <input id="documento" type="text" class="form-control enter_submit" name="documento" value="{{ old('documento') }}" placeholder="{!! trans('login.documento') !!}"  required>
                @if ($errors->has('documento'))
                <span class="help-block" id="error_documento">
                  <strong>{{ $errors->first('documento') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group{{ $errors->has('pais_id') ? ' has-error' : '' }}">
                <label>{{trans('forms.pais')}}</label>
                <select class="form-control no-chosen" name="pais_id" id="pais_id" required>
                  <option disabled selected value>{{trans('index.seleccionar')}}</option>
                  @foreach($paises as $id => $opcion)
                    <option value="{{$id}}" >{{$opcion}} </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">{!! trans('login.email') !!}</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{!! trans('login.email') !!}"  required>
                <small class="msg_sugerencia_input text-success">{{trans('auth.sugerencia_mail_personal')}}</small>
                @if ($errors->has('email'))
                <span class="help-block" id="error_email">
                  <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('usuarios') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection