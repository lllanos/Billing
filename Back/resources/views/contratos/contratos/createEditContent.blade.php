{{-- Separado para reusar las vistas desde adendas --}}
<div class="row">
    <form role="form" method="POST" data-action="{{route('contratos.storeUpdate')}}" id="form-ajax">
        {{ csrf_field() }}
        @if(!isset($isAdenda))
            @if($contrato->id != null && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                <input type='text' id='padre_id' name='padre_id' class='hidden' value="">
                <input type='text' id='tipo_id' name='tipo_id' class='hidden' value="{{$contrato->tipo_id}}">
                <input type='text' id='contrato_completo' name='contrato_completo' class='hidden' value="1">
            @else
                <input type='text' id='padre_id' name='padre_id' class='hidden' value="">
                <input type='text' id='tipo_id' name='tipo_id' class='hidden'
                       value="@if(isset($contrato->tipo_id)) {{$contrato->tipo_id}} @else {{$tipo_contrato->id}} @endif">
                <input type='text' id='contrato_completo' name='contrato_completo' class='hidden' value="0">
            @endif
        @else
            <input type='text' id='padre_id' name='padre_id' class='hidden' value="{{$contrato_padre->id}}">
            <input type='text' id='tipo_id' name='tipo_id' class='hidden'
                   value="@if(isset($contrato->tipo_id)) {{$contrato->tipo_id}} @else {{$tipo_contrato->id}} @endif">
            <input type='text' id='contrato_completo' name='contrato_completo' class='hidden'
                   value="@if($contrato->guardadoDefinitivo && $contrato->id != null) 1 @else 0 @endif">
        @endif

        <input type='text' id='id' name='id' class='hidden' value="{{$contrato->id}}">
        <input type='text' id='borrador' name='borrador' class='hidden' value="0">
        <div class="alert alert-danger hidden">
            <ul></ul>
        </div>

        @if(!isset($isAdenda))
            @if($contrato->id != null && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>@trans('index.denominacion')</label>
                        <input type='text' value="{{$contrato->denominacion}}" id='denominacion' name='denominacion'
                               class='form-control' placeholder='@trans('index.denominacion')'>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>@trans('contratos.numero_contrato')</label>
                        <input type='text' value="{{$contrato->numero_contrato}}" id='numero_contrato'
                               name='numero_contrato' class='form-control'
                               placeholder='@trans('contratos.numero_contrato')'>
                    </div>
                </div>
            @else
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>@trans('contratos.numero_contratacion')</label>
                        <input type='text' value="{{$contrato->numero_contratacion}}" id='numero_contratacion'
                               name='numero_contratacion' class='form-control'
                               placeholder='@trans('contratos.numero_contratacion')' required>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>@trans('contratos.numero_contrato')</label>
                        <input type='text' value="{{$contrato->numero_contrato}}" id='numero_contrato'
                               name='numero_contrato' class='form-control'
                               placeholder='@trans('contratos.numero_contrato')'>
                    </div>
                </div>
            @endif
        @else
            <input type='text' value="{{$contrato_padre->numero_contratacion}}" id='numero_contratacion'
                   name='numero_contratacion' class='form-control hidden'
                   placeholder='@trans('contratos.numero_contratacion')' required>
            <input type='text' value="{{$contrato_padre->numero_contrato}}" id='numero_contrato' name='numero_contrato'
                   class='form-control hidden' placeholder='@trans('contratos.numero_contrato')'>
        @endif

        @if(isset($isAdenda))
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <label>@trans('forms.expediente')</label>
                    <input type='text' value="{{$contrato->expediente_madre}}" id='expediente_madre'
                           name='expediente_madre' class='form-control' placeholder='@trans('forms.expediente')'
                           required>
                    @else
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>@trans('forms.expediente_madre')</label>
                                <input type='text' value="{{$contrato->expediente_madre}}" id='expediente_madre'
                                       name='expediente_madre' class='form-control'
                                       placeholder='@trans('forms.expediente_madre')'>
                                @endif
                            </div>
                        </div>

                        @if($contrato->id != null && $contrato->isAdenda && !$contrato->isAdendaAmpliacion)
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>@trans('index.denominacion')</label>
                                    <input type='text' value="{{$contrato->denominacion}}" id='denominacion'
                                           name='denominacion' class='form-control'
                                           placeholder='@trans('index.denominacion')'>
                                </div>
                            </div>
                        @endif

                        @if($contrato->id && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))

                        @else
                            @if(!$contrato->is_adenda_ampliacion)
                                <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                                    <div class="form-group mb-1">
                                        <label class="fixMargin4">
                                            <div class="checkbox noMarginChk">
                                                <div class="btn-group chk-group-btn" data-toggle="buttons">
                                                    <label
                                                        class="btn btn-primary btn-sm @if($contrato->empalme) active @endif">
                                                        <input autocomplete="off" class="triggerClickChk"
                                                               type="checkbox" name="empalme" id="empalme"
                                                               @if($contrato->empalme) checked @endif>
                                                        <span class="glyphicon glyphicon-ok"></span>
                                                    </label>
                                                    @trans('contratos.contrato_empalme')
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>@trans('index.denominacion')</label>
                                    <input type='text' value="{{$contrato->denominacion}}" id='denominacion'
                                           name='denominacion' class='form-control'
                                           placeholder='@trans('index.denominacion')'>
                                </div>
                            </div>
                        @endif

                        @if($contrato->id && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                        @else
                            @if(!isset($isAdenda))
                                <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                                    <div class="form-group mb-1">
                                        <label class="fixMargin4">
                                            <div class="checkbox noMarginChk">
                                                <div class="btn-group chk-group-btn" data-toggle="buttons">
                                                    <label
                                                        class="btn btn-primary btn-sm @if($contrato->no_redetermina) active @endif">
                                                        <input autocomplete="off" class="triggerClickChk"
                                                               type="checkbox" name="no_redetermina" id="no_redetermina"
                                                               @if($contrato->no_redetermina) checked @endif>
                                                        <span class="glyphicon glyphicon-ok"></span>
                                                    </label>
                                                    {!!trans('contratos.no_redetermina')!!}
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <input autocomplete="off" class="triggerClickChk hidden" type="checkbox"
                                       name="no_redetermina" id="no_redetermina"
                                       @if($contrato_padre->no_redetermina) checked @endif>
                            @endif

                            @if(!isset($isAdenda))
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group form-group-chosen">
                                        <label for="contratista_id">@trans('forms.contratista')</label>
                                        <select class="form-control" name="contratista_id" id="contratista_id">
                                            @foreach($contratistas as $key => $value)
                                                <option value="{{$key}}"
                                                        @if($contrato->contratista_id == $key) selected @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="text" class="form-control hidden" name="contratista_id" id="contratista_id"
                                       value="{{$contrato_padre->contratista_id}}">
                            @endif
                        @endif

                        @if($contrato->id && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                            <div class="float-left" style="float:left; width: 100%;">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group form-group-chosen">
                                        <label or="repre_tec_eby_id">
                                            @trans('contratos.representante_tecnico_eby')
                                        </label>

                                        <select class="form-control" name="repre_tec_eby_id[]"
                                                data-placeholder="@trans('forms.multiple.repres_tec_eby')"
                                                id="repre_tec_eby_id" multiple>
                                            @foreach($repres_tec_eby as $key => $value )
                                                <option value="{{$key}}"
                                                        @if(isset($repres_tec_eby_old) && in_array($key, $repres_tec_eby_old)) selected @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.representante_legal') @trans('forms.contratista')</label>
                                        <input type='text' value="{{$contrato->repre_leg_contratista}}"
                                               id='repre_leg_contratista' name='repre_leg_contratista'
                                               class='form-control'
                                               placeholder='@trans('contratos.representante_legal') @trans('forms.contratista')'>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.representante_tecnico') @trans('forms.contratista')</label>
                                        <input type='text' value="{{$contrato->repre_tec_contratista}}"
                                               id='repre_tec_contratista' name='repre_tec_contratista'
                                               class='form-control'
                                               placeholder='@trans('contratos.representante_tecnico') @trans('forms.contratista')'>
                                    </div>
                                </div>
                            </div>

                            <div class="float-left" style="float:left; width: 100%;">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group form-group-chosen">
                                        <label for="organo_aprobador_id">@trans('contratos.organo_aprobador')</label>
                                        <select class="form-control" name="organo_aprobador_id"
                                                id="organo_aprobador_id">
                                            @foreach($organos as $key => $value)
                                                <option value="{{$key}}"
                                                        @if($contrato->organo_aprobador_id == $key) selected @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.resoluc_adjudic')</label>

                                        <input type='text' value="{{$contrato->resoluc_adjudic}}"
                                           id='resoluc_adjudic'
                                           name='resoluc_adjudic'
                                           class='form-control'
                                           placeholder='@trans('contratos.resoluc_adjudic')'
                                        >
                                    </div>
                                </div>

                                @if($contrato->borrador || $contrato->fecha_acta_inicio == null)
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>@trans('contratos.fecha_acta_inicio')</label>
                                            <input type='text' value="{{$contrato->fecha_acta_inicio}}"
                                               id='fecha_acta_inicio' name='fecha_acta_inicio'
                                               class='form-control input-datepicker-m-y'
                                               placeholder='@trans('contratos.fecha_acta_inicio')'
                                               @if($contrato->fecha_acta_inicio) readonly="readonly" @endif>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        @else

                            <div class="col-md-6 col-sm-12">
                                <div class="form-group form-group-chosen">
                                    <label for="organo_aprobador_id">@trans('contratos.organo_aprobador')</label>
                                    <select class="form-control" name="organo_aprobador_id" id="organo_aprobador_id">
                                        @foreach($organos as $key => $value)
                                            <option
                                                value="{{$key}}"
                                                @if($contrato->organo_aprobador_id == $key) selected @endif
                                            >
                                                {{$value}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12">
                                <div class="form-group form-group-chosen">
                                    <label for="repre_tec_eby_id">@trans('contratos.representante_tecnico_eby')</label>
                                    <select class="form-control"
                                        name="repre_tec_eby_id[]" id="repre_tec_eby_id"
                                        data-placeholder="@trans('forms.multiple.repres_tec_eby')"
                                        multiple
                                    >
                                        @foreach($repres_tec_eby as $key => $value )
                                            <option value="{{$key}}"
                                                @if(isset($repres_tec_eby_old) && in_array($key, $repres_tec_eby_old)) selected @endif
                                            >
                                                {{$value}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>@trans('contratos.representante_legal') @trans('forms.contratista')</label>
                                    <input
                                        type="text"
                                        value="{{$contrato->repre_leg_contratista}}"
                                        id="repre_leg_contratista"
                                        name="repre_leg_contratista"
                                        class="form-control"
                                        placeholder="@trans('contratos.representante_legal') @trans('forms.contratista')"
                                    >
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>@trans('contratos.representante_tecnico') @trans('forms.contratista')</label>

                                    <input
                                        type="text" value="{{$contrato->repre_tec_contratista}}"
                                        id="repre_tec_contratista"
                                        name="repre_tec_contratista"
                                        class="form-control"
                                        placeholder="@trans('contratos.representante_tecnico') @trans('forms.contratista')"
                                    >
                                </div>
                            </div>

                            <div @if(!Auth::user()->usuario_causante) class="col-md-6 col-sm-12"
                                 @else class="col-md-12 col-sm-12" @endif style="height: 109px;">
                                <div class="form-group">
                                    <label>@trans('contratos.resoluc_adjudic')</label>

                                    <input type="text"
                                       value="{{$contrato->resoluc_adjudic}}"
                                       id="resoluc_adjudic"
                                       name="resoluc_adjudic"
                                       class="form-control"
                                       placeholder="@trans('contratos.resoluc_adjudic')"
                                    >
                                </div>
                            </div>
                        @endif

                        @if($contrato->id && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                        @else
                            @if(!isset($isAdenda))
                                @if(!Auth::user()->usuario_causante)
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group form-group-chosen">
                                            <label for="causante_id">@trans('forms.causante')</label>
                                            <select class="form-control" name="causante_id" id="causante_id">
                                                @foreach($causantes as $key => $value )
                                                    <option value="{{$key}}"
                                                            @if($contrato->causante_id == $key) selected @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.fecha_oferta')</label>
                                        <input type='text' value="{{$contrato->fecha_oferta}}" id='fecha_oferta'
                                               name='fecha_oferta' class='form-control input-datepicker-m-y'
                                               placeholder='@trans('contratos.fecha_oferta')'>
                                    </div>
                                </div>
                            @else
                                <input type="text" class="form-control hidden" name="causante_id" id="causante_id"
                                       value="{{$contrato_padre->causante_id}}">
                                <input type='text' value="{{$contrato_padre->fecha_oferta}}" id='fecha_oferta'
                                       name='fecha_oferta' class='form-control input-datepicker-m-y hidden'
                                       placeholder='@trans('contratos.fecha_oferta')'>
                            @endif

                            @if($contrato->borrador || $contrato->fecha_acta_inicio == null)
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.fecha_acta_inicio')</label>

                                        <input
                                            type="text"
                                            value="{{$contrato->fecha_acta_inicio}}"
                                            id="fecha_acta_inicio"
                                            name="fecha_acta_inicio"
                                            class='form-control input-datepicker-m-y'
                                            placeholder='@trans('contratos.fecha_acta_inicio')'
                                            @if($contrato->fecha_acta_inicio) readonly="readonly" @endif
                                        >
                                    </div>
                                </div>
                            @endif

                        @endif
                        @if(!$contrato->borrador)
                            @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                @if($valueContratoMoneda->itemizado->borrador)
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <input
                                                type="hidden"
                                                id="definitivo"
                                                name="definitivo"
                                                value="1"
                                            >

                                            <label>@trans('contratos.monto_basico') {{$valueContratoMoneda->moneda->nombre_simbolo}} </label>

                                            <input
                                                type="text"
                                                class="form-control currency"
                                                value="@toDosDec($valueContratoMoneda->monto_inicial)"
                                                id="monto_inicial_{{$keyContratoMoneda + 1}}"
                                                name="monto_inicial[{{$valueContratoMoneda->id}}]"
                                                placeholder="@trans('contratos.monto_basico')"
                                            >
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <div class="col-md-12 col-sm-12">
                            <div class="form-group form-group-chosen">
                                <label for="estado_id">@trans('forms.estado')</label>

                                <select class="form-control" name="estado_id" id="estado_id">
                                    @foreach($estados as $key => $value)
                                        <option
                                            value="{{$key}}"
                                            @if($contrato->estado_id == $key) selected @endif
                                            data-ejecucion="{{ ($value != trans('contratos.estados.contratos.adjudicada') && $value != trans('forms.select.estado')) ? '1' : '0' }}"
                                        >
                                            {{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($contrato->id && ($contrato->guardadoDefinitivo || $contrato->incompleto['status']) && $contrato->fecha_acta_inicio != null)
                            <div id="new_fecha_inicio">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@trans('contratos.fecha_acta_inicio')</label>

                                        <input
                                            type="text"
                                            value="{{$contrato->fecha_acta_inicio}}"
                                            id="fecha_acta_inicio"
                                             name="fecha_acta_inicio"
                                            class="form-control input-datepicker-m-y"
                                            placeholder="@trans('contratos.fecha_acta_inicio')"
                                            @if($contrato->has_cronogramas) disabled @endif
                                        >

                                        @if($contrato->has_cronogramas)
                                            <input
                                                type="hidden"
                                                id="fecha_acta_inicio"
                                                name="fecha_acta_inicio"
                                                value="{{$contrato->fecha_acta_inicio}}"
                                            >
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($contrato->id != null && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group"></div>
                            </div>
                        @else
                            <div class="col-md-12 col-sm-12 error_rad_simple_compuesto">
                                <div class="form-group" id="radio_title">
                                    <label>@trans('contratos.plazo_obra')</label>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12 radio-options">
                                <div class="container_input_check mb-1">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group" id="div_simple_compuesto">
                                            @foreach($plazos as $key => $value)
                                                <label class="col-md-6" id="simple">
                                                    <input type="radio" name="plazo_id" class="toggleSelect"
                                                           value="{{$value->id}}"
                                                           @if($value->id == $contrato->plazo_id) checked @endif>
                                                    {{trans_choice('contratos.plazo.' . $value->nombre, 2)}}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 p-0">
                                        <div class="form-group">
                                            <input type='number' value="{{$contrato->plazo}}" id='plazo' name='plazo'
                                                   class='form-control' placeholder='@trans('contratos.plazo_obra')'>
                                            @if((isset($isAdenda) && $contrato->is_adenda_ampliacion) || $contrato->is_ampliacion)
                                                <small
                                                    class="msg_sugerencia_input text-success">@trans('adendas.completar_plazo')</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Clone Monedas --}}
                            @if(!$contrato->tiene_contratos_monedas)
                                <div class="col-md-12 col-sm-12 clone-container radio-options wrapper_1 z-index-auto">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group form-group-chosen m-0">
                                            <label for="moneda_id">@trans('forms.moneda')</label>
                                            <select class="form-control moneda_id" name="moneda_id[1]" id="moneda_id_1">
                                                @foreach($monedas as $key => $value)
                                                    <option value="{{$key}}"
                                                            @if (strpos($value, '(USD)') !== false) data-dolar="1"
                                                            @else data-dolar="0" @endif>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>@trans('contratos.monto_basico')</label>
                                            <input type="text" class="form-control currency" id='monto_inicial_1'
                                                   name='monto_inicial[1]' value=""
                                                   placeholder="@trans('contratos.monto_basico')">
                                            @if((isset($isAdenda) && $contrato->is_adenda_ampliacion))
                                                <small
                                                    class="msg_sugerencia_input text-success">@trans('adendas.completar_monto')</small>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!isset($isAdenda))
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>@trans('contratos.tasa_cambio')</label>
                                                <input type="text" class="form-control currency" id='tasa_cambio_1'
                                                       name='tasa_cambio[1]' value=""
                                                       placeholder="@trans('contratos.tasa_cambio')">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <a href="#" class="btn btn-primary pull-right add_button" data-id="1">
                                    @trans('forms.agregar_moneda')<i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                            @else
                                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                    <div
                                        class="col-md-12 col-sm-12 clone-container radio-options wrapper_{{$keyContratoMoneda + 1}} @if($keyContratoMoneda > 0) can-delete @endif z-index-auto">
                                        @php($es_dolar = false)
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group form-group-chosen">
                                                <label for="moneda_id">@trans('forms.moneda')</label>
                                                <select class="form-control moneda_id"
                                                        name="moneda_id[{{$keyContratoMoneda + 1}}]"
                                                        id="moneda_id_{{$keyContratoMoneda + 1}}">
                                                    @foreach($monedas as $key => $value)
                                                        <option value="{{$key}}"
                                                                @if($valueContratoMoneda->moneda_id == $key) selected
                                                                @endif
                                                                @if (strpos($value, '(USD)') !== false) data-dolar="1"
                                                                @else data-dolar="0" @endif>{{$value}}</option>
                                                        @if(strpos($value, '(USD)') !== false && $valueContratoMoneda->moneda_id == $key) @php($es_dolar = true) @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label>@trans('contratos.monto_basico')</label>
                                                <input type="text" class="form-control currency"
                                                       value="@toDosDec($valueContratoMoneda->monto_inicial)"
                                                       id='monto_inicial_{{$keyContratoMoneda + 1}}'
                                                       name='monto_inicial[{{$keyContratoMoneda + 1}}]'
                                                       placeholder="@trans('contratos.monto_basico')">
                                                @if((isset($isAdenda) && $contrato->is_adenda_ampliacion))
                                                    <small
                                                        class="msg_sugerencia_input text-success">@trans('adendas.completar_monto')</small>
                                                @endif
                                            </div>
                                        </div>

                                        @if(!isset($isAdenda))
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group @if($es_dolar) hidden @endif">
                                                    <label>@trans('contratos.tasa_cambio')</label>
                                                    <input type="text" class="form-control currency"
                                                           value="@toDosDec($valueContratoMoneda->tasa_cambio)"
                                                           @if($es_dolar) readonly
                                                           @endif id='tasa_cambio_{{$keyContratoMoneda + 1}}'
                                                           name='tasa_cambio[{{$keyContratoMoneda + 1}}]'
                                                           placeholder="@trans('contratos.tasa_cambio')">
                                                </div>
                                            </div>
                                        @endif

                                        @if($keyContratoMoneda > 0)
                                            <a href="javascript:void(0)"
                                               class="btn btn-danger remove_button remove_button_"><i
                                                    class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
                                    </div>
                                @endforeach

                                <a href="#" class="btn btn-primary pull-right add_button"
                                   data-id="{{$keyContratoMoneda + 1}}">
                                    @trans('forms.agregar_moneda')<i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                            @endif

                            {{-- FIN Clone Monedas --}}

                            <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                                <div class="form-group mb-1">
                                    <label class="fixMargin4">
                                        <div class="checkbox noMarginChk">
                                            <div class="btn-group chk-group-btn" data-toggle="buttons">
                                                <label
                                                    class="btn btn-primary btn-sm @if($contrato->requiere_garantia) active @endif">
                                                    <input autocomplete="off" class="triggerClickChk" type="checkbox"
                                                           name="requiere_garantia" id="requiere_garantia"
                                                           @if($contrato->requiere_garantia) checked @endif>
                                                    <span class="glyphicon glyphicon-ok"></span>
                                                </label>
                                                {!!trans('contratos.requiere_garantia')!!}
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @if(!isset($isAdenda))
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group ">
                                                <label>@trans('contratos.anticipo')</label>
                                                <input class="form-control porcentaje-mask"
                                                       value="@toDosDec($contrato->anticipo)" type="text"
                                                       name="anticipo" id="anticipo"
                                                       placeholder="@trans('contratos.anticipo')">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group ">
                                                <label>@trans('contratos.fondo_reparo')</label>
                                                <input class="form-control porcentaje-mask"
                                                       value="@toDosDec($contrato->fondo_reparo)" type="text"
                                                       name="fondo_reparo" id="fondo_reparo"
                                                       placeholder="@trans('contratos.fondo_reparo')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <input class="form-control porcentaje-mask hidden"
                                       value="@toDosDec($contrato_padre->anticipo)" type="text" name="anticipo"
                                       id="anticipo" placeholder="@trans('contratos.anticipo')">
                                <input class="form-control porcentaje-mask hidden"
                                       value="@toDosDec($contrato_padre->fondo_reparo)" type="text" name="fondo_reparo"
                                       id="fondo_reparo" placeholder="@trans('contratos.fondo_reparo')">
                            @endif

                        @endif

                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="adjunto" id="adjunto">
          <span>
           @trans('forms.adjuntos') </span>
                                </label>
                                <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
                                <input type="file" name="adjunto[]" id="adjunto" class="file_upload"
                                       accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx"
                                       multiple>
                            </div>

                            @if($contrato->adjuntos != null)
                                @foreach($contrato->adjuntos as $key => $adjunto)
                                    <span id="adjunto_anterior" class="hide-on-ajax">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item"
               target="_blank">{{$adjunto->adjunto_nombre}}</a>
          </span>
                                    <br>
                                @endforeach
                            @endif
                        </div>

                        <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
                            <div class="text-right">
                                <a class="btn btn-small btn-success"
                                   href="{{ route('contratos.index') }}">@trans('forms.volver')</a>
                                @if($contrato->id != null && ($contrato->guardadoDefinitivo OR $contrato->incompleto['status']))
                                    {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar')) }}
                                @else
                                    {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar')) }}
                                    {{ Form::submit(trans('forms.guardar_borrador'), array('class' => 'btn btn-basic pull-right borrador')) }}
                                @endif
                            </div>
                        </div>

    </form>

</div>
