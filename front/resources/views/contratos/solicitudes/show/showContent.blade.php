<div id="panel_detalle_redeterminacion" class="panel panel-default panel-view-data border-top-poncho">
  <div class="panel-body">
    <div class="col-md-12">
      <div class="row detalle_contenido_height">
        <div class="col-xs-12 col-sm-6 col-md-6">
          <label class="m-0">{{trans('forms.fecha_solicitud')}}</label>
          <span class="form-control mb-1">{{ $solicitud->created_at }}</span>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6">
          <label class="m-0">{{trans('forms.expediente_madre')}}</label>
          <span class="form-control mb-1">{{ $solicitud->expediente_madre }}</span>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6">
          <label class="m-0">{{trans('forms.ultimo_movimiento')}}</label>
          <span class="form-control mb-1">{{ $solicitud->ultimo_movimiento }}</span>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6">
          <label class="m-0">{{trans('forms.caracter')}}</label>
          <span class="form-control mb-1">{{ $solicitud->caracter }}</span>
        </div>

        @foreach($solicitud->poderes as $keyPoder => $valuePoder)
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label class="m-0">{{trans('forms.poder')}}</label>
            <div class="adjuntos">
              <a href="{{route('descargar', ['nombre' => $valuePoder->adjunto_nombre, 'url' => config('app.url') . $valuePoder->adjunto_link])}}" id="file_{{$valuePoder->id}}"><i class="fa fa-paperclip grayCircle"></i>  {{$valuePoder->adjunto_nombre}}</a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6">
            @if($valuePoder->fecha_fin_poder != null)
            <label class="m-0">{{trans('forms.vigencia_poder')}}</label>
            <span class="form-control mb-1">{{$valuePoder->fecha_fin_poder}}</span>
            @endif
          </div>
        @endforeach

        <div class="col-xs-12 col-sm-12 col-md-12">
          <label class="m-0">{{trans('forms.descripcion')}}</label>
          <span class="form-control word-break mb-1" id="height-a">{{ $solicitud->descripcion }}</span>
        </div>

        @if($solicitud->observaciones != null && $solicitud->observaciones != '')
          <div class="col-md-12">
            <label class="m-0">{{trans('index.observaciones')}}</label>
            <span class="form-control word-break" id="observaciones_detalle">{{ $solicitud->observaciones }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
