<div class="hist_instancia_icon">
  <i class="fa fa-calendar" aria-hidden="true"></i>
  <label>{{ $valueInstancia->created_at}}</label>
</div>
@if($valueInstancia->instancia->poliza_caucion_id != null)
  <div class="contenido_proceso_hist_redeterminaciones">
  	<a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->poliza_nombre, 'url' => $valueInstancia->instancia->poliza_link])}}">
      <i class="fa fa-paperclip grayCircle"></i>
      {{ trans('forms.poliza')}} {{$valueInstancia->instancia->poliza_caucion->descripcion}}
      @if($valueInstancia->instancia->poliza_valida)
        <i class="fa fa-check-circle text-success"></i>
      @else
        <i class="fa fa-times-circle text-danger"></i>
      @endif
    </a>
  </div>
@endif
