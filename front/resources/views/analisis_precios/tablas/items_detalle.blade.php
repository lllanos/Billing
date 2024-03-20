<div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
  @foreach($items as $keyItem => $valueItem)
    <div class="panel-group colapsable_uno" id="accordion_it_{{$valueItem->id}}" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        {{-- Dropdown 1 --}}
          <div class="panel-heading panel_heading_it_{{$valueItem->id}} p-0 panel_heading_collapse segundo_collapse_color" role="tab" id="headingOne_it_{{$valueItem->id}}">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
              <a class="collapse_arrow collapsed" role="button" data-toggle="collapse"
                data-parent="#accordion_it_{{$valueItem->id}}"
                href="#collapse_it_{{$valueItem->id}}" aria-expanded="false"
                aria-controls="collapse_it_{{$valueItem->id}}"
              >
                <div class="d-flex container_datos_drop">
                  <span class="container_icon_angle d-flex">
                    <i class="fa fa-angle-up mr-_5"></i>
                    {{$valueItem->descripcion}}
                  </span>
                  <span class="d-flex-colum">
                    <span>{{$valueItem->cantidad}}</span>
                    <span>{{$valueItem->unidad_medida->nombre}}</span>
                  </span>
                  <span class="d-flex-colum">
                    <span class="precio_analisis">{{$valueItem->monto_actual}}</span>
                    <span class="total_analisis">{{trans('analisis_precios.total_calculado')}}</span>
                  </span>
                  <span class="d-flex-colum">
                    <span class="precio_analisis">{{$valueItem->monto_actual}}</span>
                    <span class="total_analisis">{{trans('analisis_precios.total_adaptado')}}</span>
                  </span>
                </div>
              </a>
              @if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
                <div class="container_btns_plus_action">
                  <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li>
                        <a class="open-modal-error" href="javascript:void(0);">
                          <i class="glyphicon glyphicon-sort"></i> {{ trans('index.ajustar_error')}}
                        </a>
                        <hr class="m-0">
                        <a class="open-modal-categorias" href="javascript:void(0)" data-url="{{route('AnalisisPrecios.categorias.add', ['item_id' => $valueItem->id])}}">
                          <i class="fa fa-plus"></i></i> {{trans('index.agregar_categoria')}}
                        </a>
                        @foreach($valueItem->item_categorias as $keyItemCategoria => $valueItemCategoria)
                          <a class="" href="javascript:void(0)" >
                            <i class="fa fa-tag" aria-hidden="true"></i> {{$valueItemCategoria->categoria->nombre}}
                          </a>
                        @endforeach
                      </li>
                    </ul>
                  </div>
                </div>
              @endif
            </h4>
          </div>
        {{-- Fin Dropdown 1 --}}
        {{-- Categorias --}}
          <div id="categorias_{{$valueItem->id}}">
            {{-- @foreach($valueItem->item_categorias as $keyItemCategoria => $valueItemCategoria) --}}
              @include('analisis_precios.tablas.categoria')
            {{-- @endforeach --}}
          </div>
        {{--  Fin categorias --}}
      </div>
    </div>
  @endforeach
</div>
