<!--Panel-->
  <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
    @if(isset($redeterminacion))
      <div class="panel-group colapsable_top mt-1" id="accordion_a_pre_{{$redeterminacion->id}}" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          {{-- Encabezado --}}
          <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_a_pre_{{$redeterminacion->id}}">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$redeterminacion->id}}" href="#collpapse_a_pre_{{$redeterminacion->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$redeterminacion->id}}">
                <div class="container_icon_angle">
                  <i class="fa fa-angle-down"></i> {{$redeterminacion->itemizado->contrato_moneda->moneda->nombre_simbolo}}
                </div>
              </a>
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$redeterminacion->id}}" href="#collpapse_a_pre_{{$redeterminacion->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$redeterminacion->id}}">
                <div class="container_icon_angle">
                </div>
                <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                  @trans('contratos.importe_total'): @toDosDec($redeterminacion->importe_total)
                </div>
              </a>
              <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$redeterminacion->id}}" href="#collpapse_a_pre_{{$redeterminacion->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$redeterminacion->id}}">
                <div class="container_icon_angle">
                </div>
                <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                  @trans('redeterminaciones.vr_total'): @toCuatroDec($redeterminacion->variacion)
                </div>
              </a>
            </h4>
          </div>

          <div id="collpapse_a_pre_{{$redeterminacion->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$redeterminacion->id}}">
            <div class="sort_parent_content panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 scrollable-collapse">
              @if(count($redeterminacion->itemizado->items_nivel_1) > 0)
                <div class="cancel panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                  <div class="panel panel-default">
                    <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_heading">
                      <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
                        @include('redeterminaciones.itemizado.fila', ['header' => true])
                      </h4>
                    </div>
                  </div>
                </div>
                @php($i = 0)
                @foreach($redeterminacion->itemizado->items_nivel_1 as $keyItem => $level1)
                  @php($i++)
                  <div class="panel-body panel_sub_tablas p-0" id="{{$level1->id}}">
                    <div class="panel panel-default" >
                      <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_{{$level1->id}}">
                        <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_heading_0">
                          @php ($item = $level1)
                          @php ($precio = $redeterminacion->precioRedeterminadosDeItem($item->id))
                          @php ($tab = 0)
                          @include('redeterminaciones.itemizado.fila', ['header' => false])
                          @if($level1->is_hoja && $redeterminacion->itemizado->contrato_moneda->lleva_analisis)
                            @php ($itemId = $redeterminacion->analisisItemRedeterminadoId($level1->id))
                            <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu pull-right">
                                <li><a href="{{route('empalme.analisis_item.ver', ['item_id' => $itemId])}}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver') @trans('analisis_precios.analisis_item')</a></li>
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
                              @include('redeterminaciones.itemizado.sub_item')
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
