<input type="hidden" id="empalme_version" value="{{$opciones['version']}}" />
<input type="hidden" id="empalme_visualizacion" value="{{$opciones['visualizacion']}}" />

<div class="panel-group acordion" id="accordion-empalme" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-empalme">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
          <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-empalme" href="#collapseOne_empalme" aria-expanded="true" aria-controls="collapseOne_empalme"
          @if(!isset($fromAjax)) data-seccion="empalme" data-version="original" @endif>
            <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('contratos.empalme')</div>
            @if(!$contrato->empalme_finalizado)
              <div class="container_icon_angle">
                <div class="container_btn_action">
                  <span class="badge badge-referencias badge-borrador">
                    <i class="fa fa-eraser"></i>
                    @trans('index.borrador')
                  </span>
                </div>
              </div>
            @endif
          </a>
          @permissions(('empalme-manage'))
            @if(!$contrato->empalme_finalizado && count($contrato->certificados_empalme) >= 1)
              <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li>
                    <a class="btn-confirmable-prevalidado"
                    data-prevalidacion="{{ route('empalme.preValidacion', ['id' => $contrato->id]) }}"
                    data-body="@trans('contratos.finalizar_empalme')"
                    data-action="{{ route('empalme.finalizarEmpalme', ['id' => $contrato->id]) }}"
                    data-si="@trans('index.si')" data-no="@trans('index.no')">
                      <i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.finalizar') @trans('contratos.empalme')
                    </a>
                  </li>
                </ul>
              </div>
            @endif
          @endpermission
        </h4>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-fin-empalme">
          <ul> </ul>
        </div>
      </div>
    </div>

    <div id="collapseOne_empalme" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-empalme">
      @if(isset($fromAjax))
        <div class="row">
          <div class="col-md-12">
            <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-empalme">
              <ul> </ul>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="panel-body pt-0 pb-0">
              <!-- solapa redeterminaciones -->
              @if($contrato->redetermina)
                @php ($sufijo = 'empalme_redet')
                <div class="panel-default acordion" id="accordion-{{$sufijo}}" role="tablist" aria-multiselectable="true">
                  <div class="panel panel-default">
                    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading-{{$sufijo}}">
                      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                        <a class="btn_acordion dos_datos collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion-{{$sufijo}}" href="#collapse_{{$sufijo}}" aria-expanded="true" aria-controls="collapse_{{$sufijo}}">
                          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.redeterminaciones')</div>
                        </a>
                        @if($contrato->permite_redeterminacion)
                          <div class="dropdown container_btn_action">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                              <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                              <li><a href="{{ route('empalme.createRedeterminacion', ['id' => $contrato->id]) }}"> @trans('index.nueva') @trans('index.redeterminacion')
                              </a></li>
                            </ul>
                          </div>
                        @endif
                      </h4>
                    </div>

                    <div id="collapse_{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{$sufijo}}">
                      @ifcount($contrato->redeterminaciones_empalme)
                        <div class="row">
                          <div class="col-md-12">
                            @include('contratos.contratos.show.redeterminaciones.show.tabla', ['empalme' => true, 'redeterminados' => false])
                          </div>
                        </div>
                      @elseifcount
                        <div class="panel-body p-0">
                          <div class="sin_datos_js"></div>
                          <div class="sin_datos">
                            <h1 class="text-center">@trans('index.no_datos')</h1>
                          </div>
                        </div>
                      @endifcount
                    </div>
                  </div>
                </div>
              @endif
              <!-- FIN solapa redeterminaciones -->

              <!-- solapa certificados -->
              <div class="panel-default acordion" id="accordion-certificados-empalme" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                  <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading-certificados-empalme">
                    <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                      <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-certificados-empalme" href="#collapse_certificados-empalme" aria-expanded="true" aria-controls="collapse_certificados-empalme"
                        @if(!isset($fromAjax)) data-seccion="certificados-empalme" data-version="{{$opciones['version']}}" @endif>
                        <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('certificado.certificaciones')</div>
                      </a>
                      @permissions(('empalme-manage'))
                        @if(!$contrato->empalme_finalizado && $contrato->permite_certificado_empalme)
                          <div class="dropdown container_btn_action">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                              <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                              <li><a class="loadingToggle" href="{{ route('empalme.createCertificado', ['id' => $contrato->id, 'empalme' => true]) }}"><i class="fa fa-plus" aria-hidden="true"></i> @trans('index.solicitar') @trans('contratos.certificado')
                              @trans('index.mes') {{count($contrato->certificados_empalme) + 1}}</a></li>
                            </ul>
                          </div>
                        @endif
                      @endpermission
                    </h4>
                  </div>

                  <div id="collapse_certificados-empalme" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-certificados-empalme">
                    @if(isset($fromAjax))
                      @if($contrato->has_certificados_empalme)
                        @php($empalme = true)
                         <div class="row">
                          <div class="col-md-12">
                            @php ($sufijo = 'empalme_basicos')
                            <div class="panel-body pt-0 pb-0">
                              <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
                                <div class="panel-group colapsable_top mt-1" id="accordion{{$sufijo}}" role="tablist" aria-multiselectable="true">
                                  <div class="panel panel-default">

                                    <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading{{$sufijo}}">
                                      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion{{$sufijo}}" href="#collpapse{{$sufijo}}" aria-expanded="true" aria-controls="collpapse{{$sufijo}}">
                                          <div class="container_icon_angle">
                                            <i class="fa fa-angle-down"></i> @trans('forms.certificados') @trans('certificado.basicos')
                                          </div>
                                        </a>
                                      </h4>
                                    </div>

                                    <div id="collpapse{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub{{$sufijo}}">
                                      <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                          <div class="panel-body p-0">
                                            <div class="zui-wrapper zui-action-32px-fixed">
                                              <div class="zui-scroller zui-no-data">
                                                @include('contratos.contratos.show.certificados.show.tabla', ['empalme' => true, 'redeterminados' => false ])
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

                        @if($contrato->redetermina && $contrato->has_certificados_empalme_redeterminados)
                          <div class="row">
                            <div class="col-md-12">
                              @php ($sufijo = 'redeterminados')
                              <div class="panel-body pt-0 pb-0">
                                <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
                                  <div class="panel-group colapsable_top mt-1" id="accordion{{$sufijo}}" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">

                                      <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading{{$sufijo}}">
                                        <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                                          <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion{{$sufijo}}" href="#collpapse{{$sufijo}}" aria-expanded="true" aria-controls="collpapse{{$sufijo}}">
                                            <div class="container_icon_angle">
                                              <i class="fa fa-angle-down"></i> @trans('forms.certificados') @trans('certificado.redeterminados')
                                            </div>
                                          </a>
                                        </h4>
                                      </div>

                                      <div id="collpapse{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub{{$sufijo}}">
                                        <div class="row">
                                          <div class="col-md-12 col-sm-12">
                                            <div class="panel-body p-0">
                                              <div class="zui-wrapper zui-action-32px-fixed">
                                                <div class="zui-scroller zui-no-data">
                                                  <table class="table table-striped table-hover table-bordered zui-table">
                                                    <thead>
                                                      <tr>
                                                        <th></th>
                                                        <th>@trans('certificado.nr_certificado_th')</th>
                                                        <th>{{trans('forms.ajuste')}}</th>
                                                        <th>{{trans('certificado.desc_anticipo')}}</th>
                                                        <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                                                      </tr>
                                                    </thead>
                                                    <tbody class="tbody_js">
                                                      @if($empalme)
                                                        @php ($certificados_redeterminados = $contrato->certificados_redeterminados_empalme)
                                                      @else
                                                        @php ($certificados_redeterminados = $contrato->certificados_redeterminados)
                                                      @endif

                                                      @foreach($certificados_redeterminados as $certificadoRedeterminado)
                                                        <tr>
                                                          <td class="text-center">
                                                            @if($certificadoRedeterminado->certificado->es_borrador)
                                                              <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                                                            @endif
                                                          </td>
                                                          <td>{{ $certificadoRedeterminado->mes_show }} - {{$certificadoRedeterminado->mesAnio('fecha', 'Y-m-d')}}
                                                          </td>
                                                          <td>
                                                            @foreach($certificadoRedeterminado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                                                              <span class="badge">
                                                                {{$valueContratoMoneda['simbolo']}} @toDosDec($valueContratoMoneda['ajuste'])
                                                              </span>
                                                            @endforeach
                                                          </td>
                                                          <td>
                                                            @foreach($certificadoRedeterminado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                                                              <span class="badge">
                                                                {{$valueContratoMoneda['simbolo']}}
                                                                @toDosDec($valueContratoMoneda['desc_anticipo'])
                                                              </span>
                                                            @endforeach
                                                          </td>
                                                          <td class="actions-col noFilter">
                                                            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                              </button>
                                                              <ul class="dropdown-menu pull-right">
                                                                @permissions(('certificado-view'))
                                                                  <li><a href="{{route('certificado.ver', ['id' => $certificadoRedeterminado->certificado_id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>

                                                                @endpermission
                                                                @if($certificadoRedeterminado->certificado->borrador)
                                                                  @permissions(('certificado-edit'))
                                                                    @if($empalme)
                                                                     <li>
                                                                      <a href="{{route('empalme.edit', ['id' => $certificadoRedeterminado->certificado_id]) }}"> <i class="fa fa-pencil"></i> @trans('index.editar')</a></li>
                                                                    @else
                                                                     <li><a href="{{route('certificado.edit', ['id' => $certificadoRedeterminado->certificado_id])}}"><i class="fa fa-pencil"></i> @trans('index.editar')</a></li>
                                                                    @endif
                                                                  @endpermission
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
                        @endif
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
              <div class="col-md-12 mb-1 p-0">
                <div class="buttons-on-title">
                  @permissions(('empalme-manage'))
                    @if(!$contrato->empalme_finalizado && count($contrato->certificados_empalme) >= 1)
                      <a class="btn btn-primary btn-confirmable-prevalidado pull-right"
                      data-prevalidacion="{{ route('empalme.preValidacion', ['id' => $contrato->id]) }}"
                      data-body="@trans('contratos.finalizar_empalme')"
                      data-action="{{ route('empalme.finalizarEmpalme', ['id' => $contrato->id]) }}"
                      data-si="@trans('index.si')" data-no="@trans('index.no')"> @trans('index.finalizar') @trans('contratos.empalme')
                      </a>
                    @endif
                  @endpermission
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
