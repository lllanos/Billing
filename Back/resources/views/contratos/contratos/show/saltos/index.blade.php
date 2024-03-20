@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        @if($cuadro_comparativo == null)
          <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
          <li><a href="{{route('contratos.ver', ['id' => $salto->contrato->id])}}">@trans('index.contrato') {{$salto->contrato->expediente_madre}}</a></li>
        @else
          @if($cuadro_comparativo->solicitud->en_curso)
            <li><a href="{{route('solicitudes.redeterminaciones_en_proceso')}}">@trans('forms.sol_redeterminaciones_en_proceso')</a></li>
          @else
            <li><a href="{{route('solicitudes.redeterminaciones_finalizadas')}}">@trans('forms.sol_redeterminaciones_finalizadas')</a></li>
          @endif
          <li><a href="{{route('solicitudes.ver', ['id' => $cuadro_comparativo->solicitud_id])}}">@trans('index.ver') @trans('index.redeterminacion')</a></li>
          <li> <a href="{{route('cuadroComparativo.ver', ['id' => $cuadro_comparativo->id])}}"> @trans('sol_redeterminaciones.cuadro_comparativo') @trans('index.de') @trans('forms.solicitud') {{$cuadro_comparativo->solicitud->salto->moneda_mes_anio}} </a></li>
        @endif
        <li class="active">@trans('contratos.salto') {{$salto->nro_salto}}</li>
      </ol>
      <div class="page-header">
        <h3 class="titulo_salto">
          @if($salto->nro_salto != null)
            @trans('contratos.salto') {{$salto->nro_salto}} - {{$salto->publicacion->mes_anio}}
          @else
            @trans('contratos.vr_actual')
          @endif
          @if($salto->salto_anterior != null)
            <span class="badge badge-referencias pull-right">
              @trans('contratos.salto_anterior'): {{$salto->salto_anterior->publicacion->mes_anio}}
            </span>
          @endif
        </h3>
      </div>
    </div>
  </div>

  <!--Panel-->
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="headingOne">
        <h4 class="panel-title m-0 titulo_collapse">
          <a class="a_heading_saltos" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <div>
              <i class="fa fa-angle-down"></i> {{$salto->publicacion->mes_anio}}
            </div>
            <div>
              @if(config('custom.test_mode') == 'true')
                <label class="label label-default pull-right m-0">{{ $salto->variacion }}</label>
              @endif
              @if($salto->contrato_moneda->contrato->adhesion &&
                  ($salto->publicacion->mes >= 4 && $salto->publicacion->anio == 2016) ||
                   $salto->publicacion->anio > 2016)
                   @if($salto->variacion_float > 0.5)
                     <label class="label pull-right m-0" style="background-color:var(--green-redeterminacion-color);">
                   @else
                     <label class="label pull-right m-0" style="background-color:var(--red-redeterminacion-color);">
                   @endif
               @else
                @if($salto->variacion_float > $salto->contrato_moneda->porcentaje_salto)
                  <label class="label pull-right m-0" style="background-color:var(--green-redeterminacion-color);">
                @else
                  <label class="label pull-right m-0" style="background-color:var(--red-redeterminacion-color);">
                  @endif
                @endif
                {{ $salto->variacion_show }}
              </label>
            </div>
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body p-0">
          <div class="col-md-12 col-sm-12">
            <div class="row list-table p-0">
              <div class="zui-wrapper zui-action-32px-fixed">
                <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
                  <table class="table table-striped table-hover table-bordered zui-table">
                    <thead>
                      <tr>
                        <th>@trans('contratos.insumos')</th>
                        <th>@trans('contratos.composicion')</th>
                        <th>@trans('contratos.indice_al') {{$salto->publicacion_anterior_o_inicio->mes_anio}}</th>
                        <th>@trans('contratos.indice_al') {{$salto->publicacion->mes_anio}}</th>
                        <th>@trans('contratos.variacion')</th>
                        <th>@trans('contratos.resultante')</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($salto->polinomica->composiciones_ordenadas as $keyComposicion => $valueComposicion)
                        <tr>
                          <td>{{ $valueComposicion->indice_tabla1->nombre }}</td>
                          <td class="text-right">{{ $valueComposicion->porcentaje_arg }}</td>
                          <td class="text-right">
                            {{$salto->publicacion_anterior_o_inicio->valorDe_arg($valueComposicion->tabla_indices_id)}}
                          </td>
                          <td class="text-right">
                            {{$salto->publicacion->valorDe_arg($valueComposicion->tabla_indices_id)}}
                          </td>
                          <td class="text-right">
                            {{$valueComposicion->variacionEn_arg($salto->publicacion)}}
                          </td>
                          <td class="text-right">
                            {{$valueComposicion->resultante_arg($salto->publicacion)}}
                          </td>
                          @if(config('custom.test_mode') == 'true')
                          <td class="text-right">
                            <span class="badge badge-referencias pull-right">{{ $valueComposicion->resultante($salto->publicacion)  }} </span>
                          </td>
                          @endif
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Fin Panel-->

  @foreach($salto->variaciones_desde_ultimo_salto as $keyVariacion => $valueVariacion)
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="collapsable_{{$valueVariacion->id}}">
          <h4 class="panel-title m-0 titulo_collapse">
            <a class="a_heading_saltos" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne_last_{{$valueVariacion->id}}" aria-expanded="true" aria-controls="collapseOne_last">
              <div>
                <i class="fa fa-angle-down"></i> {{$valueVariacion->publicacion->mes_anio}}
              </div>
              <div>
                @if(config('custom.test_mode') == 'true')
                  <label class="label label-default pull-right m-0">{{ $valueVariacion->variacion  }}</label>
                @endif
                @if($valueVariacion->variacion_float > $salto->contrato_moneda->porcentaje_salto)
                  <label class="label pull-right m-0" style="background-color:var(--green-redeterminacion-color);">
                @else
                  <label class="label pull-right m-0" style="background-color:var(--red-redeterminacion-color);">
                @endif
                  {{ $valueVariacion->variacion_show }}
                </label>
              </div>
            </a>
          </h4>
        </div>

        <div id="collapseOne_last_{{$valueVariacion->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapsable_{{$valueVariacion->id}}">
          <div class="panel-body p-0">
            <div class="col-md-12 col-sm-12">
              <div class="row list-table p-0">
                <div class="zui-wrapper zui-action-32px-fixed">
                  <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
                    <table class="table table-striped table-hover table-bordered zui-table">
                      <thead>
                        <tr>
                          <th>@trans('contratos.insumos')</th>
                          <th>@trans('contratos.composicion')</th>
                          <th>@trans('contratos.indice_al') {{$salto->publicacion_anterior_o_inicio->mes_anio}}</th>
                          <th>@trans('contratos.indice_al') {{$valueVariacion->publicacion->mes_anio}}</th>
                          <th>@trans('contratos.variacion')</th>
                          <th>@trans('contratos.resultante')</th>
                          @if(config('custom.test_mode') == 'true')
                          <th>RESULTANTE</th>
                          @endif
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($valueVariacion->polinomica->composiciones as $keyComposicion => $valueComposicion)
                          <tr>
                            <td>{{ $valueComposicion->indice_tabla1->nombre }}</td>
                            <td class="text-right">{{ $valueComposicion->porcentaje_arg }}</td>
                            <td class="text-right">
                              {{$salto->publicacion_anterior_o_inicio->valorDe_arg($valueComposicion->tabla_indices_id)}}
                            </td>
                            <td class="text-right">
                              {{$valueVariacion->publicacion->valorDe_arg($valueComposicion->tabla_indices_id)}}
                            </td>
                            <td class="text-right">
                              {{$valueComposicion->variacionEn_arg($valueVariacion->publicacion)}}
                            </td>
                            <td class="text-right">
                              {{$valueComposicion->resultante_arg($valueVariacion->publicacion)}}
                            </td>
                            @if(config('custom.test_mode') == 'true')
                            <td class="text-right">
                              <span class="badge badge-referencias pull-right">{{ $valueComposicion->resultante($valueVariacion->publicacion)  }} </span>
                            </td>
                            @endif
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection
