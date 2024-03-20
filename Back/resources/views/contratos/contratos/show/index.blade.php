@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
        @if($contrato->is_adenda)
          <li><a href="{{route('contratos.ver', ['id' => $contrato->contrato_padre->id]) }}">@trans('forms.contrato') {{$contrato->contrato_padre->expediente_madre}}</a></li>
          <li class="active">@trans('index.adenda') {{$contrato->numero_contrato}}</li>
        @else
          <li class="active">@trans('index.contrato') {{$contrato->expediente_madre}}</li>
        @endif
      </ol>

      <div class="page-header">
        <h3 class="page_header__titulo">
          @if($contrato->is_contrato)
            @include('contratos.contratos.show.header_contrato')
          @else
            @include('contratos.contratos.show.header_adenda')
          @endif
        </h3>
      </div>
    </div>
  </div>

  <input type='text' id='accion' name='accion' class='hidden' value="{{$accion}}">

  <div class="row">
    <div class="col-md-12">
      @include('contratos.contratos.show.detalle')
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">

      @if(!$contrato->borrador && !$contrato->doble_firma)

        @permissions('anticipos-view', 'anticipos-create')
          @if($contrato->hasSeccion('anticipos'))
            <div id="anticipos_container">
              @include('contratos.contratos.show.anticipos.index')
            </div>
          @endif
        @endpermission

        @permissions('itemizado-view', 'itemizado-edit', 'itemizado-manage')
          @if($contrato->hasSeccion('itemizado'))
            <div id="itemizado_container">
              @include('contratos.contratos.show.itemizado.index')
            </div>
          @endif
        @endpermission

        @permissions('analisis_precios-list', 'analisis_precios-edit')
          @if($contrato->hasSeccion('analisis_precios'))
            <div id="analisis_precios_container">
              @include('contratos.contratos.show.analisis_precios.index')
            </div>
          @endif
        @endpermission

        @permissions('cronograma-view', 'cronograma-edit')
          @if($contrato->hasSeccion('cronograma'))
            <div id="cronograma_container">
              @include('contratos.contratos.show.cronograma.index')
            </div>
          @endif
        @endpermission

        @permissions('polinomica-view', 'polinomica-edit')
          @if($contrato->hasSeccion('polinomica'))
            <div id="polinomica_container">
              @include('contratos.contratos.show.polinomica.index')
            </div>
          @endif
        @endpermission

        @permissions('empalme-manage')
          @if($contrato->hasSeccion('empalme'))
            <div id="empalme_container">
              @include('contratos.contratos.show.empalme.index')
            </div>
          @endif
        @endpermission

        @permissions('adenda_certificacion-view', 'adenda_certificacion-create')
          @if($contrato->hasSeccion('adendaCertificacion'))
            <div id="adendaCertificacion_container">
              @include('contratos.contratos.show.adendaCertificacion.index')
            </div>
          @endif
        @endpermission

        @permissions('adenda_ampliacion-view', 'adenda_ampliacion-create')
          @if($contrato->hasSeccion('adendaAmpliacion'))
            <div id="adendaAmpliacion_container">
              @include('contratos.contratos.show.adendaAmpliacion.index')
            </div>
          @endif
        @endpermission

        @permissions('ampliacion-view', 'ampliacion-create')
          @if($contrato->hasSeccion('ampliacion'))
            <div id="ampliacion_container">
              @include('contratos.contratos.show.ampliacion.index')
            </div>
          @endif
        @endpermission

        @permissions('reprogramacion-view', 'reprogramacion-create')
          @if($contrato->hasSeccion('reprogramacion'))
            <div id="reprogramacion_container">
              @include('contratos.contratos.show.reprogramacion.index')
            </div>
          @endif
        @endpermission

        @permissions('certificado-list', 'certificado-edit')
          @if($contrato->hasSeccion('certificados'))
            <div id="certificados_container">
              @include('contratos.contratos.show.certificados.index')
            </div>
          @endif
        @endpermission
      @endif

    </div>
  </div>

@endsection

@section('modals')
  @include('contratos.contratos.show.modals.modals')
  @include('contratos.certificados.solicitudes.modals.rechazar')
@endsection

@section('js')
<script type="text/javascript">
    window.applySortable = () => {
        var order = null;
        var orderParent = null;
        var orderChild = null;
        var orderSubChild = null;

        $('.sort_parent_content').sortable({
            handle: '.handle',
            items: '> div:not(.cancel)',
            axis: 'y',
            opacity: 0.7,
            create: function (event, ui) {
                order = $(this).sortable('toArray');
            },
            update: function (event, ui) {
                order = $(this).sortable('toArray');
            },
            stop: function (event, ui) {
                updateOrder();
            }
        });

        $('.parent_sort').sortable({
            handle: '.parent_handle',
            items: '> div:not(.cancel)',
            connectWith: $(this),
            opacity: 0.7,
            axis: 'y',
            create: function (event, ui) {
                orderParent = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            update: function (event, ui) {
                orderParent = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            stop: function (event, ui) {
                updateOrder();
            }
        });

        $('.child_sort').sortable({
            handle: '.child_handle',
            items: '> div:not(.cancel)',
            connectWith: $(this),
            opacity: 0.7,
            axis: 'y',
            create: function (event, ui) {
                orderChild = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            update: function (event, ui) {
                orderChild = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            stop: function (event, ui) {
                updateOrder();
            }
        });

        $('.sub_child_sort').sortable({
            handle: '.sub_child_handle',
            items: '> div:not(.cancel)',
            connectWith: $(this),
            opacity: 0.7,
            axis: 'y',
            create: function (event, ui) {
                orderSubChild = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            update: function (event, ui) {
                orderSubChild = $(this).sortable('toArray', {
                    attribute: 'data-id',
                });
            },
            stop: function (event, ui) {
                updateOrder();
            }
        });

        function updateOrder() {
            url = "{{route('itemizado.regenerar')}}";

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'first': order,
                    'second': orderParent,
                    'third': orderChild,
                    'fourth': orderSubChild,
                },
                success: function (resp) {
                    if (resp.status == true) {
                        modalCloseToastSuccess(resp.message);
                        handleApplyCambiarVisualizacion($(this), 'itemizado');
                    }
                }
            });
        };
    }

    let cant = 0;

    $(document).ready(() => {

        if ($('#accion').val() != null && $('#accion').val() != "") {
            $("div[id*=headingOne]").not('#headingOne-' + $('#accion').val()).each(function () {
                $(this).find('h4 a.btn_acordion').click();
            });

            if ($('#headingOne-' + $('#accion').val()).length) {
                $('html, body').scrollTop($('#headingOne-' + $('#accion').val()).position().top);
            }
        }

        $(document).on("click", "a.action", function (e) {
            e.preventDefault();
            loadingToggle();

            let url = $(this).attr("href");

            $.get(url, function (data) {

                console.log(data);

                if (data.status === true) {
                    location.reload();
                    $.each(data.message, (i, e) => {
                        modalCloseToastSuccess(e);
                    });
                }
                else {
                    loadingToggle();

                    $.each(data.errores, (i, e) => {
                        modalCloseToastError(e);
                    });
                }
            });
        });

        poliInit();

        $('.submit').off('click').on('click', function (e) {
            var accion = $(this).data('accion');

            if (accion == 'guardar') {
                $('#borrador').val(0);
            }
            else {
                $('#borrador').val(1);
            }

            $('#hidden_submit').click()
            applyAll();
        });

        $(document).on("click", ".btn_add_itemizado_item", function () {
            url = "{{route('itemizado.getViews', ['id' => ':id', 'accion' => 'add', 'item_id' => ':item_id'])}}"

            var $itemizado_id = $(this).data('itemizado');
            url = url.replace(':id', $itemizado_id);
            url = url.replace(':item_id', $(this).data('id'));

            $('#itemizadoAddModal').find('.modal-content').html('');
            {{-- headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, --}}
            var this_btn = $(this);

            $.get(url, function (data) {
                $('#itemizadoAddModal').find('.modal-content').html(data);

                var $padre_id = this_btn.data('padre_id');
                $(".modal-body #itemizado_padre_id").val($padre_id).data('value', $padre_id);

                if (($padre_id == null) || ($padre_id == "")) {
                    $(".row-itemizado-sub").show();
                    $(".row-itemizado-amount").hide();
                }
                else {
                    $(".row-itemizado-sub").show();
                    $(".row-itemizado-amount").hide();
                }

                var $nivel = this_btn.data('nivel');
                $(".modal-body #itemizado_nivel").val($nivel).data('value', $nivel);

                if ($nivel == 3) {
                    $("#itemizado_item_sub").prop('checked', false);
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").show();
                    $(".modal-body #itemizado_tipo_id").val('2').data('value', '2');
                    $(".modal-body #itemizado_item_responsable").prop('required', true)
                    //                                            .chosen()
                    //                                            .trigger("chosen:updated");
                }
                applyAll();
                applyAllContrato();
            });
        });

        $(document).on("click", ".btn_clone_itemizado_item", function () {
            url = "{{route('itemizado.getViews', [
                'id' => ':id',
                'accion' => 'clone',
                'item_id' => ':item_id'
            ])}}"

            var $itemizado_id = $(this).data('itemizado');

            url = url.replace(':id', $itemizado_id);
            url = url.replace(':item_id', $(this).data('id'));

            $('#itemizadoAddModal').find('.modal-content').html('');

            var this_btn = $(this);

            $.get(url, function (data) {
                $('#itemizadoAddModal').find('.modal-content').html(data);

                var $padre_id = this_btn.data('padre_id');

                $(".modal-body #itemizado_padre_id").val($padre_id).data('value', $padre_id);

                if (($padre_id == null) || ($padre_id == "")) {
                    $(".row-itemizado-sub").show();
                    $(".row-itemizado-amount").hide();
                }
                else {
                    $(".row-itemizado-sub").show();
                    $(".row-itemizado-amount").hide();
                }

                var $nivel = this_btn.data('nivel');
                $(".modal-body #itemizado_nivel").val($nivel).data('value', $nivel);

                if ($nivel == 3) {
                    $("#itemizado_item_sub").prop('checked', false);
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").show();
                    $(".modal-body #itemizado_tipo_id").val('2').data('value', '2');
                    $(".modal-body #itemizado_item_responsable").prop('required', true)
                }

                applyAll();
                applyAllContrato();
            });
        });

        $(document).on("change", ".itemizado_item_sub", function () {
            $('.toggleHidden:not(.hidden) :input')
            if (this.checked) {

                $(".row-itemizado-amount").hide();
                $(".modal-body #itemizado_tipo_id").val('1').data('value', '1');
                $(".modal-body #itemizado_item_responsable").prop('required', false)
                //                                            .chosen()
                //                                            .trigger("chosen:updated");

                $('.toggleHidden :input').each(function (i, e) {
                    $(this).attr('required', false);
                });
            }
            else {
                $(".row-itemizado-amount").show();
                $(".modal-body #itemizado_tipo_id").val('2').data('value', '2');
                // $(".modal-body #itemizado_item_responsable").prop('required', true)
                //                                              .chosen()
                //                                              .trigger("chosen:updated");

                $('.toggleHidden:not(.hidden) :input').each(function (i, e) {
                    $(this).attr('required', true);
                });
            }
            applyAll();
        });

        var this_btn = $(this);

        $(document).on("click", ".btn_edit_itemizado_item", function () {
            var this_btn = $(this);

            var $item_id = this_btn.data('id');
            url = "{{route('itemizado.getItem', ['item_id' => ':item_id'])}}"
            url = url.replace(':item_id', $item_id);
            // Borro form anterior
            $('#itemizadoAddModal').find('.modal-content').html('');

            request = $.ajax({
                url: url,
                type: "get"
            });

            request.done(function (response, textStatus, jqXHR) {
                // Form al modal
                $('#itemizadoAddModal').find('.modal-content').html(response['view']);

                var $padre_id = this_btn.data('padre_id');
                $(".modal-body #itemizado_padre_id").val($padre_id).data('value', $padre_id);

                if ($padre_id == null) {
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").hide();
                }
                else {
                    $(".row-itemizado-sub").show();
                    $(".row-itemizado-amount").hide();
                }

                var $itemizado_id = this_btn.data('itemizado');
                $(".modal-body #itemizado_id").val($itemizado_id).data('value', $itemizado_id);

                var $nivel = this_btn.data('nivel');
                $(".modal-body #itemizado_nivel").val($nivel).data('value', $nivel);

                if ($nivel == response['last_level']) {
                    $("#itemizado_item_sub").prop('checked', false);
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").show();
                    $(".modal-body #itemizado_tipo_id").val(response['opciones']['tipo_ultimo_nodo']).data('value', response['opciones']['tipo_ultimo_nodo']);
                }

                $(".modal-body #itemizado_item_nombre").val(response['item']['descripcion'])
                    .data('value', response['item']['descripcion']);

                $(".modal-body #itemizado_item_item")
                    .val(response['item']['item'])
                    .data('value', response['item']['item']);

                $(".modal-body #itemizado_tipo_id").val(response['item']['tipo_id'])
                    .data('value', response['item']['tipo_id']);

                if (response['item']['tipo_id'] != response['opciones']['tipo_ultimo_nodo']) {
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").hide();
                }
                else {
                    $(".row-itemizado-sub").hide();
                    $(".row-itemizado-amount").show();
                    if (response['item']['categoria_id'] == response['opciones']['categoria_ajuste']) {
                        $(".modal-body #itemizado_item_importe_total").val(response['item']['monto_total'])
                            .data('value', response['item']['monto_total'])
                            .prop('required', true);

                        $(".modal-body #itemizado_item_unidad_medida").chosen().prop('required', false);
                        $(".modal-body #itemizado_item_cantidad").prop('required', false);
                        $(".modal-body #itemizado_item_importe_unitario").prop('required', false);
                    }
                    else {
                        $(".modal-body #itemizado_item_importe_total").prop('required', false);

                        $(".modal-body #itemizado_item_cantidad").prop('required', true)
                            .val(response['item']['cantidad'])
                            .data('value', response['item']['cantidad']);

                        $(".modal-body #itemizado_item_importe_unitario").prop('required', true)
                            .val(response['item']['monto_unitario'])
                            .data('value', response['item']['monto_unitario']);

                        $(".modal-body #itemizado_item_unidad_medida").chosen()
                            .val(response['item']['unidad_medida_id'])
                            .prop('required', true)
                            .trigger("chosen:updated");

                        $(".radio-unidad_medida").click();
                        $(".toggleHidden").toggleClass('hidden');
                    }

                    $(".modal-body #itemizado_item_responsable").val(response['item']['responsable_id'])
                        .prop('required', true)
                    //                                            .chosen()
                    //                                            .trigger("chosen:updated");
                }
                applyAll();
                applyAllContrato();
            });

            request.fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Ocurrio un error: " + textStatus, errorThrown);
            });
        });

        applyAllContrato();

        if ($('#accion').val() != null && $('#accion').val() != "") {
            if ($('#headingOne-' + $('#accion').val()).length) {
                $('#headingOne-' + $('#accion').val()).find('h4 a.btn_acordion').click();
            }
        }
        applyAll();

        $(document).on("change", ".modal-body #itemizado_item_categoria_id", function () {
            $('.toggleHidden :input').each(function (i, e) {
                $(this).attr('required', false);
            });
            if (this.value == 'ajuste_alzado') {
                $(".modal-body #itemizado_item_importe_total").prop('required', true);
            }
            else if (this.value == 'unidad_medida') {
                $(".modal-body #itemizado_item_unidad_medida").chosen().prop('required', true);
                $(".modal-body #itemizado_item_cantidad").prop('required', true);
                $(".modal-body #itemizado_item_importe_unitario").prop('required', true);
            }
        });
    });

    //#region Polinomica
    applyCloneComposicion = () => {
        $('.add_button').unbind('click').click(function () {
            var polinomica_id = $(this).data('polinomica');
            var moneda_id = $(this).data('moneda');

            let wrapper = $('.wrapper_' + polinomica_id);

            $(this).addClass('hidden');
            $(this).parent().find('.remove_button').removeClass('hidden');
            cant++;
            const htmlTemplate = `
        <div class="input_chosen_clonados clon_polinomica clon_polinomica_no_borrador can_delete">
          <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="form-group ">
                <input class="form-control" type="text" placeholder="@trans('forms.nombre')"
                  name="polinomicas[${polinomica_id}][${cant}][nombre]" id="polinomicas_nombre_${polinomica_id}_${cant}"
                >
              </div>
            </div>
            @foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
            <div class="col-md-5 col-sm-5 col-xs-12 hidden poli-{{$valueContratoMoneda->polinomica_id}}-indice-{{$valueContratoMoneda->moneda_id}}">
                <div class="form-group ">
                  <select class="form-control" name="polinomicas[${polinomica_id}][${cant}][tabla_indices_id]" id="tabla_indices_id_${polinomica_id}_${cant}" >
                    <option disabled selected value> {{ trans('forms.select.indice') }}</option>
                    @if(isset($indices[$valueContratoMoneda->moneda_id]) && count($indices[$valueContratoMoneda->moneda_id]) > 0)
            @foreach($indices[$valueContratoMoneda->moneda_id] as $keyIndice => $valueIndice)
            <option value="{{ $valueIndice->id }}" >{{ $valueIndice->nombre_full }} </option>
                      @endforeach
            @endif
            </select>
            </div>
          </div>
@endforeach
            <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group">
                    <input class="form-control" type="text"
                      name="polinomicas[${polinomica_id}][${cant}][porcentaje]" id="polinomicas_porcentaje_${polinomica_id}_${cant}"
                    placeholder="@trans('forms.factor_incidencia')" data-inputmask="'mask': '9,9999'"
                  >
                  <div class="container_btn_">
                    <a href="javascript:void(0)" class="btn btn-primary add_button add_button_composicion_poli" data-polinomica="${polinomica_id}" data-moneda="${moneda_id}"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0)" class="btn btn-danger remove_button" data-polinomica="${polinomica_id}" data-moneda="${moneda_id}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </div>
              </div>
            </div>
          </div>
        </div>`;

            $(wrapper).append(htmlTemplate);
            $(wrapper).find('.poli-' + polinomica_id + '-indice-' + moneda_id).removeClass('hidden');

            applyAccionesPolinomica();
            applyAll();
        });
    }

    applyDeletePolinomica = () => {
        $('.wrapper_no_borradores').on('click', '.remove_button', function (e) {
            e.preventDefault();
            var polinomica_id = $(this).data('polinomica');
            let wrapper = $('.wrapper_' + polinomica_id);
            let container_borrador = $(this).parents('.panel_polinomica_' + polinomica_id);
            $(this).parents('div.can_delete').remove();

            let container_btns_last = $(wrapper).find('.clon_polinomica:last').find('.container_btn_');

            if ($(wrapper).find('.clon_polinomica_no_borrador').length == 1 && $(wrapper).parents('.panel_polinomica').find('.container_input_chosen_originales').length == 0) {
                container_btns_last.children('.add_button').removeClass('hidden');
                container_btns_last.children('.add_button').removeClass('add_button_composicion_poli');
                container_btns_last.children('.remove_button').addClass('hidden');
            }
            else {
                container_borrador.find('.clon_polinomica:last').find('.container_btn_').children('.add_button').removeClass('hidden');
            }

            if ($(wrapper).find('.clon_polinomica_no_borrador').length == 0 && $(wrapper).parents('.panel_polinomica').find('.container_input_chosen_originales').length == 1) {
                $(wrapper).parents('.panel_polinomica').find('.container_input_chosen_originales').find('.remove_button').addClass('hidden');
                $(wrapper).parents('.panel_polinomica').find('.container_input_chosen_originales').find('.add_button').removeClass('add_button_composicion_poli');
            }
        });
    }

    poliInit = () => {
        $('.panel_polinomica').each(function () {
            if ($(this).find('.clon_polinomica_no_borrador').length == 1 && $(this).find('.clon_polinomica_borrador').length != 0) {
                $(this).find('.clon_polinomica_no_borrador').remove();
                $(this).find('.container_input_chosen_originales:last').find('.container_btn_').children('.add_button').removeClass('hidden');
                if ($(this).find('.clon_polinomica_borrador').length == 1) {
                    $(this).find('.container_input_chosen_originales:last').find('.container_btn_').children('.remove_button').addClass('hidden');
                    $(this).find('.container_input_chosen_originales:last').find('.container_btn_').children('.add_button').removeClass('add_button_composicion_poli');
                }
            }
        });
    }

    var applyAccionesPolinomica = () => {
        applyCloneComposicion();
        applyDeletePolinomica();

        $('#btn_guardar_borrador').unbind("click").on('click', function () {
            $('#borrador').val(1);
            $('#hidden_submit_polinomica').click();
        });

    };

    var applyConfirmableSubmit = () => {
        $('.btn-confirmable-submit').unbind("click").on('click', function () {
            var action = $(this).data('action');
            var form = $(this).data('form');

            BootstrapDialog.confirm({
                message: $(this).data('body'),
                type: BootstrapDialog.TYPE_WARNING,
                closable: true,
                draggable: true,
                btnCancelLabel: $(this).data('no'),
                btnOKLabel: $(this).data('si'),
                btnOKClass: 'btn-primary btn-dialog-OK',
                btnCancelClass: 'btn-link btn-dialog-Cancel',
                callback: function (result) {
                    // result true si presiono si, false si se cierra el dialog
                    if (result) {
                        $('#borrador').val(0);
                        if (form != undefined) {
                            $('#' + form).find('#borrador').val(0);
                            $('#' + form).attr('action', action);
                            $('#' + form).data('action', action);
                            $('#' + form).submit();
                        }
                        else {
                            $.get(action, function (resp) {
                                console.log(resp);
                            });
                        }
                    }
                }
            });
        });
    }
    //#endregion

    //#region Itemizado
    window.applyFormConfirmable = () => {
        $('.btn-confirmable-submit').each(function () {
            var form = '#' + $(this).data('form');

            $(form).off('submit').on('submit', function (e) {
                e.preventDefault();
                $('.help-block').remove();
                $('.form-group').removeClass('has-error');
                $('.errores-publicacion').html('<ul> </ul>');
                $('.errores-publicacion').addClass('hidden');
                var action = $(form).data('action');

                loadingToggle();
                $.ajax({
                    url: action,
                    type: 'POST',
                    dataType: 'json',
                    data: new FormData($(form)[0]),
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.status == true) {
                            modalCloseToastSuccess(resp.message);
                            if (resp.url != undefined) {
                                window.location.href = resp.url;
                            }
                            else {
                                location.reload();
                            }
                        }
                        else {
                            loadingToggle();
                            if (resp.errores) {
                                if (resp.error_container != undefined) {
                                    $(resp.error_container).removeClass('hidden');
                                    $.each(resp.errores, (i, error) => {
                                        error.forEach(function (e) {
                                            var html = `<li>${e}</li>`;
                                            $(resp.error_container).find('ul').append(html);
                                        });
                                    });
                                    $('html, body').scrollTop($(resp.error_container).parent().parent().parent().parent().offset().top);
                                }
                                else {
                                    $.each(resp.errores, (i, e) => {
                                        var html = `<span class="help-block">${e}</span>`;
                                        if ($('#' + i).closest('.form-group').length > 0) {
                                            $('#' + i).closest('.form-group').addClass('has-error');
                                        }
                                      {{-- else
                                          $('#' + i).addClass('help-outside'); --}}

                                      $(html).insertAfter('#' + i);

                                        $('.form-control').change(function () {
                                            if (i.includes('polinomicas_suma_')) {
                                                $(this).children().remove();
                                            }
                                            else {
                                                $(this).closest('.form-group').removeClass('has-error');
                                                $(this).closest('.form-group').find('.help-block').remove();
                                                $(this).closest('.form-group').parent().find('.help-outside').remove();
                                            }

                                        });
                                    });
                                }
                                applyAll();

                                if (resp.message.length > 0) {
                                    modalCloseToastError(resp.message);
                                }
                            }
                        }
                    }
                });

            });
        });
    }

    var applyModalCronograma = () => {
        $('.open-modal-ItemCronograma').unbind("click").click(function () {
            loadingToggle();
            var url = $(this).data('url');

            $.get(url, function (data) {
                $('#ModalItemCronograma').find('.modal-content').html(data);

                $('#ModalItemCronograma').modal('show');
                applyFormAjax('#form-ajax-ItemCronograma');
                loadingToggle();
            });
        });
    };

    var applyCambiarVisualizacion = () => {
        $('.visualizacion').unbind("click").click(function () {
            handleApplyCambiarVisualizacion($(this), null);
        });
    };

    var handleApplyCambiarVisualizacion = (jQuery, newSeccion) => {
        var seccion = (newSeccion == null) ? jQuery.data('seccion') : newSeccion;
        loadingToggleThis('#' + seccion + '_container');

        var url = "{{route('contrato.editar.getViews', ['id' => $contrato->id, 'seccion' => ':seccion',
                                                    'version' => ':version', 'visualizacion' => ':visualizacion'])}}";

        var opcion = jQuery.data('version');
        if (opcion != undefined) {
            $('#' + seccion + '_version').val(opcion);
        }
        else {
            opcion = jQuery.data('visualizacion');
            $('#' + seccion + '_visualizacion').val(opcion);
        }

        url = url.replace(":seccion", seccion)
            .replace(":version", $('#' + seccion + '_version').val())
            .replace(":visualizacion", $('#' + seccion + '_visualizacion').val());

        $.get(url, function (data) {
            $('#' + seccion + '_container').html(data.view);

            loadingToggleThis('#' + seccion + '_container');
            applyAll();

            applySortable();
            applyAllContrato();
            if (data.highcharts != false) {
                applyShowWidgets(data.highcharts, seccion);
            }
        }).fail(function () {
            loadingToggleThis('#' + seccion + '_container');
        });
    };

    var applyToggleUnidadMedida = () => {
        $('input[type=radio][name=itemizado_item_categoria_id]').unbind('change').change(function () {
            var tipo = $(this).val();
            // toggle required en toggleHidden visible antes del cambio
            $('.toggleHidden:not(.hidden)').find('select').attr('required', false);

            $(".toggleHidden").addClass('hidden');
            $(".toggleHidden." + tipo).removeClass('hidden');

            // toggle required en toggleHidden visible despues del cambio
            //  $('.toggleHidden:not(.hidden)').find('select').attr('required', true);
            applyAll();
        });
    }

    var applyShowWidgets = (name, seccion) => {
        url = "{{route('widget.contratos', ['name' => ':name', 'contrato_id' => $contrato->id, 'version' => ':version'])}}";
        url = url.replace(':name', name);
        url = url.replace(':version', $('#' + seccion + '_version').val());
        var nombre = name;

        $.ajax({
            url: url,
            cache: false,
            contentType: false,
            processData: false,
            method: 'GET',
            dataType: 'html',
            success: function (response) {
                $('.content-' + name).html(response);
            },

        });
    }

    var applyModalCoeficiente = () => {
        $('.open-modal-coeficiente').unbind("click").click(function () {
            loadingToggle();

            $.get($(this).data('url'), function (data) {
                $('#modalCoeficiente').find('.modal-content').html(data);

                $('#modalCoeficiente').modal('show');
                applyFormAjax('#form-ajax-coeficiente');
                loadingToggle();
                applyAll();
                applyModalCoeficiente();
            });
        });
    };

    var applyAllContrato = () => {
        applyFormAjax('#form-ajax-Garantia');
        applyFormAjax('#form-ajax-Empalme');
        applyCambiarVisualizacion();
        applyConfirmableSubmit();
        applyFormConfirmable();
        applyModalCronograma();
        applyModalCoeficiente();
        applyAccionesPolinomica();
        applyToggleUnidadMedida();
        applySortable();
    }
    //#endregion
</script>
@endsection
