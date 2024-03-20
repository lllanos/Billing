 <div class="col-md-12 on-modal">
  <div class="row">
    <!--Clonar input y chosen-->
    <div class="container_input_chosen_clonados">
      <div class="input_chosen_clonados can_delete">
        <div class="col-md-12 col-sm-12">
          <div class="form-group">
            <div class="col-md-3 col-sm-3 col-xs-12">
              <div class="form-group ">
                <label>{{trans('forms.valor')}}</label>
                <input class="form-control num_punto_y_coma recalcular" type="text" name="valor[]" id="valor_0" required placeholder="{{trans('forms.valor')}}">
              </div>
            </div>
          </div>
          <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12">
                <select class="form-control" name="indice_compuesto_id[]" id="indice_compuesto_id_0" required>
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
      </div>
    </div>
    <!--Fin Clonar input y chosen-->
  </div>
</div>
