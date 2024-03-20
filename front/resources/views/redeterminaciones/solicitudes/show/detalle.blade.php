<div class="panel panel-default panel-view-data border-top-poncho" id="panel_detalle_redeterminacion">
  <div class="panel-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12 form-group">
          <label class="m-0">@trans('forms.contrato')</label>
          <span class="form-control">
      		  @if($contrato->is_adenda)
              <a href="{{route('adenda.ver', ['id' => $contrato->id]) }}">
            @else
              <a href="{{route('contratos.ver', ['id' => $contrato->id]) }}">
            @endif
            {{ $contrato->nombre_completo}}
            </a>
          </span>
        </div>
        <div class="col-sm-12 col-md-12 form-group">
          <label class="m-0">@trans('forms.solicitado_por')</label>
          <span class="form-control item_detalle">
            @if($solicitud->user_contratista_id != null)
              {{$solicitud->contratista->user->nombre_apellido}}
            @else
              @trans('sol_redeterminaciones.redetermino_automaticamente')
            @endif
          </span>
        </div>
        <div class="col-sm-12 col-md-6 form-group">
          <label class="m-0">@trans('forms.salto')</label>
          <span class="form-control item_detalle">
            <span class="label label-success" style="background-color:var(--green-redeterminacion-color);">
              {{ $solicitud->salto->moneda_mes_anio }}
            </span>
          </span>
        </div>
        <div class="col-sm-12 col-md-6 form-group">
          <label class="m-0">@trans('forms.vr')</label>
          <span class="form-control item_detalle">
            <span class="label label-success" style="background-color:var(--green-redeterminacion-color);">
              @toCuatroDec($solicitud->salto->variacion)
            </span>
          </span>
        </div>
        @if($solicitud->observaciones != null)
          <div class="col-md-12 form-group">
            <label class="m-0">@trans('index.observaciones')</label>
            <span class="form-control item_detalle" id="observaciones_detalle">
              {{ $solicitud->observaciones }}
            </span>
          </div>
        @endif

        @if($solicitud->adjunto != null)
          <div class="col-md-12 form-group">
            <label class="m-0">@trans('forms.calculos_contratista')</label>
            <span class="form-control item_detalle">
              <i class="fa fa-paperclip grayCircle"></i>
              <a href="{{route('descargar', ['nombre' => $solicitud->adjunto_nombre, 'url' => $solicitud->adjunto_link])}}">{{ $solicitud->adjunto_nombre}}</a>
            </span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="panel_scrollable datos_cargados">
  @include('redeterminaciones.solicitudes.show.datos_cargados')
</div>
