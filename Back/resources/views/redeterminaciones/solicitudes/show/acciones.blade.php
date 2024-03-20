@if($solicitud->en_curso)
  <div class="button_desktop">
    <!-- Accion Actual -->

    @permissions(($solicitud->modelo_actual . '-gestionar'))
      @if($solicitud->getInstancia($solicitud->modelo_actual)->instancia->puede_realizarse)
        <a class="btn btn-success open-modal-redeterminacion pull-left" href="javascript:void(0);" id="btn_gestionar"
        data-instancia="{{$solicitud->modelo_actual}}" data-id="{{$solicitud->id}}">
          @trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)
        </a>
      @else
        <a class="btn btn-success btn-observaciones pull-left" href="javascript:void(0);" id="btn_gestionar"
        data-aceptar="@trans('index.aceptar')" data-title="@trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)"
        data-observaciones="{{$solicitud->getInstancia($solicitud->modelo_actual)->instancia->puede_realizarse_error}}">
          @trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)
        </a>
      @endif
    @endpermission

    <!-- Otras acciones -->
    {{-- <div class="dropdown dd-on-table pull-left">
      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')" id="dd_acciones">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu pull-right multi-level" role="menu" aria-labelledby="dropdownMenu"> --}}
        {{-- @permissions(('CalculoPreciosRedeterminados-corregir', 'AsignacionPartidaPresupuestaria-corregir', 'ProyectoActaRDP-corregir',
                     'FirmaContratista-corregir', 'EmisionDictamenJuridico-corregir', 'ActoAdministrativo-corregir',
                     'EmisionCertificadoRDP-corregir', 'CargaPolizaCaucion-corregir'))
          @if(count($solicitud->tipos_instancias_realizadas) != 1 || $solicitud->puede_corregir_solicitud)
          <li class="dropdown-submenu">
            <a class="levelToggle" tabindex="-1" href="#">
              @trans('sol_redeterminaciones.corregir.title')
            </a>
            <ul class="dropdown-menu pull-right">
              @foreach($solicitud->tipos_instancias_realizadas as $keyInstancia => $modelo)
                @if($modelo != 'SolicitudRDP')
                  @permissions(($modelo . '-corregir'))
                    <li>
                      <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_corregir_{{$modelo}}"
                        data-instancia="{{$modelo}}" data-id="{{$solicitud->id}}" data-correccion="true">
                        @trans('sol_redeterminaciones.corregir.' . $modelo )
                      </a>
                    </li>
                  @endpermission
                @endif
              @endforeach

              @permissions(('SolicitudRDP-corregir'))
              @if($solicitud->puede_corregir_solicitud)
                <li>
                  <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_corregir_SolicitudRDP"
                    data-instancia="SolicitudRDP" data-id="{{$solicitud->id}}" data-correccion="true">
                    @trans('sol_redeterminaciones.corregir.SolicitudRDP')
                  </a>
                </li>
                @endif
              @endpermission
            </ul>
          </li>
          @endif
        @endpermission --}}

        {{-- @permissions(('SolicitudRDP-gestionar'))
          @if($solicitud->puede_crear_solicitud)
          <li>
            <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_gestion_SolicitudRDP"
              data-instancia="SolicitudRDP" data-id="{{$solicitud->id}}">
              @trans('sol_redeterminaciones.acciones.SolicitudRDP')
            </a>
          </li>
          @endif
        @endpermission --}}

        {{-- @permissions(('redeterminaciones-rechazar'))
          <li>
            <a class="btn-confirmable-motivos" href="javascript:void(0);" id="btn_rechazar"
             data-body="@trans('sol_redeterminaciones.confirmar.rechazar')"
             data-action="{{ route('solicitudes.rechazar', ['id' => $solicitud->id]) }}"
             data-title="@trans('sol_redeterminaciones.acciones.Anulada') @trans('index.solicitud')">
              @trans('sol_redeterminaciones.acciones.Anulada')
            </a>
          </li>
        @endpermission

        @permissions(('redeterminaciones-suspender'))
          @if(!$solicitud->suspendida)
            <li>
              <a class=" btn-confirmable-motivos" href="javascript:void(0);" id="btn_suspender"
               data-body="@trans('sol_redeterminaciones.confirmar.suspender')"
               data-action="{{ route('solicitudes.suspender', ['id' => $solicitud->id]) }}"
               data-title="@trans('sol_redeterminaciones.acciones.Suspendida') @trans('index.solicitud')">
                @trans('sol_redeterminaciones.acciones.Suspendida')
              </a>
            </li>
          @endif
        @endpermission --}}
      {{-- </ul>
    </div> --}}
  </div>

  @permissions(($solicitud->modelo_actual . '-gestionar'))
    <div class="button_responsive">
      <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
          <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu pull-right">
          <li>
            @if($solicitud->getInstancia($solicitud->modelo_actual)->instancia->puede_realizarse)
            <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_gestion_rsp"
              data-instancia="{{$solicitud->modelo_actual}}" data-id="{{$solicitud->id}}">
              @trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)
            </a>
          @else
            <a class="btn-observaciones pull-left" href="javascript:void(0);" id="btn_gestionar"
              data-aceptar="@trans('index.aceptar')" data-title="@trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)"
              data-observaciones="{{$solicitud->getInstancia($solicitud->modelo_actual)->instancia->puede_realizarse_error}}">
              @trans('sol_redeterminaciones.acciones.' . $solicitud->modelo_actual)
            </a>
          @endif
          </li>
          {{-- @permissions(('CalculoPreciosRedeterminados-corregir', 'AsignacionPartidaPresupuestaria-corregir', 'ProyectoActaRDP-corregir',
                       'FirmaContratista-corregir', 'EmisionDictamenJuridico-corregir', 'ActoAdministrativo-corregir',
                       'EmisionCertificadoRDP-corregir', 'CargaPolizaCaucion-corregir'))
             @if(count($solicitud->tipos_instancias_realizadas) != 1 || $solicitud->puede_corregir_solicitud)
              <li class="dropdown-submenu" id="submenu_responsive">
                <a class="levelToggle" tabindex="-1" href="#">
                  <i class="fa fa-angle-down only_responsive"></i>  @trans('sol_redeterminaciones.corregir.title')
                </a>
                <ul class="dropdown-menu pull-right submenu_responsive__ul hidden">
                  @foreach($solicitud->tipos_instancias_realizadas as $keyInstancia => $modelo)
                    @if($modelo != 'SolicitudRDP')
                      @permissions(($modelo . '-corregir'))
                        <li>
                          <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_corregir_{{$modelo}}"
                            data-instancia="{{$modelo}}" data-id="{{$solicitud->id}}" data-correccion="true">
                            @trans('sol_redeterminaciones.corregir.' . $modelo )
                          </a>
                        </li>
                      @endpermission
                    @endif
                  @endforeach

                  @permissions(('SolicitudRDP-corregir'))
                  @if($solicitud->puede_corregir_solicitud)
                    <li>
                      <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_corregir_SolicitudRDP"
                        data-instancia="SolicitudRDP" data-id="{{$solicitud->id}}" data-correccion="true">
                        @trans('sol_redeterminaciones.corregir.SolicitudRDP')
                      </a>
                    </li>
                    @endif
                  @endpermission
                </ul>
              </li>
             @endif
          @endpermission --}}

          {{-- @permissions(('SolicitudRDP-gestionar'))
            @if($solicitud->puede_crear_solicitud)
              <li>
                <a class="open-modal-redeterminacion" href="javascript:void(0);" id="btn_gestion_SolicitudRDP"
                  data-instancia="SolicitudRDP" data-id="{{$solicitud->id}}">
                  @trans('sol_redeterminaciones.acciones.SolicitudRDP')
                </a>
              </li>
            @endif
          @endpermission

          @permissions(('redeterminaciones-rechazar'))
            <li>
              <a class="btn-confirmable-motivos" href="javascript:void(0);" id="btn_rechazar"
               data-body="@trans('sol_redeterminaciones.confirmar.rechazar')"
               data-action="{{ route('solicitudes.rechazar', ['id' => $solicitud->id]) }}"
               data-title="@trans('sol_redeterminaciones.acciones.Anulada') @trans('index.solicitud')">
                @trans('sol_redeterminaciones.acciones.Anulada')
              </a>
            </li>
          @endpermission

          @permissions(('redeterminaciones-suspender'))
            @if(!$solicitud->suspendida)
              <li>
                <a class=" btn-confirmable-motivos" href="javascript:void(0);" id="btn_suspender"
                 data-body="trans('sol_redeterminaciones.confirmar.suspender')"
                 data-action="{{ route('solicitudes.suspender', ['id' => $solicitud->id]) }}"
                 data-title="@trans('sol_redeterminaciones.acciones.Suspendida') @trans('index.solicitud')">
                  @trans('sol_redeterminaciones.acciones.Suspendida')
                </a>
              </li>
            @endif
          @endpermission --}}
        </ul>
      </div>
    </div>
  @endpermission

{{-- @elseif($solicitud->suspendida && $solicitud->puede_continuarse)
  <div class="button_desktop">
    @permissions(('redeterminaciones-continuar'))
      @if($solicitud->suspendida)
        <a class="btn btn-success btn-confirmable-motivos" href="javascript:void(0);" id="btn_continuar"
            data-body="@trans('sol_redeterminaciones.confirmar.continuar')"
            data-action="{{ route('solicitudes.continuar', ['id' => $solicitud->id]) }}"
            data-title="@trans('sol_redeterminaciones.acciones.Continuada') @trans('index.solicitud')">
              @trans('sol_redeterminaciones.acciones.Continuada')
        </a>
      @endif
    @endpermission
  </div>

  <div class="button_responsive">
    <div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="">
      <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
        <i class="fa fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu pull-right">
        <li>
          <a class="btn-confirmable-motivos" href="javascript:void(0);" id="btn_continuar"
              data-body="@trans('sol_redeterminaciones.confirmar.continuar')"
              data-action="{{ route('solicitudes.continuar', ['id' => $solicitud->id]) }}"
              data-si="@trans('index.si')" data-no="@trans('index.no')">
                @trans('sol_redeterminaciones.acciones.Continuada')
          </a>
        </li>
      </ul>
    </div>
  </div> --}}

@endif
