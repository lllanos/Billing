  @if(sizeof($valueObra->saltos) > 0 || ($valueObra->ultima_variacion != null && !$valueObra->ultima_variacion->es_salto))
    <div class="col-md-12 col-sm-12 tabla_obras">
      <div class="list-table">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th class="text-center">{{trans('forms.numeral')}}</th>
                  <th>{{trans('contratos.mes_salto')}}</th>
                  <th>{{trans('forms.vr')}}</th>
                  <th>{{trans('forms.solicitud')}}</th>
                  <th>{{trans('forms.nro_resolucion')}}</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody>
                @if($valueObra->ultima_variacion != null)
                  @if(!$valueObra->ultima_variacion->es_salto)
                    <tr>
                      <td>{{ $valueObra->ultima_variacion->nro_salto }}</td>
                      <td>{{ $valueObra->ultima_variacion->publicacion->mes_anio }}</td>
                      <td class="text-right">
                        <span class="badge" style="background-color:var(--red-redeterminacion-color);">
                          {{$valueObra->ultima_variacion->variacion_show}}
                        </span>
                      </td>
                      <td></td>
                      <td></td>
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            <li><a href="{{ route('contrato.verSalto', ['variacion_id' => $valueObra->ultima_variacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> {{ trans('index.ver')}}</a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endif
                @endif
                @if(sizeof($valueObra->saltos))
                  @foreach($valueObra->saltos as $keySalto => $valueSalto)
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
                          @if($valueSalto->redeterminacion != null)
                            @if(!$valueSalto->redeterminacion->en_curso)
                              <span class="badge" style="background:#{{$valueSalto->redeterminacion->estado_nombre_color['color']}}" >
                                <span>{{$valueSalto->redeterminacion->estado_nombre_color['nombre']}}</span>
                              </span>
                            @else
                              <div class="contenedor_badges_estado_contrato">
                                <span class="m-0 badge badge-referencias badge_esperando">{{trans('index.esperando')}}</span>
                                <span class="m-0 badge badge-referencias container_estado_redeterminacion badge_esperando_estado" style="background: #{{$valueSalto->redeterminacion->estado_nombre_color['color']}};" >
                                  <span class="badge_estado_redeterminacion_tb">{{$valueSalto->redeterminacion->estado_nombre_color['nombre']}}</span>
                                </span>
                              </div>
                            @endif
                          @else
                            {{trans('redeterminaciones.mesa_entrada')}}
                          @endif
                        @endif
                      </td>
                      <td>
                        @if($valueSalto->redeterminacion != null)
                          @if($valueSalto->redeterminacion->doc_resolucion != null)
                          {{ $valueSalto->redeterminacion->doc_resolucion->nro_gedo_resolucion }}
                          @endif
                        @endif
                      </td>
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="{{ trans('index.acciones')}}">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="{{trans('index.acciones')}}">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            <li><a href="{{ route('contrato.verSalto', ['variacion_id' => $valueSalto->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> {{ trans('index.ver')}}</a></li>
                            @if($valueSalto->redeterminacion != null)
                              <li><a href="{{ route('solicitudes.ver', ['id' => $valueSalto->redeterminacion->id]) }}"><i class="glyphicon glyphicon-road"></i> {{ trans('index.ver_redeterminacion')}}</a></li>
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
  @endif
  <div class="col-md-12">
    <h3> {{trans('contratos.polinomica')}} </h3>
    <table class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th>{{trans('forms.indice')}}</th>
          <th>{{trans('forms.nombre')}}</th>
          <th>{{trans('forms.porcentaje')}}</th>
        </tr>
      </thead>
      @foreach($valueObra->polinomica->composiciones_ordenadas as $keyComposicion => $valueComposicion)
      <tr>
        <td>{{ $valueComposicion->indice_tabla1->nombre }}</td>
        <td>{{ $valueComposicion->nombre }}</td>
        <td>{{ $valueComposicion->porcentaje_arg }}</td>
      </tr>
      @endforeach
    </table>
  </div>
