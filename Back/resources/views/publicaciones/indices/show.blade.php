<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
      @trans('index.ver') @trans('forms.indice')
  </h4>
</div>

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-6 col-sm-6 mb-2">
            <label class="label_titulo">@trans('index.categoria')</label>
            <span class="span_contenido">
              {{$indice->clasificacion->categoria}}
            </span>
        </div>
        <div class="col-md-6 col-sm-6 mb-2">
          <label class="label_titulo">@trans('index.sub_categoria')</label>
          <span class="span_contenido">
            {{$indice->clasificacion->subcategoria}}
          </span>
        </div>
        <div class="col-md-2 mb-2">
          <label class="label_titulo">@trans('forms.nro')</label>
          <span class="span_contenido">{{$indice->nro}}</span>
        </div>
        <div class="col-md-10 mb-2">
          <label class="label_titulo">@trans('forms.nombre')</label>
          <span class="span_contenido">{{$indice->nombre}}</span>
        </div>
        {{-- <div class="col-md-12 mb-2">
          <label class="label_titulo">{{trans('forms.aplicacion')}}</label>
          <span class="span_contenido">{{$indice->aplicacion}}</span>
        </div> --}}
        <div class="col-md-12 col-sm-12 col-xs-12 p-0">
          <div class="form-group mb-1">
            <label class="fixMargin4">
              <div class="checkbox noMarginChk">
                <div class="btn-group chk-group-btn container_no_click" data-toggle="buttons">
                  <label class="btn no_click btn-sm @if($indice->no_se_publica) active @endif">
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
            <label class="label_titulo label_titulo_simple_compuesto">@trans('forms.fuente')</label>
            <span class="span_contenido">@if($indice->fuente != null) {{$indice->fuente->nombre}} @endif</span>
          </div>
        </div>
      </div>
      <div class="col-md-12 toggleHidden on-modal @if(!$indice->compuesto) hidden @endif">
        @foreach($indice->componentes as $key => $valueComponente)
          <div class="row mb-2">
            <label class="label_titulo label_titulo_simple_compuesto">{{trans('forms.porcentaje')}}</label>
            <div class="col-md-3 col-sm-3 col-xs-3">
              <span>{{$valueComponente->porcentaje}} @if($valueComponente->porcentaje != null) % @endif</span>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <span> {{ $valueComponente->nombre_full }} </span>
            </div>
          </div>
        @endforeach

      </div>
      @if($indice->observaciones != null)
        <div class="col-md-12">
          <label class="label_titulo">@trans('index.observaciones')</label>
          <span>{{ $indice->observaciones }}</span>
        </div>
      @endif
      </div>
    </div>
  </div>

<script type="text/javascript">
  $(document).ready(function() {
    applyAll();
  });
</script>
