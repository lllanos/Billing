{{-- Si no esta en borrador tiene itemizado --}}

<input type="hidden" id="itemizado_version" value="{{$opciones['version']}}"/>
<input type="hidden" id="itemizado_visualizacion" value="{{$opciones['visualizacion']}}"/>

<div class="panel-group acordion" id="accordion-itemizado" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-itemizado">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-itemizado" href="#collapseOne_itemizado" aria-expanded="true" aria-controls="collapseOne_itemizado"
          @if(!isset($fromAjax)) data-seccion="itemizado" data-version="{{$opciones['version']}}" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('contratos.itemizado')</div>

          <div class="container_icon_angle">
            @if($contratoIncompleto['status'])
              @if($contratoIncompleto['itemizado'])
                <div class="container_btn_action">
                    <span class="badge badge-referencias badge-borrador">
                      <i class="fa fa-eraser"></i> @trans('index.borrador')
                    </span>
                </div>
              @elseif(!empty($contratoIncompleto['doble_firma']['itemizado']))
                <div class="container_btn_action">
                  <span class="badge badge-referencias badge-borrador">
                    <i class="fa fa-pencil"></i>
                      @if(count($contratoIncompleto['doble_firma']['itemizado']) == 2)
                        @trans('index.pendiente_firmas')
                      @else
                        @trans('index.pendiente_firma')
                      @endif
                  </span>
                </div>
              @endif
            @else
              <div class="container_btn_action">
                @if($contrato->has_itemizado_vigente)
                  <span class="badge badge-referencias" style="background-color:#0695d6;">
                      <i class="glyphicon glyphicon-th-list"></i>
                      @trans('itemizado.vista.tag.' . $opciones['version'])
                    </span>
                @endif
              </div>
            @endif
          </div>
        </a>

        <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>

          <ul class="dropdown-menu pull-right">
            @if(($contrato->completo || ($contratoIncompleto['status'] && !$contratoIncompleto['itemizado'])) && $contrato->has_itemizado_vigente)

              @if($opciones['version'] == 'vigente')
                <li class="visualizacion" data-seccion="itemizado" data-version="original">
                  <a class="mouse-pointer">
                    <i class="glyphicon glyphicon-th-list"></i>
                    @trans('itemizado.vista.nombre.original')
                  </a>
                </li>
              @elseif($contrato->has_itemizado_vigente)
                <li class="visualizacion" data-seccion="itemizado" data-version="vigente">
                  <a class="mouse-pointer">
                    <i class="glyphicon glyphicon-th-list"></i>
                    @trans('itemizado.vista.nombre.vigente')
                  </a>
                </li>
              @endif

            @endif

            @php ($data_historial = $contrato->dataHistorial($opciones['version']))


            @if(!empty($contratoIncompleto['doble_firma']['itemizado']) && (
                (Auth::user()->id == $contrato->causante->jefe_contrato_ar && in_array('firma_ar', $contratoIncompleto['doble_firma']['itemizado']))
                || (Auth::user()->id == $contrato->causante->jefe_contrato_py && in_array('firma_py', $contratoIncompleto['doble_firma']['itemizado']))
            ))
              <li>
                <a class="action" href="{{ route('itemizado.firmar', ['contrato_id' => $contrato->id ]) }}">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                  @trans('index.firmar')
                </a>
              </li>

              <li>
                <a class="action" href="{{ route('itemizado.borrador', ['contrato_id' => $contrato->id ]) }}">
                  <i class="fa fa-eraser" aria-hidden="true"></i>
                  @trans('index.borrador')
                </a>
              </li>
            @endif

            <li>
              <a
                data-url="{{ route('contrato.historial', ['clase_id' => $data_historial['clase_id'], 'clase_type' => $data_historial['clase_type'], 'seccion' => 'itemizado']) }}"
                class="open-historial historial-itemizado"
              >
                <i class="fa fa-history" aria-hidden="true"></i>
                @trans('index.historial')
              </a>
            </li>

            @if((!$contratoIncompleto['status']) || ($contratoIncompleto['status'] && !$contratoIncompleto['itemizado']))
              @permissions(('itemizado-export'))
              <li>
                <form class="form_excel" method="POST" data-action="{{route('export.itemizados')}}" id="form_excel">
                  {{ csrf_field() }}

                  <input type="hidden" class="excel-search-input form-control" name="excel_input" id="excel_input_itemizados" value="{{$contrato->id}}">

                  <input type="hidden" class="excel-search-input form-control" name="version" id="version" value="{{ ($opciones['version'] == 'vigente') ? 'vigente' :  'original' }}">

                  <button type="submit" id="excel_button_itemizado" class="button_link" title="@trans('index.descargar_a_excel')">

                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> @trans('index.descargar_a_excel')
                  </button>
                </form>
              </li>
              @endpermission
            @endif
          </ul>
        </div>
      </h4>
    </div>

    <div id="collapseOne_itemizado" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-itemizado">
      @if(isset($fromAjax))
        <div class="row">
          <div class="col-md-12">
            <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-itemizado">
              <ul></ul>
            </div>
          </div>
        </div>

        @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
          @if(isset($valueContratoMoneda->itemizado))
            @php ($itemizado = ($opciones['version'] == 'vigente' && $contrato->has_itemizado_vigente) ? $valueContratoMoneda->itemizado_vigente : $valueContratoMoneda->itemizado)

            @php ($keyItemizado = $itemizado->id)

            @php ($sufijo = '_it_')
          @endif

          <div class="panel-body pt-0 pb-0">
            @include('contratos.contratos.show.itemizado.itemizado')
          </div>
        @endforeach

        @permissions(('itemizado-edit'))

        @if($contratoIncompleto['status'])
          @if($contratoIncompleto['itemizado'])
            <form method="POST"
              action="{{route('itemizado.finalizar', ['contrato_id' => $contrato->id ])}}"
              data-action="{{route('itemizado.finalizar', ['contrato_id' => $contrato->id ])}}"
              id="form_itemizado"
            >
              {{ csrf_field() }}
              <input class="hidden" id="borrador" name="borrador" value="1">

              <div class="panel-body pt-0 pb-0">
                <button type="submit" class="hidden" id="hidden_submit"></button>

                <div class="col-md-12 mb-1 p-0">
                  <div class="buttons-on-title">
                    <div class="btns_itemizado">
                      <a id="btn_guardar_confirmable_itemizado" class="btn btn-primary btn-confirmable-submit pull-right"
                        data-form="form_itemizado"
                        data-body="{{trans('contratos.confirmacion.guardar-itemizado')}}"
                        data-action="{{route('itemizado.finalizar', ['contrato_id' => $contrato->id ])}}"
                        data-si="@trans('index.si')" data-no="@trans('index.no')"
                      >
                        {{trans('index.guardar')}}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          @endif
        @endif

        @endpermission
      @endif
    </div>
  </div>
</div>
