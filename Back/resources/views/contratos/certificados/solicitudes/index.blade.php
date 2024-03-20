@extends ('layout.app')

@section('title', config('app.name') )

@section('content')

  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
          @if($finalizadas)
             <li class="active">@trans('certificado.sol_certificaciones_finalizadas')</li>
           @else
             <li class="active">@trans('certificado.sol_certificaciones_en_proceso')</li>
          @endif
      </ol>
      <div class="page-header">
        <h3>
          @if($finalizadas)
              @trans('certificado.sol_certificaciones_finalizadas')
           @else
              @trans('certificado.sol_certificaciones_en_proceso')
          @endif
        </h3>
      </div>
    </div>


    <!--Input file excel con 2 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
        @permissions(('solicitudes-certificado_en_proceso-export'))
          <form  class="form_excel" method="POST" data-action="{{ route('export.contratistas') }}" id="form_excel">
            {{ csrf_field() }}
            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
              <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
          </form>
        @endpermission
        @if($finalizadas)
          <form method="POST" data-action="{{ route('solicitudes.certificado_finalizadas.post') }}" id="search_form">
        @else
          <form method="POST" data-action="{{ route('solicitudes.certificado_en_proceso.post') }}" id="search_form">
        @endif
          {{ csrf_field() }}
          <input type="text" class="search-input form-control input_dos_btns buscar_si enter_submit" name="search_input" id="search_input" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
          <span class="input-group-btn">
            <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
        </form>
      </div>
    </div>
    <!--Input file excel con 2 form-->
    @if(sizeof($certificados) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table pt-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead class="thead_js">
                <tr>
                  <th>@trans('forms.fecha')</th>
                  <th>@trans('forms.expediente_madre')</th>
                  <th>@trans('certificado.nr_certificado')</th>
                  <th>@trans('certificado.tipo_certificado')</th>
                  <th>@trans('forms.estado')</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">
                @foreach ($certificados as $key => $certificado)
                  <tr>
                    <td>{{ $certificado->created_at }}</td>
                    <td>{{ $certificado->contrato->expediente_madre }}</td>
                    <td>@trans('index.mes') {{$certificado->mes}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</td>
                    <td> @if($certificado->redeterminado) @trans('certificado.redeterminado') @else @trans('certificado.basico') @endif</td>
                    <td>
                      <span class="badge" style="background-color:#{{ $certificado->estado['color'] }};">
                        {{ $certificado->estado['nombre_trans'] }}
                      </span>
                    </td>
                    <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">

                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            @if($finalizadas) @php($breadcrumb = 2) @else @php($breadcrumb = 1) @endif

                            <li><a @if($certificado->redeterminado)
                                     href="{{route('redeterminaciones.certificado.ver', ['id' => $certificado->id, 'breadcrumb' => $breadcrumb]) }}"
                                   @else
                                     href="{{route('certificado.ver', ['id' => $certificado->id, 'breadcrumb' => $breadcrumb]) }}"
                                   @endif> <i class="glyphicon glyphicon-eye-open"></i>@trans('index.ver')</a>
                            </li>

                            @if($certificado->puede_aprobar)
                              @permissions(('certificado-aprobar'))
                               <li><a class="aprobar btn-confirmable"
                                  data-body="@trans('index.confirmar_aprobar.certificado', ['mes' => $certificado->mes]) @include('contratos.certificados.aclaracion')"
                                  data-action="{{route('solicitudes.certificado.aprobar', ['id' => $certificado->id]) }}"
                                  data-si="@trans('index.si')" data-no="@trans('index.no')">
                                    <i class="glyphicon glyphicon-ok"></i> @trans('index.aprobar')
                                  </a>
                               </li>
                              @endpermission
                              @permissions(('certificado-rechazar'))
                                <li>
                                    <a href="javascript:void(0);" class="open-modal-rechazar"
                                      id="btn_rechazar_{{$certificado->id}}"
                                      data-action="{{ route('solicitudes.certificado.rechazar', ['id' => $certificado->id]) }}"
                                      data-id="{{$certificado->id}}"
                                      data-title="{{trans('index.confirmar_rechazar.certificado', ['mes' => $certificado->mes])}}"
                                      title="{{ trans('index.rechazar')}}">
                                      <i class="glyphicon glyphicon-remove"></i> @trans('index.rechazar')
                                    </a>
                                </li>
                              @endpermission
                            @endif

                            @if($certificado->puede_aprobar_redeterminado)
                              <li><a class="aprobar btn-confirmable"
                                  data-body="@trans('index.confirmar_aprobar.certificado_redet', ['mes' => $certificado->mes]) @include('contratos.certificados.aclaracion')"
                                  data-action="{{ route('solicitudes.certificado.aprobarCertificadoRedeterminado', ['id' => $certificado->id]) }}"
                                  data-si="@trans('index.si')" data-no="@trans('index.no')">
                                    <i class="glyphicon glyphicon-ok"></i> @trans('index.aprobar')
                                </a>
                              </li>
                            @endif
                            <li> <a href="{{route('export.certificado', ['id' => $certificado->id]) }}"> <i class="glyphicon glyphicon-save-file"></i> @trans('index.descargar') </a> </li>
                            <li><a class="open-historial mouse-pointer"
                                data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}" id="btn_historial" data-toggle="tooltip" data-placement="bottom" title="@trans('index.historial')">
                                <i class="fa fa-history" aria-hidden="true"></i>@trans('index.historial')</a>
                            </li>
                          </ul>
                        </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      {{ $certificados->render() }}
    </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
    <div class="col-md-12 col-sm-12">
      <div class="sin_datos_js"></div>
    </div>
  </div>
@endsection

@section('modals')
  <div id="modalHistorial" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="fa fa-times fa-2x"></span>
          </button>
          <h4 class="modal-title">
            @trans('index.historial') <span></span>
          </h4>
        </div>
        <div class="modal-body">
          <div class="modalContentScrollable">
            <div class="row">
              <div class="col-md-12 panel-historial">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('contratos.certificados.solicitudes.modals.rechazar')
@endsection
