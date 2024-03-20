@extends ('layout.app')

@section('title', config('app.name') )

@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <ol class="breadcrumb">
      <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
      <li class="active">@trans('index.list_of') @trans('forms.publicaciones')</li>
    </ol>
    <div class="page-header">
      <h3 class="page_header__titulo">
        <div class="titulo__contenido">
          @trans('index.list_of') @trans('forms.publicaciones')
        </div>
        <div class="buttons-on-title">
          <div class="button_desktop">
            <a class="btn btn-success open-modal-nueva" data-route="{{ route('publicaciones.create') }}" id="btn_nueva">
              @trans('index.nueva') @trans('index.publicacion')
            </a>
          </div>
          <div class="button_responsive">
            <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                <i class="fa fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a class="open-modal-nueva" data-route="{{ route('publicaciones.create') }}" id="btn_nueva_rsp">@trans('index.nueva') @trans('index.publicacion')</a></li>
              </ul>
            </div>
          </div>
        </div>
      </h3>
    </div>
  </div>
  <!--Input file excel con 2 form-->
  <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5" role="group" aria-label="...">
    <div class="col-xs-12 col-sm-offset-6 col-sm-6 col-md-offset-6 col-md-6 contenedor_input_dos_btns mb-_5">
      @permissions(('publicacion-export'))
      <form class="form_excel" method="POST" data-action="{{ route('publicaciones.export') }}" id="form_excel">
        {{ csrf_field() }}
        <input type="text" class="excel-search-input form-control" name="excel_input" id="excel_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')">
        <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq" data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')" aria-label="@trans('index.descargar_a_excel')">
          <i class="fa fa-file-excel-o fa-2x"></i>
        </button>
      </form>
      @endpermission
      <form method="POST" data-action="{{ route('publicaciones.index.post') }}" id="search_form">
        <button type="button" style="padding: 12px 19px !important" class="btn btn-default dropdown-toggle btnGroupHeight" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-dollar"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li>
            <a href="{{ route('publicaciones.index') }}">
              Todas
            </a>
          </li>
          @foreach($monedas as $keyMoneda => $valueMoneda)
          <li>
            <a href="{{route('publicaciones.filtrarPorMoneda', ['id' => $valueMoneda->id]) }}">
              {{ $valueMoneda["nombre"] }}
            </a>
          </li>
          @endforeach
        </ul>
        {{ csrf_field() }}
        <div class="input-group" style="display:inline;">
          <input type="text" class="search-input form-control input_dos_btns buscar_si" name="search_input" id="search_input" value="{{$search_input}}" placeholder="@trans('forms.busqueda_placeholder')" style="width: calc(100% - 160px) !important;">
          <span class="input-group-btn">
            <button type="submit" id="search_button" class="btn btn-info btn-filter-toggle buscar-si" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
              <i class="fa fa-search"></i>
            </button>
          </span>
        </div>
      </form>
    </div>
  </div>
  <!--Fin Input file excel con 2 form-->
  @if(sizeof($publicaciones) > 0)
  <div class="col-md-12 col-sm-12">
    <div class="list-table pt-0">
      <div class="zui-wrapper zui-action-32px-fixed">
        <div class="zui-scroller">
          <!-- zui-no-data -->
          <table class="table table-striped table-hover zui-table">
            <thead>
              <tr>
                <th>@trans('forms.mes_indice')</th>
                <th>@trans('forms.estado')</th>
                <th>@trans('forms.moneda')</th>
                <th>@trans('forms.user_publicador')</th>
                <th>@trans('forms.fecha_publicacion')</th>
                <th class="text-center actions-col"><i class="glyphicon glyphicon-cog"></i></th>
              </tr>
            </thead>
            <tbody>
              @foreach($publicaciones as $keyPublicacion => $valuePublicacion)
              <tr id="contrato_{{$valuePublicacion->id}}">
                <td>{{ $valuePublicacion->mes_anio }}</td>
                <td>
                  <span class="badge" style="background-color:#{{ $valuePublicacion->estado_nombre_color['color'] }};">
                    {{ $valuePublicacion->estado_nombre_color['nombre'] }}
                  </span>
                </td>
                <td>
                  {{ $valuePublicacion->moneda['nombre'] }}
                </td>
                <td>{{ $valuePublicacion->publicador_nombre_apellido }}</td>
                <td>{{ $valuePublicacion->fecha_publicacion }}</td>
                <td class="actions-col noFilter">
                  <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                      <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                      @permissions(('publicacion-view'))
                      <li><a href="{{route('publicaciones.show', ['id' => $valuePublicacion->id]) }}"><i class="glyphicon glyphicon-eye-open"></i> @trans('index.ver')</a></li>
                      @endpermission
                      @permissions(('publicacion-edit'))
                      @if(!$valuePublicacion->publicado && !$valuePublicacion->hay_publicaciones_publicadas_siguientes)
                      <li><a href="{{route('publicaciones.edit', ['id' => $valuePublicacion->id]) }}"><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
                      @endif
                      @endpermission
                      @permissions(('publicacion-ver_historial'))
                      <li><a data-url="{{ route('publicaciones.historial', ['id' => $valuePublicacion->id]) }}" id="btn_historial" class="open-historial"><i class="fa fa-history" aria-hidden="true"></i> @trans('index.historial')</a></li>
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
    {{ $publicaciones->render() }}
  </div>
  @else
  <div class="col-md-12 col-sm-12">
    <div class="sin_datos">
      <h1 class="text-center">@trans('index.no_datos')</h1>
    </div>
  </div>
  @endif
</div>
@endsection

@section('modals')
<div id="modalNuevaPublicacion" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>
        <h4 class="modal-title">
          @trans('index.nueva') @trans('index.publicacion')
        </h4>
      </div>
      <div class="contentNuevaPublicacion"></div>
    </div>
  </div>
</div>

@include('publicaciones.modals')
@endsection

@section('js')
<script>
  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    jqXHR.setRequestHeader('X-CSRF-Token', window.Laravel.csrfToken);
  });

  $(() => {
    applyNuevaPublicacion();
    applyModalHistorial();

    $('#modalNuevaPublicacion').on('hidden.bs.modal', function() {
      $('.contentNuevaPublicacion-content').html('');
      $('#form-solicitud-ajax').off('submit');
    });

    $(document).on("change", "#id_moneda", function() {
      let selectedID = $(this).find("option:selected").val();
      $.ajax({
        method: "POST",
        url: `/publicaciones/moneda/${selectedID}/opciones`,
        success: function(res) {
          let options = [];
          options.push("<option value=''>Seleccione Mes y Año</option>");
          for (const [key, value] of Object.entries(res.options)) {
            options.push(`<option value="${key}">${value}</option>`);
          }
          $(".mes-anio-form").show();
          $("#mes_anio").removeClass("no-chosen").empty().append(options.join(""));
          applyChosen();
        },
        error: function(res) {
          console.log(res);
        }
      })
    });
  });
  var applyNuevaPublicacion = () => {
    $('.open-modal-nueva').unbind("click").click(function() {
      route = $(this).data('route');
      $.get(route, function(data) {
        $('.contentNuevaPublicacion').html(data);
        $('#modalNuevaPublicacion').modal('show');
      });
    });

    $('#modalNuevaPublicacion').on('shown.bs.modal', function() {
      applyAll();
      applyFormAjaxCreate();
    });
  }

  var applyFormAjaxCreate = () => {
    $('#form-ajax-publicaciones').off('submit').on('submit', function(e) {
      $('.help-block').remove();
      $('.form-group').removeClass('has-error');
      e.preventDefault();
      var action = $('#form-ajax-publicaciones').data('action');
      loadingToggle();
      $.ajax({
        url: action,
        type: 'POST',
        dataType: 'json',
        data: new FormData($('#form-ajax-publicaciones')[0]),
        processData: false,
        contentType: false,
        success: function(resp) {
          if (resp.status == true) {
            if (resp.url != undefined) {
              window.location.href = resp.url;
            }
          } else {
            loadingToggle();
            $('.chosen-container').css('margin-top', '-156px');
            if (Object.keys(resp.errores).length > 0) {
              mostrarErroresEnInput(resp.errores);
              window.scrollTo($('#form-ajax-publicaciones').position());
            }

            if (resp.message.length > 0)
              modalCloseToastError(resp.message);
          }
        }
      });
    });
  }
</script>
@endsection