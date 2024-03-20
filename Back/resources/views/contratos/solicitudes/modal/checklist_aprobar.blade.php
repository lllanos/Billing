<div id="modal_ddjj" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form-ddjj" method="POST" data-action="">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="fa fa-times fa-2x"></span>
          </button>
          <h4 class="modal-title">{{trans('contratos.confirmar.aprobar'). '?'}}</h4>
        </div>
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="modalContentScrollable">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <h6>{{trans('contratos.valido_que')}}</h6>
                @foreach(trans('contratos.asociar_checklist') as $keyChecklistItem => $checklistItem)
                <div class="form-group no-margin-bottom requireGroupInputs">
                  <div class="input-group">
                    <div class="btn-group chk-group-btn outCheck" data-toggle="buttons">
                      <label class="btn btn-primary btn-sm chk-declaro-label">
                        <input autocomplete="off" class="chk-declaro" type="checkbox" id="checklist_items[$keyChecklistItem]" name="checklist_items[{{$keyChecklistItem}}]">
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
              <div class="col-md-12 col-sm-12">
                <div class="form-group">
                  <label for="nro_gde">{{trans('forms.nro_gde')}} *</label>
                  <input placeholder="{{trans('forms.nro_gde')}}" class="form-control mask_gd" id="nro_gde" 
                    required="required" name="nro_gde" value="" type="text"
                    data-inputmask="'mask': 'AA[A][A][A]-9999-99999999-\\APN-AAA[A][A]\\#AAA', 'greedy' : false"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer no-padding-bottom">
          <div class="col-md-12 col-sm-12 content-buttons-bottom-form footer-confirm">
            <div class="text-right">
              <a id="btn_aprobar" class="btn btn-primary pull-right aprobar-modal" disabled>{{trans('forms.guardar')}}</a>
             </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
