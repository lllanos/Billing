@if($header)

  @if(!isset($fromCronograma))
    <a class="btn_acordion datos_as_table collapse_arrow dato-codigo"
       role="button" data-toggle="collapse"
       data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.codigo')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-codigo"
       role="button" data-toggle="collapse"
       data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.item')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion"
       role="button"
       data-toggle="collapse"
       data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('index.descripcion')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_cantidad"
       role="button"
       data-toggle="collapse"
       data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.cantidad')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_denominacion"
       role="button"
       data-toggle="collapse"
       data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.denominacion')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-importe"
       role="button" data-toggle="collapse" data-parent="#accordion_sub_heading"
       href="#collapseOne_sub_heading"
       aria-expanded="true"
       aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.importe_unitario')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-importe"
      role="button" data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.importe_total')</div>
    </a>

    @if($itemizado->contrato_moneda->contrato && $itemizado->contrato_moneda->contrato->contratista)
      @if($itemizado->contrato_moneda->contrato->contratista->is_ute)
      <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion"
        role="button"
        data-toggle="collapse"
        data-parent="#accordion_sub_heading"
        href="#collapseOne_sub_heading"
        aria-expanded="true"
        aria-controls="collapseOne_sub_heading"
      >
        <div>@trans('forms.responsable')</div>
      </a>
      @endif
    @endif

    @if(
      // Es borrador
      $itemizado->borrador == 1
      // No es borrador pero tiene permisos de edición
      || (
        $itemizado->borrador == 0 && !isset($fromCronograma)
        && ($contrato->completo || ($itemizado != null && $itemizado->is_editable))
      )
    )
      <a class="btn_acordion datos_as_table collapse_arrow dato-opciones"
        role="button"
        data-toggle="collapse"
        data-parent="#accordion_sub_heading"
        href="#collapseOne_sub_heading"
        aria-expanded="true"
        aria-controls="collapseOne_sub_heading"
      >
        <div><i class="glyphicon glyphicon-cog"></i></div>
      </a>
    @endif
  @else
    <a class="btn_acordion datos_as_table collapse_arrow dato-codigo"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.codigo')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('index.descripcion')</div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div>@trans('forms.total')</div>
    </a>

    @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
      <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje"
         role="button"
         data-toggle="collapse"
         data-parent="#accordion_sub_heading"
         href="#collapseOne_sub_heading"
         aria-expanded="true"
         aria-controls="collapseOne_sub_heading"
      >
        <div>@trans('index.mes') {{$mes}}</div>
      </a>
    @endfor

    @if($itemizado->borrador == 1)
      <a class="btn_acordion datos_as_table collapse_arrow dato-opciones"
        role="button"
        data-toggle="collapse"
        data-parent="#accordion_sub_heading"
        href="#collapseOne_sub_heading"
        aria-expanded="true"
        aria-controls="collapseOne_sub_heading"
      >
        <div><i class="glyphicon glyphicon-cog"></i></div>
      </a>
    @endif
  @endif

@elseif($subheader)

  <a class="btn_acordion datos_as_table collapse_arrow dato-codigo"
    role="button"
    data-toggle="collapse"
    data-parent="#accordion_sub_heading"
    href="#collapseOne_sub_heading"
    aria-expanded="true"
    aria-controls="collapseOne_sub_heading"
  >
    <div>@trans('forms.total')</div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-descripcion width_descripcion"
    role="button"
    data-toggle="collapse"
    data-parent="#accordion_sub_heading"
    href="#collapseOne_sub_heading"
    aria-expanded="true"
    aria-controls="collapseOne_sub_heading"
  >
    <div>
      {{-- @trans('index.descripcion') --}}
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje"
    role="button"
    data-toggle="collapse"
    data-parent="#accordion_sub_heading"
    href="#collapseOne_sub_heading"
    aria-expanded="true"
    aria-controls="collapseOne_sub_heading"
  >
    <div>{{ $itemizado->valorItemizado($opciones['visualizacion']) }}</div>
  </a>

  @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
    <a class="btn_acordion datos_as_table collapse_arrow dato-importe-porcentaje"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div>
        {{ $itemizado->valorItemizado($opciones['visualizacion'], $mes) }}
     </div>
    </a>
  @endfor

  @if($itemizado->borrador == 1)
    <a class="btn_acordion datos_as_table collapse_arrow dato-opciones"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub_heading"
      href="#collapseOne_sub_heading"
      aria-expanded="true"
      aria-controls="collapseOne_sub_heading"
    >
      <div></div>
    </a>
  @endif

@else

  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
     role="button"
     data-toggle="collapse" data-parent="#accordion_sub{{$sufijo}}{{$item->id}}" href="#collapse_sub{{$sufijo}}{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub{{$sufijo}}{{$item->id}}">
    <div class="container_icon_angle">
      <i class="fa fa-angle-{{ $item->is_nodo ? 'down' : 'right' }} pl-{{$item->nivel - 1}}"></i>
      {{$item->codigo}}
   </div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
    role="button"
    data-toggle="collapse"
    data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
    href="#collapse_sub{{$sufijo}}{{$item->id}}"
    aria-expanded="true"
    aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
  >
      <div>{{ is_null($item->item) ? $item->codigo : $item->item }}</div>
  </a>

  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion"
    role="button"
    data-toggle="collapse"
    data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
    href="#collapse_sub{{$sufijo}}{{$item->id}}"
    aria-expanded="true"
    aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
  >
    <div class="responsable-overflow-hidden"
      data-toggle="tooltip"
      data-placement="bottom"
      title="{{$item->descripcion}}"
    >
      {{$item->descripcion}}
   </div>
  </a>

  @if(!isset($fromCronograma))
    <a class="btn_acordion datos_as_table width_cantidad collapse_arrow with-border dato-descripcion"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
      href="#collapse_sub{{$sufijo}}{{$item->id}}"
      aria-expanded="true"
      aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
    >
      <div class="text-right">
        @if ($item->is_hoja)
          @toDosDec($item->cantidad)
        @endif
     </div>
    </a>

    <a class="btn_acordion datos_as_table width_denominacion collapse_arrow with-border dato-descripcion"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
      href="#collapse_sub{{$sufijo}}{{$item->id}}"
      aria-expanded="true"
      aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
    >
      <div>
        @if ($item->is_hoja)
          @if($item->is_unidad_medida)
            {{$item->unidad_medida_nombre}}
          @else
            @trans('forms.ajuste_alzado')
          @endif
        @endif
     </div>
    </a>

    <a class="btn_acordion dos_datos collapse_arrow with-border dato-importe"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
      href="#collapse_sub{{$sufijo}}{{$item->id}}"
      aria-expanded="true"
      aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
    >
      <div class="number-format">
        @if ($item->monto_unitario > 0)
          @toDosDec($item->monto_unitario)
        @endif
     </div>
    </a>

    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
      href="#collapse_sub{{$sufijo}}{{$item->id}}"
      aria-expanded="true"
      aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
    >
      <div>{{$item->subtotal_dos_dec}}</div>
    </a>

    @if($itemizado->contrato_moneda->contrato && $itemizado->contrato_moneda->contrato->contratista)
      @if($itemizado->contrato_moneda->contrato->contratista->is_ute)
        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion"
          role="button"
          data-toggle="collapse"
          data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
          href="#collapse_sub{{$sufijo}}{{$item->id}}"
          aria-expanded="true"
          aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
        >
          <div class="text-right responsable-overflow-hidden">
            @if ($item->responsable)
              <span data-toggle="tooltip"
                data-placement="bottom"
                title="{{$item->responsable->fantasia_razon_social}}"
              >
                {{$item->responsable->fantasia_razon_social}}
              </span>
            @endif
         </div>
        </a>
      @endif
    @endif

  @else
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje"
      role="button"
      data-toggle="collapse"
      data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
      href="#collapse_sub{{$sufijo}}{{$item->id}}"
      aria-expanded="true"
      aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
    >
      <div>{{ $itemizado->valorItem($item->id, $opciones['visualizacion']) }}</div>
    </a>

    @for($mes = 1; $mes <= $itemizado->meses ; $mes++)
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje"
        role="button"
        data-toggle="collapse"
        data-parent="#accordion_sub{{$sufijo}}{{$item->id}}"
        href="#collapse_sub{{$sufijo}}{{$item->id}}"
        aria-expanded="true"
        aria-controls="collapse_sub{{$sufijo}}{{$item->id}}"
      >
        <div>
          {{ $itemizado->valorItem($item->id, $opciones['visualizacion'], $mes) }}
       </div>
      </a>
    @endfor
  @endif

@endif
