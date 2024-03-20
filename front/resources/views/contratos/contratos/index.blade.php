@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">@trans('index.mis') @trans('forms.contratos')</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.mis') @trans('forms.contratos')
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop">
              <a class="btn btn-success" href="{{ route('contrato.asociar') }}" id="btn_asociar">
                @trans('index.solicitar_asociacion')
              </a>
            </div>
            <div class="button_responsive">
              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li><a href="{{ route('contrato.asociar') }}">@trans('index.solicitar_asociacion')</a></li>
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>

    <!--Input file excel con 2 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-1 badges_vr__input_excel" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-6 col-md-6 container_badges_vr">
        <span class="badge_referencias_">{{trans('index.referencias')}}</span>
        <span class="badge badge-referencias_vr" style="background-color: var(--green-redeterminacion-color);">{{trans('contratos.redeterminan')}}</span>
        <span class="badge badge-referencias_vr m-0" style="background-color: var(--red-redeterminacion-color);">@trans('contratos.no_redeterminan')</span>
      </div>
      <!--Input file excel con 2 form-->

      <div class="col-xs-12 col-sm-6 col-md-6 contenedor_input_dos_btns">
        <form  class="form_excel" method="POST" data-action="{{ route('contrato.export') }}" id="form_excel">
          {{ csrf_field() }}
          <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="" placeholder="@trans('forms.busqueda_placeholder')">
          <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')}}" aria-label="@trans('index.descargar_a_excel')}}">
            <i class="fa fa-file-excel-o fa-2x"></i>
          </button>
        </form>
        <form method="POST" data-action="{{ route('contratos.index.post') }}" id="search_form">
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
    <!--Fin Input file excel con 2 form-->

    @if(sizeof($user_contratos) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="list-table pt-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller"> <!-- zui-no-data -->
              <table class="table table-striped table-hover table-bordered zui-table">
                <thead>
                  <tr>
                    <th class="text-center"></th>
                    <th>@trans('contratos.numero_contrato_th')</th>
                    <th>@trans('contratos.numero_contratacion_th')</th>
                    <th>@trans('contratos.expediente_madre_th')</th>
                    <th>@trans('contratos.resoluc_adjudic_th')</th>
                    <th>@trans('forms.denominacion')</th>
                    <th>{{trans('forms.montos')}}</th>
                    <th class="text-center">@trans('forms.ultimo_salto')</th>
                    <th class="text-center">@trans('contratos.ultima_solicitud_th')</th>
                    <th class="text-center">@trans('forms.vr')</th>
                    <th class="text-center">@trans('forms.estado')</th>
                    <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                  </tr>
                </thead>
                <tbody class="tbody_js">
                  @foreach($user_contratos as $keyContratos => $valueContrato)
                    <tr id="contrato_{{$valueContrato->id}}">
                      <td class="text-center">
                      </td>
                      <td>{{ $valueContrato->contrato->numero_contrato }} </td>
                      <td>{{ $valueContrato->contrato->numero_contratacion }}</td>
                      <td>{{ $valueContrato->contrato->expediente_madre }} </td>
                      <td>{{ $valueContrato->contrato->resoluc_adjudic }} </td>
                      <td>{{ $valueContrato->contrato->denominacion }}</td>
                      <td id="montos">
                        @if($valueContrato->contrato->tiene_contratos_monedas)
                          @foreach($valueContrato->contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                            @if($valueContratoMoneda->monto_vigente != null && $valueContratoMoneda->moneda != null)
                              <span class="badge">
                                {{$valueContratoMoneda->moneda->simbolo}} {{$valueContratoMoneda->monto_vigente_dos_dec }}
                              </span>
                            @endif
                          @endforeach
                        @endif
                      </td>
                      <td>
                          @if(!$valueContrato->contrato->borrador && $valueContrato->contrato->tiene_contratos_monedas)
                            @if($valueContratoMoneda->contrato->ultimo_salto != null)
                              {{ $valueContrato->contrato->ultimo_salto_m_y }}
                              @if($valueContratoMoneda->contrato->ultimo_salto->solicitado)
                                <i class="fa fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="@trans('index.solicitado')"></i>
                              @else
                                <i class="fa fa fa-times-circle text-danger" data-toggle="tooltip" data-placement="top" title="@trans('index.no_solicitado')"></i>
                              @endif
                            @elseif($valueContrato->empalme && $valueContratoMoneda->fecha_ultima_redeterminacion != null)
                              {{$valueContratoMoneda->fecha_ultima_redeterminacion_my}}
                              <i class="fa fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="@trans('index.solicitado')"></i>
                            @endif
                          @endif
                        </td>
                      <td>{{ $valueContrato->contrato->ultima_solicitud }}</td>


                      <td id="vr_salto">
                        @if(!$valueContrato->contrato->borrador && $valueContrato->contrato->tiene_contratos_monedas)
                          @foreach($valueContrato->contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                            @if($valueContratoMoneda->en_porcentaje_de_redeterminacion)
                              <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                                {{$valueContratoMoneda->moneda->nombre}} ({{$valueContratoMoneda->ultima_variacion->variacion_show }})
                              </span>
                            @else
                              <span class="badge" style="background-color:var(--red-redeterminacion-color);">
                                {{$valueContratoMoneda->moneda->nombre}}
                                @if($valueContratoMoneda->ultima_variacion != null)
                                  ({{ $valueContratoMoneda->ultima_variacion->variacion_show }})
                                @endif
                              </span>
                            @endif
                          @endforeach
                        @endif

                        @if($valueContrato->contrato->no_redetermina)
                          <i class="fa fa fa-times-circle text-danger"></i>
                          {{ trans('contratos.no_redetermina')}}
                        @endif
                      </td>

                      <td>
                          <span class="badge" style="background-color:#{{ $valueContrato->contrato->estado_nombre_color['color'] }};">
                            {{ $valueContrato->contrato->estado_nombre_color['nombre'] }}
                          </span>
                      </td>
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                              <li><a href="{{route('contratos.ver', ['id' => $valueContrato->contrato->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> {{trans('index.ver')}}</a></li>
                              @if(Auth::user()->puedeSolicitarRedeterminacion($valueContrato))
                                <li>
                                  <a href="{{ route('solicitudes.redeterminaciones.solicitar', ['id' => $valueContrato->id]) }}"><i class="fa fa-plus-square"></i>@trans('contratos.redeterminar')</a>
                                </li>
                              @endif
                              @if($valueContrato->contrato->permite_adendas)
                                <li><a href="{{route('adenda.create', ['contrato_id' => $valueContrato->contrato->id])}}"><i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.adenda')</a></li>
                              @endif
                              @if($valueContrato->contrato->permite_ampliaciones_de_obra)
                                <li><a href="{{route('ampliacion.create', ['contrato_id' => $valueContrato->contrato->id])}}"><i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.ampliacion_reprogramacion')</a></li>
                              @endif
                              @if($valueContrato->contrato->permite_certificados)
                                <li class="loadingToggle">
                                  <a href="{{route('certificado.create', ['contrato_id' => $valueContrato->contrato->id, 'empalme' => false])}}">
                                    <i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.certificado')
                                    @trans('index.mes') {{count($valueContrato->contrato->certificados()->whereRedeterminado(0)->get()) + 1}}
                                  </a>
                                </li>
                              @endif
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
      </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos_js"></div>
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
  </div>
@endsection
