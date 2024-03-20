@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        @if($publicados)
          <li class="active">@trans('index.list_of') @trans('forms.contratos')</li>
        @else
          <li class="active">@trans('forms.bandeja_contratos')</li>
        @endif
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            {{ $publicados ?  trans('index.list_of') . " " . trans('forms.contratos') : trans('forms.bandeja_contratos') }}
          </div>

          @if($publicados)
            <div class="buttons-on-title">
              @permissions(('contrato-create'))
              <div class="button_desktop">
                <a class="btn btn-success pull-right" href="{{route('contratos.create')}}">
                  @trans('forms.nuevo') @trans('index.contrato')
                </a>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="{{route('contratos.create')}}">
                        @trans('forms.nuevo') @trans('index.contrato')
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              @endpermission
            </div>
          @endif
        </h3>
      </div>
    </div>

    <div {!! "class=\"input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5 badges_vr__input_excel\" role=\"group\" aria-label=\"...\"" !!}>

      @if($publicados)
        <div class="col-xs-12 col-sm-6 col-md-6 container_badges_vr">
          <span class="badge_referencias_">{{trans('index.referencias')}}</span>
          <span class="badge badge-referencias_vr" style="background-color: var(--green-redeterminacion-color);">{{trans('contratos.redeterminan')}}</span>
          <span class="badge badge-referencias_vr m-0" style="background-color: var(--red-redeterminacion-color);">{{trans('contratos.no_redeterminan')}}</span>
        </div>
      @endif

      <div class="col-xs-12 col-sm-6 col-md-6 contenedor_input_dos_btns mb-_5 {{ $publicados ? '' : 'col-sm-offset-6 col-md-offset-6' }}">
        @permissions('contrato-export', 'nuevo-contrato-export')
          <form class="form_excel" method="POST" data-action="{{ route('contratos.export', ['publicados' => (bool)$publicados]) }}" id="form_excel">
            {{ csrf_field() }}

            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">

            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
                <i class="fa fa-file-excel-o fa-2x"></i>
              </button>
          </form>
          @endpermission

          <form method="POST" data-action="{{ route('contratos.index.post') }}" id="search_form">
            {{ csrf_field() }}

            <input type="text" class="search-input form-control input_dos_btns buscar_si enter_submit" name="search_input" id="search_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">

            <span class="input-group-btn">
              <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
                <i class="fa fa-search"></i>
              </button>
            </span>
          </form>
        </div>
    </div>

    @if(sizeof($contratos) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="list-table pt-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller"> <!-- zui-no-data -->
              <table class="table table-striped table-hover table-bordered zui-table">
                <thead>
                <tr>
                  <th class="text-center"></th>

                  <th>@trans('contratos.numero_contrato_th')</th>

                  <th>@trans('forms.contratista')</th>

                  <th>@trans('contratos.numero_contratacion_th')</th>

                  <th>@trans('contratos.expediente_madre_th')</th>

                  <th>@trans('contratos.resoluc_adjudic_th')</th>

                  <th>@trans('forms.denominacion')</th>

                  <th>@trans('forms.montos')</th>

                  @if($publicados)
                    <th class="text-center">@trans('forms.ultimo_salto')</th>
                    <th class="text-center">@trans('contratos.ultima_solicitud_th')</th>
                  @endif

                  @if($publicados)
                    <th class="text-center">@trans('forms.vr')</th>
                  @endif

                  @if(!Auth::user()->usuario_causante)
                    <th>@trans('forms.causante')</th>
                  @endif

                  <th class="text-center">@trans('forms.estado')</th>

                  @if(!$publicados)
                    <th class="text-center">{{trans('forms.motivo')}}</th>
                  @endif

                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
                </thead>
                <tbody class="tbody_js">
                @foreach($contratos as $keyContratos => $valueContrato)
                  <tr id="contrato_{{$valueContrato->id}}">
                    <td class="text-center">
                      @if($valueContrato->borrador)
                        <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                      @elseif($valueContrato->doble_firma)
                        <i class="fa fa-pencil" data-toggle="tooltip" data-placement="bottom" title="{{ trans((empty($valueContrato->firma_ar) && empty($valueContrato->firma_py)) ? 'index.pendiente_firmas' : 'index.pendiente_firma') }}"></i>
                      @endif

                      @if($valueContrato->incompleto_show['status'])
                        <i class="fa fa-star-half-empty" data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valueContrato->incompleto_show['mensaje']}}"></i>
                      @endif
                    </td>

                    <td>{{ $valueContrato->numero_contrato }} </td>

                    <td>
                      <span data-toggle="tooltip" data-placement="top" title="{{ $valueContrato->contratista_nombre_documento }}">
                        {{ $valueContrato->contratista_nombre_documento }}
                      </span>
                    </td>

                    <td>{{ $valueContrato->numero_contratacion }}</td>

                    <td>{{ $valueContrato->expediente_madre }} </td>

                    <td>{{ $valueContrato->resoluc_adjudic }} </td>

                    <td>{{ $valueContrato->denominacion }}</td>

                    <td id="montos">
                      @if($valueContrato->tiene_contratos_monedas)
                        @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                          @if($valueContratoMoneda->monto_vigente != null && $valueContratoMoneda->moneda != null)
                            <span class="badge">
                              {{$valueContratoMoneda->moneda->simbolo}} {{$valueContratoMoneda->monto_vigente_dos_dec }}
                            </span>
                          @endif
                        @endforeach
                      @endif
                    </td>

                    @if($publicados)
                      <td>
                        @if(!$valueContrato->borrador && $valueContrato->tiene_contratos_monedas)
                          @if($valueContratoMoneda->contrato->ultimo_salto != null)
                            {{ $valueContrato->ultimo_salto_m_y }}
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

                      <td>{{ $valueContrato->ultima_solicitud }}</td>
                    @endif

                    @if($publicados)
                      <td id="vr_salto">
                        @if($valueContrato->no_redetermina)
                          <i class="fa fa fa-times-circle text-danger"></i>
                          @trans('contratos.no_redetermina')
                        @else
                          @if(!$valueContrato->borrador && $valueContrato->tiene_contratos_monedas)
                            @foreach($valueContrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda)
                              @if($valueContratoMoneda->en_porcentaje_de_redeterminacion)
                                <span class="badge" style="background-color:var(--green-redeterminacion-color);">
                              {{$valueContratoMoneda->moneda->nombre}}
                                  {{$valueContratoMoneda->nombre}} ({{$valueContratoMoneda->ultima_variacion->variacion_show }})
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
                        @endif
                      </td>
                    @endif

                    @if(!Auth::user()->usuario_causante)
                      <td class="text-center">
                        @if($valueContrato->causante_id != null)
                          <span class="badge" style="background-color:#{{ $valueContrato->causante_nombre_color['color'] }};">
                        {{ $valueContrato->causante_nombre_color['nombre'] }}
                      </span>
                        @endif
                      </td>
                    @endif

                    <td>
                      @if($valueContrato->estado_id != null)
                        <span class="badge" style="background-color:#{{ $valueContrato->estado_nombre_color['color'] }};">
                          {{ $valueContrato->estado_nombre_color['nombre'] }}
                        </span>
                      @endif
                    </td>

                    @if(!$publicados)
                      <td>
                        <span class="badge" style="background-color:#{{ $valueContrato->motivo_bandeja_nombre_color['color'] }};">
                          {{ $valueContrato->motivo_bandeja_nombre_color['nombre'] }}
                        </span>
                      </td>
                    @endif

                    <td class="actions-col noFilter">
                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                        </button>

                        <ul class="dropdown-menu pull-right">
                          @if($publicados)

                            @permissions(('contrato-view'))
                              <li>
                                <a href="{{route('contratos.ver', ['id' => $valueContrato->id]) }}"><i class="glyphicon glyphicon-eye-open"></i>
                                  @trans('index.ver')
                                </a>
                              </li>
                            @endpermission

                            @permissions(('contrato-edit'))
                              @if(!$valueContrato->borrador)
                                <li>
                                  <a href="{{route('contratos.edit', ['id' => $valueContrato->id]) }}">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                    @trans('index.editar') @trans('index.contrato')
                                  </a>
                                </li>
                              @endif
                            @endpermission

                            @permissions('contrato-edit', 'contrato-edit-borrador')
                              @if($valueContrato->borrador)
                                <li>
                                  <a href="{{route('contratos.edit', ['id' => $valueContrato->id]) }}">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                    @trans('index.editar')
                                  </a>
                                </li>
                              @endif
                            @endpermission

                            @if($valueContrato->incompleto['status'])

                              @if($valueContrato->incompleto['polinomica'])
                                @permissions(('polinomica-edit'))
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'polinomica'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i>@trans('index.editar') @trans('contratos.polinomica')
                                  </a>
                                </li>
                                @endpermission
                              @endif

                              @if($valueContrato->incompleto['itemizado'])
                                @permissions(('itemizado-manage'))
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'itemizado'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i> @trans('index.editar') @trans('contratos.itemizado')
                                  </a>
                                </li>
                                @endpermission
                              @endif

                              @if($valueContrato->incompleto['cronograma'])
                                @permissions(('cronograma-manage'))
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'cronograma'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i> @trans('index.editar') @trans('contratos.cronograma')
                                  </a>
                                </li>
                                @endpermission
                              @endif

                              @if($valueContrato->incompleto['falta_finalizar_empalme'])
                                @permissions(('cronograma-manage'))
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'empalme'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i> @trans('forms.completar') @trans('contratos.empalme')
                                  </a>
                                </li>
                                @endpermission
                              @endif

                            @elseif($valueContrato->incompleto_show['status'])
                              @if(
                                (isset($valueContrato->incompleto_show['sin_analisis']) && $valueContrato->incompleto_show['sin_analisis'])
                                || (isset($valueContrato->incompleto_show['analisis_incompleto']) && $valueContrato->incompleto_show['analisis_incompleto'])
                              )
                                @permissions('analisis_precios-edit')
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'analisis_precios'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i>@trans('index.editar') @trans('contratos.analisis_precios')
                                  </a>
                                </li>
                                @endpermission
                              @endif

                              @if($valueContrato->incompleto_show['empalme'])
                                @permissions(('empalme-manage'))
                                <li>
                                  <a href="{{route('contratos.editar.incompleto', ['id' => $valueContrato->id, 'accion' => 'empalme'])}}">
                                    <i class="glyphicon glyphicon-pencil"></i> @trans('forms.completar') @trans('contratos.empalme')
                                  </a>
                                </li>
                                @endpermission
                              @endif
                            @endif

                            @if($valueContrato->permite_adendas)
                              <li>
                                <a href="{{route('adenda.create', ['contrato_id' => $valueContrato->id])}}">
                                  <i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.adenda')
                                </a>
                              </li>
                            @endif

                            @if($valueContrato->permite_ampliaciones_de_obra)
                              <li>
                                <a href="{{route('ampliacion.create', ['contrato_id' => $valueContrato->id])}}">
                                  <i class="fa fa-plus-square"></i> @trans('index.solicitar') @trans('contratos.ampliacion_reprogramacion')
                                </a>
                              </li>
                            @endif

                            @if($valueContrato->permite_certificados)
                              <li class="loadingToggle">
                                <a href="{{route('certificado.create', ['contrato_id' => $valueContrato->id, 'empalme' => false])}}">
                                  <i class="fa fa-plus-square"></i>
                                  @trans('index.solicitar')
                                  @trans('contratos.certificado')
                                  @trans('index.mes') {{count($valueContrato->certificados()->whereRedeterminado(0)->get()) + 1}}
                                </a>
                              </li>
                            @endif

                            @permissions(('contrato-delete'))

                              @if($valueContrato->borrador)
                              <li>
                                <a class="eliminar btn-confirmable-prevalidado"
                                  data-prevalidacion="{{ route('contratos.preDelete', ['id' => $valueContrato->id]) }}"
                                  data-body="{{trans('index.confirmar_eliminar.contrato', ['nombre' => $valueContrato->nombre_completo])}}"
                                  data-action="{{ route('contratos.delete', ['id' => $valueContrato->id]) }}"
                                  data-si="@trans('index.si')" data-no="@trans('index.no')">
                                  <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                </a>
                              </li>
                            @endif

                            @endpermission

                          @else
                            @permissions(('nuevo-contrato-view'))
                            <li>
                              <a href="{{route('contrato.nuevos.ver', ['id' => $valueContrato->id]) }}">
                                <i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')
                              </a>
                            </li>
                            @endpermission
                          @endif

                          @if($publicados && $valueContrato->requiere_analisis && 1 == 2)
                            <li>
                              <a href="{{ route('AnalisisPrecios.edit', ['contrato_id' => $valueContrato->id]) }}" id="btn_ap_{{$valueContrato->id}}">
                                <i class="icono-arg-pago m-0" style="position: relative; top: 5px;"></i>
                                @trans('contratos.analisis_precio')
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
        {{ $contratos->render() }}
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
