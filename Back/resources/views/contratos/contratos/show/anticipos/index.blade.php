<input type="hidden" id="anticipos_version" value="{{$opciones['version']}}" />
  <div class="panel-group acordion" id="accordion-anticipos" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-anticipos">
        <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
          <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-anticipos" href="#collapseOne_anticipos" aria-expanded="true" aria-controls="collapseOne_anticipos"
            @if(!isset($fromAjax)) data-seccion="anticipos" data-version="{{$opciones['version']}}" @endif>
            <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.anticipos')</div>
          </a>
          @permissions(('anticipos-create'))
          <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
              <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu pull-right">
              <li class="btn_add_anticipo" data-toggle="modal" data-target="#anticipoAddModal"><a><i class="fa fa-plus-square"></i> @trans('index.agregar')</a></li>
            </ul>
          </div>
          @endpermission
        </h4>
      </div>

      <div id="collapseOne_anticipos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-anticipos">
        @if(isset($fromAjax))
          <div class="row">
            <div class="col-md-12">
              <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-anticipos">
                <ul> </ul>
              </div>
            </div>
          </div>
          @if($contrato->has_anticipos)
            <div class="panel-body p-2">
              <div class="list-table">
                <div class="zui-wrapper zui-action-32px-fixed">
                  <div class="zui-scroller"> <!-- zui-no-data -->
                    <table class="table table-striped table-hover table-bordered zui-table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>{{trans('forms.fecha')}}</th>
                          <th>{{trans('forms.descripcion')}}</th>
                          <th>{{trans('forms.total')}}</th>
                          <th>{{trans('forms.porcentaje')}}</th>
                          <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                        </tr>
                      </thead>
                      <tbody class="tbody_js">
                        @foreach($contrato->anticipos as $anticipo)
                          <tr id="anticipo_{{$anticipo->id}}">
                            <td>
                                @if($anticipo->doble_firma)
                                    <span class="badge badge-referencias badge-borrador">
                                        <i class="fa fa-pencil"></i>

                                        @if(!$anticipo->firma_ar && !$anticipo->firma_py)
                                            @trans('index.pendiente_firmas')
                                        @else
                                            @trans('index.pendiente_firma')
                                        @endif

                                    </span>
                                @endif
                            </td>

                            <td> {{ $anticipo->fecha }} </td>
                            <td> {{ $anticipo->descripcion }} </td>

                            <td id="montos">
                              @foreach($anticipo->items_anticipo as $item)
                                <span class="badge" style="background-color:var(--dark-gray-color);">{{$item->contrato_moneda->moneda->simbolo}} {{$item->total}}</span>
                              @endforeach
                            </td>
                            <td>
                              @foreach($anticipo->items_anticipo as $item)
                              <span class="badge" style="background-color:var(--dark-gray-color);">{{$item->contrato_moneda->moneda->simbolo}} @toDosDec($item->porcentaje) %</span>
                              @endforeach
                            </td>

                            <td class="actions-col noFilter">
                              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    @if(
                                        $anticipo->doble_firma
                                        && (
                                            (!$anticipo->firma_ar && Auth::user()->id == $contrato->causante->jefe_contrato_ar)
                                            || (!$anticipo->firma_py && Auth::user()->id == $contrato->causante->jefe_contrato_py)
                                        )
                                    )
                                        <li>
                                            <a class="action" href="{{ route('anticipo.firmar', ['id' => $anticipo->id ]) }}">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                                @trans('index.firmar')
                                            </a>
                                        </li>
                                    @endif

                                  @permissions(('anticipos-delete'))
                                    <li>
                                      <a class="eliminar btn-confirmable-prevalidado"
                                      data-prevalidacion="{{ route('anticipo.preDelete', ['id' => $anticipo->id]) }}"
                                      data-body="{{trans('index.confirmar_eliminar.anticipo'). $anticipo->descripcion .'?'}}"
                                      data-action="{{ route('anticipo.delete', ['id' => $anticipo->id]) }}"
                                      data-si="@trans('index.si')" data-no="@trans('index.no')">
                                        <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                      </a>
                                    </li>
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
        @endif
      </div>
    </div>
  </div>
