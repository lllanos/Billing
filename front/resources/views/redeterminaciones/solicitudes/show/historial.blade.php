<div class="panel panel-default panel-view-data border-top-poncho">
  <div class="col-md-12 col-sm-12 seccion">
    <ul class="ul--general">
      <div class="">
        <ul class="estado">
          <li class="estadoItem">
            <div class="estadoStaticData">
              <span class="estadoCirculoHideTopLine"></span>
              <div class="estadoCirculoInicial"></div>
            </div>
          </li>
          @foreach($solicitud->instancias as $keyInstancia => $valueInstancia)
            @if((!($valueInstancia->tipo_instancia->modelo == 'SolicitudRDP' && $valueInstancia->instancia->borrador))
                && !($valueInstancia->tipo_instancia->modelo == 'AprobacionCertificados' && ($solicitud->modelo_actual != $valueInstancia->tipo_instancia->modelo && $solicitud->modelo_actual != 'Iniciada' && $valueInstancia->instancia->certificado == null)
              ))
                <li class="estadoItem" id="{{$valueInstancia->id}}">
                  <div class="estadoStaticData">
                    @if($solicitud->modelo_actual == $valueInstancia->tipo_instancia->modelo
                    && $solicitud->en_curso
                    && !$valueInstancia->historica)
                      <span class="estadoCirculo" style="background-color:#{{$valueInstancia->tipo_instancia->color}};">
                    @else
                      <span class="estadoCirculo" style="background-color:#{{$valueInstancia->tipo_instancia->color}}; border-color:#{{$valueInstancia->tipo_instancia->color}};">
                    @endif
                    {{-- @if($valueInstancia->correccion)
                      <span class="glyphicon glyphicon-pencil"></span>
                    @elseif($valueInstancia->tipo_instancia->modelo == 'Anulada')
                      <span class="glyphicon glyphicon-ban-circle"></span>
                    @elseif($valueInstancia->tipo_instancia->modelo == 'Suspendida')
                      <span class="glyphicon glyphicon-off"></span>
                    @elseif($valueInstancia->tipo_instancia->modelo == 'Continuada')
                      <span class="glyphicon glyphicon-refresh"></span>
                    @elseif($valueInstancia->tipo_instancia->modelo == 'CargaPolizaCaucion' || $valueInstancia->tipo_instancia->modelo == 'ValidacionPolizaCaucion')
                      <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    @elseif($valueInstancia->tipo_instancia->modelo == 'SolicitudRDP')
                      <i class="fa fa-file" aria-hidden="true"></i>
                    @else --}}
                      {{ $valueInstancia->tipo_instancia->ordenSuborden($solicitud->moneda->lleva_analisis) }}
                    {{-- @endif --}}
                      </span>
                  <h3 class="estadoNombreEtapa
                    @if($valueInstancia->tipo_instancia->orden > $solicitud->orden_tipo_instancia_editable) disabled @endif"
                  >
                    @if($solicitud->modelo_actual == $valueInstancia->tipo_instancia->modelo
                    && $solicitud->en_curso
                    && !$valueInstancia->historica)
                      <div class="esperando_hist_redeterminacion">
                        <label class="label label-default">@trans('index.esperando')</label>
                      </div>
                    @endif
                      {{ trans('sol_redeterminaciones.instancias.' . $valueInstancia->tipo_instancia->modelo )}}
                  </h3>
                  <div class="etapaEvaluacionContent">
                    @include('redeterminaciones.solicitudes.show.historial.' . $valueInstancia->tipo_instancia->modelo)
                    @if($valueInstancia->instancia->observaciones != null && $valueInstancia->instancia->observaciones != '')
                      <div class="contenido_proceso_hist_redeterminaciones">
                        <a class="btn-observaciones mouse-pointer contenido_proc_item" data-observaciones="{{$valueInstancia->instancia->observaciones}}">
                          @trans('index.ver') @trans('index.observaciones')
                        </a>
                      </div>
                    @endif
                  </div>
                </div>
              </li>
            @endif
          @endforeach
        </ul>
        <div class="circleBottomEmpty"></div>
      </div>
    </ul>
  </div>
</div>
