@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif

<div class="hist_instancia_icon">
  <i class="fa fa-clock-o" aria-hidden="true"></i>
  <label>{{$valueInstancia->tipo_instancia->plazo}} @choice('index.dias', $valueInstancia->tipo_instancia->plazo)</label>
</div>

@if($valueInstancia->instancia->monto_vigente != null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item"><b>@trans('sol_redeterminaciones.datos_a_mes', ['mes' => $valueInstancia->solicitud->salto->publicacion->mes_anio])</b></span>

    <span class="contenido_proc_item">@trans('sol_redeterminaciones.monto_vigente'): @toDosDec($valueInstancia->instancia->monto_vigente)</span>
    <span class="contenido_proc_item">@trans('sol_redeterminaciones.mayor_gasto'): @toDosDec($valueInstancia->instancia->mayor_gasto)</span>
    <span class="contenido_proc_item">@trans('sol_redeterminaciones.saldo'): @toDosDec($valueInstancia->instancia->saldo)</span>

    @if($valueInstancia->solicitud->cuadro_comparativo != null)
      <a class="contenido_proc_item" href="{{route('cuadroComparativo.ver', ['id' => $valueInstancia->solicitud->cuadro_comparativo->id])}}">
      @trans('sol_redeterminaciones.cuadro_comparativo')</a>
    @endif
  </div>
@elseif(!$valueInstancia->solicitud->a_termino && !$valueInstancia->solicitud->tiene_certificado_anterior)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item error-redeterminacion">@trans('sol_redeterminaciones.falta_certificado_anterior')</span>
  </div>
@endif
