@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
@if(!isset($redeterminado)) @php($redeterminado = false ) @endif
  <div class="row">
    <div class="col-md-12 col-sm-12">
      @if($breadcrumb == '1')
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('solicitudes.certificado_en_proceso')}}">@trans('certificado.sol_certificaciones_en_proceso')</a></li>
        <li class="active">@trans('contratos.certificado') @if($redeterminado) @trans('index.redeterminacion') {{$certificado->redeterminacion->nro_salto}} @endif - @trans('index.mes') {{$certificado->mes}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</li>
      </ol>
      @elseif($breadcrumb == '2')
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('solicitudes.certificado_finalizadas')}}">@trans('certificado.sol_certificaciones_finalizadas')</a></li>
        <li class="active">@trans('contratos.certificado') @if($redeterminado) @trans('index.redeterminacion') {{$certificado->redeterminacion->nro_salto}} @endif - @trans('index.mes') {{$certificado->mes}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</li>
      </ol>
      @else
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('index.contratos')</a></li>
        <li><a href="{{route('contratos.ver', ['id' => $certificado->contrato_id]) }}">@trans('forms.contrato') {{$certificado->contrato->expediente_madre}}</a></li>
        <li class="active">@trans('contratos.certificado') @if($redeterminado) @trans('index.redeterminacion') {{$certificado->redeterminacion->nro_salto}} @endif - @trans('index.mes') {{$certificado->mes}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}</li>
      </ol>
      @endif
      <div class="page-header page_header__badge">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">@trans('contratos.certificado') @if($redeterminado) @trans('index.redeterminacion') {{$certificado->redeterminacion->nro_salto}} @endif @if($certificado->empalme) @trans('index.de') @trans('contratos.empalme') @endif - @trans('index.mes') {{$certificado->mes}} - {{$certificado->mesAnio('fecha', 'Y-m-d')}}
            <span class="badge badge-referencias" style="background-color:#{{$certificado->estado['color']}};"
              @if($certificado->estado['nombre'] == "borrador")
                data-toggle="tooltip" data-placement="top"
                title="@trans('certificado.usuario_creador', ['user' => $certificado->user_creator->nombre_apellido])"
              @endif
              >
              {{$certificado->estado['nombre_trans']}}
            </span>
          </div>
          <div class="buttons-on-title">
            @if($edit && $certificado->puede_editar && !$redeterminado)
              @permissions(('certificado-view'))
                <div class="button_desktop">
                  <a class="btn btn-primary submit btn-success"
                    id="btn_ver_cert" href="{{route('certificado.ver', ['id' => $certificado->id])}}">
                    @trans('index.ver') @trans('contratos.certificado')
                  </a>
                  <a class="btn btn-primary open-historial mouse-pointer"
                    data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}" id="btn_historial" data-toggle="tooltip" data-placement="bottom" title="@trans('index.historial')">
                    <i class="fa fa-history" aria-hidden="true"></i>
                  </a>
                </div>
                <div class="button_responsive">
                  <div class="dropdown dd-on-table" data-placement="left">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li>
                        <a href="{{route('certificado.ver', ['id' => $certificado->id]) }}">
                          @trans('index.ver') @trans('contratos.certificado')
                        </a>
                      </li>
                      <li>
                        <a data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}"
                          class="open-historial historial-certificado"> @trans('index.historial')</a>
                      </li>
                    </ul>
                  </div>
                </div>
              @endpermission
            @elseif($certificado->puede_editar && !$redeterminado)
              @permissions(('certificado-edit'))
                <div class="button_desktop">
                  <a class="btn btn-primary submit btn-success"
                  id="btn_ver_cert" href="{{route('certificado.edit', ['id' => $certificado->id])}}">
                    @trans('index.editar') @trans('contratos.certificado')
                  </a>
                  <a class="btn btn-primary open-historial mouse-pointer"
                    data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}" id="btn_historial" data-toggle="tooltip" data-placement="bottom" title="@trans('index.historial')">
                    <i class="fa fa-history" aria-hidden="true"></i>
                  </a>
                </div>
                <div class="button_responsive">
                  <div class="dropdown dd-on-table" data-placement="left">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      <li>
                        <a href="{{route('certificado.edit', ['id' => $certificado->id]) }}">
                          @trans('index.editar') @trans('contratos.certificado')
                        </a>
                      </li>
                      <li>
                        <a data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}"
                          class="open-historial historial-certificado"> @trans('index.historial')</a>
                      </li>
                    </ul>
                  </div>
                </div>
              @endpermission
            @else
              <div class="button_desktop">
                <div class="dropdown dd-on-table" data-placement="left">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    @if($certificado->puede_aprobar && !$redeterminado)
                      @permissions(('certificado-aprobar'))
                        <li>
                          <a class="aprobar btn-confirmable"
                            data-body="@trans('index.confirmar_aprobar.certificado', ['mes' => $certificado->mes])
                                       @include('contratos.certificados.aclaracion')"
                            data-action="{{route('solicitudes.certificado.aprobar', ['id' => $certificado->id]) }}"
                            data-si="@trans('index.si')" data-no="@trans('index.no')">
                              @trans('index.aprobar')
                          </a>
                        </li>
                      @endpermission
                      @permissions(('certificado-rechazar'))
                        <li>
                          <a href="javascript:void(0);" class="open-modal-rechazar"
                            id="btn_rechazar_{{$certificado->id}}"
                            data-action="{{ route('solicitudes.certificado.rechazar', ['id' => $certificado->id]) }}"
                            data-id="{{$certificado->id}}"
                            data-title="{{trans('index.confirmar_rechazar.certificado', ['mes' => $certificado->mes])}}"
                            title="{{ trans('index.rechazar')}}">
                            @trans('index.rechazar')
                          </a>
                        </li>
                      @endpermission
                    @endif
                    @if($certificado->is_basico OR $redeterminado)
                      <li>
                        <a href="{{route('export.certificado', ['id' => $certificado->id]) }}">
                          @trans('index.descargar') @trans('contratos.certificado')
                        </a>
                      </li>
                    @endif
                    <li>
                      <a data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}"
                        class="open-historial historial-certificado"> @trans('index.historial')</a>
                    </li>

                    @if($certificado->puede_aprobar_redeterminado)
                      <li>
                        <a class="btn-confirmable"
                         data-body="@trans('certificado.mensajes.confirmacion_aprobar')"
                         data-action="{{ route('solicitudes.certificado.aprobarCertificadoRedeterminado', ['id' => $certificado->id]) }}"
                         data-si="@trans('index.si')" data-no="@trans('index.no')">
                         @trans('index.aprobar')
                        </a>
                      </li>
                    @endif

                  </ul>
                </div>
              </div>
              <div class="button_responsive">
                <div class="dropdown dd-on-table" data-placement="left">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                    <i class="fa fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a data-url="{{ route('certificado.historial', ['id' => $certificado->id]) }}"
                        class="open-historial historial-certificado"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a>
                    </li>

                    @if($certificado->puede_aprobar_redeterminado)
                      <li>
                        <a class="btn-confirmable"
                         data-body="@trans('certificado.mensajes.confirmacion_aprobar')"
                         data-action="{{ route('solicitudes.certificado.aprobarCertificadoRedeterminado', ['id' => $certificado->id]) }}"
                         data-si="@trans('index.si')" data-no="@trans('index.no')">
                         @trans('index.aprobar')
                        </a>
                      </li>
                    @endif
                  </ul>
                </div>
              </div>
            @endif
          </div>
        </h3>
      </div>
    </div>
  </div>

  @if($edit)
    <form method="POST" action="{{route('certificado.storeUpdate', ['id' => $certificado->id ])}}" data-action="{{route('certificado.storeUpdate', ['id' => $certificado->id ])}}" id="form-ajax">
      {{ csrf_field() }}
      <div class="alert alert-danger hidden"> <ul> </ul> </div>
      <input type='text' id='borrador' name='borrador' class='hidden' value="0">
      <input type='text' id='porcentaje_desvio' name='porcentaje_desvio' class='hidden' value="{{$porcentaje_desvio}}">
      @if($certificado->empalme)
        <input type='text' id='empalme' name='empalme' class='hidden' value="1">
      @else
        <input type='text' id='empalme' name='empalme' class='hidden' value="0">
      @endif
    @endif
      <div class="panel-group acordion" id="accordion-certificado" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-body pt-0 pb-0">

            @if($edit && $certificado->empalme)
              <div class="panel-body">
                <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                  <div class="alert alert-info text-center hidden" role="alert" id="fue_modificado">
                    @trans('certificado.mensajes.fue_modificado')
                  </div>
                </div>
              </div>

              <div class="panel-body">
                <div class="col-md-12 col-sm-12 col-xs-12 p-0">
                  <div class="form-group mb-1">
                    <label class="fixMargin4 pull-right">
                      <div class="checkbox noMarginChk">
                        <div class="btn-group chk-group-btn" data-toggle="buttons">
                          <label class="btn btn-primary btn-sm @if($certificado->tiene_redeterminado_empalme) active @endif">
                            <input autocomplete="off" class="triggerClickChk tiene_redeterminado" type="checkbox" name="tiene_redeterminado" id="tiene_redeterminado"
                            @if($certificado->tiene_redeterminado_empalme) checked @endif>
                            <span class="glyphicon glyphicon-ok"></span>
                          </label>
                          @trans('certificado.tiene_redeterminado')
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            @endif

            @if(sizeof($certificados_por_moneda) > 0)
              @php($contador_contratista = 1)
              <div class="panel-body panel_con_tablas_y_sub_tablas contenedor_all_tablas pt-1 pl-0 pr-0">
                @foreach($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda)
                  <div class="panel-group colapsable_top" id="accordion_mon_{{$keyCertMoneda}}" role="tablist" aria-multiselectable="true">
                    <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading_mon_{{$keyCertMoneda}}">
                      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_mon_{{$keyCertMoneda}}" href="#collapse_mon_{{$keyCertMoneda}}" aria-expanded="true" aria-controls="collapse_mon_{{$keyCertMoneda}}">
                          <i class="fa fa-angle-down"></i> {{$valueCertMoneda['nombre']}}
                        </a>

                        <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_mon_{{$keyCertMoneda}}" href="#collapse_mon_{{$keyCertMoneda}}" aria-expanded="true" aria-controls="collapse_mon_{{$keyCertMoneda}}">
                          <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                            @trans('certificado.total_menos_ant') @toDosDec($valueCertMoneda['monto'])
                          </div>
                        </a>
                      </h4>
                    </div>

                    <div class="panel panel-default">
                      <div id="collapse_mon_{{$keyCertMoneda}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_{{$keyCertMoneda}}">
                        <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 overflow-x-auto">
                          @foreach($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista)
                            <input class="inverted_tree hidden" id="inv_tree_{{$valuePorContratista->id}}" value='{!! json_encode($valuePorContratista->itemsTree()) !!}'>
                            <input class="items_nivel_1 hidden" id="inv_tree_{{$valuePorContratista->id}}" value='{!! json_encode($valuePorContratista->itemsPadresPorMonedaContr()) !!}'>

                            @if($is_ute)
                              <div class="panel-heading panel_heading_collapse p-0" role="tab" id="heading_cert_{{$keyCertMoneda}}_{{$contador_contratista}}">
                                <h4 class="panel-title titulo_collapse titulo_collapse_small panel_heading_total m-0 panel_title_btn">
                                  <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" href="#collapse_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" aria-expanded="true" aria-controls="collapse_cert_{{$keyCertMoneda}}_{{$contador_contratista}}">
                                    <i class="fa fa-angle-down"></i> {{$valuePorContratista->contratista->nombre_documento}}
                                  </a>

                                  <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" href="#collapse_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" aria-expanded="true" aria-controls="collapse_cert_{{$keyCertMoneda}}_{{$contador_contratista}}">
                                    <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
                                      @if($redeterminado)
                                        @if($certificado->anticipo != null)
                                          @toDosDec(($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior) * $valuePorContratista->item_anticipo->porcentaje_100)
                                        @else
                                          @toDosDec($valuePorContratista->monto_bruto - $valuePorContratista->monto_redeterminacion_anterior)
                                        @endif
                                      @else
                                        @trans('certificado.total_menos_ant') @toDosDec($valuePorContratista->monto)
                                      @endif
                                    </div>
                                  </a>
                                </h4>
                              </div>
                            @endif

                            <div class="panel-group colapsable_sub scrollable-collapse" id="accordion_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" role="tablist" aria-multiselectable="true">
                              <div class="panel panel-default">
                                <div id="collapse_cert_{{$keyCertMoneda}}_{{$contador_contratista}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_cert_{{$keyCertMoneda}}_{{$contador_contratista}}">
                                  @if(sizeof($valuePorContratista->items_nivel_1) > 0)
                                    <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                                      <div class="panel panel-default">
                                        <div class="panel-heading panel_heading_collapse p-0">
                                          @if($redeterminado)
                                            @include('contratos.certificados.show_edit.fila_redeterminado', ['header' => true, 'subheader' => false])
                                          @else
                                            @include('contratos.certificados.show_edit.fila', ['header' => true, 'subheader' => false, 'anticipo' => false])
                                            @include('contratos.certificados.show_edit.fila', ['header' => false, 'subheader' => true, 'anticipo' => false])
                                          @endif
                                        </div>
                                      </div>
                                    </div>

                                    @foreach($valuePorContratista->items_nivel_1 as $keyItem => $item)
                                      <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0" aria-expanded="true" role="tab">
                                        <div class="panel panel-default">
                                          <div class="panel-heading panel_heading_collapse p-0">
                                            @if($redeterminado)
                                              @include('contratos.certificados.show_edit.fila_redeterminado', ['header' => false, 'subheader' => false])
                                            @else
                                              @include('contratos.certificados.show_edit.fila', ['header' => false, 'subheader' => false, 'anticipo' => false])
                                            @endif
                                          </div>
                                        </div>
                                      </div>
                                    @endforeach

                                    @if($redeterminado)
                                      <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                                        <div class="panel panel-default">
                                          <div class="panel-heading panel_heading_collapse p-0">
                                             @include('contratos.certificados.show_edit.fila_redeterminado', ['header' => false, 'subheader' => true])
                                          </div>
                                        </div>
                                      </div>
                                    @else
                                      <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0">
                                        <div class="panel panel-default">
                                          <div class="panel-heading panel_heading_collapse p-0">
                                             @include('contratos.certificados.show_edit.fila', ['header' => false, 'subheader' => false, 'anticipo' => true])
                                          </div>
                                        </div>
                                      </div>
                                    @endif

                                    @if($certificado->empalme)
                                      <div class="panel-body panel_sub_tablas pl-0 pt-1 pr-0 pb-0 redeterminado @if(!$certificado->tiene_redeterminado_empalme) hidden @endif">
                                        <div class="panel panel-default">
                                          <div class="panel-heading panel_heading_collapse p-0">
                                            @include('contratos.certificados.show_edit.fila_redeterminado')
                                          </div>
                                        </div>
                                      </div>
                                    @endif
                                  @else
                                    <div class="sin_datos"> <h1 class="text-center">@trans('index.no_datos')</h1> </div>
                                  @endif
                                </div>
                              </div>
                          @php($contador_contratista++)
                            </div>
                          @endforeach
                      </div>
                    </div>
                    </div>
                @endforeach
              </div>
            @endif

            @if(!$certificado->empalme && ($certificado->has_adjuntos || $edit))
              @include('contratos.certificados.show_edit.adjuntos')
            @endif

            <div class="panel-body">
              <div class="col-md-12 col-sm-12 content-buttons-bottom-form">
                <div class="text-right">
                  @if($breadcrumb == '1')
                  <a class="btn btn-small btn-success" href="{{route('solicitudes.certificado_en_proceso')}}">@trans('forms.volver')</a>
                  @elseif($breadcrumb == '2')
                  <a class="btn btn-small btn-success" href="{{route('solicitudes.certificado_finalizadas')}}">@trans('forms.volver')</a>
                  @else
                    <a class="btn btn-small btn-success" href="{{ route('contratos.ver.incompleto', ['id' => $certificado->contrato_id, 'accion' => 'certificados']) }}">@trans('forms.volver')</a>
                  @endif
                  @if($edit)
                    {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right btn_guardar', 'id' => 'preValidarPureJs')) }}
                    {{ Form::submit(trans('forms.guardar_borrador'), array('class' => 'btn btn-basic pull-right borrador')) }}
                  @endif
                </div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </form>


@endsection

@section('modals')
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

  <div id="preValidacionPureJs" class="bootstrap-dialog modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bootstrap-dialog-draggable">
                <div class="bootstrap-dialog-header">
                    <div class="bootstrap-dialog-close-button">
                        <button class="close" aria-label="close">×</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="bootstrap-dialog-body">
                    <div class="bootstrap-dialog-message">
                     @trans('certificado.mensajes.confirmacion')
                     @include('contratos.certificados.aclaracion')
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="bootstrap-dialog-footer">
                    <div class="bootstrap-dialog-footer-buttons">
                      <button class="btn btn-link btn-dialog-Cancel">@trans('index.no')</button>
                      <button class="btn btn-primary btn-dialog-OK">@trans('index.si')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  @include('contratos.certificados.solicitudes.modals.rechazar')
@endsection

@section('scripts')
  let inverted_tree = [];
  let inverted_tree_upper_search = [];
  let padres_por_contratista = [];      // Usado para calcular el subheader

  $(document).ready(() => {
    applyAllCertificado();
    applyAnticipo();

    var width = $('.panel_js > .panel')[0].clientWidth;
    $('.panel_anticipo').width(width);

    $(".tiene_redeterminado").change(function() {
      $('.redeterminado').toggleClass('hidden');
    });

    $('.recalculo-empalme').bind('keyup change paste', function() {
      $('#fue_modificado').removeClass('hidden');
      val = $(this).val();
      if(val == "")
        val = "0,00";

      $('#monto_' + $(this).attr('id')).val(toArg($(this).data('montounitario') * argToFloat(val)));
    });

    applyPreValidacionPureJs();
  });

  applyAnticipo = () => {
    $( document ).ready(function() {
      var select = $('#anticipo_id').find('option:selected');

      var monto_bruto = select.attr('monto_bruto');
      var monto = select.attr('monto');

      if(monto_bruto - monto > 0) {
        var total = monto_bruto - monto;
        $('#anticipo_monto').val(formato(total));
      }
    });

    $('#anticipo_id').on('change', function() {
      var select = $(this).find('option:selected');

      var porcentaje = select.attr('porcentaje');
      var monto_bruto = select.attr('monto_bruto');

      $('#porcentaje').text(porcentaje + '%');
      var descuento = monto_bruto * porcentaje / 100;
      $('#anticipo_monto').val(formato(descuento));


      $('#anticipo_id_chosen').css('margin-top','-55px');
      $('.scrollable-collapse').css('overflow-x','unset');
    }).trigger('change');

    function formato(num) {
      return num.toFixed(2).replace('.', ',')
    }
  };

  applyAllCertificado = () => {
    $('.inverted_tree').each(function(i, e) {
      inverted_tree_ct = jQuery.parseJSON($(this).val());

      $.each(inverted_tree_ct, function(index, values) {
        inverted_tree[index] = values;

        $.each(values, function(index_padre, value_padre) {
          if(inverted_tree_upper_search[value_padre] == undefined)
            inverted_tree_upper_search[value_padre] = [];
          inverted_tree_upper_search[value_padre].push(index);
        });
      });
    });

    $('.items_nivel_1').each(function(i, e) {
      to_json = jQuery.parseJSON($(this).val());

      $.each(to_json, function(index, values) {
        $.each(values, function(index_padre, value_padre) {
          id_certificado = index.split('_')[0];
          if(padres_por_contratista[id_certificado] == undefined)
            padres_por_contratista[id_certificado] = [];
          padres_por_contratista[id_certificado].push(value_padre);
        });
      });
    });

    applyAll();
    applyCalculoCertificado();
  };
@endsection
