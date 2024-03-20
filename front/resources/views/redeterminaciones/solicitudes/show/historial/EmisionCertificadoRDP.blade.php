@if($valueInstancia->fecha_inicio != null && $valueInstancia->inicio_fin != null)
  <div class="hist_instancia_icon">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <label>{{ $valueInstancia->inicio_fin}}</label>
  </div>
@endif
@if($valueInstancia->instancia->certificados_emitidos !== null)
  @if(!count($solicitud->redeterminacion->certificados) > 0)
  @else
    <div class="hist_instancia_icon">
      <i class="fa fa-clock-o" aria-hidden="true"></i>
      <label>{{$valueInstancia->tipo_instancia->plazo}} @choice('index.dias', $valueInstancia->tipo_instancia->plazo)</label>
    </div>
  @endif
@endif
@if($solicitud->redeterminacion)
  @if(!count($solicitud->redeterminacion->certificados) > 0)
    <div class="hist_instancia_icon">
      <i class="fa fa-times-circle text-danger" aria-hidden="true"></i>
      <label>
        @trans('sol_redeterminaciones.sin_certificados')
      </label>
    </div>
  @endif
@endif
@if($valueInstancia->instancia->certificados_emitidos !== null)
  <div class="contenido_proceso_hist_redeterminaciones">
    <span class="contenido_proc_item">
      @if($valueInstancia->instancia->certificados_emitidos)
        @foreach($solicitud->redeterminacion->certificados as $keyCert => $valueCert)
        	<div class="contenido_proceso_hist_redeterminaciones">
            <a class="contenido_proc_item" href="{{route('redeterminaciones.certificado.ver', ['id' => $valueCert->id]) }}">
              @trans('index.mes') {{$valueCert->mes}} - {{$valueCert->mesAnio('fecha', 'Y-m-d')}}
            </a>
        	</div>
        @endforeach
      @endif
    </span>
  </div>
@endif
