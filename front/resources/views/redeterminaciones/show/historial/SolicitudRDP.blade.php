<div class="hist_instancia_icon">
	<i class="fa fa-calendar" aria-hidden="true"></i>
	<label>{{ $valueInstancia->inicio_fin}}</label> 
</div>
@if($valueInstancia->instancia->editada && !$valueInstancia->instancia->borrador)
<a class="contenido_proc_item" href={{route('solicitudes.descargarActaRDP', ['id'=> $redeterminacion->id, 'solicitud_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> {{trans('redeterminaciones.solicitud_rdp')}}</a>
@endif
