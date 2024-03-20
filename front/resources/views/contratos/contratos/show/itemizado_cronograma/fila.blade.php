@if($header)
  @if(!isset($fromCronograma))
    <a class="btn_acordion datos_as_table collapse_arrow dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        {{trans('forms.codigo')}}
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion width_descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        @trans('index.descripcion')
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_cantidad" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        @trans('forms.cantidad')
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_denominacion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        @trans('forms.denominacion')
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        @trans('forms.importe_unitario')
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        @trans('forms.importe_total')
      </div>
    </a>
    @if ($itemizado->contrato_moneda->contrato->contratista->is_ute)
        <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
          <div class="">
            @trans('forms.responsable')
         </div>
        </a>
    @endif
    @if($itemizado->borrador == 1)
      <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
        <div class="">
          <i class="glyphicon glyphicon-cog"></i>
        </div>
      </a>
    @endif
    <!--...-->
    @if($itemizado->borrador == 0 && $contrato->completo && !isset($fromCronograma))
      @if($itemizado != null && $itemizado->is_editable)
        <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
          <div class="">
            <i class="glyphicon glyphicon-cog"></i>
          </div>
        </a>
      @endif
    @endif

  @else
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
        @trans('forms.total')
     </div>
    </a>
    @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
      <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
        <div class="">
          @trans('index.mes') {{$mes}}
       </div>
      </a>
    @endfor

    @if($itemizado->borrador == 1)
      <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
        <div class="">
          <i class="glyphicon glyphicon-cog"></i>
        </div>
      </a>
    @endif
  @endif
{{-- $header == false --}}
@elseif($subheader)
  <a class="btn_acordion datos_as_table collapse_arrow dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      @trans('forms.total')
    </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      {{-- @trans('index.descripcion') --}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
      {{ $itemizado->valorItemizado($opciones['visualizacion']) }}
   </div>
  </a>
  @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
    <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
        {{ $itemizado->valorItemizado($opciones['visualizacion'], $mes) }}
     </div>
    </a>
  @endfor

  @if($itemizado->borrador == 1)
    <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
      <div class="">
      </div>
    </a>
  @endif
@else
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
    <div class="container_icon_angle">
      @if($item->is_nodo) <i class="fa fa-angle-down pl-{{$item->nivel - 1}}"></i>
      @else <i class="fa fa-angle-right pl-{{$item->nivel - 1}}"></i> @endif
      {{$item->codigo}}
   </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion width_descripcion width_descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
    <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->descripcion}}">
      {{$item->descripcion}}
   </div>
  </a>
  @if(!isset($fromCronograma))
    <a class="btn_acordion datos_as_table width_cantidad collapse_arrow with-border dato-descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
      <div class="text-right">
        @if ($item->is_hoja)
            @if ($item->cantidad > 0)
              @toDosDec($item->cantidad)
            @else
              1
            @endif
        @endif
     </div>
    </a>
    <a class="btn_acordion datos_as_table width_denominacion collapse_arrow with-border dato-descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
      <div class="">
        @if ($item->is_hoja)
            @if ($item->cantidad > 0)
              {{$item->unidad_medida_nombre}}
            @else
              @trans('forms.ajuste_alzado')
            @endif
        @endif
     </div>
    </a>
    <a class="btn_acordion dos_datos collapse_arrow with-border dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
      <div class="number-format">
        @if ($item->monto_unitario > 0)
          @toDosDec($item->monto_unitario)
        @endif
     </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
      <div class="">
        {{$item->subtotal_dos_dec}}
     </div>
    </a>
    @if ($itemizado->contrato_moneda->contrato->contratista->is_ute)
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
        <div class="text-right responsable-overflow-hidden">
          @if ($item->responsable)
            <span data-toggle="tooltip" data-placement="bottom" title="{{$item->responsable->fantasia_razon_social}}">
              {{$item->responsable->fantasia_razon_social}}
            </span>
          @endif
       </div>
      </a>
    @endif
  @else
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
      <div class="">
        {{ $itemizado->valorItem($item->id, $opciones['visualizacion']) }}
     </div>
    </a>
    @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button" data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
        <div class="">
          {{ $itemizado->valorItem($item->id, $opciones['visualizacion'], $mes) }}
       </div>
      </a>
    @endfor
  @endif
@endif
