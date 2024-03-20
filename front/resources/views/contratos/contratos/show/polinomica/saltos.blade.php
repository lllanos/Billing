<div class="row">
  <div class="col-md-12">
    <div class="panel-body pt-0 pb-0">
      <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
        <div class="panel-group colapsable_top mt-1" id="accordion__var_{{$valueContratoMoneda->id}}" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">

            <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading__var_{{$valueContratoMoneda->id}}">
              <h4 class="panel-title titulo_collapse panel_heading_0 m-0 panel_title_btn">
                <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion__var_{{$valueContratoMoneda->id}}" href="#collpapse__var_{{$valueContratoMoneda->id}}" aria-expanded="true" aria-controls="collpapse__var_{{$valueContratoMoneda->id}}">
                  <div class="container_icon_angle">
                    <i class="fa fa-angle-down"></i> @trans('index.saltos')
                  </div>
                </a>
              </h4>
            </div>

            <div id="collpapse__var_{{$valueContratoMoneda->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub__var_{{$valueContratoMoneda->id}}">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="panel-body p-0">
                    <div class="zui-wrapper zui-action-32px-fixed">
                      <div class="zui-scroller zui-no-data">
                        <table class="table table-striped table-hover table-bordered zui-table">
                          <thead>
                            <tr>
                              <th class="text-center">@trans('forms.numeral')</th>
                              <th>@trans('contratos.mes_salto')</th>
                              <th>@trans('forms.vr')</th>
                              <th>@trans('forms.solicitud')</th>
                              <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                            </tr>
                          </thead>
                          <tbody>
                            @if($valueContratoMoneda->ultima_variacion != null && !$valueContratoMoneda->ultima_variacion->es_salto)
                              <tr>
                                <td>{{ $valueContratoMoneda->ultima_variacion->nro_salto }}</td>
                                <td>{{ $valueContratoMoneda->ultima_variacion->publicacion->mes_anio }}</td>
                                <td class="text-right">
                                  <span class="badge" style="background-color:var(--red-redeterminacion-color);">
                                    {{$valueContratoMoneda->ultima_variacion->variacion_show}}
                                  </span>
                                </td>
                                <td></td>
                                <td class="actions-col noFilter">
                                  <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                                      <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                      <li><a href="{{ route('contratos.verSalto', ['variacion_id' => $valueContratoMoneda->ultima_variacion->id]) }}" title="@trans('index.ver')"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                    </ul>
                                  </div>
                                </td>
                              </tr>
                            @endif
                            @if(sizeof($valueContratoMoneda->saltos))
                              @foreach($valueContratoMoneda->saltos as $keySalto => $valueSalto)
                                <tr>
                                  <td class="text-center">{{ $valueSalto->nro_salto }}</td>
                                  <td>{{ $valueSalto->publicacion->mes_anio }}</td>
                                  <td class="text-right">
                                    <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                                      {{$valueSalto->variacion_show}}
                                    </span>
                                  </td>
                                  <td>
                                    @if($valueSalto->solicitado)
                                      @if($valueSalto->solicitud != null)
                                        @if(!$valueSalto->solicitud->en_curso)
                                          <span class="badge" style="background:#{{$valueSalto->solicitud->estado_nombre_color['color']}}" >
                                            <span>{{$valueSalto->solicitud->estado_nombre_color['nombre']}}</span>
                                          </span>
                                        @else
                                          <div class="contenedor_badges_estado_contrato">
                                            <span class="m-0 badge badge-referencias badge_esperando">@trans('index.esperando')</span>
                                            <span class="m-0 badge badge-referencias container_estado_redeterminacion badge_esperando_estado" style="background: #{{$valueSalto->solicitud->estado_nombre_color['color']}};" >
                                              <span class="badge_estado_redeterminacion_tb">{{$valueSalto->solicitud->estado_nombre_color['nombre']}}</span>
                                            </span>
                                          </div>
                                        @endif
                                      @elseif($valueSalto->empalme)
                                        @trans('sol_redeterminaciones.de_empalme')
                                      @else
                                        @trans('sol_redeterminaciones.mesa_entrada')
                                      @endif
                                    @endif
                                  </td>
                                  <td class="actions-col noFilter">
                                    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                                        <i class="fa fa-ellipsis-v"></i>
                                      </button>
                                      <ul class="dropdown-menu pull-right">
                                        @if(!$valueSalto->empalme)
                                          <li><a href="{{ route('contratos.verSalto', ['variacion_id' => $valueSalto->id]) }}" title="@trans('index.ver')"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                          @if($valueSalto->solicitud != null)
                                            <li><a href="{{ route('solicitudes.ver', ['id' => $valueSalto->solicitud->id]) }}"><i class="glyphicon glyphicon-road"></i> @trans('index.ver_redeterminacion')</a></li>
                                          @endif
                                          @if($valueSalto->cuadro_comparativo != null && $valueSalto->solicitud != null && $valueSalto->solicitud->monto_vigente != null)
                                            <li><a href="{{route('cuadroComparativo.ver', ['id' => $valueSalto->cuadro_comparativo->id])}}"><i class="glyphicon glyphicon-th-list"></i> @trans('index.ver') @trans('sol_redeterminaciones.cuadro_comparativo')</a></li>
                                          @endif
                                        @else
                                          <li><a href="{{route('empalme.redeterminacion.ver', ['id' => $valueSalto->redeterminacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver') @trans('index.redeterminacion')</a></li>
                                        @endif
                                      </ul>
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            @endif
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
      </div>
    </div>
  </div>
</div>
