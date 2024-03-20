<!-- Header -->
<div class="row">
  <div class="col-md-12 ">
    <div class="estados_contratos">
      <div class="container_badges_referencias badges_refencias_responsive_flex">
        @if(!$contrato->isAdendaAmpliacion)
          @if($contrato->isAdenda && !$contrato->isAdendaAmpliacion)
              <span class="badge badge-referencias" style="background-color:var(--medium-gray-color);">
               @trans('contratos.tipo_contrato.adenda_certificacion')
              </span>
          @endif
          <span class="badge badge-referencias" style="background-color:#{{ $contrato->estado_nombre_color['color'] }};">
            {{ $contrato->estado_nombre_color['nombre'] }}
          </span>
          <span class="badge badge-referencias" style="background-color:#{{ $contrato->causante_nombre_color['color'] }};">
            {{ $contrato->causante_nombre_color['nombre'] }}
          </span>
        @else
        <span class="badge badge-referencias" style="background-color:var(--medium-gray-color);">
          @trans('contratos.tipo_contrato.adenda_ampliacion')
        </span>
        <span class="badge badge-referencias" style="background-color:#{{ $contrato->contrato_padre->causante_nombre_color['color'] }};">
          {{ $contrato->contrato_padre->causante_nombre_color['nombre'] }}
        </span>
        @endif

        @if($contrato->borrador)
          <span class="badge badge-referencias badge-borrador">
            <i class="fa fa-eraser"></i>
            @trans('index.borrador')
          </span>
        @endif

        @if($contrato->doble_firma)
          <span class="badge badge-referencias badge-borrador">
          <i class="fa fa-pencil"></i>
          @if(empty($contrato->firma_ar) && empty($contrato->firma_py))
            @trans('index.pendiente_firmas')
          @else
            @trans('index.pendiente_firma')
          @endif
        </span>
        @endif

        @if($contrato->incompleto_show['status'])
          <span class="badge badge-referencias badge-borrador" data-toggle="tooltip" data-placement="bottom" data-html="true" title="{{$contrato->incompleto_show['mensaje']}}">
            <i class="fa fa-star-half-empty"></i>
            @trans('contratos.incompleto')
          </span>
        @endif

        @if($contrato->no_redetermina)
          <span class="badge badge-referencias" style="background-color:var(--red-redeterminacion-color);">
            @trans('contratos.no_redetermina')
          </span>
        @endif

        @if($contrato->empalme)
          <span class="badge badge-referencias" style="background-color:var(--green-redeterminacion-color);">
            @trans('contratos.contrato_empalme')
          </span>
        @endif
      </div>
    </div>
  </div>
</div>
<!-- FIN Header -->

<!-- Panel Detalle Contrato -->
<div class="panel-group acordion" id="accordion_detalle_contrato" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default panel-view-data border-top-poncho">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne_detalle_contrato">
      <h4 class="panel-title titulo_collapse m-0">
        <a class="btn_acordion collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_detalle_contrato" href="#collapseOne_detalle_contrato" aria-expanded="true" aria-controls="collapseOne_detalle_contrato">
          <i class="fa fa-angle-down"></i> {{$contrato->nombre_completo}}
        </a>
      </h4>
    </div>
    <div id="collapseOne_detalle_contrato" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_detalle_contrato">
      <div class="panel-body">

        @if($contrato->is_adenda)
          <div class="col-md-12 form-group">
            <label class="m-0">@trans('contratos.contrato_madre')</label>
            <span class="form-control">
              <a href="{{route('contratos.ver', ['id' => $contrato->contrato_padre->id]) }}">
                {{$contrato->contrato_padre->nombre_completo}}
              </a>
            </span>
          </div>
        @endif

        @if(!$contrato->isAdendaAmpliacion)
          <div class="col-sm-12 col-md-9 form-group">
            <label class="m-0">@trans('forms.contratista')</label>
            <span class="form-control">
              @if($contrato->contratista_id != null)
                <a href="{{route('contratistas.show', ['id' => $contrato->contratista->id]) }}">
                  {{$contrato->contratista_nombre_documento}}
                </a>
                @if($contrato->contratista->ute)
                  <div class="container_badges_referencias badges_refencias_responsive_flex">
                    <span class="badge badge-referencias" style="background-color:(--light-gray-colorvar);">
                      @trans('index.ute')
                    </span>
                </div>
                @endif
              @else
                <a>
                  @trans('contratos.sin.contratista')
                </a>
              @endif
            </span>
          </div>
          <div class="col-sm-12 col-md-3 form-group">
            <label class="m-0">@trans('contratos.organo_aprobador')</label>
            <span class="form-control item_detalle">
              {{$contrato->organo_aprobador_descripcion}}
            </span>
          </div>


        @ifcount($contrato->representante_eby)
          <div class="col-sm-4 col-md-4 form-group">
            <label class="m-0">@trans('contratos.representante_legal') @trans('forms.contratista')</label>
            <span class="form-control item_detalle">
              {{$contrato->repre_leg_contratista}}
            </span>
          </div>

          <div class="col-sm-4 col-md-4 form-group">
            <label class="m-0">@trans('contratos.representante_tecnico') @trans('forms.contratista')</label>
            <span class="form-control item_detalle">
              {{$contrato->repre_tec_contratista}}
            </span>
          </div>

          <div class="col-sm-4 col-md-4 form-group">
            <label class="m-0">@trans('contratos.representante_tecnico_eby')</label>
            <span class="form-control item_detalle">
              @foreach($contrato->representantes_tecnicos_eby as $representante)
               {{$representante->nombre}} {{$representante->apellido}}<br>
              @endforeach
            </span>
          </div>
        @else
          <div class="col-sm-6 col-md-6 form-group">
            <label class="m-0">@trans('contratos.representante_legal') @trans('forms.contratista')</label>
            <span class="form-control item_detalle">
              {{$contrato->repre_leg_contratista}}
            </span>
          </div>

          <div class="col-sm-6 col-md-6 form-group">
            <label class="m-0">@trans('contratos.representante_tecnico') @trans('forms.contratista')</label>
            <span class="form-control item_detalle">
              {{$contrato->repre_tec_contratista}}
            </span>
          </div>
        @endif
      @endif

      @if($contrato->plazo_vigente != null)
        <div class="col-sm-12 col-md-3 form-group">
          <label class="m-0">@trans('contratos.resoluc_adjudic')</label>
          <span class="form-control item_detalle">
            {{$contrato->resoluc_adjudic}}
          </span>
        </div>

        <div class="col-sm-12 col-md-3 form-group">
          <label class="m-0">@trans('contratos.plazo_obra')</label>
          <span class="form-control item_detalle">
            {{$contrato->plazo_completo}}
          </span>
        </div>

        <div class="col-sm-12 col-md-3 form-group">
          <label class="m-0">@trans('contratos.plazo_vigente_obra')</label>
          <span class="form-control item_detalle">
            {{$contrato->plazo_vigente_completo}}
          </span>
        </div>

        <div class="col-sm-12 col-md-3 form-group">
          <label class="m-0">@trans('contratos.garantia')</label>
          <span class="form-control item_detalle">
            @if($contrato->has_requiere_garantia)
              @if($contrato->has_garantia_validada)
                @trans('index.valida')
              @else
                @trans('index.no') @trans('index.valida')
              @endif
            @else
              @trans('index.no_requiere')
            @endif
          </span>
        </div>
      @else
        <div class="col-sm-12 col-md-4 form-group">
          <label class="m-0">@trans('contratos.resoluc_adjudic')</label>
          <span class="form-control item_detalle">
            {{$contrato->resoluc_adjudic}}
          </span>
        </div>

        <div class="col-sm-12 col-md-4 form-group">
          <label class="m-0">@trans('contratos.plazo_obra')</label>
          <span class="form-control item_detalle">
            {{$contrato->plazo_completo}}
          </span>
        </div>

        <div class="col-sm-12 col-md-4 form-group">
          <label class="m-0">@trans('contratos.garantia')</label>
          <span class="form-control item_detalle">
            @if($contrato->has_requiere_garantia)
              @if($contrato->has_garantia_validada)
                @trans('index.valida')
              @else
                @trans('index.no') @trans('index.valida')
              @endif
            @else
              @trans('index.no_requiere')
            @endif
          </span>
        </div>
      @endif
        @if($contrato->is_adenda)
          @if($contrato->fecha_aprobacion != null)
            <div class="col-sm-12 col-md-4 form-group">
              <label class="m-0"><i class="fa fa-calendar"></i> @trans('contratos.fecha_aprobacion')</label>
              <span class="form-control item_detalle">
                {{$contrato->fecha_aprobacion}}
              </span>
            </div>
          @endif
        @else
          <div class="col-sm-12 col-md-4 form-group">
            <label class="m-0"><i class="fa fa-calendar"></i> @trans('contratos.fecha_oferta')</label>
            <span class="form-control item_detalle">
              {{$contrato->fecha_oferta}}
            </span>
          </div>
        @endif
        <div class="col-sm-12 col-md-4 form-group">
          <label class="m-0"><i class="fa fa-calendar"></i> @trans('contratos.fecha_acta_inicio')</label>
          <span class="form-control item_detalle">
            {{$contrato->fecha_acta_inicio}}
          </span>
        </div>
        <div class="col-sm-12 col-md-4 form-group">
          <label class="m-0"><i class="fa fa-calendar"></i> @trans('contratos.fecha_fin_contrato')</label>
          <span class="form-control item_detalle">
            {{$contrato->fecha_fin_de_contrato}}
          </span>
        </div>

        @if(count($contrato->montos_saldos) > 0)
          @foreach($contrato->montos_saldos as $keyMonto => $valueMonto)
            <div class="col-sm-12 col-md-3 form-group">
              @if($contrato->is_adenda)
              <label class="m-0">@trans('adendas.monto_total_basico') {{$valueMonto['moneda']}}</label>
              @else
              <label class="m-0">@trans('contratos.monto_inicial') {{$valueMonto['moneda']}}</label>
              @endif
              <span class="form-control item_detalle">
                {{ $valueMonto['monto_inicial'] }}
              </span>
            </div>

            <div class="col-sm-12 col-md-3 form-group">
              <label class="m-0">@trans('contratos.monto_vigente') {{$valueMonto['moneda']}}</label>
              <span class="form-control item_detalle">
                {{$valueMonto['monto_vigente']}}
              </span>
            </div>

            <div class="col-sm-12 col-md-3 form-group">
              <label class="m-0">@trans('contratos.saldo') {{$valueMonto['moneda']}}</label>
              <span class="form-control item_detalle">
                {{$valueMonto['saldo_y_saldo_porcentaje']}}
              </span>
            </div>
            @if($valueMonto['moneda'] != 'USD')
              <div class="col-sm-12 col-md-3 form-group">
                <label class="m-0">@trans('contratos.tasa_cambio') {{$valueMonto['moneda']}}</label>
                <span class="form-control item_detalle">
                  @toDosDec($valueMonto['tasa_cambio'])
                </span>
              </div>
            @endif
          @endforeach
        @endif

        @if($contrato->is_contrato)
          <div class="col-sm-12 col-md-6 form-group">
            <label class="m-0"> @trans('contratos.anticipo')</label>
            <span class="form-control item_detalle">
              @toDosDec($contrato->anticipo) %
            </span>
          </div>

          <div class="col-sm-12 col-md-6 form-group">
            <label class="m-0"> @trans('contratos.fondo_reparo')</label>
            <span class="form-control item_detalle">
              @toDosDec($contrato->fondo_reparo) %
            </span>
          </div>
        @endif
      </div>

      @if($contrato->adjuntos != null)
        @foreach($contrato->adjuntos as $key => $adjunto)
          <div class="pb-1">
          <span id="adjunto_anterior_{{$key}}" class="hide-on-ajax ml-35">
            <i class="fa fa-paperclip grayCircle"></i>
            <a download="{{$adjunto->adjunto_nombre}}" href="{{$adjunto->adjunto_link}}" id="file_item" target="_blank">{{$adjunto->adjunto_nombre}}</a>
          </span>
          </div>
        @endforeach
      @endif

    </div>
  </div>
</div>
<!--Fin Panel Detalle Contrato-->

@if($contrato->hasSeccion('poderes'))
    @permissions(('poderes-view'))
    <!--Panel Poderes-->
      <div class="panel-group acordion" id="accordion_poderes" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne_poderes">
            <h4 class="panel-title titulo_collapse m-0">
              <a class="collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion_poderes" href="#collapseOne_poderes" aria-expanded="true" aria-controls="collapseOne_poderes">
                <i class="fa fa-angle-down"></i> @trans('forms.poderes')
              </a>
            </h4>
          </div>
          <div id="collapseOne_poderes" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_poderes">
            <div class="panel-body p-0">
              <div class="poder_container">
                <div class="row">
                  <div class="col-md-12">
                    <ul class="list_poderes">
                      @foreach($contrato->users_contratos_vigentes as $keyuserContrato => $valueUserContrato)
                        <li class="list__poder__item">
                          <div class="col-md-6">
                            <span>@trans('forms.nombre_apellido')</span>
                            <h4>{{ $valueUserContrato->user_publico->nombre_apellido }}</h4>
                          </div>
                          <div class="col-md-3">
                            <span>@trans('forms.documento')</span>
                            <h4>{{ $valueUserContrato->user_publico->tipo_num_documento }}</h4>
                          </div>
                          <div class="col-md-3">
                            <div class="vigencia">
                              @if(isset($valueUserContrato->poder->fecha_fin_poder))
                                <span>@trans('forms.vigencia_poder')</span>
                                <h4>{{ $valueUserContrato->poder->fecha_fin_poder}}</h4>
                              @else
                                <span>@trans('forms.vigencia_poder')</span>
                                <h4>@trans('forms.sin_fecha_vigencia')</h4>
                              @endif
                            </div>
                            @if(isset($valueUserContrato->poder->fecha_fin_poder))
                              <div class="circulo_descarga_icon_back btn-primary" data-toggle="tooltip"  title="@trans('index.descargar_poder')" data-placement="bottom">
                               <a
                                 download="{{$valueUserContrato->poder->adjunto_nombre}}" href="{{$valueUserContrato->poder->adjunto_link}}"
                                 id="file_item" target="_blank">
                                 <i class="fa fa-paperclip" aria-hidden="true"></i>
                               </a>
                              </div>
                            
                            @endif
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Fin Panel Poderes-->
    @endpermission
@endif
