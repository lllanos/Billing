@extends ('layout.app')

@section('title', config('app.name'))

@section('content')
  <div class="row">
    <div class="col-md-12">
      <ol class="breadcrumb">
        <li><a href="{{url('/')}}">@trans('forms.inicio')</a></li>
        <li><a href="{{route('publicaciones.index')}}">@trans('forms.publicaciones')</a></li>
        <li class="active">@trans('index.editar') @trans('index.indices_mensual')</li>
      </ol>

      <div class="page-header page_header__badge">
        <h3 class="page_header__titulo">
          <div class="titulo__contenido">
            @trans('index.indices_mensual') {{ $publicacion->mes_anio }}
            <span class="badge" style="background-color:#{{ $publicacion->estado_nombre_color['color'] }};">
            {{ $publicacion->estado_nombre_color['nombre'] }}
          </span>
          </div>
          <div class="buttons-on-title">
            <div class="button_desktop">
              @foreach($publicacion->acciones as $keyAccion => $valueAccion)
                @permissions(('publicacion-'. $valueAccion))
                <a class="btn btn-primary submit @if($valueAccion == 'rechazar') btn-default @endif"
                  id="btn_{{$valueAccion}}" data-accion="{{$valueAccion}}" data-id="{{$publicacion->id}}">
                  @trans('publicaciones.instancia.acciones.' . $valueAccion)
                </a>
                @endpermission
              @endforeach

              <div class="dropdown dd-on-table pull-right">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')" id="dd_acciones">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right multi-level" role="menu" aria-labelledby="dropdownMenu">
                  @permissions(('indice-create'))
                  @if($publicacion->estado_key == 'guardar_borrador' || $publicacion->estado_key == 'rechazar')
                    <li data-route="{{route('indices.create', ['publicacion_id' => $publicacion->id])}}" class="open-modal-indices mouse-pointer">
                      <a>@trans('index.nuevo_indice')</a></li>
                  @endif
                  <li>
                    <a class="open-historial mouse-pointer" id="btn_historial" data-url="{{ route('publicaciones.historial', ['id' => $publicacion->id]) }}">
                      @trans('index.historial')
                    </a>
                  </li>
                  @endpermission
                </ul>
              </div>
            </div>
            <div class="button_responsive">
              <div class="dropdown dd-on-table" data-placement="left">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                  <i class="fa fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  @foreach($publicacion->acciones as $keyAccion => $valueAccion)
                    @permissions(('publicacion-'. $valueAccion))
                    <li>
                      <a class="submit mouse-pointer" id="btn_{{$valueAccion}}_resp" data-accion="{{$valueAccion}}" data-id="{{$publicacion->id}}">
                        @trans('publicaciones.instancia.acciones.' . $valueAccion)
                      </a>
                    </li>
                    @endpermission
                  @endforeach
                  <li>
                    <a class="open-historial mouse-pointer" id="btn_historial_resp" data-url="{{ route('publicaciones.historial', ['id' => $publicacion->id]) }}">
                      @trans('index.historial')
                    </a>
                  </li>
                  @if($publicacion->estado_key == 'guardar_borrador' || $publicacion->estado_key == 'rechazar')
                    <li data-route="{{route('indices.create', ['publicacion_id' => $publicacion->id])}}" class="open-modal-indices">
                      <a href="#">@trans('index.nuevo_indice')</a>
                    </li>
                  @endif
                </ul>
              </div>
            </div>
          </div>
        </h3>
      </div>
    </div>

    <!--Input file excel con 1 form-->
    <div class="input-group rounded col-xs-12 col-sm-12 col-md-12 mb-_5 badges_vr__input_excel" role="group" aria-label="...">
      <div class="col-xs-12 col-sm-6 col-md-6 container_badges_vr container_badges_vr_indices">
        <span class="">@trans('index.referencias')</span>
        <label class="label indice_no_se_publica">@trans('index.no_publica')</label>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6 contenedor_input_dos_btns">
        <form
          class="form_excel"
          method="POST"
          data-action="{{ route('publicaciones.export.edit', ['id' => $publicacion->id]) }}"
          id="form_excel"
        >
          {{ csrf_field() }}

          <input
            type="text"
            class="excel-search-input form-control"
            name="excel_input"
            id="excel_input"
            value=""
          >

          <button type="submit" id="excel_button" class="btn btn-info btn-filter-toggle pull-left exportar btnGroupHeight btn_exc_izq"
            data-toggle="tooltip" data-placement="bottom" title="@trans('index.descargar_a_excel')"
            aria-label="@trans('index.descargar_a_excel')"
          >
            <i class="fa fa-file-excel-o fa-2x"></i>
          </button>
        </form>

        <input type="text" class="search-input form-control input_dos_btns" name="search_input_no_post" id="search_input_no_post" value="" aria-label="@trans('index.input') @trans('index.buscar')" placeholder="@trans('forms.busqueda_placeholder')">

        <span class="input-group-btn">
          <button type="submit" id="search_button_no_post" class="btn btn-info btn-filter-toggle" data-toggle="tooltip" data-placement="bottom" title="@trans('index.buscar')" aria-label="@trans('index.buscar')">
            <i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </div>
    <!-- Input file excel con 1 form-->

    <div class="col-md-12">
      <div class="errores-publicacion hidden alert alert-danger"></div>
    </div>

    <div class="col-md-12">
      <form method="POST" action="{{route('publicaciones.update', ['id' => $publicacion->id ])}}" data-action="{{route('publicaciones.update', ['id' => $publicacion->id ])}}" id="form-publicaciones">
        {{ csrf_field() }}
        <input class="hidden" name="accion" id="accion">
        <!--Panel-->
        <div class="">
          <div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
            @php($contador_categoria = 1)
            @php($contador_sub_categoria = 1)
            @if(sizeof($valores_por_categoria) > 0)
              @foreach($valores_por_categoria as $keyMoneda => $valueMoneda)
                <h5>{{$valueMoneda['moneda']}}</h5>
                @foreach($valueMoneda['valores'] as $keyCategoria => $categoria)
                <!--Collapse-->
                  <div class="panel-group colapsable_top moneda-{{$valueMoneda['moneda_key']}}" id="accordion_{{$contador_categoria}}" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="headingOne_{{$contador_categoria}}">
                        <h4 class="panel-title m-0 titulo_collapse">
                          <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_{{$contador_categoria}}" href="#collpapse_{{$contador_categoria}}" aria-expanded="true" aria-controls="collpapse_{{$contador_categoria}}">
                            <i class="fa fa-angle-down"></i> {{$keyCategoria}}
                          </a>
                        </h4>
                      </div>

                      <div id="collpapse_{{$contador_categoria}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_{{$contador_categoria}}">
                        <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
                        @foreach($categoria as $keySubcategoria => $subcategoria)
                          @if($keySubcategoria != 'N/A')
                            <!--Sub Collpase-->
                              <div class="panel-group colapsable_sub" id="accordion_sub_{{$contador_sub_categoria}}" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                  <div class="panel-heading panel_heading_collapse p-0" role="tab" id="headingOne_sub_{{$contador_sub_categoria}}">
                                    <h4 class="panel-title pl-2 m-0 titulo_collapse">
                                      <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_sub_{{$contador_sub_categoria}}" href="#collapseOne_sub{{$contador_sub_categoria}}" aria-expanded="true" aria-controls="collapseOne_sub{{$contador_sub_categoria}}">
                                        <i class="fa fa-angle-down"></i> {{$keySubcategoria}}
                                      </a>
                                    </h4>
                                  </div>
                                  <div id="collapseOne_sub{{$contador_sub_categoria}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub_{{$contador_sub_categoria}}">
                                    @endif
                                    <div class="panel-body panel_con_tablas_y_sub_tablas p-0">
                                      <!--Tabla scrollable-->
                                      <div class="col-md-12 col-sm-12">
                                        <div class="list-table pt-0 pb-1">
                                          <div class="zui-wrapper zui-action-32px-fixed">
                                            <div class="zui-scroller div_tres_tablas tabla_tres_acciones">
                                              <!-- zui-no-data -->
                                              <table class="table table-striped table-hover zui-table">
                                                <thead>
                                                <tr>
                                                  <th class="text-center tb_indice_nro">#</th>
                                                  <th class="tb_indice_nombre_edit">
                                                    @trans('forms.nombre')
                                                  </th>
                                                  <th>@trans('forms.fuente')</th>
                                                  <th class="val_anterior_publicaciones">
                                                    @trans('forms.valor_anterior')
                                                  </th>
                                                  <th class="th_td_acciones th_td_num_valor actions-col nuevo_valor_publicaciones">
                                                    @trans('forms.nuevo_valor')
                                                  </th>
                                                  <th class="th_td_acciones th_td_num_vr vr_publucaciones actions-col">
                                                    @trans('forms.vr')
                                                  </th>
                                                  <th class="opciones_tres_tbls text-center actions-col opciones_tbla_tres_acciones">
                                                    <i class="glyphicon glyphicon-cog"></i>
                                                  </th>
                                                </tr>
                                                </thead>
                                                <tbody class="tbody_con_input tbody_tooltip">
                                                @foreach($subcategoria as $keyIndice => $valor_indice)
                                                  <tr class="@if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif">
                                                    <td class="text-center tb_indice_nro">
                                                      {{-- @if($valor_indice->indice_tabla1->compuesto)
                                <span data-toggle="tooltip" data-html="true" data-placement="bottom" title="{{$valor_indice->indice_tabla1->mensaje_composicion}}">
                                  <i class="fa fa-tasks" aria-hidden="true"></i>
                              @elseif($valor_indice->indice_tabla1->calculado)
                                <span data-toggle="tooltip" data-html="true" data-placement="bottom" title="@trans('publicaciones.carretero')">
                                  <i class="fa fa-calculator" aria-hidden="true"></i>
                              @else
                              @endif --}}
                                                      <span>
                                                    {{$valor_indice->indice_tabla1->nro}}
                                                  </span>
                                                    </td>
                                                    <td class="tb_indice_nombre_edit">
                                                <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->indice_tabla1->nombre}}">
                                                  {{$valor_indice->indice_tabla1->nombre}}
                                                </span>
                                                      <span class="hidden">{{$valueMoneda['moneda']}}</span>
                                                    </td>
                                                    <td class="tb_indice_fuente">
                                                      @if($valor_indice->indice_tabla1->fuente_id != null)
                                                        <span data-toggle="tooltip" data-placement="bottom" title="{{$valor_indice->indice_tabla1->fuente->nombre}}">
                                                    {{$valor_indice->indice_tabla1->fuente->nombre}}
                                                  </span>
                                                      @endif
                                                    </td>

                                                    <td class="text-right">
                                                      <span id="valor_old_{{$valor_indice->tabla_indices_id}}">{{$valor_indice->valor_anterior_show}}</span>
                                                    </td>
                                                    <td class="th_td_num_valor th_td_acciones nuevo_valor_publicaciones actions-col @if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif">
                                                      <input type="text" data-id="{{$valor_indice->tabla_indices_id}}" name="valor[{{$valor_indice->tabla_indices_id}}]" id="valor_{{$valor_indice->tabla_indices_id}}"
                                                        class="form-control valor text-right @if($es_borrador) @if($valor_indice->indice_tabla1->calculado) num_punto_y_coma_carretero @else num_punto_y_coma @endif  @else en-borrador @endif"
                                                        value="{{$valor_indice->valor_show}}" @if(!$es_borrador || $valor_indice->indice_tabla1->compuesto || $valor_indice->indice_tabla1->calculado) readonly @endif>
                                                    </td>
                                                    <td class="th_td_num_vr th_td_acciones vr_publucaciones actions-col @if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif">
                                                      <label id="vr_{{$valor_indice->tabla_indices_id}}" class="label label_default {{$valor_indice->color_class}}">
                                                        {{ $valor_indice->variacion_show }}
                                                      </label>
                                                    </td>
                                                    <td class="opciones_tres_tbls actions-col noFilter opciones_tbla_tres_acciones @if($valor_indice->indice_tabla1->no_se_publica) indice_no_se_publica @endif">
                                                      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
                                                          <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right">
                                                          @permissions(('indice-view'))
                                                          <li data-route="{{route('indices.show', ['id' => $valor_indice->indice_tabla1->id, 'publicacion_id' => $publicacion->id])}}" class="open-modal-indices mouse-pointer">
                                                            <a><i class="glyphicon glyphicon-eye-open"></i>
                                                              @trans('index.ver')</a>
                                                          </li>
                                                          @endpermission
                                                          @if($es_borrador)
                                                            @permissions(('indice-edit'))
                                                            <li data-route="{{route('indices.edit', ['id' => $valor_indice->indice_tabla1->id, 'publicacion_id' => $publicacion->id])}}" class="open-modal-indices mouse-pointer">
                                                              <a><i class="fa fa-pencil" aria-hidden="true"></i>
                                                                @trans('forms.editar')</a>
                                                            </li>
                                                            @endpermission
                                                            @permissions(('indice-deshabilitar'))
                                                            <li data-route="{{route('indices.validarDeshabilitar', ['id' => $valor_indice->indice_tabla1->id, 'publicacion_id' => $publicacion->id])}}" class="deshabilitar mouse-pointer">
                                                              <a><i class="fa fa-times" aria-hidden="true"></i>@trans('index.deshabilitar')</a>
                                                            </li>
                                                            @endpermission
                                                          @endif
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
                                      <!--Fin Tabla scrollable-->
                                    </div>
                                    @if($keySubcategoria != 'N/A')
                                  </div>
                                </div>
                              </div>
                            @endif
                            @php($contador_sub_categoria++)
                          @endforeach
                        </div>
                      </div>

                    </div>
                  </div>
                  <!--FIN Collapse-->
                  @php($contador_categoria++)
                @endforeach
              @endforeach
          </div>
          @else
            <div class="sin_datos">
              <h1 class="text-center">@trans('index.no_datos')</h1>
            </div>
          @endif
          <div class="sin_datos_js"></div>
        </div>
        <!--Fin Panel-->
        {{ Form::submit(trans('forms.hidden'), array('class' => 'hidden', 'id' => 'btn_submit')) }}
      </form>
    </div>
  </div>
@endsection

@section('modals')
  @if(in_array('rechazar', $publicacion->acciones))
    <div id="modalRechazar" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" class="fa fa-times fa-2x"></span>
            </button>
            <h4 class="modal-title">
              @trans('publicaciones.instancia.acciones.rechazar')
            </h4>
          </div>

          <div class="modal-body">
            <div class="container">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <form class="form-horizontal form-rechazar" role="form" method="POST" data-action="" id="form-ajax">
                    {{ csrf_field() }}
                    <label class="body-label"></label>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label>@trans('index.observaciones')</label>
                        <textarea class="form-control" name="observaciones" id="observaciones" placeholder="@trans('index.observaciones')"></textarea>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-primary pull-right" id="btn_guardar">
                        @trans('index.guardar')
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  @endif

  <div id="modalIndice" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      </div>
    </div>
  </div>


  @include('publicaciones.modals')
@endsection

@section('js')
  <script>
      $(document).ready(() => {

          $('input.num_punto_y_coma').keyup(function (event) {
              $(this).val(function (index, value) {
                  return value
                      .replace(/\D/g, "")
                      .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                      .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".")
                      ;
              });
          });

          $('input.num_punto_y_coma_carretero').keyup(function (event) {
              $(this).val(function (index, value) {
                  return value
                      .replace(/\D/g, "")
                      .replace(/([0-9])([0-9]{3})$/, '$1,$2')
                      .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".")
                      ;
              });
          });

          applyFormPublicaciones();
          applyModalHistorial();
          applyCalculoVR();
          applyDeshabilitar()
          applyEditMode();

          var compuestos = {!! json_encode($publicacion->indices_compuestos) !!};

          $.each(compuestos, (ind, comp) => {
              $.each(comp, (id, porc) => {
                  $('#valor_' + id).addClass('componente');
              });
          });

          applyCompuestosOnChange(compuestos);

          $('.submit').on('click', function (e) {
              $(this).off('click');
              this.setAttribute('disabled', 'disabled');
              var accion = $(this).data('accion');
              var id = $(this).data('id');
              $('#accion').val(accion);
              if (accion == 'enviar_aprobar') {
                  pedirValidacionEnviarAprobar(accion, id);
              }
              else if (accion == 'publicar') {
                  pedirValidacionPublicacion(accion, id);
              }
              else if (accion == 'rechazar') {
                  var route = "{{route('publicaciones.rechazar', ['id' => ':id'])}}";
                  route = route.replace(':id', id);
                  $('.form-rechazar').data('action', route);
                  $('#modalRechazar').modal('show');
                  applyAll();
              }
              else {
                  loadingToggle();
                  $('#form-publicaciones').submit();
              }
          });

      });

      window.applyFormPublicaciones = () => {
          $('#form-publicaciones').off('submit').on('submit', function (e) {
              e.preventDefault();
              if (!$("#form-publicaciones")[0].checkValidity()) {
                  $('input[type="submit"]').click()
              }
              else {
                  $('.help-block').remove();
                  $('.form-group').removeClass('has-error');
                  $('.errores-publicacion').addClass('hidden');
                  var action = $('#form-publicaciones').data('action');
                  loadingToggle();
                  $.ajax({
                      url: action,
                      type: 'POST',
                      dataType: 'json',
                      data: new FormData($('#form-publicaciones')[0]),
                      processData: false,
                      contentType: false,
                      success: function (resp) {
                          loadingToggle();
                          if (resp.status == true) {
                              modalCloseToastSuccess(resp.message);
                              location.reload();
                          }
                          else {
                              if (resp.errores) {
                                  mostrarErrores(resp.errores);
                              }
                              if (resp.message.length > 0) {
                                  modalCloseToastError(resp.message);
                              }
                          }
                      }
                  });
              }
          });
      }

      var pedirValidacionEnviarAprobar = (accion, id) => {
          $('.help-block').remove();
          $('.form-group').removeClass('has-error');
          $('.errores-publicacion').addClass('hidden');
          loadingToggle();
          var url = '/publicaciones/' + accion + '/' + id + '/preValidacion';
          $.ajax({
              url: url,
              type: 'POST',
              dataType: 'json',
              data: new FormData($('#form-publicaciones')[0]),
              processData: false,
              contentType: false,
              success: function (resp) {
                  loadingToggle();
                  if (resp.status == true) {
                      alertConfirm(resp.ok.title, resp.ok.message, resp.ok.route, true);
                  }
                  else {
                      if (resp.alert != undefined) {
                          // No permite publicar si hay publicaciones posteriores sin publicar
                          createModalAlert(resp.alert.title, resp.alert.message);
                      }
                      if (resp.errores) {
                          mostrarErrores(resp.errores);
                      }

                      if (resp.message.length > 0) {
                          modalCloseToastError(resp.message);
                      }
                  }
              }
          });
      }

      var pedirValidacionPublicacion = (accion, id) => {
          $('.help-block').remove();
          $('.form-group').removeClass('has-error');
          $('.errores-publicacion').addClass('hidden');
          loadingToggle();
          var url = '/publicaciones/' + accion + '/' + id + '/preValidacion';
          $.ajax({
              url: url,
              type: 'POST',
              dataType: 'json',
              data: new FormData($('#form-publicaciones')[0]),
              processData: false,
              contentType: false,
              success: function (resp) {
                  if (resp.status == true) {
                      loadingToggle();
                      // Se pide confirmacion para publicar
                      alertConfirm(resp.ok.title, resp.ok.message, resp.ok.route);
                  }
                  else {
                      if (resp.alert != undefined) {
                          loadingToggle();
                          // No permite publicar si hay publicaciones posteriores sin publicar
                          createModalAlert(resp.alert.title, resp.alert.message);
                      }
                      else if (resp.confirm != undefined) {
                          // Si hay una publicacion previa sin publicar se advierte que esta no
                          // podra ser publicada a futuro y se pide confirmacion
                          alertConfirm(resp.confirm.title, resp.confirm.message, resp.confirm.route);
                      }
                      else {
                          if (resp.errores) {
                              mostrarErrores(resp.errores);
                          }
                      }
                  }
              }
          });
      }

      var alertConfirm = (title, message, action, submit = false) => {
          BootstrapDialog.confirm({
              message: message,
              type: BootstrapDialog.TYPE_WARNING,
              closable: true,
              draggable: true,
              btnCancelLabel: "{{(trans('index.no'))}}",
              btnOKLabel: "{{(trans('index.si'))}}",
              btnOKClass: 'btn-primary btn-dialog-OK',
              btnCancelClass: 'btn-link btn-dialog-Cancel',
              callback: function (result) {
                  // result true si presiono si, false si se cierra el dialog
                  if (result) {
                      if (submit) {
                          $('#form-publicaciones').submit();
                      }
                      else {
                          loadingToggle();
                          $.ajax({
                              url: action,
                              type: 'POST',
                              dataType: 'json',
                              data: new FormData($('#form-publicaciones')[0]),
                              processData: false,
                              contentType: false,
                              dataType: 'json',
                              success: function (response) {
                                  modalCloseToastSuccess(response.message);
                                  location.reload();
                              }
                          });
                      }
                  }
              }
          });

      }

      var applyEditMode = () => {
          $('.open-modal-indices').unbind("click").click(function () {
              loadingToggle();

              var instancia = $(this).data('instancia');
              var id = $(this).data('id');
              var route = $(this).data('route');

              $.get(route, function (data) {
                  loadingToggle();

                  if (data.status !== undefined && data.status === false) {
                      modalCloseToastError(data.message);
                  }
                  else {
                      let $modal = $('#modalIndice').modal();

                      $modal.on('shown.bs.modal', function () {
                          let $form = $('#form-solicitud-ajax');

                          console.log(603);

                          if ($('#js_applied').val() === 0) {
                              applyAll();
                              applyHtmlChange();

                              $('#js_applied').val(1);
                          }

                          $form.off('submit');

                          console.log(619);

                          $form.on('submit', function (e) {

                              console.log(623);

                              e.preventDefault();
                              loadingToggle();
                              var action = $(this).data('action');

                              $('.modal .help-block').remove();
                              $('.modal .form-group').removeClass('has-error');
                              $('.modal').find('.modalToast').remove();

                              $.ajax({
                                  url: action,
                                  type: 'POST',
                                  dataType: 'json',
                                  data: new FormData($form[0]),
                                  processData: false,
                                  contentType: false,
                                  success: function (resp) {
                                      if (resp.status === true) {

                                          if (resp.refresh !== undefined && resp.refresh === true) {
                                              location.reload();
                                          }
                                          applyAll();
                                          applyEditMode();
                                      }
                                      else if (resp.status === false) {
                                          loadingToggle();

                                          //SHOW ERRORS
                                          if (resp.errores) {

                                              $.each(resp.errores, (i, e) => {
                                                  if (i === 'porcentaje') {
                                                      $.each(e, (id, error) => {
                                                          $('#porcentaje_' + id).closest('.form-group').addClass('has-error');
                                                          var html = `<span class="help-block">${error}</span>`;
                                                          $(html).insertAfter('#porcentaje_' + id);
                                                      })
                                                  }

                                                  let $element =  $('#' + i)

                                                  $element.closest('.form-group').addClass('has-error');

                                                  let html = `<span class="help-block">${e}</span>`;

                                                  if ($element.is(':input[type=file]')) {
                                                      $element.closest('.file-input-new').append(html);

                                                      if ($element.closest('.file-input-new').length === 0) {
                                                          $element.closest('.file-input').append(html);
                                                      }
                                                  }
                                                  else {
                                                      $(html).insertAfter('#' + i);
                                                  }
                                              });

                                              $('.modal .modalContentScrollable').scrollTop($('#form-solicitud-ajax').position());

                                              $('.modal .form-control').change(function () {
                                                  $(this).closest('.form-group').removeClass('has-error');
                                                  $(this).closest('.form-group').find('.help-block').remove();
                                                  $(this).closest('.form-group').parent().find('.help-outside').remove();
                                              });
                                          }

                                          if (resp.message != null) {
                                              let dataToast = {};

                                              dataToast.alert = 'danger';
                                              dataToast.icon = 'times';
                                              dataToast.msg = resp.message;

                                              if (resp.message.length > 0) {
                                                  modalToast(dataToast);
                                              }
                                          }
                                      }
                                  }
                              });
                          });
                      });
                      $modal.find('.modal-content').html(data);
                      $modal.modal('show');
                  }
              });
          });
      }

      var applyDeshabilitar = () => {
          $('.deshabilitar').unbind("click").click(function () {
              loadingToggle();

              let route = $(this).data('route');

              $.ajax({
                  url: route,
                  type: 'GET',
                  dataType: 'json',
                  processData: false,
                  contentType: false,
                  success: function (resp) {
                      loadingToggle();

                      if (resp.status === true) {
                          alertConfirm(resp.ok.title, resp.ok.message, resp.ok.action);
                      }
                      else {
                          if (resp.alert !== undefined) {
                              createModalAlert(resp.alert.title, resp.alert.message);
                          }
                      }
                  }
              });

          });
      }

      var applyCompuestosOnChange = (compuestos) => {
          $('.componente:not(.en-borrador)').change(function () {
              $.each(compuestos, (ind, comp) => {
                  let val_comp = 0;

                  $.each(comp, (id, porc) => {
                      let valor = $('#valor_' + id).val();

                      if (valor === '') {
                          valor = 0;
                      }

                      val_comp = (val_comp + parseFloat(valor) * (porc / 100));
                      val_comp = Math.round(val_comp * 10000000) / 10000000;
                  });

                  val_comp = val_comp.toFixed(2);

                  $('#valor_' + ind).val(val_comp).trigger("change");
              });
          });
      }
  </script>
@endsection
