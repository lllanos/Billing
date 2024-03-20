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

<div class="contenido_proceso_hist_redeterminaciones">
  <span class="contenido_proc_item error-redeterminacion">@trans('certificado.desvio'): {{$valueInstancia->solicitud->salto->desvio_acumulado}}</span>
</div>

@if($valueInstancia->instancia->aplicar_penalidad_desvio !== null)
  <div class="hist_instancia_icon">
    <i class="fa fa-user-circle" aria-hidden="true" title="@trans('index.user')"></i>
    <label>
      {{$valueInstancia->instancia->user_creator->nombre_apellido}}
    </label>
  </div>

  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">
      @if($valueInstancia->instancia->aplicar_penalidad_desvio)
        <i class="fa fa-check-circle text-success"></i>
      @else
        <i class="fa fa-times-circle text-danger"></i>
      @endif

      @trans('sol_redeterminaciones.penalidad')

      @if($valueInstancia->instancia->aplicar_penalidad_desvio)
        @trans('sol_redeterminaciones.aplicada')
      @else
        @trans('sol_redeterminaciones.no_aplicada')
      @endif
  	</span>
  </div>
@endif
