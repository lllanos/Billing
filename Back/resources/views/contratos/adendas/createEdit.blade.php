@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
                <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
                <li>
                    <a href="{{route('contratos.ver', ['id' => $adenda->contrato_padre->id]) }}">@trans('forms.contrato') {{$adenda->contrato_padre->expediente_madre}}</a>
                </li>
                <li class="active">
                    @if($adenda->id == null) @trans('index.solicitar') @else @trans('index.editar') @endif @trans('contratos.adenda')</li>
            </ol>
            <div class="page-header">
                <h3 class="page_header__titulo">
                    <div class="titulo__contenido">
                        @if($adenda->id == null)
                            @trans('index.solicitar') @trans('contratos.adenda') @trans('index.de')
                        @else
                            @trans('index.editar')
                            @trans('contratos.tipo_contrato.' . $tipo_contrato->nombre) @trans('index.de')
                        @endif
                        {{$adenda->contrato_padre->expediente_madre}}
                    </div>

                    @if($adenda->id != null)
                        @permissions(($contrato->tipo_contrato->nombre . '-view'))
                        <div class="buttons-on-title">
                            <div class="button_desktop">
                                <a class="btn btn-success pull-right"
                                   href="{{route('adenda.ver', ['id' => $contrato->id]) }}">
                                    @trans('index.ver') @trans('index.adenda')
                                </a>
                            </div>
                            <div class="button_responsive">
                                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-label="@trans('index.opciones')">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li>
                                            <a href="{{route('adenda.ver', ['id' => $contrato->id]) }}">
                                                @trans('index.ver') @trans('index.adenda')
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endpermission
                    @endif
                </h3>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    @if($adenda->id == null)
                        <div class="col-md-12">
                            <div class="form-group select-create">
                                <label for="tipo_adenda">{{trans('index.tipo_adenda')}}</label>
                                <select class="form-control" name="tipo_adenda" id="tipo_adenda" required
                                        data-action="{{route('adenda.getViews', ['contrato_id' => $contrato_id, 'tipo_contrato' => ':tipo_contrato'])}}">
                                    <option disabled selected value> @trans('forms.select.tipo_adenda')</option>
                                    @foreach($select_options as $key => $value )
                                        <option value="{{$key}}">@trans('contratos.tipo_contrato.' . $value)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12" id="tipo_adenda_div">
                        @if($adenda->id != null)
                            @include('contratos.adendas.forms.' . $tipo_contrato->nombre)
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(() => {

            $('#tipo_adenda').unbind('change').on('change', function () {
                var action = $(this).data('action');
                action = action.replace(':tipo_contrato', $(this).find(":selected").val());
                $.get(action, function (data) {
                    $('#tipo_adenda_div').html(data);
                    applyEditFecha();
                    applyAllAdenda();
                });
            });

            applyEditFecha();
            applyAllAdenda();
        });

        applyEditFecha = () => {
            var inicial = $("#estado_id").val();
            if (inicial !== "2") {
                $("#new_fecha_inicio").hide();
            }

            $("#estado_id").change(function () {
                var option = $(this).val();
                if (option === "2") {
                    $("#new_fecha_inicio").show();
                } else {
                    $("#new_fecha_inicio").hide();
                }
            });

            applyAll();
        };

        applyAllAdenda = () => {
            applyAll();
        };
    </script>
@endsection
