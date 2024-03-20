@if($contrato->tiene_saltos_redeterminables)
  <h5>@trans('sol_redeterminaciones.se_redeterminaran')</h5>
  <ul>
    @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
      @foreach($valueContratoMoneda->saltos_redeterminables as $keySalto => $valueSalto)
        <span class="badge badge-referencias" style="background-color:var(--green-redeterminacion-color);">
          {{$valueContratoMoneda->moneda->nombre}} - {{$valueSalto->publicacion->mes_anio}}
        </span>
      @endforeach
    @endforeach
  </ul>
    <input id="btn_disabled" class="hidden" value="0">
@else
  <div class="no-data-no-padding text-center">
    <input id="btn_disabled" class="hidden" value="1">
    @trans('sol_redeterminaciones.no_tiene_saltos')
  </div>
@endif
