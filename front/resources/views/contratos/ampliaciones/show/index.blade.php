@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
          <li><a href="{{route('contratos.ver', ['id' => $contrato->contrato->id]) }}">@trans('forms.contrato') {{$contrato->contrato->expediente_madre}}</a></li>
          <li class="active">@trans('contratos.tipo_ampliacion.' . $contrato->tipo_ampliacion->nombre)</li>
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('contratos.tipo_ampliacion.' . $contrato->tipo_ampliacion->nombre) @trans('index.de') {{$contrato->contrato->expediente_madre}}
          </div>
          <div class="buttons-on-title">
            @if($contrato->borrador)
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                </div>
              </div>
            @endif
          </div>
        </h3>
      </div>
    </div>
  </div>

  <input type='text' id='accion' name='accion' class='hidden' value="{{$accion}}">

  <div class="row">
    <div class="col-md-12">
      <!-- Header -->
      <div class="row">
        <div class="col-md-12 ">
          <div class="estados_contratos">
            <div class="container_badges_referencias badges_refencias_responsive_flex">
              <span class="badge badge-referencias" style="background-color:#{{ $contrato->contrato->estado_nombre_color['color'] }};">
                {{ $contrato->contrato->estado_nombre_color['nombre'] }}
              </span>
              <span class="badge badge-referencias" style="background-color:#{{ $contrato->contrato->causante_nombre_color['color'] }};">
                {{ $contrato->contrato->causante_nombre_color['nombre'] }}
              </span>

              @if($contrato->borrador)
                <span class="badge badge-referencias badge-borrador">
                  <i class="fa fa-eraser"></i>
                  @trans('index.borrador')
                </span>
              @endif

              @if($contrato_incompleto['status'])
                <span class="badge badge-referencias badge-borrador" data-toggle="tooltip" data-placement="bottom" data-html="true" title="{{$contrato_incompleto['mensaje']}}">
                  <i class="fa fa-star-half-empty"></i>
                  @trans('contratos.incompleto')
                </span>
              @endif

            </div>
          </div>
        </div>
      </div>
      <!-- FIN Header -->

      <!-- Panel Detalle Contrato -->
      <div class="panel-group acordion" id="accordion_detalle_contrato" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default panel-view-data border-top-poncho">
          <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne_detalle_contrato">
            <h4 class="panel-title titulo_collapse m-0">
              <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_detalle_contrato" href="#collapseOne_detalle_contrato" aria-expanded="true" aria-controls="collapseOne_detalle_contrato">
                <i class="fa fa-angle-down"></i> {{$contrato->expediente}}
              </a>
            </h4>
          </div>
          <div id="collapseOne_detalle_contrato" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_detalle_contrato">
            <div class="panel-body">
              <div class="col-md-12 form-group">
                <label class="m-0">@trans('contratos.contrato_madre')</label>
                <span class="form-control">
                  <a href="{{route('contratos.ver', ['id' => $contrato->contrato->id]) }}">
                    {{$contrato->contrato->nombre_completo}}
                  </a>
                </span>
              </div>

              @if($contrato->tipo_ampliacion->nombre == 'ampliacion')
                <div class="col-sm-12 col-md-4 form-group">
                  <label class="m-0">{{trans('contratos.plazo_obra')}}</label>
                  <span class="form-control item_detalle">
                    {{$contrato->plazo_completo}}
                  </span>
                </div>
              @endif

              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">{{trans('contratos.resoluc_aprobatoria')}}</label>
                <span class="form-control item_detalle">
                  {{$contrato->resoluc_aprobatoria}}
                </span>
              </div>

              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">{{trans('forms.motivo')}}</label>
                <span class="form-control item_detalle">
                  {{$contrato->motivo_nombre}}
                </span>
              </div>

              @if($contrato->observaciones != '')
                <div class="col-sm-12 col-md-12 form-group">
                  <label class="m-0">{{trans('index.observaciones')}}</label>
                  <span class="form-control item_detalle">
                    {{$contrato->observaciones}}
                  </span>
                </div>
              @endif
            </div>

            @if($contrato->adjuntos != null)
              @foreach($contrato->adjuntos as $key => $adjunto)
                <div class="pb-1">
                  <span id="adjunto_anterior_{{$key}}" class="hide-on-ajax ml-35">
                    <i class="fa fa-paperclip grayCircle"></i>
                    <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
                  </span>
                </div>
              @endforeach
            @endif

          </div>
        </div>
      </div>
      <!--Fin Panel Detalle Contrato-->

    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      @if(!$contrato->borrador)
          @if($contrato->hasSeccion('cronograma'))
            <div id="cronograma_container">
              @include('contratos.contratos.show.cronograma.index')
            </div>
          @endif
      @endif
    </div>
  </div>

@endsection

@section('scripts')
  let cant = 0;
  $(document).ready(() => {
    if($('#accion').val() != null && $('#accion').val() != "") {
      $("div[id*=headingOne]").not('#headingOne-' + $('#accion').val()).each(function() {
        $(this).find('h4 a.btn_acordion').click();
      });

      if($('#headingOne-' + $('#accion').val()).length)
        $('html, body').scrollTop($('#headingOne-' + $('#accion').val()).position().top);
    }

    $('.submit').on('click', function(e) {
      var accion = $(this).data('accion');
      if(accion == 'guardar') {
        $('#borrador').val(0);
      } else {
        $('#borrador').val(1);
      }

      $('#hidden_submit').click()
      applyAll();
    });

    $(document).on("change", ".itemizado_item_sub", function () {
      if(this.checked) {
        $(".row-itemizado-amount").hide();
        $(".modal-body #itemizado_tipo_id").val('1');
        $(".modal-body #itemizado_tipo_id").data('value', '1');
        $(".modal-body #itemizado_item_responsable").chosen();
        $(".modal-body #itemizado_item_responsable").prop('required', false);
        $(".modal-body #itemizado_item_responsable").trigger("chosen:updated");
      } else {
        $(".row-itemizado-amount").show();
        $(".modal-body #itemizado_tipo_id").val('2');
        $(".modal-body #itemizado_tipo_id").data('value', '2');
        $(".modal-body #itemizado_item_responsable").chosen();
        $(".modal-body #itemizado_item_responsable").prop('required', true);
        $(".modal-body #itemizado_item_responsable").trigger("chosen:updated");
      }
      applyAll();
    });

    var this_btn = $(this);
    applyAllContrato();
    applyAll();

    $(document).on("change", ".modal-body #itemizado_item_categoria_id", function () {
      if (this.value == 'ajuste_alzado') {
        $(".modal-body #itemizado_item_importe_total").prop('required',true);

        $(".modal-body #itemizado_item_unidad_medida").chosen();
        $(".modal-body #itemizado_item_unidad_medida").prop('required',false);
        $(".modal-body #itemizado_item_cantidad").prop('required',false);
        $(".modal-body #itemizado_item_importe_unitario").prop('required',false);
      } else if (this.value == 'unidad_medida') {
        $(".modal-body #itemizado_item_importe_total").prop('required',false);

        $(".modal-body #itemizado_item_unidad_medida").chosen();
        $(".modal-body #itemizado_item_unidad_medida").prop('required',true);
        $(".modal-body #itemizado_item_cantidad").prop('required',true);
        $(".modal-body #itemizado_item_importe_unitario").prop('required',true);
      }
    });

    if($('#accion').val() != null && $('#accion').val() != "") {
      if($('#headingOne-' + $('#accion').val()).length) {
        $('#headingOne-' + $('#accion').val()).find('h4 a.btn_acordion').click();
      }
    }
  });

  var applyConfirmableSubmit = () => {
    $('.btn-confirmable-submit').unbind( "click" ).on('click', function () {
      var action = $(this).data('action');
      var form = $(this).data('form');

      BootstrapDialog.confirm({
        message: $(this).data('body'),
        type: BootstrapDialog.TYPE_WARNING,
        closable: true,
        draggable: true,
        btnCancelLabel: $(this).data('no'),
        btnOKLabel: $(this).data('si'),
        btnOKClass: 'btn-primary btn-dialog-OK',
        btnCancelClass: 'btn-link btn-dialog-Cancel',
        callback: function(result) {
          // result true si presiono si, false si se cierra el dialog
          if(result) {
            $('#borrador').val(0);
            if(form != undefined) {
              $('#' + form).find('#borrador').val(0);
              $('#' + form).attr('action', action);
              $('#' + form).data('action', action);
              $('#' + form).submit();
            } else {
              $.get(action, function(resp) {
                console.log(resp);
              });
            }
          }
        }
      });
    });
  }

  window.applyFormConfirmable = () => {
    $('.btn-confirmable-submit').each(function() {
      var form = '#' + $(this).data('form');

      $(form).off('submit').on('submit', function(e) {
        e.preventDefault();
        $('.help-block').remove();
        $('.form-group').removeClass('has-error');
        $('.errores-publicacion').html('<ul> </ul>');
        $('.errores-publicacion').addClass('hidden');
        var action = $(form).data('action');

        loadingToggle();
        $.ajax({
          url: action,
          type: 'POST',
          dataType: 'json',
          data: new FormData($(form)[0]),
          processData: false,
          contentType: false,
          success: function(resp) {
            if(resp.status == true) {
              modalCloseToastSuccess(resp.message);
              if(resp.url != undefined) {
                window.location.href = resp.url;
              } else {
                location.reload();
              }
            } else {
              loadingToggle();
              if(resp.errores) {
                if(resp.error_container != undefined) {
                  $(resp.error_container).removeClass('hidden');
                  $.each(resp.errores, (i, error) => {
                    error.forEach(function(e) {
                      var html = `<li>${e}</li>`;
                      $(resp.error_container).find('ul').append(html);
                    });
                  });
                  $('html, body').scrollTop($(resp.error_container).parent().parent().parent().parent().offset().top);
                } else {
                  $.each(resp.errores, (i, e) => {
                    var html = `<span class="help-block">${e}</span>`;
                    if($('#' + i).closest('.form-group').length > 0)
                      $('#' + i).closest('.form-group').addClass('has-error');
                    {{-- else
                      $('#' + i).addClass('help-outside'); --}}

                    $(html).insertAfter('#' + i);

                    $('.form-control').change(function() {
                      if(i.includes('polinomicas_suma_')) {
                        $(this).children().remove();
                      } else {
                        $(this).closest('.form-group').removeClass('has-error');
                        $(this).closest('.form-group').find('.help-block').remove();
                        $(this).closest('.form-group').parent().find('.help-outside').remove();
                      }

                    });
                  });
                }
                applyAll();

                if(resp.message.length > 0)
                  modalCloseToastError(resp.message);
              }
            }
          }
        });

      });
    });
  }

  var applyModalCronograma = () => {
    $('.open-modal-ItemCronograma').unbind("click").click(function() {
      loadingToggle();
      var url = $(this).data('url');

      $.get(url, function(data) {
        $('#ModalItemCronograma').find('.modal-content').html(data);

        $('#ModalItemCronograma').modal('show');
        applyFormAjax('#form-ajax-ItemCronograma');
        loadingToggle();
      });
    });
  };

  var applyCambiarVisualizacion = () => {
    $('.visualizacion').unbind("click").click(function() {
      var seccion = $(this).data('seccion');
      loadingToggleThis('#' + seccion + '_container');

      var url = "{{route('ampliacion.editar.getViews', ['id' => $contrato->id, 'seccion' => ':seccion',
                                                        'visualizacion' => ':visualizacion'])}}";

      var opcion = $(this).data('version');
      if(opcion != undefined) {
        $('#' + seccion + '_version').val(opcion);
      } else {
        opcion = $(this).data('visualizacion');
        $('#' + seccion + '_visualizacion').val(opcion);
      }

      url = url.replace(":seccion", seccion)
               .replace(":version", $('#' + seccion + '_version').val())
               .replace(":visualizacion", $('#' + seccion + '_visualizacion').val());

      $.get(url, function(data) {
        $('#' + seccion + '_container').html(data.view);
        $('.open-historial.' + seccion).data(url, data.historial)

        loadingToggleThis('#' + seccion + '_container');
        applyAll();
        applyAllContrato();
      }).fail(function() {
        loadingToggleThis('#' + seccion + '_container');
      });
    });
  };

  var applyToggleUnidadMedida = () => {
    $('input[type=radio][name=itemizado_item_categoria_id]').unbind('change').change(function() {
      var tipo = $(this).val();
      // toggle required en toggleHidden visible antes del cambio
      $('.toggleHidden:not(.hidden)').find('select').attr('required', false);

      $(".toggleHidden").addClass('hidden');
      $(".toggleHidden." + tipo).removeClass('hidden');

      // toggle required en toggleHidden visible despues del cambio
    //  $('.toggleHidden:not(.hidden)').find('select').attr('required', true);
      applyAll();
    });
  }

  var applyAllContrato = () => {
    applyCambiarVisualizacion();
    applyConfirmableSubmit();
    applyFormConfirmable();
    applyModalCronograma();
    applyToggleUnidadMedida();
  }
@endsection
