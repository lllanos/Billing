<?php

return [
  'analisis_item'                 => 'Análisis de Item',
  'componente'                    => 'Componente',
  'costo_total'                   => 'Costo Total',
  'costo_unitario'                => 'Costo Unitario',
  'descripcion_calculo'           => 'Descripción Cálculo',
  'nuevo_componente'              => 'Nuevo Componente de ',
  'nuevo_nombre'                  => 'Nombre',
  'precio_unitario'               => 'Importe Unitario',
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
    'aprobar_precios'   => 'Aprobar por Comisión',
    'a_validar'         => 'A validar',
		'a_corregir'        => 'A corregir',
	],

  'confirmaciones' => [
    'aprobar_obras'     => '¿Está seguro que desea Aprobar?',
    'aprobar_precios'   => '¿Está seguro que desea Aprobar?',
    'a_validar'         => '¿Está seguro que desea enviar a validar?',
		'a_corregir'        => '¿Está seguro que desea enviar a corregir?',
	],

  // En orden de las acciones
  'mensajes' => [
    'success' => [
      'aprobar_obras'     => 'Análisis de Item aprobado con éxito por Inspección de Obras',
      'aprobar_precios'   => 'Análisis de Item aprobado con éxito por Comisión de Redeterminación ',
      'rechazar'          => 'Análisis de Item rechazado',
    ],
    'confirmar' => [
      'enviar_aprobar'    => '¿Está seguro que desea enviar el Análisis de Item de :expediente para su aprobación?',
      'aprobar'           => '¿Está seguro que desea aprobar el Análisis de Item de :expediente?',
      'rechazar'          => '¿Está seguro que desea rechazar el Análisis de Item de :expediente?',
    ],
    'error' => [
      'aprobar_obras'     => 'Errores al validar Análisis de Item',
      'aprobar_precios'   => 'Errores al validar Análisis de Item',
      'estado'            => ':item: El Análisis de Item se encuentra en estado ":estado".',
      'estado_item'       => 'El Análisis de Item se encuentra en estado ":estado".',
      'falta_maquinas_eq' => 'Debe cargar al menos un Componente a Máquinas y Equipos si cargó componentes de :categoria.',
      'precio'            => ':item: El precio cargado en el Análisis de ítem (Costo x Coeficiente K) no coincide con el importe unitario del ítem.',
      'precio_item'       => 'El precio cargado en el Análisis de ítem (Costo x Coeficiente K) no coincide con el importe unitario Redeterminado del ítem.',
      'rendimiento'       => ':item: El rendimiento de :categoria no tiene unidad.',
      'rendimiento_item'  => 'El rendimiento de :categoria no tiene unidad.',
    ],
  ],

  'estados' => [
      'nombre' => [
    		'sin'  	            => 'Sin análisis de ítem',
    		'borrador'  			  => 'Análisis de ítem en borrador',
        'a_validar'      	  => 'Análisis de ítem a validar',
        'aprobado_obras'    => 'Análisis de ítem aprobado por Inspección de Obras',
        'aprobado_precios'  => 'Análisis de ítem aprobado por Comisión de Redeterminaciones',
        'aprobado'          => 'Análisis de ítem aprobado',
        'a_corregir'        => 'Análisis de ítem a corregir',
        'nuevo_item'        => 'Análisis de ítem con un nuevo ítem',
        'a_firmar'          => 'Pendiente de firmas',
        'firma'             => 'Pendiente de una firma',
      ],
      'nombre_tag' => [
        'sin'  	            => 'Sin análisis',
    		'borrador'  			  => 'Borrador',
        'a_validar'      	  => 'A validar',
        'aprobado_obras'    => 'Aprobado por Obras',
        'aprobado_precios'  => 'Aprobado por Comisión',
        'aprobado'          => 'Aprobado',
    		'a_corregir'        => 'A corregir',
    		'nuevo_item'        => 'A completar por nuevo itemizado',
        'a_firmar'          => 'Pendiente de firmas',
        'firma'             => 'Pendiente de una firma',
      ]
		],

];
