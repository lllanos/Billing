@if ($itemizado->borrador == 1 && !isset($fromCronograma))
  <div class="dropdown container_btn_action dato-opciones" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
      <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu pull-right">
      <li class="btn_edit_itemizado_item" data-itemizado="{{$keyItemizado}}" data-padre_id="{{$item->padre_id}}" data-id="{{$item->id}}" data-nivel="{{$item->nivel}}" data-toggle="modal" data-target="#itemizadoAddModal"><a><i class="fa fa-pencil" aria-hidden="true"></i> @trans('index.editar')</a></li>
      <li class="btn_add_itemizado_item" data-itemizado="{{$keyItemizado}}" data-padre_id="{{$item->padre_id}}" data-id="{{$item->id}}" data-nivel="{{$item->nivel}}" data-toggle="modal" data-target="#itemizadoAddModal"><a><i class="fa fa-plus" aria-hidden="true"></i> @trans('index.agregar')</a></li>
      @if (sizeof($item->child) == 0)
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
@elseif($itemizado->borrador == 0 && $contrato->completo && !isset($fromCronograma))
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
  <a class="btn_acordion datos_as_table collapse_arrow dato-opciones" role="button" data-toggle="collapse" data-parent="#accordion_sub_heading" href="#collapseOne_sub_heading" aria-expanded="true" aria-controls="collapseOne_sub_heading">
    <div class="">
    </div>
  </a>
@endif
