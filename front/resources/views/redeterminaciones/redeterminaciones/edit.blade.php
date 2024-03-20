@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
<div class="row">
  <div class="col-md-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
      <li><a href="{{route('contratos.ver', ['id' => $redeterminacion->contrato->id]) }}">@trans('forms.contrato') {{$redeterminacion->contrato->expediente_madre}}</a></li>
      <li class="active"> @if($edit) @trans('index.editar') @else @trans('index.ver') @endif @trans('index.redeterminacion') </li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @if($edit) @trans('index.editar') @else @trans('index.ver') @endif @trans('index.redeterminacion')
          @if($redeterminacion->borrador)
            <span class="badge badge-referencias badge-borrador">
              <i class="fa fa-eraser"></i>
              @trans('index.borrador')
            </span>
          @endif
        </div>
        <div class="buttons-on-title">
          @if($edit)
            @permissions(('redeterminaciones-view'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{route('empalme.redeterminacion.ver', ['id' => $redeterminacion->id]) }}">
                  @trans('index.ver') @trans('index.redeterminacion')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="{{route('empalme.redeterminacion.ver', ['id' => $redeterminacion->id]) }}">
                        @trans('index.ver') @trans('index.redeterminacion')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            @endpermission
          @elseif($redeterminacion->permite_editar)
            @permissions(('redeterminaciones-edit'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{route('empalme.redeterminacion.edit', ['id' => $redeterminacion->id]) }}">
                  @trans('index.editar') @trans('index.redeterminacion')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="{{route('empalme.redeterminacion.edit', ['id' => $redeterminacion->id]) }}">
                        @trans('index.editar') @trans('index.redeterminacion')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            @endpermission
          @endif
        </div>
      </h3>
    </div>

    <div class="row">
      <div class="col-md-12">
        <!-- Panel Detalle Contrato -->
        <div class="panel-group acordion" id="accordion_detalle_contrato" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default panel-view-data border-top-poncho">
            <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading_detalle_contrato">
              <h4 class="panel-title titulo_collapse m-0">
                <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_detalle_contrato" href="#collapse_detalle_contrato" aria-expanded="true" aria-controls="collapse_detalle_contrato">
                  <i class="fa fa-angle-down"></i> {{$redeterminacion->itemizado->contrato_moneda->moneda->nombre_simbolo}}
                </a>
              </h4>
            </div>
            <div id="collapse_detalle_contrato" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_detalle_contrato">
              <div class="panel-body">

                <div class="col-sm-12 col-md-12 form-group">
                  <label class="m-0">@trans('contratos.contrato_madre')</label>
                  <span class="form-control">
                    <a href="{{route('contratos.ver', ['id' => $redeterminacion->contrato->id]) }}">
                      {{$redeterminacion->contrato->nombre_completo}}
                    </a>
                  </span>
                </div>

                <div class="col-sm-6 col-md-3 form-group">
                  <label class="m-0">@trans('index.publicacion')</label>
                  <span class="form-control item_detalle">
                    {{$redeterminacion->publicacion->mes_anio}}
                  </span>
                </div>

                <div class="col-sm-6 col-md-3 form-group">
                  <label class="m-0">@trans('redeterminaciones.nro_salto')</label>
                  <span class="form-control item_detalle" id="costo_unitario_analisis">
                    {{$redeterminacion->nro_salto}}
                  </span>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="alert alert-danger hidden"> <ul> </ul> </div>

    <div class="row">
      <div class="col-md-12">
        <!-- Panel -->
        <div class="panel-group acordion" id="accordion_Itemizado" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default panel-view-data border-top-poncho">
            <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="heading_Itemizado">
              <h4 class="panel-title titulo_collapse m-0">
                <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_Itemizado" href="#collapse_Itemizado" aria-expanded="true" aria-controls="collapse_Itemizado">
                  <i class="fa fa-angle-down"></i> @trans('contratos.itemizado')
                </a>
              </h4>
            </div>
            <div id="collapse_Itemizado" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_Itemizado">
              <div class="panel-body">
            		<div class="panel panel-default">
                  <div class="panel-body">
                    <div class="row">
                      @include('redeterminaciones.itemizado.show_edit')
                    </div>
                  </div>
            		</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
          <div class="text-right">
            <a class="btn btn-small btn-success" href="{{route('contratos.ver.incompleto', ['id' => $redeterminacion->contrato->id, 'accion' => 'polinomica'])}}">@trans('forms.volver')</a>
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
