@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{ route('contrato.asociar') }}">{!!trans('index.mis')!!}{!!trans('index.solicitudes_asociacion')!!}</a></li>
        <li class="active">{!!trans('index.ver')!!} {!!trans('index.solicitud_contrato')!!}</li>
      </ol>
      <div class="page-header">
        <h3 class="m-0">
          {!!trans('index.solicitud_contrato')!!} {{ $solicitud->expediente_madre }}
          <span class="badge mb-_5" style="background-color:#{{ $solicitud->estado_nombre_color['color'] }};">
            {{ $solicitud->estado_nombre_color['nombre'] }}
          </span>
      </div>
      @if($solicitud->nro_gde != null)
        <div class="col-md-12 nro_gede_ p-0">
          <labe class="label label-warning">{{trans('forms.nro_gde')}}: {{$solicitud->nro_gde}}</label>
        </div>
      @endif
    </div>

    <div class="disp_block_fl_left">
      <div class="col-md-8">
        @include('contratos.solicitudes.show.showContent')
      </div>
      <div class="col-md-4">
        @include('contratos.solicitudes.show.historial')
      </div>
    </div>

  </div>
@endsection
