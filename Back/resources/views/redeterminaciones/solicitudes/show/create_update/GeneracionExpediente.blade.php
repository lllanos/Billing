<input type="hidden" name="js_applied" id="js_applied" value="0">
<div class="modal-header">
  <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" class="fa fa-times fa-2x"></span>
  </button>
  <h4 class="modal-title">
    @trans('sol_redeterminaciones.acciones.' . $instancia)
  </h4>
</div>

<form method="POST" data-action="{{ route('solicitudes.update.store', ['instancia' => $instancia, 'id_solicitud' => $solicitud->id, 'correccion' => $correccion]) }}" id="form-solicitud-ajax" enctype="multipart/form-data">
  {{ csrf_field() }}
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
            <label>@trans('sol_redeterminaciones.nro_expediente')</label>
            <input placeholder="{{trans('sol_redeterminaciones.nro_expediente')}}" type='text' id='nro_expediente' name='nro_expediente' class='form-control' required>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label>@trans('index.observaciones')</label>
            <textarea class="form-control" name="observaciones" id="observaciones"></textarea>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal-footer no-padding-bottom">
    <div class="col-md-12">
      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
    </div>
  </div>
</form>

<script type="text/javascript">

</script>
