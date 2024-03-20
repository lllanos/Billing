@if(sizeof($instancias) > 0)
  <div class="col-md-12 col-sm-12">
    <ul class="ul--general">
      <div class="">
        <ul class="estado">
          <li class="estadoItem">
            <div class="estadoStaticData">
              <span class="estadoCirculoHideTopLine"></span>
              <div class="estadoCirculoInicial"></div>
            </div>
          </li>

          @foreach($instancias as $keyInstancia => $valueInstancia)
            <li class="estadoItem" id="{{$valueInstancia->id}}">
              <div class="estadoStaticData">
                <span class="estadoCirculo" style="background-color:#{{$valueInstancia->estado_nombre_color['color']}}; border-color:#{{$valueInstancia->estado_nombre_color['color']}});">
                  {{ $keyInstancia + 1 }}
                </span>
                <h3 class="estadoNombreEtapa">
                  @trans($seccion . '.estados.nombre.' . $valueInstancia->estado_nombre_color['nombre'])
                </h3>
                <div class="etapaEvaluacionContent">
                  <div class="hist_instancia_icon">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                    <label>{{ $valueInstancia->created_at}}</label>
                  </div>
                  @if($valueInstancia->user_creator != null)
                    <div class="hist_instancia_icon">
                      <i class="fa fa-user-circle" aria-hidden="true" title="@trans('index.user')"></i>
                      <label>
                        {{ $valueInstancia->user_creator->nombre_apellido }}
                      </label>
                    </div>
                  @endif

                  @if($valueInstancia->gde != null)
                    <div class="contenido_proceso_hist_redeterminaciones">
                  		<span class="contenido_proc_item">@trans('index.gde'): {{$valueInstancia->gde}}</span>
                  	</div>
                  @endif
                  {{ $valueInstancia->observaciones }}
                </div>
              </div>
            </li>
          @endforeach
        </ul>
        <div class="circleBottomEmpty"></div>
      </div>
    </ul>
  </div>
@else
  <div class="no-data-no-padding text-center">
      @trans('index.sin_instancias')
  </div>
@endif
