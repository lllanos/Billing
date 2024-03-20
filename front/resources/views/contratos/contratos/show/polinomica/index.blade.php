
<div class="panel-group acordion" id="accordion-polinomica" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-polinomica">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-polinomica" href="#collapseOne_polinomica" aria-expanded="true" aria-controls="collapseOne_polinomica"
        @if(!isset($fromAjax)) data-seccion="polinomica" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.redeterminaciones')</div>
        </a>
        <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li><a data-url="{{ route('contrato.historial', ['clase_id' => $contrato->id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => 'polinomica']) }}" class="open-historial historial-polinomica"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a></li>
          </ul>
        </div>
      </h4>
    </div>
    <div id="collapseOne_polinomica" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-polinomica">
      @if(isset($fromAjax))

        <div class="panel-body pb-0">
          @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
            <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
              @include('contratos.contratos.show.polinomica.polinomica')
            </div>
          @endforeach
        </div>

      @endif
    </div>
  </div>
</div>
