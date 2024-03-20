    <!--Panel-->
    <div class="">
      <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
        <?php $contador_categoria = 1; $contador_sub_categoria = 1; ?>
        @if(sizeof($valores_por_categoria) > 0)
        @foreach($valores_por_categoria as $keyCategoria => $categoria)
        <!--Collapse-->
          <div class="panel-group colapsable_top" id="accordion_{{$contador_categoria}}" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
              <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="headingOne_{{$contador_categoria}}">
                <h4 class="panel-title m-0 titulo_collapse">
                  @if(isset($isExcel))
                    {{$keyCategoria}}
                  @else
                  <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$contador_categoria}}" href="#collpapse_{{$contador_categoria}}" aria-expanded="true" aria-controls="collpapse_{{$contador_categoria}}">
                    <i class="fa fa-angle-down"></i> {{$keyCategoria}}
                  </a>
                  @endif
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
                            <h4 class="panel-title pl-2 m-0">
                              @if(isset($isExcel))
                                {{$keySubcategoria}}
                              @else
                              <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$contador_sub_categoria}}" href="#collapseOne_sub_{{$contador_sub_categoria}}" aria-expanded="true" aria-controls="collapseOne_sub_{{$contador_sub_categoria}}">
                                <i class="fa fa-angle-down"></i> {{$keySubcategoria}}
                              </a>
                              @endif
                            </h4>
                          </div>
                          <div id="collapseOne_sub_{{$contador_sub_categoria}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_{{$contador_sub_categoria}}">
                    @endif
                            <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
                            <!--Tabla scrollable-->
                            <div class="col-md-12 col-sm-12">
                              <div class="row list-table pt-0 pb-1">
                                <div class="zui-wrapper zui-action-32px-fixed">
                                  <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
                                    <table class="table table-striped table-hover table-bordered zui-table">
                                      <thead>
                                          <tr>
                                            <th class="text-center">#</th>
                                            <th class="tb_nombre_reporte">{{trans('forms.nombre')}}</th>
                                            @foreach($ids_publicaciones as $keyPublicacion => $valuePublicacion)
                                              <th class="text-center tb_meses_reporte">{{trans('meses.mes_reducido.'. $valuePublicacion)}}</th>
                                            @endforeach
                                          </tr>
                                      </thead>
                                      <tbody class="tbody_tooltip">
                                        @foreach($subcategoria[0] as $keyIndice => $valor_indice)
                                          @if(!$valor_indice->no_se_publica)
                                            <tr>
                                              <td class="text-center tb_indice_nro">
                                                @if(!isset($isExcel))
                                                  {{$valor_indice->nro}}
                                                @endif
                                              </td>
                                              <td class="tb_nombre_reporte">
                                                <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->nombre}}">
                                                  {{$valor_indice->nombre}}
                                                </span>
                                              </td>
                                              @foreach($ids_publicaciones as $keyPublicacion => $valuePublicacion)
                                                <td class="tb_meses_reporte text-right">
                                                  @if(isset($valor_indice['valores'][$keyPublicacion]))
                                                    {{ $valor_indice['valores'][$keyPublicacion] }}
                                                  @else
                                                    -
                                                  @endif
                                                   </td>
                                              @endforeach
                                            </tr>
                                          @endif
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
        @endforeach
      </div>
      @else
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      @endif
      <div class="sin_datos_js"></div>
    </div>
    <!--Fin Panel-->
