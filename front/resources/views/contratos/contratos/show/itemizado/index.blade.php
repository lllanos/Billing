<input type="hidden" id="itemizado_version" value="{{$opciones['version']}}" />
<input type="hidden" id="itemizado_visualizacion" value="{{$opciones['visualizacion']}}" />

  <div class="panel-group acordion" id="accordion-itemizado" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-itemizado">
        <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
          <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-itemizado" href="#collapseOne_itemizado" aria-expanded="true" aria-controls="collapseOne_itemizado"
           @if(!isset($fromAjax)) data-seccion="itemizado" data-version="{{$opciones['version']}}" @endif>
            <div class="container_icon_angle"><i class="fa fa-angle-down"></i> {{trans('contratos.itemizado')}}</div>
          </a>
          <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu pull-right">
              <li><a data-url="{{ route('contrato.historial', ['clase_id' => $contrato->id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => 'itemizado']) }}" class="open-historial historial-itemizado"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a></li>
              <li>
                <form class="form_excel" method="POST" data-action="{{route('export.itemizado')}}" id="form_excel">
                  {{ csrf_field() }}
                  <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input"
                  value="{{$contrato->id}}">
                  <input type="hidden" class="excel-search-input form-control" name="version" id="version" value="vigente">

                  <button type="submit" id="excel_button" class="button_link" title="@trans('index.descargar_a_excel')">
                  <i class="fa fa-file-excel-o" aria-hidden="true"></i> @trans('index.descargar_a_excel')</button>
                </form>
              </li>
            </ul>
          </div>
        </h4>
      </div>
      <div id="collapseOne_itemizado" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-itemizado">
        @if(isset($fromAjax))
          <div class="row">
            <div class="col-md-12">
              <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-itemizado">
                <ul> </ul>
              </div>
            </div>
          </div>

          @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
            @if(isset($valueContratoMoneda->itemizado))
              @php ($itemizado = $valueContratoMoneda->itemizado)
              @php ($keyItemizado = $itemizado->id)
              @php ($sufijo = '_it_')
            @endif
            <div class="panel-body pt-0 pb-0">
              @include('contratos.contratos.show.itemizado.itemizado')
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
