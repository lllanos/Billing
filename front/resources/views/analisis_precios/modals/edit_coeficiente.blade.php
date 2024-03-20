<form method="POST" data-action="{{route('AnalisisPrecios.coeficiente.edit.update', ['coeficiente_id' => $coeficiente_id, 'dato' => $dato])}}" id="form-ajax-coeficiente">
  {{ csrf_field() }}

  <div class="col-md-6 col-sm-6 col-xs-12">
    <div class="form-group ">
      <label>{{trans('forms.' . $dato)}}</label>
      <input class="form-control" type="text" name="valor" id="valor" required placeholder="{{trans('forms.' . $dato)}}"
      value="{{$valor}}">
    </div>
  </div>

  <div class="modal-footer no-padding-bottom">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" name="btn_guardar" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>
</form>
