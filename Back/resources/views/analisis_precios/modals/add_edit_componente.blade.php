<input type="hidden" name="js_applied" id="js_applied" value="0">
@php($redetermina = $categoria->analisis_item->analisis_precios->es_redeterminacion)
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @if($componente->id == null)
      @trans('index.nuevo') @trans('analisis_item.componente') @trans('index.de') {{$categoria->nombre}}
    @else
      @trans('index.editar') @trans('analisis_item.componente') @trans('index.de') {{$categoria->nombre}}
    @endif
  </h4>
</div>

<form method="POST" data-action="{{route('analisis_item.componente.updateOrStore', ['categoria_id' => $categoria->id])}}" id="form-ajax-componente">
  {{ csrf_field() }}

  <input type="hidden" name="id" id="id" value="{{ $componente->id }}" />

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        @if($redetermina && $accion == 'edit')
            <input type="hidden" value="{{$componente->nombre}}" name="nombre" id="nombre">
            <input type="hidden" value="{{$componente->indice_id}}" name="indice_id" id="indice_id">
        @else
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
              <label>@trans('forms.nombre')</label value="">
              <input class="form-control" value="{{$componente->nombre}}" type="text" name="nombre" id="nombre" placeholder="@trans('forms.nombre')" required @if($aprobado) readonly="true" @endif>
            </div>
          </div>


          @if($categoria->tiene_indice)
            <div class="form-group">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <label for="indice_id">@trans('forms.indice')</label>
                <select class="form-control" name="indice_id" id="indice_id" required>
                  <option disabled selected value> @trans('forms.select.indice')</option>
                    @foreach($indices as $keyIndice => $valueIndice)
                      <option value="{{ $valueIndice->id }}" @if($valueIndice->id == $componente->indice_id) selected @endif>{{ $valueIndice->nombre_full }} </option>
                    @endforeach
                </select>
              </div>
            </div>
          @endif

          @if($categoria->tiene_descripcion)
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <label>@trans('analisis_item.descripcion_calculo')</label>
                <textarea rows="4" class="form-control" name="descripcion" id="descripcion" placeholder="@trans('analisis_item.descripcion_calculo')" @if($aprobado) readonly="readonly" @endif required>{{$componente->descripcion}}</textarea>
              </div>
            </div>
          @endif
        @endif
      </div>
      <div class="row">
        @if(!$redetermina)
          @if($categoria->tiene_cantidad)
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="form-group">
                <label>@trans('forms.cantidad')</label>
                <input class="form-control currency recalcular" type="text" name="cantidad" id="cantidad" placeholder="@trans('forms.cantidad')" value="@toDosDec($componente->cantidad)" @if($aprobado) readonly="readonly" @endif>
              </div>
            </div>
          @endif

          @if($categoria->tiene_costo_unitario)
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="form-group">
                <label>@trans('analisis_item.costo_unitario')</label>
                <input class="form-control currency recalcular" type="text" name="costo_unitario" id="costo_unitario" placeholder="@trans('analisis_item.costo_unitario')" value="@toDosDec($componente->costo_unitario)" @if($aprobado) readonly="readonly" @endif>
              </div>
            </div>
          @endif
        @else
            <input class="form-control currency recalcular" type="hidden" name="cantidad" id="cantidad" value="0" >
            <input class="form-control currency recalcular" type="hidden" name="costo_unitario" id="costo_unitario" value="0" >
        @endif
        <div class="col-md-4 col-sm-4 col-xs-12 @if(!$redetermina) pull-right @endif">
          <div class="form-group">
            <label>@trans('analisis_item.costo_total')  @if($redetermina) @trans('sol_redeterminaciones.redeterminado') @endif</label>
            <input class="form-control currency @if(!$categoria->tiene_costo_unitario) copiar-costo @endif" type="text" name="costo_total_adaptado" id="costo_total_adaptado" placeholder="@trans('analisis_item.costo_total')" required value="@toDosDec($componente->costo_total_adaptado)" @if($aprobado) readonly="readonly" @endif>
          </div>
        </div>
        <input type="hidden" name="costo_total" id="costo_total" value="{{$componente->costo_total_adaptado}}"/>
      </div>

    </div>
  </div>

  <div class="modal-footer no-padding-bottom footer-original">
    <div class="col-md-12">
      @if($accion == 'add')
        <div class="btn-group chk-group-btn" data-toggle="buttons">
          <label class="btn btn-primary btn-sm">
            <input type="checkbox" autocomplete="off" name="agregar_otro" id="agregar_otro">
            <span class="glyphicon glyphicon-ok"></span>
          </label>
          @trans('index.agregar_otro')
        </div>
      @endif
      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>

</form>

<script type="text/javascript">
  $(document).ready(function() {
    $('.recalcular').unbind().on('keyup change paste cut', function() {
      calcularCostoTotal();
    });

    $('.copiar-costo').unbind().on('keyup change paste cut', function() {
      $('#costo_total').val($('#costo_total_adaptado').val());
    });

    applyAll();
  });

  var calcularCostoTotal = () => {
    var cantidad = $('#cantidad').val();
    var costo_unitario = $('#costo_unitario').val();

    if(cantidad == "" || costo_unitario == "" || cantidad == undefined || costo_unitario == undefined)
      return true;

    cantidad = parseFloat(cantidad.split('.').join('').replace(',', '.'));
    costo_unitario = parseFloat(costo_unitario.split('.').join('').replace(',', '.'));

    costo_total = cantidad * costo_unitario;
    costo_total = costo_total.toFixed(2);
    costo_total = costo_total.replace('.', ',');

    $('#costo_total').val(costo_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    $('#costo_total_adaptado').val(costo_total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
  };

</script>
