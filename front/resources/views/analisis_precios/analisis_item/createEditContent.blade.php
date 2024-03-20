{{-- Separado para actualizar html --}}
@php($redetermina = $analisis_item->analisis_precios->es_redeterminacion)
<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
  @foreach ($analisis_item->categorias_padres as $keyCategoria => $valueCategoria)
    <span  id="panel_{{$valueCategoria->id}}"></span>
    <div class="panel-group colapsable_top" id="accordion_{{$valueCategoria->id}}" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_{{$valueCategoria->id}}">
          <h4 class="panel-title titulo_collapse m-0 panel_title_btn panel_heading_ap_0">
            <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueCategoria->id}}" href="#collpapse_{{$valueCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueCategoria->id}}">
              <i class="fa fa-angle-down"></i> {{$valueCategoria->nombre}}
            </a>
            <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueCategoria->id}}" href="#collpapse_{{$valueCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueCategoria->id}}">
              <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                @trans('analisis_item.costo_total'): @toDosDec($valueCategoria->costo_total_adaptado)
              </div>
            </a>
            @if(!$redetermina)
               @include('analisis_precios.analisis_item.actions', ['categoria' => $valueCategoria])
            @endif
          </h4>
        </div>

        <div id="collpapse_{{$valueCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueCategoria->id}}">
          @include('analisis_precios.analisis_item.componentes', ['categoria' => $valueCategoria])

           @if(count($valueCategoria->sub_categorias) > 0)
             <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
               @foreach ($valueCategoria->sub_categorias as $keySubCategoria => $valueSubCategoria)
                 <span  id="panel_{{$valueSubCategoria->id}}"></span>
                 <div class="panel-group colapsable_top" id="accordion_{{$valueSubCategoria->id}}" role="tablist" aria-multiselectable="true">
                   <div class="panel panel-default">
                     <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_{{$valueSubCategoria->id}}">
                       <h4 class="panel-title titulo_collapse m-0 panel_title_btn panel_heading_ap_1">
                         <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubCategoria->id}}" href="#collpapse_{{$valueSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubCategoria->id}}">
                           <i class="fa fa-angle-down pl-1"></i> {{$valueSubCategoria->nombre}}
                         </a>
                         <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubCategoria->id}}" href="#collpapse_{{$valueSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubCategoria->id}}">
                           <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                             @trans('analisis_item.costo_total'): @toDosDec($valueSubCategoria->costo_total_adaptado)
                           </div>
                         </a>
                           @if(!$redetermina)
                             @include('analisis_precios.analisis_item.actions', ['categoria' => $valueSubCategoria])
                           @endif
                       </h4>
                     </div>

                     <div id="collpapse_{{$valueSubCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueSubCategoria->id}}">
                       @include('analisis_precios.analisis_item.componentes', ['categoria' => $valueSubCategoria])

                       @if(count($valueCategoria->sub_categorias) > 0)
                         <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
                           @foreach ($valueSubCategoria->sub_categorias as $keySubSubCategoria => $valueSubSubCategoria)
                             <span  id="panel_{{$valueSubSubCategoria->id}}"></span>
                             <div class="panel-group colapsable_top" id="accordion_{{$valueSubSubCategoria->id}}" role="tablist" aria-multiselectable="true">
                               <div class="panel panel-default">
                                 <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_{{$valueSubSubCategoria->id}}">
                                   <h4 class="panel-title titulo_collapse m-0 panel_title_btn panel_heading_ap_2">
                                     <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubSubCategoria->id}}" href="#collpapse_{{$valueSubSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubSubCategoria->id}}">
                                       <i class="fa fa-angle-down pl-2"></i> {{$valueSubSubCategoria->nombre}}
                                     </a>
                                     <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubSubCategoria->id}}" href="#collpapse_{{$valueSubSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubSubCategoria->id}}">
                                       <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                         @trans('analisis_item.costo_total'): @toDosDec($valueSubSubCategoria->costo_total_adaptado)
                                       </div>
                                     </a>
                                     @if(!$redetermina)
                                       @include('analisis_precios.analisis_item.actions', ['categoria' => $valueSubSubCategoria])
                                     @endif
                                   </h4>
                                 </div>

                                 <div id="collpapse_{{$valueSubSubCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueSubSubCategoria->id}}">
                                   <!-- scrollable-collapse pl-1 pt-1 pr-1 pb-0 class remove -->
                                   @include('analisis_precios.analisis_item.componentes', ['categoria' => $valueSubSubCategoria])

                                   @include('analisis_precios.analisis_item.subtotales', ['categoria' => $valueSubSubCategoria])
                                 </div>
                               </div>
                             </div>
                           @endforeach
                         </div>
                       @endif
                     @include('analisis_precios.analisis_item.subtotales', ['categoria' => $valueSubCategoria])
                     </div>
                   </div>
                 </div>
               @endforeach
             </div>
           @endif
         @include('analisis_precios.analisis_item.subtotales', ['categoria' => $valueCategoria])
        </div>
      </div>
    </div>
  @endforeach
</div>
