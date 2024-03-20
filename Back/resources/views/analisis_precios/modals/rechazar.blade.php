<div id="modalRechazar" class="modal fade bs-example-modal-lg modal_rechazar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>
        <h4 class="modal-title"></h4>
      </div>

        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <form class="form-horizontal" role="form" method="POST" data-action="" id="form-ajax">
                  {{ csrf_field() }}
                  <div class="form-group">
                    {{ Form::label('observaciones', trans('forms.observaciones')) }}
                    {{ Form::textarea('observaciones', '', array('placeholder' => trans('forms.observaciones'), 'class' => 'form-control', 'id'=>'observaciones')) }}
                  </div>

                  <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">@trans('index.guardar')</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
