<h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$item->item->nivel - 1}} m-0 panel_title_btn">
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
  @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
    <div class="container_icon_angle">
      @if($item->item->is_nodo) <i class="fa fa-angle-down pl-{{$item->item->nivel - 1}}"></i>
      @else <i class="fa fa-angle-right pl-{{$item->item->nivel - 1}}"></i> @endif
      {{$item->item->codigo}}
    </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion"
  @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
    <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->item->descripcion}}">
      {{$item->item->descripcion}}
    </div>
  </a>
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje">
    <div class="">
      <span id="acumulado_anterior_val_{{$item->certificado_id}}_{{$item->id}}">
        @toDosDec($item->acumulado_anterior)  {{$item->item->porc_unidad_medida}}
      </span>
    </div>
  </a>
  @if($item->is_hoja && $edit)
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
      <div class="input_in_collapsable input_actual">
        <input type='text' class="form-control currency pull-right actual" id='val_{{$item->certificado_id}}_{{$item->id}}' name='cant[{{$item->certificado_id}}][{{$item->id}}]'
          value="@toDosDec($item->cantidad)"
          data-esperado="{{$item->esperado}}" data-montounitario="{{$item->item->monto_unitario_o_porcentual}}">
          <span class="input-group-addon">
            {{$item->item->porc_unidad_medida}}
          </span>
      </div>
    </a>
  @else
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje"
    @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
      <div class="">
        @toDosDec($item->cantidad) {{$item->item->porc_unidad_medida}}
      </div>
    </a>
  @endif
  <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
  @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
    <div class="">
      <span id="acumulado_val_{{$item->certificado_id}}_{{$item->id}}">
        @toDosDec($item->cantidad + $item->acumulado_anterior)  {{$item->item->porc_unidad_medida}}
      </span>
    </div>
  </a>
  @if($certificado->empalme)
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe-porcentaje"
    @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
      <div class="input_in_collapsable input_actual">
        @if($edit && $item->is_hoja)
        <input type='text' class="form-control currency actual" id='monto_val_{{$item->certificado_id}}_{{$item->id}}' name='val[{{$item->certificado_id}}][{{$item->id}}]'
        value="@toDosDec($item->monto)">
        <span class="input-group-addon">
          {{$item->item->itemizado->contrato_moneda->moneda->simbolo}}
        </span>
        @else
          <div class="">
            <span id="monto_val_{{$item->certificado_id}}_{{$item->id}}">
              @toDosDec($item->monto)
            </span>
          </div>
        @endif
      </div>
    </a>
  @else
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
    @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
      <div class="">
        <span id="monto_val_{{$item->certificado_id}}_{{$item->id}}">
          @toDosDec($item->monto)
        </span>
      </div>
    </a>
    <a class="btn_acordion datos_as_table collapse_arrow with-border dato-desvio"
    @if(!$item->is_hoja) role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}" aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
      <div class="">
        <span id="desvio_val_{{$item->certificado_id}}_{{$item->id}}"
          @if(abs($item->desvio) > $porcentaje_desvio) class="red-span" @endif>
          @toDosDec($item->desvio) %
        </span>
      </div>
    </a>
  @endif
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
