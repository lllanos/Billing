<div id="modalHistorial" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>

        <h4 class="modal-title">
          @trans('index.historial') <span></span>
        </h4>
      </div>

      <div class="modal-body">
        <div class="modalContentScrollable">
          <div class="row">
            <div class="col-md-12 panel-historial">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="ModalItemCronograma" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div>
</div>

@permissions(('itemizado-edit'))
  <div id="itemizadoAddModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      </div>
    </div>
  </div>
@endpermission


@permissions(('anticipos-create'))
  <div id="anticipoAddModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
       <div class="modal-content">
          @include('contratos.contratos.show.anticipos.modals.anticipoAdd')
       </div>
    </div>
  </div>
@endpermission

@permissions(('garantias-manage'))
  <div id="garantiaAddModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
       <div class="modal-content">
          @include('contratos.contratos.show.garantias.modals.garantiaAdd')
       </div>
    </div>
  </div>
@endpermission

@permissions(('analisis_precios-edit'))
<div id="modalCoeficiente" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div>
</div>
@endpermission
