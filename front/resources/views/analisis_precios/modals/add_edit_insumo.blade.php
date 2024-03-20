<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @if(isset($insumo))
      @trans('index.nuevo') @trans('index.insumo')
    @else
      @trans('index.editar') @trans('index.insumo')
    @endif
  </h4>
</div>

<form method="POST" data-action="{{route('AnalisisPrecios.insumos.add.post', ['id' => $item_categoria->id])}}" id="form-ajax-insumo">
  {{ csrf_field() }}

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-10 mb-2">
          <label class="label_titulo">@trans('forms.categoria')</label>
          <span class="span_contenido">{{$item_categoria->categoria->nombre}}</span>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
            <label>@trans('forms.select_create.nombre')</label>
            <input placeholder="@trans('forms.select_create.nombre')" type='text' id='nombre' name='nombre' class='form-control'>
            <input name="new_name" id="new_name" class="hidden" value="0">
          </div>
        </div>

        @include('analisis_precios.modals.composicion_insumo')

        <div class="col-md-3 col-sm-3 col-xs-12">
          <div class="form-group ">
            <label>@trans('forms.perdida')</label>
            <input class="form-control porcentaje-mask recalcular" type="text" name="perdida" id="perdida" required placeholder="@trans('forms.perdida')">
          </div>
        </div>
      </div>

  <!--Fin Toggle Simple compuesto-->
      <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-12 pull-right">
          <div class="form-group">
            <label>@trans('forms.total')</label>
            <input class="form-control" type="text" name="total" id="total" readonly placeholder="@trans('forms.total')">
          </div>
        </div>
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
  $(document).ready(function() {
    $('input.num_punto_y_coma').keyup(function(event) {
      $(this).val(function(index, value) {
        return value
          .replace(/\D/g, "")
          .replace(/([0-9])([0-9]{2})$/, '$1,$2')
          .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
      });
    });

    $('.recalcular').on('change', function() {
      sumTotal();
    });

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

      applyAll();
    });

  applyAll();
  applyCloneFile();
  applySelectize();
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

      if(x < maxField) { //Check maximum number of input fields
        x++; //Increment field counter
        y++;
        const htmlTemplate= `
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

  var applySelectize = () => {
    $('#nombre').selectize({
       persist: true,
       maxItems: 1,
       valueField: 'id',
       labelField: 'nombre',
       delimiter: '|||',
       searchField: 'id',
       options: {!! json_encode($insumos_auxiliares) !!},

       render: {
           item: function(item, escape) {
            console.log('item', item.id, item.nombre);
             if(item.nombre == undefined) {
               $('#new_name').val(1);
               return '<div><span class="selectize-seleccionada">' + escape(item.id) + '</span></div>';
             } else {
               $('#new_name').val(0);
               return '<div>' +
                   (item.nombre ? '<span class="email">' + escape(item.nombre) + '</span>' : '') +
               '</div>';
             }
           },
           option: function(item, escape) {
             if(item.nombre == undefined) {
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
              return '<div class="create">' +"{{ trans('analisis_precios.nuevo_nombre') }}" + ': <strong>' + escape(data.input) + '</strong></div>';
            },
       },
       create: function(input) {
                return {id: input};
       },
       onChange: function(value) {
         $(".selectize-input input[placeholder]").attr("style", "width: 100%;");
       },
    }).parent().find('label').append(' *');
    $(".selectize-input input[placeholder]").attr("style", "width: 100%;");
  }

  var sumTotal = () => {
    var total = 0;
    $('input.num_punto_y_coma').each(function() {
      var valor = parseFloat($(this).val().split('.').join('').replace(',', '.'));
      total += parseFloat(valor);
    });
    var perdida = $('#perdida').val().replace(' %', '');
    if(perdida != '') {
      total = total * parseFloat((1 + '.' + perdida));
    }
    total = (total).toFixed(2);
    total = total.replace('.', ',');
    $('#total').val(total);
  }

</script>
