<div class="titulo__contenido">
  @trans('index.contrato') {{$contrato->numero_contrato}}
</div>
<div class="buttons-on-title">
  <div>
    @if(Auth::user()->puedeSolicitarRedeterminacion($user_contrato) ||
        ($contrato->permite_adendas) || ($contrato->permite_ampliaciones_de_obra) ||
        $contrato->permite_certificados
       )
      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
        <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
          <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu pull-right">
          @if(Auth::user()->puedeSolicitarRedeterminacion($user_contrato))
            <li>
              <a href="{{ route('solicitudes.redeterminaciones.solicitar', ['id' => $user_contrato->id]) }}">
                @trans('contratos.redeterminar')
              </a>
            </li>
          @endif
          @if($contrato->permite_adendas)
            <li>
              <a href="{{route('adenda.create', ['contrato_id' => $contrato->id])}}">
                @trans('index.solicitar') @trans('contratos.adenda')
              </a>
            </li>
          @endif
          @if($contrato->permite_ampliaciones_de_obra)
            <li>
              <a href="{{route('ampliacion.create', ['contrato_id' => $contrato->id])}}">
                @trans('index.solicitar') @trans('contratos.ampliacion_reprogramacion')
              </a>
            </li>
          @endif
          @if($contrato->permite_certificados)
            <li>
              <a href="{{route('certificado.create', ['contrato_id' => $contrato->id])}}">
                @trans('index.solicitar') @trans('contratos.certificado')
                @trans('index.mes') {{count($contrato->certificados()->whereRedeterminado(0)->get()) + 1}}
              </a>
            </li>
          @endif
        </ul>
      </div>
    @endif
  </div>
</div>
