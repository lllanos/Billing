@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i> 
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
@if(sizeof($redeterminacion->certificados_rdp))
  @foreach($redeterminacion->certificados_rdp as $keyCert => $valueCert)
  	<div class="contenido_proceso_hist_redeterminaciones">
		  <a class="contenido_proc_item"
      href="{{route('descargar', ['nombre' => $valueCert->adjunto_nombre, 'url' => $valueCert->adjunto_link])}}">
      <i class="fa fa-paperclip grayCircle"></i> {{ trans('forms.certificado')}} {{$keyCert + 1}}</a>
  	</div>
  @endforeach
@endif
