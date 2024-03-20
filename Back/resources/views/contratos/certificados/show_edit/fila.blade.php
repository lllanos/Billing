@if($header)
    <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
            <div >
                @trans('forms.codigo')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
            <div>
                @trans('forms.item')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion" role="button">
            <div >
                @trans('forms.descripcion')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button">
            <div >
                @trans('certificado.acumulado_anterior')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border @if($edit)dato-importe @else dato-importe-porcentaje @endif" role="button">
            <div >
                @trans('certificado.actual')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button">
            <div>
                @trans('certificado.acumulado')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
            <div>
                @trans('certificado.monto')
            </div>
        </a>

        @if(!$certificado->empalme)
            <a class="btn_acordion datos_as_table collapse_arrow with-border dato-desvio" role="button">
                <div>
                    @trans('certificado.desvio')
                </div>
            </a>
        @endif
    </h4>
@elseif($subheader)
    <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
            <div>
                @trans('forms.total')
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion" role="button">
            <div>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button">
            <div>
                @toDosDec($sub_header[$valuePorContratista->id]['acumulado_anterior']) %
                <span id="sh_monto_acumulado_anterior_{{$valuePorContratista->id}}" class="hidden">
         {{$sub_header[$valuePorContratista->id]['sh_monto_acumulado_anterior']}}
       </span>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
            <div>
                <span id="sh_actual_{{$valuePorContratista->id}}">
                  @toDosDec($sub_header[$valuePorContratista->id]['actual']) %
                </span>

                <span id="sh_monto_actual_{{$valuePorContratista->id}}" class="hidden">
                    {{$sub_header[$valuePorContratista->id]['sh_monto_actual']}}
                    </span>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje" role="button">
            <div>
                <span id="sh_acumulado_{{$valuePorContratista->id}}">
                  @toDosDec($sub_header[$valuePorContratista->id]['acumulado']) %
                </span>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
            <div>
                <span id="sh_monto_sumarizado_{{$valuePorContratista->id}}">
                  @toDosDec($sub_header[$valuePorContratista->id]['monto_sumarizado'])
                </span>
            </div>
        </a>
        @if(!$certificado->empalme)
            <a class="btn_acordion datos_as_table collapse_arrow with-border dato-desvio" role="button">
                <div>
          <span id="desvio_val_{{$valuePorContratista->id}}" @if(abs($valuePorContratista->desvio) >= $porcentaje_desvio) class="red-span" @endif>
            @toDosDec($valuePorContratista->desvio) %
          </span>
                    <span id="sh_monto_total_items_{{$valuePorContratista->id}}" class="hidden">
            {{$sub_header[$valuePorContratista->id]['sh_monto_total_items']}}
          </span>
                    <span id="sh_monto_total_itemizado_{{$valuePorContratista->id}}" class="hidden">
            {{$sub_header[$valuePorContratista->id]['sh_monto_total_itemizado']}}
          </span>
                </div>
            </a>
        @endif
    </h4>
@elseif($anticipo)
    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
    <div class="panel-heading panel_heading_collapse p-0 panel_conceptos" role="tab" id="heading_otros_{{$valuePorContratista->id}}">
        <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_total m-0 panel_title_btn">
            <a class="collapse_arrow" role="button" data-toggle="collapse" href="#collapse_otros_{{$valuePorContratista->id}}" aria-expanded="true" aria-controls="collapse_otros_{{$valuePorContratista->id}}">
                <i class="fa fa-angle-down"></i> @trans('certificado.otros_conceptos')
            </a>
        </h4>
    </div>

    <div class="panel-heading panel_heading_collapse p-0 collapse in" role="tab" aria-expanded="true" id="collapse_otros_{{$valuePorContratista->id}}">
        @if($certificado->empalme)
            <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_anticipo">
                @if(count($certificado->contrato->anticipos) == 1)
                    @if($edit)
                        <div class="col-md-6">
                            <a>
                                @trans('certificado.descuento_anticipo')
                                (<span id="porcentaje">{{$certificado->contrato->ultimo_anticipo->ultimo_item->porcentaje}}</span>%)
                            </a>
                        </div>

                        <div class="col-md-6">
                            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right input_actual input_in_collapsable">
                                <input type='text' class="form-control currency actual" id='anticipo_monto' name='anticipo_monto' value="@toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto)">

                                <span class="input-group-addon">
                                 {{$item->item->itemizado->contrato_moneda->moneda->simbolo}}
                                </span>
                            </div>
                        </div>
                    @else
                        <a>
                            @trans('certificado.descuento_anticipo')
                            (@toDosDec($valuePorContratista->item_anticipo->porcentaje) %)
                        </a>
                        <a>
                            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                @toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto)
                            </div>
                        </a>
                    @endif
                @elseif(count($certificado->contrato->anticipos) > 1)
                    @if($edit)
                        <div class="col-md-4">
                            <div class="form-group form-group-chosen">
                                <select class="form-control" name="anticipo_id" id="anticipo_id">
                                    @foreach($certificado->contrato->anticipos as $opcion)
                                        <option value="{{$opcion->id}}" monto_bruto="{{$valuePorContratista->monto_bruto}}" monto="{{$valuePorContratista->monto}}" porcentaje="{{$opcion->ultimo_item->porcentaje}}" @if($certificado->anticipo && $certificado->anticipo->id == $opcion->id) selected @endif>
                                            {{$opcion->descripcion}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a>
                                @trans('certificado.descuento_anticipo') ( <span id="porcentaje"></span> )
                            </a>
                        </div>
                        <div class="col-md-4">
                            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right input_actual input_in_collapsable">
                                <input type='text' class="form-control currency actual" id='anticipo_monto' name='anticipo_monto' value="">

                                <span class="input-group-addon">
                                    {{$item->item->itemizado->contrato_moneda->moneda->simbolo}}
                                </span>
                            </div>
                        </div>
                    @else
                        <a>
                            @trans('certificado.descuento_anticipo')
                            (@toDosDec($valuePorContratista->item_anticipo->porcentaje) %)
                        </a>
                        <a>
                            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                @toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto)
                            </div>
                        </a>
                    @endif
                @else
                    <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_anticipo">
                        <a class="text-center">
                            @trans('certificado.sin_anticipo')
                        </a>
                    </h4>
                @endif
            </h4>
        @else
            @if($certificado->anticipo != null)
                <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_anticipo">
                    <a>
                        @trans('certificado.descuento_anticipo')
                        (@toDosDec($valuePorContratista->item_anticipo->porcentaje) %)
                    </a>
                    <a>
                        <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                            @toDosDec($valuePorContratista->monto_bruto *
                            $valuePorContratista->item_anticipo->porcentaje_100)
                        </div>
                    </a>
                </h4>
            @else
                <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn panel_anticipo">
                    <a class="text-center">
                        @trans('certificado.sin_anticipo')
                    </a>
                </h4>
            @endif
        @endif
        <div class="panel-heading panel_heading_collapse p-0 collapse in">
            <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
                <a>
                    @trans('contratos.fondo_reparo')
                </a>
                <a>
                    <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                        @toDosDec($certificado->contrato->fondo_reparo) %
                    </div>
                </a>
            </h4>
        </div>
    </div>

    <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
@elseif($certificado->empalme)
    @include('contratos.certificados.show_edit.fila_empalme')
@else
    {{-- Datos de Item --}}
    <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$item->item->nivel - 1}} m-0 panel_title_btn">
        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div class="container_icon_angle">
                @if($item->item->is_nodo) <i class="fa fa-angle-down pl-{{$item->item->nivel - 1}}"></i> @else
                    <i class="fa fa-angle-right pl-{{$item->item->nivel - 1}}"></i> @endif
                {{ $item->item->codigo }}
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div class="container_icon_angle">
                {{ $item->item->item ? $item->item->item : $item->item->codigo }}
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->item->descripcion}}">
                {{$item->item->descripcion}}
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje">
            <div>
        <span id="acumulado_anterior_val_{{$item->certificado_id}}_{{$item->id}}">
          @if($item->is_hoja)
                @toDosDec($item->acumulado_anterior) {{$item->item->porc_unidad_medida}}
            @else
                @toDosDec($item->avance_anterior_item) %
            @endif
        </span>
            </div>
        </a>

        @if($item->is_hoja && $edit)
            <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
                <div class="input_in_collapsable input_actual">
                    <input type='text' class="form-control currency pull-right actual" id='val_{{$item->certificado_id}}_{{$item->id}}' name='val[{{$item->certificado_id}}][{{$item->id}}]'
                        value="@toDosDec($item->cantidad)"
                        data-esperado="{{$item->certificado->esperadoAcumuladoItem($item->item_id)}}"
                        data-montounitario="{{$item->item->monto_unitario_o_porcentual}}" data-montoitem="{{$item->item->monto_total}}">
                    <span class="input-group-addon">
              {{$item->item->porc_unidad_medida}}
            </span>
                </div>
            </a>
        @else
            <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
                @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
                <div>
          <span id="actual_val_{{$item->certificado_id}}_{{$item->id}}"
              @if($edit)
              data-esperado="{{$item->certificado->esperadoAcumuladoItem($item->item->id)}}"
              data-montounitario="{{$item->item->monto_unitario_o_porcentual}}" data-montoitem="{{$item->item->subtotal}}"
            @endif>
            @if($item->is_hoja)
                  @toDosDec($item->cantidad) {{$item->item->porc_unidad_medida}}
              @else
                  @toDosDec($item->avance_actual_item) %
              @endif
          </span>
                </div>
            </a>
        @endif

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div>
        <span id="acumulado_val_{{$item->certificado_id}}_{{$item->id}}">
          @if($item->is_hoja)
                @toDosDec($item->cantidad + $item->acumulado_anterior)  {{$item->item->porc_unidad_medida}}
            @else
                @toDosDec($item->avance_acumulado_item) %
            @endif
        </span>
                <span id="acumulado_postfix_{{$item->certificado_id}}_{{$item->id}}" class="hidden">{{$item->item->porc_unidad_medida}}</span>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div>
        <span id="monto_val_{{$item->certificado_id}}_{{$item->id}}">
          @toDosDec($item->monto)
        </span>
            </div>
        </a>

        <a class="btn_acordion datos_as_table collapse_arrow with-border dato-desvio"
            @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div>
        <span id="desvio_val_{{$item->certificado_id}}_{{$item->id}}"
            @if(abs($item->desvio) >= $porcentaje_desvio) class="red-span" @endif>
          @toDosDec($item->desvio) %
        </span>
            </div>
        </a>
    </h4>

    @php($padre = $item)
    <div class="panel-body panel_sub_tablas panel_js pl-0 pt-1 pr-0 pb-0 collapse in" aria-expanded="true" role="tab" id="collapse_sub_{{$item->id}}">
        <div class="panel panel-default">
            <div class="panel-heading panel_heading_collapse p-0">
                @foreach ($item->child as $key => $subItem)
                    @php($item = $subItem)
                    @include('contratos.certificados.show_edit.fila', ['header' => false])
                @endforeach
            </div>
        </div>
    </div>
    @php($item = $padre)
@endif
