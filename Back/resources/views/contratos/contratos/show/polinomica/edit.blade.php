@permissions(('polinomica-edit'))
  <div class="row">
    <div class="col-md-12 col-sm-12">

      <!--Clonar input y chosen-->
      <div class="row">
        <div class="col-md-12 ttl_composicion_err  mr-1 ml-1">
          <label>@trans('contratos.composicion') *</label><label id="polinomicas_suma_{{$valueContratoMoneda->polinomica->id}}"></label>
        </div>
      </div>

        @php ($i = 0)
        @foreach($valueContratoMoneda->polinomica->composiciones_ordenadas as $keyComposicion => $valueComposicion)
          <div class="container_input_chosen_originales can_delete borrador_{{$valueContratoMoneda->polinomica->id}} wrapper_no_borradores mr-1 ml-1">
            <div class="input_chosen_clonados clon_polinomica clon_polinomica_borrador can_delete" id="polinomicas_{{$valueContratoMoneda->polinomica->id}}_{{$i}}">
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                  <div class="form-group">
                    <input class="form-control" type="text" name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][nombre]" id="polinomicas_nombre_{{$valueContratoMoneda->polinomica->id}}_{{$i}}" placeholder="{{trans('forms.nombre')}}"
                     value="{{ $valueComposicion->nombre }}">
                  </div>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <div class="form-group ">
                    <select class="form-control" name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][tabla_indices_id]" id="tabla_indices_id_{{$valueContratoMoneda->polinomica->id}}_{{$i}}">
                      <option selected value> {{ trans('forms.select.indice') }}</option>
                      @if(isset($indices[$valueContratoMoneda->moneda_id]) && count($indices[$valueContratoMoneda->moneda_id]) > 0)
                        @foreach($indices[$valueContratoMoneda->moneda_id] as $keyIndice => $valueIndice)
                          <option value="{{ $valueIndice->id }}" @if($valueComposicion->tabla_indices_id == $valueIndice->id) selected @endif>{{ $valueIndice->nombre_full }} </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12">
                  <div class="form-group">
                    <input
                      class="form-control composicion-mask" type="text" name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][porcentaje]"
                      id="polinomicas_porcentaje_{{$valueContratoMoneda->polinomica->id}}_{{$i}}" placeholder="{{trans('forms.factor_incidencia')}}"
                      value="{{ $valueComposicion->porcentaje }}" data-inputmask="'mask': '9,9999'"
                    >
                    <div class="container_btn_">
                      <a href="javascript:void(0)" class="btn btn-primary add_button hidden add_button_composicion_poli" data-polinomica="{{$valueContratoMoneda->polinomica->id}}" data-moneda="{{$valueContratoMoneda->moneda_id}}"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0)" class="btn btn-danger remove_button" data-polinomica="{{$valueContratoMoneda->polinomica->id}}"  data-moneda="{{$valueContratoMoneda->moneda_id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @php ($i++)
        @endforeach
        {{-- Fin borradores --}}

        <div class="container_input_chosen_clonados wrapper_{{$valueContratoMoneda->polinomica->id}} wrapper_no_borradores mr-1 ml-1">
          <div class="input_chosen_clonados clon_polinomica clon_polinomica_no_borrador can_delete" id="polinomicas_{{$valueContratoMoneda->polinomica->id}}_{{$i}}">
            <div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group ">
                  <input
                    class="form-control" type="text" name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][nombre]"
                    id="polinomicas_nombre_{{$valueContratoMoneda->polinomica->id}}_{{$i}}" placeholder="{{trans('forms.nombre')}}"
                  >
                </div>
              </div>
              <div class="col-md-5 col-sm-5 col-xs-12">
                <div class="form-group ">
                  <select class="form-control" name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][tabla_indices_id]" id="tabla_indices_id_{{$valueContratoMoneda->polinomica->id}}_{{$i}}">
                    <option selected value> {{ trans('forms.select.indice') }}</option>
                    @if(isset($indices[$valueContratoMoneda->moneda_id]) && count($indices[$valueContratoMoneda->moneda_id]) > 0)
                      @foreach($indices[$valueContratoMoneda->moneda_id] as $keyIndice => $valueIndice)
                        <option value="{{ $valueIndice->id }}" >{{ $valueIndice->nombre_full }} </option>
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>

            <div class="col-md-3 col-sm-3 col-xs-12">
              <div class="form-group">
                <input
                  class="form-control composicion-mask composicion_clon" type="text"
                  name="polinomicas[{{$valueContratoMoneda->polinomica->id}}][{{$i}}][porcentaje]"
                  id="polinomicas_porcentaje_{{$valueContratoMoneda->polinomica->id}}_{{$i}}" placeholder="{{trans('forms.factor_incidencia')}}"
                  data-inputmask="'mask': '9,9999'"
                >
                <div class="container_btn_">
                  <a href="javascript:void(0)" class="btn btn-primary add_button @if($i != 0) add_button_composicion_poli @endif" data-polinomica="{{$valueContratoMoneda->polinomica->id}}" data-moneda="{{$valueContratoMoneda->moneda_id}}"><i class="fa fa-plus" aria-hidden="true"></i></a><a href="javascript:void(0)" class="btn btn-danger remove_button @if($i == 0) hidden @endif"
                    data-polinomica="{{$valueContratoMoneda->polinomica->id}}" data-moneda="{{$valueContratoMoneda->moneda_id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Fin Clonar input y chosen-->
      </div>
    </div>
  </div>
@endpermission
