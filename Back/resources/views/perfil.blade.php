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

  		      <div class="col-md-12 col-sm-12">
  		        <div class="form-group">
  		          {{ Form::label('email', trans('forms.mail')) }}
  		          {{ Form::text('email', $user->email, array('class' => 'form-control', 'readonly')) }}
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
  		          {{ Form::label('nombre', trans('forms.name')) }}
  		          {{ Form::text('nombre', Input::old('nombre'), array('class' => 'form-control', 'required', 'placeholder' => trans('forms.name'))) }}
  		        </div>
  		      </div>

  		      <div class="col-md-6 col-sm-12">
  		        <div class="form-group">
  		          {{ Form::label('apellido', trans('forms.apellido')) }}
  		          {{ Form::text('apellido', Input::old('apellido'), array('class' => 'form-control', 'required', 'placeholder' => trans('forms.apellido'))) }}
  		        </div>
  		      </div>

            <div class="form-group">
              <div class="col-md-6 col-md-offset-4">
                <a class="btn btn-small btn-success" href="{{ url('/') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right col-only-button')) }}
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
@endsection
