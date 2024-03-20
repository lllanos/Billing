@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('contratos.index')}}">@trans('index.mis') @trans('forms.contratos')</a></li>
        <li class="active">@trans('index.analisis_precios') {{$contrato->expediente_madre}}</li>
      </ol>
      <div class="page-header page_header_detalle">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            {!!trans('index.analisis_precios')!!} {{$contrato->expediente_madre}}
            <span class="badge" style="background-color:#{{$contrato->estado_actual_analisis->nombre_color['color']}};">
              {{$contrato->estado_actual_analisis->nombre_color['nombre_tag']}}
            </span>
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop">
              @foreach($contrato->acciones_posibles_analisis as $keyAccion => $valueAccion)
                <a data-toggle="tooltip" data-placement="left"class="btn btn-primary btn-confirmable"
                 data-body="{{trans('analisis_precios.mensajes.confirmar.' . $valueAccion)}}"
                  data-action="{{ route('AnalisisPrecios.' . $valueAccion, ['id' => $contrato->id]) }}"
                  data-si="@trans('index.si')" data-no="@trans('index.no')">
                  {!!trans('analisis_precios.acciones.' . $valueAccion)!!}
                </a>
              @endforeach
              @if($contrato->estado_actual_analisis->nombre != 'sin_analisis')
                <a class="open-historial btn btn-info" id="btn_historial" data-url="{{ route('AnalisisPrecios.historial', ['contrato_id' => $contrato->id]) }}">
                  @trans('index.historial')
                </a>
              @endif
            </div>
            <div class="button_responsive">
              <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <li>
                    <a href="{{ route('AnalisisPrecios.enviar_aprobar', ['id' => $contrato->id]) }}">
                      @trans('analisis_precios.acciones.enviar_aprobar')
                    </a>
                  </li>
                  <li>
                    <a class="open-historial mouse-pointer" id="btn_historial_resp" data-url="{{ route('AnalisisPrecios.historial', ['contrato_id' => $contrato->id]) }}">
                      @trans('index.historial')
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
        <form class="form_excel" method="POST" data-action="{{ route('AnalisisPrecios.exportar', ['id' => $contrato->id]) }}" id="form_excel">
          {{ csrf_field() }}
          <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input"
          value="">
          <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')}}" aria-label="@trans('index.descargar_a_excel')}}">
          <i class="fa fa-file-excel-o fa-2x"></i>
          </button>
        </form>

        <input type="text" class="search-input form-control input_dos_btns" name="search_input_no_post" id="search_input_no_post" value ="" placeholder="@trans('forms.busqueda_placeholder')" aria-label="@trans('index.input') @trans('index.buscar')">
        <span class="input-group-btn">
          <button type="submit" id="search_button_no_post" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
            <i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </div>

  </div>
  <div class="row">
    <div class="col-md-12">
      @include('contratos.contratos.show.detalle')
    </div>
  </div>

  @if($contrato->instancia_actual_analisis != null && $contrato->instancia_actual_analisis->observaciones != '' && $contrato->instancia_actual_analisis->observaciones != null)
    <div class="col-md-12">
      <div class="observaciones alert alert-info">
        {{$contrato->instancia_actual_analisis->observaciones}}
      </div>
    </div>
  @endif

  {{-- tabs --}}
    <ul class="nav nav-tabs" id="tabs_mod">
      <li class="active tab__li"><a data-toggle="tab" href="#items">@trans('analisis_precios.items')</a></li>
      <li class="tab__li"><a data-toggle="tab" href="#materiales_directos">@trans('analisis_precios.materiales_directos')</a></li>
      <li class="tab__li"><a data-toggle="tab" href="#materiales_comerciales">@trans('analisis_precios.materiales_comerciales')</a></li>
      <li class="tab__li"><a data-toggle="tab" href="#materiales_explotados">@trans('analisis_precios.materiales_explotados')</a></li>
      <li class="tab__li"><a data-toggle="tab" href="#materiales_obra">@trans('analisis_precios.mano_obra')</a></li>
      <li class="tab__li"><a data-toggle="tab" href="#coeficiente_resumen">@trans('analisis_precios.coeficiente_resumen')</a></li>
    </ul>
  {{-- fin tabs --}}
  <div class="row">
    <div class="col-md-12" id="panel_tabla">
      @if(sizeof($items_por_obra) > 0)
        {{-- contenedor tabs --}}
          <div class="tab-content">
            @include('analisis_precios.tablas.index')
          </div>
        {{-- fin contenedor tabs --}}
      @else
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="sin_datos">
            <h1 class="text-center">@trans('analisis_precios.no_datos')</h1>
          </div>
        </div>
      </div>
      @endif
      <div class="sin_datos_js"></div>
    </div>
  </div>


@endsection

@section('modals')
  @include('analisis_precios.modals.modals')
@endsection

@section('scripts')
  $(document).ready(() => {
    applyModalHistorial();
    applyModalCategoria();
    applyModalCoeficiente();
    applyModalInsumo();
    applyFlex();

    $('.modal').on('shown.bs.modal', function () {
      if($('#js_applied').val() == 0) {
        applyAll();
      }
    });

    $('.submit-accion').on('click', function(e) {
      var action = $(this).data('action');
      loadingToggle();
      $.ajax({
        url: action,
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
            loadingToggle();
            if(resp.status == true) {
              modalCloseToastSuccess(resp.message);
              location.reload();
            } else {
              if(resp.errores) {
                mostrarErrores(resp.errores);
              }
              if(resp.message.length > 0)
                modalCloseToastError(resp.message);
            }
        }
      });
    });

  });



  const applyFlex = () =>{
    $('.container_datos_drop').each(function(){
      let containerText = $(this).children('span.container_icon_angle');
      let text = $(this).children('span.container_icon_angle').text();
      let cantCaracteres = $.trim(text).length;
      if(cantCaracteres < 54){
        containerText.removeClass('d-flex');
      }
    })
  }


  // Categorias
  var applyModalCategoria = () => {
    $('.open-modal-categorias').unbind("click").click(function() {
      loadingToggle();
      var url = $(this).data('url');

      $.get(url, function(data) {
        $('#modalCategoria').find('.panel-content').html(data);

        $('#modalCategoria').modal('show');
        applyFormAjaxItemizado('form-ajax-categorias');
        loadingToggle();
      });
    });
  }

  // Coeficiente
  var applyModalCoeficiente = () => {
    $('.open-modal-coeficiente').unbind("click").click(function() {
      loadingToggle();
      var url = $(this).data('url');
      var title = $(this).data('title');

      $.get(url, function(data) {
        $('#modalCategoria').find('.panel-content').html(data);
        $('#modalCategoria').find('.modal-title').html(title);

        $('#modalCategoria').modal('show');
        applyFormAjaxItemizado('form-ajax-coeficiente');
        loadingToggle();
      });
    });
  }

  var applyFormAjaxItemizado = (form) => {
    $('#' + form).off('submit').on('submit', function(e) {
      $('.help-block').remove();
      $('.form-group').removeClass('has-error');
      e.preventDefault();
      var action = $('#' + form).data('action');
      console.log(action);
      loadingToggle();
      $.ajax({
        url: action,
        type: 'POST',
        dataType: 'json',
        data: new FormData($('#' + form)[0]),
        processData: false,
        contentType: false,
        success: function(resp) {
          if(resp.status == true) {
            $(resp.destino).append(resp.html);
            console.log(resp.destino, resp.html);
            applyOnAppend();
          } else {
            loadingToggle();
            $('.chosen-container').css('margin-top', '-156px');
            if(Object.keys(resp.errores).length > 0) {
              mostrarErroresEnInput(resp.errores);
              window.scrollTo($('#modalCategoria').position());
            }

            if(resp.message.length > 0)
              modalCloseToastError(resp.message);
          }
        }
      });
    });
  }
  // FIN Categorias


  var applyModalInsumo = () => {
    $('.open-modal-insumo').unbind("click").click(function() {
      loadingToggle();
      var url = $(this).data('url');

      $.get(url, function(data) {
        $('#modalInsumo').find('.modal-content').html(data);

        $('#modalInsumo').modal('show');
        applyFormAjaxItemizado('form-ajax-insumo');
        loadingToggle();
      });
    });
  }


  // Al actualizar HTML
  var applyOnAppend = () => {
    applyAll();
    applyModalCategoria();
    applyModalCoeficiente();
    applyModalInsumo();
    $('#modalCategoria').modal('toggle');
    loadingToggle();
  }
@endsection
