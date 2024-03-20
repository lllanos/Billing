
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a href="javascript:void(0);" class="pull-left toggleMenuRsp"><i class="fa fa-bars"></i></a>
      <a class="navbar-brand" href="{{ url('/') }}" alt="{{config('app.name')}}">
        <img src="{{asset('img/main-logo-eby-arg.png')}}"/>
      </a>
    </div>
  </div>

  <div class="custom-nav ocultar">
    <ul class="nav navbar-nav navbar-right">
      @if(Auth::check())
        <li class="dropdown">
          @permissions(('sol-contrato-pendientes-list', 'sol-contrato-finalizadas-list', 'contrato-list'))
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nav_contratos">@trans('forms.contratos')
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            @permissions(('sol-contrato-pendientes-list'))
            <li class="dropdown">
              <a id="asociaciones_pendientes_list" class="dropdown-toggle" href="{{ route('solicitudes.asociaciones_pendientes') }}">
                @trans('forms.asociaciones_pendientes')
              </a>
            </li>
            @endpermission
            @permissions(('sol-contrato-finalizadas-list'))
            <li class="dropdown">
              <a id="asociaciones_finalizadas_list" class="dropdown-toggle" href="{{ route('solicitudes.asociaciones_finalizadas') }}">
                @trans('forms.asociaciones_finalizadas')
              </a>
            </li>
            @endpermission
            @permissions(('contrato-list'))
            <li role="separator" class="divider"></li>
            <li class="dropdown">
              <a id="nav_contratos_list" class="dropdown-toggle" href="{{ route('contratos.index') }}">
                @trans('forms.contratos')
              </a>
            </li>
            @endpermission
            @permissions(('nuevo-contrato-list'))
            <li class="dropdown">
              <a id="nav_contratos_list" class="dropdown-toggle" href="{{ route('contratos.bandeja.index') }}">
                @trans('forms.bandeja_contratos')
              </a>
            </li>
            @endpermission
          </ul>
          @endpermission
        </li>

        <li class="dropdown">
          @permissions(('redeterminaciones-en_proceso-list', 'redeterminaciones-finalizadas-list'))
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nav_solicitudes">@trans('forms.solicitudes')
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            @permissions(('redeterminaciones-en_proceso-list'))
            <li class="dropdown">
              <a id="redeterminaciones_en_proceso_list" class="dropdown-toggle" href="{{ route('solicitudes.redeterminaciones_en_proceso') }}">
                @trans('forms.sol_redeterminaciones_en_proceso')
              </a>
            </li>
            @endpermission
            @permissions(('redeterminaciones-finalizadas-list'))
            <li class="dropdown">
              <a id="redeterminaciones_aprobadas_list" class="dropdown-toggle" href="{{ route('solicitudes.redeterminaciones_finalizadas') }}">
                @trans('forms.sol_redeterminaciones_finalizadas')
              </a>
            </li>
            @endpermission
            <li role="separator" class="divider"></li>
            @permissions(('certificado-en_proceso-list'))
            <li class="dropdown">
              <a id="certificados-en_proceso-list" class="dropdown-toggle" href="{{ route('solicitudes.certificado_en_proceso') }}">
                @trans('certificado.sol_certificaciones_en_proceso')
              </a>
            </li>
            @endpermission
            @permissions(('certificado-finalizadas-list'))
            <li class="dropdown">
              <a id="certificados-finalizadas-list" class="dropdown-toggle" href="{{ route('solicitudes.certificado_finalizadas') }}">
                @trans('certificado.sol_certificaciones_finalizadas')
              </a>
            </li>
            @endpermission
          </ul>
          @endpermission

          <li class="dropdown">
            @permissions(('indice-list', 'publicacion-list'))
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nav_indices">@trans('forms.indices')
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              @permissions(('publicacion-list'))
                <li class="dropdown">
                  <a id="indices_list" class="dropdown-toggle" href="{{ route('publicaciones.index') }}">
                    @trans('forms.publicaciones')
                  </a>
                </li>
              @endpermission
              <li role="separator" class="divider"></li>
              @permissions(('indice-list'))
                <li class="dropdown">
                  <a id="indices_list" class="dropdown-toggle" href="{{ route('publicaciones.reporteIndices') }}">
                    @trans('index.reporte_indices_valores')
                  </a>
                </li>
              @endpermission
              @permissions(('indice-list'))
                <li class="dropdown">
                  <a id="indices_list" class="dropdown-toggle" href="{{ route('publicaciones.fuentesIndices') }}">
                    @trans('index.reporte_indices_fuentes')
                  </a>
                </li>
              @endpermission
            </ul>
            @endpermission
          </li>
        </li>

        <li class="dropdown">
          @permissions(('contratista-list'))
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nav_contratistas">@trans('forms.contratistas')
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li class="dropdown">
              <a id="contratistas_list" class="dropdown-toggle" href="{{ route('contratistas.index') }}">
                @trans('forms.contratistas')
              </a>
            </li>
            @permissions(('usuario-list'))
              <li class="dropdown">
                <a id="contratistas_list" class="dropdown-toggle" href="{{ route('contratistas.usuarios.index') }}">
                  @trans('forms.usuarios')
                </a>
              </li>
            @endpermission
          </ul>
          @endpermission
        </li>

        <li class="dropdown">
          @permissions(('ReporteAdendas', 'ReporteEconomico', 'ReporteFinanciero', 'ReporteFisico', 'ReporteRedeterminaciones'))
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="nav_reportes">@trans('forms.reportes')
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li class="dropdown">
              <a id="contratistas_list" class="dropdown-toggle" href="{{ route('reportes.index') }}">
                @trans('forms.reportes')
              </a>
            </li>
          </ul>
          @endpermission
        </li>

        <li class="dropdown">
           @permissions(('role-list', 'user-list', 'grupo-list', 'motivos-list'))
           <li class="dropdown">
             <a id="dropdown_opciones" href="javascript:void(0);" class="dropdown-toggle with-icon rsp-no-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fa fa-cog desktop-only"></i>
               <div class="rsp-only">@trans('index.configuracion')
                 <span class="caret"></span>
               </div>
              </a>
                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                  @permissions(('user-list', 'role-list', 'grupo-list'))
                    <li class="dropdown-submenu">
                      <a class="levelToggle" tabindex="-1" href="#">@trans('index.seguridad')</a>
                      <ul class="dropdown-menu">
                        @permissions(('user-list'))
                          <li><a id="user_list" href="{{ route('seguridad.users.index') }}">@trans('index.usuarios')</a></li>
                        @endpermission
                        @permissions(('role-list'))
                          <li><a id="role_list" href="{{ route('seguridad.roles.index') }}">@trans('index.roles')</a></li>
                        @endpermission
                        @permissions(('causante-list'))
                          <li><a id="role_list" href="{{ route('causantes.index') }}">@trans('index.causantes')</a></li>
                        @endpermission
                      </ul>
                    </li>
                  @endpermission
                  @permissions(('motivos-list'))
                    <li class="dropdown-submenu">
                      <a class="levelToggle" tabindex="-1" href="#">@trans('index.configuracion')</a>
                      <ul class="dropdown-menu">
                        @permissions(('motivos-list'))
                          <li class="motivos_list"><a id="role_list" href="{{ route('motivos.index') }}">@trans('index.motivos_reprogramacion')</a></li>
                        @endpermission
                      </ul>
                    </li>
                  @endpermission

                  @permission((['alarma-list', 'alarma-create']))
                  <li class="dropdown-submenu">
                    <a class="levelToggle" tabindex="-1" href="#">@trans('index.alarmas')</a>
                    <ul class="dropdown-menu">
                      @permission(('alarma-list'))
                        <li><a id="alarma_list" href="{{ route('alarmas.solicitud') }}">@trans('index.alarmas')</a></li>
                      @endpermission
                    </ul>
                  </li>
                  @endpermission

                  @if(config('custom.test_mode') == 'true')
                    <li class="dropdown-submenu">
                      <a class="levelToggle" tabindex="-1" href="#">Rutas TEST</a>
                      <ul class="dropdown-menu">
                          <li class="test_list"><a id="role_list" href="{{ route('test.testRoutes') }}">Rutas TEST</a></li>
                      </ul>
                    </li>
                  @endif
                </ul>
            </li>
            @endpermission
        </li>
    </ul>
  </div>
  <div class="custom-nav-icons">
    <ul class="list_icons">
      <!--Usuario Dropdown-->
        <li class="dropdown pull-right">
          <a href="javascript:void(0);" id="nav_user" class="dropdown-toggle with-icon rsp-no-icon" title="{{Auth::user()->nombre_apellido}}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-user-circle" aria-hidden="true"></i>
          </a>
          <ul class="dropdown-menu">
            <li class="dropdown-header desktop-only">{{Auth::user()->nombre_apellido}}</li>
            <li><a href="{{ url('seguridad/perfil') }}" id="nav_perfil">@trans('index.perfil')</a></li>
            <li><a href="{{ route('seguridad.cambiarContrasenia') }}" id="nav_cambiar_contrasenia">@trans('index.cambiar_contrasenia')</a></li>
            <li>
              <a href="{{ route('logout') }}" id="nav_logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                @trans('index.logout')

              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
              </form>
            </li>
          </ul>
        </li>
      <!--Fin Usuario Dropdown-->
      <!--Notificaciones Dropdown-->
        <li class="dropdown pull-right">
          <a href="#" class="dropdown-toggle with-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bell-o text-success"></i>
            <span class="count-notify">{{count(Auth::user()->unreadNotifications)}}</span>
          </a>
          <ul class="dropdown-menu dd-notification">
            @if(count(Auth::user()->unreadNotifications) > 0)
              <li>
                <a id="markAllAsRead" href="javascript:void(0);" data-url="{{route('notification.markAllAsRead')}}">@trans('index.marcar_como_leidas')</a>
              </li>
            @endif
            @foreach(Auth::user()->notifications as $notification)
              @if($notification->read_at == null)
                <li class="notification-li notification-no-leido">
                  <a href="{{ route('notification.markAsRead', [$notification->id, $notification->data['route'], $notification->data['routeParam']])}}">{{ $notification->data['msg'] }}</a>
                </li>
              @endif
              @if($notification->read_at != null)
                <li class="notification-li notification-leido">
                  <a href="{{ action('NotificationsController@markAsRead', [$notification->id, $notification->data['route'], $notification->data['routeParam']]) }}">{{ $notification->data['msg'] }}</a>
                </li>
              @endif
            @endforeach
            @if(count(Auth::user()->notifications) == 0)
              <li class="notification-li notification-leido">
                @trans('index.sin_notificaciones')
              </li>
            @endif
          </ul>
        </li>
      <!--Fin Notificaciones Dropdown-->
      <!--Modal Ayuda-->
        <li class="dropdown pull-right">
          <a href="javascript:void(0);" id="nav_user" class="dropdown-toggle with-icon rsp-no-icon" title="" data-toggle="modal" role="button" data-target="#modal_ayuda">
            <i class="fa fa-question-circle" aria-hidden="true"></i>
          </a>
        </li>
      <!--Fin Modal Ayuda-->

      @elseif(!isset($exception))
        <li>
          <a href="{{ route('ingresar') }}" class="dropdown-toggle" id="login_iniciar_sesion"  role="button" aria-haspopup="true" aria-expanded="false">
            @trans('login.iniciar_sesion')
          </a>
        </li>
      @endif
    </ul>
  </div>
</nav>
