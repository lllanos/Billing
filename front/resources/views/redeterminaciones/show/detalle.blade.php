<div class="panel panel-default panel-view-data border-top-poncho" id="panel_detalle_redeterminacion">
  <div class="panel-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12 form-group">
          <label class="m-0">{{trans('forms.contrato')}}</label>
          <span class="form-control">
            <a href="{{route('contrato.ver', ['id' => $user_contrato->id]) }}">
              {{ $redeterminacion->salto->obra->contrato->expedientes}}
              @if($redeterminacion->contrato->contratista_id != null)
                - {{ $redeterminacion->contrato->contratista->nombre_cuit }}
              @endif
            </a>
          </span>
        </div>
        <div class="col-sm-12 col-md-6 form-group">
          <label class="m-0">{{trans('forms.categoria_de_obra')}}</label>
          <span class="form-control item_detalle">
              {{$redeterminacion->salto->obra->categoria_obra->nombre }}
          </span>
        </div>
        <div class="col-sm-12 col-md-6 form-group">
          <label class="m-0">{{trans('forms.solicitado_por')}}</label>
          <span class="form-control item_detalle">
            @if($redeterminacion->user_contratista_id != null)
              {{$redeterminacion->contratista->user->nombre_apellido}}
            @else
              {{trans('redeterminaciones.redetermino_automaticamente')}}
            @endif
          </span>
        </div>
        <div class="col-sm-12 col-md-12 form-group">
          <label class="m-0">{{trans('forms.salto')}}</label>
          <span class="form-control item_detalle">
            <span class="label label-success" style="background-color:var(--green-redeterminacion-color);">
              {{ $redeterminacion->salto->categoria_mes_anio }}
            </span>
          </span>
        </div>
        @if($redeterminacion->observaciones != null)
          <div class="col-md-12 form-group">
            <label class="m-0">{{trans('index.observaciones')}}</label>
            <span class="form-control item_detalle" id="observaciones_detalle">
              {{ $redeterminacion->observaciones }}
            </span>
          </div>
        @endif

        @if($redeterminacion->adjunto != null)
          <div class="col-md-12 form-group">
            <label class="m-0">{{trans('forms.calculos_contratista')}}</label>
            <span class="form-control item_detalle">
              <i class="fa fa-paperclip grayCircle"></i>
              <a href="{{route('descargar', ['nombre' => $redeterminacion->adjunto_nombre, 'url' => $redeterminacion->adjunto_link])}}">{{ $redeterminacion->adjunto_nombre}}</a>
            </span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="panel_scrollable datos_cargados">
  @include('redeterminaciones.show.datos_cargados')
</div>
