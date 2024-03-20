<div class="hist_instancia_icon">
  <i class="fa fa-calendar" aria-hidden="true"></i>
  <label>{{ $valueInstancia->created_at}}</label>
</div>
<div class="hist_instancia_icon">
  <i class="fa fa-user-circle" aria-hidden="true"></i>
  <label>
    {{ $valueInstancia->user->nombre_apellido}}
  </label>
</div>

@if($valueInstancia->motivo_rechazo != null)
  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">@trans('forms.motivo'): {{ $valueInstancia->motivo_rechazo }}</span>
  </div>
@endif

@if($valueInstancia->gde != null)
  <div class="contenido_proceso_hist_redeterminaciones">
  	<span class="contenido_proc_item">@trans('forms.nro_gde'): {{ $valueInstancia->gde }}</span>
  </div>
@endif
