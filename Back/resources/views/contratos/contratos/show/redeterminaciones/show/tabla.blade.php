@foreach($contrato->redeterminaciones_por_moneda($empalme) as $redeterminaciones)
  {{-- <div class="panel-default acordion" id="accordion-{{$redeterminaciones['key']}}" role="tablist" aria-multiselectable="true"> --}}
    {{-- <div class="panel panel-default"> --}}
      <div class="panel-body pt-0 pb-0">
        <div class="panel-body panel_con_tablas_y_sub_tablas contenedor_all_tablas pt-1 pl-0 pr-0">
          <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading-{{$redeterminaciones['key']}}">
            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
              <a class="btn_acordion dos_datos collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion-{{$redeterminaciones['key']}}" href="#collapse_{{$redeterminaciones['key']}}" aria-expanded="true" aria-controls="collapse_{{$redeterminaciones['key']}}">
                <div class="container_icon_angle"><i class="fa fa-angle-down"></i> {{$redeterminaciones['nombre']}}</div>
              </a>
            </h4>
          </div>
          <div id="collapse_{{$redeterminaciones['key']}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{$redeterminaciones['key']}}">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="panel-body p-0">
                  <div class="zui-wrapper zui-action-32px-fixed">
                    <div class="zui-scroller zui-no-data">
                      <table class="table table-striped table-hover table-bordered zui-table">
                        <thead>
                          <tr>
                            <th class="text-center th-tag"></th>
                            <th>@trans('redeterminaciones.nro_salto')</th>
                            <th>@trans('forms.fecha')</th>
                            <th>@trans('forms.vr')</th>
                            <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                          </tr>
                        </thead>
                        <tbody class="tbody_js">
                          @foreach($redeterminaciones['redeterminaciones'] as $redeterminacion)
                            <tr id="redeterminaciones_{{$redeterminacion->id}}">
                              <td class="text-center">
                                @if($redeterminacion->borrador)
                                  <i class="fa fa-eraser" data-toggle="tooltip" data-placement="bottom" title="@trans('index.borrador')"></i>
                                @endif
                              </td>
                              <td>{{$redeterminacion->nro_salto}}</td>
                              <td>{{$redeterminacion->publicacion->mes_anio}}</td>
                              <td>@toCuatroDec($redeterminacion->variacion)</td>
                              <td class="actions-col noFilter">
                                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                    <i class="fa fa-ellipsis-v"></i>
                                  </button>
                                  <ul class="dropdown-menu pull-right">
                                    @permissions(('redeterminaciones-view'))
                                      <li><a href="{{route('empalme.redeterminacion.ver', ['id' => $redeterminacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                                    @endpermission
                                    @if($redeterminacion->permite_editar)
                                      @permissions(('redeterminaciones-edit'))
                                         <li>
                                          <a href="{{route('empalme.redeterminacion.edit', ['id' => $redeterminacion->id]) }}"><i class="fa fa-pencil"></i> @trans('index.editar')</a></li>
                                      @endpermission
                                      @permissions(('redeterminaciones-delete'))
                                        <li>
                                          <a class="eliminar btn-confirmable-prevalidado"
                                          data-prevalidacion="{{ route('empalme.redeterminaciones.preDelete', ['id' => $redeterminacion->id]) }}"
                                          data-body="@trans('index.confirmar_eliminar.redeterminaciones', ['salto' => $redeterminacion->nro_salto])"
                                          data-action="{{ route('empalme.redeterminaciones.delete', ['id' => $redeterminacion->id]) }}"
                                          data-si="@trans('index.si')" data-no="@trans('index.no')">
                                            <i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')
                                          </a>
                                        </li>
                                      @endpermission
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
            </div>
          </div>
        </div>
      </div>
    {{-- </div> --}}
  {{-- </div> --}}
@endforeach
