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

          @foreach($redeterminacion->instancias as $keyInstancia => $valueInstancia)
            @if(!($valueInstancia->tipo_instancia->modelo == 'SolicitudRDP' && $valueInstancia->instancia->borrador))
              @if($valueInstancia->tipo_instancia->modelo == 'ActoAdministrativo')
                <li class="estadoItem" id="separacion">
                  <div></div>
                </li>
              @endif
              <li class="estadoItem" id="{{$valueInstancia->id}}">
                <div class="estadoStaticData">
                  @if($redeterminacion->modelo_actual == $valueInstancia->tipo_instancia->modelo && $redeterminacion->en_curso
                      && !$valueInstancia->historica)
                    <span class="estadoCirculo" style="background-color:#{{$valueInstancia->tipo_instancia->color}};">
                  @else
                    <span class="estadoCirculo" style="background-color:#{{$valueInstancia->tipo_instancia->color}}; border-color:#{{$valueInstancia->tipo_instancia->color}};">
                  @endif
                  @if($valueInstancia->correccion)
                    <span class="glyphicon glyphicon-pencil"></span>
                  @elseif($valueInstancia->tipo_instancia->modelo == 'Anulada')
                    <span class="glyphicon glyphicon-ban-circle"></span>
                  @elseif($valueInstancia->tipo_instancia->modelo == 'Suspendida')
                    <span class="glyphicon glyphicon-off"></span>
                  @elseif($valueInstancia->tipo_instancia->modelo == 'Continuada')
                    <span class="glyphicon glyphicon-refresh"></span>
                  @elseif($valueInstancia->tipo_instancia->modelo == 'CargaPolizaCaucion' || $valueInstancia->tipo_instancia->modelo == 'ValidacionPolizaCaucion')
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                  @elseif($valueInstancia->tipo_instancia->modelo == 'EmisionCertificadoRDP' && $redeterminacion->contrato->normativa->banco)
                    4
                  @elseif($valueInstancia->tipo_instancia->modelo == 'SolicitudRDP')
                    <i class="fa fa-file" aria-hidden="true"></i>
                  @else
                    {{ $valueInstancia->tipo_instancia->orden }}
                  @endif
                  </span>
                  <h3 class="estadoNombreEtapa
                             @if($valueInstancia->tipo_instancia->orden > $redeterminacion->orden_tipo_instancia_editable) disabled @endif">
                    @if($redeterminacion->modelo_actual == $valueInstancia->tipo_instancia->modelo && $redeterminacion->en_curso
                        && !$valueInstancia->historica)
                      <div class="esperando_hist_redeterminacion">
                        <label class="label label-default">{{trans('index.esperando')}}</label>
                      </div>
                    @endif
                    {{ trans('redeterminaciones.instancias.' . $valueInstancia->tipo_instancia->modelo )}}
                  </h3>
                  <div class="etapaEvaluacionContent">
                    @include('redeterminaciones.show.historial.' . $valueInstancia->tipo_instancia->modelo)
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
