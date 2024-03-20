@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.list_of') {!!trans('forms.contratistas')!!}</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.list_of') @trans('forms.contratistas')
          </div>
          <div class="buttons-on-title">
            @permissions(('contratista-create'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{route('contratistas.create')}}">
                  @trans('forms.nuevo') @trans('index.contratista')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="{{route('contratistas.create')}}">
                        @trans('forms.nuevo') @trans('index.contratista')
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

    <!--Input file excel con 2 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
        @permissions(('contratista-export'))
          <form  class="form_excel" method="POST" data-action="{{ route('export.contratistas') }}" id="form_excel">
            {{ csrf_field() }}
            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
              <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
          </form>
        @endpermission
        <form method="POST" data-action="{{ route('contratistas.post.index') }}" id="search_form">
          {{ csrf_field() }}
          <input type="text" class="search-input form-control input_dos_btns buscar_si enter_submit" name="search_input" id="search_input" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
          <span class="input-group-btn">
            <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
        </form>
      </div>
    </div>
    <!--Input file excel con 2 form-->
    @if(sizeof($contratistas) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="list-table pt-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller"> <!-- zui-no-data -->
              <table class="table table-striped table-hover table-bordered zui-table">
                <thead>
                  <tr>
                    <th>{{trans('forms.nombre_razon_social')}}</th>
                    <th>{{trans('forms.tipo_contratista')}}</th>
                    <th>{{trans('forms.tipo_doc_num_doc')}}</th>
                    <th>{{trans('forms.nombre_fantasia')}}</th>
                    <th>{{trans('forms.mail')}}</th>
                    <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                  </tr>
                </thead>
                <tbody class="tbody_js">
                  @foreach($contratistas as $keyContratista => $valueContratista)
                    <tr id="contratista_{{$valueContratista->id}}">
                      <td>{{ $valueContratista->razon_social }}</td>
                      <td>{{ $valueContratista->tipo->nombre }}</td>
                      <td>{{ $valueContratista->tipo_num_documento }}</td>
                      <td>{{ $valueContratista->nombre_fantasia }}</td>
                      <td>{{ $valueContratista->email }}</td>
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            @permissions(('contratista-view'))
                              <li><a href="{{ route('contratistas.show', $valueContratista->id) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                            @endpermission

                            @permissions(('contratista-edit'))
                              <li><a href="{{ route('contratistas.edit', $valueContratista->id) }}" title="{{ trans('index.editar')}}"><i class="glyphicon glyphicon-pencil"></i> {{ trans('index.editar')}}</a></li>
                            @endpermission

                            @permissions(('contratista-delete'))
                              <li>
                                <a class="eliminar btn-confirmable-prevalidado"
                                 data-prevalidacion="{{ route('contratistas.preDelete', ['id' => $valueContratista->id]) }}"
                                 data-body="{{trans('index.confirmar_eliminar.contratista', ['razon_social' => $valueContratista->razon_social])}}"
                                 data-action="{{ route('contratistas.delete', ['id' => $valueContratista->id]) }}"
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
        {{ $contratistas->render() }}
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
