@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('seguridad.perfil')}}">@trans('index.perfil')</a></li>
        <li class="active">@trans('index.cambiar_contrasenia')</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.cambiar_contrasenia')
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
          {{ Form::model($user, array('route' => array('seguridad.newPassword'), 'method' => 'POST', 'id' => 'edit_form')) }}
            <ul class ="mensaje"></ul>
            {{ csrf_field() }}

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('old_password', trans('forms.password_actual')) }}
                {{ Form::password('old_password', ['class' => 'form-control', 'required', 'placeholder' => trans('forms.password_actual')]) }}
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('password', trans('forms.nueva_password')) }}
                {{ Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => trans('forms.nueva_password')]) }}
                <small class="msg_sugerencia_input text-success">{{trans('auth.formato_contrasenia')}}</small>
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('password_confirmation', trans('forms.confirmar_password')) }}
                {{ Form::password('password_confirmation', ['class' => 'form-control', 'required', 'placeholder' => trans('forms.confirmar_password')]) }}
              </div>
            </div>

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('/') }}">Volver</a>
                {{ Form::submit('Guardar', array('class' => 'btn btn-primary pull-right col-only-button')) }}
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
@endsection
