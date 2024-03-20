<?php

return [
////////////////// Seguridad //////////////////
  'seguridad' => 'Seguridad',

    'rol' => 'Roles',
      'role-list' 		    => 'Listar Roles',
      'role-create' 		  => 'Crear Roles',
      'role-edit' 		    => 'Editar Roles',
      'role-delete' 		  => 'Eliminar Roles',
      'role-view' 		    => 'Ver Rol',
      'role-edit-acl' 	  => 'Editar Permisos',
      'role-edit-widget' 	=> 'Editar Widgets',
      'role-view-acl' 	  => 'Ver Permisos',
      'role-view-widget' 	=> 'Ver Widgets',
      'role-export' 		  => 'Exportar Roles',

      'user' => 'Usuarios',
        'user-list' 		    => 'Listar Usuarios',
        'user-create' 		  => 'Crear Usuarios',
        'user-edit' 		    => 'Editar Usuarios',
        'user-delete' 		  => 'Eliminar Usuarios',
        'user-view' 		    => 'Ver Usuario',
        'user-export' 		  => 'Exportar Usuarios',

  	  'grupo' => 'Grupo',
        'grupo-create' 		  => 'Crear Grupos',
        'grupo-delete' 		  => 'Eliminar Grupos',
        'grupo-edit' 		    => 'Editar Grupos',
        'grupo-list'        => 'Listar Grupos',
        'grupo-view' 		    => 'Ver Grupo',
        'grupo-export'      => 'Exportar Grupos',
////////////////// FIN Seguridad //////////////////

////////////////// Configuracion //////////////////
    'configuracion' => 'Configuración',
  	  'distrito' => 'Distrito',
        'distrito-list'        => 'Listar Distritos',
        'distrito-export'      => 'Exportar Distritos',
	      'distrito-edit'        => 'Editar Distrito',
////////////////// FIN Configuracion //////////////////

////////////////// Contratista //////////////////
    'contratista' => 'Contratista',
        'contratista-list'        => 'Listar Contratistas',
        'contratista-view'        => 'Ver Contratistas',
        'contratista-export'      => 'Exportar Contratistas',
////////////////// FIN Contratista //////////////////

////////////////// Indice //////////////////
    'indice' => 'Índice',
        'indice-list'         => 'Listar Índices',
        'indice-create'       => 'Crear Índices',
        'indice-edit'         => 'Editar Índices',
        'indice-view'         => 'Ver Índices',
        'indice-deshabilitar' => 'Desabilitar Índices',
        'indice-export'       => 'Exportar Índices',
////////////////// FIN Indice //////////////////

////////////////// Contrato //////////////////
    'contrato' => 'Contrato',
  	  'solicitudes-contrato' => 'Solicitudes',
        'sol-contrato-pendientes-list'              => 'Listar Solicitudes Pendientes',
        'sol-contrato-finalizadas-list'             => 'Listar Solicitudes Finalizadas',
        'sol-contrato-pendientes-view'              => 'Ver Solicitudes Pendientes',
        'sol-contrato-finalizadas-view'             => 'Ver Solicitudes Finalizadas',
        'sol-contrato-pendientes-export'            => 'Exportar Solicitudes Pendientes',
        'sol-contrato-finalizadas-export'           => 'Exportar Solicitudes Finalizadas',
        'sol-contrato-ver'                          => 'Ver Solicitudes',
        'sol-contrato-aprobar'                      => 'Aprobar Solicitudes',
        'sol-contrato-rechazar'                     => 'Rechazar Solicitudes',
        'sol-contrato-NuevaAsociacionNotification'  => 'Recibir Notificaciones de Nuevas Asociaciones',

	      'contrato-list'                       => 'Listar Contratos',
        'contrato-view'                       => 'Ver Contratos',
        'contrato-export'                     => 'Exportar Contratos',

      'salto'                       => 'Saltos',
        'salto-view'                       => 'Ver Saltos',
        'salto-list'                       => 'Listar Saltos',

      'nuevo-contrato'              => 'Nuevos Contratos',
	      'nuevo-contrato-list'                 => 'Listar Nuevos Contratos',
        'nuevo-contrato-view'                 => 'Ver Nuevos Contratos',
        'nuevo-contrato-export'               => 'Exportar Nuevos Contratos',
////////////////// FIN Contrato //////////////////

////////////////// Dashboard //////////////////
    'dashboard' => 'Dashboard',
  	  'widget' => 'Vistas',
        'redeterminaciones_por_estado'        => 'Redeterminaciones por estado',
        'tiempos_redeterminaciones'           => 'Tiempos de Solicitudes de Redeterminación por estado',
        'solicitudes_asociacion'              => 'Solicitudes de Asociación',

////////////////// FIN Dashboard //////////////////

////////////////// Solicitud //////////////////
    'redeterminaciones' => 'Redeterminaciones',
  	  'solicitudes-redeterminaciones' => 'Solicitudes',
        'redeterminaciones-en_proceso-list'        => 'Listar Solicitudes En Proceso',
        'redeterminaciones-finalizadas-list'       => 'Listar Solicitudes Finalizadas',
        'redeterminaciones-en_proceso-view'        => 'Ver Solicitudes En Proceso',
        'redeterminaciones-finalizadas-view'       => 'Ver Solicitudes Finalizadas',
        'redeterminaciones-en_proceso-export'      => 'Exportar Solicitudes En Proceso',
        'redeterminaciones-finalizadas-export'     => 'Exportar Solicitudes Finalizadas',
        'redeterminaciones-ver'                    => 'Ver Solicitud',
        'redeterminaciones-rechazar'               => 'Rechazar Solicitud',
        'redeterminaciones-suspender'              => 'Suspender Solicitud',
        'redeterminaciones-continuar'              => 'Continuar Solicitud',

  	  'gestion' => 'Gestión',
        'CalculoPreciosRedeterminados-gestionar'        => 'Gestionar Carga de Precios Redeterminados',
        'AsignacionPartidaPresupuestaria-gestionar'   => 'Gestionar Asignación de Partida Presupuestaria',
        'ProyectoActaRDP-gestionar'                   => 'Gestionar Proyecto de Acta RDP',
        'FirmaContratista-gestionar'                  => 'Gestionar Firma de Contratista',
        'EmisionDictamenJuridico-gestionar'           => 'Gestionar Emisión de Dictamen Jurídico',
        'ActoAdministrativo-gestionar'                => 'Gestionar Acto Administrativo',
        'EmisionCertificadoRDP-gestionar'             => 'Gestionar Emisión de Certificado RDP',
    		'ValidacionPolizaCaucion-gestionar'           => 'Validar Poliza de Caucion',
        'CargaPolizaCaucion-gestionar'          	    => 'Gestionar Poliza de Caucion',
    		'SolicitudRDP-gestionar'          	          => 'Gestionar Solicitud de RDP',

  	  // 'correccion' => 'Corrección',
      //   'CalculoPreciosRedeterminados-corregir'       => 'Corregir Carga de Precios Redeterminados',
      //   'AsignacionPartidaPresupuestaria-corregir'  => 'Corregir Asignación de Partida Presupuestaria',
      //   'ProyectoActaRDP-corregir'                  => 'Corregir Proyecto de Acta RDP',
      //   'FirmaContratista-corregir'                 => 'Corregir Firma de Contratista',
      //   'EmisionDictamenJuridico-corregir'          => 'Corregir Emisión de Dictamen Jurídico',
      //   'ActoAdministrativo-corregir'               => 'Corregir Acto Administrativo',
      //   'EmisionCertificadoRDP-corregir'            => 'Corregir Emisión de Certificado RDP',
      //   'CargaPolizaCaucion-corregir'				        => 'Corregir Poliza de Caución',
	    //   'SolicitudRDP-corregir'				              => 'Corregir Solicitud de RDP',

////////////////// FIN Solicitud //////////////////

////////////////// Publicaciones //////////////////
  	  'publicacion' => 'Publicación',
        'publicacion-list' 		                       => 'Listar Publicaciones',
        'publicacion-create' 		                     => 'Crear Publicación',
        'publicacion-edit' 		                       => 'Editar Publicaciones',
        'publicacion-delete' 		                     => 'Eliminar Publicaciones',
        'publicacion-view' 		                       => 'Ver Publicaciones',
        'publicacion-export' 		                     => 'Exportar Publicaciones',
        'publicacion-guardar_borrador'               => 'Guardar Borrador de Publicación',
        'publicacion-enviar_aprobar' 		             => 'Enviar Publicación para Aprobar',
        'publicacion-publicar' 		                   => 'Publicar Publicación',
        'publicacion-rechazar' 		                   => 'Rechazar Publicación',
        'publicacion-ver_historial' 	               => 'Ver Historial de Publicación',
        'publicacion-AprobarDesaprobarNotification'  => 'Recibir Notificaciones de Aprobación/Desaprobación de Índices',
        'publicacion-EnviadoAprobarNotification' 	   => 'Recibir Notificaciones de Índices enviados para Aprobar',

  ////////////////// FIN Publicaciones //////////////////

  ////////////////// Alarmas //////////////////
      'alarma' => 'Alarmas',
        'alarma-create' 		             => 'Crear Alarmas',
        'alarma-list' 		               => 'Listar Alarmas',
        'alarma-habilitar' 		           => 'Habilitar/Deshabilitar Alarmas',
  ////////////////// FIN Alarmas //////////////////

  ////////////////// Procesos //////////////////
      'proceso' => 'Procesos',
        'proceso-list' 		                   => 'Listar Procesos',
        'proceso-view'                       => 'Ver Procesos',
        'proceso-detalle' 		               => 'Detalle de Procesos',
        'proceso-ejecutar' 		               => 'Ejecutar Procesos',
        'proceso-ProcesoFallidoNotification' => 'Recibir Notificación Proceso Fallido',
  ////////////////// FIN Procesos //////////////////

  ////////////////// Analisis de Precios //////////////////
      'analisis-precios' => 'Análisis de Precios',
        'analisis-precios-view'                       => 'Ver Análisis de Precios',
        'analisis-precios-edit' 		                  => 'Editar Análisis de Precios',
        'analisis-precios-export'                     => 'Exportar Análisis de Precios',
        'analisis-precios-add-categoria'              => 'Agregar Categoría',
        'analisis-precios-delete-categoria'           => 'Eliminar Categoría',
        'analisis-precios-add-insumo'                 => 'Agregar Insumo',
        'analisis-precios-edit-insumo'                => 'Editar Insumo',
        'analisis-precios-delete-insumo'              => 'Eliminar Insumo',
        'analisis-precios-aprobar'                    => 'Aprobar Análisis de Precios',
        'analisis-precios-rechazar'                   => 'Rechazar Análisis de Precios',
        'analisis-precios-add-subcategoria'           => 'Agregar Sub Categoría',
        'analisis-precios-delete-subcategoria'        => 'Eliminar Sub Categoría',
        'analisis-precios-Notification'               => 'Recibir Notificaciones de Gestión de Análisis',
        'analisis-precios-procesoFallidoNotification' => 'Recibir Notificaciones de error al actualizar Itemizado',
  ////////////////// FIN Analisis de Precios //////////////////

];
