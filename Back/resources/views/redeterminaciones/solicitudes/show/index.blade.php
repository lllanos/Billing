@extends('layout.app')

@section('title', config('app.name'))

@section('content')

  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        @if($solicitud->en_curso)
          <li><a href="{{route('solicitudes.redeterminaciones_en_proceso')}}">@trans('forms.sol_redeterminaciones_en_proceso')</a></li>
        @else
          <li><a href="{{route('solicitudes.redeterminaciones_finalizadas')}}">@trans('forms.sol_redeterminaciones_finalizadas')</a></li>
        @endif
        <li class="active">@trans('index.ver') @trans('index.redeterminacion')</li>
      </ol>
      <div class="page-header page_header_detalle">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.ver') @trans('index.redeterminacion')
          </div>
          <div class="buttons-on-title">
            @include('redeterminaciones.solicitudes.show.acciones')
          </div>
        </h3>
      </div>
    </div>
    <div class="col-md-12 estados_contratos">
      <span id="estado_contrato_refresh">
        @include('redeterminaciones.solicitudes.show.estado_solicitud')
      </span>
      <span>
        @if($solicitud->a_termino)
          <i class="fa fa-check-circle text-success"></i> @trans('index.solicitud_termino')
        @else
          <i class="fa fa-times-circle text-danger"></i> @trans('index.solicitud_termino')
        @endif
      </span>
    </div>
    <div class="col-md-8">
      @include('redeterminaciones.solicitudes.show.detalle')
    </div>
    <div class="col-md-4 historial_refresh">
      @include('redeterminaciones.solicitudes.show.historial')
    </div>
  </div>
@endsection

@include('redeterminaciones.solicitudes.show.modals')

@section('scripts')
  $(() => {
    applyEditMode();
    $('#modalRedeterminacion').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').html('');
        $('#form-solicitud-ajax').off('submit');
    });
  });

  var applyEditMode = () => {
    applyModalMotivos();

    $('.open-modal-redeterminacion').unbind( "click" ).click(function() {
      loadingToggle();
      var instancia = $(this).data('instancia');
      var id = $(this).data('id');
      var correccion = false;
      if($(this).data('correccion') != undefined)
        correccion = true;

      $.get('/modalRedeterminacion/' + instancia + '/' + id + '/' + correccion, function(data) {
      let submit = true;
        if(data.status != undefined && data.status == false) {
          modalCloseToastSuccess(data.message);
          loadingToggle();
        } else {
          $('#modalRedeterminacion').find('.modal-content').html(data);

          $('#modalRedeterminacion').modal('show');
          applyWysiwygFinish();
          applyEliminarArchivo();
          loadingToggle();
          $("#modalRedeterminacion").on('shown.bs.modal', function () {
            if($('#js_applied').val() == 0) {
              applyAll();
              $('#js_applied').val(1);
            }

            $('#form-solicitud-ajax').off('submit');
            $('#form-solicitud-ajax').on('submit', function(e) {
              if($('#borrador').length > 0)
                $('#borrador').val(0);

              var $btn = $(document.activeElement);
              if($btn != undefined) {
                if($btn[0].name == "btn_guardar_borrador") {
                  $('#borrador').val(1);
                }
              }

              e.preventDefault();
              var action = $('#form-solicitud-ajax').data('action');

              loadingToggle();

              $('.modal .help-block').remove();
              $('.modal .form-group').removeClass('has-error');
              $('.modal').find('.modalToast').remove();

              if(submit == true) {
                $.ajax({
                  url: action,
                  type: 'POST',
                  dataType: 'json',
                  data: new FormData($('#form-solicitud-ajax')[0]),
                  processData: false,
                  contentType: false,
                  success: function(resp) {
                    loadingToggle();
                    if(resp.status == true) {
                      $('#modalRedeterminacion').modal('hide');

                      $('.buttons-on-title').html('').html(resp.acciones);
                      $('.historial_refresh').html('').html(resp.historial_refresh);
                      $('#estado_contrato_refresh').html('').html(resp.estado_contrato);
                      $('.datos_cargados').html('').html(resp.datos_cargados);

                      if(resp.message != null) {
                        dataToast = {};
                        dataToast.alert = 'success';
                        dataToast.icon = 'check';
                        dataToast.msg = resp.message;
                        modalToast(dataToast);
                      }
                      applyAll();
                      applyEditMode();
                      applyObservaciones();
                    }
                    else if(resp.status == false) {
                      // SHOW ERRORS
                      if(resp.errores) {
                        $.each(resp.errores, (i, e) => {
                          var html = `<span class="help-block">${e}</span>`;
                          if(i == 'selectize_dictamen') {
                            $('#nro_dictamen_gedo').closest('.form-group').addClass('has-error');
                            $('#nro_dictamen_gedo').closest('.form-group').append(html);
                          }

                          $('#' + i).closest('.form-group').addClass('has-error');

                          if($('#' + i).is(':input[type=file]')) {
                            $('#' + i).closest('.file-input-new').append(html);
                            if($('#' + i).closest('.file-input-new').length == 0)
                              $('#' + i).closest('.file-input').append(html);
                          }
                          else{
                            $(html).insertAfter('#' + i);
                          }
                        });

                        $('.modal .form-control').change(function() {
                          $(this).closest('.form-group').removeClass('has-error');
                          $(this).closest('.form-group').find('.help-block').remove();
                          $(this).closest('.form-group').parent().find('.help-outside').remove();
                        });
                      }

                      if(resp.message != null && resp.exige_confirmacion == undefined) {
                        dataToast = {};
                        dataToast.alert = 'danger';
                        dataToast.icon = 'times';
                        dataToast.msg = resp.message;
                        if(resp.message.length > 0) {
                          modalToast(dataToast);
                        }
                      }

                      if(resp.exige_confirmacion != undefined) {
                        $('#acepta_correccion').val(1);
                        $('.footer-confirm').removeClass('hidden');
                        $('.footer-original').addClass('hidden');
                        $('.message-confirm').text(resp.message);

                        $('#btn_cancelar_confirm').unbind('click').on('click', function () {
                          submit = false;
                          loadingToggle();
                          $('#modalRedeterminacion').modal('hide');
                        });
                      }

                    }
                  }
                });
              }
            });
          });
        }
      });
    });
  }

  var applyEliminarArchivo = () => {

    $('.eliminar_old').unbind( "click" ).click(function() {
      var file = $(this).data('file');
      $(file).prop('required', true)
      $(this).parent().remove();
      applyRequired();
    });
  }

  window.applyModalMotivos = () => {
    $('.btn-confirmable-motivos').unbind( "click" ).on('click', function() {
      $('#motivo').val('')
      var id = $(this).data('id');
      var title = $(this).data('title');
      var action = $(this).data('action');

      $('.modal-title').html(title);
      $('.body-label').html($(this).data('body'));

      $('#form-motivos').data('action', action);

      $('#modalMotivos').modal('show');
      $('#form-motivos').off('submit');
      $('#form-motivos').on('submit', function(e) {

        e.preventDefault();
        var action = $('#form-motivos').data('action');

        loadingToggle();

        $('.modal .help-block').remove();
        $('.modal .form-group').removeClass('has-error');
        $('.modal').find('.modalToast').remove();

        $.ajax({
          url: action,
          type: 'POST',
          dataType: 'json',
          data: new FormData($('#form-motivos')[0]),
          processData: false,
          contentType: false,
          success: function(resp) {
            loadingToggle();
            if(resp.status == true) {

              $('#modalMotivos').modal('hide');

              $('.buttons-on-title').html('').html(resp.acciones);
              $('.historial_refresh').html('').html(resp.historial_refresh);
              $('#estado_contrato_refresh').html('').html(resp.estado_contrato);
              $('.datos_cargados').html('').html(resp.datos_cargados);

              if(resp.message != null) {
                dataToast = {};
                dataToast.alert = 'success';
                dataToast.icon = 'check';
                dataToast.msg = resp.message;
                modalToast(dataToast);
              }
              applyAll();
              applyEditMode();
              applyObservaciones();
            }
            else if(resp.status == false) {
              //SHOW ERRORS
              if(resp.errores) {
                $.each(resp.errores, (i, e) => {
                  $('#' + i).closest('.form-group').addClass('has-error');
                  var html = `<span class="help-block">${e}</span>`;
                  if($('#' + i).is(':input[type=file]')) {
                    $('#' + i).closest('.file-input-new').append(html);
                  }
                  else{
                    $(html).insertAfter('#' + i);
                  }
                });

                $('.modal .form-control').change(function() {
                  $(this).closest('.form-group').removeClass('has-error');
                  $(this).closest('.form-group').find('.help-block').remove();
                  $(this).closest('.form-group').parent().find('.help-outside').remove();
                });

              }
              if(resp.message != null) {
                dataToast = {};
                dataToast.alert = 'danger';
                dataToast.icon = 'times';
                dataToast.msg = resp.message;
                if(resp.message.length > 0) {
                  modalToast(dataToast);
                }
              }
            }
          }
        });
      });
    });
  }
@endsection
