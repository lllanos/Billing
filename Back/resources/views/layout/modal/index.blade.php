@if(Auth::check())
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

                <ul class="container_list_p-0 p-0">
                  <li><a href="#srdp">{!!trans('ayuda.sistema_redeterminacion_precio.pantalla_incio.titulo')!!}</a></li>
                </ul>



                @if(Auth::user()->puedeVerModulo('contrato'))
                  <label class="ttl_label_modal">{!!trans('ayuda.contratos.titulo')!!}</label>
                  <ul class="container_list_p-0 p-0">
                    <li><a href="#contrato_nuevo_contrato">{!!trans('ayuda.contratos.subtitulo_nuevo_contrato')!!}</a></li>
                    <li><a href="#contrato_editar_contrato">{!!trans('ayuda.contratos.subtitulo_editar_contrato')!!}</a></li>
                    <li><a href="#contrato_firmar_contrato">{!!trans('ayuda.contratos.subtitulo_firmar_contrato')!!}</a></li>
                    <li><a href="#contrato_editar_items">{!!trans('ayuda.contratos.subtitulo_editar_itemizado')!!}</a></li>
                    <li><a href="#contrato_editar_Plan">{!!trans('ayuda.contratos.subtitulo_editar_plan')!!}</a></li>
                    <li><a href="#contrato_editar_polinomica">{!!trans('ayuda.contratos.subtitulo_editar_polinomica')!!}</a></li>
                    <li><a href="#contrato_adenda">{!!trans('ayuda.contratos.subtitulo_adendas')!!}</a></li>
                    <li><a href="#contrato_ampliacion_rep">{!!trans('ayuda.contratos.subtitulo_ampliacion_rep')!!}</a></li>
                    <li><a href="#contrato_anticipo">{!!trans('ayuda.contratos.subtitulo_anticipo')!!}</a></li>
                    <li><a href="#contrato_empalme">{!!trans('ayuda.contratos.subtitulo_empalme')!!}</a></li>
                    <li><a href="#contrato_analisis">{!!trans('ayuda.contratos.subtitulo_analisis_precios')!!}</a></li>
                    <li><a href="#contrato_certificados">{!!trans('ayuda.contratos.subtitulo_Certificados')!!}</a></li>
                  </ul>
                @endif

                @if(Auth::user()->puedeVerModulo('redeterminaciones'))
                  <label class="ttl_label_modal">{!!trans('ayuda.solicitudes.titulo')!!}</label><br>
					   <ul class="container_list_p-0 p-0">
							<li><a href="#solicitudes_redet_proceso">{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.titulo')!!}</a></li>
							<li><a href="#solicitudes_redet_finalizadas">{!!trans('ayuda.solicitudes.solicitudes_redet_finalizadas.titulo')!!}</a></li>
							<li><a href="#solicitudes_certif_proceso">{!!trans('ayuda.solicitudes.solicitudes_certif_proceso.titulo')!!}</a></li>
							<li><a href="#solicitudes_certif_finalizadas">{!!trans('ayuda.solicitudes.solicitudes_certif_finalizadas.titulo')!!}</a></li>
						</ul>
                @endif

				@if(Auth::user()->puedeVerModulo('reportes'))
                  <label class="ttl_label_modal">{!!trans('ayuda.reportes.titulo')!!}</label><br>
					   <ul class="container_list_p-0 p-0">
							<li><a href="#reporte_economico">{!!trans('ayuda.reportes.economico.titulo')!!}</a></li>
							<li><a href="#reporte_fisico">{!!trans('ayuda.reportes.fisico.titulo')!!}</a></li>
							<li><a href="#reporte_financiero">{!!trans('ayuda.reportes.financiero.titulo')!!}</a></li>
							<li><a href="#reporte_adenda">{!!trans('ayuda.reportes.adenda.titulo')!!}</a></li>
							<li><a href="#reporte_redeterminacio">{!!trans('ayuda.reportes.redeterminacion.titulo')!!}</a></li>
					</ul>
                @endif

                @if(Auth::user()->puedeVerModulo('publicacion'))
                  <label class="ttl_label_modal">{!!trans('ayuda.indices.titulo')!!}</label>
                  <ul class="container_list_p-0 p-0">
                    <li><a href="#listado_publicaciones">{!!trans('ayuda.indices.listado_publicaciones.titulo')!!}</a></li>
                    <li><a href="#tabla_i_valores">{!!trans('ayuda.indices.tabla_i_valores.titulo')!!}</a></li>
                    <li><a href="#tabla_i_fuentes">{!!trans('ayuda.indices.tabla_i_fuentes.titulo')!!}</a></li>
                  </ul>
                @endif

                @if(Auth::user()->puedeVerModulo('contratista'))
                  <label class="ttl_label_modal">{!!trans('ayuda.contratistas.titulo')!!}</label>
					<ul class="container_list_p-0 p-0">
						<li><a href="#contratistas">{!!trans('ayuda.contratistas.titulo')!!}</a></li>
					</ul>
			   @endif

                @if(Auth::user()->puedeVerModulo('configuracion'))
                  <label class="ttl_label_modal">{!!trans('ayuda.configuracion.titulo')!!}</label>
                  <ul class="container_list_p-0 p-0">
                    <li><a href="#configuracion_distritos">{!!trans('ayuda.configuracion.configuracion_distritos.titulo')!!}</a></li>
                    <li><a href="#procesos">{!!trans('ayuda.configuracion.procesos.titulo')!!}</a></li>
                    <li><a href="#procesos_alarmas">{!!trans('ayuda.configuracion.procesos_alarmas.titulo')!!}</a></li>
                  </ul>
                @endif
                <!--Sistema de Redeterminacion de Precios-->
                  @if(Auth::user()->puedeVerModulo('redeterminaciones'))
                    <h4>{!!trans('ayuda.sistema_redeterminacion_precio.titulo')!!}</h4>
                    @include('layout.modal.sistema_redeterminacion_precio.index')
                  @endif
                <!--Fin Sistema de Redeterminacion de Precios-->
                <!--Contratos-->
                  @if(Auth::user()->puedeVerModulo('contrato'))
                    <h4 id="contrato">{!!trans('ayuda.contratos.titulo')!!}</h4>
                    @include('layout.modal.contratos.index')
                    @include('layout.modal.contratos.asociacion_contratos_pendientes')
                    @include('layout.modal.contratos.asociacion_contratos_finalizadas')
                    @include('layout.modal.contratos.aprobar_solic_asoc_contrato')
                    @include('layout.modal.contratos.rechazar_solic_asoc_contrato')
                  @endif
                <!--Fin Contratos-->
                <!--Solicitudes-->
                  @if(Auth::user()->puedeVerModulo('redeterminaciones'))
                    <h4 id="solicitudes">{!!trans('ayuda.solicitudes.titulo')!!}</h4>
                    @include('layout.modal.solicitudes.index')
                    @include('layout.modal.solicitudes.solicitudes_redet_proceso')
                    @include('layout.modal.solicitudes.solicitudes_redet_finalizadas')
                  @endif
                <!--Fin Solicitudes-->
				 <!--Solicitudes-->
                  @if(Auth::user()->puedeVerModulo('redeterminaciones'))
                    @include('layout.modal.solicitudes.solicitudes_certif_proceso')
                    @include('layout.modal.solicitudes.solicitudes_certif_finalizadas')
                  @endif
                <!--Fin Solicitudes-->
                <!--Indices-->
                  @if(Auth::user()->puedeVerModulo('publicacion'))
                    <h4 id="indices">{!!trans('ayuda.indices.titulo')!!}</h4>
                    @include('layout.modal.indices.index')
                    @include('layout.modal.indices.listado_publicaciones')
                    @include('layout.modal.indices.tabla_i_valores')
                    @include('layout.modal.indices.tabla_i_fuentes')
                  @endif
                <!--Fin Indices-->
				<!--Reportes-->
                  @if(Auth::user()->puedeVerModulo('reportes'))
                    @include('layout.modal.reportes.index')
                  @endif
                <!--Fin reportes-->
                <!--Contratistas-->
                  @if(Auth::user()->puedeVerModulo('contratista'))
                    <h4 id="contratistas">{!!trans('ayuda.contratistas.titulo')!!}</h4>
                    @include('layout.modal.contratistas.index')
                  @endif
                <!--Fin Contratistas-->
                <!--Seguridad-->
                  @if(Auth::user()->puedeVerModulo('seguridad'))
                    <h4 id="seguridad">{!!trans('ayuda.seguridad.titulo')!!}</h4>
                    @include('layout.modal.seguridad.seguridad_usuarios')
                    @include('layout.modal.seguridad.seguridad_roles')
                  @endif
                <!--Fin Seguridad-->
                <!--Configuracion-->
                  @if(Auth::user()->puedeVerModulo('configuracion'))
                    <h4 id="configuracion">{!!trans('ayuda.configuracion.titulo')!!}</h4>
                    @include('layout.modal.configuracion.index')
                    @include('layout.modal.configuracion.configuracion_distritos')
                    @include('layout.modal.configuracion.procesos')
                    @include('layout.modal.configuracion.procesos_alarmas')
                  @endif
                <!--Fin Configuracion-->
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal-dialog -->
@endif
