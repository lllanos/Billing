<table class="table table-striped table-hover table-bordered zui-table">
    <thead>
    <tr>
        <th class="text-center"></th>
        <th>@trans('certificado.nr_certificado_th')</th>
        <th>@trans('certificado.avance_certificado')</th>
        <th>@trans('certificado.avance_acumulado')</th>
        <th>@trans('certificado.importe_certificado')</th>
        <th>@trans('certificado.importe_acumulado')</th>
        @if(!$empalme)
            <th>@trans('forms.estado')</th>
        @endif
        <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
    </tr>
    </thead>
    <tbody class="tbody_js">
    @foreach($certificados as $certificado)
        <tr id="certificado_{{$certificado->id}}">
            <td class="text-center">
                @if($certificado->es_borrador)
                    <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom"
                       title="@trans('index.borrador')"></i>
                @elseif($certificado->doble_firma)
                    <i class="fa fa-pencil" data-toggle="tooltip" data-placement="bottom" title="{{ trans((!$certificado->firma_ar && !$certificado->firma_py) ? 'index.pendiente_firmas' : 'index.pendiente_firma') }}"></i>
                @elseif($certificado->solicitado_a_validar OR $certificado->solicitado_a_corregir)
                    <i class="fa fa-star-half-empty" data-toggle="tooltip" data-placement="bottom"
                       title="{{$certificado->estado['nombre_trans']}}"></i>
                @endif
            </td>
            <td>
                {{$certificado->mes_show}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</td>
            <td>
                @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                    <span class="badge">
            {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->avanceCertificadoPorMoneda($keyContratoMoneda)) %
          </span>
                @endforeach
            </td>
            <td>
                @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                    <span class="badge">
            {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->avanceAcumuladoPorMoneda($keyContratoMoneda)) %
          </span>
                @endforeach
            </td>
            <td>
                @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                    <span class="badge">
           {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->montoPorMoneda($keyContratoMoneda))
          </span>
                @endforeach
            </td>
            <td>
                @foreach($certificado->certificados_por_moneda as $keyContratoMoneda => $valueContratoMoneda)
                    <span class="badge">
            {{$valueContratoMoneda['simbolo']}} @toDosDec($certificado->importeAcumuladoPorMoneda($keyContratoMoneda))
          </span>
                @endforeach
            </td>
            @if(!$empalme)
                <td>
                  <span class="badge badge-referencias" style="background-color:#{{$certificado->estado['color']}};">
                    {{$certificado->estado['nombre_trans']}}
                  </span>
                    @if($certificado->empalme)
                        <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                          @trans('contratos.empalme')
                        </span>
                    @endif
                </td>
            @endif
            <td class="actions-col noFilter">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left"
                     title="@trans('index.opciones')">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        @permissions(('certificado-view'))
                        <li>
                            <a href="{{route('certificado.ver', ['id' => $certificado->id]) }}">
                                <i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')
                            </a>
                        </li>

                        @if(!$certificado->borrador)
                            <li>
                                <a href="{{route('export.certificado', ['id' => $certificado->id]) }}">
                                    <i class="glyphicon glyphicon-save-file"></i> @trans('index.descargar')
                                </a>
                            </li>
                        @endif

                        @if(
                            $certificado->doble_firma
                            && (
                                (!$certificado->firma_ar && Auth::user()->id == $contrato->causante->jefe_contrato_ar)
                                || (!$certificado->firma_py && Auth::user()->id == $contrato->causante->jefe_contrato_py)
                            )
                        )
                            <li>
                                <a class="action" href="{{ route('certificado.sign', ['id' => $certificado->id ]) }}">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                    @trans('index.firmar')
                                </a>
                            </li>
                        @endif
                        @endpermission
                        @if($certificado->puede_editar)
                            @permissions(('certificado-edit'))
                            @if($empalme)
                                <li>
                                    <a href="{{route('empalme.edit', ['id' => $certificado->id]) }}">
                                        <i class="fa fa-pencil"></i> @trans('index.editar')</a></li>
                            @else
                                <li><a href="{{route('certificado.edit', ['id' => $certificado->id])}}"><i
                                            class="fa fa-pencil"></i> @trans('index.editar')</a></li>
                            @endif
                            @endpermission
                            @permissions(('certificado-delete'))
                            <li>
                                <a class="eliminar btn-confirmable-prevalidado"
                                   data-prevalidacion="{{ route('certificado.preDelete', ['id' => $certificado->id]) }}"
                                   data-body="@trans('index.confirmar_eliminar.certificado', ['mes' => $certificado->mes])"
                                   data-action="{{ route('certificado.delete', ['id' => $certificado->id]) }}"
                                   data-si="@trans('index.si')" data-no="@trans('index.no')">
                                    <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                </a>
                            </li>
                            @endpermission
                        @elseif($certificado->puede_aprobar)
                            @permissions(('certificado-aprobar'))
                            <li><a class="aprobar btn-confirmable"
                                   data-body="@trans('index.confirmar_aprobar.certificado', ['mes' => $certificado->mes]) @include('contratos.certificados.aclaracion')"
                                   data-action="{{route('solicitudes.certificado.aprobar', ['id' => $certificado->id]) }}"
                                   data-si="@trans('index.si')" data-no="@trans('index.no')">
                                    <i class="glyphicon glyphicon-ok"></i> @trans('index.aprobar')
                                </a>
                            </li>
                            @endpermission
                            @permissions(('certificado-rechazar'))
                            <li>
                                <a href="javascript:void(0);" class="open-modal-rechazar"
                                   id="btn_rechazar_{{$certificado->id}}"
                                   data-action="{{ route('solicitudes.certificado.rechazar', ['id' => $certificado->id]) }}"
                                   data-id="{{$certificado->id}}"
                                   data-title="{{trans('index.confirmar_rechazar.certificado', ['mes' => $certificado->mes])}}"
                                   title="{{ trans('index.rechazar')}}">
                                    <i class="glyphicon glyphicon-remove"></i> @trans('index.rechazar')
                                </a>
                            </li>
                            @endpermission
                        @endif
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
