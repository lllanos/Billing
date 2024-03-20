<div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>  
    <label>{{ $valueInstancia->created_at}}</label>
</div>
@if($valueInstancia->instancia->poliza_caucion_id != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-user-circle" aria-hidden="true"></i>
    <label>
      {{$valueInstancia->instancia->user_creator->nombre_apellido}}
    </label>
  </div>
@endif
@if($valueInstancia->instancia->poliza_caucion_id != null)
  @if($valueInstancia->instancia->poliza_caucion_id != null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item">
      {{trans('redeterminaciones.nro_poliza')}}: {{$valueInstancia->instancia->poliza_caucion->descripcion}}
    </span>
  </div>
  @endif
  <div class="contenido_proceso_hist_redeterminaciones">
  	<a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->poliza_nombre, 'url' => $valueInstancia->instancia->poliza_link])}}">
      <i class="fa fa-paperclip grayCircle"></i>
      {{ trans('forms.poliza')}}</a>
  </div>
@endif
