<div id="modal_ddjj" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form-ddjj" enctype="multipart/form-data">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>
        <h4 class="modal-title">{{trans('contratos.asociacion_modal')}}</h4>
      </div>
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="modalContentScrollable">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <h6>{{trans('contratos.declaro_que')}}</h6>
                @foreach(trans('contratos.asociar_checklist') as $keyChecklistItem => $checklistItem)
                <div class="form-group no-margin-bottom requireGroupInputs">
                  <div class="input-group">
                    <div class="btn-group chk-group-btn outCheck" data-toggle="buttons">
                      <label class="btn btn-primary btn-sm chk-declaro-label">
                        <input autocomplete="off" class="chk-declaro" type="checkbox" id="checklist_items[{{$keyChecklistItem}}]" name="checklist_items[{{$keyChecklistItem}}]">
                        <span class="glyphicon glyphicon-ok"></span>
                      </label>
                      <p class="blockLabel">
                        {{$checklistItem}}
                      </p>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <hr>

            <textarea class="form-control ddjjReadonly" readonly>
            {{trans('ddjj.' . $traduccion_ddjj . '.texto')}}
            </textarea>

            <div class="col-md-10 col-md-offset-1 text-center">
              <div class="form-group{{ $errors->has('terminos_y_condiciones') ? ' has-error' : '' }}">

                <div class="btn-group chk-group-btn" data-toggle="buttons">
                  <label class="btn btn-primary btn-sm chk-declaro-label">
                    <input autocomplete="off" class="chk-declaro" type="checkbox" name="terminos_y_condiciones" id="terminos_y_condiciones">
                    <span class="glyphicon glyphicon-ok"></span>
                  </label>
                  {!! trans('contratos.declaro.texto') . trans('contratos.declaro.link') !!}
                </div>
                @if ($errors->has('terminos_y_condiciones'))
                <span class="help-block" id="error_terminos">
                  <strong>{{ $errors->first('terminos_y_condiciones') }}</strong>
                </span>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer no-padding-bottom">
          <div class="col-md-12 col-sm-12 content-buttons-bottom-form footer-confirm">
            <div class="text-right">
              <a id="btn_asociar" class="btn btn-primary pull-right asociar-modal" disabled>{{trans('forms.guardar')}}</a>
             </div>
          </div>

          <div class="modal-footer no-padding-bottom hidden footer-confirm">
            <div class="modalToast-content pull-left" id="toast_confirm">
              <span class="message-confirm alert alert-info">
              </span>
              <a id="btn_guardar_de_todos_modos" class="btn btn-primary pull-right asociar-modal">{{trans('forms.guardar_de_todos_modos')}}</a>
            </div>
          </div>

        </div>
      </div>
    </form>
  </div>
</div>
