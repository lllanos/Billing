<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/login', ['as' => 'login', function () {
  return redirect('/ingresar');
}]);

Route::get('/configcache', function () {
  Artisan::call('config:cache');
  Artisan::call('config:clear');
  Artisan::call('view:clear');
  print('Cache cleared');

  return redirect('/');

});

Route::get('/descargar', ['as' => 'descargar', 'uses' => 'Controller@getDownload']);

////////////////// Confirmacion de usuario //////////////////
Route::post('register/verify/confirmationMail', ['as' => 'register.confirmationmail', 'uses' => 'ConfirmarMailController@confirmarMailUsuario']);
Route::get('register/verify/{confirmationCode}', ['as' => 'register.verify', 'uses' => 'ConfirmarMailController@confirmarUsuario']);
Route::get('reenviar/confirmacion/{confirmation_code}', ['as' => 'reenviar.confirmacion', 'uses' => 'ConfirmarMailController@reenviarConfirmarUsuario']);

Route::get('users/generarPassword', ['as' => 'users.generarPassword', 'uses' => 'ConfirmarMailController@generarPassword']);
Route::post('users/generarPasswsord', ['as' => 'users.finConfirmacionUsuario', 'uses' => 'ConfirmarMailController@finConfirmacionUsuario']);
////////////////// FIN Confirmacion de usuario //////////////////

////////////////// Auth Routes //////////////////
// Login Routes (en espaniol)
Route::get('ingresar', ['as' => 'ingresar', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('ingresar', ['as' => 'ingresar.login', 'uses' => 'Auth\LoginController@login']);
// Password Reset Routes (en espaniol)
Route::get('recuperar/contrasenia', ['as' => 'recuperar.contrasenia', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('contrasenia/email', ['as' => 'contrasenia.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmailPublic']);

Route::post('recuperar/contrasenia', ['as' => '', 'uses' => 'Auth\ResetPasswordController@reset']);
Route::get('recuperar/contrasenia/{token}', ['as' => 'recuperar.contrasenia.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
// Comento Register original de Auth para que un usuario no pueda registrarse
Route::match(['get', 'post'], 'register', function () {
  return redirect('/');
});
////////////////// FIN Auth Routes //////////////////

Route::group(['middleware' => ['auth']], function () {
  ////////////////// Notifications //////////////////
  Route::get('markAsRead/{notification}/{route}/{routeParam}', ['as' => 'notification.markAsRead', 'uses' => 'NotificationsController@markAsRead']);
  Route::get('markAllAsRead', ['as' => 'notification.markAllAsRead', 'uses' => 'NotificationsController@markAllAsRead']);
  ////////////////// Fin Notifications //////////////////

  ////////////////// Dashboard //////////////////
  Route::resource('/', 'DashboardController');
  Route::resource('inicio', 'DashboardController');

  Route::get('widget/dashboard/{widget}', ['as' => 'widget.dashboard', 'uses' => 'DashboardController@widget']);

  Route::get('layout', ['as' => 'layout', 'uses' => 'DashboardController@elegirLayout']);
  Route::get('update/layout/{id}', ['as' => 'update.layout', 'uses' => 'DashboardController@updateLayout']);
  Route::get('widgets', ['as' => 'edit.widgets', 'uses' => 'DashboardController@editWidgets']);
  Route::post('update/widgets', ['as' => 'update.widgets', 'uses' => 'DashboardController@updateWidgets']);
  ////////////////// FIN Dashboard //////////////////

  ////////////////// Descarga de Excel //////////////////
  Route::get('excel/exports/{filename}', [
    function ($filename) {
      $filePath = storage_path() . '/excel/exports/' . $filename;
      $fileContents = File::get($filePath);
      return Response::make($fileContents, 200, array('Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
    }
  ]);
  ////////////////// FIN Descarga de Excel //////////////////

  ////////////////// Roles //////////////////
  Route::get('seguridad/roles', ['as' => 'seguridad.roles.index', 'uses' => 'RoleController@index', 'middleware' => ['permission:role-list']]);
  Route::post('seguridad/roles', ['as' => 'seguridad.roles.index.post', 'uses' => 'RoleController@index', 'middleware' => ['permission:role-list']]);

  Route::get('seguridad/roles/crear', ['as' => 'seguridad.roles.create', 'uses' => 'RoleController@create', 'middleware' => ['permission:role-create']]);
  Route::post('seguridad/roles/crear', ['as' => 'seguridad.roles.store', 'uses' => 'RoleController@store', 'middleware' => ['permission:role-create']]);
  Route::get('seguridad/roles/{id}', ['as' => 'seguridad.roles.show', 'uses' => 'RoleController@show', 'middleware' => ['permission:role-view']]);
  Route::get('seguridad/roles/{id}/editar', ['as' => 'seguridad.roles.edit', 'uses' => 'RoleController@edit', 'middleware' => ['permission:role-edit']]);
  Route::patch('seguridad/roles/{id}', ['as' => 'seguridad.roles.update', 'uses' => 'RoleController@update', 'middleware' => ['permission:role-edit']]);

  Route::get('seguridad/roles/preDelete/{id}', ['as' => 'seguridad.roles.preDelete', 'uses' => 'RoleController@preDelete', 'middleware' => ['permission:role-delete']]);
  Route::get('seguridad/roles/eliminar/{id}', ['as' => 'seguridad.roles.delete', 'uses' => 'RoleController@delete', 'middleware' => ['permission:role-delete']]);

  Route::post('seguridad/exportar/roles', ['as' => 'seguridad.export.roles', 'uses' => 'RoleController@exportar', 'middleware' => ['permission:role-export']]);
  ////////////////// FIN Roles //////////////////

  ////////////////// Usuarios //////////////////
  Route::get('seguridad/usuarios', ['as' => 'seguridad.users.index', 'uses' => 'UsersController@index', 'middleware' => ['permission:user-list']]);
  Route::post('seguridad/usuarios', ['as' => 'seguridad.users.index.post', 'uses' => 'UsersController@index', 'middleware' => ['permission:user-list']]);
  Route::get('seguridad/usuarios/crear', ['as' => 'seguridad.users.create', 'uses' => 'UsersController@create', 'middleware' => ['permission:user-create']]);
  Route::post('seguridad/usuarios/crear', ['as' => 'seguridad.users.store', 'uses' => 'UsersController@store', 'middleware' => ['permission:user-create']]);
  Route::get('seguridad/usuarios/{id}', ['as' => 'seguridad.users.show', 'uses' => 'UsersController@show', 'middleware' => ['permission:user-view']]);
  Route::get('seguridad/usuarios/{id}/editar', ['as' => 'seguridad.users.edit', 'uses' => 'UsersController@edit', 'middleware' => ['permission:user-edit']]);
  Route::post('seguridad/usuarios/{id}', ['as' => 'seguridad.users.update', 'uses' => 'UsersController@update', 'middleware' => ['permission:user-edit']]);

  Route::get('seguridad/usuarios/preToggleHabilitar/{id}/{accion}', ['as' => 'seguridad.users.preToggleHabilitar', 'uses' => 'UsersController@preToggleHabilitar', 'middleware' => ['permission:user-enable|user-disable']]);
  Route::get('seguridad/usuarios/toggleHabilitado/{id}/{accion}', ['as' => 'seguridad.users.toggleHabilitado', 'uses' => 'UsersController@toggleHabilitado', 'middleware' => ['permission:user-enable|user-disable']]);

  Route::get('seguridad/usuarios/preDelete/{id}', ['as' => 'seguridad.users.preDelete', 'uses' => 'UsersController@preDelete', 'middleware' => ['permission:user-delete']]);
  Route::get('seguridad/usuarios/eliminar/{id}', ['as' => 'seguridad.users.delete', 'uses' => 'UsersController@delete', 'middleware' => ['permission:user-delete']]);

  Route::post('seguridad/exportar/usuarios', ['as' => 'seguridad.export.users', 'uses' => 'UsersController@exportar', 'middleware' => ['permission:user-export']]);
  ////////////////// FIN Usuarios //////////////////

  ////////////////// Causantes //////////////////
  Route::get('causantes', ['as' => 'causantes.index', 'uses' => 'CausantesController@index', 'middleware' => ['permission:causante-list']]);
  Route::post('causantes', ['as' => 'causantes.index.post', 'uses' => 'CausantesController@index', 'middleware' => ['permission:causante-list']]);
  Route::get('causantes/crear', ['as' => 'causantes.create', 'uses' => 'CausantesController@create', 'middleware' => ['permission:causante-create']]);
  Route::post('causantes/crear', ['as' => 'causantes.store', 'uses' => 'CausantesController@store', 'middleware' => ['permission:causante-create']]);
  Route::get('causantes/{id}', ['as' => 'causantes.show', 'uses' => 'CausantesController@show', 'middleware' => ['permission:causante-view']]);
  Route::get('causantes/{id}/editar', ['as' => 'causantes.edit', 'uses' => 'CausantesController@edit', 'middleware' => ['permission:causante-edit']]);
  Route::post('causantes/{id}', ['as' => 'causantes.update', 'uses' => 'CausantesController@update', 'middleware' => ['permission:causante-edit']]);
  Route::get('causantes/preDelete/{id}', ['as' => 'causantes.preDelete', 'uses' => 'CausantesController@preDelete', 'middleware' => ['permission:causante-delete']]);
  Route::get('causantes/eliminar/{id}', ['as' => 'causantes.delete', 'uses' => 'CausantesController@delete', 'middleware' => ['permission:causante-delete']]);
  Route::post('causantes/exportar/causantes', ['as' => 'export.causantes', 'uses' => 'CausantesController@exportar', 'middleware' => ['permission:causante-export']]);
  ////////////////// FIN Causantes //////////////////

  ////////////////// Motivos de Reprogramacion //////////////////
  Route::get('motivos', ['as' => 'motivos.index', 'uses' => 'MotivosReprogramacionController@index', 'middleware' => ['permission:motivos-list']]);
  Route::get('motivos/crear', ['as' => 'motivos.create', 'uses' => 'MotivosReprogramacionController@create', 'middleware' => ['permission:motivos-create']]);
  Route::post('motivos/crear', ['as' => 'motivos.store', 'uses' => 'MotivosReprogramacionController@store', 'middleware' => ['permission:motivos-create']]);
  Route::get('motivos/{id}/editar', ['as' => 'motivos.edit', 'uses' => 'MotivosReprogramacionController@edit', 'middleware' => ['permission:motivos-edit']]);
  Route::post('motivos/{id}', ['as' => 'motivos.update', 'uses' => 'MotivosReprogramacionController@update', 'middleware' => ['permission:motivos-edit']]);
  Route::get('motivos/preDelete/{id}', ['as' => 'motivos.preDelete', 'uses' => 'MotivosReprogramacionController@preDelete', 'middleware' => ['permission:motivos-delete']]);
  Route::get('motivos/eliminar/{id}', ['as' => 'motivos.delete', 'uses' => 'MotivosReprogramacionController@delete', 'middleware' => ['permission:motivos-delete']]);
  Route::post('motivos/exportar/motivos', ['as' => 'export.motivos', 'uses' => 'MotivosReprogramacionController@exportar', 'middleware' => ['permission:motivos-export']]);
  ////////////////// FIN Motivos de Reprogramacion //////////////////

  ////////////////// Perfil //////////////////
  Route::get('seguridad/perfil', ['as' => 'seguridad.perfil', 'uses' => 'UsersController@perfil']);
  Route::post('seguridad/perfil', ['as' => 'seguridad.updatePerfil', 'uses' => 'UsersController@updatePerfil']);

  Route::get('seguridad/cambiarContrasenia', ['as' => 'seguridad.cambiarContrasenia', 'uses' => 'UsersController@cambiarContrasenia']);
  Route::post('seguridad/cambiarContrasenia', ['as' => 'seguridad.newPassword', 'uses' => 'UsersController@newPassword']);
  ////////////////// FIN Perfil //////////////////

  ////////////////// Contratistas //////////////////
  Route::get('contratistas', ['as' => 'contratistas.index', 'uses' => 'ContratistasController@index', 'middleware' => ['permission:contratista-list']]);

  Route::post('contratistas', ['as' => 'contratistas.post.index', 'uses' => 'ContratistasController@index', 'middleware' => ['permission:contratista-list']]);
  Route::post('exportar/contratistas', ['as' => 'export.contratistas', 'uses' => 'ContratistasController@exportar', 'middleware' => ['permission:contratista-export']]);
  Route::get('contratistas/crear', ['as' => 'contratistas.create', 'uses' => 'ContratistasController@create', 'middleware' => ['permission:contratista-create']]);
  Route::post('contratistas/crear', ['as' => 'contratistas.store', 'uses' => 'ContratistasController@store', 'middleware' => ['permission:contratista-create']]);
  Route::get('contratistas/{id}', ['as' => 'contratistas.show', 'uses' => 'ContratistasController@show', 'middleware' => ['permission:contratista-view']]);
  Route::get('contratistas/{id}/editar', ['as' => 'contratistas.edit', 'uses' => 'ContratistasController@edit', 'middleware' => ['permission:contratista-edit']]);
  Route::post('contratistas/{id}', ['as' => 'contratistas.update', 'uses' => 'ContratistasController@update', 'middleware' => ['permission:contratista-edit']]);

  Route::get('contratistas/preDelete/{id}', ['as' => 'contratistas.preDelete', 'uses' => 'ContratistasController@preDelete', 'middleware' => ['permission:contratista-delete']]);
  Route::get('contratistas/eliminar/{id}', ['as' => 'contratistas.delete', 'uses' => 'ContratistasController@delete', 'middleware' => ['permission:contratista-delete']]);

  Route::get('contratistas/preDeleteTelefono/{id}', ['as' => 'contratistas.preDeleteTelefono', 'uses' => 'ContratistasController@preDeleteTelefono', 'middleware' => ['permission:contratista-delete']]);
  Route::get('contratistas/eliminarTelefono/{id}', ['as' => 'contratistas.deleteTelefono', 'uses' => 'ContratistasController@deleteTelefono', 'middleware' => ['permission:contratista-delete']]);

  Route::get('contratistas/preDeleteContratista/{id}', ['as' => 'contratistas.preDeleteContratista', 'uses' => 'ContratistasController@preDeleteContratista', 'middleware' => ['permission:contratista-delete']]);
  Route::get('contratistas/eliminarContratista/{id}/{uteId}', ['as' => 'contratistas.deleteContratista', 'uses' => 'ContratistasController@deleteContratista', 'middleware' => ['permission:contratista-delete']]);

  Route::post('contratistas/{id}/agregar', ['as' => 'contratistas.updateUte', 'uses' => 'ContratistasController@updateUte', 'middleware' => ['permission:contratista-create']]);
  ////////////////// FIN Contratistas //////////////////

  ////////////////// Usuarios Contratistas //////////////////
  Route::get('usuarios/', ['as' => 'contratistas.usuarios.index', 'uses' => 'UsuariosContratistasController@index', 'middleware' => ['permission:usuario-list']]);
  Route::get('usuarios/crear', ['as' => 'contratistas.usuarios.create', 'uses' => 'UsuariosContratistasController@showRegistrationForm', 'middleware' => ['permission:usuario-edit']]);
  Route::post('usuarios/store', ['as' => 'contratistas.usuarios.store', 'uses' => 'UsuariosContratistasController@register', 'middleware' => ['permission:usuario-edit']]);
  Route::get('usuarios/{id}/editar', ['as' => 'contratistas.usuarios.edit', 'uses' => 'UsuariosContratistasController@edit', 'middleware' => ['permission:usuario-edit']]);
  Route::post('usuarios/{id}', ['as' => 'contratistas.usuarios.update', 'uses' => 'UsuariosContratistasController@update', 'middleware' => ['permission:usuario-edit']]);
  Route::get('usuarios/{id}/{accion}', ['as' => 'contratistas.usuarios.preToggleHabilitar', 'uses' => 'UsuariosContratistasController@preToggleHabilitar', 'middleware' => ['permission:usuario-enable|usuario-disable']]);
  Route::get('usuarios/toggleHabilitado/{id}/{accion}', ['as' => 'contratistas.usuarios.toggleHabilitado', 'uses' => 'UsuariosContratistasController@toggleHabilitado', 'middleware' => ['permission:usuario-enable|usuario-disable']]);
  Route::post('usuarios/exportar/usuarios', ['as' => 'contratistas.export.usuarios', 'uses' => 'UsuariosContratistasController@exportar', 'middleware' => ['permission:usuario-export']]);
  ////////////////// FIN Usuarios Contratistas //////////////////

  ////////////////// Publicaciones //////////////////
  Route::get('publicaciones', ['as' => 'publicaciones.index', 'uses' => 'PublicacionesController@index', 'middleware' => ['permission:publicacion-list']]);
  Route::post('publicaciones', ['as' => 'publicaciones.index.post', 'uses' => 'PublicacionesController@index', 'middleware' => ['permission:publicacion-list']]);

  Route::get('publicaciones/historial/{id}', ['as' => 'publicaciones.historial', 'uses' => 'PublicacionesController@historial', 'middleware' => ['permission:publicacion-view']]);

  Route::get('publicaciones/crear', ['as' => 'publicaciones.create', 'uses' => 'PublicacionesController@create', 'middleware' => ['permission:publicacion-create']]);
  Route::post('publicaciones/store', ['as' => 'publicaciones.store', 'uses' => 'PublicacionesController@store', 'middleware' => ['permission:publicacion-create']]);

  Route::get('publicaciones/{id}/ver', ['as' => 'publicaciones.show', 'uses' => 'PublicacionesController@show', 'middleware' => ['permission:publicacion-view']]);
  Route::get('publicaciones/{id}/editar', ['as' => 'publicaciones.edit', 'uses' => 'PublicacionesController@edit', 'middleware' => ['permission:publicacion-edit']]);
  Route::post('publicaciones/{id}', ['as' => 'publicaciones.update', 'uses' => 'PublicacionesController@update', 'middleware' => ['permission:publicacion-edit']]);

  Route::post('publicaciones/{id}/publicar', ['as' => 'publicaciones.publicar', 'uses' => 'PublicacionesController@publicar', 'middleware' => ['permission:publicacion-publicar']]);
  Route::post('publicaciones/{id}/rechazar', ['as' => 'publicaciones.rechazar', 'uses' => 'PublicacionesController@rechazar', 'middleware' => ['permission:publicacion-rechazar']]);

  Route::post('publicaciones/{accion}/{id}/preValidacion', ['as' => 'publicaciones.preValidacion', 'uses' => 'PublicacionesController@preValidacion', 'middleware' => ['permission:publicacion-edit']]);

  Route::get('publicaciones/reportes', ['as' => 'publicaciones.reporteIndices', 'uses' => 'PublicacionesController@reporteIndices', 'middleware' => ['permission:indice-list']]);
  Route::get('publicaciones/getHtmlTablareporteIndices/{anio}/{moneda_id}', ['as' => 'publicaciones.getHtmlTablareporteIndices', 'uses' => 'PublicacionesController@getHtmlTablareporteIndices', 'middleware' => ['permission:indice-list']]);

  Route::get('publicaciones/fuentes', ['as' => 'publicaciones.fuentesIndices', 'uses' => 'PublicacionesController@fuentesIndices', 'middleware' => ['permission:indice-list']]);
  Route::get('publicaciones/getHtmlTablafuentesIndices/{de}/{a}/{moneda_id}', ['as' => 'publicaciones.getHtmlTablafuentesIndices', 'uses' => 'PublicacionesController@getHtmlTablafuentesIndices', 'middleware' => ['permission:indice-list']]);
  Route::post('publicaciones/exportar/publicacion', ['as' => 'publicaciones.export', 'uses' => 'PublicacionesController@exportar', 'middleware' => ['permission:publicacion-export']]);

  Route::post('publicaciones/exportar/{id}/publicacion/edit', ['as' => 'publicaciones.export.edit', 'uses' => 'PublicacionesController@exportarEdicion', 'middleware' => ['permission:publicacion-export']]);

  Route::post('publicaciones/exportar/{anio}/{moneda_id}/exportarIndices', ['as' => 'publicaciones.export.exportarIndices', 'uses' => 'PublicacionesController@exportarIndices', 'middleware' => ['permission:publicacion-export']]);
  Route::post('publicaciones/exportar/{de}/{a}/{moneda_id}/exportarFuentes', ['as' => 'publicaciones.export.exportarFuentes', 'uses' => 'PublicacionesController@exportarFuentes', 'middleware' => ['permission:publicacion-export']]);

  Route::get('publicaciones/moneda/{id}', ['as' => 'publicaciones.filtrarPorMoneda', 'uses' => 'PublicacionesController@filtrarPorMoneda', 'middleware' => ['permission:publicacion-list']]);
  Route::post('publicaciones/moneda/{id}', ['as' => 'publicaciones.filtrarPorMoneda', 'uses' => 'PublicacionesController@filtrarPorMoneda', 'middleware' => ['permission:publicacion-list']]);
  Route::post('publicaciones/moneda/{id}/opciones', ['as' => 'publicaciones.publicacionesDisponiblesPorMoneda', 'uses' => 'PublicacionesController@publicacionesDisponiblesPorMoneda', 'middleware' => ['permission:publicacion-create']]);
  ////////////////// FIN Publicaciones //////////////////

  ////////////////// Indices //////////////////
  Route::get('indices/crear/{publicacion_id}', ['as' => 'indices.create', 'uses' => 'IndicesController@create', 'middleware' => ['permission:indice-create']]);
  Route::post('indices/crear/{publicacion_id}', ['as' => 'indices.store', 'uses' => 'IndicesController@store', 'middleware' => ['permission:indice-create']]);

  Route::get('indices/{id}/{publicacion_id}/editar', ['as' => 'indices.edit', 'uses' => 'IndicesController@edit', 'middleware' => ['permission:indice-edit']]);
  Route::post('indices/{id}/{publicacion_id}/editar', ['as' => 'indices.update', 'uses' => 'IndicesController@update', 'middleware' => ['permission:indice-edit']]);
  Route::get('indices/{id}/{publicacion_id}/ver', ['as' => 'indices.show', 'uses' => 'IndicesController@show', 'middleware' => ['permission:indice-view']]);

  Route::get('html/getSubCategorias/{id}', ['as' => 'html.getSubCategorias', 'uses' => 'IndicesController@getSubCategorias']);

  Route::get('indices/{id}/{publicacion_id}/validarDeshabilitar', ['as' => 'indices.validarDeshabilitar', 'uses' => 'IndicesController@validarDeshabilitar', 'middleware' => ['permission:indice-deshabilitar']]);
  Route::post('indices/{id}/{publicacion_id}/deshabilitar', ['as' => 'indices.deshabilitar', 'uses' => 'IndicesController@deshabilitar', 'middleware' => ['permission:indice-deshabilitar']]);

  Route::get('indices/calculador', ['as' => 'indices.calculador', 'uses' => 'IndicesController@calculador', 'middleware' => ['permission:indice-create']]);
  Route::post('indices/storeCalculado', ['as' => 'indices.storeCalculado', 'uses' => 'IndicesController@storeCalculado', 'middleware' => ['permission:indice-create']]);

  Route::post('exportar/indices', ['as' => 'export.indices', 'uses' => 'IndicesController@exportar', 'middleware' => ['permission:indice-export']]);
  ////////////////// FIN Indices //////////////////

  ////////////////// Reportes //////////////////
  Route::get('reportes', ['as' => 'reportes.index', 'uses' => 'ReportesController@index', 'middleware' => ['permission:ReporteAdendas|ReporteEconomico|ReporteFinancieroo|ReporteFisico|ReporteRedeterminaciones']]);
  Route::get('reportes/{nombre}/generar', ['as' => 'reportes.generar', 'uses' => 'ReportesController@generar', 'middleware' => ['permission:ReporteAdendas|ReporteEconomico|ReporteFinancieroo|ReporteFisico|ReporteRedeterminaciones']]);
  Route::post('reportes/{nombre}/exportar', ['as' => 'reportes.exportar', 'uses' => 'ReportesController@exportar', 'middleware' => ['permission:ReporteAdendas|ReporteEconomico|ReporteFinancieroo|ReporteFisico|ReporteRedeterminaciones']]);
  ////////////////// FIN Reportes //////////////////

  ////////////////// Alarmas //////////////////
  Route::get('alarmas/solicitud', ['as' => 'alarmas.solicitud', 'uses' => 'AlarmasController@indexSolicitud', 'middleware' => ['permission:alarma-list']]);
  Route::get('alarmas/solicitud/crear', ['as' => 'alarmas.solicitud.create', 'uses' => 'AlarmasController@createSolicitud', 'middleware' => ['permission:alarma-create']]);
  Route::post('alarmas/solicitud/crear', ['as' => 'alarmas.solicitud.update', 'uses' => 'AlarmasController@updateSolicitud', 'middleware' => ['permission:alarma-create']]);
  Route::get('alarmas/solicitud/{id}/ver', ['as' => 'alarmas.solicitud.show', 'uses' => 'AlarmasController@showSolicitud', 'middleware' => ['permission:alarma-create']]);

  Route::get('alarmas/solicitud/{id}/editar', ['as' => 'alarmas.solicitud.edit', 'uses' => 'AlarmasController@editSolicitud', 'middleware' => ['permission:alarma-create']]);
  Route::post('alarmas/solicitud/{id}/editar', ['as' => 'alarmas.solicitud.edit.post', 'uses' => 'AlarmasController@editSolicitudPost', 'middleware' => ['permission:alarma-create']]);

  Route::get('alarmas/solicitud/{id}/habilitar', ['as' => 'alarmas.solicitud.habilitar', 'uses' => 'AlarmasController@habilitar', 'middleware' => ['permission:alarma-create']]);
  Route::get('alarmas/solicitud/{id}/deshabilitar', ['as' => 'alarmas.solicitud.deshabilitar', 'uses' => 'AlarmasController@deshabilitar', 'middleware' => ['permission:alarma-create']]);
  ////////////////// Fin Alarmas //////////////////

});
////////////////// FIN Route::group(['middleware' => ['auth']], function() { //////////////////

////////////////// Test //////////////////
Route::group(array('prefix' => 'test'), function () {
  if (config('custom.test_mode') != 'true') {
    return redirect('/');
  }

  Route::get('testRoutes', ['as' => 'test.testRoutes', function () {
    return view('test.index');
  }]);

  Route::get('/phpinfo', ['as' => 'test.phpinfo', function () {
    echo phpinfo();
  }]);

  Route::get('runJobs', ['as' => 'test.runJobs', 'uses' => 'TestController@runJobs']);
  Route::get('asociarAPublic', ['as' => 'test.asociarAPublic', 'uses' => 'TestController@asociarAPublic']);
  Route::get('asociarContratoIdAPublic/{contrato_id}', ['as' => 'test.asociarContratoIdAPublic', 'uses' => 'TestController@asociarContratoIdAPublic']);
  // Route::get('fixCertificadoCambioAnio', ['as' => 'test.fixCertificadoCambioAnio', 'uses' => 'TestController@fixCertificadoCambioAnio']);
  Route::get('reCalculoMontoYSaldo/{contrato_id}', ['as' => 'test.reCalculoMontoYSaldo', 'uses' => 'TestController@reCalculoMontoYSaldo']);
  Route::get('finalizarPendientes', ['as' => 'test.finalizarPendientes', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@finalizarPendientes']);
});
////////////////// FIN Test //////////////////
