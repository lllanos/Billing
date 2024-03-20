@if($redeterminado)
  @if($header)
    <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
        <div class="">
          @trans('forms.codigo')
       </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
        <div>
          @trans('forms.item')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion" role="button">
        <div class="">
          @trans('forms.descripcion')
       </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
        <div class="">
          @trans('certificado.precio_redeterminado')
       </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
        <div class="">
          @trans('certificado.avance_certificado')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe" role="button">
        <div class="">
          @trans('certificado.importe_certificado')
       </div>
      </a>
    </h4>
  @elseif($subheader)
    <h4 class="panel-title bottom-border titulo_collapse titulo_collapse_small m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="">
          @trans('certificado.bruto_cert_redeterminado')
        </div>
      </a>
      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="pull-right">
          @toDosDec($valuePorContratista->monto_bruto)
       </div>
      </a>
    </h4>

    <h4 class="panel-title bottom-border titulo_collapse titulo_collapse_small m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="">
          @trans('certificado.bruto_cert_redeterminado') @trans('index.anterior')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class=" pull-right">
          @toDosDec($valuePorContratista->monto_redeterminacion_anterior)
       </div>
      </a>
    </h4>

    @if($valuePorContratista->penalidad)

    <h4 class="panel-title bottom-border titulo_collapse titulo_collapse_small m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="">
          @trans('certificado.deduccion_penalidad')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="pull-right">
          @toDosDec($valuePorContratista->penalidad)
       </div>
      </a>
    </h4>
    @endif

    <h4 class="panel-title bottom-border titulo_collapse titulo_collapse_small m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="">
          @trans('certificado.total_cert_redeterminado_actual')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow" role="button">
        <div class="pull-right">
          @if($valuePorContratista->penalidad)
             @toDosDec(($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) + $valuePorContratista->penalidad)
          @else
             @toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior)
          @endif
       </div>
      </a>
    </h4>

    @if($certificado->anticipo != null)
      <div class="panel-heading panel_heading_collapse p-0 panel_conceptos" role="tab" id="heading_otros_{{$valuePorContratista->id}}">
        <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_total m-0 panel_title_btn">
          <a class="collapse_arrow" role="button" data-toggle="collapse" href="#collapse_otros_{{$valuePorContratista->id}}" aria-expanded="true" aria-controls="collapse_otros_{{$valuePorContratista->id}}">
            <i class="fa fa-angle-down"></i> @trans('certificado.otros_conceptos')
          </a>
        </h4>
      </div>

      <div class="panel-heading panel_heading_collapse p-0 collapse in" role="tab" aria-expanded="true" id="collapse_otros_{{$valuePorContratista->id}}">
        <h4 class="panel-title titulo_collapse titulo_collapse_small m-0 panel_title_btn">
          <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo" role="button">
            <div class="">
              @trans('certificado.descuento_anticipo') (@toDosDec($valuePorContratista->item_anticipo->porcentaje)%)
            </div>
          </a>
          <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion" role="button">
            <div class="pull-right">
              @toDosDec(($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) * $valuePorContratista->item_anticipo->porcentaje_100)
           </div>
          </a>
        </h4>
      </div>
    @endif

  @else
    @php($itemRedeterminado = $item->item->precio_redeterminado_item($certificado->redeterminacion->id))

    <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_{{$item->item->nivel - 1}} m-0 panel_title_btn">
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
      @if(!$item->item->is_hoja) role="button" data-toggle="collapse"
       data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}"
       aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
        <div class="container_icon_angle">
          @if($item->item->is_nodo) <i class="fa fa-angle-down pl-{{$item->item->nivel - 1}}"></i>
          @else <i class="fa fa-angle-right pl-{{$item->item->nivel - 1}}"></i> @endif
          {{$item->item->codigo}}
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo"
            @if(!$item->item->is_hoja) role="button" data-toggle="collapse"
            data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}"
            aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
            <div class="container_icon_angle">
                {{ $item->item->item ? $item->item->item : $item->item->codigo }}
            </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-descripcion width_descripcion"
      @if(!$item->item->is_hoja) role="button" data-toggle="collapse"
       data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}"
       aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
        <div class="responsable-overflow-hidden" data-toggle="tooltip" data-placement="bottom" title="{{$item->item->descripcion}}">
          {{$item->item->descripcion}}
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
        <div class="">
          <span id="precio_redeterminado_{{$item->certificado_id}}_{{$item->id}}">
            @if($item->item->is_hoja)
              @toDosDec($itemRedeterminado->precio)
            @else

            @endif
          </span>
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe"
      @if(!$item->item->is_hoja) role="button" data-toggle="collapse"
       data-parent="#accordion_sub_{{$valuePorContratista->id}}_{{$item->id}}" href="#collapse_sub_{{$item->id}}"
       aria-expanded="true" aria-controls="collapse_sub_{{$item->id}}" @endif>
        <div class="">
          @if($item->item->is_hoja)
            @toDosDec($item->cantidad) {{$item->item->porc_unidad_medida}}
          @else
            @toDosDec($item->avance_actual_item)%
          @endif
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
        <div class="">
          <span id="importe_certificado_{{$item->certificado_id}}_{{$item->id}}">
            @toDosDec($item->monto)
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
            @include('contratos.certificados.show_edit.fila_redeterminado', ['header' => false, 'subheader' => false])
          @endforeach
        </div>
      </div>
    </div>
    @php($item = $padre)
  @endif
@else
  @php($certificadoRedeterminado = $certificado->certificadosRedeterminadoDe($valuePorContratista->contrato_moneda_id, $valuePorContratista->contratista_id))

  <div class="panel-heading panel_heading_collapse p-0 panel_conceptos" role="tab" id="heading_redet_{{$valuePorContratista->id}}">
    <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_total m-0 panel_title_btn">
      <a class="collapse_arrow" role="button" data-toggle="collapse" href="#collapse_redet_{{$valuePorContratista->id}}" aria-expanded="true" aria-controls="collapse_redet_{{$valuePorContratista->id}}">
        <i class="fa fa-angle-down"></i> @trans('certificado.redeterminados')
      </a>
    </h4>
  </div>

  <div class="panel-heading panel_heading_collapse p-0 collapse in" role="tab" aria-expanded="true" id="collapse_redet_{{$valuePorContratista->id}}">
    <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading m-0 panel_title_btn panel_anticipo">
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo">
        <div class="container_icon_angle">
          @trans('certificado.importes_por_ajustes')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
        <div class="fecha_obra_ultima_redeterminacion_collapse pull-right input_actual input_in_collapsable">

          @if($edit)
            <input type='text' class="form-control currency actual" id='importes_por_ajustes_{{$valuePorContratista->id}}' name='redeterminado[{{$valuePorContratista->id}}][importes_por_ajustes]'
              value="@toDosDec($certificadoRedeterminado->monto_bruto)">
            <span class="input-group-addon">
              {{$item->item->itemizado->contrato_moneda->moneda->simbolo}}
            </span>
          @else
            @toDosDec($certificadoRedeterminado->monto_bruto)
          @endif
        </div>
      </a>
    </h4>

    <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading m-0 panel_title_btn panel_anticipo">
      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-codigo">
        <div class="container_icon_angle">
          @trans('certificado.desc_anticipo_importes_por_ajustes')
        </div>
      </a>

      <a class="btn_acordion datos_as_table collapse_arrow with-border dato-importe">
        <div class="fecha_obra_ultima_redeterminacion_collapse pull-right input_actual input_in_collapsable">
          @if($edit)
            <input type='text' class="form-control currency actual" id='desc_anticipo_importes_por_ajustes_{{$valuePorContratista->id}}' name='redeterminado[{{$valuePorContratista->id}}][desc_anticipo_importes_por_ajustes]'
              value="@toDosDec($certificadoRedeterminado->monto_bruto - $certificadoRedeterminado->monto)">
            <span class="input-group-addon">
              {{$item->item->itemizado->contrato_moneda->moneda->simbolo}}
            </span>
          @else
            @toDosDec($certificadoRedeterminado->monto_bruto - $certificadoRedeterminado->monto)
          @endif
        </div>
      </a>
    </h4>
  </div>
@endif
