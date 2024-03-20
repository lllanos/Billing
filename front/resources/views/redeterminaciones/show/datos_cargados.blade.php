<!-- CargaPreciosRedeterminados -->
@if($redeterminacion->nro_sigo != null)
  <div class="panel panel-default panel-view-data border-top-poncho">
    <div class="panel-body panel_rederterminacion_hist_detalles" id="panel_rederterminacion_hist_detalles">
      <div class="col-md-12">
        @if($redeterminacion->nro_sigo != null)
        <div class="row">
          @if($redeterminacion->monto_vigente != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.monto_vigente')}}</label>
              <span class="form-control">
                {{ $redeterminacion->monto_vigente }}
              </span>
            </div>
          @endif

          @if($redeterminacion->mayor_gasto_no_red != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.mayor_gasto_no_red')}}</label>
              <span class="form-control">
                {{ $redeterminacion->mayor_gasto_no_red }}
              </span>
            </div>
          @endif

          @if($redeterminacion->cuadro_comparativo != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.cuadro_comparativo')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i> <a href="{{route('descargar', ['nombre' => $redeterminacion->cuadro_comparativo_nombre, 'url' => $redeterminacion->cuadro_comparativo_link])}}"
                id="cuadro_comparativo_{{$redeterminacion->id}}">{{ $redeterminacion->cuadro_comparativo_nombre}}</a>
              </span>
            </div>
          @endif
        </div>
      @endif
<!-- FIN CargaPreciosRedeterminados -->

<!-- AsignacionPartidaPresupuestaria -->
<!-- FIN AsignacionPartidaPresupuestaria -->

<!-- CargaPolizaCaucion -->
      @if($redeterminacion->poliza_caucion_id != null)
        <div class="row">
          <div class="col-sm-12 col-md-12 form-group">
            <label class="m-0">{{trans('forms.poliza')}}</label>
            <span class="form-control">
              <i class="fa fa-paperclip grayCircle"></i>
              <a href="{{route('descargar', ['nombre' => $redeterminacion->poliza_nombre, 'url' => $redeterminacion->poliza_link])}}" id="poliza_caucion_{{$redeterminacion->id}}" >
                {{$redeterminacion->poliza_nombre}}
                @if($redeterminacion->poliza_valida) <i class="fa fa-check-circle text-success"></i> @endif
              </a>
            </span>
          </div>
        </div>
      @endif
<!-- FIN CargaPolizaCaucion -->

<!-- ProyectoActaRDP -->
      @if($redeterminacion->acta != null)
        <div class="row">
          <div class="col-sm-12 col-md-12 form-group">
            <label class="m-0">{{trans('forms.acta')}}</label>
            <span class="form-control">
              <i class="fa fa-paperclip grayCircle"></i>
              <a href="{{route('solicitudes.descargarActa', ['id'=> $redeterminacion->id])}}"
              id="acta_rdp">{{trans('redeterminaciones.acta')}}</a>
            </span>
          </div>
        </div>
      @endif
<!-- FIN ProyectoActaRDP -->

<!--SolicitudRDP-->
    @if($redeterminacion->solicitud_rdp != null)
      <div class="row">
        <div class="col-sm-12 col-md-6 form-group">
          <label class="m-0">{{trans('redeterminaciones.solicitud_rdp')}}</label>
          <span class="form-control">
            <i class="fa fa-paperclip grayCircle"></i>
            <a href="{{route('solicitudes.descargarActaRDP', ['id'=> $redeterminacion->id])}}" id="solicitud_rdp">{{trans('redeterminaciones.solicitud_rdp')}}</a>
          </span>
        </div>
      </div>
    @endif
<!--Fin SolicitudRDP-->

<!-- FirmaContratista -->

      @if($redeterminacion->fc_gedo_acta != null)
        <div class="row">
          @if($redeterminacion->usuario_firma_id != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('redeterminaciones.usuario_que_firmo')}}</label>
              <span class="form-control">
                {{ $redeterminacion->usuario_firma->nombre_apellido }}
              </span>
            </div>
          @endif

          @if($redeterminacion->fc_poliza != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.poliza')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('descargar', ['nombre' => $redeterminacion->fc_poliza_nombre, 'url' => $redeterminacion->fc_poliza_link])}}"
                id="fc_poliza">{{$redeterminacion->fc_poliza_nombre}}</a>
              </span>
            </div>
          @endif
          @if($redeterminacion->fc_solicitud_firmada != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.solicitud_rdp_firmada')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('descargar', ['nombre' => $redeterminacion->fc_solicitud_firmada_nombre, 'url' => $redeterminacion->fc_solicitud_firmada_link])}}"
                id="fc_solicitud_firmada_link">{{$redeterminacion->fc_solicitud_firmada_nombre}}</a>
              </span>
            </div>
          @endif
          @if($redeterminacion->fc_acta_firmada != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.acta_rdp_firmada')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('descargar', ['nombre' => $redeterminacion->fc_acta_firmada_nombre, 'url' => $redeterminacion->fc_acta_firmada_link])}}"
                id="acta_rdp_firmada_link">{{$redeterminacion->fc_acta_firmada_nombre}}</a>
              </span>
            </div>
          @endif
        </div>
      @endif
<!-- FIN FirmaContratista -->

<!-- EmisionDictamenJuridico -->
<!-- FIN EmisionDictamenJuridico -->

<!-- ActoAdministrativo -->
      @if($redeterminacion->doc_resolucion_id != null)
        <div class="row">
          @if($redeterminacion->doc_resolucion_id != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.resolucion_disposicion')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('descargar', ['nombre' => $redeterminacion->doc_resolucion->adjunto_nombre, 'url' => $redeterminacion->doc_resolucion->adjunto_link])}}"
                id="doc_resolucion_link">{{$redeterminacion->doc_resolucion->adjunto_nombre}}</a>
              </span>
            </div>
          @endif

          @if($redeterminacion->acto_acta_adjunta != null)
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">{{trans('forms.acta_rdp_aprobada')}}</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('descargar', ['nombre' => $redeterminacion->acto_acta_adjunta_nombre, 'url' => $redeterminacion->acto_acta_adjunta_link])}}"
                  id="acto_acta_adjunta_link" target="_blank">{{$redeterminacion->acto_acta_adjunta_nombre}}</a>
              </span>
            </div>
          @endif
        </div>
      @endif
<!-- FIN ActoAdministrativo -->

<!-- EmisionCertificadoRDP -->
      @if(sizeof($redeterminacion->certificados_rdp) > 0)
        <div class="row">
          <div class="col-md-12 col-sm-12 form-group">
            <label class="m-0">{{trans('forms.certificados')}}</label>
            <span class="form-control">
            @foreach($redeterminacion->certificados_rdp as $keyCert => $valueCert)
              <div class="col-md-3 pl-0">
                <span>
                  <i class="fa fa-paperclip grayCircle"></i>
                  <a href="{{route('descargar', ['nombre' => $valueCert->adjunto_nombre, 'url' => $valueCert->adjunto_link])}}">
                  {{ trans('forms.certificado')}} {{$keyCert + 1}}</a>
                </span>
              </div>
            @endforeach
            </span>
          </div>
        </div>
      @endif
<!-- FIN EmisionCertificadoRDP -->

      </div>
    </div>
  </div>
@endif
