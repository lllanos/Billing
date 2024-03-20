@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif

@if($valueInstancia->instancia->aplicar_penalidad_desvio !== null)

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
