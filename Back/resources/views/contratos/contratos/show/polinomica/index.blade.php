{{-- Si no esta en borrador tiene polinomica --}}

<input type="hidden" id="polinomica_version" value="{{$opciones['version']}}"/>
<input type="hidden" id="polinomica_visualizacion" value="{{$opciones['visualizacion']}}"/>

<div class="panel-group acordion" id="accordion-polinomica" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-polinomica">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-polinomica" href="#collapseOne_polinomica" aria-expanded="true" aria-controls="collapseOne_polinomica"
        @if(!isset($fromAjax)) data-seccion="polinomica" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.redeterminaciones')</div>

          <div class="container_icon_angle">
            @if($contratoIncompleto['status'])
              @if($contratoIncompleto['polinomica'])
                <div class="container_btn_action">
                  <span class="badge badge-referencias badge-borrador">
                    <i class="fa fa-eraser"></i>
                    @trans('index.borrador')
                  </span>
                </div>
              @endif
            @endif
          </div>
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
        @permissions(('polinomica-edit'))
        @if($contratoIncompleto['status'])
          @if($contratoIncompleto['polinomica'])
            <form method="POST"
              action="{{route('polinomica.updateOrStore', ['contrato_id' => $contrato->id ])}}"
              data-action="{{route('polinomica.updateOrStore', ['contrato_id' => $contrato->id ])}}"
              id="form_polinomicas"
            >
              {{ csrf_field() }}

            @endif
          @endif
        @endpermission
        <div class="panel-body pb-0">
          @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
            <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
              @include('contratos.contratos.show.polinomica.polinomica')
            </div>
          @endforeach
        </div>
        @permissions(('polinomica-edit'))
          @if($contratoIncompleto['status'])
            @if($contratoIncompleto['polinomica'])
              <input class="hidden" id="borrador" name="borrador" value="1">
              <button type="submit" class="hidden" id="hidden_submit_polinomica"> </button>
            </form>
              <div class="panel-body pt-0 pb-0">

                <div class="col-md-12 mb-1 p-0">
                  <div class="buttons-on-title">
                    <div class="btns_polinomica">
                      <a class="btn btn-success submit pull-right hidden" href="javascript:void(0);" data-accion="guardar" id="btn_guardar">
                        @trans('index.guardar')
                      </a>
                      <a id="btn_guardar_confirmable_polinomica" class="btn btn-success btn-confirmable-submit pull-right"
                        data-form="form_polinomicas"
                        data-body="{{trans('contratos.confirmacion.edit-polinomica')}}"
                        data-action="{{route('polinomica.updateOrStore', ['contrato_id' => $contrato->id ])}}"
                        data-si="@trans('index.si')" data-no="@trans('index.no')">
                        @trans('index.guardar')
                      </a>
                      <a class="btn btn-success submit pull-right" href="javascript:void(0);" data-accion="guardar_borrador" id="btn_guardar_borrador">
                        @trans('index.guardar_borrador')
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endif
        @endpermission
      @endif
    </div>
  </div>
</div>
