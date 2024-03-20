@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif

@if($valueInstancia->instancia->certificados_aprobados !== null)

  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">
      @if($valueInstancia->instancia->certificados_aprobados)
        <i class="fa fa-check-circle text-success"></i>
      @else
        <i class="fa fa-times-circle text-danger"></i>
      @endif

      @trans('sol_redeterminaciones.certificados')

      @if($valueInstancia->instancia->certificados_aprobados)
        @trans('sol_redeterminaciones.aprobados')
      @else
        @trans('sol_redeterminaciones.no_aprobados')
      @endif
  	</span>

    @if($valueInstancia->solicitud->certificado != null)
      <a class="contenido_proc_item" href="{{route('certificado.ver', ['id' => $valueInstancia->solicitud->certificado->id])}}">
      @trans('sol_redeterminaciones.ver_certificado')</a>
    @endif
  </div>
@elseif($valueInstancia->solicitud->certificado == null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item error-redeterminacion">@trans('sol_redeterminaciones.falta_certificado_de', ['mes' => $valueInstancia->solicitud->salto->publicacion->mes_anio_anterior])</span>
  </div>
@endif
