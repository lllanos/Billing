<div class="modal-header">
    <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="fa fa-times fa-2x"></span>
    </button>

    <h4 class="modal-title">
        @trans('index.agregar') @trans('anticipos.anticipo')
    </h4>
</div>

<div class="anticipoAddModal-content">
    <form id="form-ajax"
        method="POST"
        class="formAnticipo"
        action="{{route('anticipo.store')}}"
        data-action="{{route('anticipo.store')}}"
    >
        <input type="hidden" name="contrato" value="{{$contrato->id}}">
        {{ csrf_field() }}

        <!-- Modal body -->
        <div class="modal-body pt-1 pb-1">
            <div class="modalContentScrollable">
                <div class="panel panel-default">
                    <div class="panel-body container_detalle_itemizado pt-0 pb-0">
                        <div id="formulario">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label>{{trans('forms.fecha')}}</label>

                                        <input type='text'
                                            id='anticipo_fecha'
                                            name='anticipo_fecha'
                                            class='form-control input-datepicker-m-y'
                                            placeholder="{{trans('forms.fecha')}}"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group mb-2">
                                        <label>{{trans('forms.descripcion')}}</label>

                                        <input type='text'
                                            id='anticipo_descripcion'
                                            name='anticipo_descripcion'
                                            class='form-control'
                                            placeholder="{{trans('forms.descripcion')}}"
                                            required
                                        >
                                    </div>
                                </div>

                                @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                                    <input
                                        type="hidden"
                                        name="anticipo_contratos_monedas[]"
                                        value="{{$valueContratoMoneda->id}}"
                                    >

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group mb-2">
                                            <label>
                                                {{$valueContratoMoneda->moneda->nombre}} ({{$valueContratoMoneda->moneda->simbolo}})
                                            </label>

                                            <input type='text'
                                                id='anticipo_total'
                                                name='anticipo_total[]'
                                                class="form-control currency"
                                                placeholder="{{trans('forms.total')}}"
                                                required="required"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group mb-2">
                                            <label>{{trans('forms.porcentaje')}}</label>

                                            <input type='text'
                                                id='anticipo_porcentaje'
                                                name='anticipo_porcentaje[]'
                                                class="form-control porcentaje-mask"
                                                value="00.0%"
                                                required
                                            >

                                            <small class="msg_sugerencia_input text-success">
                                                {{trans('anticipos.completar_porcentaje')}}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer no-padding-bottom footer-original">
            <div class="col-md-12">
                <button type="submit"
                    class="btn btn-primary submitItemizado pull-right"
                    id="btn_guardar_anticipo"
                    data-accion="guardar"
                >
                    {{trans('index.guardar')}}
                </button>
            </div>
        </div>
    {{ Form::close() }}
</div>
e
