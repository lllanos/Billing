@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.list_of') @trans('forms.roles')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.list_of') @trans('forms.roles')
          </div>
          <div class="buttons-on-title">
            @permissions(('role-create'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{route('seguridad.roles.create')}}">
                  @trans('forms.nuevo') @trans('index.rol')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a class="btn pull-left nvo_rol_rspv" href="{{route('seguridad.roles.create')}}">
                        @trans('forms.nuevo') @trans('index.rol')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            @endpermission
          </div>
        </h3>
      </div>
    </div>
    <!--Input file excel con 1 form-->
      <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
        <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
          @permissions(('role-export'))
            <form class="form_excel" method="POST" data-action="{{route('seguridad.export.roles')}}" id="form_excel">
              {{ csrf_field() }}
              <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input"
              value="">
              <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
              <i class="fa fa-file-excel-o fa-2x"></i>
              </button>
            </form>
          @endpermission
            <input type="text" class="search-input form-control input_dos_btns" name="search_input_no_post" id="search_input_no_post" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')" aria-label="{{trans('index.input')}} @trans('index.buscar')">
            <span class="input-group-btn">
              <button type="submit" id="search_button_no_post" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
                <i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </div>
    <!--Fin Input file excel con 1 form-->
    @if(sizeof($roles) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="list-table p-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller" id="sortable-table-wrapper"> <!-- zui-no-data -->
              <table class="table table-striped table-hover table-bordered zui-table">
                <thead>
                  <tr>
                    <th class="sort" data-sort="rol">@trans('forms.name')</th>
                    <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                  </tr>
                </thead>
                <tbody class="list tbody_js">
                  @foreach ($roles as $key => $role)
                    <tr id="role_{{$role->id}}">
                      <td class="rol">{{ $role->name }}</td>
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            <!-- <li><a href="{{ route('seguridad.roles.show', $role->id) }}" title="@trans('index.ver')"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li> -->
                            @permissions(('role-edit'))
                              <li><a href="{{ route('seguridad.roles.edit', $role->id) }}"><i class="glyphicon glyphicon-pencil"></i> {{ trans('index.editar')}}</a></li>
                            @endpermission
                            @permissions(('role-delete'))
                              <li>
                                <a class="eliminar btn-confirmable-prevalidado"
                                 data-prevalidacion="{{ route('seguridad.roles.preDelete', ['id' => $role->id]) }}"
                                 data-body="{{trans('index.confirmar_eliminar.rol', ['name' => $role->name])}}"
                                 data-action="{{ route('seguridad.roles.delete', ['id' => $role->id]) }}"
                                 data-si="@trans('index.si')" data-no="@trans('index.no')">
                                 <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                </a>
                              </li>
                            @endpermission
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
    <div class="col-md-12 col-sm-12">
      <div class="sin_datos_js"></div>
    </div>
  </div>
@endsection

@section('scripts')
  var list_js_options = { valueNames: [ 'rol' ] };

@endsection
