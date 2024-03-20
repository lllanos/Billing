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

@if($valueInstancia->instancia->nro_resolucion !== null)
	<div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item">@trans('sol_redeterminaciones.nro_resolucion'): {{$valueInstancia->instancia->nro_resolucion}}</span>
    @if($valueInstancia->instancia->acta_firmada != null)
      <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->fileNombre('acta_firmada'), 'url' => $valueInstancia->instancia->fileLink('acta_firmada')])}}">
        <i class="fa fa-paperclip grayCircle"></i>
        @trans('sol_redeterminaciones.acta') @trans('sol_redeterminaciones.firmada')</a>
    @endif
    @if($valueInstancia->instancia->resolucion_firmada != null)
      <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->fileNombre('resolucion_firmada'), 'url' => $valueInstancia->instancia->fileLink('resolucion_firmada')])}}">
        <i class="fa fa-paperclip grayCircle"></i>
        @trans('sol_redeterminaciones.resolucion') @trans('sol_redeterminaciones.firmada')</a>
    @endif
	</div>
@endif
