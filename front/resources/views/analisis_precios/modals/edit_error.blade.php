<form method="POST" data-action="{{route('AnalisisPrecios.error.update', ['modelo' => $modelo, 'contrato_id' => $contrato_id, 'id' => $id])}}" id="form-ajax-coeficiente">
  {{ csrf_field() }}

  <div class="col-md-3 col-sm-3 col-xs-12">
    <div class="form-group">
      <label>{{trans('forms.total')}}</label>
      <input class="form-control" type="text" name="total" id="total" readonly placeholder="{{trans('forms.total')}}"
      value="{{$objeto->total_show}}">
    </div>
  </div>

  <div class="col-md-3 col-sm-3 col-xs-12">
    <div class="form-group">
      {{-- <div class="col-md-3 col-sm-3 col-xs-12"> --}}
        <div class="form-group ">
          <label>{{trans('forms.error')}}</label>
          <input class="form-control num_punto_y_coma" type="text" name="error" id="error" required placeholder="{{trans('forms.error')}}"
          value="{{$objeto->error_show}}">
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-3 col-xs-12">
      <div class="form-group">
        <label>{{trans('forms.total_adaptado')}}</label>
        <input class="form-control" type="text" name="total_adaptado" id="total_adaptado" readonly placeholder="{{trans('forms.total_adaptado')}}"
        value="{{$objeto->total_adaptado_show}}">
      </div>
    </div>

  <div class="modal-footer no-padding-bottom">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" name="btn_guardar" id="btn_guardar">@trans('index.guardar')</button>
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

    $('input.num_punto_y_coma').on('change', function() {
      var error = parseFloat($(this).val().split('.').join('').replace(',', '.'));
      var total = parseFloat($('#total').val().split('.').join('').replace(',', '.'));
      var total_adaptado = total + error;

      total_adaptado = total_adaptado.toFixed(2).replace('.', ',');
      $('#total_adaptado').val(total_adaptado);
    });

  });

</script>
