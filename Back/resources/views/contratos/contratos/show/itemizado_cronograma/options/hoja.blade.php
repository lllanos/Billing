@if ($itemizado->borrador == 1 && !isset($fromCronograma))
  <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
      <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu pull-right">
      <li class="btn_edit_itemizado_item" data-itemizado="{{$keyItemizado}}" data-padre_id="{{$item->padre_id}}" data-id="{{$item->id}}" data-nivel="{{$item->nivel}}" data-toggle="modal" data-target="#itemizadoAddModal"><a><i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.editar')</a></li>
      @if($level1->no_certificado)
        <li>
          <a class="btn-confirmable"
            data-body="@trans('contratos.confirmacion.delete-itemizado', ['item' => $item->descripcion])" data-action="{{route('itemizado.deleteItem', ['item_id' => $item->id ])}}" data-si="@trans('index.si')" data-no="@trans('index.no')">
            <i class="fa fa-trash" aria-hidden="true"></i>
            @trans('index.eliminar')
          </a>
        </li>
      @endif
    </ul>
  </div>

@elseif($itemizado->borrador == 0 && !isset($fromCronograma))
   @if($itemizado != null && $itemizado->is_editable)
      <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
          <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu pull-right">
          <li class="btn_edit_itemizado_item" data-itemizado="{{$keyItemizado}}" data-padre_id="{{$item->padre_id}}" data-id="{{$item->id}}" data-nivel="{{$item->nivel}}" data-toggle="modal" data-target="#itemizadoAddModal"><a><i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.editar')</a></li>
        </ul>
      </div>
    @endif
@elseif ($itemizado->borrador == 1 && isset($fromCronograma))
  @if($itemizado != null && $itemizado->is_editable)
    <div class="dropdown container_btn_action dato-opciones">
      <button class="btn btn-primary open-modal-ItemCronograma"
        data-toggle="tooltip" data-placement="bottom" title="{{trans('cronograma.agregar_avances')}}"
        aria-label="{{trans('cronograma.agregar_avances')}}"
        data-url="{{ route('cronograma.item.getHtmlEdit', ['item_id' => $item->id, 'cronograma_id' => $itemizado->id])}}"
      >
        <i class="glyphicon glyphicon-pencil"></i>
      </button>
    </div>
  @endif
@endif
