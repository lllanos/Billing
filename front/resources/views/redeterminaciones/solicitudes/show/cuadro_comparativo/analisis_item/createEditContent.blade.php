{{-- Separado para actualizar html --}}
<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
  @foreach ($analisis_item->item->analisis_item->categorias_padres as $keyCategoria => $valueCategoria)
    @php($valueCategoriaCuadro = $analisis_item->getCategoriaCuadro($valueCategoria->id))
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
                @trans('redeterminaciones.cuadro_comparativo.total_redeterminado'): @toDosDec($valueCategoriaCuadro->costo_total)
              </div>
            </a>
          </h4>
        </div>

        <div id="collpapse_{{$valueCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueCategoria->id}}">
          @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.componentes', ['categoriaCuadro' => $valueCategoriaCuadro])

           @if(count($valueCategoria->sub_categorias) > 0)
             <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
               @foreach ($valueCategoria->sub_categorias as $keySubCategoria => $valueSubCategoria)
                 @php($valueSubCategoriaCuadro = $analisis_item->getCategoriaCuadro($valueSubCategoria->id))
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
                               @trans('redeterminaciones.cuadro_comparativo.total_redeterminado'): @toDosDec($valueSubCategoriaCuadro->costo_total)
                           </div>
                         </a>
                       </h4>
                     </div>

                     <div id="collpapse_{{$valueSubCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueSubCategoria->id}}">
                       @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.componentes', ['categoriaCuadro' => $valueSubCategoriaCuadro])

                       @if(count($valueCategoria->sub_categorias) > 0)
                         <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
                           @foreach ($valueSubCategoria->sub_categorias as $keySubSubCategoria => $valueSubSubCategoria)
                             @php($valueSubSubCategoriaCuadro = $analisis_item->getCategoriaCuadro($valueSubSubCategoria->id))
                             <span  id="panel_{{$valueSubSubCategoria->id}}"></span>
                             <div class="panel-group colapsable_top" id="accordion_{{$valueSubSubCategoria->id}}" role="tablist" aria-multiselectable="true">
                               <div class="panel panel-default">
                                 <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_{{$valueSubSubCategoria->id}}">
                                   <h4 class="panel-title titulo_collapse m-0 panel_title_btn panel_heading_ap_2">
                                     <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubSubCategoria->id}}" href="#collpapse_{{$valueSubSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubSubCategoria->id}}">
                                       <i class="fa fa-angle-down pl-2"></i> {{$valueSubSubCategoria->nombre}}
                                     </a>
                                     @if($valueSubSubCategoria->tipo_categoria->nombre == 'MaquinasEquipos')
                                     <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubSubCategoria->id}}" href="#collpapse_{{$valueSubSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubSubCategoria->id}}">
                                       @trans('sol_redeterminaciones.vr')  @toCuatroDec($analisis_item->vr_equipos)
                                     </a>
                                     @endif
                                     <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$valueSubSubCategoria->id}}" href="#collpapse_{{$valueSubSubCategoria->id}}" aria-expanded="true" aria-controls="collpapse_{{$valueSubSubCategoria->id}}">
                                       <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                         @trans('redeterminaciones.cuadro_comparativo.total_redeterminado'): @toDosDec($valueSubSubCategoriaCuadro->costo_total)
                                       </div>
                                     </a>
                                   </h4>
                                 </div>

                                 <div id="collpapse_{{$valueSubSubCategoria->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$valueSubSubCategoria->id}}">
                                   @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.componentes', ['categoriaCuadro' => $valueSubSubCategoriaCuadro])

                                   @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.subtotales', ['categoria' => $valueSubSubCategoriaCuadro])
                                 </div>
                               </div>
                             </div>
                           @endforeach
                         </div>
                       @endif
                     @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.subtotales', ['categoria' => $valueSubCategoriaCuadro])
                     </div>
                   </div>
                 </div>
               @endforeach
             </div>
           @endif
         @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.subtotales', ['categoria' => $valueCategoriaCuadro])
        </div>
      </div>
    </div>
  @endforeach
</div>
