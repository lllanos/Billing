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
          <li class="estadoItem">
            <div class="estadoStaticData">
              <span class="estadoCirculo" style="background-color:#{{$valueInstancia->estado_nombre_color['color']}};border-color:#{{$valueInstancia->estado_nombre_color['color']}};">
                {{ $keyInstancia + 1 }}
              </span>
              <h3 class="estadoNombreEtapa disabled">
                {{$valueInstancia->estado_nombre_color['nombre']}}
              </h3>
              <div class="etapaEvaluacionContent">
                @include('contratos.solicitudes.show.historialContent')
              </div>
            </div>
          </li>
          @endforeach
        </ul>
        <div class="circleBottomEmpty"></div>
      </div>
    </ul>
  </div>
</div>
