<?php

return [
  'analisis_item'         => 'Análisis de Item',
  'coeficiente_resumen'   => 'Coeficiente Resumen',
  'coeficiente_k'         => 'Coeficiente K',
  'costo_coeficiente_k'   => 'Costo x Coeficiente K',
  'costo_total'           => 'Costo Total',
  'no_datos'              => 'No hay datos disponibles, por favor contacte a los administradores',
  'nuevo_nombre'          => 'Nombre',
  'sin_analisis'          => 'Falta el análisis de Precios de :item',
  'total_por'             => 'TOTAL/',

  'categorias' => [
    'Amortizaciones'            => 'Amortizaciones',
    'CombustiblesLubricantes'   => 'Combustible y Lubricantes',
    'Ejecucion'      	          => 'Ejecución',
    'Equipos'      	            => 'Equipos',
    'ManoObra'                  => 'Mano de Obra',
    'MaquinasEquipos'           => 'Máquinas y Equipos',
    'Materiales'                => 'Materiales',
    'ReparacionesRepuestos'     => 'Reparaciones y Repuestos',
    'Transporte'                => 'Transporte',
  ],

  'acciones' => [
    'guardar'           => 'Guardar',
    'guardar_borrador'  => 'Guardar Borrador',
    'aprobar'           => 'Aprobar',
    'aprobar_obras'     => 'Aprobar por Obras',
    'aprobar_precios'   => 'Aprobar por Precios',
    'a_validar'         => 'A validar',
		'a_corregir'        => 'A corregir',
	],

  // En orden de las acciones
  'mensajes' => [
    'success' => [
      'aprobar_obras'     => 'Análisis de Precios aprobado Obras con éxito',
      'aprobar_precios'   => 'Análisis de Precios aprobado Precios con éxito',
      'rechazar'          => 'Análisis de Precios rechazado con éxito',
    ],
    'confirmar' => [
      'guardar'           => 'Guardar',
      'guardar_borrador'  => 'Guardar Borrador',
      'aprobar'           => 'Aprobar',
      'aprobar_obras'     => '¿Está seguro que desea aprobar el Análisis de Precios de :nombre_completo?',
      'aprobar_precios'   => '¿Está seguro que desea aprobar el Análisis de Precios de :nombre_completo?',      
      'aprobar_redeterminado'   => '¿Está seguro que desea aprobar el Análisis de Precios Redeterminado?',
      'rechazar'          => '¿Está seguro que desea rechazar el Análisis de Precios de :nombre_completo?',
    ],
  ],

  'estados' => [
      'nombre' => [
    		'sin'  	            => 'Sin análisis de precios',
    		'borrador'  			  => 'Análisis de precios en borrador',
        'a_validar'      	  => 'Análisis de precios a validar',
        'aprobado_obras'    => 'Análisis de precios aprobado por Obras',
        'aprobado_precios'  => 'Análisis de precios aprobado por Precios',
        'aprobado'          => 'Análisis de precios aprobado',
    		'a_corregir'        => 'Análisis de precios a corregir',
        'nuevo_item'        => 'Análisis de precios con nuevo item',

      ],
      'nombre_tag' => [
        'sin'  	            => 'Sin análisis',
    		'borrador'  			  => 'Borrador',
        'a_validar'      	  => 'A validar',
        'aprobado_obras'    => 'Aprobado por Obras',
        'aprobado_precios'  => 'Aprobado por Precios',
        'aprobado'          => 'Aprobado',
    		'a_corregir'        => 'A corregir',
        'nuevo_item'        => 'Con un nuevo item',

      ]
		],

];
