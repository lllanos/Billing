@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('seguridad.roles.index')}}">{{trans('index.roles')}}</a></li>
      <li class="active">{!! trans('index.editar').' '.trans('forms.rol') !!}</li>
    </ol>
    <div class="page-header">
      <h3>
        {!! trans('index.editar').' '.trans('forms.rol') !!}
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
			    {{ Form::model($role, array('route' => array('seguridad.roles.update', $role->id), 'method' => 'PATCH', 'class' => 'form-horizontal')) }}

            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6 col-sm-12">
    			        <div class="form-group">
                    {{ Form::label('name', trans('forms.name')) }}
    			          {{ Form::text('name', $role->name, array('class' => 'form-control', 'required', 'autofocus')) }}
    			        </div>
    			      </div>
              </div>
            </div>

							@permissions(('role-edit-acl'))
              <div class="col-md-12">
                <div class="form-group RoleCollapse">
                  {{ Form::label('name', trans('index.permisos')) }}
	                @foreach($modulos as $modulo => $valueModulo)
										<ul class="col-md-12 ul--general">
					          	<li class="col-md-12 li-modulo">
								 				<label>
                          <div class="btn-group chk-group-btn" data-toggle="buttons">
                            <label class="btn btn-primary btn-sm">
                              <input type="checkbox" id="{{$modulo}}" class="chk-modulo chk-modulo-{{$modulo}}" data-este="{{$modulo}}">
                              <span class="glyphicon glyphicon-ok"></span>
                            </label>
                            {{trans('permisos.'.$modulo)}}
                          </div>
									 			</label>
				            		<a class="btn-collapse">
				              		<i class="fa fa-angle-up"></i>
				            		</a>
				          		</li>
				          		<div class="collapsable collapse-up">
												@foreach($valueModulo as $submodulo => $valueSubModulo)
					            		<li class="col-md-12 col-sub">
					              		<ul class="col-md-12 ul--general">
  					                	<li class="col-md-12 li-submodulo">
  													 		<label>
                                  <div class="btn-group chk-group-btn" data-toggle="buttons">
                                    <label class="btn btn-primary btn-sm">
                                      <input type="checkbox" id="{{$modulo.'-'.$submodulo}}" class="chk-submodulo chk-submodulo-{{$modulo}}-{{$submodulo}}"
                                      data-parent="{{$modulo}}" data-este="{{$modulo}}-{{$submodulo}}">
                                      <span class="glyphicon glyphicon-ok"></span>
                                    </label>
                                    {{trans('permisos.'.$submodulo)}}
                                  </div>
  														 	</label>
  		                				</li>
  														@foreach($valueSubModulo as $permiso)
  			                  			<li class="col-md-6 li-perm">
  		                    				<label>
                                    <div class="btn-group chk-group-btn" data-toggle="buttons">
                                      <label class="btn btn-primary btn-sm @if(in_array($permiso->id, $rolePermissions)) active @endif}}">
                                        {{ Form::checkbox('permission[]',
                                        $permiso->id, in_array($permiso->id, $rolePermissions) ? true : false,
                                        array('class' => 'chk-permiso chk-permiso-'.$modulo.'-'.$submodulo,
                                        'data-parent' => $modulo.'-'.$submodulo, 'data-parent-modulo' => $modulo, 'id' => $modulo.'-'.$submodulo.'-'.$permiso->name)) }}
                                        <span class="glyphicon glyphicon-ok"></span>
                                      </label>
                                      {{trans('permisos.'.$permiso->name)}}
                                    </div>
  																</label>
  		                  				</li>
          					          @endforeach
              			        </li>
            			        </ul>
        		            @endforeach
                      </div>
                    </ul>
                  @endforeach
				        </div>
              </div>
							@endpermission

              <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
                <div class="text-right">
                  <a class="btn btn-small btn-success" href="{{ url('seguridad/roles') }}">@trans('forms.volver')</a>
                  {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary')) }}
                </div>
              </div>
			    {{ Form::close() }}
			  </div>
			</div>
	  </div>
	</div>
@endsection

@section('scripts')
	$(() => {

		$(".chk-submodulo").each(function() {
			var este = $(this).data('este');
			var permisos = document.querySelectorAll('.chk-permiso-' + este);
			var permisos_checked = document.querySelectorAll('.chk-permiso-' + este + ':checked');
			if(permisos.length == permisos_checked.length) {
				$(this).prop('checked', 'true');
        $(this).closest('.btn-sm').addClass('active');
      }
		});

		$(".chk-modulo").each(function() {
			var este = $(this).data('este');

			var submodulos =  document.querySelectorAll('[data-parent="' + este + '"]');
			var submodulos_checked =  document.querySelectorAll('[data-parent="' + este + '"]:checked');
			if(submodulos.length == submodulos_checked.length) {
				$(this).prop('checked', 'true');
        $(this).closest('.btn-sm').addClass('active');
      }
		});

	});
@endsection
