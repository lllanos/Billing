<div id="solicitudes_asociacion" class="m-0">
  <div class="col-md-12 col-sm-12">
    <h3 class="container_titulo_solicitudes_asociacion m-0">
      <div class="titulo__solicitudes__asociacion">
        <a href="{{route('contrato.solicitudes')}}" id="link_solicitudes_asociacion"
        title="@trans('index.solicitudes_asociacion')"
        >@trans('index.solicitudes_asociacion')</a>
        <span class="badge">
          {{sizeof($solicitudes_contrato)}} / {{$cantidad_solicitudes}}
        </span>
      </div>
      <div class="button__solicitudes__asociacion">
        <a class="btn btn-success" href="{{ route('contrato.asociar') }}" id="boton_asociar_contrato" title="@trans('index.solicitar')">
          <i class="fa fa-plus" aria-hidden="true"></i> <span>@trans('index.solicitar')</span>
        </a>
      </div>
    </h3>
  </div>
  <div class="col-md-12 col-sm-12">
    @if(sizeof($solicitudes_contrato) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="row list-table pt-1 mb-2">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data">
              <table class="table table-striped table-hover table-bordered zui-table">
                <tbody>
                  @foreach ($solicitudes_contrato as $key => $solicitud_contrato)
                    <tr>
                      <td class="widget_tb_contrato">
                        <a href="{{route('contrato.solicitud.ver', ['id' => $solicitud_contrato->id])}}">{{ $solicitud_contrato->expediente_madre }}</a></td>
                      <td>{{ $solicitud_contrato->descripcion }}</td>
                      <td class="widget_tb_redeterminacion">
                        <span class="badge" style="background-color:#{{ $solicitud_contrato->estado_nombre_color['color'] }};">
                          {{ $solicitud_contrato->estado_nombre_color['nombre'] }}
                        </span>
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
    <div class="no-data-no-padding">
        <span>@trans('contratos.sin_solicitudes_asociacion')</span>
    </div>
    @endif
  </div>
</div>
<div id="contratos_asociados" class="m-0">
  <div class="col-md-12 col-sm-12">
  <h3 class="titulo__solicitudes__asociacion m-0">
    <a href="{{route('contratos.index')}}" title="@trans('contratos.contratos_asociados')">@trans('contratos.contratos_asociados') </a>
    <span class="badge">
      {{sizeof($user_contratos)}} / {{sizeof(Auth::user()->user_publico->contratos)}}
    </span>
  </h3>
  </div>
  <div class="col-md-12 col-sm-12">
    @if(sizeof($user_contratos) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="row list-table pt-1 mb-2">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data">
              <table class="table table-striped table-hover table-bordered zui-table">
                <tbody>
                  @foreach ($user_contratos as $key => $user_contrato)
                    <tr>
                      <td class="widget_tb_contrato">
                        <a href="{{route('contratos.ver', ['id' => $user_contrato->contrato->id])}}">{{ $user_contrato->contrato->expediente_madre }}</a></td>

                      <td>{{ $user_contrato->descripcion }}</td>
                      <td class="widget_tb_redeterminacion">
                        @if(Auth::user()->puedeSolicitarRedeterminacion($user_contrato))
                          <a data-toggle="tooltip" data-placement="left" title="@trans('contratos.redeterminar')" href="{{ route('solicitudes.redeterminaciones.solicitar', ['id' => $user_contrato->id]) }}">
                            @trans('contratos.redeterminar')
                          </a>
                        @else
                          <span class="badge" style="background-color:var(--red-redeterminacion-color);">
                            @trans('contratos.no_redeterminan')
                          </span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="no-data-no-padding">
        <span>@trans('contratos.sin_contratos_asociados')</span>
      </div>
    @endif
  </div>
</div>
