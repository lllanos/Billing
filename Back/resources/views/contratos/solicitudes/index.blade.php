@extends ('layout.app')

@section('title', config('app.name') )

@section('content')

  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li class="active">{!!trans('forms.asociaciones_' . $estado)!!}</li>
      </ol>
      <div class="page-header">
        <h3>
          {!!trans('forms.asociaciones_' . $estado)!!}
        </h3>
      </div>
    </div>
    <!--Input file excel con 1 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
          @permissions(('sol-contrato-' . $estado . '-export'))
          <form  class="form_excel" method="POST" data-action="{{ route('solicitudes.contrato.export', ['estado' => $estado]) }}" id="form_excel">
            {{ csrf_field() }}
            <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
            <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
              <i class="fa fa-file-excel-o fa-2x"></i>
            </button>
          </form>
          @endpermission
          <input type="text" class="search-input form-control input_dos_btns" name="search_input_no_post" id="search_input_no_post" value ="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')" aria-label="{{trans('index.input')}} @trans('index.buscar')">
          <span class="input-group-btn">
            <button type="submit" id="search_button_no_post" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
      </div>
    </div>
    <!--Fin Input file excel con 1 form-->
    @if(sizeof($solicitudes) > 0)
    <div class="col-md-12 col-sm-12">
      <div class="list-table pt-0">
        <div class="zui-wrapper zui-action-32px-fixed">
          <div class="zui-scroller"> <!-- zui-no-data -->
            <table class="table table-striped table-hover table-bordered zui-table">
              <thead class="thead_js">
                <tr>
                  <th>{{trans('forms.fecha_solicitud_th')}}</th>
                  <th>{{trans('forms.expediente_madre')}}</th>
                  <th>{{trans('forms.contratista')}}</th>
                  @if(!Auth::user()->usuario_causante)
                    <th>{{trans('forms.causante')}}</th>
                  @endif
                  @if($estado == 'finalizadas')
                    <th>{{trans('forms.estado')}}</th>
                  @endif
                  <th>{{trans('forms.ultimo_movimiento_th')}}</th>
                  <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
                </tr>
              </thead>
              <tbody class="tbody_js">
                @foreach ($solicitudes as $key => $solicitud)
                  <tr>
                    <td>{{ $solicitud->created_at }}</td>
                    <td>{{ $solicitud->expediente_madre }}</td>
                    <td>{{ $solicitud->user_publico->nombre_apellido_documento }}</td>
                    @if(!Auth::user()->usuario_causante)
                      <td>
                        <span class="badge" style="background-color:#{{ $solicitud->causante_nombre_color['color'] }};">
                          {{ $solicitud->causante_nombre_color['nombre'] }}
                        </span>
                      </td>
                    @endif
                    @if($estado == 'finalizadas')
                    <td>
                      <span class="badge" style="background-color:#{{ $solicitud->estado_nombre_color['color'] }};">
                        {{ $solicitud->estado_nombre_color['nombre'] }}
                      </span>
                    </td>
                    @endif
                    <td>{{ $solicitud->ultimo_movimiento }}</td>
                    <td class="actions-col noFilter">
                        <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">

                          <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                            <i class="fa fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu pull-right">
                            @permissions(('sol-contrato-' . $estado . '-view'))
                            <li><a href="{{ route('contrato.solicitud.ver', ['id' => $solicitud->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                            @endpermission
                            @permissions(('sol-contrato-aprobar'))
                              @if($solicitud->instancia_actual->puede_aprobar)
                              <li>
                                <a id="btn_aprobar_list" href="javascript:void(0);" class="open-modal-ddjj"
                                  data-action="{{route('contrato.solicitud.aprobar', ['id' => $solicitud->id])}}">
                                  <i class="glyphicon glyphicon-ok"></i> {{ trans('index.aprobar')}}</a>
                              </li>
                              @endif
                            @endpermission
                            @permissions(('sol-contrato-rechazar'))
                              @if($solicitud->instancia_actual->puede_rechazar)
                                <li>
                                  <a href="javascript:void(0);" class="open-modal-rechazar"
                                    id="btn_rechazar_{{$solicitud->id}}"
                                    data-action="{{ route('contrato.solicitud.rechazar', ['id' => $solicitud->id]) }}"
                                    data-id="{{$solicitud->id}}"
                                    data-title="{{trans('contratos.confirmar.rechazar'). $solicitud->expediente_madre .'?'}}"
                                    title="{{ trans('index.rechazar')}}">
                                    <i class="glyphicon glyphicon-remove"></i> {{ trans('index.rechazar')}}
                                  </a>
                                </li>
                              @endif
                            @endpermission
                          </ul>
                        </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @else
      <div class="col-md-12 col-sm-12">
        <div class="sin_datos">
          <h1 class="text-center">@trans('index.no_datos')</h1>
        </div>
      </div>
    @endif
    <div class="col-md-12 col-sm-12">
      <div class="sin_datos_js"></div>
    </div>
  </div>
@endsection

@section('modals')
  @include('contratos.solicitudes.modal.rechazar')
  @include('contratos.solicitudes.modal.checklist_aprobar')
@endsection

@section('scripts')
  $(document).ready(() => {
    $('#form-ddjj').data('action', '')
    var total = $('.chk-declaro').length;
    var aceptados = 0;
    $('.chk-declaro-label').on('click', function () {
      if(!$(this).hasClass('active')) {
        aceptados++;
      } else {
        aceptados--;
      }
      if(aceptados == total) {
        $('#btn_aprobar').attr('disabled', false);
      } else {
        $('#btn_aprobar').attr('disabled', true);
      }
    });

    $('.open-modal-ddjj').unbind( "click" ).click(function() {
      $('#form-ddjj').data('action', $(this).data('action'));
      $('#modal_ddjj').modal('show');
    });

    applyFormChecklistAjax();
  });

  window.applyFormChecklistAjax = () => {
    $('.aprobar-modal').unbind('click').on('click', function(e) {
      var action = $('#form-ddjj').data('action');
      if($(this).attr('disabled') != undefined)
        return false;
      $('.help-block').remove();
      $('.form-group').removeClass('has-error');
      e.preventDefault();
      if(!$('body').find('.gralState').hasClass('state-loading'))
          loadingToggle();

      $.ajax({
        url: action,
        type: 'POST',
        dataType: 'json',
        data: new FormData($('#form-ddjj')[0]),
        processData: false,
        contentType: false,
        success: function(resp) {
          if(resp.status == true) {
            if(resp.message != undefined)
              modalCloseToastSuccess(resp.message);
            if(resp.url != undefined) {
              window.location.href = resp.url;
            }
            if(resp.refresh != undefined && resp.refresh == true) {
              location.reload();
            }
          } else {
            if($('body').find('.gralState').hasClass('state-loading'))
                loadingToggle();

            if(Object.keys(resp.errores).length > 0) {
              mostrarErroresEnInput(resp.errores);
            }

            if(resp.message.length > 0)
              modalCloseToastError(resp.message);
          }
        }
      });
    });
  }
@endsection
