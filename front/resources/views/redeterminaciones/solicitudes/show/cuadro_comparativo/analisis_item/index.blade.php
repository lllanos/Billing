@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
  <div class="row">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        @if($analisis_item->cuadro_comparativo->solicitud->en_curso)
          <li><a href="{{route('solicitudes.redeterminaciones_en_proceso')}}">@trans('forms.sol_redeterminaciones_en_proceso')</a></li>
        @else
          <li><a href="{{route('solicitudes.redeterminaciones_finalizadas')}}">@trans('forms.sol_redeterminaciones_finalizadas')</a></li>
        @endif
        <li><a href="{{route('solicitudes.ver', ['id' => $analisis_item->cuadro_comparativo->solicitud_id])}}">@trans('index.redeterminacion')</a></li>
         <li><a href="{{route('cuadroComparativo.ver', ['id' => $analisis_item->cuadro_comparativo->id])}}">@trans('sol_redeterminaciones.cuadro_comparativo') @trans('index.de') @trans('forms.solicitud') {{$analisis_item->cuadro_comparativo->solicitud->salto->moneda_mes_anio}} </a></li>
        <li class="active">@trans('index.detalle') @trans('index.de')
              @trans('sol_redeterminaciones.cuadro_comparativo') @trans('forms.item') {{$analisis_item->item->descripcion_codigo}} </li>
      </ol>
      <div class="page-header page_header__badge">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
              @trans('index.detalle') @trans('index.de')
              @trans('sol_redeterminaciones.cuadro_comparativo') @trans('forms.item') {{$analisis_item->item->descripcion_codigo}}
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
              <div class="col-sm-3 col-md-3 form-group">
                <label class="m-0">@trans('forms.unidad_medida')</label>
                <span class="form-control item_detalle">
                  {{$analisis_item->item->unidad_medida_o_alzado_nombre}}
                </span>
              </div>

              <div class="col-sm-3 col-md-3 form-group">
                <label class="m-0">@trans('analisis_item.costo_unitario')</label>
                <span class="form-control item_detalle">
                  @toDosDec($analisis_item->costo_unitario)
                </span>
              </div>

              <div class="col-sm-3 col-md-3 form-group">
                <label class="m-0">@trans('analisis_precios.coeficiente_k')</label>
                <span class="form-control item_detalle">
                  @toCuatroDec($analisis_precios->coeficiente_k)
                </span>
              </div>

              <div class="col-sm-3 col-md-3 form-group">
                <label class="m-0">@trans('analisis_precios.costo_coeficiente_k')</label>
                <span class="form-control item_detalle">
                  @toDosDec($analisis_item->precio_redeterminado)
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-12" id="analisis_container">
      @include('redeterminaciones.solicitudes.show.cuadro_comparativo.analisis_item.createEditContent')
    </div>
    <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
      <div class="text-right">
        <a class="btn btn-small btn-success" href="{{route('cuadroComparativo.ver', ['id' => $analisis_item->cuadro_comparativo->id])}}">@trans('forms.volver')</a>
      </div>
    </div>
  </div>

@endsection

@section('scripts')

@endsection
