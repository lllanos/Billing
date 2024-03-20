<?php

return [

  'cronograma' => [
    'suma'                  => ':moneda: La suma del item ":item" da :resultado, se esperaba :esperado.',
    'sin_cargar'            => ':moneda: No se cargó el Plan de Trabajo de ":item".',
  ],

  'itemizado' => [
    'sin_hoja'            => ':moneda: ":item" no tiene nodo',
    'menor_certificado'   => ':moneda: La cantidad de ":item" no puede ser menor a la certificada (:cant)',
  ],

  'distinct' => [
    'grupo_nombre'          => 'Ya existe un Grupo con ese Nombre.',
    'requisito'             => 'Ya existe un Requisito con esa Descripción.',
    'documentacion'         => 'Ya existe una Documentación con esa Descripción.',
    'rol_nombre'            => 'Ya existe un Rol con ese Nombre.',
    'user_email'            => 'Ya existe un Usuario con ese Correo Electrónico.',
    'polinomica'            => 'Ya existe una Polinómica con ese Nombre.',
    'numero_contrato'       => 'Ya existe un Contrato con ese Número.',
    'numero_contratacion'   => 'Ya existe un Contrato con ese Número de Contratación.',
  ],

  'max' => [
    'institucion' => 'La Institución no puede ser mayor que :max caracteres.',
  ],

  'fecha' => [
    'inicio_anterior_oferta'          => 'La Fecha de Inicio no puede ser anterior a la Fecha Base.',
    'inicio_anterior_oferta_contrato' => 'La Fecha de Inicio no puede ser anterior a la Fecha Base del contrato.',
    'aprobacion_anterior_inicio'      => 'La Fecha de Aprobación no puede ser anterior a la de Inicio del contrato.',
  ],

  'mayor_1'   => ':attribute no puede ser mayor a 1.',
  'mayor_100' => ':attribute no puede ser mayor a 100.',

  'menor_0'   => ':attribute no puede ser menor a 0.',

  'mes_ultima_certificacion_mayor_meses'   => 'Mes de última certificación debe ser menor o igual que la cantidad de meses del plan de trabajo.',
  'meses_mayor_mes_ultima_certificacion'   => 'La cantidad de meses del plan de trabajo debe ser mayor que el Mes de última certificación.',

  'min' => [
    'institucion' => 'La Institución debe ser de al menos :min caracteres.',
  ],

  'password' => [
    'confirmed'         => 'La Contraseña y la confirmación no coinciden.',
    'formato_invalido'  => 'El formato de la contraseña no es válido.',
  ],

  'polinomica_composicion_1'  => 'La composición de la polinómica debe sumar 1',
  'polinomica_vacia'          => 'La polinómica no puede estar vacía',


  'required' => [
    'terminos_y_condiciones' => 'Debe aceptar los términos y condiciones.',
  ],

  'acumulado_mayor_total'     => ':item: El acumulado es mayor al total del item',
  'descuento_mayor_importes'  => ':item: El Descuento del Anticipo no puede ser mayor a los Importes por ajustes',
  'sin_monedas'               => 'Debe cargar al menos una moneda',
  'moneda_duplicada'          => 'La moneda :moneda fue seleccionada más de una vez',

  'regex' => [
    'sime' => 'El formato debe ser: 1235467/2018',
  ],

  'seleccionar_publicacion' => 'Debe seleccionar una publicación',
];
