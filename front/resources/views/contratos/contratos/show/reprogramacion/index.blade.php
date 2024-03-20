<div class="panel-group acordion" id="accordion-reprogramacion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-reprogramacion">
      <h4 class="panel-title titulo_collapse m-0">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-reprogramacion" href="#collapseOne_reprogramacion" aria-expanded="true" aria-controls="collapseOne_reprogramacion"
        @if(!isset($fromAjax)) data-seccion="reprogramacion" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.reprogramaciones')</div>
        </a>
      </h4>
    </div>
    <div id="collapseOne_reprogramacion" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-reprogramacion">
      @if(isset($fromAjax))
          @if(sizeof($contrato->reprogramaciones) > 0)
            <div class="panel-body p-2">
              <div class="list-table">
                <div class="zui-wrapper zui-action-32px-fixed">
                  <div class="zui-scroller"> <!-- zui-no-data -->
                    <table class="table table-striped table-hover table-bordered zui-table">
                      <thead>
                        <tr>
                          <th class="text-center"></th>
                          <th>{{trans('forms.fecha_solicitud')}}</th>
                          <th>{{trans('forms.motivo')}}</th>
                          <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                        </tr>
                      </thead>
                      <tbody class="tbody_js">
                        @foreach($contrato->reprogramaciones as $keyReprogramacion => $valueReprogramacion)
                          <tr id="contrato_{{$valueReprogramacion->id}}">
                            <td class="text-center">
                              @if($valueReprogramacion->borrador)
                                <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                              @endif
                              @if($valueReprogramacion->incompleto['status'])
                                <i class="fa fa-star-half-empty" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valueReprogramacion->incompleto['mensaje']}}"></i>
                              @endif
                            </td>
                            <td>{{ $valueReprogramacion->updated_at }} </td>
                            <td>{{ $valueReprogramacion->motivo_nombre }}</td>
                            <td class="actions-col noFilter">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="{{route('ampliacion.ver', ['id' => $valueReprogramacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                </ul>
                              </div>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
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
      @endif
    </div>
  </div>
</div>
