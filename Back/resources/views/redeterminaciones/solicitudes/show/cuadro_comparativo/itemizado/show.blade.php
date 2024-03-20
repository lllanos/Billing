<!--Panel-->
<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
    @if(isset($cuadro_comparativo))
        <div class="panel-group colapsable_top mt-1" id="accordion_a_pre_{{$cuadro_comparativo->id}}" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                {{-- Encabezado --}}
                <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_a_pre_{{$cuadro_comparativo->id}}">
                    <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$cuadro_comparativo->id}}" href="#collpapse_a_pre_{{$cuadro_comparativo->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$cuadro_comparativo->id}}">
                            <div class="container_icon_angle">
                                <i class="fa fa-angle-down"></i> {{$cuadro_comparativo->itemizado->contrato_moneda->moneda->nombre_simbolo}}
                            </div>
                        </a>
                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_a_pre_{{$cuadro_comparativo->id}}" href="#collpapse_a_pre_{{$cuadro_comparativo->id}}" aria-expanded="true" aria-controls="collpapse_a_pre_{{$cuadro_comparativo->id}}">
                            <div class="container_icon_angle">
                            </div>
                            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                @trans('redeterminaciones.cuadro_comparativo.total_redeterminado'):
                                @toDosDec($cuadro_comparativo->total_redeterminado)
                            </div>
                        </a>
                    </h4>
                </div>

                <div id="collpapse_a_pre_{{$cuadro_comparativo->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$cuadro_comparativo->id}}">
                    <div class="sort_parent_content panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 scrollable-collapse">
                        @if(count($cuadro_comparativo->itemizado->items_nivel_1) > 0)
                            <div class="cancel panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_heading">
                                        <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
                                            @include('redeterminaciones.solicitudes.show.cuadro_comparativo.itemizado.fila', ['header' => true])
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            @php($i = 0)
                            @foreach($cuadro_comparativo->itemizado->items_nivel_1 as $keyItem => $level1)
                                @php($i++)
                                <div class="panel-body panel_sub_tablas p-0" id="{{$level1->id}}">
                                    <div class="panel panel-default">
                                        <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_{{$level1->id}}">
                                            <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_heading_0">
                                                @php ($item = $level1)
                                                @php ($tab = 0)
                                                @include('redeterminaciones.solicitudes.show.cuadro_comparativo.itemizado.fila', ['header' => false])
                                                @if($level1->is_hoja)
                                                    @php($item_cuadro_comparativo = $cuadro_comparativo->getItemCuadroComparativo($item))
                                                    <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right">
                                                            @if($cuadro_comparativo->contrato_moneda->lleva_analisis)
                                                                <li>
                                                                    <a href="{{route('cuadroComparativo.item.ver', ['id' => $item_cuadro_comparativo->id])}}"><i class="glyphicon glyphicon-eye-open"></i>
                                                                        @trans('index.ver')
                                                                        @trans('redeterminaciones.cuadro_comparativo.analisis_item')</a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a href="{{ route('contratos.verSalto', ['variacion_id' => $cuadro_comparativo->salto_id, 'id_cuadro' => $cuadro_comparativo->id]) }}" title="@trans('index.ver')"><i class="glyphicon glyphicon-eye-open"></i>
                                                                        @trans('index.ver') @trans('forms.salto')</a>
                                                                </li>
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
                                        <div id="collapse_sub_cuad_{{$level1->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$level1->id}}">
                                            <div class="parent_sort panel-body panel_sub_tablas p-0">
                                                @ifcount($level1->child)
                                                @foreach($level1->child as $subItem)
                                                    @php ($tab = 1)
                                                    @include('redeterminaciones.solicitudes.show.cuadro_comparativo.itemizado.sub_item')
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
