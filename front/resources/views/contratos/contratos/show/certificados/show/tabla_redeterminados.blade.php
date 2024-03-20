@foreach ($certificados as $keyFila => $valueFila)
  <div id="collapseOne_certificados" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-certificados">
    <div class="row">
      <div class="col-md-12">
        <div class="panel-group colapsable_top mt-1" id="accordion_redet_{{$keyFila}}" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-body pt-0 pb-0">
              <div class="panel-body panel_con_tablas_y_sub_tablas contenedor_all_tablas pt-1 pl-0 pr-0">

                <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_redet_{{$keyFila}}">
                  <h4 class="panel-title titulo_collapse panel_heading_0 m-0 panel_title_btn">
                    <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_redet_{{$keyFila}}" href="#collapse_redet_{{$keyFila}}" aria-expanded="false" aria-controls="collapse_redet_{{$keyFila}}">
                      <div class="container_icon_angle">
                        <i class="fa fa-angle-down"></i> {{$valueFila['titulo']}}
                      </div>
                    </a>
                    <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_redet_{{$keyFila}}" href="#collapse_redet_{{$keyFila}}" aria-expanded="false" aria-controls="collapse_redet_{{$keyFila}}">
                      <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                        {{$valueFila['ajustes']['titulo']}}
                        @foreach ($valueFila['ajustes']['data'] as $keyData => $valueData)
                          <span class="badge badge-referencias" style="background-color:var(--dark-gray-color);margin-top:-6px;">
                            {{$valueData}}
                          </span>
                        @endforeach
                      </div>
                    </a>
                  </h4>
                </div>

                <div id="collapse_redet_{{$keyFila}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_sub_redet_{{$keyFila}}">
                  <div class="row">
                    <div class="col-md-12 col-sm-12">
                      <div class="panel-body p-0">
                        <div class="zui-wrapper zui-action-32px-fixed">
                          <div class="zui-scroller zui-no-data">

                            <table class="table table-striped table-hover table-bordered zui-table">
                              <thead>
                                <tr>
                                  <th>@trans('certificado.nr_certificado_th') @trans('index.redeterminacion')</th>
                                  <th>@trans('forms.ajuste')</th>
                                  <th>@trans('certificado.precio_redeterminado')</th>
                                  <th>@trans('forms.estado')</th>
                                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                                </tr>
                              </thead>
                              <tbody class="tbody_js">
                                @foreach($valueFila['certificados'] as $certificado)
                                  <tr id="certificado_{{$certificado->id}}">
                                    <td>
                                      @if(!$certificado->empalme)
                                        {{str_pad($certificado->redeterminacion->nro_salto, 3, "0", STR_PAD_LEFT)}}
                                      @else
                                        {{$certificado->mes_show}}
                                      @endif
                                    </td>

                                    <td>
                                      @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                                        @if(!$certificado->empalme)
                                          @foreach($valueContratoMoneda['certificados'] as $keyPorContratista => $valuePorContratista)
                                            <span class="badge">
                                              {{$valueContratoMoneda['simbolo']}} @toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior)
                                            </span>
                                          @endforeach
                                        @else
                                          <span class="badge">
                                            {{$valueContratoMoneda['simbolo']}} @toDosDec($valueContratoMoneda['ajuste'])
                                          </span>
                                        @endif
                                      @endforeach
                                    </td>

                                    <td>
                                      @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                                        <span class="badge">
                                          {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->montoPorMoneda($keyContratoMoneda))
                                        </span>
                                      @endforeach
                                    </td>

                                    <td>
                                      <span class="badge badge-referencias" style="background-color:#{{$certificado->estado['color']}};">
                                        {{$certificado->estado['nombre_trans']}}
                                      </span>
                                      @if($certificado->empalme)
                                        <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                                          @trans('contratos.empalme')
                                        </span>
                                      @endif
                                    </td>

                                    <td class="actions-col noFilter">
                                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                          <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                          @permissions(('certificado-view'))
                                            <li><a href="{{route('redeterminaciones.certificado.ver', ['id' => $certificado->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                            <li> <a href="{{route('export.certificado', ['id' => $certificado->id]) }}"> <i class="glyphicon glyphicon-save-file"></i> @trans('index.descargar') </a> </li>
                                          @endpermission
                                          @if($certificado->puede_aprobar_redeterminado)
                                            <li>
                                              <a class="btn-confirmable"
                                               data-body="@trans('certificado.mensajes.confirmacion_aprobar_mes', ['mes' => $certificado->mes_show  . ' - ' . $certificado->mesAnio('fecha', 'Y-m-d')])"
                                               data-action="{{ route('solicitudes.certificado.aprobarCertificadoRedeterminado', ['id' => $certificado->id]) }}"
                                               data-si="@trans('index.si')" data-no="@trans('index.no')">
                                               <i class="glyphicon glyphicon-ok"></i>@trans('index.aprobar')
                                              </a>
                                            </li>
                                          @endif
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
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endforeach
