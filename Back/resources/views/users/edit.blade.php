@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('seguridad.users.index')}}">{{trans('index.usuarios')}}</a></li>
      <li class="active">{!! trans('forms.editar').' '.trans('forms.user') !!}</li>
    </ol>
    <div class="page-header">
      <h3>
        {!! trans('index.editar').' '.trans('forms.user') !!}
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
			    {{ Form::model($user, array('route' => array('seguridad.users.update', $user->id), 'method' => 'POST')) }}

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('nombre', trans('forms.name')) }}
                {{ Form::text('nombre', Input::old('nombre'), array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.name'))) }}
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group">
                {{ Form::label('apellido', trans('forms.apellido')) }}
                {{ Form::text('apellido', Input::old('apellido'), array('class' => 'form-control', 'required', 'placeholder' => trans('forms.apellido'))) }}
              </div>
            </div>

            <div class="col-md-6 col-sm-12">
              <div class="form-group chosen_user">
                {{ Form::label('roles', trans('forms.roles')) }}
                {!! Form::select('roles[]', $roles, $userRoles, array('class' => 'form-control chosen-select', 'multiple', 'data-placeholder' => trans('forms.multiple.roles'))) !!}
              </div>
            </div>

			      <div class="col-md-6 col-sm-12">
			        <div class="form-group">
			          {{ Form::label('email', trans('forms.mail')) }}
			          {{ Form::text('email', $user->email, array('class' => 'form-control', 'readonly')) }}
			        </div>
			      </div>

            @if(!Auth::user()->usuario_causante)
  			      <div class="col-md-12 col-sm-12 fecha-egreso">
                <div class="form-group">
                  <label class="fixMargin4">
                    <div class="checkbox noMarginChk">
                      <div class="btn-group chk-group-btn" data-toggle="buttons">
                        <label class="btn btn-primary btn-sm @if($user->usuario_causante) active @endif">
                          <input autocomplete="off" class="triggerClickChk" type="checkbox" name="usuario_causante" id="usuario_causante" @if($user->usuario_causante) checked @endif>
                          <span class="glyphicon glyphicon-ok"></span>
                        </label>
                        {{trans('forms.causante')}}
                      </div>
                    </div>
                  </label>
                </div>
              </div>

            <div class="pertenece-a-causantes @if(!$user->usuario_causante) hidden @endif col-md-12 col-sm-12">
  		        <div class="form-group">
  		          {!! Form::select('causante_id', $causantes, $user->causante_id, array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.select.causante'), 'id' => 'distrito_id')) !!}
  		        </div>
            </div>
          @endif

            <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
              <div class="text-right">
                <a class="btn btn-small btn-success" href="{{ url('seguridad/usuarios') }}">@trans('forms.volver')</a>
                {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
              </div>
            </div>

			    {{ Form::close() }}
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('scripts')
  showCausantes();
@endsection
