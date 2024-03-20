<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @trans('index.editar') @trans('analisis_item.rendimiento')
  </h4>
</div>

<form method="POST" data-action="{{route('analisis_precios.updateRendimiento', ['categoria_id' => $categoria_id])}}" id="form-ajax-rendimiento">
  {{ csrf_field() }}

  <input type="hidden" name="id" id="id" value="{{ $rendimiento->id }}" />

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label>@trans('analisis_item.rendimiento')</label>
            <input class="form-control currency" type="text" name="rendimiento" id="rendimiento" placeholder="@trans('analisis_item.rendimiento')"
            value="@toDosDec($rendimiento->valor)">
          </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label for="indice_id">@trans('analisis_item.unidad')</label>
            <select class="form-control" name="unidad_id" id="unidad_id" required>
              @foreach($unidades as $keyUnidad => $valueUnidad)
                <option value="{{ $keyUnidad }}" @if($keyUnidad == $rendimiento->unidad_id) selected @endif>{{ $valueUnidad }} </option>
              @endforeach
            </select>
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
