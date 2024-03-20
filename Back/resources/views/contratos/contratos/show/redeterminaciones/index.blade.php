@php ($sufijo = 'redeterminaciones')
<input type="hidden" id="certificados_version" value="{{$opciones['version']}}" />
  <div class="panel-group acordion" id="accordion-{{$sufijo}}" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading-{{$sufijo}}">
        <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
          <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-{{$sufijo}}" href="#collapse_{{$sufijo}}" aria-expanded="true" aria-controls="collapse_{{$sufijo}}">
            <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.certificados')</div>
          </a>
        </h4>
      </div>

      <div id="collapse_{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{$sufijo}}">
        @if($contrato->has_redeterminaciones)
         <div class="row">
          <div class="col-md-12">
            <div class="panel-body pt-0 pb-0">
              <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
                <div class="panel-group colapsable_top mt-1" id="accordion{{$sufijo}}" role="tablist" aria-multiselectable="true">
                  <div class="panel panel-default">

                    <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading{{$sufijo}}">
                      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion{{$sufijo}}" href="#collpapse{{$sufijo}}" aria-expanded="true" aria-controls="collpapse{{$sufijo}}">
                          <div class="container_icon_angle">
                            <i class="fa fa-angle-down"></i> @trans('forms.certificados') @trans('certificado.basicos')
                          </div>
                        </a>
                      </h4>
                    </div>

                    <div id="collpapse{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_sub{{$sufijo}}">
                      <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 scrollable-collapse">
                        <div class="panel-body p-2">
                          <div class="list-table">
                            <div class="zui-wrapper zui-action-32px-fixed">
                              <div class="zui-scroller"> <!-- zui-no-data -->
                                @include('contratos.contratos.show.redeterminaciones.show.tabla', ['empalme' => false])
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        @else
          <div class="panel-body p-0">
            <div class="sin_datos_js"></div>
            <div class="sin_datos">
              <h1 class="text-center">@trans('index.no_datos')</h1>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
