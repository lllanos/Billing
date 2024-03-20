@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
<div class="hist_instancia_icon">
  <i class="fa fa-clock-o" aria-hidden="true"></i>
  <label>{{$valueInstancia->tipo_instancia->plazo}} @choice('index.dias', $valueInstancia->tipo_instancia->plazo)</label>
</div>

@if($valueInstancia->instancia->editada && !$valueInstancia->instancia->borrador)
  <div class="hist_instancia_icon">
    <i class="fa fa-user-circle" aria-hidden="true" title="@trans('index.user')"></i>
    <label>{{$valueInstancia->instancia->user_creator->nombre_apellido}}</label>
  </div>
@endif

@if($valueInstancia->instancia->editada)
  <a class="contenido_proc_item" href={{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' => 'acta', 'acta_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> @trans('sol_redeterminaciones.acta')</a>
@endif

@if($valueInstancia->instancia->editada)
  <a class="contenido_proc_item" href={{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' => 'resolucion', 'acta_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> @trans('sol_redeterminaciones.resolucion')</a>
@endif

@if($valueInstancia->instancia->editada)
  <a class="contenido_proc_item" href={{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' => 'informe', 'acta_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> @trans('sol_redeterminaciones.informe')</a>
@endif
