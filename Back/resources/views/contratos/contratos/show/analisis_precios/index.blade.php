{{-- Si no esta en borrador tiene analisis_precios --}}

<input type="hidden" id="analisis_precios_version" value="{{$opciones['version']}}"/>

<input type="hidden" id="analisis_precios_visualizacion" value="{{$opciones['visualizacion']}}"/>

<div class="panel-group acordion" id="accordion-analisis_precios" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-analisis_precios">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-analisis_precios" href="#collapseOne_analisis_precios" aria-expanded="true" aria-controls="collapseOne_analisis_precios"
          @if(!isset($fromAjax)) data-seccion="analisis_precios" data-version="{{$opciones['version']}}" @endif>

          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('contratos.analisis_precios')</div>

          <div class="container_icon_angle">
            <div class="container_btn_action">
              <span class="badge badge-referencias" style="background-color:#{{$contrato->analisis_precios[0]->estado['color']}};">
                {{$contrato->analisis_precios[0]->estado['nombre_trans']}}
              </span>
            </div>
          </div>
        </a>

        <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
            <i class="fa fa-ellipsis-v"></i>
          </button>

          <ul class="dropdown-menu pull-right">
            <li>
              <a data-url="{{ route('analisis_precios.historial', ['clase_id' => $contrato->analisis_precios[0]->id, 'seccion' => 'analisis_precios']) }}" class="open-historial historial-analisis_precios"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')
              </a>
            </li>

            @permissions(('analisis_precios-export'))
            <li>
              <form class="form_excel_2" method="POST" data-action="{{route('export.analisis_precios')}}" id="form_excel_analisis_precios">
                {{ csrf_field() }}

                <input type="text" class="hidden" name="excel_input" id="excel_input_analisis_precios" value="{{$contrato->id}}">

                <input type="hidden" class="excel-search-input form-control" name="version" id="version" value="{{ ($opciones['version'] == 'vigente') ? 'vigente ' : 'original' }}">

                <button type="submit" id="excel_button_analisis" class="button_link" title="@trans('index.descargar_a_excel')">
                  <i class="fa fa-file-excel-o" aria-hidden="true"></i> @trans('index.descargar_a_excel')
                </button>
              </form>
            </li>
            @endpermission
          </ul>
        </div>
      </h4>
    </div>

    <div id="collapseOne_analisis_precios" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-analisis_precios">
      @if(isset($fromAjax))

        <div class="row">
          <div class="col-md-12">
            <div class="errores-analisis hidden alert alert-danger m-2" id="errores-analisis">
              <ul></ul>
            </div>
          </div>
        </div>

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
              <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-analisis_precios">
                <ul></ul>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                @if($valueContratoMoneda->moneda->lleva_analisis)
                  <div class="panel-body pt-0 pb-0">
                    @php ($analisis_precios = $valueContratoMoneda->analisis_precios)

                    @if($analisis_precios == null)
                      <div class="col-md-12 col-sm-12">
                        <div class="row">
                          <h1 class="text-center">{{trans('contratos.sin.analisis_precios')}}</h1>
                        </div>
                      </div>
                    @else
                      @include('contratos.contratos.show.analisis_precios.show_edit')
                    @endif
                  </div>
                @endif
              @endforeach
            </div>
          </div>

          @permissions(('analisis_precios-edit'))
          <form method="POST" action="" data-action="" id="form_analisis_precios">
            {{ csrf_field() }}
            <div class="panel-body pt-0 pb-0">
              <button type="submit" class="hidden" id="hidden_submit"></button>
              <div class="col-md-12 mb-1 p-0">
                <div class="buttons-on-title">
                  <div class="btns_analisis_precios">

                    <a class="btn btn-success submit pull-right hidden" href="javascript:void(0);" data-accion="guardar" id="btn_guardar">
                      @trans('index.guardar')
                    </a>

                    @foreach ($analisis_precios->acciones_posibles as $valueAccion)
                      <a id="btn_guardar_confirmable_analisis_precios" class="btn btn-primary btn-confirmable-submit pull-right"
                        data-form="form_analisis_precios"
                        data-body="@trans('analisis_precios.mensajes.confirmar.' . $valueAccion, ['nombre_completo' => $contrato->nombre_completo])"
                        data-action="{{route('analisis_precios.updateOrStore', ['analisis_precios_id' => $analisis_precios->id, 'accion' => $valueAccion])}}"
                        data-si="@trans('index.si')" data-no="@trans('index.no')">
                        @trans('analisis_precios.acciones.' . $valueAccion)
                      </a>
                    @endforeach

                  </div>
                </div>
              </div>
            </div>
          </form>
          @endpermission
        @endif
      @endif
    </div>
  </div>
</div>
