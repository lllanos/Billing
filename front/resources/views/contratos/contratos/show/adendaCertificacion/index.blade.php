<div class="panel-group acordion" id="accordion-adendaCertificacion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-adendaCertificacion">
      <h4 class="panel-title titulo_collapse m-0">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-adendaCertificacion" href="#collapseOne_adendaCertificacion" aria-expanded="true" aria-controls="collapseOne_adendaCertificacion"
        @if(!isset($fromAjax)) data-seccion="adendaCertificacion" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.adendas_certificacion')</div>
        </a>
      </h4>
    </div>
    <div id="collapseOne_adendaCertificacion" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-adendaCertificacion">
      @if(isset($fromAjax))
            @if(sizeof($contrato->adendasCertificacion) > 0)
                <div class="panel-body p-2">
                    <div class="list-table">
                      <div class="zui-wrapper zui-action-32px-fixed">
                        <div class="zui-scroller"> <!-- zui-no-data -->
                          <table class="table table-striped table-hover table-bordered zui-table">
                            <thead>
                              <tr>
                                <th class="text-center"></th>
                                <th>{{trans('forms.expediente')}}</th>
                                <th>{{trans('contratos.resoluc_adjudic_th')}}</th>
                                <th>{{trans('forms.denominacion')}}</th>
                                <th>{{trans('forms.montos')}}</th>
                                @if($publicados)
                                  <th class="text-center">{{trans('forms.ultimo_salto')}}</th>
                                  <th class="text-center">{{trans('contratos.ultima_solicitud_th')}}</th>
                                @endif
                                @if($publicados)
                                  <th class="text-center">{{trans('forms.vr')}}</th>
                                @endif
                                @if(!Auth::user()->usuario_causante)
                                  <th>{{trans('forms.causante')}}</th>
                                @endif
                                <th class="text-center">{{trans('forms.estado')}}</th>

                                @if(!$publicados)
                                  <th class="text-center">{{trans('forms.motivo')}}</th>
                                @endif
                                <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                              </tr>
                            </thead>
                            <tbody class="tbody_js">
                              @foreach($contrato->adendasCertificacion as $keyContratos => $valueContrato)
                                <tr id="contrato_{{$valueContrato->id}}">
                                  <td class="text-center">
                                    @if($valueContrato->borrador)
                                      <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                                    @endif
                                    @if($valueContrato->incompleto['status'])
                                      <i class="fa fa-star-half-empty" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valueContrato->incompleto['mensaje']}}"></i>
                                    @endif
                                  </td>
                                  <td>{{ $valueContrato->expediente_madre }} </td>
                                  <td>{{ $valueContrato->resoluc_adjudic }} </td>
                                  <td>{{ $valueContrato->denominacion }}</td>
                                  <td id="montos">
                                    @if($valueContrato->tiene_contratos_monedas)
                                      @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                        @if($valueContratoMoneda->monto_vigente != null && $valueContratoMoneda->moneda != null)
                                          <span class="badge">
                                            {{$valueContratoMoneda->moneda->simbolo}} {{$valueContratoMoneda->monto_vigente_dos_dec }}
                                          </span>
                                        @endif
                                      @endforeach
                                    @endif
                                  </td>

                                  @if($publicados)
                                    <td>
                                      @if(!$valueContrato->borrador && $valueContrato->tiene_contratos_monedas)
                                        @if($valueContratoMoneda->contrato->ultimo_salto != null)
                                            {{ $valueContrato->ultimo_salto_m_y }}
                                            @if($valueContratoMoneda->contrato->ultimo_salto->solicitado)
                                              <i class="fa fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="{{ trans('index.solicitado')}}"></i>
                                            @else
                                              <i class="fa fa fa-times-circle text-danger" data-toggle="tooltip" data-placement="top" title="{{ trans('index.no_solicitado')}}"></i>
                                            @endif
                                        @endif
                                      @endif
                                    </td>
                                    <td>{{ $valueContrato->fecha_ultima_solicitud }}</td>
                                  @endif

                                  @if($publicados)
                                    <td id="vr_salto">
                                      @if(!$valueContrato->borrador && $valueContrato->tiene_contratos_monedas)
                                        @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                          @if($valueContratoMoneda->en_porcentaje_de_redeterminacion)
                                            <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                                              {{$valueContratoMoneda->nombre}} ({{$valueContratoMoneda->ultima_variacion->variacion_show }})
                                            </span>
                                          @else
                                            <span class="badge" style="background-color:var(--red-redeterminacion-color);">
                                              {{$valueContratoMoneda->moneda->simbolo}}
                                              @if($valueContratoMoneda->ultima_variacion != null)
                                                ({{ $valueContratoMoneda->ultima_variacion->variacion_show }})
                                              @endif
                                            </span>
                                          @endif
                                        @endforeach
                                      @endif

                                      @if($valueContrato->no_redetermina)
                                        <i class="fa fa fa-times-circle text-danger"></i>
                                        {{ trans('contratos.no_redetermina')}}
                                      @endif
                                    </td>
                                  @endif
                                  @if(!Auth::user()->usuario_causante)
                                    <td class="text-center">
                                      @if($valueContrato->causante_id != null)
                                        <span class="badge" style="background-color:#{{ $valueContrato->causante_nombre_color['color'] }};">
                                          {{ $valueContrato->causante_nombre_color['nombre'] }}
                                        </span>
                                      @endif
                                    </td>
                                  @endif
                                  <td>
                                    @if($valueContrato->estado_id != null)
                                      <span class="badge" style="background-color:#{{ $valueContrato->estado_nombre_color['color'] }};">
                                        {{ $valueContrato->estado_nombre_color['nombre'] }}
                                      </span>
                                    @endif
                                  </td>

                                  @if(!$publicados)
                                    <td>
                                      <span class="badge" style="background-color:#{{ $valueContrato->motivo_bandeja_nombre_color['color'] }};">
                                        {{ $valueContrato->motivo_bandeja_nombre_color['nombre'] }}
                                      </span>
                                    </td>
                                  @endif
                                  <td class="actions-col noFilter">
                                    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                        <i class="fa fa-ellipsis-v"></i>
                                      </button>
                                      <ul class="dropdown-menu pull-right">
                                          <li><a href="{{route('adenda.ver', ['id' => $valueContrato->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
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
              <div class="panel-body p-0">
                    <div class="sin_datos_js"></div>
                    <div class="sin_datos">
                        <h1 class="text-center">@trans('index.no_datos')</h1>
                    </div>
                </div>
            @endif
      @endif
    </div>
  </div>
</div>
