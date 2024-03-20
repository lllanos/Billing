<div id="mis_asociaciones m-0">
  <div class="col-md-12 col-sm-12">
    <h3 class="container_titulo_solicitudes_asociacion m-0">
      <div class="titulo__solicitudes__asociacion">
        <a href="{{route('contratos.index')}}" id="link_mis_asociaciones"
         data-toggle="tooltip" data-placement="bottom" title="@trans('index.list_of') @trans('forms.contratos')" aria-label="@trans('index.list_of') @trans('forms.contratos')"
        >@trans('index.list_of') @trans('forms.contratos')</a>
        <span class="badge">
          {{sizeof($contratos)}} / {{$cantidad_contratos}}
        </span>
      </div>
      {{-- <div class="button__solicitudes__asociacion">
        <a class="btn btn-success" href="{{route('solicitudes.redeterminaciones.solicitar') }}"
        title="{{trans('index.solicitar')}}" id="boton_solicitar_redeterminacion">
          <i class="fa fa-plus" aria-hidden="true"></i> <span>{{trans('index.solicitar')}}</span>
        </a>
      </div> --}}
    </h3>
  </div>
  <div class="col-md-12 col-sm-12">
    @if(sizeof($contratos) > 0)
      <div class="col-md-12 col-sm-12">
        <div class="row list-table pt-1 mb-2">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data">
              <table class="table table-striped table-hover table-bordered zui-table">
                <tbody>
                  @foreach ($contratos as $key => $valueContrato)
                    <tr>
                      <td><a href="{{route('contratos.ver', ['id' => $valueContrato->id]) }}">
                            {{ $valueContrato->numero_contrato }}</a></td>
                      <td>{{ $valueContrato->denominacion }}</td>
                      <td>
                        @if($valueContrato->estado_id != null)
                          <span class="badge" style="background-color:#{{ $valueContrato->estado_nombre_color['color'] }};">
                            {{ $valueContrato->estado_nombre_color['nombre'] }}
                          </span>
                        @endif
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
        <span>{!!trans('contratos.sin_asignaciones') !!}</span>
    </div>
    @endif
  </div>
</div>
