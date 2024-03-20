@if(isset($select_options))
  <form method="POST" data-action="{{route('AnalisisPrecios.categorias.add.post', ['item_id' => $item_id])}}" id="form-ajax-categorias">
    {{ csrf_field() }}

    <div class="col-md-12">
    	<div class="form-group">
  		<label for="categoria">@trans('index.categoria')</label>
  		<select class="form-control" name="categoria" id="categoria" required>
        <option disabled selected value> @trans('forms.select.categoria')</option>
        @foreach($select_options as $key => $value )
    			<option value="{{$key}}" >{{$value}}</option>
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
    {!!trans('publicaciones.no_se_pueden_crear') !!}
  </div>
@endif
