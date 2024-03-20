<div class="panel-group acordion" id="accordion-redet-{{$valueContratoMoneda->id}}" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading-redet-{{$valueContratoMoneda->id}}">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow " role="button" data-toggle="collapse" data-parent="#accordion-redet-{{$valueContratoMoneda->id}}" href="#collapse_redet_{{$valueContratoMoneda->id}}" aria-expanded="true" aria-controls="collapse_redet_{{$valueContratoMoneda->id}}">
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> {{$valueContratoMoneda->moneda->nombre_simbolo}}</div>
        </a>
        @if($valueContratoMoneda->fecha_ultima_redeterminacion != null)
          <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion-redet-{{$valueContratoMoneda->id}}" href="#collapse_redet_{{$valueContratoMoneda->id}}" aria-expanded="true" aria-controls="collapse_redet_{{$valueContratoMoneda->id}}">
            <div class="container_icon_angle">
            </div>
            <div class="fecha_obra_ultima_redeterminacion_collapse pull-right">
              @trans('contratos.fecha_ultima_redeterminacion'): {{$valueContratoMoneda->fecha_ultima_redeterminacion_my}}
            </div>
          </a>
        @endif
      </h4>
    </div>

    <div id="collapse_redet_{{$valueContratoMoneda->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-redet-{{$valueContratoMoneda->id}}">
      @permissions(('salto-list'))
        @if(sizeof($valueContratoMoneda->saltos) > 0 || (isset($valueContratoMoneda->ultima_variacion) && $valueContratoMoneda->ultima_variacion != null))
          @include('contratos.contratos.show.polinomica.saltos')
        @endif
      @endpermission

      @if($valueContratoMoneda->polinomica != null && !$valueContratoMoneda->polinomica->borrador)
        @include('contratos.contratos.show.polinomica.show')
      @else
        @cant(('polinomica-edit'))
          @if($valueContratoMoneda->polinomica == null || count($valueContratoMoneda->polinomica->composiciones) == 0)
            <div class="col-md-12 col-sm-12">
              <div class="row">
                <h1 class="text-center">@trans('contratos.sin.polinomica')</h1>
              </div>
            </div>
          @else
            @include('contratos.contratos.show.polinomica.show')
          @endif
        @endcant

        @permissions(('polinomica-edit'))
          @if($valueContratoMoneda->polinomica != null)
            @include('contratos.contratos.show.polinomica.edit')
          @else
            @include('contratos.contratos.show.polinomica.show')
          @endif
        @endpermission
      @endif
    </div>
  </div>
</div>
