{{-- 2do panel --}}
<div id="collapse_it_{{$valueItem->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_sub_sub_cat">
  <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
    {{-- Sub Collpase --}}
    @foreach($valueItem->item_categorias as $keyItemCategoria => $valueItemCategoria)
      <div class="panel-group colapsable_dos" id="accordion_sub_cat_{{$valueItemCategoria->id}}" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading panel_heading_collapse p-0 tercer_collapse_color" role="tab" id="headingOne_sub_sub_cat">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
              <a
                class="collapse_arrow collapsed"
                role="button" data-toggle="collapse"
                data-parent="#accordion_sub_cat_{{$valueItemCategoria->id}}"
                href="#collapse_sub_cat_{{$valueItemCategoria->id}}" aria-expanded="false"
                aria-controls="collapse_sub_cat_{{$valueItemCategoria->id}}"
              >

                <div class="d-flex container_datos_drop">
                  <span class="container_icon_angle d-flex">
                    <i class="fa fa-angle-up"></i>
                    {{$valueItemCategoria->categoria->nombre}}
                  </span>
                  <span class="d-flex-colum">
                     <span>$24.500.000,80</span>
                     <span>{{trans('analisis_precios.total_calculado')}}</span>
                  </span>
                  <span class="d-flex-colum">
                    <span>$24.500.000</span>
                    <span>{{trans('analisis_precios.total_adaptado')}}</span>
                  </span>
                  {{-- <span>{{trans('analisis_precios.total_por')}}{{$valueItem->unidad_medida->nombre}}</span> --}}
                </div>
              </a>
              @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                @if($valueItemCategoria->categoria->tiene_sub_categorias)

                {{-- <button class="btn btn-success open-modal-sub-categorias"
                 data-toggle="tooltip" data-placement="bottom" title="{{trans('index.agregar_sub_categoria')}}" aria-label="{{trans('index.agregar_sub_categoria')}}"
                  data-url="{{ route('AnalisisPrecios.subcategorias.add', ['item_id' => $valueItem->id])}}">
                  <i class="fa fa-plus"></i>
                </button> --}}

                <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="javascript:void()">
                        <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                      </a>
                    </li>
                    <li>
                      <a class="open-modal-sub-categorias" href="javascript:void()" data-url="{{ route('AnalisisPrecios.subcategorias.add', ['item_id' => $valueItem->id])}}">
                        <i class="fa fa-plus"></i> {{trans('index.agregar_sub_categoria')}}
                      </a>
                    </li>
                  </ul>
                </div>
                @else
                  <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li>
                        <a href="">
                          <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                        </a>
                      </li>
                      <li>
                        <a class="open-modal-insumo" href="javascript:void()" data-url="{{ route('AnalisisPrecios.insumos.add', ['item_categoria_id' => $valueItemCategoria->id])}}">
                          <i class="fa fa-plus"></i> {{trans('index.agregar_insumo')}}
                        </a>
                      </li>
                    </ul>
                  </div>
                @endif
              @endif
            </h4>
          </div>
          <div id="collapse_sub_cat_{{$valueItemCategoria->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_sub_sub_cat">
            <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
              <!--Tabla scrollable-->
                <div class="col-md-12 col-sm-12">
                  <div class="row list-table pt-0 pb-1">
                    <div class="zui-wrapper zui-action-32px-fixed">
                      <div class="zui-scroller"> <!-- zui-no-data -->
                        <table class="table table-striped table-hover table-bordered zui-table">
                          <thead>
                            <tr>
                              <th>{{trans('forms.nombre')}}</th>
                              <th>Cantidad</th>
                              <th>Valor Unitario</th>
                              <th>Total</th>
                              <th class="actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                            </tr>
                          </thead>
                          <tbody class="tbody_tooltip">
                            <tr>
                              <td>nombre</td>
                              <td>cantidad</td>
                              <td class="text-right">$12313</td>
                              <td class="text-right">$123123112</td>
                              <td class="actions-col noFilter">
                                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                    <i class="fa fa-ellipsis-v"></i>
                                  </button>
                                  <ul class="dropdown-menu pull-right">
                                    @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                                      <li><a href=""><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                      <li><a href=""><i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')</a></li>
                                    @endif
                                  </ul>
                                </div>
                              </td>
                            </tr>
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
    @endforeach
    {{-- Fin Sub Collpase --}}
  </div>
</div>
