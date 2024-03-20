<div class="row">
  <div class="col-md-12">
    <div class="panel-body pt-0 pb-0">
      <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
        <div class="panel-group colapsable_top mt-1" id="accordion_poli_{{$valueContratoMoneda->id}}" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">

            <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_poli_{{$valueContratoMoneda->id}}">
              <h4 class="panel-title titulo_collapse panel_heading_0 m-0 panel_title_btn">
                <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_poli_{{$valueContratoMoneda->id}}" href="#collpapse_poli_{{$valueContratoMoneda->id}}" aria-expanded="true" aria-controls="collpapse_poli_{{$valueContratoMoneda->id}}">
                  <div class="container_icon_angle">
                    <i class="fa fa-angle-down"></i> @trans('contratos.polinomica')
                  </div>
                </a>
              </h4>
            </div>

            <div id="collpapse_poli_{{$valueContratoMoneda->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_poli_{{$valueContratoMoneda->id}}">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="panel-body p-0">
                    <div class="zui-wrapper zui-action-32px-fixed">
                      <div class="zui-scroller zui-no-data">
                        <div class="col-md-12">
                          <div class="row">
                            <table class="table table-striped table-bordered table-hover">
                              <thead>
                                <tr>
                                  <th>@trans('forms.indice')</th>
                                  <th>@trans('forms.nombre')</th>
                                  <th>@trans('forms.factor_incidencia')</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($valueContratoMoneda->polinomica->composiciones_ordenadas as $keyComposicion => $valueComposicion)
                                  <tr>
                                    <td>{{ $valueComposicion->indice_tabla1->nombre }}</td>
                                    <td>{{ $valueComposicion->nombre }}</td>
                                    <td class="text-right">{{ $valueComposicion->porcentaje_arg }} </td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
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
  </div>
</div>
