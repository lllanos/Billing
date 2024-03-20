@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i> 
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
@if($valueInstancia->instancia->doc_resolucion_id != null)
	<div class="contenido_proceso_hist_redeterminaciones">
    @if($valueInstancia->instancia->doc_resolucion_id != null)
		  <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->doc_resolucion->adjunto_nombre, 'url' => $valueInstancia->instancia->doc_resolucion->adjunto_link])}}">
        <i class="fa fa-paperclip grayCircle"></i> {{ trans('forms.resolucion_disposicion')}}</a>
    @endif

    @if($valueInstancia->instancia->acto_acta_adjunta != null)
		  <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->acto_acta_adjunta_nombre, 'url' => $valueInstancia->instancia->acto_acta_adjunta_link])}}">
        <i class="fa fa-paperclip grayCircle"></i> {{ trans('forms.acta')}}</a>
    @endif
	</div>
@endif
