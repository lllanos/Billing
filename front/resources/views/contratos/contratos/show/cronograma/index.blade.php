<input type="hidden" id="cronograma_version" value="{{$opciones['version']}}" />
<input type="hidden" id="cronograma_visualizacion" value="{{$opciones['visualizacion']}}" />

<div class="panel-group acordion" id="accordion-cronograma" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-cronograma">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-cronograma" href="#collapseOne_cronograma" aria-expanded="true" aria-controls="collapseOne_cronograma"
        @if(!isset($fromAjax)) data-seccion="cronograma" data-version="{{$opciones['version']}}" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('contratos.cronograma')</div>

          <div class="container_icon_angle">

              <div class="container_btn_action">
                <span class="badge badge-referencias" style="background-color:var(--poncho-light-blue);">
                  @if($opciones['visualizacion'] == 'porcentaje')
                    <i class="fa fa-percent" aria-hidden="true"></i>
                    @trans('cronograma.vista.tag.porcentaje')
                  @elseif($opciones['visualizacion'] == 'moneda')
                    <i class="fa fa-usd" aria-hidden="true"></i>
                    @trans('cronograma.vista.tag.moneda')
                  @elseif($opciones['visualizacion'] == 'all')
                    <i class="rob rob-ruler"></i>
                    @trans('cronograma.vista.tag.all')
                  @elseif($opciones['visualizacion'] == 'curva_inversion')
                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                    @trans('cronograma.vista.tag.curva_inversion')
                  @endif
                </span>
              </div>
          </div>
        </a>
        <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li><a data-url="{{ route('contrato.historial', ['clase_id' => $contrato->id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => 'cronograma']) }}" class="open-historial historial-cronograma"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a></li>
            @if($contrato->completo || ($contrato_incompleto['status'] && !$contrato_incompleto['cronograma'] && !$contrato_incompleto['sin_fecha_inicio']))
              @if($opciones['visualizacion'] != 'moneda')
                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="moneda"><a class="mouse-pointer"><i class="fa fa-usd" aria-hidden="true"></i> @trans('cronograma.vista.nombre.moneda')</a></li>
              @endif
              @if($opciones['visualizacion'] != 'porcentaje')
                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="porcentaje"><a class="mouse-pointer"><i class="fa fa-percent" aria-hidden="true"></i> @trans('cronograma.vista.nombre.porcentaje')</a></li>
              @endif
              @if($opciones['visualizacion'] != 'all')
                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="all"><a class="mouse-pointer"><i class="rob rob-ruler"></i> @trans('cronograma.vista.nombre.all')</a></li>
              @endif
              @if($opciones['visualizacion'] != 'curva_inversion')
                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="curva_inversion"><a class="mouse-pointer"><i class="fa fa-line-chart" aria-hidden="true"></i> @trans('cronograma.vista.nombre.curva_inversion')</a></li>
              @endif
            @endif
            @if($contrato->completo)
                @if($opciones['visualizacion'] != 'curva_inversion')
                  <li>
                    <form class="form_excel_2" method="POST" data-action="{{route('export.cronograma')}}" id="form_excel_2">
                      {{ csrf_field() }}
                      <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input"
                      value="{{$contrato->id}}">
                      <input type="hidden" class="excel-search-input form-control" name="visualizacion" id="visualizacion"
                      @if($opciones['visualizacion'] == 'porcentaje')
                         value="porcentaje"
                      @elseif($opciones['visualizacion'] == 'moneda')
                         value="moneda"
                      @elseif($opciones['visualizacion'] == 'all')
                         value="all"
                      @endif
                      >
                      <input type="hidden" class="excel-search-input form-control" name="version" id="version" value="vigente">
                       <button type="submit" id="excel_button" class="button_link width100" title="@trans('index.descargar_a_excel')">
                       <i class="fa fa-file-excel-o" aria-hidden="true"></i> @trans('index.descargar_a_excel')</button>
                    </form>
                  </li>
                @endif
            @endif
          </ul>
        </div>
      </h4>
    </div>

    <div id="collapseOne_cronograma" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-cronograma">
      @if(isset($fromAjax))
        @if($opciones['visualizacion'] == 'curva_inversion')
          <div class="panel panel-default">
            <div class="panel-body">
              <div class='block-modal block-curva_inversion'></div>
              <div class="content-curva_inversion"></div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-12">
              <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-cronograma">
                <ul> </ul>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              @if($opciones['version'] == 'vigente' && $contrato->has_cronograma_vigente)
                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                  <div class="panel-body pt-0 pb-0">
                    @php ($cronograma = $valueContratoMoneda->cronograma_vigente)
                    @include('contratos.contratos.show.cronograma.cronograma')
                  </div>
                @endforeach
              @else
                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                  <div class="panel-body pt-0 pb-0">
                    @php ($cronograma = $valueContratoMoneda->cronograma)
                    @include('contratos.contratos.show.cronograma.cronograma')
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        @endif
      @endif
    </div>
  </div>
</div>
