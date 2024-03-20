<div class="">
  <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
    <?php $contador_categoria = 1; $contador_sub_categoria = 1; ?>
    @if(sizeof($valores_por_categoria) > 0)
    @foreach($valores_por_categoria as $keyCategoria => $categoria)

      @if(!(sizeof($categoria) == 1 && sizeof(reset($categoria)) == 0))
    <!--Collapse-->
      <div class="panel-group colapsable_top" id="accordion_{{$contador_categoria}}" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="headingOne_{{$contador_categoria}}">
            <h4 class="panel-title m-0 titulo_collapse">
              <a role="button" data-toggle="collapse" data-parent="#accordion_{{$contador_categoria}}" href="#collpapse_{{$contador_categoria}}" aria-expanded="true" aria-controls="collpapse_{{$contador_categoria}}">
                <i class="fa fa-angle-down"></i> {{$keyCategoria}}
              </a>
            </h4>
          </div>

          <div id="collpapse_{{$contador_categoria}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_{{$contador_categoria}}">
            <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
              @foreach($categoria as $keySubcategoria => $subcategoria)
                @if($keySubcategoria != 'N/A')
                <!--Sub Collpase-->
                  <div class="panel-group colapsable_sub" id="accordion_sub_{{$contador_sub_categoria}}" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_{{$contador_sub_categoria}}">
                        <h4 class="panel-title pl-2 m-0 titulo_collapse">
                          <a role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$contador_sub_categoria}}" href="#collapseOne_sub{{$contador_sub_categoria}}" aria-expanded="true" aria-controls="collapseOne_sub{{$contador_sub_categoria}}">
                            <i class="fa fa-angle-down"></i> {{$keySubcategoria}}
                          </a>
                        </h4>
                      </div>
                      <div id="collapseOne_sub{{$contador_sub_categoria}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_{{$contador_categoria}}">
                @endif
                        <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
                        <!--Tabla scrollable-->
                          <div class="col-md-12 col-sm-12 pt-0 pb-1">
                            <div class="list-table p-0">
                              <div class="zui-wrapper zui-action-32px-fixed">
                                <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
                                  <table class="table table-striped table-hover zui-table">
                                    <thead>
                                      <tr>
                                        <th class="text-center tb_indice_nro">#</th>
                                        <th class="tb_indice_nombre">@trans('forms.nombre')</th>
                                        <th class="tb_indice_app">@trans('forms.aplicacion')</th>
                                        <th class="tb_indice_fuente">@trans('forms.fuente')</th>
                                        <th class=" tb_valor_anterior">@trans('forms.valor_anterior')</th>
                                        <th class="tb_nvo_valor">@trans('forms.nuevo_valor')</th>
                                        <th class="tb_indice_vr text-center">@trans('forms.vr')</th>
                                      </tr>
                                    </thead>
                                    <tbody class="tbody_con_input tbody_tooltip">
                                      @foreach($subcategoria[0] as $keyIndice => $valor_indice)
                                        <tr class="@if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif">
                                          <td class="text-center tb_indice_nro">
                                            @if($valor_indice->indice_tabla1->compuesto)
                                              <span data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valor_indice->indice_tabla1->mensaje_composicion}}">
                                                <i class="fa fa-tasks" aria-hidden="true"></i>
                                            @else
                                              <span>
                                            @endif
                                                {{$valor_indice->indice_tabla1->nro}}
                                              </span>
                                          </td>
                                          <td class="tb_indice_nombre" title="{{$valor_indice->indice_tabla1->nombre}}">
                                            <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->indice_tabla1->nombre}}">
                                              {{$valor_indice->indice_tabla1->nombre}}
                                            </span>
                                          </td>
                                          <td class="tb_indice_app" title="{{$valor_indice->indice_tabla1->aplicacion}}">
                                            <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->indice_tabla1->aplicacion}}">
                                              {{$valor_indice->indice_tabla1->aplicacion}}
                                            </span>
                                          </td>
                                          <td class="tb_indice_fuente">
                                            @if($valor_indice->indice_tabla1->fuente_id != null)
                                              <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->indice_tabla1->fuente->nombre}}">
                                                {{$valor_indice->indice_tabla1->fuente->nombre}}
                                              </span>
                                            @endif
                                          </td>
                                          <td class="text-right tb_valor_anterior"><span id="valor_old_{{$valor_indice->tabla1_id}}">{{$valor_indice->valor_anterior_show}}</span></td>
                                          <td class="text-right tb_nvo_valor">{{$valor_indice->valor_show}}</td>
                                          <td class="tb_indice_vr text-center">
                                            <label id="vr_{{$valor_indice->tabla1_id}}" class="label label_default {{$valor_indice->color_class}} text-center">
                                              {{$valor_indice->variacion_show}}
                                            </label>
                                          </td>
                                        </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        <!--Fin Tabla scrollable-->
                        </div>
                @if($keySubcategoria != 'N/A')
                      </div>
                    </div>
                  </div>
                @endif
                <?php $contador_sub_categoria++; ?>
              @endforeach
            </div>
          </div>

        </div>
      </div>
    <!--FIN Collapse-->
    <?php $contador_categoria++; ?>
  @endif
    @endforeach
  </div>
  @else
    <div class="sin_datos">
      <h1 class="text-center">@trans('index.no_datos')</h1>
    </div>
  @endif
  <div class="sin_datos_js"></div>
</div>
