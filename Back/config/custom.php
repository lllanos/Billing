<?php

return [
  // URL del entorno de contratistas
  'url_front'             => env('URL_FRONT', 'http://localhost'),
  'url_back'              => env('APP_URL'),

  // TEST_MODE=true permite el acceso a rutas test
  'test_mode'             => env('TEST_MODE', true),

  // Si se usa Maria DB o MySQL < 5.7.7, setear true por longitud de mysql key
  'maria_db'              => env('MARIA_DB', true),

  // Cantidad de elementos por pagina (paginado)
  'items_por_pagina'      => env('ITEMS_POR_PAGINA', 15),

  // Bloqueo Login
  'intentos_login'        => env('INTENTOS_LOGIN', 3),
  // Minutos que se bloquea el usuario
  'tiempo_bloqueo_login'  => env('TIEMPO_BLOQUEO_LOGIN', 30),


  // Cantidad de elementos por widget de tipo tabla
  'items_por_widget'      => env('ITEMS_POR_WIDGET', 4),
  // Milisegundos para el timeout de los widgets
  'timeout_widget'        => env('TIMEOUT_WIDGET', 1000),

  // Hora y minuto en los que se corren los procesos
  'hora_proceso'          => env('HORA_PROCESO', 2),
  'min_proceso'           => env('MIN_PROCESO', 23),

  // Delta de redondeo de cuentas
  'delta'                 => env('DELTA', 0.00001),
  'delta_2d'              => env('DELTA_2D', 0.01),
];
