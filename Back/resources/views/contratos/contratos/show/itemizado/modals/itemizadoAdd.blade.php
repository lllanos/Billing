<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @if($accion == 'clone')
      @trans('index.clone')
    @elseif($accion == 'add')
      @trans('index.agregar') @trans('forms.item')
    @else
      @trans('index.editar') @trans('forms.item')
    @endif
  </h4>
</div>

<div class="itemizadoAddModal-content">
  @if($itemizado != null && $itemizado->is_editable)
    @if ($accion == 'clone')
      <form method="POST" class="formItemizadoItem" action="{{route('itemizado.updateOrStore', ['contrato_id' => $contrato_id ])}}" data-action="{{route('itemizado.updateOrStore', ['contrato_id' => $contrato_id ])}}" id="form-ajax">
        {{ csrf_field() }}
          <!-- Modal body -->
        <div class="modal-body pt-0 pb-1" style="min-height: auto;">
          <div class="modalContentScrollable" style="min-height: auto;">
          <input type="hidden" name="itemizado_accion" id="itemizado_accion" value="{{$accion}}" data-value="{{$accion}}" />
          <input type="hidden" name="itemizado_id" id="itemizado_id" value="{{$itemizado->id}}" data-value="{{$itemizado->id}}" />
          <input type="hidden" name="itemizado_item_id" id="itemizado_item_id" value="@if($item != null) {{$item->id}} @else 0 @endif" data-value="@if($item != null) {{$item->id}} @else 0 @endif" />
          <input type="hidden" name="itemizado_padre_id" id="itemizado_padre_id" value="0" data-value="0" />
          <input type="hidden" name="itemizado_nivel" id="itemizado_nivel" value="0" data-value="0" />
          <input type="hidden" name="itemizado_tipo_id" id="itemizado_tipo_id" value="{{$tipo_agrupador_id}}" data-value="{{$tipo_agrupador_id}}" />

          <div class="panel panel-default">
            <div class="panel-body container_detalle_itemizado pt-0 pb-0">
              <div id="formulario">

                <div class="row">
                  <div class="col-xs-12">
                    <label class="col-md-12">{{trans('index.monedas')}}</label>
                  </div>

                  @foreach($monedas as $contratoMoneda)
                  <div class="col-md-6 col-xs-12 p-06">
                    <div class="form-group mb-1">
                      <label class="fixMargin4">
                        <div class="checkbox noMarginChk">
                          <div class="btn-group chk-group-btn" data-toggle="buttons">
                            <label class="btn btn-primary btn-sm ">
                              <input autocomplete="off" c
                                lass="triggerClickChk"
                                type="checkbox" name="monedas[]"
                                id="monedas-{{ $contratoMoneda->moneda->id }}"
                                value="{{ $contratoMoneda->moneda->id }}"
                              >
                              <span class="glyphicon glyphicon-ok"></span>
                            </label>

                            {{ $contratoMoneda->moneda->nombre }}
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>
                  @endforeach
                 </div>
               </div>
             </div>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer no-padding-bottom footer-original">
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary submitItemizado pull-right" id="btn_guardar" data-accion="guardar" >{{trans('index.clone')}}</button>
          </div>
        </div>
      </form>
    @elseif(in_array($accion, ['add', 'edit']))
      <form method="POST" class="formItemizadoItem" action="{{route('itemizado.updateOrStore', ['contrato_id' => $contrato_id ])}}" data-action="{{route('itemizado.updateOrStore', ['contrato_id' => $contrato_id ])}}" id="form-ajax">
        {{ csrf_field() }}
          <!-- Modal body -->
        <div class="modal-body pt-0 pb-1">
          <div class="modalContentScrollable">
          <input type="hidden" name="itemizado_accion" id="itemizado_accion" value="{{$accion}}" data-value="{{$accion}}" />
          <input type="hidden" name="itemizado_id" id="itemizado_id" value="{{$itemizado->id}}" data-value="{{$itemizado->id}}" />
          <input type="hidden" name="itemizado_item_id" id="itemizado_item_id" value="@if($item != null) {{$item->id}} @else 0 @endif" data-value="@if($item != null) {{$item->id}} @else 0 @endif" />
          <input type="hidden" name="itemizado_padre_id" id="itemizado_padre_id" value="0" data-value="0" />
          <input type="hidden" name="itemizado_nivel" id="itemizado_nivel" value="0" data-value="0" />
          <input type="hidden" name="itemizado_tipo_id" id="itemizado_tipo_id" value="{{$tipo_agrupador_id}}" data-value="{{$tipo_agrupador_id}}" />

          <div class="panel panel-default">
            <div class="panel-body container_detalle_itemizado">
              <div id="formulario">
                <div class="row">
                  <div class="col-md-12 col-sm-12">
                    <div class="form-group mb-2">
                      {{ Form::label('itemizado_item_nombre', trans('forms.nombre')) }}
                      {{ Form::text('itemizado_item_nombre', '', array('class' => 'form-control', 'required', 'autofocus', 'placeholder' => trans('forms.nombre'))) }}
                    </div>
                  </div>

                  <div class="col-md-12 col-sm-12">
                    <div class="form-group mb-2">
                      {{ Form::label('itemizado_item_item', trans('forms.item')) }}
                      {{ Form::text('itemizado_item_item', '', array('class' => 'form-control', 'placeholder' => trans('forms.item'))) }}
                      <small class="msg_sugerencia_input">{{trans('forms.item_help')}}</small>
                    </div>
                  </div>
                </div>

                  @if ($itemizado->borrador == 1)
                    <div class="row-itemizado-sub">
                      <div class="row">
                        <div class="col-md-12 col-sm-12">
                          <div class="btn-group chk-group-btn p-0" data-toggle="buttons">
                            <label class="btn btn-primary btn-sm active">
                              <input type="checkbox" name="itemizado_item_sub" id="itemizado_item_sub" class="itemizado_item_sub" value="1" data-value="1" checked />
                              <span class="glyphicon glyphicon-ok"></span>
                            </label>
                            {{trans('forms.tiene_subitem')}}
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row-itemizado-amount">
                      <div class="col-md-12 col-sm-12 error_rad_itemizado_item_categoria_id">
                        <div class="form-group" id="div_itemizado_item_categoria_id">
                          <label class="col-md-6" id="ajuste_alzado">
                            <input type="radio" name="itemizado_item_categoria_id" id="itemizado_item_categoria_id" class="toggleSelect radio-ajuste_alzado" value="ajuste_alzado"
                              @if($item->categoria_id == null) checked @elseif($item->is_ajuste_alzado) checked @endif
                            @if($item->no_puede_editar_valores) disabled @endif
                            >{{trans('forms.ajuste_alzado')}}
                          </label>
                          <label class="col-md-6">
                            <input type="radio" name="itemizado_item_categoria_id" id="itemizado_item_categoria_id" class="toggleSelect radio-unidad_medida" value="unidad_medida"
                              @if($item->categoria_id == null) @elseif(!$item->is_ajuste_alzado) checked @endif
                            @if($item->no_puede_editar_valores) disabled @endif
                            >{{trans('forms.unidad_medida')}}
                          </label>
                        </div>
                      </div>

                      <div class="col-md-12 toggleHidden ajuste_alzado on-modal">
                        <div class="row">
                          <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                              {{ Form::label('itemizado_item_importe_total', trans('forms.importe_total')) }}
                              <input type="text" class="form-control currency" name='itemizado_item_importe_total' id='itemizado_item_importe_total' value="" placeholder="{{trans('forms.importe_total')}}"
                              @if($item->no_puede_editar_valores) disabled @endif>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-12 toggleHidden unidad_medida on-modal hidden" id="unidad_medida_fix">
                        <div class="col-md-4 col-sm-12">
                          <div class="form-group">
                            {{ Form::label('itemizado_item_unidad_medida', trans('forms.unidad_medida')) }}
                            {!! Form::select('itemizado_item_unidad_medida', $unidadesMedida, '', array('class' => 'form-control chosen-select', 'data-placeholder' => trans('forms.unidad_medida'), 'id' => 'itemizado_item_unidad_medida', 'disabled' => $item->no_puede_editar_valores == 1)) !!}
                          </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                          <div class="form-group">
                            {{ Form::label('itemizado_item_cantidad', trans('forms.cantidad')) }}
                            <input type="text" class="form-control currency" name='itemizado_item_cantidad' id='itemizado_item_cantidad' value="" placeholder="{{trans('forms.cantidad')}}">
                          </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                          <div class="form-group">
                            {{ Form::label('itemizado_item_importe_unitario', trans('forms.importe_unitario')) }}
                            <input type="text" class="form-control currency" name='itemizado_item_importe_unitario' id='itemizado_item_importe_unitario' value="" placeholder="{{trans('forms.importe_unitario')}}"
                              @if($item->no_puede_editar_valores) disabled @endif>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-12 col-sm-12">
                          <div class="form-group display_top">
                            {{ Form::label('itemizado_item_responsable', trans('forms.responsable')) }}
                            {!! Form::select('itemizado_item_responsable', $responsables, '1', array('class' => 'form-control no-chosen', 'data-placeholder' => trans('forms.responsable'), 'id' => 'itemizado_item_responsable', 'disabled' => $item->no_puede_editar_valores == 1)) !!}
                          </div>
                        </div>
                      </div>

                    </div>
                  @endif
               </div>
             </div>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer no-padding-bottom footer-original">
          <div class="col-md-12">

            @if($accion == 'add' && $itemizado->borrador == 1)
              <div class="btn-group chk-group-btn" data-toggle="buttons">
                <label class="btn btn-primary btn-sm">
                  <input type="checkbox" autocomplete="off" name="agregar_otro" id="agregar_otro">
                  <span class="glyphicon glyphicon-ok"></span>
                </label>
                @trans('index.agregar_otro')
              </div>
            @endif

            <button type="submit" class="btn btn-primary submitItemizado pull-right" id="btn_guardar" data-accion="guardar" >{{trans('index.guardar')}}</button>
          </div>
        </div>
      </form>
    @endif
  @endif
</div>
