<div class="hist_instancia_icon">
	<i class="fa fa-calendar" aria-hidden="true"></i> 
	<label>{{ $valueInstancia->inicio_fin}}</label>
</div>

@if($valueInstancia->instancia->nro_expediente != null)
  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">{{trans('sol_redeterminaciones.nro_expediente')}}: {{ $valueInstancia->instancia->nro_expediente }}</span>
  </div>
@endif