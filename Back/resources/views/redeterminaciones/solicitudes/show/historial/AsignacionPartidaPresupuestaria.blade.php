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

@if($valueInstancia->instancia->nro_partida_presupuestaria != null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item">{{trans('sol_redeterminaciones.nro_partida_presupuestaria')}}: {{ $valueInstancia->instancia->nro_partida_presupuestaria }}</span>
  </div>
@endif

@if($valueInstancia->instancia->adjunto != null)
	<div class="contenido_proceso_hist_redeterminaciones">
      <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->adjunto_nombre, 'url' => $valueInstancia->instancia->adjunto_link])}}">
        <i class="fa fa-paperclip grayCircle"></i>
        {{ trans('sol_redeterminaciones.partida_presupuestaria')}}</a>
	</div>
@endif
