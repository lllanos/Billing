@if(isset($select_options))
<form method="POST" data-action="{{route('publicaciones.store')}}" id="form-ajax-publicaciones">
  {{ csrf_field() }}

  <div class="col-md-12">
    <div class="form-group select-create">
      <label for="moneda">@trans('index.moneda')</label>
      <select class="form-control" name="moneda" id="id_moneda" required>
        <option disabled selected value> @trans('forms.select.moneda')</option>
        @foreach($moneda_options as $key => $value )
        <option value="{{$key}}">{{$value}}</option>
        @endforeach
      </select>
      <label for="mes_anio" class="mes-anio-form" style="display: none;">@trans('index.mes_publicacion')</label>
      <select class="form-control mes-anio-form no-chosen" name="mes_anio" id="mes_anio" required style="display: none;">
        <option disabled selected value> @trans('forms.select.mes_anio')</option>
        @foreach($select_options as $key => $value )
        <option value="{{$key}}">{{$value}}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="modal-footer no-padding-bottom">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" name="btn_guardar" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>
</form>
@else
<div class="no-data-no-padding text-center">
  @trans('publicaciones.no_se_pueden_crear')
</div>
@endif