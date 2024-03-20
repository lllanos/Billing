@if($user_contrato->contrato->tiene_saltos_redeterminables)
  <h5>{!! trans('redeterminaciones.se_redeterminaran') !!}</h5>
  <ul>
    @foreach($user_contrato->contrato->obras as $keyObra => $valueObra)
      @foreach($valueObra->saltos_redeterminables as $keySalto => $valueSalto)
        <span class="badge badge-referencias" style="background-color:var(--green-redeterminacion-color);">
          {{$valueObra->nombre}} - {{$valueSalto->publicacion->mes_anio}}
        </span>
      @endforeach
    @endforeach
  </ul>
    <input id="btn_disabled" class="hidden" value="0">
@else
  <div class="no-data-no-padding text-center">
    <input id="btn_disabled" class="hidden" value="1">
    {!!trans('redeterminaciones.no_tiene_saltos') !!}
  </div>
@endif
