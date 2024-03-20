<?php

return [
    'error_finalizar' => 'Error: todos los nodos del itemizado deben finalizar con un elemento hoja, ya sea global o unidad de medida. Todas los nodos deben tener un subtotal acumulado > 0',

    'vista' => [
        'nombre' => [
            'original' => 'Visualizar Original',
            'vigente' => 'Visualizar Vigente',
            'all' => 'Visualizar en UM',
            'moneda' => 'Visualizar en Moneda',
            'porcentaje' => 'Visualizar en Porcentaje',
        ],
        'tag' => [
            'original' => 'Original',
            'vigente' => 'Vigente',
            'all' => 'UM',
            'moneda' => 'Moneda',
            'porcentaje' => 'Porcentaje',
        ],
    ],

    'estados' => [
        'nombre' => [
            'sin' => 'Sin itemizado',
            'borrador' => 'Itemizado en borrador',
            'a_validar' => 'Itemizado a validar',
            'aprobado' => 'Itemizado aprobado',
            'a_corregir' => 'Itemizado a corregir',
            'a_firmar' => 'Itemizado pendiente de firmas',
            'firma' => 'Itemizado pendiente de una firma',
        ],
        'nombre_tag' => [
            'sin' => 'Sin itemizado',
            'borrador' => 'Borrador',
            'a_validar' => 'A validar',
            'aprobado' => 'Aprobado',
            'a_corregir' => 'A corregir',
            'a_firmar' => 'A firmar',
            'firma' => 'Firma',
        ]
    ],

    'mensajes' => [
        'item_agregado' => 'Item agregado con éxito',
        'item_editado' => 'Item editado con éxito',
        'item_eliminado' => 'Item eliminado exitosamente',
        'itemizado_actualizado' => 'Itemizado actualizado con éxito',
        'error_eliminar_hijos' => 'No se puede eliminar un Ítem con hijos',
        'error_eliminar_hijos' => 'No se puede eliminar un Ítem con éxito',
        'item_cloned' => 'Duplicación finalizada con exito',
    ],

];
