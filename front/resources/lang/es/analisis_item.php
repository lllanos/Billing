<?php

return [
  'analisis_item'                 => 'Análisis de Item',
  'componente'                    => 'Componente',
  'costo_total'                   => 'Costo Total',
  'costo_unitario'                => 'Costo Unitario',
  'descripcion_calculo'           => 'Descripción Cálculo',
  'nuevo_componente'              => 'Nuevo Componente de ',
  'nuevo_nombre'                  => 'Nombre',
  'precio_unitario'               => 'Precio Unitario',
  'rendimiento'                   => 'Rendimiento',
  'sin_analisis'                  => 'Falta el análisis de Precios de :item',
  'subtotal_ejecucion'            => 'Subtotal Ejecución',
  'subtotal_mo_eq'                => 'Subtotal Mano de Obra + Equipos',
  'subtotal_sobre_rendimiento'    => 'Subtotal Ejecución / Rendimiento',
  'total_por'                     => 'TOTAL/',
  'unidad'                        => 'Unidad',

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

  'confirmaciones' => [
    'aprobar_obras'     => '¿Está seguro que desea Aprobar por Obras?',
    'aprobar_precios'   => '¿Está seguro que desea Aprobar por Precios?',
    'a_validar'         => '¿Está seguro que desea enviar A validar?',
		'a_corregir'        => '¿Está seguro que desea enviar A corregir?',
	],

  // En orden de las acciones
  'mensajes' => [
    'success' => [
      'aprobar_obras'     => 'Análisis de Item aprobado Obras con éxito',
      'aprobar_precios'   => 'Análisis de Item aprobado Precios con éxito',
      'rechazar'          => 'Análisis de Item rechazado con éxito',
    ],
    'confirmar' => [
      'enviar_aprobar'    => '¿Está seguro que desea enviar el Análisis de Item de :expediente para su aprobación?',
      'aprobar'           => '¿Está seguro que desea aprobar el Análisis de Item de :expediente?',
      'rechazar'          => '¿Está seguro que desea rechazar el Análisis de Item de :expediente?',
    ],
    'error' => [
      'estado'            => ':item: El Análisis de Item de se encuentra en estado ":estado".',
      'estado_item'       => 'El Análisis de Item de se encuentra en estado ":estado".',
      'precio'            => ':item: El precio cargado en el Análisis de Item no coincide con el del item.',
      'precio_item'       => 'El precio cargado en el Análisis de Item no coincide con el del item.',
      'rendimiento'       => ':item: El rendimiento de :categoria no tiene unidad.',
      'rendimiento_item'  => 'El rendimiento de :categoria no tiene unidad.',
    ],
  ],

  'estados' => [
      'nombre' => [
    		'sin'  	            => 'Sin análisis de item',
    		'borrador'  			  => 'Análisis de item en borrador',
        'a_validar'      	  => 'Análisis de item a validar',
        'aprobado_obras'    => 'Análisis de item aprobado por Obras',
        'aprobado_precios'  => 'Análisis de item aprobado por Precios',
        'aprobado'          => 'Análisis de item aprobado',
        'a_corregir'        => 'Análisis de item a corregir',
    		'nuevo_item'        => 'Análisis de item con un nuevo item',

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
