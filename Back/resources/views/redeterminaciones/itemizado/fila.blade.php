@if($header)

  <a class="btn_acordion datos_as_table collapse_arrow dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('forms.codigo')
    </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('index.descripcion')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('forms.unidad_medida')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_importe pull-right" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="pull-right">
      @trans('redeterminaciones.monto_unitario_anterior')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_importe pull-right" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="pull-right">
      @trans('redeterminaciones.monto_unitario_redeterminado')
   </div>
  </a>
  @if($redeterminacion->analisis_precios)
    <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
      <div class="">
        @trans('analisis_precios.costo_coeficiente_k')
     </div>
    </a>
  @endif
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('forms.vr')
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      <i class="glyphicon glyphicon-cog"></i>
    </div>
  </a>
@else
@php ($analisis_item = $redeterminacion->analisisItemRedeterminadoId($item->id))
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="container_icon_angle">
      @if($item->is_nodo) <i class="fa fa-angle-down pl-{{$item->nivel - 1}}"></i>
      @else <i class="fa fa-angle-right pl-{{$item->nivel - 1}}"></i> @endif
      {{$item->codigo}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->descripcion}}">
      {{$item->descripcion}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($precio != null)
        {{$item->unidad_medida_o_alzado_nombre}}
      @endif
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="pull-right">
      @if($precio != null)
        <span id="monto_unitario_anterior_{{$item->id}}">
             @toDosDec($precio->monto_unitario_anterior)
        </span>
      @endif
   </div>
  </a>

   @if($item->is_hoja && $edit)
     <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_importe">
       <div class="input_in_collapsable_lg input_actual">
         <input type='text' class="form-control currency pull-right precio_unitario_actual" id='precio_unitario_{{$item->id}}' name='precio_unitario[{{$item->id}}]'
           data-id="{{$item->id}}" value="@toDosDec($precio->precio)">
            <span class="input-group-addon">
              {{$item->itemizado->contrato_moneda->moneda->simbolo}}
            </span>
       </div>
     </a>
   @elseif($item->is_hoja)
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-width_importe" role="button" data-toggle="collapse"
      data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
       <div class="text-center">
         @toDosDec($precio->precio)
       </div>
     </a>
   @else
     <a class="btn_acordion datos_as_table collapse_arrow with-border dato-width_importe"
     @if(!$item->is_hoja) role="button" data-toggle="collapse"
      data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}" @endif>
       <div class="pull-right">
         {{-- @toDosDec($precio->precio) --}}
       </div>
     </a>
   @endif

  @if($analisis_item)
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($analisis_item != null)
        @toDosDec($analisis_item->costo_unitario_adaptado * $analisis_item->analisis_precios->coeficiente_k)
      @endif
   </div>
  </a>
  @endif

   <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
     <div class="">
       @if($precio != null)
         <span id="variacion_{{$item->id}}">
           @toCuatroDec($precio->variacion)
         </span>
       @endif
    </div>
  </a>
@endif
