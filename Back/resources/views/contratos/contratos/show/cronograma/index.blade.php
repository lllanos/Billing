{{-- Si no esta en borrador tiene cronograma --}}

<input type="hidden" id="cronograma_version" value="{{$opciones['version']}}"/>
<input type="hidden" id="cronograma_visualizacion" value="{{$opciones['visualizacion']}}"/>

<div class="panel-group acordion" id="accordion-cronograma" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-cronograma">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                <a
                   class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif"
                   role="button"
                   data-toggle="collapse"
                   data-parent="#accordion-cronograma"
                   href="#collapseOne_cronograma"
                   aria-expanded="true"
                   aria-controls="collapseOne_cronograma"
                   @if(!isset($fromAjax))
                    data-seccion="cronograma"
                    data-version="{{ $opciones['version']}} "
                   @endif
                >
                    <div class="container_icon_angle">
                        <i class="fa fa-angle-down"></i> @trans('contratos.cronograma')
                    </div>

                    <div class="container_icon_angle">
                        @if($contratoIncompleto['status'])
                            @if($contratoIncompleto['cronograma'] || $contratoIncompleto['sin_fecha_inicio'])
                                <div class="container_btn_action">
                                    <span class="badge badge-referencias badge-borrador">
                                        @if(isset($contratoIncompleto['sin_fecha_inicio']) && $contratoIncompleto['sin_fecha_inicio'])
                                            @trans('contratos.motivo_incompleto.fecha_acta_inicio_tag')
                                        @else
                                            <i class="fa fa-eraser"></i>
                                            @trans('index.borrador')
                                        @endif
                                    </span>
                                </div>
                            @elseif(!empty($contratoIncompleto['doble_firma']['cronograma']))
                                <div class="container_btn_action">
                                    <span class="badge badge-referencias badge-borrador">
                                        <i class="fa fa-pencil"></i>
                                        @if(count($contratoIncompleto['doble_firma']['cronograma']) == 2)
                                            @trans('index.pendiente_firmas')
                                        @else
                                            @trans('index.pendiente_firma')
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="container_btn_action">
                                @if($contrato->has_cronograma_vigente)
                                    <span
                                        class="badge badge-referencias"
                                        style="background-color:var(--poncho-light-blue);"
                                    >
                                        <i class="glyphicon glyphicon-th-list"></i>
                                        @trans('cronograma.vista.tag.' . $opciones['version'])
                                    </span>
                                @endif

                                <span
                                    class="badge badge-referencias"
                                    style="background-color:var(--poncho-light-blue);"
                                >
                                @if($opciones['visualizacion'] == 'porcentaje')
                                    <i class="fa fa-percent" aria-hidden="true"></i>
                                    @trans('cronograma.vista.tag.porcentaje')
                                @elseif($opciones['visualizacion'] == 'moneda')
                                    <i class="fa fa-usd" aria-hidden="true"></i>
                                    @trans('cronograma.vista.tag.moneda')
                                @elseif($opciones['visualizacion'] == 'all')
                                    <i class="rob rob-ruler"></i>
                                    @trans('cronograma.vista.tag.all')
                                @elseif($opciones['visualizacion'] == 'curva_inversion')
                                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                                    @trans('cronograma.vista.tag.curva_inversion')
                                @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </a>

                <div
                    class="dropdown container_btn_action"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="@trans('index.opciones')"
                >
                    <button
                        class="btn btn-primary dropdown-toggle"
                        type="button" data-toggle="dropdown"
                        aria-label="@trans('index.opciones')"
                    >
                        <i class="fa fa-ellipsis-v"></i>
                    </button>

                    <ul class="dropdown-menu pull-right">

                        @if(!empty($contratoIncompleto['doble_firma']['cronograma']) && (
                            (Auth::user()->id == $contrato->causante->jefe_obras_ar && in_array('firma_ar', $contratoIncompleto['doble_firma']['cronograma']))
                            || (Auth::user()->id == $contrato->causante->jefe_obras_py && in_array('firma_py', $contratoIncompleto['doble_firma']['cronograma']))
                        ))
                            <li>
                                <a class="action"
                                   href="{{ route('cronograma.firmar', ['contrato_id' => $contrato->id ]) }}"
                                >
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                    @trans('index.firmar')
                                </a>
                            </li>

                            <li>
                                <a class="action"
                                   href="{{ route('cronograma.borrador', ['contrato_id' => $contrato->id ]) }}"
                                >
                                    <i class="fa fa-eraser" aria-hidden="true"></i>
                                    @trans('index.borrador')
                                </a>
                            </li>
                        @endif

                        @if(($contrato->completo || ($contratoIncompleto['status'] && !$contratoIncompleto['cronograma'])) && $contrato->has_cronograma_vigente)
                            @if($opciones['version'] == 'vigente')
                                <li class="visualizacion" data-seccion="cronograma" data-version="original">
                                    <a class="mouse-pointer">
                                        <i class="glyphicon glyphicon-th-list"></i> @trans('cronograma.vista.nombre.original')
                                    </a>
                                </li>
                            @elseif($contrato->has_cronograma_vigente)
                                <li class="visualizacion" data-seccion="cronograma" data-version="vigente">
                                    <a class="mouse-pointer">
                                        <i class="glyphicon glyphicon-th-list"></i> @trans('cronograma.vista.nombre.vigente')
                                    </a>
                                </li>
                            @endif
                        @endif

                        @php ($data_historial = $contrato->dataHistorial($opciones['version']))

                        <li>
                            <a data-url="{{ route('contrato.historial', ['clase_id' => $data_historial['clase_id'], 'clase_type' => $data_historial['clase_type'], 'seccion' => 'cronograma']) }}"
                               class="open-historial historial-cronograma">
                                <i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')
                            </a>
                        </li>

                        @if($contrato->completo || ($contratoIncompleto['status'] && !$contratoIncompleto['cronograma'] && !$contratoIncompleto['sin_fecha_inicio']))
                            @if($opciones['visualizacion'] != 'moneda')
                                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="moneda">
                                    <a class="mouse-pointer">
                                        <i class="fa fa-usd" aria-hidden="true"></i> @trans('cronograma.vista.nombre.moneda')
                                    </a>
                                </li>
                            @endif

                            @if($opciones['visualizacion'] != 'porcentaje')
                                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="porcentaje">
                                    <a class="mouse-pointer">
                                        <i class="fa fa-percent"
                                           aria-hidden="true"></i> @trans('cronograma.vista.nombre.porcentaje')
                                    </a>
                                </li>
                            @endif

                            @if($opciones['visualizacion'] != 'all')
                                <li class="visualizacion" data-seccion="cronograma" data-visualizacion="all">
                                    <a class="mouse-pointer">
                                        <i class="rob rob-ruler"></i> @trans('cronograma.vista.nombre.all')
                                    </a>
                                </li>
                            @endif

                            @if($opciones['visualizacion'] != 'curva_inversion')
                                <li class="visualizacion" data-seccion="cronograma"
                                    data-visualizacion="curva_inversion">
                                    <a class="mouse-pointer">
                                        <i class="fa fa-line-chart" aria-hidden="true"></i> @trans('cronograma.vista.nombre.curva_inversion')
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if($contrato->completo || ($contratoIncompleto['status'] && !$contratoIncompleto['cronograma']))
                            @permissions(('cronograma-export'))
                            @if($opciones['visualizacion'] != 'curva_inversion')
                                <li>
                                    <form
                                        class="form_excel_2" method="POST"
                                        data-action="{{route('export.cronograma')}}"
                                        id="form_excel_cronograma"
                                    >
                                        {{ csrf_field() }}

                                        <input type="hidden"
                                             class="excel-search-input form-control"
                                             name="excel_input"
                                             id="excel_input_cronograma"
                                             value="{{$contrato->id}}"
                                        >

                                        <input type="hidden"
                                            class="excel-search-input form-control"
                                            name="visualizacion" id="visualizacion"
                                            value="{{ $opciones['visualizacion'] ?? '' }}"
                                        >
                                        <input type="hidden" class="excel-search-input form-control" name="version"
                                           id="version"
                                           value="{{ $opciones['version'] ?? '' }}"
                                        >
                                        <button type="submit"
                                            id="excel_button_cronograma"
                                            class="button_link width100"
                                            title="@trans('index.descargar_a_excel')"
                                        >
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            @trans('index.descargar_a_excel')
                                        </button>
                                    </form>
                                </li>
                            @endif
                            @endpermission
                        @endif

                    </ul>
                </div>
            </h4>
        </div>

        <div id="collapseOne_cronograma" class="panel-collapse collapse in" role="tabpanel"
             aria-labelledby="headingOne-cronograma">
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
                            <div class="errores-publicacion hidden alert alert-danger m-2" id="errores-cronograma">
                                <ul></ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @if($opciones['version'] == 'vigente' && $contrato->has_cronograma_vigente)
                                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                    <div class="panel-body pt-0 pb-0">
                                        @php ($cronograma = $valueContratoMoneda->cronograma_vigente)
                                        @include('contratos.contratos.show.cronograma.cronograma')
                                    </div>
                                @endforeach
                            @else
                                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                    <div class="panel-body pt-0 pb-0">
                                        @php ($cronograma = $valueContratoMoneda->cronograma)
                                        @include('contratos.contratos.show.cronograma.cronograma')
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    @permissions(('cronograma-edit'))
                    @if($contratoIncompleto['status'])
                        @if($contratoIncompleto['cronograma'])
                            <form method="POST"
                                action="{{route('cronograma.updateOrStore', ['contrato_id' => $contrato->id ])}}"
                                data-action="{{route('cronograma.updateOrStore', ['contrato_id' => $contrato->id ])}}"
                                id="form_cronograma"
                            >
                                {{ csrf_field() }}

                                <div class="panel-body pt-0 pb-0">
                                    <button type="submit" class="hidden" id="hidden_submit"></button>

                                    <div class="col-md-12 mb-1 p-0">
                                        <div class="buttons-on-title">
                                            <div class="btns_cronograma">
                                                <a class="btn btn-success submit pull-right hidden"
                                                   href="javascript:void(0);" data-accion="guardar" id="btn_guardar">
                                                    @trans('index.guardar')
                                                </a>

                                                <a id="btn_guardar_confirmable_cronograma"
                                                    class="btn btn-primary btn-confirmable-submit pull-right"
                                                    data-form="form_cronograma"
                                                    @if($contrato->is_ampliacion)
                                                    data-body="@trans('contratos.confirmacion.edit-cronograma-ampliacion')"
                                                    data-action="{{route('cronograma.updateOrStoreAmpliacion', ['id' => $contrato->id ])}}"
                                                    @else
                                                    data-body="@trans('contratos.confirmacion.edit-cronograma')"
                                                    data-action="{{route('cronograma.updateOrStore', ['contrato_id' => $contrato->id ])}}"
                                                    data-action="{{route('cronograma.updateOrStore', ['contrato_id' => $contrato->id ])}}"
                                                    @endif
                                                    data-si="@trans('index.si')" data-no="@trans('index.no')"
                                                >
                                                    @trans('index.guardar')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @endif
                    @endpermission
                @endif
            @endif
        </div>
    </div>
</div>
