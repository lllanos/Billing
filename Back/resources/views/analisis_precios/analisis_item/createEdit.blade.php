@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
{{-- @php($redetermina = $analisis_item->analisis_precios->es_redeterminacion) --}}
  <div class="row">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <ol class="breadcrumb">
          <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
          <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
          <li><a href="{{route('contratos.ver', ['id' => $analisis_item->contrato->id]) }}">@trans('forms.contrato') {{$analisis_item->contrato->expediente_madre}}</a></li>
          @if($redetermina)
            @if($edit)
             <li><a href="{{route('empalme.redeterminacion.edit', ['id' => $analisis_item->analisis_precios->redeterminacion_id]) }}"> @trans('index.redeterminacion') </a></li>
            @else
             <li><a href="{{route('empalme.redeterminacion.ver', ['id' => $analisis_item->analisis_precios->redeterminacion_id]) }}"> @trans('index.redeterminacion') </a></li>
            @endif
          @endif
          <li class="active">@trans('analisis_precios.analisis_item')</li>
        </ol>
      <div class="page-header page_header__badge">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @if($redetermina)
              @trans('index.detalle') @trans('index.de')
            @endif
              @trans('analisis_precios.analisis_item')
            @if($redetermina)
              @trans('index.de') @trans('contratos.empalme')
            @endif
              <span class="badge" style="background-color:#{{ $analisis_item->estado['color'] }};">
                {{ $analisis_item->estado['nombre_trans'] }}
              </span>
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop_rsp">
              <div class="dropdown dd-on-table pull-right">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')" id="dd_acciones">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right multi-level" role="menu" aria-labelledby="dropdownMenu">
                  @if(!$redetermina)
                    @foreach($analisis_item->acciones_posibles as $keyAccion => $valueAccion)
                      <li>
                        <a class="btn-confirmable"
                         data-body="@trans('analisis_item.confirmaciones.' . $valueAccion)"
                         data-action="{{route('analisis_item.storeUpdate', ['analisis_item_id' => $analisis_item->id, 'accion' => $valueAccion])}}"
                         data-si="@trans('index.si')" data-no="@trans('index.no')">
                         @trans('analisis_item.acciones.' . $valueAccion)
                        </a>
                      </li>
                    @endforeach
                  @endif
                  <li>
                    <a class="open-historial mouse-pointer" id="btn_historial_resp" data-url="{{ route('analisis_precios.historial', ['clase_id' => $analisis_item->id, 'seccion' => 'analisis_item']) }}">
                      @trans('index.historial')
                    </a>
                  </li>
                  <li>
                    @if($edit)
                      @if($redetermina)
                        <a href="{{route('empalme.analisis_item.ver', ['analisis_item_id' => $analisis_item->id])}}">
                          @trans('index.ver') @trans('analisis_item.analisis_item')
                        </a>
                      @else
                        <a href="{{route('analisis_item.ver', ['analisis_item_id' => $analisis_item->id])}}">
                          @trans('index.ver') @trans('analisis_item.analisis_item')
                        </a>
                      @endif
                    @elseif($analisis_item->permite_editar)
                      @if($redetermina)
                        <a href="{{route('empalme.analisis_item.edit', ['analisis_item_id' => $analisis_item->id])}}">
                          @trans('index.editar') @trans('analisis_item.analisis_item')
                        </a>
                      @else
                        <a href="{{route('analisis_item.edit', ['analisis_item_id' => $analisis_item->id])}}">
                          @trans('index.editar') @trans('analisis_item.analisis_item')
                        </a>
                      @endif
                    @endif
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <!-- Panel Detalle Contrato -->
      <div class="panel-group acordion" id="accordion_detalle_contrato" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default panel-view-data border-top-poncho">
          <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne_detalle_contrato">
            <h4 class="panel-title titulo_collapse m-0">
              <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_detalle_contrato" href="#collapseOne_detalle_contrato" aria-expanded="true" aria-controls="collapseOne_detalle_contrato">
                <i class="fa fa-angle-down"></i> {{$analisis_item->item->descripcion_codigo}}
              </a>
            </h4>
          </div>
          <div id="collapseOne_detalle_contrato" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_detalle_contrato">
            <div class="panel-body">

              <div class="col-sm-12 col-md-12 form-group">
                <label class="m-0">@trans('contratos.contrato_madre')</label>
                <span class="form-control">
                  <a href="{{route('contratos.ver', ['id' => $analisis_item->contrato->id]) }}">
                    {{$analisis_item->contrato->nombre_completo}}
                  </a>
                </span>
              </div>

              <div class="col-sm-6 col-md-3 form-group">
                <label class="m-0">@trans('forms.unidad_medida')</label>
                <span class="form-control item_detalle">
                  {{$analisis_item->item->unidad_medida_o_alzado_nombre}}
                </span>
              </div>

              <div class="col-sm-6 col-md-3 form-group">
                <label class="m-0">@trans('analisis_item.costo_unitario')</label>
                <span class="form-control item_detalle" id="costo_unitario_analisis">
                  @toDosDec($analisis_item->costo_unitario_adaptado)
                </span>
              </div>

              <div class="col-sm-6 col-md-3 form-group">
                <label class="m-0">@trans('analisis_precios.coeficiente_k')</label>
                <span class="form-control item_detalle">
                  @toCuatroDec($analisis_item->analisis_precios->coeficiente_k)
                </span>
              </div>

              <div class="col-sm-6 col-md-3 form-group">
                <label class="m-0">@trans('analisis_precios.costo_coeficiente_k')</label>
                <span class="form-control item_detalle" id="costo_coeficiente_k">
                  @toCuatroDec($analisis_item->costo_unitario_adaptado * $analisis_item->analisis_precios->coeficiente_k)
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="errores-analisis hidden alert alert-danger">
      </div>
    </div>



    @if($redetermina)
      <form method="POST" action="{{route('empalme.analisis_item.componentes.edit', ['id' => $analisis_item->id ])}}" data-action="{{route('empalme.analisis_item.componentes.edit', ['id' => $analisis_item->id ])}}" id="form-ajax">
        {{ csrf_field() }}
        {{-- <div class="alert alert-danger hidden"> <ul> </ul> </div> --}}
    @endif

    <div class="col-md-12" id="analisis_container">
        @include('analisis_precios.analisis_item.createEditContent')
    </div>
    <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
      <div class="text-right">
        @if($redetermina)
          @if($edit)
            <a class="btn btn-small btn-success" href="{{route('empalme.redeterminacion.edit', ['id' => $analisis_item->analisis_precios->redeterminacion_id]) }}">@trans('forms.volver')</a>
            {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar')) }}
          @else
             <a class="btn btn-small btn-success" href="{{route('empalme.redeterminacion.ver', ['id' => $analisis_item->analisis_precios->redeterminacion_id]) }}">@trans('forms.volver')</a>
          @endif
        @else
          <a class="btn btn-small btn-success" href="{{route('contratos.editar.incompleto', ['id' => $analisis_item->contrato->id, 'accion' => 'analisis_precios'])}}">@trans('forms.volver')</a>
        @endif
      </div>
    </div>
  </div>

@endsection

@section('modals')
  @include('analisis_precios.modals.modals')
@endsection

@section('scripts')
  $(document).ready(() => {
    applyModals();
  });

  var applyModalComponente = () => {
    $('.open-modal-componente').unbind("click").click(function() {
      loadingToggle();

      $.get($(this).data('url'), function(data) {
        $('#modalComponente').find('.modal-content').html(data);

        $('#modalComponente').modal('show');
        applyFormAjax('#form-ajax-componente');
        loadingToggle();
        applyAll();
      });
    });
  };

  var applyModalRendimiento = () => {
    $('.open-modal-rendimiento').unbind("click").click(function() {
      loadingToggle();

      $.get($(this).data('url'), function(data) {
        $('#modalRendimiento').find('.modal-content').html(data);

        $('#modalRendimiento').modal('show');
        applyFormAjax('#form-ajax-rendimiento');
        loadingToggle();
        applyAll();
      });
    });
  };

  var applyModals = () => {
    applyModalRendimiento();
    applyModalComponente();
  };
@endsection
