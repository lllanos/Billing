@if($header)
  <a class="btn_acordion datos_as_table collapse_arrow dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.codigo')
    </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('index.descripcion')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.unidad_medida')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.estado')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('analisis_item.precio_unitario')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('analisis_precios.costo_coeficiente_k')
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      <i class="glyphicon glyphicon-cog"></i>
    </div>
  </a>
@else
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="container_icon_angle">
      @if($item->is_nodo) <i class="fa fa-angle-down pl-{{$item->nivel - 1}}"></i>
      @else <i class="fa fa-angle-right pl-{{$item->nivel - 1}}"></i> @endif
      {{$item->codigo}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion width_descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->descripcion}}">
      {{$item->descripcion}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($analisis_item != null)
        {{$item->unidad_medida_o_alzado_nombre}}
      @endif
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($analisis_item != null)
        <span class="badge badge-referencias" style="background-color:#{{$analisis_item->estado['color']}};">
        {{ $analisis_item->estado['nombre_trans'] }}
      </span>
      @endif
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($analisis_item != null)
        @toDosDec($analisis_item->item->monto_unitario)
      @endif
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_a_pre_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_a_pre_{{$item->id}}">
    <div class="">
      @if($analisis_item != null)
        @toCuatroDec($analisis_item->costo_unitario_adaptado * $analisis_precios->coeficiente_k)
      @endif
   </div>
  </a>
@endif
