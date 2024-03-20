<?php

return [
  'actual'                      => 'Actual',
  'acumulado'                   => 'Acumulado',
  'acumulado_anterior'          => 'Acum. Anterior',
  'avance_acumulado'            => 'Avance Acum.',
  'avance_certificado'          => 'Avance Certificado',
  'basico'                      => 'Básico',
  'basicos'                     => 'Básicos',
  'bruto_cert_redeterminado'    => 'Bruto Certificado Redeterminado',
  'certificado_actual'          => 'Certificado actual',
  'certificaciones'             => 'Certificaciones',
  'desc_anticipo'               =>'Descuento por Anticipo',
  'desc_anticipo_importes_por_ajustes'  =>'Descuento Anticipo a Importes de ajustes',
  'descuento_anticipo'          => 'Descuento Anticipo',
  'desvio'                      => 'Desvío Acum.',
  'desvio_acumulado'            => 'Desvio Acum.',
  'importe_acumulado'           => 'Importe Acum.',
  'importe_certificado'         => 'Importe Certificado',
  'importes_por_ajustes'        => 'Importe por Ajustes',
  'monto'                       => 'Importe',
  'no_se_puede_crear'           => 'No se pueden solicitar Ampliaciones ya que hay una Ampliación/Reprogramación/Adenda incompleta. Por favvor complete la misma y vuelva a intentarlo',
  'nr_certificado'              => 'Nro de certificado',
  'nr_certificado_th'           => 'Nro.',
  'otros_conceptos'             => 'Otros Conceptos',
  'redeterminado'               => 'Redeterminado',
  'redeterminados'              => 'Redeterminados',
  'planificado_actual'          => 'Planificado Actual',
  'planificado_acumulado'       => 'Planificado Acum.',
  'planificado_anterior'        => 'Planificado Ant.',
  'precio_redeterminado'        => 'Precio Redeterminado',
  'sin_anticipo'                => 'Sin Anticipo',
  'sin_permisos'                => 'No tiene permisos para cargar Certificados',
  'tiene_redeterminado'         => 'Tiene certificado Redeterminado',
  'tipo_certificado'            => 'Tipo Certificado',
  'total_menos_ant'             => 'Total-anticipo: ',
  'total_cert_redeterminado_actual' => 'Total Certificado Redeterminado Actual',

	'estados' => [
      'nombre' => [
    		'sin'  	          => 'Sin certificado',
        'borrador_por_contratista'        => 'Certificado en Borrador por Contratista',
    		'borrador'  			=> 'Certificado en borrador',
    		'emitido'  			  => 'Certificado Emitido',
        'a_validar'      	=> 'Certificado en trámite',
        'aprobado'      	=> 'Certificado aprobado',
    		'a_corregir'      => 'Certificado a corregir',
      ],
      'nombre_tag' => [
        'sin'  	          => 'Sin certificado',
        'borrador_por_contratista'        => 'Borrador por Contratista',
    		'borrador'  			=> 'Borrador',
    		'emitido'  			  => 'Emitido',
        'a_validar'      	=> 'En trámite',
        'aprobado'      	=> 'Aprobado',
    		'a_corregir'      => 'A corregir',
      ]
		],

  'adjuntos' => [
        'acta_medicion'                    => 'Acta de medición',
        'seguro_responsabilidad_civil'     => 'Seguro de responsabilidad Civil',
        'seguro_vida'                      => 'Seguro de Vida',
        'ART'                              => 'ART',
        'nueve_tres_uno'                   => 'Formulario 931',
    ],

    'mensajes' => [
      'confirmacion' => 'Va a finalizar la carga del certificado. Realizando esta acción el certificado se dará por aprobado y el avance no podrá ser corregido ¿Esta seguro de realizar esta acción?',

      'confirmacion_validar' => 'Va a enviar para aprobar el certificado. Una vez enviado el certificado no podrá ser modificado sin la previa autorización del inspector. ¿Esta seguro de realizar esta acción?',
      'confirmacion_validar_aclaracion'   => '</br> Se va a generar el certificado básico del mes :mes  y los certificados redeterminados ',
      'confirmacion_validar_aclaracion_2' => ' correspondientes por las redeterminaciones.',

      'confirmacion_enviar_aprobar'     => 'Va a enviar el certificado redeterminado. Una vez enviado, se enviará para aprobación del inspector. ¿Esta seguro de realizar esta acción?',
      'confirmacion_enviar_aprobar_mes' => 'Va a enviar el certificado redeterminado <b>:mes</b>. Una vez enviado, se enviará para aprobación del inspector. ¿Esta seguro de realizar esta acción?',
      'enviado_aprobar'                 => 'Certificado enviado para abrobación',

      'no_puede_solicitar' => 'No se puede solicitar',
    ]
];
