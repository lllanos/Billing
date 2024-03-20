<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
      @trans('index.editar') @trans('forms.indice')
  </h4>
</div>

<form method="POST" data-action="{{route('indices.update', ['id' => $indice->id, 'publicacion_id' => $publicacion_id])}}" id="form-solicitud-ajax">
  {{ csrf_field() }}

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">

      <div class="col-md-6 col-sm-6">
        <div class="form-group">
          <label for="categoria_id">@trans('index.categoria')</label>
          <select class="form-control select-html-change" name="categoria_id" id="categoria_id" data-action="{{route('html.getSubCategorias', ['id' => ':id'])}}" required>
            <option disabled selected value> @trans('forms.select.categoria')</option>
            @foreach($categorias as $keyCategoria => $valueCategoria)
              <option value="{{ $keyCategoria }}" {{ ($indice->clasificacion->categoria == $keyCategoria) ? 'selected' : '' }}>
                {{ $valueCategoria }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-md-6 col-sm-6">
        <div class="form-group">
          <label for="sub_categoria_id">@trans('index.sub_categoria')</label>
          <select class="form-control select-subcategorias" name="sub_categoria_id" id="sub_categoria_id" required>
            <option disabled value> @trans('forms.select.sub_categoria')</option>
              @foreach($subcategorias as $keySubCategoria => $valueSubCategoria)
                <option
                  value="{{ $keySubCategoria }}"
                  {{ ($indice->clasificacion_id == $keySubCategoria) ? 'selected' : '' }}
                >
                {{ $valueSubCategoria }}
                </option>
              @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>@trans('forms.nro')</label>
          <input class="form-control" type="text" name="nro" id="nro" required value="{{$indice->nro}}" placeholder="@trans('forms.nro')">
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group ">
          <label>@trans('forms.nombre')</label>
          <input class="form-control" type="text" name="nombre" id="nombre" required value="{{$indice->nombre}}" placeholder="@trans('forms.nombre')">
        </div>
      </div>

      <div class="col-md-12 col-sm-12 col-xs-12 p-0">
        <div class="form-group mb-1">
          <label class="fixMargin4">
            <div class="checkbox noMarginChk">
              <div class="btn-group chk-group-btn" data-toggle="buttons">
                <label class="btn btn-primary btn-sm @if($indice->no_se_publica) active @endif">
                  <input autocomplete="off" class="triggerClickChk" type="checkbox" name="no_se_publica" id="no_se_publica" @if($indice->no_se_publica) checked @endif>
                  <span class="glyphicon glyphicon-ok"></span>
                </label>
                @trans('publicaciones.no_se_publica')
              </div>
            </div>
          </label>
        </div>
      </div>
      <div class="col-md-12 col-sm-12 hidden">
        <div class="form-group">
          <label class="col-md-3">
            <input type="radio" disabled name="simple_compuesto" class="toggleSelect" value="simple" @if(!$indice->compuesto) checked @endif>{{trans('forms.simple')}}
          </label>

          <label class="col-md-3">
            <input type="radio" disabled name="simple_compuesto" class="toggleSelect" value="compuesto" @if($indice->compuesto) checked @endif>{{trans('forms.compuesto')}}
          </label>
        </div>
      </div>
      <!--Toggle Simple compuesto-->
      <div class="col-md-12 toggleHidden on-modal @if($indice->compuesto) hidden @endif">
        <div class="container_input_check mb-1">
          <div class="col-md-8 col-sm-12  col-xs-12 p-0">
            <div class="form-group ">
              <label>{{trans('forms.fuente')}}</label>

              <input
                class="form-control"
                type="text"
                name="fuente_id"
                id="fuente_id"
                value="{{ ($indice->fuente != null) ? $indice->fuente->nombre : '' }}"
                >
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12 toggleHidden on-modal @if(!$indice->compuesto) hidden @endif">
        <div class="row">
          <!--Clonar input y chosen-->
          <div class="container_input_chosen_clonados">
            @foreach($indice->componentes as $key => $valueComponente)
              <div class="input_chosen_clonados">
                <div class="col-md-12 col-sm-12">
                  <div class="form-group">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <div class="form-group ">
                        <label>@trans('forms.porcentaje')</label>
                        <input
                          class="form-control porcentaje-mask"
                          type="text" name="porcentaje[{{$valueComponente->componente_id}}]"
                          id="porcentaje_{{$valueComponente->componente_id}}"
                          placeholder="@trans('forms.porcentaje')"
                          value="{{$valueComponente->porcentaje}}"
                          required
                          disabled>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <select class="form-control" disabled name="indice_compuesto_id[]" id="indice_compuesto_id_0" required>
                        <option disabled selected value> {{ trans('forms.select.indice') }}</option>
                          @foreach($indices as $keyIndice => $valueIndice)
                            <option value="{{ $valueIndice->id }}" {( $valueComponente->componente_id == $valueIndice->id) ? "selected" : "" }}>
                              {{ $valueIndice->nombre_full }}
                            </option>
                          @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <!--Fin Clonar input y chosen-->
        </div>
      </div>

      @if($a_publicar)
        <div class="col-md-12 col-sm-12 col-xs-12 p-0 publicaciones-anteriores">
          <label class="fixMargin4"> @trans('publicaciones.de_publicaciones_anteriores') </label>
          <div class="col-md-12 publicaciones-anteriores on-modal" id="div_pub_anteriores">

            @foreach($publicaciones as $keyPublicacion => $valuePublicacion)
              <div class='col-md-3 col-sm-3 col-xs-12'>
                <div class='form-group '>
                  <label>{{$valuePublicacion['mes_anio']}}</label>
                  <input
                    class="form-control currency"
                    type="text"
                    name="pub_old[{{$valuePublicacion['key']}}]"
                    id="pub_old_{{$valuePublicacion['key']}}"
                    placeholder="{{$valuePublicacion['mes_anio']}}"
                    value="@toDosDec($valuePublicacion['value'])"
                  >
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <div class="col-md-12">
        <div class="form-group">
          <label>@trans('index.observaciones')</label>
          <textarea class="form-control" name="observaciones" id="observaciones" placeholder="@trans('index.observaciones')">{{ $indice->observaciones }}</textarea>
        </div>
      </div>

  <!--Fin Toggle Simple compuesto-->
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
    applyAll();
  });
</script>
