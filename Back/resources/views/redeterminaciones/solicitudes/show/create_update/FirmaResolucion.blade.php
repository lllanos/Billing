<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @trans('sol_redeterminaciones.acciones.' . $instancia)
  </h4>
</div>

<form method="POST" data-action="{{ route('solicitudes.update.store', ['instancia' => $instancia, 'id_solicitud' => $id_solicitud, 'correccion' => $correccion]) }}" id="form-solicitud-ajax" enctype="multipart/form-data">
  {{ csrf_field() }}

  {{-- <input name="acepta_correccion" id="acepta_correccion" class="hidden"
    @if($solicitud->post_dictamen) value="0" @else value="1" @endif> --}}

  <div class="modal-body">
    <div class="modalContentScrollable">
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-info" role="alert">
            <span class="alert-link">@trans('sol_redeterminaciones.titulo_pasos')</span>
            <ul class="modal_lista">
              @foreach(trans('sol_redeterminaciones.pasos.' . $instancia) as $key => $value)
                <li class="modal_lista_item">{{$value}}</li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>@trans('sol_redeterminaciones.nro_resolucion')</label>
            <input type='text' id='nro_resolucion' name='nro_resolucion' class='form-control' required>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>@trans('sol_redeterminaciones.acta') @trans('sol_redeterminaciones.firmada')</label>
              <input type="file" name="acta_firmada" id="acta_firmada" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf" required>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>@trans('sol_redeterminaciones.resolucion') @trans('sol_redeterminaciones.firmada')</label>
            <input type="file" name="resolucion_firmada" id="resolucion_firmada" class="file_upload" accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf" required>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>@trans('index.observaciones')</label>
            <textarea class="form-control" name="observaciones" id="observaciones" placeholder="@trans('index.observaciones')"></textarea>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal-footer no-padding-bottom footer-original">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>

  <div class="modal-footer no-padding-bottom hidden footer-confirm">
    <div class="modalToast-content pull-left" id="toast_confirm">
      <span class="message-confirm alert alert-info"> </span>
      <button class="btn btn-primary pull-right" id="btn_guardar_confirm">@trans('index.guardar')</button>
      <button class="btn btn-primary pull-right" id="btn_cancelar_confirm">@trans('index.cancelar')</button>
    </div>
  </div>
</form>

<script type="text/javascript">
</script>
