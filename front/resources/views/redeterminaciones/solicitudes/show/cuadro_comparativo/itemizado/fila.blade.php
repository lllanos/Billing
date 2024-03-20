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
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_cantidad" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.cantidad')
   </div>
  </a>
  {{-- <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_denominacion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.denominacion')
   </div>
  </a> --}}

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="pull-right">
      @trans('redeterminaciones.cuadro_comparativo.precio_unitario', ['mes' => $cuadro_comparativo->publicacion_anterior->mes_anio])
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('redeterminaciones.cuadro_comparativo.precio_unitario_redet', ['mes' => $cuadro_comparativo->publicacion_actual->mes_anio])
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('redeterminaciones.cuadro_comparativo.medicion_a_cert', ['mes' => $cuadro_comparativo->publicacion_actual->mes_anio])
        @if($cuadro_comparativo->aplicar_penalidad_45_dias || $cuadro_comparativo->aplicar_penalidad_desvio)
          <i class="fa fa-exclamation-triangle" style="color:var(--yellow-desvio-color)"
          @if($cuadro_comparativo->aplicar_penalidad_desvio)
            data-toggle="tooltip" data-html="true" data-placement="top" title="@trans('redeterminaciones.cuadro_comparativo.mensajes.aplicar_penalidad_desvio')"
          @elseif($cuadro_comparativo->aplicar_penalidad_45_dias)
            data-toggle="tooltip" data-html="true" data-placement="top" title="@trans('redeterminaciones.cuadro_comparativo.mensajes.aplicar_penalidad_45_dias', ['mes_anterior' => $cuadro_comparativo->solicitud->fecha_certificado_anterior])"
          @endif
          ></i>
        @endif
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('redeterminaciones.cuadro_comparativo.incremento')
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      @trans('redeterminaciones.cuadro_comparativo.total_redeterminado_mas_inc')
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapse_sub_heading" aria-expanded="true" aria-controls="collapse_sub_heading">
    <div class="">
      <i class="glyphicon glyphicon-cog"></i>
    </div>
  </a>
@else
  @if($item->is_hoja) @php($is_hoja = true) @else @php($is_hoja = false) @endif
  @if($is_hoja) @php ($item_cuadro_comparativo = $cuadro_comparativo->getItemCuadroComparativo($item)) @endif
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
  @if(!$is_hoja)role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="container_icon_angle">
      @if($item->is_nodo) <i class="fa fa-angle-down pl-{{$item->nivel - 1}}"></i>
      @else <i class="fa fa-angle-right pl-{{$item->nivel - 1}}"></i> @endif
      {{$item->codigo}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion with-border"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->descripcion}}">
      {{$item->descripcion}}
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_cantidad with-border text-right"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="">
      @if($is_hoja) @toDosDec($item->cantidad) @endif
   </div>
  </a>

  {{-- <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_denominacion with-border"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="">
      @if($is_hoja) {{$item->unidad_medida_o_alzado_nombre}} @endif
   </div>
  </a> --}}

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="pull-right">
      @if($is_hoja) @toDosDec($item_cuadro_comparativo->precio_anterior) @endif
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="pull-right">
      @if($is_hoja) @toDosDec($item_cuadro_comparativo->precio_unitario_redet) @endif
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="pull-right">
      @if($is_hoja) @toDosDec($item_cuadro_comparativo->medicion_utilizada) {{$item->porc_unidad_medida}}  @endif
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje"
  @if(!$is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="pull-right">
      @if($is_hoja) @toDosDec($item_cuadro_comparativo->incremento) @endif
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button"
  @if(!$is_hoja) data-toggle="collapse" data-parent="#accordion_sub_a_pre_{{$item->id}}" href="#collapse_sub_cuad_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_cuad_{{$item->id}}" @endif>
    <div class="pull-right">
      @if($is_hoja) @toDosDec($item_cuadro_comparativo->total_redeterminado) @endif
   </div>
  </a>
@endif
