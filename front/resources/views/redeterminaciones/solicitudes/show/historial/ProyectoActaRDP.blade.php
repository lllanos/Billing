@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif

@if(!$valueInstancia->instancia->borrador)
  <a class="contenido_proc_item" href={{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' => 'acta', 'acta_id' => $valueInstancia->instancia->id])}}><i class="fa fa-paperclip grayCircle"></i> @trans('sol_redeterminaciones.acta')</a>
@endif
