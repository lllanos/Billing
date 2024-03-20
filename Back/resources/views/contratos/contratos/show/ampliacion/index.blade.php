<div class="panel-group acordion" id="accordion-ampliacion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-ampliacion">
      <h4 class="panel-title titulo_collapse m-0">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-ampliacion" href="#collapseOne_ampliacion" aria-expanded="true" aria-controls="collapseOne_ampliacion"
        @if(!isset($fromAjax)) data-seccion="ampliacion" data-version="original" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.ampliaciones')</div>
          @ifcount($contrato->ampliaciones_borrador)
            <div class="container_icon_angle">
              <div class="container_btn_action">
                <span class="badge badge-referencias badge-borrador">
                  <i class="fa fa-eraser"></i>
                  @trans('index.borrador')
                </span>
              </div>
            </div>
          @endifcount
        </a>
      </h4>
    </div>

    <div id="collapseOne_ampliacion" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-ampliacion">
      @if(isset($fromAjax))
        @permissions(('ampliacion-view', 'ampliacion-create'))
          @if(sizeof($contrato->ampliaciones) > 0)
            <div class="panel-body p-2">
              <div class="list-table">
                <div class="zui-wrapper zui-action-32px-fixed">
                  <div class="zui-scroller"> <!-- zui-no-data -->
                    <table class="table table-striped table-hover table-bordered zui-table">
                      <thead>
                        <tr>
                          <th class="text-center"></th>
                          <th>{{trans('forms.fecha_solicitud')}}</th>
                          <th>{{trans('contratos.plazo_obra')}}</th>
                          <th>{{trans('forms.motivo')}}</th>
                          <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                        </tr>
                      </thead>
                      <tbody class="tbody_js">
                        @foreach($contrato->ampliaciones as $keyAmpliacion => $valueAmpliacion)
                          <tr id="contrato_{{$valueAmpliacion->id}}">
                            <td class="text-center">
                              @if($valueAmpliacion->borrador)
                                <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                              @endif
                              @if($valueAmpliacion->incompleto['status'])
                                <i class="fa fa-star-half-empty" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valueAmpliacion->incompleto['mensaje']}}"></i>
                              @endif
                            </td>
                            <td>{{ $valueAmpliacion->updated_at }} </td>
                            <td>{{ $valueAmpliacion->plazo_completo }} </td>
                            <td>{{ $valueAmpliacion->motivo_nombre }}</td>

                            <td class="actions-col noFilter">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                  @permissions(('ampliacion-view'))
                                    <li><a href="{{route('ampliacion.ver', ['id' => $valueAmpliacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                  @endpermission
                                  @if($valueAmpliacion->incompleto['status'] && $valueAmpliacion->incompleto['cronograma'])
                                    @permissions(('cronograma-manage'))
                                      <li><a href="{{route('ampliacion.editar.incompleto', ['id' => $valueAmpliacion->id, 'accion' => 'cronograma'])}}"><i class="glyphicon glyphicon-pencil"></i>@trans('index.editar') @trans('contratos.cronograma')</a></li>
                                    @endpermission
                                  @endif
                                  @permissions(('ampliacion-edit'))
                                    @if($valueAmpliacion->borrador)
                                      <li><a href="{{route('ampliacion.edit', ['id' => $valueAmpliacion->id]) }}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                                    @endif
                                  @endpermission

                                  @permissions(('ampliacion-delete'))
                                    @if($valueAmpliacion->borrador)
                                      <li>
                                        <a class="eliminar btn-confirmable-prevalidado"
                                        data-prevalidacion="{{ route('ampliacion.preDelete', ['id' => $valueAmpliacion->id]) }}"
                                        data-body="{{trans('index.confirmar_eliminar.ampliacion', ['fecha' => $valueAmpliacion->updated_at])}}"
                                        data-action="{{ route('ampliacion.delete', ['id' => $valueAmpliacion->id]) }}"
                                        data-si="@trans('index.si')" data-no="@trans('index.no')">
                                          <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                        </a>
                                      </li>
                                    @endif
                                  @endpermission
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
        @endpermission
      @endif
    </div>
  </div>
</div>
