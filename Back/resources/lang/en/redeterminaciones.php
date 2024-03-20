<?php

return [
  'monto_unitario'                => 'Importe Unitario',
  'monto_unitario_anterior'       => 'Importe Unitario Anterior',
  'monto_unitario_redeterminado'  => 'Importe Unitario Redeterminado',
  'nro_salto'                     => 'N° de salto',
  'sin_permisos'                  => 'No tiene permisos para crear Redeterminaciones',
  'vr_total'                      => 'Variación total',



  'mensajes' => [
    'error' => [
      'required'                   => ':item: Debe completar el :attribute',
      'falta_certificado'          => '<b>:moneda:</b> Falta el certificado de la redeterminación de :mes',
      'necesita_certificado'       => 'Debe cargar el certificado de empalme en el mes de la última redeterminación:',
      'redeterminacion_borrador'   => '<b>Reterminación nº :nro_salto:</b> Se encuentra en estado borrador'
    ],
  ],

  'cuadro_comparativo' => [
    'analisis_item'                 => 'Análisis de item',
    'incremento'                    => 'Incremento',
    'medicion_a_cert'               => 'A Certificar a :mes',
    'precio_unitario'               => 'Unitario a :mes',
    'precio_unitario_redet'         => 'Unitario a :mes',
    'total_redeterminado'           => 'Total Redeterminado',
    'total_redeterminado_mas_inc'   => 'Total Redeterminado',
    'mensajes'  => [
      'aplicar_penalidad_desvio'    => 'Penalidad por desvío aplicada: El porcentaje a certificar corresponde al acumulado al mes en el plan de trabajo',
      'aplicar_penalidad_45_dias'   => 'Penalidad por solicitud fuera de término: El porcentaje a certificar corresponde al acumulado al mes :mes_anterior ',
    ]
  ],

  'confirmacion' => 'Los datos de la redeterminación serán tomados para recalcular los montos del contrato. Luego de guardado no se podrá eliminar. ¿Esta seguro de guardar la redeterminación de empalme?',

];
