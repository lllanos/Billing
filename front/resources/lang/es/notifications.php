<?php

return [
/////////// Contratos ////////////

  // Aviso a URDP que se solicitó una nueva asociación
  'NuevaAsociacionNotification' => [
		'subject'           => 'Solicitud de Asociación',
		'cuerpo'            => 'Te informamos que el Usuario :usuario solicitó la asociación al Contrato :contrato.',
		'boton'             => 'Ver solicitud de Asociación',
		// 'mensaje_despedida' => 'mensaje_despedida',
  ],

// Aviso a la Contratista que se aprobó/rechazósu solicitud de asociación
  'AsociacionGestionadaNotification' => [
		'subject'           => 'Solicitud de Asociación a Contrato :estado',
		'cuerpo'            => 'Te informamos que la solicitud que realizaste al Contrato :contrato ha sido: :estado',
		'boton'             => 'Ver solicitud de Asociación',
	],

// Aviso a la Contratista que un contrato asociado tiene un nuevo Salto
	'SaltoNotification' => [
		'subject'           => 'Redeterminación :contrato',
		'cuerpo'            => 'Le queremos informar que puede solicitar una redeterminación de precios de la obra :contrato_moneda del contrato :contrato',
		'boton'             => 'Ver Salto',
	],

// Aviso a EBY que hay un nuevo Contrato
  'NuevoContratoNotification' => [
		'subject'           => 'Nuevo Contrato',
		'cuerpo'            => 'Te informamos que el Contrato :contrato fue importado al sistema.',
		'boton'             => 'Ver Contrato',
		// 'mensaje_despedida' => 'mensaje_despedida',
  ],
/////////// FIN Contratos ////////////

/////////// Indices ////////////
// Aviso a quien tenga permisos de publicar que hay una publicación para que revise
	'EnviadoAprobarNotification' => [
		'subject'           => 'Índices para publicar',
		'cuerpo'            => 'Te informamos que se encuentran disponibles para aprobar los índices de :publicacion',
		'boton'             => 'Ver Índices',
	],

// Aviso a quien tenga permisos de editar índices que una publicación fue rechazada o aceptada
	'AprobarDesaprobarNotification' => [
		'subject'           => 'Índices :estado',
		'cuerpo'            => 'Te informamos que los índices de :publicacion fueron :estado',
		'boton'             => 'Ver Índices',
	],
/////////// FIN Indices ////////////

/////////// Poderes ////////////

// Aviso a la contratista que se venció su asociación
  'VencimientoPoderNotification' => [
    'subject'           => 'Contrato :contrato',
    'cuerpo'            => 'Te informamos que se venció el poder con tu asociación al contrato :contrato',
    // 'boton'             => '',
  ],
/////////// FIN Poderes ////////////

/////////// Analisis de Precios ////////////
// Aviso a quien tenga permisos de editar índices que una publicación fue rechazada o aceptada
  	'instanciaGestionadaNotification' => [
  		'subject'           => ':estado',
  		'cuerpo'            => 'Te informamos que el Análisis de Precios de :expediente fue :estado',
  		'boton'             => 'Ver Análisis de Precios',
  	],

// Avisa que fallo la actualizacion del itemizado
  	'SIGONotification' => [
  		'subject'           => 'Proceso fallido',
  		'cuerpo'            => 'Te informamos que fallo el proceso de actualización de itemizado de :expediente',
  		'boton'             => 'Ver Análisis de Precios',
  	],
/////////// FIN Analisis de Precios ////////////

/////////// Certificado ////////////
  'CertificadoGestionadoNotification' => [
    'subject'           => 'Certificado :estado',
    'cuerpo'            => 'Te informamos que el Certificado de :expediente fue :estado',
    'boton'             => 'Ver Certificado',
  ],
/////////// FIN Certificado ////////////
/////////// Error ////////////
'error' => [
    'titulo'           => 'Error al enviar la notificación',
    'mensaje'          => 'No se pudo enviar la notificación',    
  ],
/////////// FIN Errores ////////////
];
