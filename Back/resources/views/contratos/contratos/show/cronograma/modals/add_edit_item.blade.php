<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @trans('cronograma.agregar_avances_en')  {{ $item->descripcion }}
  </h4>
</div>

<form method="POST" data-action="{{route('cronograma.item.updateItemCronograma', ['cronograma_id' => $cronograma_id, 'item_id' => $item->id])}}" id="form-ajax-ItemCronograma">
  {{ csrf_field() }}
  <input type="hidden" name="cronograma_id" id="cronograma_id" value="{{$cronograma_id}}">
  <input type="hidden" name="item_id" id="item_id" value="{{$item->id}}">

  <div class="modal-body">
    <div class="modalContentScrollable">

      <div class="row">
        @foreach($itemsCronograma as $keyItemCronograma => $valueItemCronograma)
          <div class="col-md-3 col-sm-4 col-xs-12">
            <label class="mt-_5" for="valor{{$valueItemCronograma->mes}}">{{trans('index.mes')}} {{{$valueItemCronograma->mes}}}</label>
            <div class="input-group">
              <input class="form-control currency recalcular" type="text" name="valor[{{$valueItemCronograma->mes}}]" id="valor_{{$valueItemCronograma->mes}}"
                placeholder="{{trans('cronograma.valor')}}" @if($valueItemCronograma->mes < $mes_proximo_certificado) disabled @endif
                value="{{ $valueItemCronograma->cantidad_porcentaje_dos_dec }}"
              >
              <span class="input-group-addon">
                @if($valueItemCronograma->item->is_unidad_medida)
                  {{$valueItemCronograma->item->unidad_medida_nombre}}
                @else
                  %
                @endif
              </span>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>
  <div class="modal-footer no-padding-bottom footer-original">
    <div class="col-md-1 text-right">
      <label>{{trans('forms.total')}}</label>
    </div>

    <div class="col-md-3">
      <div class="input-group">
        <input class="form-control text-right" type="text" name="total" id="total" readonly value="{{$total}}">
        <span class="input-group-addon">
          @if ($valueItemCronograma->item->is_unidad_medida)
            {{$valueItemCronograma->item->unidad_medida_nombre}}
          @else
            %
          @endif
        </span>
      </div>
    </div>

    <div class="col-md-1 text-right">
      <label>{{trans('forms.faltante')}}</label>
    </div>

    <div class="col-md-3">
      <div class="input-group">
        <input class="form-control text-right" type="text" name="faltante" id="faltante" readonly data-total="{{$total_item}}" value="{{$faltante}}">
        <span class="input-group-addon">
          @if ($valueItemCronograma->item->is_unidad_medida)
            {{$valueItemCronograma->item->unidad_medida_nombre}}
          @else
            %
          @endif
        </span>
      </div>
    </div>
    <div class="col-md-4">
      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">{{trans('index.guardar')}}</button>
    </div>
  </div>

</form>

<script type="text/javascript">
  $(document).ready(function() {
    $('.recalcular').on('change', function() {
      sumTotal();
    });

  applyAll();
 });

  var sumTotal = () => {
    var total = 0;
    var total_itemizado = $('#faltante').data('total');

    total_itemizado = parseFloat(total_itemizado.split('.').join('').replace(',', '.'))

    $('input.currency').each(function() {
      if($(this).val() == '')
        var valor = 0;
      else
        var valor = parseFloat($(this).val().split('.').join('').replace(',', '.'));
      total += parseFloat(valor);
    });
    faltante = total_itemizado - total;

    total = (total).toFixed(2);
    total = total.replace('.', ',');

    $('#total').val(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));

    faltante = (faltante).toFixed(2);
    faltante = faltante.replace('.', ',');

    $('#faltante').val(faltante.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
  };

</script>
