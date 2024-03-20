@if($solicitud->certificados_aprobados !== null || $solicitud->monto_vigente != null)
  <div class="panel panel-default panel-view-data border-top-poncho">
    <div class="panel-body panel_rederterminacion_hist_detalles" id="panel_rederterminacion_hist_detalles">
      <div class="col-md-12">

<!-- Paso 1.1 AprobacionCertificados -->
        @if($solicitud->certificados_aprobados !== null)
          <div class="row">
            <div class="col-sm-12 col-md-12 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.certificados')</label>
              <span class="form-control">
                <a>
                  @if($solicitud->certificados_aprobados)
                    <i class="fa fa-check-circle text-success"></i>
                  @else
                    <i class="fa fa-times-circle text-danger"></i>
                  @endif

                  @trans('sol_redeterminaciones.certificados')

                  @if($solicitud->certificados_aprobados)
                    @trans('sol_redeterminaciones.aprobados')
                  @else
                    @trans('sol_redeterminaciones.no_aprobados')
                  @endif
                </a>
              </span>
            </div>
          </div>
        @endif
<!-- FIN Paso 1.1 AprobacionCertificados -->

<!-- Paso 1.2 VerificacionDesvio -->
        @if($solicitud->aplicar_penalidad_desvio !== null)
          <div class="row">
            <div class="col-sm-12 col-md-12 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.penalidad')</label>
              <span class="form-control">
                <a>
                  @if($solicitud->aplicar_penalidad_desvio)
                    <i class="fa fa-check-circle text-success"></i>
                  @else
                    <i class="fa fa-times-circle text-danger"></i>
                  @endif

                  @trans('sol_redeterminaciones.penalidad')

                  @if($solicitud->aplicar_penalidad_desvio)
                    @trans('sol_redeterminaciones.aplicada')
                  @else
                    @trans('sol_redeterminaciones.no_aplicada')
                  @endif
                </a>
              </span>
            </div>
          </div>
        @endif
<!-- FIN 1.2 VerificacionDesvio -->

<!-- Paso 2 CalculoPreciosRedeterminados -->
        @if($solicitud->monto_vigente != null)
          <div class="row">
            <div class="col-sm-12 col-md-4 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.monto_vigente')</label>
              <span class="form-control">
                @toDosDec($solicitud->monto_vigente)
              </span>
            </div>
            @if($solicitud->mayor_gasto != null )
              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.mayor_gasto')</label>
                <span class="form-control pt_5">
                  @toDosDec($solicitud->mayor_gasto)
                </span>
              </div>
            @endif

            @if($solicitud->saldo != null)
              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.saldo')</label>
                <span class="form-control">
                  @toDosDec($solicitud->saldo)
                </span>
              </div>
            @endif
            @if($solicitud->cuadro_comparativo != null)
              <div class="col-sm-12 col-md-12 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.cuadro_comparativo')</label>
                <span class="form-control">
                  <a href="{{route('cuadroComparativo.ver', ['id' => $solicitud->cuadro_comparativo->id])}}"
                    id="ver_cuadro_comparativo">
                    @trans('index.ver') @trans('sol_redeterminaciones.cuadro_comparativo')</a>
                </span>
              </div>
            @endif
          </div>
        @endif
<!-- FIN Paso 2 CalculoPreciosRedeterminados -->

<!-- Paso 3 GeneracionExpediente -->
        @if($solicitud->nro_expediente != null)
        <div class="row">
          <div class="col-sm-12 col-md-6 form-group">
            <label class="m-0">@trans('sol_redeterminaciones.nro_expediente')</label>
            <span class="form-control">
              {{$solicitud->nro_expediente}}
            </span>
          </div>
        {{-- @if($solicitud->doc_dictamen_id != null) --}}
        </div>
        @endif
<!-- FIN Paso 3 GeneracionExpediente -->

<!-- Paso 4 AsignacionPartidaPresupuestaria -->
        @if($solicitud->nro_partida_presupuestaria != null)
          <div class="row">
            <div class="col-sm-12 col-md-6 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.nro_partida_presupuestaria')</label>
              <span class="form-control">
                {{ $solicitud->nro_partida_presupuestaria }}
              </span>
            </div>
          </div>
          @if($solicitud->adjunto != null)
            <div class="contenido_proceso_hist_redeterminaciones">
                <a class="contenido_proc_item" href="{{route('descargar', ['nombre' => $solicitud->adjunto_nombre, 'url' => $solicitud->adjunto_link])}}">
                  <i class="fa fa-paperclip grayCircle"></i>
                  {{ trans('sol_redeterminaciones.partida_presupuestaria')}}</a>
            </div>
          @endif
        @endif
<!-- FIN Paso 4 AsignacionPartidaPresupuestaria -->

<!-- Paso 5 ProyectoActaRDP -->
        @if($solicitud->acta != null)
          <div class="row">
            <div class="col-sm-12 col-md-4 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.acta')</label>
              <span class="form-control">
                <i class="fa fa-paperclip grayCircle"></i>
                <a href="{{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' =>'acta'])}}"
                id="acta_rdp">@trans('sol_redeterminaciones.acta')</a>
              </span>
            </div>

            @if($solicitud->resolucion != null)
              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.resolucion')</label>
                <span class="form-control">
                  <i class="fa fa-paperclip grayCircle"></i>
                  <a href="{{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' =>'resolucion'])}}"
                  id="resolucion_rdp">@trans('sol_redeterminaciones.resolucion')</a>
                </span>
              </div>
            @endif

            @if($solicitud->informe != null)
              <div class="col-sm-12 col-md-4 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.informe')</label>
                <span class="form-control">
                  <i class="fa fa-paperclip grayCircle"></i>
                  <a href="{{route('solicitudes.descargarActa', ['id'=> $solicitud->id, 'tipo' =>'informe'])}}"
                  id="informe_rdp">@trans('sol_redeterminaciones.informe')</a>
                </span>
              </div>
            @endif
          </div>
        @endif
<!-- FIN  Paso 5 ProyectoActaRDP -->

<!-- Paso 6 FirmaResolucion -->
        @if($solicitud->nro_resolucion != null)
          <div class="row">
            <div class="col-sm-12 col-md-12 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.nro_resolucion')</label>
              <span class="form-control">
                {{ $solicitud->nro_resolucion }}
              </span>
            </div>

            @if($solicitud->acta_firmada != null)
              <div class="col-sm-12 col-md-6 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.acta') @trans('sol_redeterminaciones.firmada')</label>
                <span class="form-control">
                  <i class="fa fa-paperclip grayCircle"></i>
                  <a href="{{route('descargar', ['nombre' => $solicitud->fileNombre('acta_firmada'), 'url' => $solicitud->fileLink('acta_firmada')])}}"
                    id="acta_firmada">
                    {{ $solicitud->fileNombre('acta_firmada')}}</a>
                </span>
              </div>
            @endif

            @if($solicitud->resolucion_firmada != null)
              <div class="col-sm-12 col-md-6 form-group">
                <label class="m-0">@trans('sol_redeterminaciones.resolucion') @trans('sol_redeterminaciones.firmada')</label>
                <span class="form-control">
                  <i class="fa fa-paperclip grayCircle"></i>
                  <a href="{{route('descargar', ['nombre' => $solicitud->fileNombre('resolucion_firmada'), 'url' => $solicitud->fileLink('resolucion_firmada')])}}"
                    id="resolucion_firmada">
                    {{ $solicitud->fileNombre('resolucion_firmada')}}</a>
                </span>
              </div>
            @endif
          </div>
        @endif
<!-- FIN Paso 6 FirmaResolucion -->

<!-- EmisionCertificadoRDP -->
        @if($solicitud->certificados_emitidos !== null)
          <div class="row">
            <div class="col-md-12 col-sm-12 form-group">
              <label class="m-0">@trans('sol_redeterminaciones.certificados') @trans('certificado.redeterminados')</label>
              <span class="form-control">
                <a>
                  @if($solicitud->certificados_emitidos)
                    @foreach($solicitud->redeterminacion->certificados as $keyCert => $valueCert)
                      <div class="contenido_proceso_hist_redeterminaciones">
                        <a class="contenido_proc_item" href="{{route('redeterminaciones.certificado.ver', ['id' => $valueCert->id]) }}">
                         @trans('index.mes') {{$valueCert->mes}} - {{$valueCert->mesAnio('fecha', 'Y-m-d')}}
                         <span class="float-right label label-default" style="background-color:#{{$valueCert->estado['color']}}">
                           {{ucfirst($valueCert->estado['nombre_trans'])}}
                         </span>
                        </a>
                      </div>
                    @endforeach
                  @else
                    <i class="fa fa-times-circle text-danger" aria-hidden="true"></i>
                    <label>
                      @trans('sol_redeterminaciones.sin_certificados')
                    </label>
                  @endif
                </a>
              </span>
            </div>
          </div>
        @endif
<!-- FIN EmisionCertificadoRDP -->
@endif

@if($solicitud->certificados_aprobados !== null || $solicitud->monto_vigente != null)
      </div>
    </div>
  </div>
@endif
