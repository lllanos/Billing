<div class="panel-group acordion" id="accordion-adendaAmpliacion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-adendaAmpliacion">
            <h4 class="panel-title titulo_collapse m-0">
                <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif"
                   role="button" data-toggle="collapse" data-parent="#accordion-adendaAmpliacion"
                   href="#collapseOne_adendaAmpliacion" aria-expanded="true"
                   aria-controls="collapseOne_adendaAmpliacion"
                   @if(!isset($fromAjax)) data-seccion="adendaAmpliacion" data-version="original" @endif>
                    <div class="container_icon_angle">
                        <i class="fa fa-angle-down"></i>
                        @trans('forms.adendas_ampliacion')
                    </div>

                    @ifcount($contrato->adendasAmpliacion_borrador)
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

        <div id="collapseOne_adendaAmpliacion" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-adendaAmpliacion">
            @if(isset($fromAjax))
                @permissions('adenda_ampliacion-view', 'adenda_ampliacion-create')

                @if(sizeof($contrato->adendasAmpliacion) > 0)
                    <div class="panel-body p-2">
                        <div class="list-table">
                            <div class="zui-wrapper zui-action-32px-fixed">
                                <div class="zui-scroller"> <!-- zui-no-data -->
                                    <table class="table table-striped table-hover table-bordered zui-table">

                                        <thead>

                                        <tr>
                                            <th class="text-center"></th>
                                            <th>{{trans('forms.expediente')}}</th>
                                            <th>{{trans('contratos.resoluc_adjudic_th')}}</th>
                                            <th>{{trans('forms.denominacion')}}</th>
                                            <th>{{trans('forms.montos')}}</th>
                                            <th>{{trans('adendas.monto_ampliado')}}</th>
                                            <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i>
                                            </th>
                                        </tr>

                                        </thead>

                                        <tbody class="tbody_js">

                                        @foreach($contrato->adendasAmpliacion as $keyContrato => $valueContrato)
                                            <tr id="contrato_{{$valueContrato->id}}">
                                                <td class="text-center">
                                                    @if($valueContrato->borrador)
                                                        <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                                                    @elseif ($valueContrato->doble_firma)
                                                        <i class="fa fa-pencil" data-toggle="tooltip" data-placement="bottom" title="{{ trans((empty($valueContrato->firma_ar) && empty($valueContrato->firma_py)) ? 'index.pendiente_firmas' : 'index.pendiente_firma') }}"></i>
                                                    @endif

                                                    @if($valueContrato->incompleto_show['status'])
                                                        <i class="fa fa-star-half-empty" data-toggle="tooltip"
                                                           data-html="true" data-placement="bottom"
                                                           title="{{$valueContrato->incompleto_show['mensaje']}}"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $valueContrato->expediente_madre }} </td>
                                                <td>{{ $valueContrato->resoluc_adjudic }} </td>
                                                <td>{{ $valueContrato->denominacion }}</td>

                                                <td id="montos">
                                                    @if($valueContrato->tiene_contratos_monedas)
                                                        @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                                            @if($valueContratoMoneda->monto_vigente !== null && $valueContratoMoneda->moneda != null)
                                                                <span class="badge">
                                                                    {{$valueContratoMoneda->moneda->simbolo}} {{$valueContratoMoneda->monto_vigente_dos_dec }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($valueContrato->tiene_contratos_monedas)
                                                        @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                                            @if($valueContratoMoneda->monto_ampliado !== null && $valueContratoMoneda->moneda != null)
                                                                <span class="badge">
                                                                    {{$valueContratoMoneda->moneda->simbolo}} {{$valueContratoMoneda->toDosDec('monto_ampliado') }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </td>

                                                <td class="actions-col noFilter">
                                                    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>

                                                        <ul class="dropdown-menu pull-right">
                                                            @permissions(('adenda_ampliacion-view'))
                                                            <li>
                                                                <a href="{{route('adenda.ver', ['id' => $valueContrato->id]) }}">
                                                                    <i class="glyphicon glyphicon-eye-open"></i>
                                                                    @trans('index.ver')
                                                                </a>
                                                            </li>
                                                            @endpermission

                                                            @permissions(('adenda_ampliacion-edit-borrador'))
                                                            @if($valueContrato->borrador)
                                                                <li>
                                                                    <a href="{{route('adenda.edit', ['id' => $valueContrato->id]) }}"><i
                                                                            class="glyphicon glyphicon-pencil"></i> @trans('index.editar')
                                                                    </a></li>
                                                            @endif
                                                            @endpermission

                                                            @permissions(('adenda_ampliacion-edit'))

                                                            @if(!$valueContrato->borrador)
                                                                <li>
                                                                    <a href="{{route('adenda.edit', ['id' => $valueContrato->id]) }}">
                                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                                        @trans('index.editar')
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @endpermission

                                                            @if($valueContrato->incompleto['status'])
                                                                @if($valueContrato->incompleto['itemizado'])
                                                                    @permissions(('itemizado-manage'))
                                                                    <li>
                                                                        <a href="{{route('adenda.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'itemizado'])}}"><i
                                                                                class="glyphicon glyphicon-pencil"></i>@trans('index.editar') @trans('contratos.itemizado')
                                                                        </a></li>
                                                                    @endpermission
                                                                @endif

                                                                @if($valueContrato->incompleto['cronograma'])
                                                                    @permissions(('cronograma-manage'))
                                                                    <li>
                                                                        <a href="{{route('adenda.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'cronograma'])}}"><i
                                                                                class="glyphicon glyphicon-pencil"></i>@trans('index.editar') @trans('contratos.cronograma')
                                                                        </a></li>
                                                                    @endpermission
                                                                @endif
                                                            @endif

                                                            @permissions(('adenda_ampliacion-delete'))

                                                            @if($valueContrato->borrador)
                                                                <li>
                                                                    <a class="eliminar btn-confirmable-prevalidado"
                                                                       data-prevalidacion="{{ route('contratos.preDelete', ['id' => $valueContrato->id]) }}"
                                                                       data-body="{{trans('index.confirmar_eliminar.adenda', ['nombre' => $valueContrato->nombre_completo])}}"
                                                                       data-action="{{ route('contratos.delete', ['id' => $valueContrato->id]) }}"
                                                                       data-si="@trans('index.si')"
                                                                       data-no="@trans('index.no')">
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
