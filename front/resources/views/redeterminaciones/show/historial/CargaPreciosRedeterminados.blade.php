@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i> 
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
@if($valueInstancia->instancia->nro_sigo != null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item">{{trans('forms.monto_vigente')}}: {{ $valueInstancia->instancia->monto_vigente }}</span>
  	<span class="contenido_proc_item">{{trans('forms.mayor_gasto_no_red')}}: {{ $valueInstancia->instancia->mayor_gasto_no_red }}</span>
    <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $valueInstancia->instancia->cuadro_comparativo_nombre, 'url' => $valueInstancia->instancia->cuadro_comparativo_link])}}">
      <i class="fa fa-paperclip grayCircle"></i> {{trans('forms.cuadro_comparativo')}}</a>
  </div>
@endif
