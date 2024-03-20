@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
        <li><a href="{{route('contratos.ver', ['id' => $certificado->contrato->id]) }}">@trans('forms.contrato') {{$certificado->contrato->expediente_madre}}</a></li>
        <li class="active">@trans('contratos.certificado')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('contratos.certificado') @trans('index.de') {{$certificado->contrato->expediente_madre}}
          </div>
          <div class="buttons-on-title">
            @if($certificado->borrador)
              @permissions(('certificado-edit'))
                <div class="button_desktop">
                  <a class="btn btn-success pull-right" href="{{route('ampliacion.edit', ['id' => $certificado->id]) }}">
                    @trans('index.editar') @trans('contratos.certificado')
                  </a>
                </div>
                  <div class="button_responsive">
                    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu pull-right">
                        <li>
                          <a href="{{route('ampliacion.edit', ['id' => $certificado->id]) }}">
                            @trans('index.editar') @trans('contratos.certificado')
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
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <!-- Header -->
      <div class="row">
        <div class="col-md-12 ">
          <div class="estados_contratos">
            <div class="container_badges_referencias badges_refencias_responsive_flex">
              <span class="badge badge-referencias" style="background-color:#{{ $certificado->contrato->causante_nombre_color['color'] }};">
                {{ $certificado->contrato->causante_nombre_color['nombre'] }}
              </span>

              @if($certificado->borrador)
                <span class="badge badge-referencias badge-borrador">
                  <i class="fa fa-eraser"></i>
                  @trans('index.borrador')
                </span>
              @endif

            </div>
          </div>
        </div>
      </div>
      <!-- FIN Header -->

      <!-- Panel Detalle Contrato -->
      <div class="panel-group acordion" id="accordion_detalle_contrato" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default panel-view-data border-top-poncho">
          <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne_detalle_contrato">
            <h4 class="panel-title titulo_collapse m-0">
              <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_detalle_contrato" href="#collapseOne_detalle_contrato" aria-expanded="true" aria-controls="collapseOne_detalle_contrato">
                <i class="fa fa-angle-down"></i> {{$certificado->expediente}}
              </a>
            </h4>
          </div>
          <div id="collapseOne_detalle_contrato" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_detalle_contrato">
            <div class="panel-body">
              <div class="col-md-12 form-group">
                <label class="m-0">@trans('contratos.contrato_madre')</label>
                <span class="form-control">
                  <a href="{{route('contratos.ver', ['id' => $certificado->contrato->id]) }}">
                    {{$certificado->contrato->nombre_completo}}
                  </a>
                </span>
              </div>

              @if($certificado->tipo_ampliacion->nombre == 'ampliacion')
                <div class="col-sm-12 col-md-4 form-group">
                  <label class="m-0">{{trans('contratos.plazo_obra')}}</label>
                  <span class="form-control item_detalle">
                    {{$certificado->plazo_completo}}
                  </span>
                </div>
              @endif

              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">{{trans('contratos.resoluc_aprobatoria')}}</label>
                <span class="form-control item_detalle">
                  {{$certificado->resoluc_aprobatoria}}
                </span>
              </div>

              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">{{trans('forms.motivo')}}</label>
                <span class="form-control item_detalle">
                  {{$certificado->motivo_nombre}}
                </span>
              </div>

              @if($certificado->observaciones != '')
                <div class="col-sm-12 col-md-12 form-group">
                  <label class="m-0">@trans('index.observaciones')</label>
                  <span class="form-control item_detalle">
                    {{$certificado->observaciones}}
                  </span>
                </div>
              @endif
            </div>

            @if($certificado->adjuntos != null)
              @foreach($certificado->adjuntos as $key => $adjunto)
                <div class="pb-1">
                  <span id="adjunto_anterior_{{$key}}" class="hide-on-ajax ml-35">
                    <i class="fa fa-paperclip grayCircle"></i>
                    <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
                  </span>
                </div>
              @endforeach
            @endif

          </div>
        </div>
      </div>
      <!--Fin Panel Detalle Contrato-->

    </div>
  </div>

@endsection

@section('scripts')
@endsection
