<?php

return [
  'aplica_tope'                       => 'Aplica Tope',
  'anterior_a_sistema'                => 'Publicación anterior al sistema',
  'carretero'                         => 'Carretero',
  'no_se_pueden_crear'                => 'Ya fueron creadas todas las publicaciones posibles',
  'no_se_publica'                     => 'No se publica',
  'nueva_fuente'                      => 'Nueva Fuente',
  'publicacion_existente'             => 'Ya existe esa publicación',
  'sin_instancias'					          => 'No hay información histórica',
  'ver_fuentes'                       => 'Ver fuentes',
  'ver_valores'                       => 'Ver valores',

  'errores' => [
    'publicado'                     => 'No se puede editar porque ya fue publicada',
    'publicados_anteriores'         => 'El mes :mes no fue publicado y no podrá publicarse luego de realizar esta acción. ¿Desea realizar la publicación de todas maneras?',
    'publicados_siguientes'         => 'Existe la publicación :publicacion_posterior. No es posible aprobar este índice',
    'publicados_siguientes_accion'  => 'Existe la publicación :publicacion_posterior. No es posible :accion',
    'se_usa_en_polinomica'          => 'Debe completar el valor de :nro - :nombre',
    'deshabilitar_indice'           => [
            'contrato_activo'    => 'No se puede dar de baja porque está siendo utilizado por la polinómica :polinomica',
            'es_componente'      => 'No se puede dar de baja porque está siendo utilizado por el índice :padre',
    ]
  ],

  // En orden de las acciones
  'mensajes' => [
    'borrador_guardado'   => 'Borrador guardado con éxito',
    'enviada_aprobar'     => 'Publicacion enviada para aprobación',
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
      'guardar_borrador'  => 'EC407A',
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
