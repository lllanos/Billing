@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{ $solicitud->estado_ruta_titulo['ruta'] }}">{{ $solicitud->estado_ruta_titulo['titulo'] }}</a></li>
        <li class="active">@trans('index.ver') {!!trans('index.solicitud_contrato')!!}</li>
      </ol>
      <div class="page-header">
        <h3 class="title_conbtns">
          <div>
            @trans('index.solicitud_contrato') {{ $solicitud->expediente_madre }}
            <span class="badge" style="background-color:#{{ $solicitud->estado_nombre_color['color'] }};">
              {{ $solicitud->estado_nombre_color['nombre'] }}
            </span>
          </div>
          <div class="buttons-on-title">
            @permissions(('sol-contrato-aprobar'))
              @if($solicitud->instancia_actual->puede_aprobar)
                <a id="btn_guardar" href="javascript:void(0);"
                  data-action="{{route('contrato.solicitud.aprobar', ['id' => $solicitud->id])}}"
                class="btn btn-success open-modal-ddjj">@trans('index.aprobar')</a>
              @endif
            @endpermission
            @permissions(('sol-contrato-rechazar'))
              @if($solicitud->instancia_actual->puede_rechazar)
              <a href="javascript:void(0);" class="open-modal-rechazar btn btn-default"
                id="btn_rechazar_{{$solicitud->id}}"
                data-action="{{ route('contrato.solicitud.rechazar', ['id' => $solicitud->id]) }}"
                data-id="{{$solicitud->id}}"
                data-title="{{trans('contratos.confirmar.rechazar'). $solicitud->expediente_madre .'?'}}"
                title="@trans('index.rechazar')">
                @trans('index.rechazar')
              </a>
              @endif
            @endpermission
          </div>
        </h3>
      </div>
      @if($solicitud->nro_gde != null)
        <div class="col-md-12 nro_gede_ p-0">
          <labe class="label label-warning">@trans('forms.nro_gde'): {{$solicitud->nro_gde}}</label>
        </div>
      @endif

    <div class="row">
      <div class="col-md-8">
        @include('contratos.solicitudes.show.showContent')
      </div>
      <div class="col-md-4">
        @include('contratos.solicitudes.show.historial')
      </div>
    </div>

    </div>
  </div>
@endsection

@section('modals')
  @include('contratos.solicitudes.modal.rechazar')
  @include('contratos.solicitudes.modal.checklist_aprobar')
@endsection

@section('scripts')
  $(document).ready( () => {
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

    applyHtmlChange();
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
