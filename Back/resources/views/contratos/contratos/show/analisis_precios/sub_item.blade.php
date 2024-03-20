  <div class="panel-group colapsable_sub m-0 pt-1" id="accordion_sub_a_pre_{{$subItem->id}}" data-id='{{$subItem->id}}' role="tablist" aria-multiselectable="true">
    <div class="panel panel-default" id="pepe">
      @if ($subItem->is_hoja)
        <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_a_pre_{{$subItem->id}}">
          <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$tab}} m-0 panel_title_btn">
            @php($item = $subItem)

            @php($analisis_item = $analisis_precios->getAnalisisItem($item->id))
            @include('contratos.contratos.show.analisis_precios.fila', ['header' => false])

            @if((Auth::user()->can('analisis_precios-edit') || Auth::user()->can('analisis_precios-view')))
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
            @endif
          </h4>
        </div>
      @else
        <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_a_pre_{{$subItem->id}}">
          <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$tab}} m-0 panel_title_btn">
            @php($item = $subItem)
            @php ($analisis_item = $analisis_precios->getAnalisisItem($item->id))
            @include('contratos.contratos.show.analisis_precios.fila', ['header' => false])
            <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
              <div class="">
              </div>
            </a>
          </h4>
        </div>

        <div id="collapse_sub_a_pre_{{$subItem->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_a_pre_{{$subItem->id}}">
          <div class="@if($tab > 1) sub_child_sort @else child_sort @endif panel-body panel_sub_tablas p-0 asdfsdfd">
            @ifcount($subItem->child)
              @php ($level = $subItem)
              @foreach($level->child as $subItem)
                @php ($tab++)
                @include('contratos.contratos.show.analisis_precios.sub_item')
              @endforeach
              @php ($subItem = $level)
            @endifcount
          </div>
        </div>
    @endif
  </div>
</div>
