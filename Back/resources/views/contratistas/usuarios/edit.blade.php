@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratistas.usuarios.index')}}">@trans('index.usuarios') @trans('index.contratistas')</a></li>
      <li class="active">@trans('forms.editar') @trans('forms.user') @trans('index.contratista')</li>
    </ol>
    <div class="page-header">
      <h3>
        @trans('index.editar') @trans('forms.user') @trans('index.contratista')
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
			    {{ Form::model($user, array('route' => array('contratistas.usuarios.update', $user->id), 'method' => 'POST')) }}

            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                {{ Form::label('nombre', trans('forms.name')) }}
                {{ Form::text('nombre', Input::old('nombre'), array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.name'))) }}
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                {{ Form::label('apellido', trans('forms.apellido')) }}
                {{ Form::text('apellido', Input::old('apellido'), array('class' => 'form-control', 'required', 'placeholder' => trans('forms.apellido'))) }}
              </div>
            </div>

            <div class="col-md-4 col-sm-12">
              <div class="form-group container_documento_registro">
                <label>{{trans('login.documento')}}</label>
                <select class="form-control no-chosen" name="tipo_documento" id="tipo_documento" required>
                  @foreach($tipos_documento as $id => $opcion)
                    <option value="{{$id}}" @if($user->user_publico->tipo_documento->id == $id) selected="selected" @endif>{{$opcion}} </option>
                  @endforeach
                </select>
                <input id="documento" type="text" class="form-control enter_submit" name="documento" value="{{ $user->user_publico->documento }}" placeholder="{!! trans('login.documento') !!}"  required>
                @if ($errors->has('documento'))
                <span class="help-block" id="error_documento">
                  <strong>{{ $errors->first('documento') }}</strong>
                </span>
                @endif
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group chosen_user">
                {{ Form::label('pais', trans('forms.pais')) }}
                {!! Form::select('pais', $paises, $userPais, array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.multiple.pais'))) !!}
              </div>
            </div>

			      <div class="col-md-6 col-sm-12">
			        <div class="form-group">
			          {{ Form::label('email', trans('forms.mail')) }}
			          {{ Form::text('email', $user->email, array('class' => 'form-control', 'readonly')) }}
			        </div>
			      </div>

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('usuarios') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>

			    {{ Form::close() }}
			  </div>
			</div>
	  </div>
	</div>
@endsection
