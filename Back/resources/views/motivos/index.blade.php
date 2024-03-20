@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.list_of') {!!trans('forms.motivos')!!}</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.list_of') {!!trans('forms.motivos')!!}
          </div>
          <div class="buttons-on-title">
              @permissions(('motivos-create'))
                <div class="button_desktop">
                  <a class="btn btn-success pull-right" href="{{route('motivos.create')}}">
                    @trans('forms.nuevo') @trans('forms.motivo')
                  </a>
                </div>
                <div class="button_responsive">
                  <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li>
                        <a href="{{route('motivos.create')}}">
                          @trans('forms.nuevo') @trans('forms.motivo')
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
      <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" motivo="group" aria-label="...">
        <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
          @permissions(('motivos-export'))
          <form class="form_excel" method="POST" data-action="{{route('export.motivos')}}" id="form_excel">
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
    @if(sizeof($motivos) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table p-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th>{{trans('forms.descripcion')}}</th>
                  <th>{{trans('forms.responsable')}}</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">
                @foreach($motivos as $key => $motivo)
                  <tr id="motivo_{{$motivo->id}}">
                    <td>{{ $motivo->descripcion }}</td>
                    <td>{{ $motivo->responsable }}</td>
                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          @permissions(('motivos-edit'))
                            <li><a href="{{ route('motivos.edit', $motivo->id) }}" title="{{ trans('index.editar')}}"><i class="glyphicon glyphicon-pencil"></i> {{ trans('index.editar')}}</a></li>
                          @endpermission

                          @permissions(('motivos-delete'))
                            <li>
                              <a class="eliminar btn-confirmable-prevalidado"
                               data-prevalidacion="{{ route('motivos.preDelete', ['id' => $motivo->id]) }}"
                               data-body="{{trans('index.confirmar_eliminar.motivo', ['nombre' => $motivo->descripcion])}}"
                               data-action="{{ route('motivos.delete', ['id' => $motivo->id]) }}"
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
