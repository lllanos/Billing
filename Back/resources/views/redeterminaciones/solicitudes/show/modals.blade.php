@section('modals')
  <div id="modalRedeterminacion" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      </div>
    </div>
  </div>

  <div id="modalObservaciones" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog ">
      <div class="modal-content">
        <input type="hidden" name="js_applied" id="js_applied" value="0">
          <div class="modal-header">
            <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times fa-2x"></span>
            </button>
            <h4 class="modal-title">
              @trans('index.observaciones')
            </h4>
          </div>
          <div class="modal-body">
            <div class="modalContentScrollable">
              <div class="row">
                <div class="col-md-12 contenido_observacion">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="modalMotivos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
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
                  <form class="form-horizontal" role="form" method="POST" data-action="" id="form-motivos">
                    {{ csrf_field() }}
                    <label class="body-label"></label>
                    <div class="col-md-12 col-sm-12">
                      <div class="form-group">
                        {{ Form::label('motivo', trans('forms.motivo') . ' *') }}
                        {{ Form::textarea('motivo', '', array('placeholder' => trans('forms.motivo'), 'class' => 'form-control', 'id'=>'motivo', 'required' => 'required')) }}
                      </div>
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

@endsection
