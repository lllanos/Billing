<?php

return [
	'sistema_rdp' => [
		'titulo' => 'Sistema OCRE - Obras, Certificaciones y Redeterminaciones de Precios',
		'url'	 => 'ocre.eby.gob.ar',
		'que_es_rdp' => [
			'titulo'  			=> '¿Qué es el Sistema OCRE?',
			'descripcion'       => 'El sistema OCRE permite realizar el seguimiento de tus contratos con EBY . Desde el sistema podrás:',
			'lista' => [
				0 => 'Seguir las contrataciones: el seguimiento de contrataciones incluye ver los datos de contratos, revisar itemizado, plan de trabajo y polinómicas, ver las modificaciones contractuales por adendas,ampliaciones y reprogramaciones y anticipos.',
				1 => 'Gestionar los certificados: Te permitirá realizar la carga de los certificados básico y solicitar los certificados redeterminados. Podrás ver el estado hasta su aprobación.',
				2 => 'Solicitar redeterminaciones de precios: Recibirás un mensaje de aviso para indicarte que se ha calculado el mes de salto y que tu contrato tiene una Redeterminación de Precios para solicitar.',
				3 => 'Podrás ver el detalle de todos los trámites asociados a la Redeterminación de Precios.',
				4 => 'A través de un Árbol de Datos sabrás en qué etapa está tu expediente y cuáles son los próximos pasos, como así también la fecha estimada de finalización.',
				5 => 'Recibirás un mensaje de aviso para indicarte que los certificados han sido aprobados.',
				],
		],
		'registro_en_sistema' =>[
			'titulo'             => 'Registro en el Sistema',
			'descripcion'        => 'Para hacer uso del <strong>Sistema de Redeterminación de Precios (Sistema RDP)</strong>, primero tenés que registrarte como usuario. Desde un 						 navegador web podrás acceder a través de la siguiente dirección URL:',
			'titulo_lista'		 => 'Pasos para Registrarte:',
			'lista' => [
				0 => 'Presioná el botón <strong>REGISTRATE</strong> o sobre la pregunta <strong>¿Todavía no estás registrado?</strong>',
				1 => 'Completá el formulario con los datos solicitados.',
				2 => 'Deberás aceptar los <strong>Términos y Condiciones</strong>.',
				3 => 'Presioná el botón <strong>REGISTRARME</strong>.',
				4 => 'Recibirás un correo electrónico con una contraseña y donde se te solicitará confirmar tu Usuario.'
			]
		],
		'inicio_sesion' => [
			'titulo'			 => 'Inicio de Sesión',
			'descripcion'		 => 'Para Iniciar Sesión en el <strong>Sistema de Redeterminación de Precios (Sistema RDP)</strong>.
									 Desde un navegador web podrás acceder a través de la siguiente dirección URL: <a href="https://ocre.eby.gob.ar">ocre.eby.gob.ar</a>',
			'titulo_lista'		 => 'Pasos para <strong>Iniciar Sesión<strong>:',
			'lista' => [
				0 => 'Ingresá tu correo electrónico.',
				1 => 'Ingresá la Contraseña.',
				2 => 'Presioná el botón <strong>INGRESAR</strong>.',
			]
		],
		'recuperar_contrasena' => [
			'titulo'			 => 'Recuperar Contraseña',
			'descripcion'		 => 'Si perdiste la contraseña, podés recuperarla haciendo clic en el enlace con el texto <strong>No 							 recuerdo mi contraseña</strong>.',
			'titulo_lista'		 => 'Pasos para <strong>Recuperar la Contraseña:</strong>',
			'lista' => [
				0 => 'Ingresá a la web: <a href="https://ocre.eby.gob.ar">ocre.eby.gob.ar</a>',
				1 => 'Presioná el enlace con el texto: <strong>No recuerdo mi contraseña</strong>',
				2 => 'Ingresá la dirección de correo que tenés registrada en el Sistema',
				3 => 'Presioná el botón <strong>SOLICITO NUEVA CONTRASEÑA</strong>',
				4 => 'Recibirás un mail de confirmación para generar una nueva contraseña',
			]
		],
		'pantalla_inicio' => [
			'titulo' => 'Pantalla de Inicio',
			'descripcion' => 'En la pantalla de Inicio podés visualizar dos paneles principales. El panel <strong>Mis Contratos</strong> y 				  el panel <strong>Mis Solicitudes</strong>. Ambos paneles te proporcionarán una visión rápida y te permitirán 				  saber la cantidad de Asociaciones a Contratos y Solicitudes de Redeterminación que tenés en tu bandeja.',
			'titulo_lista_contratos' => 'El panel <strong>Mis Contratos</strong> contiene:',
			'lista_contratos' => [
				0 => 'Las <strong>Solicitudes de Asociación</strong>, aquellas que están pendientes de aprobación y las que han sido rechazadas.',
				1 => 'Los <strong>Contratos Asociados</strong>, aquellos que han sido efectivamente aprobados por  Yacyreta (EBY).'
			],
			'titulo_lista_solicitudes' => 'El panel <strong>Mis Solicitudes</strong> contiene:',
			'lista_solicitudes' => [
				0 => 'Las Solicitudes de Redeterminación, cada una con su estado respectivo.'
			],
			'nota' => 'Deberás tener contratos asociados para poder ver los datos del contrato, y las redeterminaciones. Podés gestionarla desde el botón <strong> solicitar asociación</strong>.'
		]
	],
	'contratos' => [
		'titulo' => 'Contratos',
		'descripcion' => 'En la sección Contratos vas a poder visualizar tus Contratos en forma detallada; realizar una solicitud de Asociación a un Contrato y visualizar el estado de tramitación en el que se encuentran tus Solicitudes.',
		'solicitudes_asociaciones' => [
			'titulo' => 'Mis Solicitudes de Asociación',
			'descripcion' => 'La sección <strong>Mis solicitudes de Asociación</strong> contiene el listado detallado de los Contratos y su respectivo Estado. Desde esta pantalla podés:',
			'lista' => [
				0 => 'Buscar un Contrato por Nro. de Expediente, Descripción, Estado o por Fecha del último Movimiento realizado.',
				1 => 'Exportar o descargar el listado de tus Solicitudes de Asociación a un archivo Excel.',
				2 => 'Solicitar una Asociación a Contrato presionando el botón de acceso rápido.',
				3 => 'Ver el Detalle de la Solicitud de Asociación.',
			],
			'titulo_lista_registro' => 'En el listado de Mis Solicitudes de Asociación, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_registro' => [
				0 => '<strong>Fecha Solicitud</strong>: Indica la fecha en la que fue solicitada la Asociación a Contrato.',
				1 => '<strong>N° Contr.	Mod. y N° de Contr.	Exp. Madre</strong>: Son los números de la contratación',
				2 => '<strong>Descripción</strong> La descripción que hayas colocado cuando realizaste la solicitud.',
				3 => '<strong>Estado</strong>: El estado en el que se encuentra la Solicitud de Asociación.',
				4 => [
					0 => 'Pendiente de Aprobación: El trámite de la Solicitud de Asociación está siendo procesado por la EBY.',
					1 => 'Aprobado: La Solicitud de Asociación al Contrato ha sido Aprobada exitosamente'
				],
				5 => '<strong>Último Mov.</strong>: Muestra el último movimiento que se ha realizado sobre el Contrato.'
			],
			'nota' => 'Podés ver los datos detallados de tu Solicitud de Asociación así como también conocer el estado en el que se encuentra el trámite.',
			'titulo_lista_detalle_solic' => 'Pasos para ver el Detalle de tu pedido de <strong>Solicitud de Asociación al Contrato</strong>',
			'lista_solicitud_detalle' => [
				0 => 'Presioná el botón <strong>Opciones</strong>.',
				1 => 'Luego, hacé clic sobre <strong>Ver</strong>.'
			],
			'alert' => 'Inmediatamente después de que la <strong>EBY</strong> evalúe la Solicitud de Asociación al Contrato, recibirás un correo electrónico informándote si la misma ha sido <strong>Aprobada</strong> o <strong>Rechazada</strong>.'
		],
		'solicitar_asociacion' => [
			'titulo' => 'Solicitar Asociación',
			'descripcion' => 'En la sección Solicitar Asociación podés solicitar la Asociación a tu Contrato para que la EBY pueda evaluarlo, vincularlo con el número de Expediente Electrónico y luego Aprobarlo o Rechazarlo.',
			'titulo_lista' => 'Pasos solicitar una <strong>Asociación a Contrato:</strong>',
			'lista' => [
				0 => '<strong>Contrato (*)</strong>: Buscá o seleccioná tu contrato en la lista desplegable.',
				1 => '<strong>Descripción (*)</strong>: Escribí la descripción del contrato para poder reconocerlo fácilmente.',
				2 => 'Seleccioná si sos <strong>Apoderado</strong> o <strong>Representante</strong>.',
				3 => [
					0 => 'Si sos <strong>Apoderado</strong> deberás adjuntar el Poder Legal que te habilita a serlo y opcionalmente podés completar la Fecha de vigencia del Poder Legal.',
					1 => 'Si sos <strong>Representante</strong> deberás adjuntar el Estatuto y el Acta de Nombramiento, luego deberás ingresar la fecha de vigencia del Acta.',
				],
				4 => '<strong>Observaciones</strong>: Aquí podés ingresar las observaciones que creas necesarias.',
				5 => 'Presioná el botón <strong>Guardar</strong>.',
				6 => 'Luego, deberás completar y aceptar la Declaración Jurada de Asociación al Contrato.',
				7 => 'Finalmente, presioná el botón <strong>Guardar</strong>.'
			],
			'nota' => 'Una vez realizada la Solicitud, el trámite pasará a ser evaluado por la EBY y se encontrará en proceso hasta su Aprobación.'

		],
		'mis_contratos' => [

			'titulo' => 'Mis Contratos',
			'descripcion' => 'En la sección <strong>MIS CONTRATOS</strong> encontrarás un listado detallado de los contratos cuya solicitud de asociación fue Aprobada por la <strong>EBY</strong> (EBY).',
			'titulo_lista_seccion' => 'En esta sección podés:',
			'lista_seccion' => [
				0 => 'Buscar un Contrato por Nro. de Expediente, Fecha de Licitación, Descripción, Fecha del Último Salto o por la Fecha de la Última Solicitud',
				1 => 'Exportar o descargar el listado de tus Solicitudes de Asociación a Contrato Aprobadas.',
				2 => 'Solicitar una Asociación a Contrato presionando el botón de acceso rápido',
				3 => 'Visualizar más detalles del Contrato, el itemizado, el plan de trabajo, el listado de los meses de salto de la obra y el detalle de la composición de la fórmula polinómica, los certificados, el análisis de precios, etc.',
				4 => 'También vas a poder ver de cada Salto el detalle de los índices utilizados, su composición y la Variación de Referencia obtenida mensualmente.',
				5 => 'Desde aquí disponés del acceso rápido para realizar una Solicitud de Redeterminación, simplemente deberás presionar el botón <strong>Opciones</strong> y luego <strong>Solicitar Redeterminación</strong>.'
			],

		'subtitulo_editar_itemizado' => 'Itemizado',
		'titulo_editar_Itemizado' => 'Con la opción de <strong>Itemizado</strong>, vas a poder ver el itemizado del contrato. El itemizado se muestra por cada una de las monedas del contrato.',

		'titulo_editar_items' => 'Los ítems pueden tener sub-items o ser ítems finales. En el caso de tener sub-items tendrás, el desplegable para ver los sub-items.<br>
		Los ítems se guardan de forma automática a medida que los vas cargando. Cuando hayas cargado todos los ítems y revisado, podrás darlos por finalizados con el botón de <strong>Guardar</strong>.<br>
		Del itemizado podrás:',

		'items_Completo' => [
			0 => 'Ver el <strong> Historial</strong>: Permite ver las modificaciones por cambios de estado que sufrió el itemizado',
			1 => '<strong> Realizar la Descarga a Excel </strong>: .',
		],

		'subtitulo_editar_polinomica' => 'Polinómica',
		'titulo_editar_Polinomica' => 'Con la opción de <strong>Polinómica</strong>, vas a poder ver los insumos que forman parte de la polinómica del contrato. Esta polinómica se muestra por cada una de las monedas. ',

		'subtitulo_editar_plan' => 'Plan de Trabajo',
		'titulo_editar_Plan' => 'Con la opción de <strong>Plan de trabajo</strong>, vas a poder ver la planificación de los ítems en los meses que dure el contrato. Una vez completo el plan de trabajo y la polinómica, el contrato cambiará su estado a <strong>Completo</strong>. A partir de este momento, se puede obtener más información del plan.<br> Los datos que se pueden obtener son:',

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

		'titulo_contrato_completo' => 'Una vez completado el <strong> Plan de trabajo</strong> el contrato se considera completo y se pueden ver también los siguientes datos:',
		'contrato_Completo' => [
			0 => '<strong> Adendas</strong>: Adiciones o complementos añadidos al contrato',
			1 => '<strong> Ampliaciones y Reprogramaciones </strong>: cambios realizados en las planificaciones originales del contrato que no impactan en los ítems del mismo.',
			2 => '<strong>Anticipos</strong>: Permite cargar lo recibido por el contratista como anticipo',
			3 => '<strong>Certificaciones </strong>: Permite visualizar los certificados del contrato.',
			4 => '<strong>Empalme </strong>: Permite gestionar las redeterminaciones y certificaciones que se hicieron en forma anterior al sistema',
			5 => '<strong>Redeterminaciones</strong>: Permite ver los saltos del contrato y las redeterminaciones asociadas.',

		],

		'subtitulo_adendas' => 'Adendas',

		'titulo_nueva_adenda' => 'Con la opción de <strong>Adenda</strong>, vas a poder ver las modificaciones aprobadas del contrato, con las adiciones o complementos al mismo. Las adendas pueden ser: Ampliación o Certificación independiente. <br>
		Las <strong>Adendas de Ampliación</strong> contienen los datos generales de la adenda y además, la nueva duración y el nuevo monto a valores originales. Luego de completados estos datos y guardados como definitivos vas a poder modificar los ítems del contrato y la planificación. (el monto a valores actuales se calculará como un proceso posterior que revisa las redeterminaciones. <br>
		Las adendas de certificación independiente se trabajan como una contratación extra dentro del contrato. Por eso mismo tienen su propio itemizado y su propio plan de trabajo. Además, a la adenda de certificación independiente se les pueden incluir otras adendas de ampliación, reprogramaciones, etc., tal cual lo realizado en un contrato.<br>',

		'subtitulo_ampliacion_rep' => 'Ampliaciones y Reprogramaciones',
		'titulo_nueva_ampliacion_rep' => 'Con la opción de <strong>Ampliación/reprogramación</strong>, vas a poder ver las modificaciones al plan de trabajo de un contrato. <br>
		Con las <Strong> Ampliaciones </Strong> podrás extender la cantidad de meses del mismo. La duración a completar es el total que llevará el contrato. <br>
		Con las <strong>reprogramaciones </strong> podrás replanificar el trabajo de modo diferente. ',

		'subtitulo_anticipo' => 'Anticipos',
		'titulo_agregar_anticipo' => 'Con la opción de <strong>Agregar Anticipo</strong>, vas a poder completar cual fue el anticipo recibido por el contratista. El porcentaje que se debe completar es el total del anticipo que recibió el contratista. De modificarse por adendas o redeterminaciones se deberá completar el nuevo monto pagado y el porcentaje total de anticipo que representa. Este porcentaje se utilizará para realizar descuentos en los certificados posteriores  ',

		'subtitulo_empalme' => 'Empalme',
		'titulo_agregar_empalme' => 'En la sección de <strong>Empalme</strong>, vas a poder ver el avance de un contrato iniciado anteriormente al sistema (redeterminado y certificado). Lo que se debe completar en estos casos es:',
		'lista_Empalme' => [
				0 => '<strong>Redeterminaciones</strong>: Permite ver los precios actualizados a la fecha de última redeterminación aprobada por fuera del sistema.',
				1 => '<strong>Certificados</strong>: Permite cargar el avance certificado hasta la fecha. Esta opción permite cargar el avance en cada uno de los ítems y automáticamente calcula el importe básico asociado. De haberse generado el certificado básico con alguna diferencia (por realizar los calculos con otra herramienta) se puede adaptar el monto individual. También, permite indicar el monto generado por los certificados redeterminados. Estos datos se utilizaran para conocer los avances físicos y financieros.,',
			],

		'subtitulo_analisis_precios' => 'Análisis de Precios',
		'titulo_completar_analisis' => 'En la sección de <strong>Análisis de Precios</strong>, vas a poder ver por cada ítem su análisis de precios. Además, podrás ver el coeficiente K del análisis. El listado muestra un resumen del precio unitario cargado en el ítem y el costo multiplicado por el coeficiente K que resume el precio cargado en el análisis de precios.  El análisis de Precios pasa por dos <strong>instancias de aprobación</strong> primero por la de <strong>inspección de obras</strong> y luego por la <strong>comisión de redeterminaciones de precios</strong> que verifica los índices asociados a cada componente. Por cada ítem se podrán ver los siguientes componentes del Análisis de Precios:',
		'lista_Analisis_precios' => [
				0 => '<strong>Resumen</strong>: Permite ver un resumen de lo completado en el análisis de precios y datos del ítem particular.',
				1 => '<strong>Materiales</strong>: Permite cargar los costos de materiales que se utilizan para el desarrollo del ítem.',
				2 => '<strong>Ejecución</strong>: Permite cargar los costos de ejecución. La ejecución se divide en: Mano de Obra y Equipos. En caso de que el ítem sea por Unidad de Medida se puede cargar un rendimiento que puede ser por día o por hora. El rendimiento lo podrás cargar desde el botón de edición que se encuentra al lado de la ejecución.',
				3 => '<strong>Mano de Obra</strong>: Permite cargar el personal que estará asociado al desarrollo del ítem.',
				4 => '<strong>Equipos</strong>: Permite cargar los costos de equipamiento que se utilizarán para el desarrollo del ítem. Los equipamientos se dividen en: Máquinas y Equipos, Amortizaciones, Combustibles y Lubricantes y Reparaciones y Repuestos.',
				5 => '<strong>Máquinas y Equipos</strong>: Permite cargar las máquinas y equipos que van a ser utilizados para el desarrollo del ítem. Estas máquinas y equipos nos son considerados como costos directos sino que lo que se considera es; la amortización, las reparaciones y los repuestos y los combustibles y lubricantes derivados de su uso.',
				6 => '<strong>Amortizaciones</strong>: Permite ver los costos por amortizaciones y la descripción de como se llegó al cálculo.',
				7 => '<strong>Combustibles y Lubricantes</strong>: Permite cargar los costos en combustibles y lubricantes asociados a los equipos.',
				8 => '<strong>Reparaciones y Repuestos</strong>: Permite cargar los costos de reparaciones y repuestos asociados a los equipos. Se puede cargar la descripción de como se llega al cálculo.',
				9 => '<strong>Transporte</strong>: Permite cargar los costos en transporte.',


			],

			'subtitulo_Certificados' => 'Certificados',
			'titulo_certificados' => 'En la sección de <strong>Certificados </strong>, vas a poder ver y completar el avance de mensual de los contratos. Vas a ver por  cada uno de los ítems el avance acumulados según las mediciones. El sistema automáticamente calcula los montos y los desvíos acumulados en base al plan de trabajo vigente. Cuando realices el guardado borrador o definitivo se hace el cálculo de los totales. Además, para el guardado definitivo del certificado debes completar los adjuntos:',
			'lista_certificados' => [
					0 => '<strong>Certificados Redeterminados </strong>: Permite ver los certificados redeterminados.',
					1 => '<strong>Descargar Certificado</strong>: Permitirá descargar el certificado para que pueda ser revisado y presentado junto con las facturas.,',
				],

			'titulo_lista_mis_contratos' => 'En el listado de Mis Contratos, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_mis_contratos' => [
				0 => '<strong># Exp. Madre</strong>: Es el número de Expediente Principal o el número de Expediente Electrónico.',
				1 => '<strong>Licitación</strong>: Fecha de Licitación del Pliego.',
				2 => '<strong>Descripción</strong>: La descripción que hayas colocado cuando realizaste la solicitud.',
				3 => '<strong>VR</strong>: Muestra el Nombre de la Obra y la Variación de Referencia. Según el color de la etiqueta, te indicará:',
				4 => [
					0 => '<strong>Etiqueta Roja</strong>: “La Obra NO Redetermina”. El cálculo de la Variación de Referencia del último mes no produjo un Salto.',
					1 => '<strong>Etiqueta Verde</strong>: “La Obra Redetermina”. El cálculo de la Variación de Referencia del último mes produjo un Salto.',
				],
				5 => '<strong>Último Salto</strong>: Fecha del último Salto. Según el ícono que se muestra, te indicará:',
				6 => [
					0 => '<strong>Icono rojo (cruz)</strong>: La Solicitud de Redeterminación del último Salto NO fue solicitada.',
					1 => '<strong>Icono verde (tilde)</strong>: La Solicitud de Redeterminación del último Salto SI fue solicitada.',
				],
				7 => '<strong>Última Solicitud</strong>: Fecha de la última Solicitud de Redeterminación de Precios.'
			],
			'nota_1' => 'De una manera muy sencilla también podés ver más detalles de cada uno de tus Contratos.',
			'titulo_lista_pasos_detalle_solic' => 'Pasos para ver el Detalle de tu pedido de Solicitud de Asociación al Contrato',
			'lista_pasos_detalle_solic' => [
				0 => 'Presioná el botón <strong>Opciones</strong> del Contrato.',
				1 => 'Luego, hacé clic sobre <strong>Ver</strong>.',
			],
			'nota_2' => 'Desde esta sección, Solicitar una Redeterminación de Precios, es también un proceso muy sencillo.',
			'titulo_lista_pasos_detalle_solic_2' => 'Pasos para ver el Detalle de tu pedido de Solicitud de Asociación al Contrato.',
			'lista_pasos_detalle_solic_2' => [
				0 => 'Presioná el botón <strong>Opciones</strong> del Contrato.',
				1 => 'Luego, hacé clic sobre <strong>Solicitar Redeterminación</strong>.',
			],
		]
	],
	'solicitudes_redeterminacion' => [
		'titulo' => 'Solicitudes de Redeterminación',
		'descripcion' => 'En la sección <strong>SOLICITUDES DE REDETERMINACIÓN</strong> vas a poder Solicitar una Redeterminación del Contrato que tengas asociado, visualizar tus Solicitudes de Redeterminación en forma detallada y realizar el seguimiento de todo el proceso de la Redeterminación de Precios que hayas solicitado.',
		'solicitar_redeterminacion' => [
			'titulo' => 'Solicitar Redeterminación',
			'descripcion' => 'En la sección Solicitar Redeterminación podés requerir la Redeterminación de Precios de tu Obra. Esta acción dará inicio al proceso que la EBY deberá realizar para llevar a cabo todos los trámites que involucran la Redeterminación. <br><br>
				Al Solicitar una Redeterminación de Precios el Sistema automáticamente incluirá todas las Redeterminaciones anteriores que hayan tenido salto y que no hayan sido solicitadas debidamente en la fecha correspondiente.',
			'titulo_lista_solic_rede' => 'Pasos solicitar una Redeterminación:',
			'lista_solic_rede' => [
				0 => '<strong>Contrato (*)</strong>: Buscá o seleccioná tu contrato en la lista desplegable.',
				1 => 'Una vez seleccionado el Contrato a Redeterminar, el Sistema automáticamente te mostrará, si es que los hubiere, todos los saltos anteriores que no hayan sido redeterminados.',
				2 => '<strong>Observaciones</strong>: Aquí podés ingresar las observaciones que creas necesarias.',
				3 => '<strong>Adjunto</strong>: Podés adjuntar una documentación adicional que desees enviar con la Solicitud. Toda aquella documentación requerida y obligatoria te será solicitada oportunamente durante el proceso de Redeterminación. Tené en cuenta que los archivos que subas deberán tener alguno de los siguientes formatos válidos: .PNG, .JPG, GIF o .PDF)',
				4 => 'Luego, deberás marcar la casilla y aceptar la Declaración Jurada de la Solicitud de Redeterminación.',
				5 => 'Finalmente, presioná el botón <strong>Guardar</strong>.'
			],
			'nota' => 'Una vez realizada la Solicitud de Redeterminación, desde la EBY evaluarán el pedido y según sea el caso la Solicitud de RDP podrá ser Aprobada, Rechazada o Suspendida. A través del sistema serás debidamente informado sobre el estado de la Solicitud de RDP para que puedas enterarte y darle seguimiento.',
			'alert' => 'Inmediatamente después de que la <strong>EBY</strong> evalúe la Solicitud de Redeterminación de Precios, recibirás un correo electrónico informándote si la misma ha sido procesada dando comienzo a la serie de pasos que deberán ser cumplidos para su <strong>Aprobación Final</strong> o bien si ha sido <strong>Rechazada</strong> incluyendo el motivo por el cual no puede procesarse tu solicitud.'
		],
		'mis_solicitudes_redeterminacion' => [
			'titulo' => 'Mis Solicitudes de Redeterminación',
			'descripcion' => 'En la sección Mis solicitudes de Redeterminación verás el listado detallado de los contratos/obras cada uno con su estado respectivo.',
			'titulo_lista' => 'Desde esta pantalla vas a poder:',
			'lista' => [
				0 => 'Buscar un contrato por Nro. de Expediente, Descripción, Obra, Fecha de Solicitud, Estado o por Fecha del último Movimiento realizado.',
				1 => 'Exportar o descargar el listado de tus Solicitudes de Redeterminación a un archivo Excel.',
				2 => 'Solicitar una Nueva Redeterminación presionando el botón de acceso rápido.',
				3 => 'Ver el Detalle de la Solicitud de Redeterminación.'
			],
			'titulo_lista_mis_solic' => 'En el listado de <strong>Mis Solicitudes de Redeterminación</strong>, cada registro de la tabla te mostrará los siguientes datos:',
			'lista_mis_solic' => [
				0 => '<strong># Exp. Madre</strong>: Es el número de Expediente Principal o el número de Expediente electrónico.',
				1 => '<strong>Descripción</strong>: La descripción que hayas colocado cuando realizaste la solicitud de redeterminación.',
				2 => '<strong>Fecha Solicitud</strong>: Indica la fecha en la que fue solicitada la Asociación a Contrato.',
				3 => '<strong>Salto</strong>: El tipo de obra (Camino, Puente, etc.)',
				4 => '<strong>Estado</strong>: El estado en el que se encuentra la Solicitud de Redeterminación. Aquí podrás observar cada uno de los estados por los que atraviesa la Solicitud de RDP.',
				5 => [
					0 => 'Esperando... <strong>Aprobación de Certificados</strong>.',
					1 => 'Esperando... <strong>Verificación de Desvíos</strong>.',
					2 => 'Esperando... <strong>Calculo de Precios Redeterminados</strong>.',
					3 => 'Esperando... <strong>Generación de Expediente Electrónico</strong>.',
					4 => 'Esperando... <strong>Asignación de Partida Presupuestaria</strong>.',
					5 => 'Esperando... <strong>Generación de Proyecto Acta RDP, Resolución e Informe</strong>.',
					6 => 'Esperando... <strong>Firma de Resolución Aprobatoria</strong>.',
					7 => 'Esperando... <strong>Emisión de Certificados</strong>.',
					8 => '<strong>Aprobada</strong> (El proceso ha finalizado y la Solicitud de RDP fue Aprobada)',
					]
				],
				'nota' => 'Podés ver los datos detallados de tu Solicitud de Redeterminación, seguir paso a paso el proceso conociendo el estado en el que se encuentra el trámite.',
				'titulo_lista_detalle_rede' => 'Pasos para ver el Detalle de tu pedido de <strong>Redeterminación de Precios</strong>',
				'lista_detalle_rede' =>[
					0 => 'Presioná el botón <strong>Opciones</strong>.',
					1 => 'Luego, hacé clic sobre <strong>Ver</strong>.'
			]

		]
	],
	'indices' => [
		'titulo' => 'Índices',
		'descripcion' => 'En la sección <strong>ÍNDICES</strong> vas a poder visualizar las <strong>Publicaciones</strong> y las fechas en las que fueron realizadas; también podrás acceder a la <strong>Tabla de Índices</strong> que dispone de dos vistas categorizadas para facilitarte la visualización de los datos.',
		'lista_publicaciones' => [
			0 => '<strong>Publicaciones</strong>: Contiene el listado de todas las Publicaciones realizadas indicando el Mes de cada publicación y su fecha. Desde aquí podrás:',
			1 => [
				0 => 'Realizar una búsqueda por: Mes del Índice y Fecha de Publicación.',
				1 => 'Exportar o descargar el <strong>Listado de Publicaciones</strong> a un archivo Excel',
			]
		],
		'lista_tabla_valores' => [
			0 => '<strong>Valores</strong>: Con los valores de los índices mes a mes categorizados en listas desplegables y filtrados por año de publicación. Desde aquí podrás:',
			1 => [
				0 => 'Filtrar la <strong>Valores</strong> por año de publicación.',
				1 => 'Realizar una búsqueda por: Categoría, Subcategoría, Número de índice, Nombre de índice y Valor del índice.',
				2 => 'Exportar o descargar la <strong>Valores</strong> a un archivo Excel.',
				3 => 'Acceder a la <strong>Fuentes</strong> mediante el botón de acceso directo ubicado en la parte superior de la página. ',
			]
		],
		'lista_tabla_fuentes' => [
			0 => '<strong>Fuentes</strong>: Con los índices y sus fuentes categorizados en listas desplegables y filtrados por períodos de publicación.  Desde aquí podrás:',
			1 => [
				0 => 'Filtrar la <strong>Fuentes</strong> por Períodos de publicación.',
				1 => 'Realizar una búsqueda por: Categoría, Subcategoría, Número de índice, Nombre de índice, Aplicación, Fuente y Valor del índice.',
				2 => 'Exportar o descargar la <strong>Fuentes</strong> a un archivo Excel.',
				3 => 'Acceder a la <strong>Valores</strong> mediante el botón de acceso directo ubicado en la parte superior de la página. ',

			]
		],
		'alert' => '<strong>El Período de Publicación</strong> comienza a contabilizarse desde el Mes/Año de Publicación de la Tabla de Índices hasta el Mes/Año de la siguiente Publicación de la Tabla. Con cada nueva Publicación finaliza el período anterior y da comienzo a uno nuevo.'
	],
	'notificaciones' => [
		'titulo' => 'Notificaciones',
		'descripcion' => 'En el menú principal encontrarás el ícono de Notificaciones, haciendo clic sobre él se desplegará el listado de avisos que aún no han sido leídos. Allí disponés de la opción: Marcar todas como leídas, lo que dejará en cero a las notificaciones pendientes. O bien, haciendo clic sobre la notificación automáticamente el Sistema te mostrará qué acción generó la notificación.'
	],
	'usuario' => [
		'titulo' => 'Usuario',
		'descripcion' => 'El ícono de Usuario te permitirá acceder a tu Perfil y a la opción de modificar la contraseña.',
		'perfil' => [
			'titulo' => 'Perfil',
			'descripcion' => 'En la pantalla del Perfil podrás modificar tu Nombre, Apellido o correo electrónico. Luego de haber realizado alguna modificación deberás guardar los cambios.',
		],
		'contrasena' => [
			'titulo' => 'Contraseña',
			'descripcion' => 'En la pantalla Cambiar Contraseña tenés la opción de modificar tu Contraseña por una nueva. Luego de haber cambiado la contraseña deberás guardar los cambios.'
		]
	]
]










 ?>
