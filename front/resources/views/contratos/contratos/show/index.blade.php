@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('forms.contratos')</a></li>
        @if($contrato->is_adenda)
          <li><a href="{{route('contratos.ver', ['id' => $contrato->contrato_padre->id]) }}">@trans('forms.contrato') {{$contrato->contrato_padre->expediente_madre}}</a></li>
          <li class="active">@trans('index.adenda') {{$contrato->numero_contrato}}</li>
        @else
          <li class="active">@trans('index.contrato') {{$contrato->expediente_madre}}</li>
        @endif
      </ol>
      <div class="page-header">
        <h3 class="page_header__titulo">
          @if($contrato->is_contrato)
            @include('contratos.contratos.show.header_contrato')
          @else
            @include('contratos.contratos.show.header_adenda')
          @endif
        </h3>
      </div>
    </div>
  </div>

  <input type='text' id='accion' name='accion' class='hidden' value="{{$accion}}">

  <div class="row">
    <div class="col-md-12">
      @include('contratos.contratos.show.detalle')
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
        @if($contrato->hasSeccion('anticipos'))
          <div id="anticipos_container">
            @include('contratos.contratos.show.anticipos.index')
          </div>
        @endif

        @if($contrato->hasSeccion('itemizado'))
          <div id="itemizado_container">
            @include('contratos.contratos.show.itemizado.index')
          </div>
        @endif

        @if($contrato->hasSeccion('analisis_precios'))
          <div id="analisis_precios_container">
            @include('contratos.contratos.show.analisis_precios.index')
          </div>
        @endif

        @if($contrato->hasSeccion('itemizado'))
          <div id="cronograma_container">
            @include('contratos.contratos.show.cronograma.index')
          </div>
        @endif

        @if(!$contrato->isAdendaAmpliacion)
            @if($contrato->hasSeccion('polinomica'))
              <div id="polinomica_container">
                @include('contratos.contratos.show.polinomica.index')
              </div>
            @endif
        @endif

        @if($contrato->hasSeccion('adendaCertificacion'))
          <div id="adendaCertificacion_container">
            @include('contratos.contratos.show.adendaCertificacion.index')
          </div>
        @endif

        @if($contrato->hasSeccion('adendaAmpliacion'))
          <div id="adendaAmpliacion_container">
            @include('contratos.contratos.show.adendaAmpliacion.index')
          </div>
        @endif

        @if(!$contrato->isAdendaAmpliacion)
          @if($contrato->hasSeccion('ampliaciones'))
            <div id="ampliacion_container">
              @include('contratos.contratos.show.ampliacion.index')
            </div>
          @endif
        @endif

        @if($contrato->hasSeccion('reprogramaciones'))
          <div id="reprogramacion_container">
            @include('contratos.contratos.show.reprogramacion.index')
          </div>
        @endif

        @if($contrato->hasSeccion('certificados'))
          <div id="certificados_container">
            @include('contratos.contratos.show.certificados.index')
          </div>
        @endif
    </div>
  </div>

@endsection

@section('modals')
  @include('contratos.contratos.show.modals.modals')
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

    applyAllContrato();

    if($('#accion').val() != null && $('#accion').val() != "") {
      if($('#headingOne-' + $('#accion').val()).length) {
        $('#headingOne-' + $('#accion').val()).find('h4 a.btn_acordion').click();
      }
    }
  });

  var applyCambiarVisualizacion = () => {
    $('.visualizacion').unbind("click").click(function() {
      var seccion = $(this).data('seccion');
      loadingToggleThis('#' + seccion + '_container');

      var url = "{{route('contrato.getViews', ['id' => $contrato->id, 'seccion' => ':seccion',
                                                      'version' => ':version', 'visualizacion' => ':visualizacion'])}}";

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
        if(data.highcharts != false)
          applyShowWidgets(data.highcharts, seccion);
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

  var applyShowWidgets = (name, seccion) => {
    url = "{{route('widget.contratos', ['name' => ':name', 'contrato_id' => $contrato->id, 'version' => ':version'])}}";
    url = url.replace(':name', name);
    url = url.replace(':version', $('#' + seccion + '_version').val());
    var nombre = name;

    $.ajax({
      url: url,
      cache: false,
      contentType: false,
      processData: false,
      method: 'GET',
      dataType: 'html',
      success: function(response) {
        $('.content-' + name).html(response);
      },

    });
  }

  var applyAllContrato = () => {
    applyCambiarVisualizacion();
    applyToggleUnidadMedida();
  }
@endsection
