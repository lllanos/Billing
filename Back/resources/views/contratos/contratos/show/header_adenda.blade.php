<div class="titulo__contenido">
    @trans('index.adenda') {{$contrato->expediente_madre}}
</div>
<div class="buttons-on-title">
    @if($contrato->borrador)
        @permissions($contrato->tipo_contrato->nombre . '-edit', $contrato->tipo_contrato->nombre . '-edit-borrador')
        <div class="button_desktop">
            <a class="btn btn-success pull-right" href="{{route('adenda.edit', ['id' => $contrato->id]) }}">
                @trans('index.editar') @trans('index.adenda')
            </a>
        </div>

        @if(Auth::user()->can($contrato->tipo_contrato->nombre . '-edit'))
            <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>

                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="{{route('adenda.edit', ['id' => $contrato->id]) }}">
                                @trans('index.editar') @trans('index.adenda')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
        @endpermission
    @elseif ($dobleFirma && (!$contrato->firma_ar || !$contrato->firma_py))
        @if(
            Auth::user()->can($contrato->tipo_contrato->nombre . '-edit') ||
           ($contrato->permite_adendas) || ($contrato->permite_ampliaciones_de_obra)
        )
            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                <button class="btn btn-primary dropdown-toggle" id="dd_menu" type="button" data-toggle="dropdown"
                        aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                </button>

                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="{{route('adenda.edit', ['id' => $contrato->id]) }}">
                            @trans('forms.editar') @trans('index.adenda')
                        </a>
                    </li>

                    @if(
                      (Auth::user()->id == $firmaAr && !$contrato->firma_ar)
                      || (Auth::user()->id == $firmaPy && !$contrato->firma_py)
                    )
                        <li>
                            <a class="action" href="{{route('adenda.firmar', ['id' => $contrato->id]) }}">
                                @trans('forms.firmar') @trans('index.adenda')
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        @endif
    @else
        <div>
            @if(
              Auth::user()->can($contrato->tipo_contrato->nombre . '-edit') ||
              ($contrato->permite_adendas) || ($contrato->permite_ampliaciones_de_obra)
            )
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>

                    <ul class="dropdown-menu pull-right">
                        @if(Auth::user()->can($contrato->tipo_contrato->nombre . '-edit'))
                            <li>
                                <a href="{{ route('adenda.edit', ['id' => $contrato->id]) }}">
                                    @trans('index.editar') @trans('index.adenda')
                                </a>
                            </li>
                        @endif

                        @if($contrato->permite_adendas)
                            @permissions('adenda_certificacion-create', 'adenda_ampliacion-create')
                            <li>
                                <a href="{{route('adenda.create', ['contrato_id' => $contrato->id])}}">
                                    @trans('index.solicitar') @trans('contratos.adenda')
                                </a>
                            </li>
                            @endpermission
                        @endif

                        @if($contrato->permite_ampliaciones_de_obra)
                            @permissions('adenda_certificacion-create', 'adenda_ampliacion-create')
                            <li>
                                <a href="{{route('ampliacion.create', ['contrato_id' => $contrato->id])}}">
                                    @trans('index.solicitar') @trans('contratos.ampliacion_reprogramacion')
                                </a>
                            </li>
                            @endpermission
                        @endif

                        @if(!$contrato->isAdendaAmpliacion)
                            @permissions(('anticipos-create'))
                            <li class="btn_add_anticipo" data-toggle="modal" data-target="#anticipoAddModal">
                                <a> @trans('index.agregar')  @trans('forms.anticipo')</a>
                            </li>
                            @endpermission
                        @endif

                        @if($contrato->has_requiere_garantia)
                            @permissions(('garantias-manage'))
                            <li class="btn_add_garantia" data-toggle="modal" data-target="#garantiaAddModal">
                                <a> @trans('index.validar')  @trans('contratos.garantia')</a>
                            </li>
                            @endpermission
                        @endif

                        @if($contrato->isAdenda && !$contrato->isAdendaAmpliacion)
                            @if($contrato->permite_certificados)
                                <li class="loadingToggle">
                                    <a href="{{route('certificado.create', ['contrato_id' => $contrato->id, 'empalme' => false])}}">
                                        <i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.certificado')
                                        @trans('index.mes') {{count($contrato->certificados()->whereRedeterminado(0)->get()) + 1}}
                                    </a>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>
