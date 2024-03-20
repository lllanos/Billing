@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      @if($cuadro_comparativo->solicitud->en_curso)
        <li><a href="{{route('solicitudes.redeterminaciones_en_proceso')}}">@trans('forms.sol_redeterminaciones_en_proceso')</a></li>
      @else
        <li><a href="{{route('solicitudes.redeterminaciones_finalizadas')}}">@trans('forms.sol_redeterminaciones_finalizadas')</a></li>
      @endif
      <li><a href="{{route('solicitudes.ver', ['id' => $cuadro_comparativo->solicitud_id])}}">@trans('index.ver') @trans('index.redeterminacion')</a></li>
      <li class="active"> @trans('sol_redeterminaciones.cuadro_comparativo') @trans('index.de') @trans('forms.solicitud') {{$cuadro_comparativo->solicitud->salto->moneda_mes_anio}} </li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('sol_redeterminaciones.cuadro_comparativo') @trans('index.de') @trans('forms.solicitud') {{$cuadro_comparativo->solicitud->salto->moneda_mes_anio}}
        </div>
        <div class="buttons-on-title"> </div>
      </h3>
    </div>

    @php($solicitud = $cuadro_comparativo->solicitud)
    @php($sin_datos = true)
    @include('redeterminaciones.solicitudes.show.detalle')

    <div class="row">
      <div class="col-md-12">
        <!-- Panel -->
        <div class="panel-group acordion" id="accordion_Itemizado" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default panel-view-data border-top-poncho">
            <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading_Itemizado">
              <h4 class="panel-title titulo_collapse m-0">
                <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_Itemizado" href="#collapse_Itemizado" aria-expanded="true" aria-controls="collapse_Itemizado">
                  <i class="fa fa-angle-down"></i> @trans('sol_redeterminaciones.cuadro_comparativo')
                </a>
              </h4>
            </div>
            <div id="collapse_Itemizado" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_Itemizado">
              <div class="panel-body">
            		<div class="panel panel-default">
                  <div class="panel-body">
                    <div class="row">
                      @include('redeterminaciones.solicitudes.show.cuadro_comparativo.itemizado.show')
                    </div>
                  </div>
            		</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
          <div class="text-right">
            <a class="btn btn-small btn-success" href="{{route('solicitudes.ver', ['id' => $cuadro_comparativo->solicitud_id])}}">@trans('forms.volver')</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  $(document).ready(() => {

  });
@endsection
