<input type="hidden" id="empalme_version" value="{{$opciones['version']}}" />
<input type="hidden" id="empalme_visualizacion" value="{{$opciones['visualizacion']}}" />

<div class="panel-group acordion" id="accordion-empalme" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-empalme">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-empalme" href="#collapseOne_empalme" aria-expanded="true" aria-controls="collapseOne_empalme"
        @if(!isset($fromAjax)) data-seccion="empalme" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('contratos.empalme')</div>
        </a>
        <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li><a data-url="{{ route('contrato.historial', ['clase_id' => $contrato->id, 'clase_type' => $contrato->getShortClassName(), 'seccion' => 'empalme']) }}" class="open-historial historial-empalme"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a></li>
          </ul>
        </div>
      </h4>
    </div>

    <div id="collapseOne_empalme" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-empalme">
      @if(isset($fromAjax))
        <div class="row">
          <div class="col-md-12">
            <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-empalme">
              <ul> </ul>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
          </div>
        </div>

      @endif
    </div>
  </div>
</div>
