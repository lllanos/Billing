@if($categoriaCuadro->tiene_componentes)
  @ifnotcount($categoriaCuadro->componentes)
    <div class="sin_datos">
      <h1 class="text-center">@trans('index.no_datos')</h1>
    </div>
  @elseifnotcount
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel-body p-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data">
              <table class="table table-striped table-hover table-bordered zui-table">
                <thead>
                  <tr>
                    <th class="tb_nombre_reporte">@trans('forms.nombre')</th>
                    @if($categoriaCuadro->tiene_indice)
                      <th class="tb_nombre_reporte">@trans('forms.indice')</th>
                    @endif
                    <th class="tb_vr_por_mes">@trans('sol_redeterminaciones.vr_por_mes') {{$publicacion_anterior->mes_anio}}</th>
                    <th class="tb_costo_por_mes">@trans('sol_redeterminaciones.costo_por_mes') {{$publicacion_anterior->mes_anio}}</th>
                    <th class="tb_costo_redeterminado_por_mes">@trans('sol_redeterminaciones.costo_redeterminado_por_mes') {{$publicacion_actual->mes_anio}}</th>
                  </tr>
                </thead>
                <tbody class="tbody_tooltip text-right">
                  @foreach($categoriaCuadro->componentes as $keyComponente => $valueComponente)
                    <tr>
                      <td class="tb_nombre_reporte text-left">
                        <span data-toggle="tooltip" data-placement="bottom" title="{{$valueComponente->nombre}}">
                          {{$valueComponente->nombre}}
                        </span>
                      </td>
                      @if($categoriaCuadro->tiene_indice)
                        <td class="tb_nombre_reporte text-left">
                          <span data-toggle="tooltip" data-placement="bottom" title="{{$valueComponente->indice->nombre_full}}">
                            {{$valueComponente->indice->nombre_full}}
                          </span>
                        </td>
                      @endif
                      <td class="tb_vr_por_mes">
                        @toCuatroDec($valueComponente->vr)
                      </td>
                      <td class="tb_costo_por_mes">
                         @toDosDec($valueComponente->costo_anterior)
                      </td>
                      <td class="tb_costo_redeterminado_por_mes">
                        @toDosDec($valueComponente->vr_por_costo)
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
  @endifnotcount
@endif
