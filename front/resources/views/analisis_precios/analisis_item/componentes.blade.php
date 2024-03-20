{{-- @php($redetermina = $categoria->analisis_item->analisis_precios->es_redeterminacion) --}}
@if($categoria->tiene_componentes)
  @ifnotcount($categoria->componentes)
    <div class="sin_datos">
      <h1 class="text-center">@trans('index.no_datos')</h1>
    </div>
  @elseifnotcount
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="panel-body p-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller @if(!$edit || ($edit && $redetermina)) zui-no-data @endif">
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead>
                <tr>
                  <th class="tb_nombre_reporte">@trans('forms.nombre')</th>
                  @if($categoria->tiene_descripcion)
                    <th class="tb_nombre_reporte">@trans('analisis_item.descripcion_calculo')</th>
                  @endif
                  @if($categoria->tiene_indice)
                    <th class="tb_nombre_reporte">@trans('forms.indice')</th>
                  @endif

                  @if($categoria->tiene_cantidad && !$redetermina)
                    <th class="td-130px">@trans('forms.cantidad')</th>
                  @endif
                  @if($categoria->tiene_costo_unitario && !$redetermina)
                    <th class="td-130px">@trans('analisis_item.costo_unitario')</th>
                  @endif
                  <th class="">@trans('analisis_item.costo_total')</th>

                  @if($redetermina)
                    <th class="">@trans('analisis_item.costo_total') @trans('sol_redeterminaciones.redeterminado')</th>
                  @endif

                  @if($edit && !$redetermina)
                    <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                  @endif
                </tr>
              </thead>
              <tbody class="tbody_tooltip">
                @foreach($categoria->componentes as $keyComponente => $valueComponente)
                  <tr>
                    <td class="tb_nombre_reporte">
                      <span data-toggle="tooltip" data-placement="bottom" title="{{$valueComponente->nombre}}">
                        {{$valueComponente->nombre}}
                      </span>
                    </td>
                    @if($categoria->tiene_descripcion)
                      <td class="tb_nombre_reporte">
                        <span data-toggle="tooltip" data-placement="bottom" title="{{$valueComponente->descripcion}}">
                          {{$valueComponente->descripcion}}
                        </span>
                      </td>
                    @endif
                    @if($categoria->tiene_indice)
                      <td class="tb_nombre_reporte">
                        <span data-toggle="tooltip" data-placement="bottom" title="{{$valueComponente->indice->nombre_full}}">
                          {{$valueComponente->indice->nombre_full}}
                        </span>
                      </td>
                    @endif
                    @if($categoria->tiene_cantidad && !$redetermina)
                      <td class="td-130px">
                        <span>
                          @toDosDec($valueComponente->cantidad)
                        </span>
                      </td>
                    @endif
                    @if($categoria->tiene_costo_unitario && !$redetermina)
                      <td class="td-130px">
                        <span>
                          @toDosDec($valueComponente->costo_unitario)
                        </span>
                      </td>
                    @endif
                    <td class="">
                      <span class="text-right">
                        @if($redetermina)
                          @toDosDec($valueComponente->costo_anterior_o_inicio)
                        @else
                          @toDosDec($valueComponente->costo_total_adaptado)
                        @endif
                      </span>
                    </td>
                    @if($redetermina)
                      @if($edit)
                        <td class="pull-right input_in_collapsable input_actual">
                          <input type='text' class="form-control currency actual" id='val_redeter_{{$valueComponente->categoria_id}}_{{$valueComponente->id}}' name='val[{{$valueComponente->categoria_id}}][{{$valueComponente->id}}]'
                          value="@toDosDec($valueComponente->costo_total_adaptado)">
                          <span class="input-group-addon">
                            {{$categoria->analisis_item->analisis_precios->itemizado->contrato_moneda->moneda->simbolo}}
                          </span>
                        </td>
                      @else
                        <td class="">
                          <span class="text-right">
                            @toDosDec($valueComponente->costo_total_adaptado)
                          </span>
                        </td>
                      @endif
                    @endif
                    @if($edit && !$redetermina)
                      <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                          <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            <li><a class="open-modal-componente" data-url="{{route('analisis_item.componente.editComponente', ['id' => $valueComponente->id]) }}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                          <ul>
                        </div>
                      </td>
                    @endif
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
