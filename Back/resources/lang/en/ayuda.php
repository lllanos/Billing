<?php
return [
	'sistema_redeterminacion_precio' => [
		'titulo' => 'OCRE - Obras, Certificaciones y Redeterminaciones de Precios',
		'introduccion' => 'El sistema OCRE permite realizar la gestión de las contrataciones permitiendo conocer a toda la entidad estado de ejecuciones físicas, económicas y financieras.En su versión final la plataforma permite:',
		'lista_introduccion' => [
				0 => 'Gestionar las contrataciones: la gestión de contrataciones incluye: gestión de contratistas, gestión de contratos, itemizado, Plan de trabajo y polinómicas, modificaciones contractuales por adendas,ampliaciones y reprogramaciones, anticipos.',
				1 => 'Gestionar los certificados: la gestión de certificados incluye: Certificados básicos y redeteminados con su circuito de aprobación y el seguimiento de la curva de las obra original y desvíos.',
				2 => 'Gestionar los procesos de redeterminaciones de precios: para esto se incluye la carga del Análisis de precios, se realizan los cálculos de saltos en base a las polinómicas, se gestionan las solicitudes de redeterminación, se realizan los cálculos definitivos de redeterminaciones.',
				3 => 'Gestión de índices: para esto se incluye la carga de los indices a modo de mensual de publicaciones',
				4 => 'Emitir reportes: permite extraer informes sumarizados de los contratos.',
				],
		'pantalla_incio' => [
			'titulo' => 'Pantalla de Inicio',
			'descripcion' => 'La pantalla principal presenta Paneles o Widgets estadísticos. Desde aquí podrás configurar las distintas ubicaciones en su disposición. Podrás también mostrar u ocultar las vistas de gráficos.',
		],
		'paneles_widgets' => [
			'titulo' => 'Paneles o Widgets',
			'descripcion' => 'Estos paneles te permitirán tener rápidamente la información de las métricas más importantes sobre la gestión de las Solicitudes de Asociación a Contratos y de las Solicitudes de Redeterminaciones de Precios.'
		],
		// FIN 'paneles_widgets'
		// Widgets
		'solic_redeterminacion_estado' => [
			'titulo' => 'Panel de <strong>Solicitudes de Redeterminación</strong> por estado ',
			'descripcion' => 'Es un gráfico de Torta en el que se mostrarán las Solicitudes de Redeterminación que aún no han finalizado según sea el Distrito al que pertenezcas o bien te mostrará todas ellas si tu perfil no posee un distrito asignado.'
		],
		'dias_promedio_esperados' => [
			'titulo' => 'Panel de Días promedio vs. Días esperados ',
			'descripcion' => 'Es un gráfico de Barras que te mostrará los tiempos insumidos de las Solicitudes de Redeterminación por estado y la comparación de los mismos respecto a los distintos tiempos sugeridos.'
		],
		'contratos_por_estado' => [
			'titulo' => 'Panel de Contratos por Estado ',
			'descripcion' => 'Es un gráfico de Torta que muestra la cantidad de contratos por estado.'
		],
		'mis_asignaciones' => [
			'titulo' => 'Panel de Contratos asignados',
			'descripcion' => 'Es un listado que muestra los contratos que tenes asignados como inspector.'
		],
		// FIN Widgets
		'solic_asociacion' => [
			'titulo' => 'Panel de Solicitudes de Asociación ',
			'descripcion' => 'A través de este sencillo gráfico de barras podrás ver la cantidad de Solicitudes de Asociaciones a Contratos según sea su estado (Solicitudes Pendientes de Aprobación y Solicitudes Aprobadas).',
			'titlo_lista' => 'Para facilitarte la comprensión de los datos, los Widgets tienen un modo de lectura interactivo, esto te permitirá:',
			'lista' => [
				0 => 'Ver los valores totales o porcentajes representativos posicionando el mouse sobre las distintas regiones del gráfico.',
				1 => 'Mostrar u ocultar las distintas regiones de los gráficos haciendo clic con el mouse sobre las leyendas de las referencias.',
			],
			'alert' => 'Cada uno de los <strong>Widgets</strong> o <strong>Paneles</strong> posee un menú superior derecho con opciones para que puedas imprimirlos o descargarlos como imágenes en distintos formatos (.PNG; .JPG; PDF; o .SVG).',
			'vistas' => 'Vistas',
			'lista_vistas' => [
				0 => 'Con el botón IMG podés elegir las distintas disposiciones de ubicación de los paneles en la pantalla de Inicio.',
				1 => 'Con el botón IMG también podés optar por mostrar u ocultar cada uno de los paneles.',
			]
		],
		// FIN 'solic_asociacion'
	],
	// FIN 'sistema_redeterminacion_precio'
	'contratos' => [
		'titulo' => 'Contratos',
		'descripcion' => 'En la sección Contratos visualizarás el listado de los Contratos en forma detallada.',
		'titulo_lista' => 'Desde esta pantalla vas a poder:',
		'lista' => [
			0 => 'Buscar un Contrato por número de Contratación, Contrato, Denominación de la Obra, Contratista, Resolución de adjudicación o Expediente Madre. También podrás realizar búsquedas por cualquiera de los datos que se encuentra en el listado.',
			1 => 'Exportar o descargar el Listado de Contratos a un archivo Excel.',
			2 => 'Ver el detalle del Contrato.',
			3 => 'Editar datos del contrato.',
		],

		'titulo_lista_contratos' => 'En el <strong>Listado de Contratos</strong>, vas a encontrar cada contrato con la información principal del mismo. Va a poder ver:',
		'lista_contratos' => [
			0 => '<strong> Estado de Carga</strong>: Muestra en que estado está la carga del contrato. Al posicionarse sobre el ícono correspondiente se obtiene más detalle.',
			1 => '<strong> Datos del contrato </strong>',
			2 => '<strong>Nombre</strong>: Nombre de la Obra.',
			3 => '<strong>Importes Vigentes</strong>.',
			4 => '<strong>Estado del Contrato</strong>.',
			5 => '<strong>Último Salto</strong>: Mes y Año del último salto.',
			6 => '<strong>Última Solicitud</strong>: Fecha de la última Solicitud de Redeterminación realizada.',
		],
		'subtitulo_nuevo_contrato' => 'Nuevo Contrato',
		'titulo_nuevo_Contrato' => 'Con la opción de <strong>Nuevo Contrato</strong>, vas a poder crear nuevos contratos. Los contratos los podés crear como <strong>borrador </strong> sino conoces todos los datos y continuar con la carga después. Con el </strong>Guardar</strong> definitivo el sistema va a validar que los datos necesarios de un contrato se encuentren completos. En caso de que falte algún dato vas a encontrar un mensaje de error explicativo:',
		'nuevo_contratos' => [
			0 => '<strong> Datos Generales</strong>: Vas a poder completar los datos básicos',
			1 => '<strong> Empalme </strong>: te permite indicar si el contrato ya se encuentra iniciado y con posibles certificaciones y redeterminaciones.Con esta opción luego el sistema va a permitir que cargues los datos para dejar la ejecución del contrato al día',
			2 => '<strong>No Redetermina</strong>: Indica que este contrato no se redetermina. La opción de polinómica va a quedar deshabilitada',
			3 => '<strong>Importes</strong> Te permite completar los importes originales en cada una de las monedas.',
			4 => '<strong>Estado del Contrato.</strong>',
			5 => '<strong>Anticipo Financiero</strong>: Permite indicar el tipo de anticipo financiero que podrá ser concedido por esta contratación. El anticipo financiero otorgado luego se carga en el listado de anticipos.',
			6 => '<strong>Adjuntos</strong>: Va a poder adjuntar documentación realiva al contrato.',
		],
		'subtitulo_editar_contrato' => 'Editar Contrato',
		'titulo_editar_Contrato' => 'Con la opción de <strong>Editar Contrato</strong>, vas a poder editar contratos que se encuentren en borrador o editar contratos guardados de forma definitiva. Los contratos guardados de forma definitita sólo podrán modificarse por personas autorizadas y los datos a modificar son los que no impactan en la ejecución del contrato.',

		'subtitulo_editar_itemizado' => 'Itemizado',
		'titulo_editar_Itemizado' => 'Con la opción de <strong>Editar itemizado</strong>, vas a poder crear y editar el itemizado del contrato. El itemizado se completa por cada una de las monedas del contrato.',

		'titulo_editar_items' => 'Los ítems se agregan desde la opción <strong>(+)</strong> y pueden tener sub-items o ser ítems finales. En el caso de tener sub-items tendrás, luego, que crear los sub-items. En el caso de ser ítems finales, vas a tener que completar si el ítem es <strong>Global</strong> o por <strong>Unidad de Medida</strong>. En el caso de ser unidad de medida, tendrás que completar la unidad de medida de medición, la cantidad y el importe unitario. En el caso de ser global, sólo el importe total. Si el contrato es realizado por una UT, además, vas a tener que completar quien es el responsable de realizar ese ítem.<br>
		Los ítems se guardan de forma automática a medida que los vas cargando. Cuando hayas cargado todos los ítems y revisado, podrás darlos por finalizados con el botón de <strong>Guardar</strong>.<br>
		Una vez guardado el itemizado podrás:',

		'items_Completo' => [
			0 => 'Ver el <strong> Historial</strong>: Permite ver las modificaciones por cambios de estado que sufrió el itemizado',
			1 => '<strong> Descargar a Excel </strong>: .',
		],

		'subtitulo_editar_polinomica' => 'Polinómica',
		'titulo_editar_Polinomica' => 'Con la opción de <strong>Editar Polinómica</strong>, vas a poder cargar los insumos que forman parte de la polinómica del contrato. Esta polinómica se completa por cada una de las monedas. En cada moneda la suma del porcentaje de afectación de los insumos debe ser de 1. La polinómica se puede guardar como borrador o guardar como definitiva',

		'subtitulo_editar_plan' => 'Plan de Trabajo',
		'titulo_editar_Plan' => 'Con la opción de <strong>Editar Plan de trabajo</strong>, vas a poder completar la planificación de los ítems en los meses que dure el contrato. A medida que se va completando la planificación, la misma queda guardada. Una vez terminada la planificación es importante realizar una revisión para corroborar que los datos esten correctos. Una vez realizada la revisión se podrá <strong>Guardar</strong> el Plan de trabajo como definitivo. Una vez completo el plan de trabajo y la polinómica, el contrato cambiará su estado a <strong>Completo</strong>. A partir de este momento, se puede obtener más información del plan.<br> Los datos que se pueden obtener son:',

		'plan_Completo' => [
			0 => '<strong> Historial</strong>: Permite ver los cambios de estados sufrido por el contrato.',
			1 => '<strong> Visualizar en Porcentaje </strong>: permite ver el porcentaje de planificación de los ítems.',
			2 => '<strong>Visualizar en Moneda</strong>: Permite ver los montos a desembolsar de acuerdo con lo planificado por ítem.',
			3 => '<strong>Visualizar en UM </strong>: Permite ver el plan en la unidad de carga del mismo.',
			4 => '<strong>Curva de Inversión </strong>: Permite ver la curva de inversión planificada por cada una de las monedas.',
			5 => '<strong>Descargar excel</strong>: Permite descargar los datos que han sido completados en el plan.',

		],

		'titulo_Plan_porcentaje' => 'Con la opción de <strong>Visualizar en Porcentaje</strong>',

		'titulo_Plan_moneda' => 'Con la opción de <strong>Visualizar en Moneda</strong>',

		'titulo_Plan_curva' => 'Con la opción de <strong>Curva de Inversión</strong>',

		'titulo_contrato_completo' => 'Una vez completado el <strong> Plan de trabajo</strong> el contrato se considera completo y se pueden continuar actualizando los siguientes datos:',
		'contrato_Completo' => [
			0 => '<strong> Adendas</strong>: Adiciones o complementos añadidos al contrato',
			1 => '<strong> Ampliaciones y Reprogramaciones </strong>: cambios realizados en las planificaciones originales del contrato que no impactan en los ítems del mismo.',
			2 => '<strong>Anticipos</strong>: Permite cargar lo recibido por el contratista como anticipo',
			3 => '<strong>Certificaciones </strong>: Permite visualizar los certificados del contrato.',
			4 => '<strong>Empalme </strong>: Permite gestionar las redeterminaciones y certificaciones que se hicieron en forma anterior al sistema',
			5 => '<strong>Redeterminaciones</strong>: Permite ver los saltos del contrato y las redeterminaciones asociadas.',

		],

		'subtitulo_adendas' => 'Adendas',

		'titulo_nueva_adenda' => 'Con la opción de <strong>Nueva adenda</strong>, vas a poder modificar el contrato con las adiciones o complementos al mismo. Las adendas pueden ser: Ampliación o Certificación independiente. <br>
		Las <strong>Adendas de Ampliación</strong> contienen los datos generales de la adenda y además, la nueva duración y el nuevo monto a valores originales. Luego de completados estos datos y guardados como definitivos vas a poder modificar los ítems del contrato y la planificación. (el monto a valores actuales se calculará como un proceso posterior que revisa las redeterminaciones. <br>
		Las adendas de certificación independiente se trabajan como una contratación extra dentro del contrato. Por eso mismo tienen su propio itemizado y su propio plan de trabajo. Además, a la adenda de certificación independiente se les pueden incluir otras adendas de ampliación, reprogramaciones, etc., tal cual lo realizado en un contrato.<br>',

		'subtitulo_ampliacion_rep' => 'Ampliaciones y Reprogramaciones',
		'titulo_nueva_ampliacion_rep' => 'Con la opción de <strong>Solicitar Ampliación/reprogramación</strong>, vas a poder modificar el plan de trabajo de un contrato. <br>
		Con las <Strong> Ampliaciones </Strong> podrás extender la cantidad de meses del mismo. La duración a completar es el total que llevará el contrato. <br>
		Con las <strong>reprogramaciones </strong> podrás replanificar el trabajo de modo diferente. ',

		'subtitulo_anticipo' => 'Anticipos',
		'titulo_agregar_anticipo' => 'Con la opción de <strong>Agregar Anticipo</strong>, vas a poder completar cual fue el anticipo recibido por el contratista. El porcentaje que se debe completar es el total del anticipo que recibió el contratista. De modificarse por adendas o redeterminaciones se deberá completar el nuevo monto pagado y el porcentaje total de anticipo que representa. Este porcentaje se utilizará para realizar descuentos en los certificados posteriores  ',

		'subtitulo_empalme' => 'Empalme',
		'titulo_agregar_empalme' => 'En la sección de <strong>Empalme</strong>, vas a poder completar el avance de un contrato ya iniciado (redeterminado y certificado). Lo que se debe completar en estos casos es:',
		'lista_Empalme' => [
				0 => '<strong>Redeterminaciones</strong>: Permite completar los precios actualizados a la fecha de última redeterminación. Pueden cargarse cada una de las redeterminaciones ó puede cargarse solamente la última redeterminación. Es importante completar los datos de la redeterminación ya que el sistema empieza a realizar los cálculos a partir de estos valores cargados.,',
				1 => '<strong>Certificados</strong>: Permite cargar el avance certificado hasta la fecha. Esta opción permite cargar el avance en cada uno de los ítems y automáticamente calcula el importe básico asociado. De haberse generado el certificado básico con alguna diferencia (por realizar los calculos con otra herramienta) se puede adaptar el monto individual. También, permite indicar el monto generado por los certificados redeterminados. Estos datos se utilizaran para conocer los avances físicos y financieros.,',

			],

		'subtitulo_analisis_precios' => 'Análisis de Precios',
		'titulo_completar_analisis' => 'En la sección de <strong>Análisis de Precios</strong>, vas a poder completar por cada ítem su análisis de precios. Además, podrás completar el coeficiente K del análisis. El listado muestra un resumen del precio unitario cargado en el ítem y el costo multiplicado por el coeficiente K que resume el precio cargado en el análisis de precios. El coeficiente K lo podrás editar desde el botón de edición que se encuentra al lado del dato de Coeficiente K. El análisis de Precios pasa por dos <strong>instancias de aprobación</strong> primero por la de <strong>inspección de obras</strong> y luego por la <strong>comisión de redeterminaciones de precios</strong> que verifica los indicies asociados a cada componente. Por cada ítem se podrán cargar los siguientes componentes del Análisis de Precios:',
		'lista_Analisis_precios' => [
				0 => '<strong>Resumen</strong>: Permite ver un resumen de lo completado en el análisis de precios y datos del ítem particular.',
				1 => '<strong>Materiales</strong>: Permite cargar los costos de materiales que se utilizan para el desarrollo del ítem.',
				2 => '<strong>Ejecución</strong>: Permite cargar los costos de ejecución. La ejecución se divide en: Mano de Obra y Equipos. En caso de que el ítem sea por Unidad de Medida se puede cargar un rendimiento que puede ser por día o por hora. El rendimiento lo podrás cargar desde el botón de edición que se encuentra al lado de la ejecución.',
				3 => '<strong>Mano de Obra</strong>: Permite cargar el personal que estará asociado al desarrollo del ítem.',
				4 => '<strong>Equipos</strong>: Permite cargar los costos de equipamiento que se utilizarán para el desarrollo del ítem. Los equipamientos se dividen en: Máquinas y Equipos, Amortizaciones, Combustibles y Lubricantes y Reparaciones y Repuestos.',
				5 => '<strong>Máquinas y Equipos</strong>: Permite cargar las máquinas y equipos que van a ser utilizados para el desarrollo del ítem. Estas máquinas y equipos nos son considerados como costos directos sino que lo que se considera es; la amortización, las reparaciones y los repuestos y los combustibles y lubricantes derivados de su uso.',
				6 => '<strong>Amortizaciones</strong>: Permite completar los costos por amortizaciones y la descripción de como se llegó al cálculo.',
				7 => '<strong>Combustibles y Lubricantes</strong>: Permite cargar los costos en combustibles y lubricantes asociados a los equipos.',
				8 => '<strong>Reparaciones y Repuestos</strong>: Permite cargar los costos de reparaciones y repuestos asociados a los equipos. Se puede cargar la descripción de como se llega al cálculo.',
				9 => '<strong>Transporte</strong>: Permite cargar los costos en transporte.',


			],

		'subtitulo_Certificados' => 'Certificados',
		'titulo_certificados' => 'En la sección de <strong>Certificados </strong>, vas a poder completar el avance de mensual de los contratos. Lo que se debe completar es por cada uno de los ítems el avance acordado según las mediciones. El sistema automáticamente calcula los montos y los desvíos acumulados en base al plan de trabajo vigente. Cuando se realice el guardado borrador o definitivo se hace el cálculo de los totales. Además, para el guardado definitivo del certificado se deben completar los adjuntos guardados como definitivos:',
		'lista_certificados' => [
				0 => '<strong>Certificados Redeterminados </strong>: Permite ver los certificados redeterminados.',
				1 => '<strong>Descargar Certificado</strong>: Permitirá descargar el certificado para que pueda ser revisado y presentado junto con las facturas.,',
			],

		'asociacion_contratos_pendientes' => [
		'titulo' => 'Asociaciones de Contratos Pendientes',
		'descripcion' => 'En esta pantalla encontrarás el listado de <strong>Solicitudes de Asociaciones Pendientes</strong>, es decir, Solicitudes de Asociaciones a Contratos que no han sido Aprobadas ni Rechazadas por la <strong>EBY</strong>.
				<strong>Desde aquí podrás:</strong>',
				'lista' => [
					0 => 'Buscar un Contrato por Fecha de Solicitud, Número de Expediente Principal o Electrónico, Contratista, Distrito o por Último Movimiento realizado.',
					1 => 'Exportar o descargar el listado de Solicitudes de Asociaciones a Contratos Finalizadas',
					2 => 'Rechazar una Asociación a Contrato que ya se encuentra Aprobada.',
					3 => 'Ver más detalles de la Asociación a Contrato, presionando el botón <strong>Opciones</strong>. Desde aquí también visualizarás el estado de la Solicitud de Asociación, la que podrás <strong>Rechazar</strong>, presionando el botón ubicado en la parte superior de la pantalla.',
				],
		'titulo_lista_asoc' => 'En el listado de <strong>Asociaciones de Contratos Pendientes</strong>, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_asoc' => [
				0 => '<strong>Fecha de Solicitud</strong>: Fecha en la que fue efectuada la Solicitud de Asociación a Contrato.,',
				1 => '<strong># Exp. Madre.</strong>: Es el número de Expediente Principal o número de Expediente Electrónico.,',
				2 => '<strong>Contratista</strong>: Nombre, Apellido y Documento de identificación del Contratista.,',
				3 => '<strong>Distrito</strong>: Distrito al que pertenece la Solicitud de Asociación al Contrato de la Obra.,',
				4 => '<strong>Último Mov.</strong>: Fecha del último movimiento efectuado,',
			],
			'detalles_solic_asoc_contratos_pendientes' => 'Ver Detalles de las <strong>Solicitudes de Asociación de Contratos Pendientes</strong>',
			'detalles_solic_asoc_contratos_pendientes_descrip' => 'Presionando el botón <strong>Opciones</strong> y luego <strong>Ver</strong>, el Sistema de mostrará todos los datos asociados a la Solicitud de Asociación. Podrás ver también el Árbol de Datos o Historial, situado a la derecha de la pantalla, indicando el estado en el que se encuentra la Solicitud y desde aquí también podrás <strong>Aprobar</strong> o <strong>Rechazar</strong> la misma a través de los botones de acceso rápido.',
		],
		// FIN 'asociacion_contratos_pendientes'
		'asociacion_contratos_finalizadas' => [
			'titulo' => 'Asociaciones de Contratos Finalizadas',
			'descripcion' => 'En esta pantalla encontrarás el listado de <strong>Solicitudes de Asociaciones Finalizadas</strong>, es decir, Solicitudes de Asociaciones a Contratos que han sido Aprobadas por la <strong>EBY</strong> .
				<strong>Desde aquí podrás:</strong>',
				'lista' => [
					0 => 'Buscar un Contrato por Fecha de Solicitud, Número de Expediente Principal o Electrónico, Contratista, Distrito o por Último Movimiento realizado.',
					1 => 'Exportar o descargar el listado de Solicitudes de Asociaciones a Contratos Finalizadas',
					2 => 'Rechazar una Asociación a Contrato que ya se encuentra Aprobada.',
					3 => 'Ver más detalles de la Asociación a Contrato, presionando el botón <strong>Opciones</strong>. Desde aquí también visualizarás el estado de la Solicitud de Asociación, la que podrás <strong>Rechazar</strong>, presionando el botón ubicado en la parte superior de la pantalla.',
				],
				'titulo_lista_asoc_contratos_fin' => 'En el listado de <strong>Asociaciones de Contratos Finalizadas</strong>, cada registro de la tabla te mostrará los siguientes datos:',
				'lista_asoc_contratos_fin' => [
					0 => '<strong>Fecha de Solicitud</strong>: Fecha en la que fue efectuada la Solicitud de Asociación a Contrato.',
					1 => '<strong># Exp. Madre.</strong>: Es el número de Expediente Principal o número de Expediente Electrónico.',
					2 => '<strong>Contratista</strong>: Nombre, Apellido y Documento de identificación del Contratista.',
					3 => '<strong>Distrito</strong>: Distrito al que pertenece la Solicitud de Asociación al Contrato de la Obra.',
					4 => '<strong>Estado</strong>: Indica el estado en el que se encuentra la Solicitud de Asociación a Contrato.',
					5 => '<strong>Último Mov.</strong>: Fecha del último movimiento efectuado',
				],
				'titulo_detalle' => 'Ver Detalles de las <strong>Solicitudes de Asociación de Contratos Finalizadas</strong>',
				'descripcion_detalle' => 'Presionando el botón <strong>Opciones</strong> y luego <strong>Ver</strong>, el Sistema de mostrará todos los datos asociados a la Solicitud de Asociación. Podrás ver también el Árbol de Datos o Historial, situado a la derecha de la pantalla, indicando el estado en el que se encuentra la Solicitud y desde aquí también podrás <strong>Rechazar</strong> la misma a través del botón de acceso rápido.'
		],
		// FIN 'asociacion_contratos_finalizadas'
		'aprobar_solic_asoc_contrato' => [
			'titulo' => 'Aprobar una Solicitud de Asociación a Contrato',
			'descripcion' => 'El Sistema OCRE te permitirá Aprobar una Asociación a Contrato que ha sido solicitada por el Contratista. Toda vez que un Contratista solicite una asociación a Contrato desde la Web, verás reflejada dicha solicitud como un nuevo registro en la tabla de la sección Asociaciones de Contratos Pendientes. Existen dos modos para aprobar una Solicitud, simplemente deberás seguir los siguientes pasos:',
			'modulo_1' => '(Modo 1)',
			'titulo_lista_aprobar_solic_asoc_contrato' => 'Pasos para Aprobar una Solicitud de Asociación a Contrato',
			'lista_modulo_1' => [
				0 => 'En la sección <strong>Asociaciones de Contratos Pendientes</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Contrato que quieras Aprobar.',
				2 => 'Luego, presioná la opción <strong>Aprobar</strong> del menú desplegable.',
				3 => 'Seleccioná las casillas de los requisitos obligatorios para la aprobación de la Solicitud de Asociación.',
				4 => 'Ingresá el Número de <strong>GDE</strong>.',
				5 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'modulo_2' => '(Modo 2)',
			'lista_modulo_2' => [
				0 => 'En la sección <strong>Asociaciones de Contratos Pendientes</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Contrato que quieras Aprobar.',
				2 => 'Luego, presioná la opción <strong>Ver</strong> del menú desplegable.',
				3 => 'Seleccioná las casillas de los requisitos obligatorios para la aprobación de la Solicitud de Asociación.',
				4 => 'Ingresá el Número <strong>GDE</strong>.',
				5 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'nota' => 'La <strong>Solicitud de Asociación</strong> a <strong>Contrato</strong> que ha sido aprobada podrás visualizarla en la sección: <strong>Asociaciones de Contratos Finalizadas</strong>.',
			'alert' => 'Una vez <strong>Aprobada</strong> la <strong>Asociación a Contrato</strong>, automáticamente el Sistema le enviará un mail al Contratista para informarle que su Solicitud de Asociación a Contrato ha sido Aprobada con éxito por la <strong>EBY</strong>.'
		],
		// FIN 'aprobar_solic_asoc_contrato'
		'rechazar_solic_asoc_contrato' => [
			'titulo' => 'Rechazar una Solicitud de Asociación a Contrato',
			'descripcion' => 'El Sistema OCRE te permitirá Rechazar una Asociación a Contrato que ha sido Aprobada.<br>
								Podrás realizar esta acción de los siguientes dos modos:',
			'modulo_1' => '(Modulo 1)',
			'titulo_pasos_rechazar' => 'Pasos para Rechazar una Solicitud de Asociación a Contrato',
			'lista_modulo_1' => [
				0 => 'En la sección <strong>Asociaciones de Contratos Finalizadas</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Contrato que quieras Rechazar.',
				2 => 'Luego, presioná la opción <strong>Rechazar</strong> del menú desplegable.',
				3 => 'Deberás completar el motivo por el que se Rechaza la Solicitud de Asociación.',
				4 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'modulo_2' => '(Modulo 2)',
			'lista_modulo_2' => [
		 		0 => 'En la sección <strong>Asociaciones de Contratos Finalizadas</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Contrato que quieras Rechazar.',
				2 => 'Luego, presioná la opción <strong>Ver</strong> del menú desplegable.',
				3 => 'Presioná el botón <strong>Rechazar</strong>, ubicado en la parte superior derecha de la página.',
				4 => 'Ingresá el Motivo por el cual la solicitud de Asociación a Contrato es Rechazada',
				5 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'nota' => 'La Solicitud de Asociación a Contrato que ha sido rechazada podrás visualizarla en la sección: <strong>Asociaciones de Contratos Finalizadas</strong>.<br>
								 Una solicitud de asociación Rechazada puede ser Aprobada nuevamente.'
	],
	],
	// FIN 'contratos'
	// FIN 'rechazar_solic_asoc_contrato'
	'solicitudes' => [
		'titulo' => 'Solicitudes ',
		'descripcion' => 'La sección <strong>Solicitudes</strong> contiene los listados y detalles de las Solicitudes de Redeterminación y las solicitudes para aprobar los certificados. Las solicitudes de redeterminación se pueden ver en dos listados  Proceso y Finalizadas. Las <strong>Solicitudes en Proceso</strong> son todas las solicitudes tramitadas e iniciadas por los Contratistas. Las <strong>Solicitudes Finalizadas</strong> son todas aquellas solicitudes que han sido <strong>Aprobadas</strong>.',
		'solicitudes_redet_proceso' => [
			'titulo' => 'Solicitudes de Redeterminaciones en Proceso',
			'descripcion' => 'En esta sección encontrarás todas las solicitudes tramitadas e iniciadas por los Contratistas. <br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Buscar una Redeterminación por Expediente Madre, Nro. de Expediente, Moneda, Fecha de Solicitud, Contratista, Estado, Último Movimiento o por Causante.',
				1 => 'Exportar o descargar el listado de las Solicitudes de Redeterminaciones en Proceso a un archivo Excel.',
				2 => 'Ver más detalles de las Redeterminaciones en Proceso, presionando el botón Opciones.',
			],
			'titulo_lista_solic_redet' => 'En el listado de las <strong>Solicitudes de Redeterminaciones en Proceso</strong>, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_solic_redet' => [
				0 => '<strong># Expediente Madre.</strong>: Es el número de Expediente Principal o número de Expediente Electrónico.',
				1 => '<strong>Solicitud</strong>: Indica la Fecha en la que el Contratista solicitó la Redeterminación de Precios.',
				2 => '<strong>Expediente</strong>: Número de Expediente con el que se tramita la solicitud',
				3 => '<strong>Contratista</strong>: Nombre de la empresa Contratista.',
				4 => '<strong>Salto</strong>: Nombre de la Obra seguido del Mes y Año en el que se produjo el salto.',
				5 => '<strong>Estado</strong>: Es el estado en el que se encuentra la Solicitud de Redeterminación. Aquí podrás observar, a medida que se van realizando los pasos, los distintos estados por los que atraviesa la Solicitud de RDP.',
				6 => [
					0 => 'Esperando... <strong>Aprobación de Certificados</strong>.',
					1 => 'Esperando... <strong>Verificación de Desvíos</strong>.',
					2 => 'Esperando... <strong>Calculo de Precios Redeterminados</strong>.',
					3 => 'Esperando... <strong>Generación de Expediente Electrónico</strong>.',
					4 => 'Esperando... <strong>Asignación de Partida Presupuestaria</strong>.',
					5 => 'Esperando... <strong>Generación de Proyecto Acta RDP, Resolución e Informe</strong>.',
					6 => 'Esperando... <strong>Firma de Resolución Aprobatoria</strong>.',
					7 => 'Esperando... <strong>Emisión de Certificados</strong>.',
					8 => '<strong>Aprobada</strong>... (El proceso ha finalizado y la Solicitud de RDP fue Aprobada)',
				],
				8 => '<strong>Último Mov.</strong>: Muestra el último movimiento que se ha realizado sobre la Solicitud de RDP.',
				9 => '<strong>Causante</strong>: Indica el causante al que pertenece el contrato de la Solicitud de Redeterminación de Precios.',
			],
			'nota' => 'Podés ver todos los datos detallados y pasos que comprenden el Circuito de Redeterminación de Precios haciendo clic sobre el botón <strong>Opciones</strong> de la Redeterminación en proceso.',
			'detalle' => [
				'titulo' => 'Detalle de la redeterminación de precios',
				'descripcion' => 'En el detalle de la redeterminación de precios encontras los datos de salto, moneda, solicitante, contrato, etc.Además, el estado en el que se encuentra y los datos cargados en cada paso y en caso de que puedas realizar el siguiente paso tendrás habilitada la acción correspondiente. A continuación se muestra la pantalla de detalle de la redeterminación de precios.',
				'circuito' => 'A continuación se describe el circuito que deben seguir la redeterminaciones de precios hasta su aprobación',
				'lista_circuito' => [
					0 => '<strong>Aprobación de Certificados</strong>: la redeterminación se detiene en este estado cuando faltan las certificaciones necesarias para poder determinar los porcentajes a certificar en ese mes. Se deben cargar los certificados para poder avanzar en este paso.',
					1 => '<strong>Verificación de Desvíos</strong>: la redeterminación queda a la espera de que el inspector verifique debido a qué fueron los desvíos del plan de trabajo con la certificación e indique si es correspondiente aplicar multa.',
					2 => '<strong>Calculo de Precios Redeterminados</strong>: La redeterminación queda en este estado para que se procesen los datos y se realice el cálculo. Este paso se hace de forma automática. En caso de existir algún inconveniente para poder realizar el cálculo el detalle se muestra en rojo en el paso.',
					3 => '<strong>Generación de Expediente Electrónico</strong>: Personal la comisión de redeterminaciones debe cargar el expediente electrónico asociado.',
					4 => '<strong>Asignación de Partida Presupuestaria</strong>: Personal de presupuesto debe completar la partida presupuestaria asignada a cubrir la redeterminación.',
					5 => '<strong>Generación de Proyecto Acta RDP, Resolución e Informe</strong>: Personal de la comisión de precios debe completar los datos de los templates para poder generar los proyectos de los documentos. Los mismos podrán ser descargados una vez generados.',
					6 => '<strong>Firma de Resolución Aprobatoria</strong>: El inspector de obra debe completar cuando se haya llevado acabo la resolución aprobatoria.',
					7 => '<strong>Emisión de Certificados</strong>: este paso se genera de forma automática. Se emiten los certificados redeterminados desde el actual mes de la redeterminación en adelante.',
					8 => '<strong>Aprobada</strong>: El proceso ha finalizado y la Solicitud de RDP se marca como aprobada',
				],
				'nota' => 'Los usuarios habilitados podrán recibir alertas para que realicen los pasos que les corresponden en el circuito. En caso de querer ser notificado de las redeterminaciones de precios, realiza la solicitud para estar informado',
			],
		],
		'solicitudes_redet_finalizadas' => [
			'titulo' => 'Solicitudes de Redeterminaciones Finalizadas',
			'descripcion' => 'En la sección <strong>Redeterminaciones Finalizadas</strong> podrás ver todas las solicitudes que han sido <strong>Aprobadas</strong>, Rechazadas o aquellas que por algún motivo se encuentran <strong>Suspendidas</strong>.<br> Desde esta pantalla vas a poder:',
			'lista' => [
				0 => 'Buscar una Redeterminación por Número de Expediente Principal o Electrónico, Tipo de Obra, Fecha de Solicitud, Número de Expediente GDE, Contratista, Estado, Último Movimiento o por Distrito.',
				1 => 'Exportar o descargar el listado de las Solicitudes de Redeterminaciones Finalizadas a un archivo Excel.',
				2 => 'Ver más detalles de las Redeterminaciones Finalizadas, presionando el botón Opciones.',
			],
			'titulo_lista_solic_redet_finalizadas' => 'En el listado de las <strong>Solicitudes de Redeterminaciones Finalizadas</strong>, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_solic_redet_finalizadas' => [
				0 => '<strong># Expediente Madre.</strong>: Es el número de Expediente Principal o número de Expediente Electrónico.',
				1 => '<strong>Solicitud</strong>: Indica la Fecha en la que el Contratista solicitó la Redeterminación de Precios.',
				2 => '<strong>Expediente</strong>: Número de Expediente con el que se tramita la solicitud',
				3 => '<strong>Contratista</strong>: Nombre de la empresa Contratista.',
				4 => '<strong>Salto</strong>: Nombre de la Obra seguido del Mes y Año en el que se produjo el salto.',
				5 => '<strong>Estado</strong>: Es el estado en el que se encuentra la Solicitud de Redeterminación. Aquí podrás observar, a medida que se van realizando los pasos, los distintos estados por los que atraviesa la Solicitud de RDP.',
				7 => [
					0 => 'Aprobada.',

				],
				8 => '<strong>Último Mov.</strong>: Muestra el último movimiento que se ha realizado sobre la Solicitud de RDP.',
				9 => '<strong>Distrito</strong>: Indica el Distrito al que pertenece la Solicitud de Redeterminación de Precios.',
			],
			'nota' => 'Podés ver todos los datos detallados y pasos que comprenden el Circuito de Redeterminación de Precios haciendo clic sobre el botón <strong>Opciones</strong> de la Redeterminación Finalizada.'
		],
		// FIN 'solicitudes_redet finalizadas'
		'solicitudes_certif_proceso' => [
			'titulo' => 'Solicitudes de Certificación en Proceso',
			'introduccion' => 'Las solicitudes de Certificación en Proceso son los trámites de certificación que se encuentran en curso. Estos trámites siguen el siguiente circuito hasta ser aprobados:',
			'lista_introduccion' => [
				0 => '<strong>1.1 Borrador:</strong> El contratista crea el certificado. Puede guardar como borrador lo que lo dejará en el mismo estado estado ó enviar para la aprobación de la EBY. El contratista podrá editar el certificado mientras se encuentre en estado borrador. Las opciones de creación se encuentra descriptas en: 1.1.1 Crear certificado básico y 1.1.2 Crear certificado redeterminado. La opción de edición se encuentra descripta en: 1.1.6 Editar un certificado borrador ó un certificado a corregir',
				1 => '<strong>1.2 Borrador:</strong> El inspector de la EBY crea el certificado. Puede guardar como borrador lo que lo dejará en el mismo estado o guardarlo lo que lo dejará ya aprobado. El inspector podrá editar el certificado mientras se encuentre en estado borrador. Las opciones de creación se encuentra descriptas en: 1.2.1Crear certificado básico. La opción de edición se encuentra descripta en: 1.2.5 Editar un certificado no aprobado.',
				2 => '<strong>2.1 En trámite:</strong> Un certificado en trámite se puede visualizar desde la bandeja de certificados del contratista (1.2.2 Bandeja de Certificados). El inspector lo puede aprobar (1.2.6 Aprobar certificado básico y 1.2.7 Aprobar certificado Redeterminado), puede indicar que necesita corrección (1.2.8 Rechazar Certificado) ó lo puede modificar (1.2.5 Editar un certificado no aprobado).',
				3 => '<strong>2.2 A Corregir:</strong> Un certificado se encuentra en este estado cuando el inspector revisó el certificado y encontró algo que necesita ser corregido. El contratista deberá editar el certificado y enviar para aprobación. La opción de edición se encuentra descripta en : 1.1.6 Editar un certificado borrador ó un certificado a corregir',
				4 => '<strong>3.1 Aprobado:</strong> Un certificado aprobado se visualiza desde el contrato (1.2.4 Listado de Certificados del Contrato) ó desde la bandeja de certificados aprobados (1.2.3 Listado de Certificados aprobados). De estos certificados sólo se pueden ver los datos cargados.',
			],
			'descripcion' => 'En esta sección encontrarás todas las solicitudes de certificación tramitadas e iniciadas por los Contratistas. <br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Buscar una solicitud de Certificación por Fecha, Expediente Madre, Nro. de solicitud, Moneda, tipo de Certificado y Estado.',
				1 => 'Ver más detalles del certificado en Proceso, presionando el botón Ver.',
				2 => 'Aprobar el certificado.',
				3 => 'Rechazar el certificado. Indicando que se debe corregir para que este se considere aprobado.',
			],
			'titulo_lista_solic_certif' => 'En el listado de las <strong>Solicitudes de Certificación en Proceso</strong>, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_solic_certif' => [
				0 => '<strong>Fecha</strong>: Es la fecha en la el contratista envió el certificado',
				1 => '<strong># Expediente Madre.</strong>: es el expediente madre del contrato',
				2 => '<strong>Nro. Certificado</strong>: Número y Mes del certificado',
				3 => '<strong>Tipo</strong>: Tipo de Certificado, puede ser básico o redeterminado.',
				4 => '<strong>Estado</strong>: Es el estado en el que se encuentra la Solicitud de Redeterminación. Aquí podrás observar, a medida que se van realizando los pasos, los distintos estados por los que atraviesa la Solicitud de RDP.',
				5 => [
					0 => 'Esperando... <strong>En trámite</strong>.',
					1 => 'Esperando... <strong>Emitido</strong>.',
				],
			],
		],
		// FIN 'solicitudes_cert_en proceso'
		'solicitudes_certif_finalizadas' => [
			'titulo' => 'Solicitudes de Certificación Finalizadas',
			'introducción' => 'Las solicitudes de Certificación en Finalizadas son las solicitudes tramitadas por el contratista que fueron aprobadas por el/los representantes ténicos de la EBY.',
			'descripcion' => 'En esta sección encontrarás todas las solicitudes de certificación finalizadas e iniciadas por los Contratistas. <br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Buscar una solicitud de Certificación por Fecha, Expediente Madre, Nro. de solicitud, Moneda, tipo de Certificado y Estado.',
				1 => 'Ver más detalles del certificado en Aprobado, presionando el botón Ver.',
			],
			'titulo_lista_solic_certif' => 'En el listado de las <strong>Solicitudes de Certificación en Finalizadas</strong>, podrás ver los siguientes datos:',
			'lista_solic_certif' => [
				0 => '<strong>Fecha</strong>: Es la fecha en la el contratista envió el certificado',
				1 => '<strong># Expediente Madre.</strong>: es el expediente madre del contrato',
				2 => '<strong>Nro. Certificado</strong>: Número y Mes del certificado',
				3 => '<strong>Tipo</strong>: Tipo de Certificado, puede ser básico o redeterminado.',
				4 => '<strong>Estado</strong>: Es el estado en el que se encuentra la Solicitud de Redeterminación. Aquí podrás observar, a medida que se van realizando los pasos, los distintos estados por los que atraviesa la Solicitud de RDP.',
				5 => [
					0 => 'Esperando... <strong>Aprobada</strong>.',
				],

			],
		],
		// FIN 'solicitudes_cert_finalizadas'
	],
	// FIN 'solicitudes'
	'indices' => [
		'titulo' => 'Indices',
		'descripcion' => 'En la Sección de Índices vas a poder gestionar las Publicaciones de los Índices y visualizar las dos vistas Valores y Fuentes. De un modo sencillo podrás realizar las siguientes operaciones:',
		'lista' => [
			0 => 'Crear un nuevo índice. Al momento de la creación del indice vas a poder elegir desde que fecha tenes valores para el índice. En ese caso, deberás completar los valores para cada una de esas fecha y al momento de la publicación los índices tendrán los valores',
			1 => 'Ocultar índices para que no sean visibles en las publicaciones.',
			2 => 'Visualizar una Publicación que ya se encuentra publicada y acceder a su historial.',
			3 => 'Crear una nueva publicación',
			4 => 'Guardar una publicación en modo Borrador para poder editarla en cualquier momento.',
			5 => 'Enviar una Publicación para su Aprobación.',
		],
		'listado_publicaciones' => [
			'titulo' => 'Listado de Publicaciones',
			'descripcion' => 'En esta pantalla podrás visualizar el listado de todas la Publicaciones y las fechas en las que fueron realizadas. Gestionar las nuevas publicaciones y editar los Borradores de las Publicaciones.<br>También podrás:',
			'lista' => [
				0 => 'Realizar una búsqueda por: Mes del Índice, Estado, Usuario que realizó la publicación y Fecha de Publicación.',
				1 => 'Exportar o descargar el <strong>Listado de Publicaciones</strong> a un archivo Excel.',
			],
			'circuito' => 'Las publicaciones siguen el procedimiento descripto debajo hasta que son publicadas',
			'proceso' => 'Luego de la publicación de los índices se dispara un procedimiento que calcula que contratos tienen saltos esos meses.A partir de que se detectan los saltos, los contratistas quedan habilitados para solicitar la redeterminación de precios. A continuación se muestra una imagen en la que se ve es circuito realizado hasta la redeterminación de precios',
		],
		'tabla_i_valores' => [
			'titulo' => 'Valores',
			'descripcion' => 'Esta pantalla te presentará los valores de los índices mes a mes categorizados en listas desplegables y filtrados por año de publicación.<br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Filtrar la <strong>Valores</strong> por año de publicación.',
				1 => 'Realizar una búsqueda por: Categoría, Subcategoría, Número de índice, Nombre de índice y Valor del índice.',
				2 => 'Ver los índices no publicados referenciados con color (gris oscuro).',
				3 => 'Exportar o descargar la <strong>Valores</strong> a un archivo Excel.',
				4 => 'Acceder a la <strong>Fuentes</strong> mediante el botón de acceso directo ubicado en la parte superior de la página. ',
			],
		],
		'tabla_i_fuentes' => [
			'titulo' => 'Fuentes',
			'descripcion' => 'Esta pantalla te presentará  los índices y sus fuentes categorizados en listas desplegables y filtrados por períodos de publicación.<br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Filtrar la <strong>Fuentes</strong> por Períodos de publicación.',
				1 => 'Realizar una búsqueda por: Categoría, Subcategoría, Número de índice, Nombre de índice, Aplicación, Fuente y Valor del índice.',
				2 => 'Ver los índices no publicados referenciados con color (gris oscuro).',
				3 => 'Exportar o descargar la <strong> Fuentes</strong> a un archivo Excel.',
				4 => 'Acceder a la <strong>Valores</strong> mediante el botón de acceso directo ubicado en la parte superior de la página.',
			]
		]
	],
	// FIN 'indices'
	'contratistas' => [
		'titulo' => 'Contratistas',
		'descripcion' => 'En la sección <strong>Contratistas</strong> tendrás a disposición el Listado de todos los usuarios Contratistas que se han registrado en el Sistema.<br><strong>Desde aquí podrás:</strong>',
		'lista' => [
			0 => '<strong>Buscar </strong> un Contratista por Fecha de Registro, Nombre, CUIT/CUIL o por Correo electrónico.',
			1 => '<strong>Exportar o Descargar</strong> el listado de los Contratistas a un archivo Excel.',
			2 => '<strong>Ver Detalles </strong> del Contratista como sus datos, solicitudes y contratos.',
		],
		'titulo_lista_contratistas' => 'En el listado de los <strong>Contratistas</strong>, cada registro de la tabla te mostrará los siguientes datos:',
		'lista_contratistas' => [
			0 => '<strong>Razón Social / Nombre y Apellido</strong>',
			1 => '<strong>Tipo Contratista</strong>: Muestra si es Persona Física, Jurídica o UT.',
			2 => '<strong>Tipo y N° de documento</strong>: Tipo de documentación (CUIT/RUC/DNI etc.) y Número.',
			4 => '<strong>Correo Electrónico</strong>: Dirección de correo electrónico del Contratista',
			3 => '<strong>Nombre Fantasía</strong>: ',
		],
		'nota' => 'También podés ver más Detalles acerca del Contratista haciendo clic sobre el botón Opciones ubicado en la última columna de cada registro. En esta pantalla podrás observar los datos a detalle y también si pertenece a una UT ó los integrantes de la misma.<br>
		Si quisiera editar alguno de los datos del contratista lo tendras disponible desde la opción <strong>Editar</strong>',

		'titulo_nuevo_contratista' => 'Con la opción de <strong> Nuevo Contratista</strong> vas a poder cargar los contratistas sobre los que tenes contratos. En la carga vas a tener que completar los siguientes datos:',
		'lista_nuevo_contratistas' => [
			0 => '<strong>Tipo Contratista</strong>: Muestra si es Persona Física, Jurídica o UT. Luego en base a esto se desplegaran las opciones según el tipo',
			1 => '<strong>Datos del Contratista</strong>.',
			2 => '<strong>Datos de Contacto</strong>: Como dirección, email. y teléfonos.',
			4 => '<strong>Integrantes</strong>: Para el caso que sea UT. La UT deberá tener al menos un integrante y deberá estar creado como contratista previamente',
		],
	],
	// FIN 'contratistas'
	'reportes' => [
		'titulo' => 'Reportes',
		'descripcion' => 'En la sección <strong>Reportes</strong> tendrás a disposición el Listado de los reportes disponibles según tu perfil.<br><strong>Desde aquí podrás obtener los siguientes reportes:</strong>',
		'economico' => [
			'titulo' => 'Reporte Económico',
			'descripcion' => 'El reporte económico permite obtener un resumen económico de los contratos y las certificaciones independientes',
			'nota' => 'El reporte muestra los contratos que se encuentran completos y se exporta en formato excel.',
			],
		'fisico' => [
			'titulo' => 'Reporte Físico',
			'descripcion' => 'El reporte físico permite obtener el avance físico de los contratos. El avance físico se obtiene a partir de los certificados y montos básicos',
			'nota' => 'El reporte muestra los contratos que se encuentran completos y se exporta en formato excel.',
			],
		'financiero' => [
			'titulo' => 'Reporte Financiero',
			'descripcion' => 'El reporte ecónomico permite obtener un resumen financiero de los contratos. Se muestra un acumulado hasta el mes de inicio. En las columnas se muestra la fecha en la que se aprobó el certificado y la fecha en la que se proyecta que se realizará el pago (2 meses posteriores). En los meses anteriores a la fecha actual se muestra el ejecutado y en los meses posteriores se muestra el proyecto a ejecutar',
			'nota' => 'El reporte muestra los contratos que se encuentran completos y se exporta en formato excel.',
			],
		'adenda' => [
			'titulo' => 'Reporte de Modificaciones Contractuales',
			'descripcion' => 'El reporte de modificaciones contractuales permite visualizar el impacto de las modificaciones en los incrementos contractuales',
			'nota' => 'El reporte muestra los contratos que se encuentran completos y se exporta en formato excel.',
			],
		'redeterminacion' => [
			'titulo' => 'Reporte de Redeterminaciones',
			'descripcion' => 'El reporte de redeterminaciones permite visualizar el impacto de las redeterminaciones en los contratos. Los valores tomados en los contratos como vigentes corresponden a la redeterminaciones aprobadas',
			'nota' => 'El reporte muestra los contratos que se encuentran completos y se exporta en formato excel.',
			],
	],
	// FIN 'contratistas'

	'seguridad' => [
		'titulo' => 'Seguridad',
		'seguridad_usuarios' => [
			'titulo' => 'Seguridad - Usuarios',
			'descripcion' => 'Aquí encontrarás el listado de todos los usuarios de Gestión del Sistema y desde donde vas a poder administrarlos.<br>Desde esta sección podrás:',
			'lista' => [
				0 => 'Buscar un Usuario por Nombre, Apellido, Correo electrónico o por el Causante al cual pertenece.',
				1 => 'Exportar o descargar el listado de Usuario de Gestión del Sistema a un archivo Excel.',
				2 => 'Crear un Nuevo Usuario, asignarle un Rol determinado y un Causante.',
				3 => 'Editar un Usuario.',
				4 => 'Eliminar un Usuario.',
			],
			'titulo_lista_pasos_crear_usuario' => 'Pasos para <strong>Crear</strong> un Nuevo Usuario',
			'lista_pasos_crear_usuario' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Usuarios</strong>.',
				1 => 'Presioná el botón <strong>Nuevo Usuario</strong>.',
				2 => '<strong>Nombre (*)</strong>: Ingresá el nombre del Usuario.',
				3 => '<strong>Apellido (*)</strong>: Ingresá el Apellido del Usuario.',
				4 => '<strong>Correo Electrónico (*)</strong>: Colocá su dirección de e-mail.',
				5 => '<strong>Roles</strong>: Seleccioná un Rol de la lista desplegable. (Los Roles deben haber sido previamente cargados).',
				6 => '<strong>Causante</strong>: Seleccioná un Causante de la lista desplegable.',
				7 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_pasos_editar_usuario' => 'Pasos para <strong>Editar</strong> un Usuario',
			'lista_pasos_editar_usuario' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Usuarios</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Usuario que quieras Editar.',
				2 => 'Luego, presioná la opción <strong>Editar</strong> del menú desplegable.',
				3 => 'Modificá los datos que desees.',
				4 => 'Finalmente presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_pasos_eliminar_usuario' => 'Pasos para <strong>Eliminar</strong> un Usuario',
			'lista_pasos_eliminar_usuario' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Usuarios</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Usuario que quieras Eliminar.',
				2 => 'Luego, deberás confirmar la Eliminación del usuario presionando SI o NO.',
			]
		],
		// FIN 'seguridad_usuarios'
		'seguridad_roles' => [
			'titulo' => 'Seguridad - Roles',
			'descripcion' => 'Esta es la sección de <strong>Roles</strong> donde vas a poder crear distintos roles. Cada Rol comprende un conjunto de permisos asignados al usuario que permiten delimitar su contexto de acción respecto a las funcionalidades que posee el Sistema.<br>Desde esta pantalla podrás:',
			'lista' => [
				0 => 'Buscar un Rol por el nombre.',
				1 => 'Exportar o descargar el listado de Roles del Sistema a un archivo Excel.',
				2 => 'Crear un Nuevo Rol.',
				3 => 'Editar un Rol.',
				4 => 'Eliminar un Rol',
			],
			'titulo_lista_nuevo_rol' => 'Pasos para <strong>Crear</strong> un Nuevo Rol',
			'lista_nuevo_rol' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Roles</strong>.',
				1 => 'Presioná el botón <strong>Nuevo Rol</strong>.',
				2 => '<strong>Nombre (*)</strong>: Ingresá el nombre del nuevo Rol.',
				3 => 'Seleccioná los permisos que le serán asignados al Usuario que posea dicho Rol.',
				4 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_editar_rol' => 'Pasos para <strong>Editar</strong> un Rol',
			'lista_editar_rol' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Roles</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Rol que quieras Editar.',
				2 => 'Luego, presioná la opción <strong>Editar</strong> del menú desplegable.',
				3 => 'Modificá los datos que desees.',
				4 => 'Finalmente presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_eliminar_rol' => 'Pasos para <strong>Eliminar</strong> un Rol',
			'lista_eliminar_rol' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Roles</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro del Rol que quieras Eliminar.',
				2 => 'Luego, deberás confirmar la Eliminación del Rol presionando SI o NO.',
			]
		]
		// FIN 'seguridad_roles'
	],
	// FIN 'seguridad'
	'configuracion' => [
		'titulo' => 'Configuración',
		'descripcion' => 'En la pantalla de <strong>Configuración</strong> encontrarás 2 secciones (Configuración y Procesos) que te brindarán opciones de gestión de Usuarios y Roles, Motivos, Procesos y Alarmas .',
		'configuracion_distritos' =>[
			'titulo' => 'Configuración – Motivos',
			'descripcion' => 'En esta pantalla se encuentran todos los <strong>Motivos</strong> que pueden ser seleccionados como motivos de reprogramación. De cada <strong>Motivo</strong> se muestra la descripción y la responsabilidad.<br><strong>Desde aquí podrás:</strong>',
			'lista' => [
				0 => 'Buscar un Motivo por descripción o responsabilidad',
				1 => 'Exportar o descargar el listado de Motivos a un archivo Excel.',
			]
		],
		'procesos' => [
			'titulo' => 'Procesos ',
			'descripcion' => 'En esta sección encontrarás el listado de los procesos programados que ejecuta automáticamente el Sistema.<br>Podrás visualizar una tabla con los siguientes datos:',
			'lista' => [
				0 => '<strong>Clasificación</strong>: Indica la categoría o tipo del proceso.',
				1 => '<strong>Última Ejecución</strong>: Muestra la fecha en la que se produjo la última ejecución.',
				2 => '<strong>Próxima Ejecución</strong>: Muestra la fecha de la próxima ejecución a realizarse o bien te informará que No hay ejecuciones programadas.',
			],
			'nota' => 'También podrás ver más detalles haciendo clic sobre el botón de Opciones, ubicado en la última columna de cada registro.',
		],
		'procesos_alarmas' => [
			'titulo' => 'Procesos – Alarmas ',
			'descripcion' => 'En la sección de Alarmas podrás gestionar y administrar los avisos que recibirán los usuarios cuando se realiza una determinada acción en el Sistema.<br><strong>Desde aquí podrás:</strong>',
			'lista' =>[
				0 => 'Crear una Nueva Alarma',
				1 => 'Editar una Alarma',
				2 => 'Deshabilitar una Alarma.',
			],
			'titulo_lista_crear_alarma' => 'Pasos para <strong>Crear</strong> una Nueva Alarma',
			'lista_crear_alarma' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Procesos</strong> y luego la opción <strong>Alarmas</strong>.',
				1 => 'Presioná el botón <strong>Nueva Alarma</strong>.',
				2 => '<strong>Nombre (*)</strong>: Ingresá el nombre de la nueva Alarma.',
				3 => '<strong>Receptor</strong>: El Receptor podrá ser un usuario Contratista o un usuario perteneciente a la EBY ',
				4 => [
					0 => 'Contratista: Es el usuario que se encuentra registrado en el Sistema.',
					1 => 'EBY: Es el usuario de Gestión que ya se encuentra registrado en el Sistema.',
				],
				5 => '<strong>Evento</strong>: Define las acciones o los Estados que dispararán los mensajes de Alertas.',
				6 => '<strong>Mensaje (*)</strong>: Colocá el título y el mensaje que será incluído en la Alerta.',
				7 => 'Finalmente, presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_editar_alarma' => 'Pasos para <strong>Editar</strong> una Alerta',
			'lista_editar_alarma' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Procesos</strong> y luego la opción <strong>Alarmas</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro de la Alerta que quieras Editar.',
				2 => 'Luego, presioná la opción <strong>Editar</strong> del menú desplegable.',
				3 => 'Modificá los datos que desees.',
				4 => 'Finalmente presioná el botón <strong>Guardar</strong>.',
			],
			'titulo_lista_deshabilitar_alarma' => 'Pasos para <strong>Deshabilitar</strong> una Alerta',
			'lista_deshabilitar_alarma' => [
				0 => 'En el menú <strong>Configuración</strong> seleccioná <strong>Seguridad</strong> y luego la opción <strong>Roles</strong>.',
				1 => 'Presioná el botón <strong>Opciones</strong> perteneciente al registro de la Alerta que quieras Deshabilitar.',
				2 => 'Luego, deberás confirmar la Deshabilitación de la Alerta presionando SI o NO.',
			]
		]
	]
	// FIN 'configuracion'

];

?>
