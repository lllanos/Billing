<input type="hidden" name="js_applied" id="js_applied" value="0">
  <div class="modal-header">
    <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true" class="fa fa-times fa-2x"></span>
    </button>
    <h4 class="modal-title">
      @trans('sol_redeterminaciones.acciones.' . $instancia)
    </h4>
  </div>

  <form method="POST" data-action="{{ route('solicitudes.update.store', ['instancia' => $instancia, 'id_solicitud' => $id_solicitud, 'correccion' => $correccion]) }}" id="form-solicitud-ajax" name="form-solicitud-ajax" enctype="multipart/form-data">
    {{ csrf_field() }}

    {{-- <input name="acepta_correccion" id="acepta_correccion" class="hidden" @if($solicitud->post_dictamen) value="0" @else value="1" @endif> --}}
    <input class="hidden" name="borrador" id="borrador" value="0">

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
          {{-- @if(isset($acepta_select))
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <label for="usuario_firma_id">{{trans('sol_redeterminaciones.usuario_que_firma')}}</label>
                <select class="form-control" name="usuario_firma_id" id="usuario_firma_id">
                  <option disabled selected value> {{trans('sol_redeterminaciones.usuario_que_firma')}}</option>
                    @foreach($solicitud->usuarios_firma as $keyUsuario => $valueUsuario)
                      <option value="{{ $keyUsuario }}" @if($solicitud->usuario_firma_id == $keyUsuario)  @endif
                      data-route="{{route('descargar', ['nombre' => $valueUsuario['poder']['nombre'], 'url' => config('custom.url_front') . $valueUsuario['poder']['link']])}}"
                      data-nombre="{{$valueUsuario['poder']['nombre'] }}"
                      data-dni="{{$valueUsuario['dni'] }}"
                      data-userpublico="{{$valueUsuario['userpublico'] }}">
                      {{ $valueUsuario['user'] }} </option>
                    @endforeach
                </select>
              </div>
            </div>
          @endif --}}

          <div class="col-md-12 col-sm-12 no-padding-bottom">
            <div class="modalToast-content">
              <button type="button" class="btn btn-default btn-as-tab active" data-tab="acta">@trans('sol_redeterminaciones.acta')</button>
              <button type="button" class="btn btn-default btn-as-tab" data-tab="resolucion">@trans('sol_redeterminaciones.resolucion')</button>
              <button type="button" class="btn btn-default btn-as-tab" data-tab="informe">@trans('sol_redeterminaciones.informe')</button>
            </div>
          </div>

          <div class="btn-tab-div " id="acta">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <textarea id="acta_ck" name="acta_ck">
                  {{$acta_content}}
                </textarea>
              </div>
            </div>
          </div>

          <div class="btn-tab-div hidden" id="resolucion">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <textarea id="resolucion_ck" name="resolucion_ck">
                  {{$resolucion_content}}
                </textarea>
              </div>
            </div>
          </div>

          <div class="btn-tab-div hidden" id="informe">
            <div class="col-md-12 col-sm-12">
              <div class="form-group">
                <textarea id="informe_ck" name="informe_ck">
                  {{$informe_content}}
                </textarea>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="modal-footer no-padding-bottom footer-original">
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary pull-right" name="btn_guardar_borrador" id="btn_guardar_borrador">{{trans('index.guardar_borrador')}}</button>
        <button type="submit" class="btn btn-primary pull-right" name="btn_guardar" id="btn_guardar">@trans('index.guardar')</button>
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
    $(document).ready(function() {
      CKEDITOR.replace('acta_ck');
      CKEDITOR.replace('resolucion_ck');
      CKEDITOR.replace('informe_ck');
    });

    $('.btn-as-tab').unbind('click').on('click', function() {
      $('.btn-as-tab').removeClass('active');
      $(this).addClass('active');
      $('.btn-tab-div').addClass('hidden');
      var seccion = $(this).data('tab');
      $("#" + seccion).removeClass('hidden');

      // CKEDITOR.replace(seccion + '_ck');
    });
    /*$('#usuario_firma_id').unbind('change').on('change', function() {
      loadingToggle();
      var dni = $( "#usuario_firma_id option:selected" ).data('dni');
      var userpublico = $( "#usuario_firma_id option:selected" ).data('userpublico');
      var text = $('#acta_ck').text();

      text = text.replace('[nombrecocontratistafirma]', userpublico);
      text = text.replace('[dnicocontratistafirma]', dni);
      $('#acta_ck').text(text);
      CKEDITOR.instances['acta_ck'].destroy(true)
      CKEDITOR.replace('acta_ck');
      loadingToggle();
    });
*/
  </script>
