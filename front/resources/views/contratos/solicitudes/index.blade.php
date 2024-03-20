@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.mis') @trans('index.solicitudes_asociacion')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.mis') @trans('index.solicitudes_asociacion')
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop">
              <a class="btn btn-success" href="{{ route('contrato.asociar') }}" id="btn_asociar">
                @trans('index.solicitar_asociacion')
              </a>
            </div>
            <div class="button_responsive">
              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li><a href="{{ route('contrato.asociar') }}">@trans('index.solicitar_asociacion')</a></li>
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>

    <!--Input file excel con 1 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
          <form  class="form_excel" method="POST" data-action="{{ route('solicitudes.contrato.export') }}" id="form_excel">
            {{ csrf_field() }}
            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input">
            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')}}" aria-label="@trans('index.descargar_a_excel')}}">
              <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
          </form>

          <form method="POST" data-action="{{ route('contrato.solicitudes.post') }}" id="search_form">
            {{ csrf_field() }}
            <input type="text" class="search-input form-control input_dos_btns buscar_si" name="search_input" id="search_input" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
            <span class="input-group-btn">
              <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
                <i class="fa fa-search"></i>
              </button>
            </span>
          </form>

      </div>
    </div>

    <!--Fin Input file excel con 1 form-->
    @if(sizeof($solicitudes_contrato) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table p-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th>@trans('forms.fecha_solicitud_th')</th>
                  <th>@trans('forms.expediente_madre')</th>
                  <th>@trans('forms.descripcion')</th>
                  <th>@trans('forms.estado')</th>
                  <th>@trans('forms.ultimo_movimiento_th')</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">

                @foreach ($solicitudes_contrato as $key => $solicitud_contrato)
                  <tr>
                    <td>{{ $solicitud_contrato->created_at }}</td>
                    <td>{{ $solicitud_contrato->expediente_madre }}</td>
                    <td>{{ $solicitud_contrato->descripcion }}</td>
                    <td>
                      <span class="badge" style="background-color:#{{ $solicitud_contrato->estado_nombre_color['color'] }};">
                        {{ $solicitud_contrato->estado_nombre_color['nombre'] }}
                      </span>
                    </td>
                    <td>{{ $solicitud_contrato->ultimo_movimiento }}</td>
                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          <li><a href="{{ route('contrato.solicitud.ver', ['id' => $solicitud_contrato->id]) }}" id="btn_ver_{{$solicitud_contrato->id}}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
      {{ $solicitudes_contrato->render() }}
    </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos_js"></div>
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
  </div>
@endsection
