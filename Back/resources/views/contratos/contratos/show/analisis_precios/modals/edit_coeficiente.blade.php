<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @trans('index.editar') @trans('analisis_precios.coeficiente_k')
  </h4>
</div>

<form method="POST" data-action="{{route('analisis_precios.updateCoeficienteK', ['analisis_precios_id' => $analisis_precios->id])}}" id="form-ajax-coeficiente">
  {{ csrf_field() }}

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
            <label>@trans('analisis_precios.coeficiente_k')</label>
            <input class="form-control currency 4dec" type="text" name="coeficiente_k" id="coeficiente_k" placeholder="@trans('analisis_item.coeficiente_k')"
            value="@toCuatroDec($analisis_precios->coeficiente_k)">
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
