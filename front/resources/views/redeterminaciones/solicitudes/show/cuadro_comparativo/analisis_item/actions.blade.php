@if($edit && $estado_key != 'aprobado_obras' && $categoria->tiene_componentes)
  <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('analisis_item.nuevo_componente') {{($categoria->nombre)}}">
    <button class="btn btn-primary open-modal-componente"
      aria-label="@trans('analisis_item.nuevo_componente') {{($categoria->nombre)}}"
      data-url="{{ route('analisis_item.componente.createComponente', ['categoria_id' => $categoria->id])}}"
    >
      <i class="fa fa-plus"></i>
    </button>
  </div>
@endif

@if($edit && $estado_key != 'aprobado_obras' && $analisis_item->item->is_unidad_medida && $categoria->tiene_rendimiento)
  <div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.editar') @trans('analisis_item.rendimiento')">
    <button class="btn btn-primary open-modal-rendimiento"
      aria-label="@trans('index.editar') @trans('analisis_item.rendimiento')"
      data-url="{{ route('analisis_precios.editRendimiento', ['categoria_id' => $categoria->id])}}"
    >
      <i class="glyphicon glyphicon-pencil"></i>
    </button>
  </div>
@endif
