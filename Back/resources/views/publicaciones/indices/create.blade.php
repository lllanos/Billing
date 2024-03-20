<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
    <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="fa fa-times fa-2x"></span>
    </button>
    <h4 class="modal-title">
        {!! trans('index.nuevo_indice') !!}
    </h4>
</div>

<form method="POST" data-action="{{route('indices.create', ['id' => $publicacion_id])}}" id="form-solicitud-ajax">
    {{ csrf_field() }}

    <div class="modal-body">
        <div class="modalContentScrollable">
            <div class="row">

                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="categoria_id">@trans('index.categoria')</label>
                        <select class="form-control select-html-change" name="categoria_id" id="categoria_id" data-action="{{route('html.getSubCategorias', ['id' => ':id'])}}" required>
                            <option disabled selected value> @trans('forms.select.categoria')</option>
                            @foreach($categorias as $keyCategoria => $valueCategoria)
                            <option value="{{ $valueCategoria['nombre'] }}">{{ $valueCategoria['nombre'] }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="sub_categoria_id">@trans('index.sub_categoria')</label>
                        <select class="form-control select-subcategorias" name="sub_categoria_id" id="sub_categoria_id" required>
                            <option disabled selected value> @trans('forms.select.sub_categoria')</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>@trans('forms.nro')</label>
                        <input class="form-control" type="text" name="nro" id="nro" required value="" placeholder="@trans('forms.nro')">
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="form-group ">
                        <label>@trans('forms.nombre')</label>
                        <input class="form-control" type="text" name="nombre" id="nombre" required placeholder="@trans('forms.nombre')">
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                    <div class="form-group mb-1">
                        <label class="fixMargin4">
                            <div class="checkbox noMarginChk">
                                <div class="btn-group chk-group-btn" data-toggle="buttons">
                                    <label class="btn btn-primary btn-sm ">
                                        <input autocomplete="off" class="triggerClickChk" type="checkbox" name="no_se_publica" id="no_se_publica">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </label>
                                    @trans('publicaciones.no_se_publica')
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 error_rad_simple_compuesto hidden">
                    <div class="form-group" id="div_simple_compuesto">
                        <label class="col-md-3" id="simple">
                            <input type="radio" name="simple_compuesto" class="toggleSelect" value="simple" checked>{{trans('forms.simple')}}
                        </label>
                        <label class="col-md-3">
                            <input type="radio" name="simple_compuesto" class="toggleSelect" value="compuesto">{{trans('forms.compuesto')}}
                        </label>
                        <label class="col-md-3 hidden">
                            <input type="radio" name="simple_compuesto" class="toggleSelect" value="calculado">{{trans('forms.calculado')}}
                        </label>
                    </div>
                </div>

                <!--Toggle Simple compuesto-->
                <!-- Simple -->
                <div class="col-md-12 toggleHidden simple on-modal">
                    <div class="container_input_check mb-1">
                        <div class="col-md-12 col-sm-12  col-xs-12 p-0">
                            <div class="form-group">
                                <label>@trans('forms.select_create.fuente')</label>
                                <input placeholder="@trans('forms.select_create.fuente')" type='text' id='fuente_id' name='fuente_id' class='form-control'>
                            </div>
                        </div>
                    </div>
                    <input name="new_fuente" id="new_fuente" class="hidden" value="0">

                    <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                        <div class="form-group mb-1">
                            <label class="fixMargin4">
                                <div class="checkbox noMarginChk">
                                    <div class="btn-group chk-group-btn" data-toggle="buttons">
                                        <label class="btn btn-primary btn-sm">
                                            <input autocomplete="off" class="triggerClickChk de_publicaciones_anteriores" type="checkbox" name="de_publicaciones_anteriores" id="de_publicaciones_anteriores">
                                            <span class="glyphicon glyphicon-ok"></span>
                                        </label>
                                        @trans('publicaciones.de_publicaciones_anteriores')
                                    </div>
                                </div>
                            </label>
                        </div>
                        <input class="hidden" value="0" name="btn_de_publicaciones_anteriores" id="btn_de_publicaciones_anteriores">
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 p-0 publicaciones-anteriores">
                        <div class="container_input_check">
                            <div class="form-group ">
                                <label>@trans('forms.valor_inicial')</label>
                                <input class="form-control num_punto_y_coma" type="text" name="valor_inicial" id="valor_inicial" required placeholder="@trans('forms.valor_inicial')">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 p-0 publicaciones-anteriores hidden">
                        <div class="form-group">
                            <label>@trans('index.publicacion')</label>
                            <select class="form-control" name="sel_pub_anteriores" id="sel_pub_anteriores">
                                @foreach($publicaciones as $keyPublicacion => $valuePublicacion)
                                <option data-key="{{$keyPublicacion}}" value="{{ $valuePublicacion['key'] }}">{{ $valuePublicacion['mes_anio'] }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 publicaciones-anteriores on-modal hidden" id="div_pub_anteriores"></div>
                    </div>
                </div>
                <!-- FIN Simple -->

                <!-- Compuesto -->
                <div class="col-md-12 toggleHidden compuesto on-modal hidden">
                    <div class="row">
                        <!--Clonar input y chosen-->
                        <div class="container_input_chosen_clonados">
                            <div class="input_chosen_clonados can_delete">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <div class="form-group ">
                                                <label>@trans('forms.porcentaje')</label>
                                                <input class="form-control porcentaje-mask" type="text" name="porcentaje[]" id="porcentaje_0" placeholder="@trans('forms.porcentaje')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <select class="form-control" name="indice_compuesto_id[]" id="indice_compuesto_id_0">
                                                <option disabled selected value>@trans('forms.select.indice')</option>
                                                @foreach($indices as $keyIndice => $valueIndice)
                                                <option value="{{ $valueIndice->id }}">{{ $valueIndice->nombre_full }} </option>
                                                @endforeach
                                            </select>
                                            <div class="container_btn_">
                                                <a href="#" class="btn btn-primary add_button"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                <a href="#" class="btn btn-danger remove_button hidden"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Fin Clonar input y chosen-->
                    </div>
                </div>
                <!-- FIN Compuesto -->

                <div class="col-md-12">
                    <div class="form-group">
                        <label>@trans('index.observaciones')</label>
                        <textarea class="form-control" name="observaciones" id="observaciones" placeholder="@trans('index.observaciones')"></textarea>
                    </div>
                </div>

                <!--Fin Toggle Simple compuesto-->
            </div>
        </div>
    </div>
    <div class="modal-footer no-padding-bottom footer-original">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
        </div>
    </div>

</form>

<script type="text/javascript">
    var publicaciones = {!!json_encode($publicaciones) !!};
    $(document).ready(function() {
        $(".de_publicaciones_anteriores").change(function() {
            $('#valor_inicial').attr('required', function(_, attr) {
                return !attr
            });
            if ($('#btn_de_publicaciones_anteriores').val() == 1) {
                $('#btn_de_publicaciones_anteriores').val(0);
                $('#sel_pub_anteriores').val(['']).trigger("chosen:updated");
                $('#div_pub_anteriores').html('');
            } else {
                $('#btn_de_publicaciones_anteriores').val(1);
            }

            $('.publicaciones-anteriores').toggleClass('hidden');
            applyAll();
        });

        $('#sel_pub_anteriores').unbind('change').on('change', function() {
            $('#div_pub_anteriores').html('');
            var key = $(this).find("option:selected").data('key');
            jQuery.each(publicaciones, function(i, e) {
                if (i >= key) {
                    input = `<div class='col-md-3 col-sm-3 col-xs-12'>
              <div class='form-group '>
                <label>${e.mes_anio}</label>
                <input class='form-control currency' type='text' name='pub_old[${e.key}]' id='pub_old_${e.key}' required placeholder='${e.mes_anio}'>
              </div>`;

                    $('#div_pub_anteriores').append(input);
                }
            });
            applyAll();
        });

        $('input.num_punto_y_coma').keyup(function(event) {
            $(this).val(function(index, value) {
                return value
                    .replace(/\D/g, "")
                    .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                    .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
            });
        });

        $('#fuente_id').selectize({
            persist: true,
            maxItems: 1,
            valueField: 'id',
            labelField: 'nombre',
            delimiter: '|||',
            searchField: 'id',
            options: {!! json_encode($indice -> opciones_selectize) !!},
            render: {
                item: function(item, escape) {
                    if (item.nombre == undefined) {
                        $('#new_fuente').val(1);
                        return '<div><span class="selectize-seleccionada">' + escape(item.id) + '</span></div>';
                    } else {
                        $('#new_fuente').val(0);
                        return '<div>' +
                            (item.nombre ? '<span class="email">' + escape(item.nombre) + '</span>' : '') +
                            '</div>';
                    }
                },
                option: function(item, escape) {
                    if (item.nombre == undefined) {
                        return '<div>' +
                            (item.id ? '<span class="email">' + escape(item.id) + '</span>' : '') +
                            '</div>';
                    } else {
                        return '<div>' +
                            (item.nombre ? '<span class="email">' + escape(item.nombre) + '</span>' : '') +
                            '</div>';
                    }
                },
                option_create: (data, escape) => {
                    return '<div class="create">' + "{{ trans('publicaciones.nueva_fuente') }}" + ': <strong>' + escape(data.input) + '</strong></div>';
                },
            },
            create: function(input) {
                return {
                    id: input
                };
            },
            onChange: function(value) {
                $(".selectize-input input[placeholder]").attr("style", "width: 100%;");
            },
        }).parent().find('label').append(' *');
        $(".selectize-input input[placeholder]").attr("style", "width: 100%;");

        $('input[type=radio][name=simple_compuesto]').change(function() {
            var tipo = $(this).val();
            // toggle required en toggleHidden visible antes del cambio
            $('.toggleHidden:not(.hidden)').find('select').attr('required', false);

            $(".toggleHidden").addClass('hidden');
            $(".toggleHidden." + tipo).removeClass('hidden');

            // toggle required en toggleHidden visible despues del cambio
            $('.toggleHidden:not(.hidden)').find('select').attr('required', true);

            // $('.toggleHidden').find('select').attr('required', function (_, attr) { return !attr });

            // VER: solo en un caso
            $('#valor_inicial').attr('required', function(_, attr) {
                return !attr
            });
            applyAll();
        });

        applyAll();
        applyCloneFile();
    });

    var y = 1;
    applyCloneFile = () => {
        let addButton = $('.add_button');
        $(addButton).click(function() { //Once add button is clicked
            let maxField = 100; //Input fields increment limitation
            let addButton = $('.add_button'); //Add button selector
            let wrapper = $('.container_input_chosen_clonados'); //Input field wrapper
            var x = 1;

            let $this = $(this);
            $this.addClass('hidden');
            $this.parent().find('.remove_button').removeClass('hidden');

            if (x < maxField) { //Check maximum number of input fields
                x++; //Increment field counter
                y++;
                const htmlTemplate = `
        <div class="input_chosen_clonados can_delete">
          <div class="col-md-12 col-sm-12">
            <div class="form-group">
              <div class="col-md-3 col-sm-3 col-xs-12">
                <div class="form-group ">
                  <label>{{trans('forms.porcentaje')}}</label>
                  <input class="form-control porcentaje-mask" type="text" name="porcentaje[${y}]" id="porcentaje_${y}" required placeholder="{{trans('forms.porcentaje')}}">
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12">
                <select class="form-control" name="indice_compuesto_id[${y}]" id="indice_compuesto_id_${y}" required>
                  <option disabled selected value> {{ trans('forms.select.indice') }}</option>
                  @foreach($indices as $keyIndice => $valueIndice)
                    <option value="{{ $valueIndice->id }}" >{{ $valueIndice->nombre_full }} </option>
                  @endforeach
                </select>
                <div class="container_btn_">
                  <a href="#" class="btn btn-primary add_button"><i class="fa fa-plus" aria-hidden="true"></i></a>
                  <a href="#" class="btn btn-danger remove_button hidden"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>`;
                $(wrapper).append(htmlTemplate); // Add field html
                applyCloneFile();
                applyAll();

                $(wrapper).on('click', '.remove_button', function(e) { //Once remove button is clicked
                    e.preventDefault();
                    $(this).parents('div.can_delete').remove(); //Remove field html
                    x--; //Decrement field counter
                });
            }
        });
    }
</script>