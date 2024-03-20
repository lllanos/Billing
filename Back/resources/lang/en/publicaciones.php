<?php

return [
  'aplica_tope'                       => 'Aplica Tope',
  'anterior_a_sistema'                => 'Publicación anterior al sistema',
  'carretero'                         => 'Carretero',
  'de_publicaciones_anteriores'       => 'Partir de publicación anterior',
  'no_se_pueden_crear'                => 'Ya fueron creadas todas las publicaciones posibles',
  'no_se_publica'                     => 'No se publica',
  'nueva_fuente'                      => 'Nueva Fuente',
  'publicacion_existente'             => 'Ya existe esa publicación',
  'ver_fuentes'                       => 'Ver fuentes',
  'ver_valores'                       => 'Ver valores',

  'errores' => [
    'publicado'                     => 'No se puede editar porque ya fue publicada',
    'publicados_anteriores'         => 'El mes <b>:mes</b> no fue publicado, debe publicarlo antes de realizar esta publicación',
    'publicados_siguientes'         => 'Existe la publicación <b>:publicacion_posterior</b>. No es posible aprobar este índice',
    'publicados_siguientes_accion'  => 'Existe la publicación <b>:publicacion_posterior</b>. No es posible :accion',
    'se_usa_en_polinomica'          => 'Debe completar el valor de :nro - :nombre',
    'deshabilitar_indice'           => [
            'analisis_precios'   => 'No se puede dar de baja porque está siendo utilizado por el Análisis de Precios de :contrato',
            'contrato_activo'    => 'No se puede dar de baja porque está siendo utilizado por la Polinómica de :contrato',
            'es_componente'      => 'No se puede dar de baja porque está siendo utilizado por el Índice :padre',
    ]
  ],

  // En orden de las acciones
  'mensajes' => [
    'borrador_guardado'   => 'Borrador guardado con éxito',
    'enviada_aprobar'     => 'Publicacion enviada para aprobación',
    'enviar_aprobar'      => '¿Está seguro que desea enviar para aprobar?',
    'confirmar_publicar'  => '¿Está seguro que desea publicar? Una vez publicados, los índices no podrán modificarse',
    'publicada'           => 'Publicación realizada con éxito',
    'rechazada'           => 'Publicación rechazada con éxito',
    'deshabilitar_indice' => '¿Está seguro que desea deshabilitar el índice :nombre?'
  ],

  // En orden de las acciones
  'instancia' => [
    'estado' => [
      'nueva'             => 'Nueva',
      'guardar_borrador'  => 'Borrador',
      'enviar_aprobar'    => 'Enviado para aprobación',
      'rechazar'          => 'Rechazado',
      'publicar'          => 'Publicado',
    ],
    'color' => [
      'guardar_borrador'  => '455A64',
      'borrador'          => '607D8B',
      'enviar_aprobar'    => 'FFCA28',
      'rechazar'          => 'EF5350',
      'publicar'          => '00C853',
    ],
    'acciones' => [
      'nueva'             => 'Nueva Publicación',
      'guardar_borrador'  => 'Guardar Borrador',
      'enviar_aprobar'    => 'Enviar para aprobar',
      'rechazar'          => 'Rechazar',
      'publicar'          => 'Publicar',
    ],
  ],

  'acciones_indices' => [
    'rechazados'         => 'Rechazados',
    'publicados'         => 'Publicados',
  ],
];
