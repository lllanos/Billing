<?php

return [

  'distinct' => [
    'grupo_nombre'  => 'Ya existe un Grupo con ese Nombre.',
    'requisito'     => 'Ya existe un Requisito con esa Descripción.',
    'documentacion' => 'Ya existe una Documentación con esa Descripción.',
    'rol_nombre'    => 'Ya existe un Rol con ese Nombre.',
    'user_email'    => 'Ya existe un Usuario con ese Correo Electrónico.',
  ],

  'max' => [
    'institucion' => 'La Institución no puede ser mayor que :max caracteres.',
  ],

  'min' => [
    'institucion' => 'La Institución debe ser de al menos :min caracteres.',
  ],

  'password' => [
    'confirmed'         => 'La Contraseña y la confirmación no coinciden.',
    'formato_invalido'  => 'El formato de la contraseña no es válido.',
  ],

  'required' => [
    'terminos_y_condiciones' => 'Debe aceptar los términos y condiciones.',
  ],
];
