<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
  <?php $items_cont = 1;?>
  <?php $cont_accordion = 1;?>
  {{-- {{dd($items_por_obra)}} --}}
  @foreach($items_por_obra as $keyItemPorObra => $items)
    <div class="panel-group acordion colapsable_cero" id="accordion-{{$cont_accordion}}" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        {{-- Dropdown 0 --}}
          <div class="panel-heading p-0 panel_heading_collapse primer_collapse_color" role="tab" id="headingOne-{{$cont_accordion}}">
            <h4 class="panel-title titulo_collapse m-0">
              <a class="btn_acordion dos_datos collapse_arrow collapsed ajax-collapse" role="button" data-toggle="collapse"
                  data-parent="#accordion-{{$cont_accordion}}" href="#collapseTipoObra_{{$cont_accordion}}"
                  aria-expanded="false" aria-controls="collapseTipoObra_{{$cont_accordion}}"
                  data-url="{{ route('AnalisisPrecios.item.detalle', ['categoria_obra' => $keyItemPorObra, 'contrato_id' => $contrato->id])}}"
                  data-id="{{$cont_accordion}}"
              >
                <div class="d-flex container_datos_drop w-100">
                  <span class="container_icon_angle">
                    <i class="fa fa-angle-up"></i>
                    {{trans($keyItemPorObra)}}
                  </span>
                  <span class="d-flex-colum">
                     <span>$300.000</span>
                     <span>{{trans('analisis_precios.total')}}</span>
                  </span>
                </div>
              </a>
            </h4>
          </div>
        {{-- Fin Dropdown 0 --}}
        {{-- 1r panel --}}
          <div id="collapseTipoObra_{{$cont_accordion}}" class="panel-collapse collapse more-data-{{$cont_accordion}}" role="tabpanel" aria-labelledby="headingOne">
          </div>
          <?php $cont_accordion++;?>
        {{-- Fin 1r panel --}}
      </div>
    </div>
  @endforeach
</div>
