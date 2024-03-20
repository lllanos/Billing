<div id="modal_ayuda" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <input type="hidden" name="js_applied" id="js_applied" value="0">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>
        <h4 class="modal-title pl-1_5">
          {{trans('index.ayuda')}}
        </h4>
        <input type="hidden" name="scroll_ayuda" id="scroll_ayuda" value="@if(isset($ayuda)) {{$ayuda}} @endif">
      </div>
      <div class="modal-body">
        <div class="modalContentScrollable">
          <div class="row">
            <div class="col-md-12">
              <label class="ttl_label_modal">{!!trans('ayuda.sistema_rdp.titulo')!!}</label>
              <ul class="container_list_p-0 p-0">
                <li><a href="#modal_que_es_rdp">{!!trans('ayuda.sistema_rdp.que_es_rdp.titulo')!!}</a></li>
                {{-- <li><a href="#modal_registro_en_sistema">{!!trans('ayuda.sistema_rdp.registro_en_sistema.titulo')!!}</a></li> --}}
                <li><a href="#modal_inicio_sesion">{!!trans('ayuda.sistema_rdp.inicio_sesion.titulo')!!}</a></li>
                <li><a href="#modal_recuperar_contrasena">{!!trans('ayuda.sistema_rdp.recuperar_contrasena.titulo')!!}</a></li>
                <li><a href="#modal_pantalla_inicio">{!!trans('ayuda.sistema_rdp.pantalla_inicio.titulo')!!}</a></li>
              </ul>
              @if (Auth::check())
                <label class="ttl_label_modal">{!!trans('ayuda.contratos.titulo')!!}</label>
                <ul class="container_list_p-0 p-0">
                  <li><a href="#modal_solicitar_asociacion">{!!trans('ayuda.contratos.solicitar_asociacion.titulo')!!}</a></li>
                  <li><a href="#modal_solicitudes_asociaciones">{!!trans('ayuda.contratos.solicitudes_asociaciones.titulo')!!}</a></li>
                  <li><a href="#modal_mis_contratos">{!!trans('ayuda.contratos.mis_contratos.titulo')!!}</a></li>
                </ul>
                <label class="ttl_label_modal">{!!trans('ayuda.solicitudes_redeterminacion.titulo')!!}</label>
                <ul class="container_list_p-0 p-0">
                  <li><a href="#modal_solicitar_redeterminacion">{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.titulo')!!}</a></li>
                  <li><a href="#modal_mis_solicitudes_redeterminacion">{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.titulo')!!}</a></li>
                </ul>
              @endif
              <label class="ttl_label_modal">{!!trans('ayuda.indices.titulo')!!}</label>
              <ul class="container_list">
                <li>{!!trans('ayuda.indices.titulo')!!}</li>
              </ul>
            </div>
            <!--Sistema Rdp-->
            <div class="col-md-12">
              <div id="">
                <h4>{!!trans('ayuda.sistema_rdp.titulo')!!}</h4>
              </div>
            </div>

            @include('layout.modal.sistema_rdp.que_es_rdp')
            {{-- @include('layout.modal.sistema_rdp.registro_en_sistema') --}}
            @include('layout.modal.sistema_rdp.inicio_sesion')
            @include('layout.modal.sistema_rdp.recuperar_contrasena')
            @if(Auth::check())
              @include('layout.modal.sistema_rdp.pantalla_inicio')
            @endif
            <!--Fin Sistema Rdp-->

            @if (Auth::check())
              <!--Contratos-->
              <div class="col-md-12" id="contratos">
                  <h4>{!!trans('ayuda.contratos.titulo')!!}</h4>
                  <p class="parrafo_modal">
                    {!!trans('ayuda.contratos.descripcion')!!}
                  </p>
                  <div class="container_img_ayuda">
                    <img src="{{asset('img/ayuda/imagen_4.png')}}">
                  </div>
              </div>
              @include('layout.modal.contratos.solicitar_asociacion')
              @include('layout.modal.contratos.solicitudes_asociacion')
              @include('layout.modal.contratos.mis_contratos')
              <!--Fin contratos-->
              <!--Solicitudes de Redeterminacion-->
              <div class="col-md-12" id="redeterminacion">
                  <h4>{!!trans('ayuda.solicitudes_redeterminacion.titulo')!!}</h4>
                  <p class="parrafo_modal">
                    {!!trans('ayuda.solicitudes_redeterminacion.descripcion')!!}
                  </p>
              </div>
              @include('layout.modal.solicitudes_redeterminacion.solicitar_redeterminacion')
              @include('layout.modal.solicitudes_redeterminacion.mis_solicitudes_redeterminacion')
              <!--Fin Solicitudes de Redeterminacion-->
            @endif
              <!--Indices-->
              <div class="col-md-12" id="indices">
                  <h4>{!!trans('ayuda.indices.titulo')!!}</h4>
              </div>
              @include('layout.modal.indices.index')
              <!--Fin Indices-->
            @if (Auth::check())
              <!--Notificaciones-->
              <div class="col-md-12">
                  <h4>{!!trans('ayuda.notificaciones.titulo')!!}</h4>
              </div>
              @include('layout.modal.notificaciones.index')
              <!--Fin Notificaciones-->
              <!--Usuario-->
              <div class="col-md-12">
                  <h4>{!!trans('ayuda.usuario.titulo')!!}</h4>
              </div>
              @include('layout.modal.usuario.index')
              <!--Fin Usuario-->
            @endif
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal-dialog -->
