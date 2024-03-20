<?php

return [
	'base'											=> 'Base',
	'fecha_extraccion'					=> 'Fecha de extracción',
	'incremento'								=> 'Incremento',
	'redeterminacion_nro'				=> 'Redeterminación :nro_salto',

	'ReporteEconomico'          => 'Económico Resumen',
	'ReporteFisico'             => 'Físico Resumen',
	'ReporteFinanciero'         => 'Financiero Resumen',
	'ReporteRedeterminaciones'  => 'Redeterminaciones Resumen',
	'ReporteAdendas'            => 'Reporte de Modificaciones Contractuales',

	'descripcion' => [
		'ReporteEconomico'          => 'Permite obtener un resumen económico de los contratos',
		'ReporteFisico'             => 'Permite obtener el avance físico de los contratos. El avance físico se obtiene a partir de los certificados y montos básicos',
		'ReporteFinanciero'         => 'Permite obtener un resumen financiero de los contratos. Se muestra un acumulado hasta el mes de inicio.
																		En las columnas se muestra la fecha en la que se aprobó el certificado y la fecha en la que se proyecta que se realizará el pago (2 meses posteriores). 
																		En los meses anteriores a la fecha actual se muestra el ejecutado y en los meses posteriores se muestra el proyecto a ejecutar',
		'ReporteRedeterminaciones'  => 'Permite visualizar el impacto de las redeterminaciones en los contratos. Los valores tomados en los contratos como vigentes correponden a la redeterminaciones aprobadas',
		'ReporteAdendas'            => 'Permite visualizar el impacto de las modificaciones en los incrementos contractuales',
	],

	'notas' => [
		'titulo' => 'Notas:',

		'ReporteEconomico'				=> [
			0	=> 'Vigente* y Redeterminado*: Los importes se toman en base al último cálculo de la redeterminación aprobada',
			1	=> 'Se acumula lo ocurrido hasta el mes anterior al de inicio y se desglosan los restantes meses',
			2	=> '* La planificación se toma en base al último plan aprobado',
			3	=> 'Ejecutado* es lo ejecutado bruto',
		],
		'ReporteFisico'						=> [
			0	=> '* Los importes se muestran en valores básicos',
			1	=> '* La planificación se toma en base al último plan aprobado',
			2	=> '*Meses Se acumula lo ocurrido hasta el mes anterior al de inicio del rango seleccionado y se desglosan los restantes meses',
			3	=> '* a ejecutar cantidad a ejecutar actualizada',
		],
		'ReporteFinanciero'				=> [
			0	=> '*Los importes se toman en base al último cálculo de la redeterminación (aunque no se encuentre aprobada)',
			1	=> 'Ejecutado*: Certificados del mes en importe Neto',
			2	=> 'Vigente*: Importe a la última redeterminacion (aunque no se encuentre aprobada)',
			3	=> 'Saldo a Ejecutar*: Importe  neto a la última redeterminacion calculada (aunque no se encuentre aprobada). Se le descuenta el porcentaje de anticipo',
			4	=> 'A Ejecutar*: Importe neto en base a la última redeterminacion calculada con el porcentaje a ejecutar según plan de trabajo +- Desvíos proporcionales',
			5	=> '* La planificación se toma en base al último plan aprobado. Lo planificado también se expresa en importes netos',
		],
		'ReporteRedeterminaciones' => [
			0	=> 'El valor Vigente del contrato se toma en base al último aprobado',
			1	=> 'El VR definitivo del contrato se toma dividiendo monto Base/Redeterminado aprobado',
			2	=> 'Saldo a certificar, incremento y vigente son los valores aprobados en la redeterminación'
		],
		'ReporteAdendas'					 => [
			0	=> 'Vigente*: se toma en base a la última redeterminación aprobada. Fórmula: Monto certificado + saldo a certificar',
			1	=> 'Saldo*: Cantidad a certificar del contrato * valores unitarios a últimaredeterminación aprobada',
		],
	],

	'filtros' => [
		'titulo'									=> 'Filtros de Búsqueda',
		'moneda'									=> 'Moneda',
		'periodo'									=> 'Periodo',
		'inspector'								=> 'Inspector de Obra',
		'causante'								=> 'Causante',
		'contrato'								=> 'Contrato',
		'contratista'							=> 'Contratista',
		'estado_contrato'					=> 'Estado del Contrato',
		'estado_redeterminacion'	=> 'Redeterminaciones',
	],

	'error' => [
		'hasta_anterior_desde' 	=> 'Rango de fechas incorrecto: "Fecha Hasta" no puede ser anterior a la "Fecha Desde".',
		'no_data'								=> 'No se encontraron resultados que coincidan con los filtros seleccionados',
		'revisar'								=> 'Errores al completar los filtros',		
		'fecha_hasta_financiero' 	=> 'Rango de fechas incorrecto: "Fecha Hasta" no puede ser anterior a la Fecha Actual.',
	],
	'encabezados' => [
		'ReporteEconomico'				=> [
			'anticipo'	=> 'Anticipo',
			'basico'	=> 'Básico',
			'redeterminado'	=> 'Redeterminado*',
			'vigente'	=> 'Vigente*',
			'saldo_ejecutar'	=> 'Saldo a Ejecutar',
			'simbolo_peso'	=> '$',
			'simbolo_porcentaje'	=> '%',
			'planificado'	=> 'Planificado',
			'ejecutado'	=> 'Ejecutado',
			'desvio'	=> 'Desvío',
			'acumulado'	=> 'Acumulado',
			'anticipo'	=> 'Anticipo',
		],
		'ReporteFisico'						=> [
			'causante'	=> 'Causante',
			'inspector'	=> 'Inspector de Obra',
			'contratista'	=> 'Contratista',
			'estado'	=> 'Estado',
			'saldo_ejecutar'	=> 'Saldo a Ejecutar',
			'simbolo_peso'	=> '$',
			'simbolo_porcentaje'	=> '%',
			'basico'	=> 'Básico',
			'a_ejecutar'	=> 'A Ejecutar',
			'planificado'	=> 'Planificado ',
			'ejecutado'	=> 'Ejecutado',
			'desvio'	=> 'Desvío',
			'actual'	=> 'Actual',
			'acumulado'	=> 'Acumulado',
			'en_ejecucion'	=> 'En Ejecucion',
			'adjudicada'	=> 'Adjudicada',
			'recepcion_provisoria'	=> 'Recepcion Provisoria',
			'recepcion_definitiva' => 'Recepcion Definitiva',
		],
		'ReporteFinanciero'				=> [
            'simbolo_peso'	=> '$',
			'simbolo_porcentaje'	=> '%',
			'basico'	=> 'Básico',
			'a_ejecutar'	=> 'A Ejecutar',
			'planificado'	=> 'Planificado',
			'ejecutado'	=> 'Ejecutado',
			'desvio'	=> 'Desvío',
			'actual'	=> 'Actual',
			'acumulado'	=> 'Acumulado',
			'anticipo'	=> 'Anticipo',
			'redeterminado'	=> 'Redeterminado',
			'a_pagar'	=> 'A pagar',
			'mes_certificados'	=> 'Certificados del mes',
			'total' => 'Total',			
			'vigente'	=> 'Vigente',
			'saldo_a_ejecutar' => 'Saldo a Ejecutar'
		],
		'ReporteRedeterminaciones' => [
			'mes'							=> 'Mes',
			'estado'					=> 'Estado',
			'vr_polinomica'		=> 'VR Polinómica',
			'vr_definitivo'		=> 'VR Definitivo',
			'monto_vigente'		=> 'Monto Vigente',
			'mayor_gasto'			=> 'Incremento',
			'saldo'						=> 'Saldo',
		],
		'ReporteAdendas'					 => [
			'mes_oferta'					=> 'Mes Oferta',
			'mes_inicio'					=> 'Mes Inicio',
			'mes_ultima_aprobada'	=> 'Mes última redeterminación aprobada',
			'estado'							=> 'Estado',
			'monto_val_base'			=> 'Monto a valores Base',
			'vigente'							=> 'Vigente*',
			'saldo'								=> 'Saldo*',
		],
	],


	'penalidad' => [
		'desvio' 	=> 'Con Penalidad x Desvío',
		'45_dias'	=> 'Con Penalidad x Fecha',
		'sin'			=> 'Sin Penalidad'
	],


];
