@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i> 
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
@if($valueInstancia->instancia->editada && !$valueInstancia->instancia->borrador)
  <span>
    <a class="contenido_proc_item" href={{route('solicitudes.descargarActa', ['id'=> $redeterminacion->id, 'acta_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> {{trans('forms.acta')}}</a>
  </span>
@endif
