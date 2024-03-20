@extends ('layout.app')

@section('title', config('app.name'))

@section('content')

<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li class="active">@trans('index.perfil')</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.perfil')
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
          {{ Form::model($user, array('route' => array('seguridad.updatePerfil'), 'enctype' => 'multipart/form-data', 'method' => 'POST', 'id' => 'edit_form')) }}
            <ul class ="mensaje"></ul>
            {{ csrf_field() }}

  		      <div class="col-md-6 col-sm-12">
  		        <div class="form-group">
  		          {{ Form::label('email', trans('forms.mail')) }}
  		          {{ Form::text('email', $user->email, array('class' => 'form-control', 'readonly')) }}
                <small class="msg_sugerencia_input text-success">@trans('auth.sugerencia_mail_personal')</small>
  		        </div>
  		      </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group container_documento_registro">
                <label>{{trans('login.documento')}}</label>
                <select class="form-control no-chosen" name="tipo_documento" id="tipo_documento" required>
                  <option disabled selected value>{{trans('login.tipo')}}</option>
                  @foreach($tipos_documento as $id => $opcion)
                    <option value="{{$id}}" @if($id == $user_publico->tipo_documento_id) selected @endif>{{$opcion}} </option>
                  @endforeach
                </select>
                <input id="documento" type="text" class="form-control enter_submit" name="documento" value="{{ $user_publico->documento }}" placeholder="@trans('login.documento')"  required>
                @if ($errors->has('documento'))
                <span class="help-block" id="error_documento">
                  <strong>{{ $errors->first('documento') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12 p-0">
              <div class="form-group">
                <label class="fixMargin4">
                  <div class="checkbox noMarginChk">
                    <div class="btn-group chk-group-btn" data-toggle="buttons">
                      <label class="btn btn-primary btn-sm @if($user->notificaciones_por_mail) active @endif">
                        <input autocomplete="off" class="triggerClickChk" type="checkbox" name="notificaciones_por_mail" id="notificaciones_por_mail"
                        @if($user->notificaciones_por_mail) checked @endif>
                        <span class="glyphicon glyphicon-ok"></span>
                      </label>
                      @trans('index.notificaciones_por_mail')
                    </div>
                  </div>
                </label>
              </div>
            </div>

  		      <div class="col-md-6 col-sm-12">
  		        <div class="form-group">
  		          {{ Form::label('nombre',trans('forms.name')) }}
  		          {{ Form::text('nombre', Input::old('nombre'), array('class' => 'form-control', 'required')) }}
  		        </div>
  		      </div>

  		      <div class="col-md-6 col-sm-12">
  		        <div class="form-group">
  		          {{ Form::label('apellido', trans('forms.apellido')) }}
  		          {{ Form::text('apellido', Input::old('apellido'), array('class' => 'form-control', 'required')) }}
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
