
<div class="panel panel-default">
  <div class="panel-heading p-0 panel_heading_collapse primer_collapse_color" role="tab" id="headingOne-coeficiente">
    {{-- A Dropdown 0 coeficiente--}}
    <h4 class="panel-title titulo_collapse m-0 d-flex">
      <a class="btn_acordion dos_datos collapse_arrow collapsed" role="button" 
        data-toggle="collapse" data-parent="#accordion-coeficiente" 
        href="#collapseTipoObra_coeficiente" aria-expanded="false" 
        aria-controls="collapseTipoObra_coeficiente"
      >
        <div class="d-flex container_datos_drop w-100">
          <span class="container_icon_angle">
            <i class="fa fa-angle-up"></i> 
            {{strtoupper(trans('analisis_precios.coeficiente.coeficiente_resumen'))}}
          </span>
          <span class="d-flex-colum">
            <span>$1,4579</span>
            <span>{{trans('analisis_precios.total_calculado')}}</span>            
          </span>
          <span class="d-flex-colum">
            <span>{{$contrato->coeficiente->error}}</span>
            <span>{{trans('analisis_precios.total_adaptado')}}</span> 
          </span>          
        </div>
      </a>
      
      @if(!isset($isExcel))
        <div class="container_btns_plus_action">
          <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu pull-right">
            <li><a class="open-modal-coeficiente mouse-pointer"
              data-title="{{trans('index.ajustar_error')}} {{strtolower(trans('index.de'))}} {{trans('analisis_precios.coeficiente.coeficiente_resumen')}}"
              data-url="{{route('AnalisisPrecios.error', ['modelo' => 'Coeficiente', 'contrato_id' => $contrato->id, 'id' => $contrato->coeficiente->id])}}"><i class="glyphicon glyphicon-pencil"></i>
              <i class="glyphicon glyphicon-sort"></i> {{ trans('index.ajustar_error')}}</a></li>
            </ul>
          </div>
        </div>
      @endif
    </h4>
    {{--Fin A Dropdown 0 coeficiente--}}
  </div>

  <!-- Costos -->
  <div id="collapseTipoObra_coeficiente" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
    <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
      <div class="panel-group colapsable_uno" id="accordion_costos" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading panel_heading_costos p-0 panel_heading_collapse segundo_collapse_color" role="tab" id="headingOne_costos">
            {{-- A Dropdown 1 coeficiente--}}
              <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                <a class="collapse_arrow collapsed" role="button" data-toggle="collapse"
                  data-parent="#accordion_costos"
                  href="#collapse_costos" aria-expanded="false"
                  aria-controls="collapse_costos">
                  <div class="d-flex container_datos_drop">
                    <span class="container_icon_angle d-flex">
                      <i class="fa fa-angle-up"></i> 
                      {{strtoupper(trans('analisis_precios.coeficiente.costos'))}}
                    </span>
                    <span class="d-flex-colum">
                      <span>$1,4579</span>
                      <span>{{trans('analisis_precios.total_calculado')}}</span>
                    </span>
                    <span class="d-flex-colum">
                      <span>{{$contrato->coeficiente->costos_coeficiente->total + $contrato->coeficiente->costos_coeficiente->error}}</span>
                      <span>{{trans('forms.total_adaptado')}}</span>
                    </span>
                  </div>                 
                </a>                
                @if(!isset($isExcel))
                  <div class="container_btns_plus_action">
                    <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu pull-right">
                      <li><a class="open-modal-coeficiente mouse-pointer"
                        data-title="{{trans('index.ajustar_error')}} {{strtolower(trans('index.de'))}} {{trans('analisis_precios.coeficiente.costos')}}"
                        data-url="{{route('AnalisisPrecios.error', ['modelo' => 'CostosCoeficiente', 'contrato_id' => $contrato->id, 'id' => $contrato->coeficiente->costos_coeficiente->id])}}">
                        <i class="glyphicon glyphicon-sort"></i> {{ trans('index.ajustar_error')}}</a></li>
                      </li>
                      </ul>
                    </div>
                  </div>
                @endif
              </h4>
            {{--Fin A Dropdown 1 coeficiente--}}
          </div>
        </div>

        <div id="collapse_costos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_sub_sub_cat">
          <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
            <!--Tabla scrollable-->
            <div class="col-md-12 col-sm-12">
              <div class="row list-table pt-0 pb-1">
                <div class="zui-wrapper zui-action-32px-fixed">
                  <div class="zui-scroller"> <!-- zui-no-data -->
                    <table class="table table-striped table-hover table-bordered zui-table">
                      <tbody class="tbody_tooltip">
                        <tr>
                          <td>{{strtoupper(trans('analisis_precios.coeficiente.costos'))}}</td>
                          <td></td>
                          <td class="text-right"> 100 % </td>
                          <td class="text-right"> 1.000 </td>
                          <td class="actions-col noFilter">
                          </td>
                        </tr>
                        <tr>
                          <td>{{strtoupper(trans('analisis_precios.coeficiente.gastos_generales'))}}</td>
                          <td></td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->gastos_generales}} %</td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->gastos_generales_val}}</td>
                          <td class="actions-col noFilter">
                            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                  <li><a class="open-modal-coeficiente mouse-pointer" data-title="{{trans('index.editar')}} {{trans('forms.gastos_generales')}}"
                                    data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'gastos_generales'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                @endif
                              </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>{{strtoupper(trans('analisis_precios.coeficiente.beneficios'))}}</td>
                          <td></td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->beneficios}} %</td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->beneficios_val}}</td>
                          <td class="actions-col noFilter">
                            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                  <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.beneficios')}}"
                                     data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'beneficios'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                @endif
                              </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>{{strtoupper(trans('analisis_precios.coeficiente.costos_financieros'))}}</td>
                          <td class="text-right">@if($contrato->coeficiente->costos_coeficiente->indice_tabla1 != null){{$contrato->coeficiente->costos_coeficiente->indice_tabla1->numero_nombre}} @endif</td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->costos_financieros}} %</td>
                          <td class="text-right">{{$contrato->coeficiente->costos_coeficiente->costos_financieros_val}}</td>
                          <td class="actions-col noFilter">
                            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                  <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.costos_financieros')}}"
                                    data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'costos_financieros'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                @endif
                              </ul>
                            </div>
                          </td>
                        </tr>
                        {{-- SUBTOTAL GASTOS  --}}
                        <tr>
                          <td colspan="4" class="total_analisis">
                            <span class="pull-left">SUB TOTAL GASTOS GENERALES x COSTOS FINANCIEROS</span>
                            <span class="pull-right">0,027</span>     
                          </td>
                          <td class="actions-col noFilter total_analisis">
                            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                  <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.costos_financieros')}}"
                                    data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'costos_financieros'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                @endif
                              </ul>
                            </div>
                          </td>
                        </tr>
                        {{-- FIN SUBTOTAL GASTOS  --}}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!--Fin Tabla scrollable-->
          </div>
        </div>

      </div>
    </div>
    <!-- FIN Costos -->

    <!-- Impuestos -->
      <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
        <div class="panel-group colapsable_uno" id="accordion_impuestos" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading panel_heading_costos p-0 panel_heading_collapse segundo_collapse_color" role="tab" id="headingOne_costos">
              <h4 class="panel-title titulo_collapse m-0 panel_title_btn collapsed">
                <a class="collapse_arrow" role="button" data-toggle="collapse"
                  data-parent="#accordion_impuestos"
                  href="#collapse_impuestos" aria-expanded="false"
                  aria-controls="collapse_impuestos"
                >
                  <div class="d-flex container_datos_drop">
                    <span class="container_icon_angle">
                      <i class="fa fa-angle-up"></i> 
                      {{strtoupper(trans('analisis_precios.coeficiente.impuestos'))}}
                    </span>
                    <span class="d-flex-colum">
                      <span>$1,4579</span>
                      <span>{{trans('analisis_precios.total_calculado')}}</span>
                    </span>
                    <span class="d-flex-colum">
                      <span>$1,4579</span>
                      <span>{{trans('analisis_precios.total_adaptado')}}</span>
                    </span>          
                  </div>
                </a>
                @if(!isset($isExcel))
                  <div class="container_btns_plus_action">
                    <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu pull-right">
                      <li><a class="open-modal-coeficiente mouse-pointer"
                        data-title="{{trans('index.ajustar_error')}} {{strtolower(trans('index.de'))}} {{trans('analisis_precios.coeficiente.impuestos')}}"
                        data-url="{{route('AnalisisPrecios.error', ['modelo' => 'ImpuestosCoeficiente', 'contrato_id' => $contrato->id, 'id' => $contrato->coeficiente->impuestos_coeficiente->id])}}">
                        <i class="glyphicon glyphicon-sort"></i> {{ trans('index.ajustar_error')}}</a></li>
                      </ul>
                    </div>
                  </div>
                @endif
              </h4>
            </div>
          </div>

          <div id="collapse_impuestos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_sub_sub_cat">
            <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
              <!--Tabla scrollable-->
              <div class="col-md-12 col-sm-12">
                <div class="row list-table pt-0 pb-1">
                  <div class="zui-wrapper zui-action-32px-fixed">
                    <div class="zui-scroller"> <!-- zui-no-data -->
                      <table class="table table-striped table-hover table-bordered zui-table">
                        <tbody class="tbody_tooltip">
                          <tr>
                            <td>{{strtoupper(trans('analisis_precios.coeficiente.iibb'))}}</td>
                            <td class="text-right">{{$contrato->coeficiente->impuestos_coeficiente->iibb}} %</td>
                            <td class="text-right">{{$contrato->coeficiente->impuestos_coeficiente->iibb_val}}</td>
                            <td class="actions-col noFilter">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                  @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                    <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.iibb')}}"
                                       data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'iibb'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                  @endif
                                </ul>
                              </div>
                            </td>
                          </tr>                          
                          {{-- TOTAL GASTOS GENERALES x IIBB --}}
                          <tr>
                            <td colspan="4" class="total_analisis">
                              <span class="pull-left">TOTAL GASTOS GENERALES x IIBB</span>
                              <span class="pull-right">0,027</span>     
                            </td>
                            <td class="actions-col noFilter total_analisis">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                  @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                    <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.costos_financieros')}}"
                                      data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'costos_financieros'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                  @endif
                                </ul>
                              </div>
                            </td>
                          </tr>
                          {{--FIN TOTAL GASTOS GENERALES x IIBB --}}

                          <tr>
                            <td>{{strtoupper(trans('analisis_precios.coeficiente.iva'))}}</td>
                            <td class="text-right">{{$contrato->coeficiente->impuestos_coeficiente->iva}} %</td>
                            <td class="text-right">{{$contrato->coeficiente->impuestos_coeficiente->iva_val}}</td>
                            <td class="actions-col noFilter">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                  @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                    <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.iva')}}"
                                       data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'iva'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                  @endif
                                </ul>
                              </div>
                            </td>
                          </tr>
                          {{-- SUB TOTAL COSTOS x IVA --}}
                          <tr>
                            <td colspan="4" class="total_analisis">
                              <span class="pull-left">SUB TOTAL COSTOS x IVA</span>
                              <span class="pull-right">0,027</span>     
                            </td>
                            <td class="actions-col noFilter total_analisis">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                  @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                    <li><a class="open-modal-coeficiente mouse-pointer"  data-title="{{trans('index.editar')}} {{trans('forms.costos_financieros')}}"
                                      data-url="{{route('AnalisisPrecios.coeficiente.edit', ['coeficiente_id' => $contrato->coeficiente->id, 'dato' => 'costos_financieros'])}}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                  @endif
                                </ul>
                              </div>
                            </td>
                          </tr>
                          {{-- FIN SUB TOTAL COSTOS x IVA --}}
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!--Fin Tabla scrollable-->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN Impuestos -->
    </div>

  </div>
