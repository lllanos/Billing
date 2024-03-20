<!--Panel-->
  <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
    @if(isset($analisis_precios))
      <div class="panel-group colapsable_top mt-1" id="accordion_a_pre_{{$analisis_precios->id}}" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          {{-- Encabezado --}}
          <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_a_pre_{{$analisis_precios->id}}">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$analisis_precios->id}}" href="#collpapse_a_pre_{{$analisis_precios->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$analisis_precios->id}}">
                <div class="container_icon_angle">
                  <i class="fa fa-angle-down"></i> {{$valueContratoMoneda->moneda->nombre_simbolo}}
                </div>
              </a>
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$analisis_precios->id}}" href="#collpapse_a_pre_{{$analisis_precios->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$analisis_precios->id}}">
                <div class="container_icon_angle">
                </div>
                <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                  @trans('contratos.importe_total'): @toDosDec($analisis_precios->costo_total_adaptado * $analisis_precios->coeficiente_k)
                </div>
              </a>
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$analisis_precios->id}}" href="#collpapse_a_pre_{{$analisis_precios->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$analisis_precios->id}}">
                <div class="container_icon_angle">
                </div>
                <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                  @trans('analisis_precios.coeficiente_k'): @toCuatroDec($analisis_precios->coeficiente_k)
                </div>
              </a>

              @if($analisis_precios->permite_editar)
                <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('analisis_precios.coeficiente_k')">
                  <button class="btn btn-primary open-modal-coeficiente"
                    aria-label="@trans('analisis_precios.coeficiente_k')"
                    data-url="{{ route('analisis_precios.editCoeficienteK', ['analisis_precios_id' => $analisis_precios->id])}}">
                      <i class="glyphicon glyphicon-pencil"></i>
                  </button>
                </div>
              @endif
            </h4>
          </div>

          <div id="collpapse_a_pre_{{$analisis_precios->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$analisis_precios->id}}">
            <div class="sort_parent_content panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 scrollable-collapse">
              @if(count($analisis_precios->items_nivel_1) > 0)
                <div class="cancel panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                  <div class="panel panel-default">
                    <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_heading">
                      <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
                        @include('contratos.contratos.show.analisis_precios.fila', ['header' => true])
                      </h4>
                    </div>
                  </div>
                </div>
                @php($i = 0)
                @foreach($analisis_precios->items_nivel_1 as $keyItem => $level1)
                  @php($i++)
                  <div class="panel-body panel_sub_tablas p-0" id="{{$level1->id}}">
                    <div class="panel panel-default" >
                      <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_{{$level1->id}}">
                        <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_heading_0">
                          @php ($item = $level1)
                          @php ($analisis_item = $analisis_precios->getAnalisisItem($level1->id))
                          @php ($tab = 0)
                          @include('contratos.contratos.show.analisis_precios.fila', ['header' => false])
                          @if($level1->is_hoja && (Auth::user()->can('analisis_precios-edit') || Auth::user()->can('analisis_precios-view')))
                            <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                <li><a href="{{route('analisis_item.ver', ['analisis_item_id' => $analisis_item->id])}}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                @if($analisis_item->permite_editar)
                                  <li><a href="{{route('analisis_item.edit', ['analisis_item_id' => $analisis_item->id])}}"><i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.editar')</a></li>
                                  @foreach($analisis_item->acciones_posibles as $keyAccion => $valueAccion)
                                    <li>
                                      <a class="btn-confirmable"
                                       data-body="@trans('analisis_item.confirmaciones.' . $valueAccion)"
                                       data-action="{{route('analisis_item.storeUpdate', ['analisis_item_id' => $analisis_item->id, 'accion' => $valueAccion])}}"
                                       data-si="@trans('index.si')" data-no="@trans('index.no')">
                                       <i class="fa fa-check" aria-hidden="true"></i>@trans('analisis_item.acciones.' . $valueAccion)
                                      </a>
                                    </li>
                                  @endforeach
                                @endif
                              </ul>
                            </div>
                          @else
                            <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
                              <div class=""></div>
                            </a>
                          @endif
                        </h4>
                      </div>
                      <div id="collapse_sub_a_pre_{{$level1->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$level1->id}}">
                        <div class="parent_sort panel-body panel_sub_tablas p-0">
                          @ifcount($level1->child)
                            @foreach($level1->child as $subItem)
                              @php ($tab = 1)
                              @include('contratos.contratos.show.analisis_precios.sub_item')
                            @endforeach
                          @endifcount
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="sin_datos">
        <h1 class="text-center">@trans('index.no_datos')</h1>
      </div>
    @endif
  <div class="sin_datos_js"></div>
</div>
