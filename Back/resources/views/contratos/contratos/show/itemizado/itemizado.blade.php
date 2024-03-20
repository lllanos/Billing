<!--Panel-->
<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
  {{-- Si es adenda el cronograma se crea antes para copiar los items pero esta oculto para el usuario --}}
  @if(isset($itemizado) && (
    (isset($fromCronograma) && $valueContratoMoneda->contrato->is_adenda && !$valueContratoMoneda->itemizado->borrador)
    || (isset($fromCronograma) && $valueContratoMoneda->contrato->is_contrato)
    || !isset($fromCronograma)
  ))
    <div class="panel-group colapsable_top mt-1"
      id="accordion{{$sufijo}}{{$keyItemizado}}"
      role="tablist"
      aria-multiselectable="true"
    >
      <div class="panel panel-default">
        {{-- Encabezado --}}
        <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse"
          role="tab"
          id="heading{{$sufijo}}{{$keyItemizado}}"
        >
          <h4 class="panel-title titulo_collapse m-0 panel_title_btn">

            @if(isset($isExcel))
              DATOS EXCEL
            @else

              <a class="btn_acordion datos_as_table collapse_arrow"
                 role="button"
                 data-toggle="collapse"
                 data-parent="#accordion{{$sufijo}}{{$keyItemizado}}"
                 href="#collpapse{{$sufijo}}{{$keyItemizado}}"
                 aria-expanded="true"
                 aria-controls="collpapse{{$sufijo}}{{$keyItemizado}}"
              >
                <div class="container_icon_angle">
                  <i class="fa fa-angle-down"></i> {{$valueContratoMoneda->moneda->nombre_simbolo}}
                </div>
              </a>

              <a class="btn_acordion datos_as_table collapse_arrow"
                 role="button"
                 data-toggle="collapse"
                 data-parent="#accordion{{$sufijo}}{{$keyItemizado}}"
                 href="#collpapse{{$sufijo}}{{$keyItemizado}}"
                 aria-expanded="true"
                 aria-controls="collpapse{{$sufijo}}{{$keyItemizado}}"
              >
                <div class="container_icon_angle"></div>

                @if(!isset($fromCronograma))
                  <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                    @trans('contratos.importe_total'): {{$itemizado->total_dos_dec}}
                  </div>
                @endif
              </a>

              @if($itemizado->borrador == 1 && !isset($fromCronograma))
                <div class="dropdown container_btn_action"
                  data-toggle="tooltip"
                  data-placement="bottom"
                  title="@trans('index.opciones')"
                >
                  @if($monedas->count() < 2 || count($itemizado->items_nivel_1) == 0)
                    <button id="btn_add_itemizado_item"
                      class="btn btn-primary btn_add_itemizado_item"
                      type="button" data-itemizado="{{$keyItemizado}}"
                      data-padre_id=""
                      data-id="0"
                      data-toggle="modal"
                      data-target="#itemizadoAddModal"
                    >
                      <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                  @else
                    <button class="btn btn-primary dropdown-toggle"
                      type="button"
                      data-toggle="dropdown"
                      aria-label="@trans('index.opciones')"
                    >
                      <i class="fa fa-ellipsis-v"></i>
                    </button>

                    <ul class="dropdown-menu pull-right">
                      <li class="btn_options_itemizado">
                        <a id="btn_add_itemizado_item"
                          class="btn_add_itemizado_item"
                          data-itemizado="{{$keyItemizado}}"
                          data-padre_id=""
                          data-id="0"
                          data-toggle="modal"
                          data-target="#itemizadoAddModal"
                        >
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          @trans('index.add')
                        </a>
                      </li>

                      <li class="btn_options_itemizado">
                        <a id="btn_clone_itemizado_item" class="btn_clone_itemizado_item" data-itemizado="{{$keyItemizado}}" data-padre_id="" data-id="0" data-toggle="modal" data-target="#itemizadoAddModal">
                          <i class="fa fa-clone" aria-hidden="true"></i>
                          @trans('index.clone_all')
                        </a>
                      </li>
                    </ul>
                  @endif

                </div>
              @endif

            @endif
          </h4>
        </div>

        <div id="collpapse{{$sufijo}}{{$keyItemizado}}"
          class="panel-collapse collapse in"
          role="tabpanel"
          aria-labelledby="headingOne_sub{{$sufijo}}{{$keyItemizado}}"
        >

          <div class="sort_parent_content panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 scrollable-collapse">

            @if(count($itemizado->items_nivel_1) > 0)
              <div class="cancel panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                <div class="panel panel-default">
                  <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_heading">
                    <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
                      @include('contratos.contratos.show.itemizado_cronograma.fila', ['header' => true, 'subheader' => false])
                    </h4>
                  </div>
                </div>
              </div>

              @if(isset($fromCronograma))
                <div class="cancel panel-body panel_sub_tablas p-0">
                  <div class="panel panel-default">
                    <div class="panel-heading panel_heading_collapse p-0"
                      role="tab" id="headingOne_sub_heading"
                    >
                      <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_total m-0 panel_title_btn">
                        @include('contratos.contratos.show.itemizado_cronograma.fila', ['header' => false, 'subheader' => true])
                      </h4>
                    </div>
                  </div>
                </div>
              @endif

              @php($i=0)

                @foreach($itemizado->items_nivel_1 as $keyItem => $level1)
                @php($i++)
                <div class="panel-body panel_sub_tablas p-0" id="{{$level1->id}}">
                  <div class="panel panel-default">
                    <div class="panel-heading panel_heading_collapse p-0"
                      role="tab"
                      id="headingOne_sub_{{$level1->id}}"
                    >
                      <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_heading_0">
                        @if($itemizado->borrador == 1 && !isset($fromCronograma))
                          <span class="handle"></span>
                        @endif

                         @if(isset($isExcel))
                          DATOS EXCEL
                        @else
                          @php ($item = $level1)
                          @php ($tab = 0)

                          @include('contratos.contratos.show.itemizado_cronograma.fila', ['header' => false, 'subheader' => false])

                          @if($itemizado->borrador == 1 && !isset($fromCronograma))

                            <div class="dropdown container_btn_action dato-opciones"
                              data-toggle="tooltip"
                              data-placement="bottom"
                              title="@trans('index.opciones')">
                              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                <i class="fa fa-ellipsis-v"></i>
                              </button>

                              <ul class="dropdown-menu pull-right">
                                <li class="btn_edit_itemizado_item"
                                  data-itemizado="{{$keyItemizado}}"
                                  data-padre_id="{{$level1->padre_id}}"
                                  data-id="{{$level1->id}}"
                                  data-nivel="{{$level1->nivel}}"
                                  data-toggle="modal"
                                  data-target="#itemizadoAddModal"
                                >
                                  <a><i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.editar')</a>
                                </li>

                                @if($monedas->count() > 1)
                                <li class="btn_clone_itemizado_item"
                                  data-itemizado="{{$keyItemizado}}"
                                  data-padre_id="{{$level1->padre_id}}"
                                  data-id="{{$level1->id}}"
                                  data-nivel="{{$level1->nivel}}"
                                  data-toggle="modal"
                                  data-target="#itemizadoAddModal"
                                >
                                  <a><i class="fa fa-clone" aria-hidden="true"></i> @trans('index.clone')</a>
                                </li>
                                @endif

                                @if (!$level1->is_hoja)
                                  <li class="btn_add_itemizado_item"
                                    data-itemizado="{{$keyItemizado}}"
                                    data-padre_id="{{$level1->id}}"
                                    data-id="{{$level1->id}}"
                                    data-nivel="{{$level1->nivel}}"
                                    data-toggle="modal"
                                    data-target="#itemizadoAddModal">
                                    <a><i class="fa fa-plus" aria-hidden="true"></i> @trans('index.agregar')</a>
                                  </li>
                                @endif

                                @if (sizeof($level1->child) == 0 && $level1->no_certificado)
                                  <li>
                                    <a class="btn-confirmable"
                                      data-body="{{trans('contratos.confirmacion.delete-itemizado', ['item' => $level1->descripcion])}}"
                                      data-action="{{route('itemizado.deleteItem', ['item_id' => $level1->id ])}}"
                                      data-si="@trans('index.si')"
                                      data-no="@trans('index.no')"
                                    >
                                      <i class="fa fa-trash" aria-hidden="true"></i>
                                      @trans('index.eliminar')
                                    </a>
                                  </li>
                                @endif
                              </ul>
                            </div>

                          @elseif($itemizado->borrador == 0 && !isset($fromCronograma))

                            @if($itemizado != null && $itemizado->is_editable)
                              <div class="dropdown container_btn_action dato-opciones"
                                data-toggle="tooltip"
                                data-placement="bottom"
                                title="@trans('index.opciones')"
                              >

                                <button class="btn btn-primary dropdown-toggle"
                                  type="button"
                                  data-toggle="dropdown"
                                  aria-label="@trans('index.opciones')"
                                >
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>

                                <ul class="dropdown-menu pull-right">
                                  <li class="btn_edit_itemizado_item"
                                    data-itemizado="{{$keyItemizado}}"
                                    data-padre_id="{{$level1->padre_id}}"
                                    data-id="{{$level1->id}}"
                                    data-nivel="{{$level1->nivel}}"
                                    data-toggle="modal"
                                    data-target="#itemizadoAddModal"
                                  >
                                    <a>
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                        @trans('index.editar')
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            @endif

                          @elseif ($itemizado->borrador == 1 && isset($fromCronograma))
                            @if (!$level1->is_hoja)
                              <a class="btn_acordion datos_as_table collapse_arrow dato-opciones"
                                role="button"
                                data-toggle="collapse"
                                data-parent="#accordion_sub_heading"
                                href="#collapseOne_sub_heading"
                                aria-expanded="true"
                                aria-controls="collapseOne_sub_heading"
                              >
                                <div></div>
                              </a>
                            @else
                              @if($itemizado != null && $itemizado->is_editable)
                                <div class="dropdown container_btn_action dato-opciones">
                                  <button class="btn btn-primary open-modal-ItemCronograma"
                                    data-toggle="tooltip" data-placement="bottom"
                                    title="{{trans('cronograma.agregar_avances')}}"
                                    aria-label="{{trans('cronograma.agregar_avances_en')}}"
                                    data-url="{{ route('cronograma.item.getHtmlEdit', ['item_id' => $level1->id, 'cronograma_id' => $itemizado->id])}}"
                                  >
                                    <i class="glyphicon glyphicon-pencil"></i>
                                  </button>
                                </div>
                              @endif
                            @endif
                          @endif
                        @endif
                      </h4>
                    </div>

                    <div id="collapse_sub{{$sufijo}}{{$level1->id}}"
                      class="panel-collapse collapse in"
                      role="tabpanel"
                      aria-labelledby="headingOne_sub{{$sufijo}}{{$level1->id}}"
                    >
                      <div class="parent_sort panel-body panel_sub_tablas p-0">
                        @ifcount($level1->child)
                          @foreach($level1->child as $subItem)
                            @php ($tab = 1)
                            @include('contratos.contratos.show.itemizado.sub_item')
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
