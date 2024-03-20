<?php

return [

	'estados' => [
      'nombre' => [
    		'sin'  	          => 'Sin polinómica',
    		'borrador'  			=> 'Polinómica en borrador',
        'a_validar'      	=> 'Polinómica a validar',
        'aprobado'      	=> 'Polinómica aprobada',
    		'a_corregir'      => 'Polinómica a corregir',
      ],
      'nombre_tag' => [
        'sin'  	          => 'Sin polinómica',
    		'borrador'  			=> 'Borrador',
        'a_validar'      	=> 'A validar',
        'aprobado'      	=> 'Aprobada',
    		'a_corregir'      => 'A corregir',
      ]
		],

		'mensajes' => [
			'polinomica' 					=> 'Polinomica guardada con éxito',
			'polinomica_borrador' => 'Borrador de Polinomica guardado con éxito',
		],

		'confirmacion' => [
			'polinomica' 					=> '¿Está seguro que desea dar por completada la polinómica? Una vez guardada la polinómica no se puede modificar puesto que se comienza a realizar el cálculo de saltos sobre ese contrato.',
			'publicar' 						=> '¿Está seguro que desea publicar? Una vez publicado, el contrato se empieza a visualizar por los contratistas y se comienza a realizar el cálculo de saltos.',
		],
];
