@extends('layout.app')

@section('title', config('app.name'))

@section('content')

  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('redeterminaciones.index')}}">@trans('index.mis') @trans('index.solicitudes_redeterminacion')</a></li>
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
        @include('redeterminaciones.solicitudes.show.estado_contrato')
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

    <div class="col-md-4">
      @include('redeterminaciones.solicitudes.show.historial')
    </div>
  </div>
</div>
@endsection

@section('modals')
<div id="modalRedeterminacion" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

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
            <div class="col-md-12 contenido_observacion"></div>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal-dialog -->
@endsection

@section('scripts')
  $(() => {
    applyEditMode();
    $('#modalRedeterminacion').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').html('');
        $('#form-solicitud-ajax').off('submit');
    })
  });


  var applyEditMode = () => {
    $('.open-modal-redeterminacion').unbind( "click" );

    $('.open-modal-redeterminacion').click(function() {
      loadingToggle();
      var instancia = $(this).data('instancia');
      var id = $(this).data('id');
      var correccion = false;
      if($(this).data('correccion') != undefined)
        correccion = true;

      $.get('/modalRedeterminacion/' + instancia + '/' + id + '/' + correccion, function(data) {
        $('.modal-content').html(data);

        $('#modalRedeterminacion').modal('show');

        loadingToggle();
        $("#modalRedeterminacion").on('shown.bs.modal', function () {
          if($('#js_applied').val() == 0) {
            applyAll();
            $('#js_applied').val(1);
          }

          $('#form-solicitud-ajax').off('submit');

          $('#form-solicitud-ajax').on('submit', function(e) {
            e.preventDefault();
            var action = $('#form-solicitud-ajax').data('action');

            loadingToggle();

            $('.modal .help-block').remove();
            $('.modal .form-group').removeClass('has-error');
            $('.modal').find('.modalToast').remove();

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
                  applyEditMode();

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
                  //SHOW ERRORS
                  if(resp.errores) {
                    $.each(resp.errores, (i, e) => {
                      $('#'+i).closest('.form-group').addClass('has-error');
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
      });
    });
  }

@endsection
