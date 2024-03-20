<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
      @trans('index.validar')  @trans('contratos.garantia')
  </h4>
</div>
<div class="garantiaAddModal-content">
      <form method="POST" class="formGarantia" action="{{route('garantia.store')}}" data-action="{{route('garantia.store')}}" id="form-ajax-Garantia">
        <input type="hidden" name="contrato" value="{{$contrato->id}}">
        {{ csrf_field() }}
          <!-- Modal body -->
        <div class="modal-body pt-1 pb-1">
          <div class="modalContentScrollable">
          <div class="panel panel-default">
            <div class="panel-body container_detalle_itemizado pt-0 pb-0">
              <div id="formulario">
                <div class="row">
                  <div class="col-md-12 col-sm-12">
                     <label for="is_valido"> <span> @trans('contratos.garantia') *</span></label>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group ">
                      <input type="radio" name="is_valido" value="1" @if($contrato->has_garantia && $contrato->has_garantia_validada)  checked="checked" @endif > @trans('index.valida') <br>
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="form-group ">
                      <input type="radio" name="is_valido" value="0" @if($contrato->has_garantia && !$contrato->has_garantia_validada)  checked="checked" @endif > @trans('index.no') @trans('index.es') @trans('index.valida')<br>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12">
                    <div class="form-group ">
                      <label for="observacion"> <span>@trans('index.observaciones')</span></label><br>
                      <input type="text"  name="observacion" class="form-control" placeholder="@trans('index.observaciones')" @if($contrato->has_garantia) value="{{$contrato->garantia->observacion}}" @endif>
                    </div>
                  </div>
                  <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                      <label for="adjunto" id="adjunto">
                        <span>
                         @trans('forms.adjuntos') </span>
                       </label>
                      <span class="format_adjuntar_poder">{{ trans('forms.formatos_validos_all') }}</span>
                      <input type="file" name="adjunto[]" id="adjunto" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,.csv,.doc,.xlsx" multiple>
                    </div>
                    @if($contrato->garantia)
                      @if($contrato->garantia->adjuntos != null)
                        @foreach($contrato->garantia->adjuntos as $key => $adjunto)
                          <span id="adjunto_anterior" class="hide-on-ajax">
                            <i class="fa fa-paperclip grayCircle"></i>
                            <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
                          </span>
                          <br>
                        @endforeach
                      @endif
                    @endif
                  </div>
                </div>
               </div>
             </div>
            </div>
          </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer no-padding-bottom footer-original">
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary submitItemizado pull-right" id="btn_guardar_garantia" data-accion="guardar" >{{trans('index.validar')}}</button>
          </div>
        </div>
      {{ Form::close() }}
</div>
