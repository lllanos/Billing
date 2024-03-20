@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif


@if($valueInstancia->instancia->usuario_firma_id != null)
  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">{{trans('redeterminaciones.usuario_que_firmo')}}: {{ $valueInstancia->instancia->usuario_firma->nombre_apellido }}</span>
  </div>
@endif

@if($valueInstancia->instancia->fc_gedo_acta != null)
	<div class="contenido_proceso_hist_redeterminaciones">
    @if($valueInstancia->instancia->poliza_caucion_id != null)
      @if($valueInstancia->instancia->fc_poliza != null)
        <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->fc_poliza_nombre, 'url' => $valueInstancia->instancia->fc_poliza_link])}}">
      @else
        <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->poliza_caucion->adjunto_nombre, 'url' => $valueInstancia->instancia->poliza_caucion->adjunto_link])}}">
      @endif
        <i class="fa fa-paperclip grayCircle"></i> {{trans('forms.poliza')}} {{$valueInstancia->instancia->poliza_caucion->descripcion}}</a>
    @endif

    @if($valueInstancia->instancia->fc_solicitud_firmada != null)
		  <a class="contenido_proc_item"
        href="{{route('descargar', ['nombre' => $valueInstancia->instancia->fc_solicitud_firmada_nombre, 'url' => $valueInstancia->instancia->fc_solicitud_firmada_link])}}">
        <i class="fa fa-paperclip grayCircle"></i>
        {{trans('forms.solicitud_rdp_firmada')}}</a>
    @endif

    @if($valueInstancia->instancia->fc_acta_firmada != null)
		  <a class="contenido_proc_item"
        href="{{route('descargar', ['nombre' => $valueInstancia->instancia->fc_acta_firmada_nombre, 'url' => $valueInstancia->instancia->fc_acta_firmada_link])}}">
        <i class="fa fa-paperclip grayCircle"></i>
        {{trans('forms.acta_rdp_firmada')}}</a>
    @endif
	</div>
@endif
