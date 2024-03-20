{{-- Si no esta en borrador tiene analisis_precios --}}

<input type="hidden" id="analisis_precios_version" value="{{$opciones['version']}}" />
<input type="hidden" id="analisis_precios_visualizacion" value="{{$opciones['visualizacion']}}" />

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
      </h4>
    </div>

    <div id="collapseOne_analisis_precios" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-analisis_precios">
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
              <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-analisis_precios">
                <ul> </ul>
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
        @endif
      @endif
    </div>
  </div>
</div>
