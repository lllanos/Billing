@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('forms.sol_redeterminaciones_' . $estado)</li>
      </ol>
      <div class="page-header">
        <h3>
          @trans('forms.sol_redeterminaciones_' . $estado)
        </h3>
      </div>
    </div>

    <!--Input file excel con 2 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns">
        @permissions(('redeterminaciones-' . $estado . '-export'))
          <form  class="form_excel" method="POST" data-action="{{ route('solicitudes.redeterminacion.export', ['estado' => $estado]) }}" id="form_excel">
            {{ csrf_field() }}
            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
              <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
          </form>
        @endpermission
        <form method="POST" data-action="{{ route('contratistas.post.index') }}" id="search_form">
          {{ csrf_field() }}
          <input type="text" class="search-input form-control input_dos_btns enter_submit buscar_si" name="search_input" id="search_input" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
          <span class="input-group-btn">
            <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle buscar-si" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
        </form>
      </div>
    </div>
    <!--Input file excel con 2 form-->

    @if(sizeof($solicitudes) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table pt-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th class="text-center"></th>
                  <th>@trans('contratos.expediente_madre_th')</th>
                  <th>@trans('forms.fecha_solicitud_th')</th>
                  <th>@trans('forms.expediente_solicitud_th')</th>
                  <th>@trans('forms.contratista')</th>
                  <th>@trans('forms.salto')</th>
                  <th>@trans('forms.estado')</th>
                  <th>@trans('forms.ultimo_movimiento_th')</th>
                  @if(!Auth::user()->usuario_causante)
                    <th>@trans('forms.causante')</th>
                  @endif
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">
                @foreach($solicitudes as $keySolicitud => $valueSolicitud)
                  <tr id="redeterminacion_{{$valueSolicitud->id}}">
                    <td class="text-center">
                      @if($valueSolicitud->a_termino)
                      <span>
                        <i class="fa fa-check-circle text-success"
                         data-toggle="tooltip" data-placement="top" title="@trans('sol_redeterminaciones.a_termino')"></i>
                      </span>
                      @else
                      <span>
                        <i class="fa fa-times-circle text-danger"
                         data-toggle="tooltip" data-placement="top" title="@trans('sol_redeterminaciones.no_a_termino')"></i>
                      </span>
                      @endif
                    </td>
                    <td>{{ $valueSolicitud->contrato->expediente_madre }}</td>
                    <td>{{ $valueSolicitud->created_at }}</td>
                    <td>{{ $valueSolicitud->nro_expediente }}</td>
                    <td>{{ $valueSolicitud->contrato->contratista->nombre_documento }}</td>
                    <td>
                      <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                        {{ $valueSolicitud->salto->moneda_mes_anio }}
                      </span>
                    </td>
                    <td>
                      <div class="contenedor_badges_estado_contrato">
                        @if($valueSolicitud->en_curso)
                          <span class="badge badge_esperando m-0">@trans('index.esperando')</span>
                        @endif
                        <span class="badge badge_esperando_estado m-0" style="background: #{{$valueSolicitud->estado_nombre_color['color']}}" >
                          <span>{{$valueSolicitud->estado_nombre_color['nombre']}}</span>
                        </span>
                      </div>
                    </td>
                    <td>{{ $valueSolicitud->ultimo_movimiento }}</td>

                    @if(!Auth::user()->usuario_causante)
                      <td>
                        <span class="badge" style="background-color:#{{ $valueSolicitud->contrato->causante_nombre_color['color'] }};">
                          {{ $valueSolicitud->contrato->causante_nombre_color['nombre'] }}
                        </span>
                      </td>
                    @endif
                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu pull-right">
                          @permissions(('redeterminaciones-' . $estado . '-view'))
                            <li><a href="{{ route('solicitudes.ver', ['id' => $valueSolicitud->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
      {{$solicitudes->render()}}
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
</div>
@endsection
