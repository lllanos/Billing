<div id="solicitudes_redeterminacion m-0">
  <div class="col-md-12 col-sm-12">
    <h3 class="container_titulo_solicitudes_asociacion m-0">
      <div class="titulo__solicitudes__asociacion">
        <a href="{{route('redeterminaciones.index')}}" id="link_solicitudes_redeterminacion"
         data-toggle="tooltip" data-placement="bottom" title="{{trans('index.solicitudes_redeterminacion')}}" aria-label="{{trans('index.solicitudes_redeterminacion')}}"
        >{{trans('index.solicitudes_redeterminacion')}}</a>
        <span class="badge">
          {{sizeof($solicitudes)}} / {{$cantidad_solicitudes}}
        </span>
      </div>
      <div class="button__solicitudes__asociacion">
        <a class="btn btn-success" href="{{route('solicitudes.redeterminaciones.solicitar') }}"
        title="@trans('index.solicitar')" id="boton_solicitar_redeterminacion">
          <i class="fa fa-plus" aria-hidden="true"></i> <span>@trans('index.solicitar')</span>
        </a>
      </div>
    </h3>
  </div>
  <div class="col-md-12 col-sm-12">
    @if(sizeof($solicitudes) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="row list-table pt-1 mb-2">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data">
              <table class="table table-striped table-hover table-bordered zui-table">
                <tbody>
                  @foreach ($solicitudes as $key => $solicitud)
                    <tr>
                      <td>
                        <a href="{{route('solicitudes.ver', ['id' => $solicitud->id])}}">
                        {{ $solicitud->contrato->expediente_madre }}</a>
                      </td>

                      <td>{{ $solicitud->salto->moneda_mes_anio }}</td>
                      <td>
                        <div class="contenedor_badges_estado_contrato">
                          @if($solicitud->en_curso)
                            <span class="m-0 badge badge-referencias badge_esperando">@trans('index.esperando')</span>
                          @endif
                          <span class="m-0 badge badge-referencias container_estado_redeterminacion @if($solicitud->en_curso) badge_esperando_estado @endif" style="background: #{{$solicitud->estado_nombre_color['color']}}" >
                            <span class="badge_estado_redeterminacion_tb">{{$solicitud->estado_nombre_color['nombre']}}</span>
                          </span>
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
    <div class="no-data-no-padding height">
        <span>@trans('sol_redeterminaciones.sin_solicitudes')</span>
    </div>
    @endif
  </div>
</div>
