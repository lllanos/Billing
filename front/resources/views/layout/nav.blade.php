<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
          <a href="javascript:void(0);" class="pull-left toggleMenuRsp" aria-label="menu-responsive"><i class="fa fa-bars"></i></a>
          <a class="navbar-brand" href="{{ url('/') }}" alt="{{config('app.name')}}">
              <img src="{{asset('img/main-logo-eby-arg.png')}}" alt="{{config('app.name')}}"/>
          </a>
        </div>
    </div>

    <div class="custom-nav ocultar @if(!Auth::check()) nav_derecha @endif">
      <ul class="nav navbar-nav navbar-right">
        @if (Auth::check())
          <!--Contratos Dropdown-->
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" id="nav_contratos" href="#">@trans('index.contratos')
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="dropdown">
                <a class="dropdown-toggle" href="{{ route('contratos.index') }}" id="nav_mis_contratos">
                  @trans('index.mis') @trans('index.contratos')
                </a>
              </li>
              <li role="separator" class="divider"></li>
                <li class="dropdown">
                <a class="dropdown-toggle" href="{{ route('contrato.asociar') }}" id="nav_solicitar_asociacion">
                  @trans('index.solicitar_asociacion')
                </a>
              </li>
                <li class="dropdown text-center">
                <a class="dropdown-toggle text-center" href="{{ route('contrato.solicitudes') }}" id="nav_mis_solicitudes_asociacion">
                  @trans('index.mis') @trans('index.solicitudes_asociacion')
                </a>
              </li>
            </ul>
          </li>
          <!--Fin Contratos Dropdown-->

          <!--Redeterminaciones Dropdown-->
          <li class="dropdown">
            <a class="dropdown-toggle text-center" data-toggle="dropdown" id="nav_solicitudes_redeterminacion" href="#">@trans('index.solicitudes_redeterminacion')
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="dropdown">
                <a class="dropdown-toggle" href="{{ route('redeterminaciones.index') }}" id="nav_mis_solicitudes_redeterminacion">
                  @trans('index.mis') @trans('index.solicitudes_redeterminacion')
                </a>
              </li>
                <li class="dropdown">
                <a class="dropdown-toggle" href="{{ route('solicitudes.redeterminaciones.solicitar') }}" id="nav_solicitar_redeterminacion">
                  @trans('index.solicitar_redeterminacion')
                </a>
              </li>
            </ul>
          </li>
          <!--Fin Redeterminaciones Dropdown-->
        @endif
        @if(!isset($exception))
          <!--Indices Dropdown-->
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" id="nav_indices" href="#">@trans('index.indices')
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="dropdown">
                <a id="publicaciones_list" class="dropdown-toggle" href="{{ route('publicaciones.index') }}">
                  @trans('forms.publicaciones')
                </a>
              </li>
              <li role="separator" class="divider"></li>
              <li class="dropdown">
                <a class="dropdown-toggle" href="{{route('publicaciones.reportes')}}" id="nav_indices_valores">
                  @trans('index.reporte_indices_valores')
                </a>
              </li>
              <li class="dropdown">
                <a id="indices_list" class="dropdown-toggle" href="{{ route('publicaciones.fuentesIndices') }}">
                  @trans('index.reporte_indices_fuentes')
                </a>
              </li>
            </ul>
          </li>
          <!--Fin Indices Dropdown-->
        @endif

        @if (Auth::check())
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
                  <a id="markAllAsRead" href="javascript:void(0);" data-url="{{action('NotificationsController@markAllAsRead')}}">@trans('index.marcar_como_leidas')</a>
                </li>
              @endif
              @foreach(Auth::user()->notifications as $notification)
                @if($notification->read_at == null)
                  <li class="notification-li notification-no-leido">
                    <a href="{{ action('NotificationsController@markAsRead', [$notification->id, $notification->data['route'], $notification->data['routeParam']])}}">{{ $notification->data['msg'] }}</a>
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
            <a href="{{ route('ingresar') }}" class="dropdown-toggle btn-highlighted" id="login_iniciar_sesion"  role="button" aria-haspopup="true" aria-expanded="false">
              @trans('login.iniciar_sesion')
            </a>
          </li>
        <!--Modal Ayuda-->
          <li id="ayuda_no_log" class="dropdown pull-right ayuda_no_log adasdsadqdasd">
            <a href="javascript:void(0);" id="nav_ayuda" class="dropdown-toggle with-icon rsp-no-icon p-0" title="" data-toggle="modal" role="button" data-target="#modal_ayuda">
              <i class="fa fa-question-circle" aria-hidden="true"></i>
            </a>
          </li>
        <!--Fin Modal Ayuda-->
      @endif
      </ul>
    </div>
    @if (!Auth::check())
      <div class="custom-nav-icons ayuda_no_log_responsive">
        <ul class="list_icons">
          <!--Modal Ayuda-->
          <li class="dropdown pull-right">
            <a href="javascript:void(0);" id="nav_user" class="dropdown-toggle with-icon rsp-no-icon" title="" data-toggle="modal" role="button" data-target="#modal_ayuda">
              <i class="fa fa-question-circle" aria-hidden="true"></i>
            </a>
          </li>
          <!--Fin Modal Ayuda-->
        </ul>
      </div>
    @endif
</nav>
<div class="backdropBlock ocultarBackdrop"></div>
