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
Route::post('registro/nueva/contrasenia', ['as' => 'register.confirmationmail', 'uses' => 'ConfirmarMailController@confirmarMailUsuario']);
Route::get('register/verify/{confirmationCode}', ['as' => 'register.verify', 'uses' => 'ConfirmarMailController@confirmarUsuario']);
Route::get('reenviar/confirmacion/{confirmation_code}', ['as' => 'reenviar.confirmacion', 'uses' => 'ConfirmarMailController@reenviarConfirmarUsuario']);

Route::get('users/generarContrasenia', ['as' => 'users.generarContrasenia', 'uses' => 'ConfirmarMailController@generarPassword']);
Route::post('users/generarPasswsord', ['as' => 'users.finConfirmacionUsuario', 'uses' => 'ConfirmarMailController@finConfirmacionUsuario']);
////////////////// FIN Confirmacion de usuario //////////////////

////////////////// Auth Routes //////////////////
// Login Routes (en espaniol)
Route::get('ingresar', ['as' => 'ingresar', 'uses' => 'Auth\LoginController@showLoginFormPublic']);
Route::get('inicio', ['as' => 'inicio', 'uses' => 'DashboardController@index']);

Route::post('ingresar', ['as' => 'ingresar.login', 'uses' => 'Auth\LoginController@loginPublic']);

// Password Reset Routes (en espaniol)
Route::get('recuperar/contrasenia', ['as' => 'recuperar.contrasenia', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestFormPublic']);
Route::post('contrasenia/email', ['as' => 'contrasenia.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmailPublic']);

Route::post('recuperar/contrasenia', ['as' => '', 'uses' => 'Auth\ResetPasswordController@reset']);
Route::get('recuperar/contrasenia/{token}', ['as' => 'recuperar.contrasenia.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);

// Register Routes (en espaniol)
// Route::get('registrarse', ['as' => 'register', 'uses' => 'Auth\RegisterController@showRegistrationForm']);
// Route::post('registrarse', ['as' => 'registrarse', 'uses' => 'Auth\RegisterController@register']);
Route::get('terminos-y-condiciones', ['as' => 'login.terminos_y_condiciones', 'uses' => 'Auth\RegisterController@terminosYCondiciones']);

// Comento Register original de Auth para que un usuario no pueda registrarse
Route::match(['get', 'post'], 'register', function () {
    return redirect('/');
});
////////////////// FIN Auth Routes //////////////////

//////////// Publicaciones ////////////
Route::get('publicaciones', ['as' => 'publicaciones.index', 'uses' => 'PublicacionesController@index']);
Route::post('publicaciones', ['as' => 'publicaciones.index.post', 'uses' => 'PublicacionesController@index']);
Route::post('publicaciones/exportar/publicacion', ['as' => 'publicaciones.export', 'uses' => 'PublicacionesController@exportar']);

Route::get('publicaciones/reportes', ['as' => 'publicaciones.reportes', 'uses' => 'PublicacionesController@reporteIndices']);
Route::get('publicaciones/getHtmlTablareporteIndices/{anio}/{moneda_id}', ['as' => 'publicaciones.getHtmlTablareporteIndices', 'uses' => 'PublicacionesController@getHtmlTablareporteIndices']);

Route::get('publicaciones/fuentes', ['as' => 'publicaciones.fuentesIndices', 'uses' => 'PublicacionesController@fuentesIndices']);
Route::get('publicaciones/getHtmlTablafuentesIndices/{de}/{a}/{moneda_id}', ['as' => 'publicaciones.getHtmlTablafuentesIndices', 'uses' => 'PublicacionesController@getHtmlTablafuentesIndices']);

Route::post('publicaciones/exportar/{id}/{moneda_id}/exportarIndices', ['as' => 'publicaciones.export.exportarIndices', 'uses' => 'PublicacionesController@exportarIndices']);
Route::post('publicaciones/exportar/{de}/{a}/exportarFuentes', ['as' => 'publicaciones.export.exportarFuentes', 'uses' => 'PublicacionesController@exportarFuentes']);

Route::get('publicaciones/moneda/{id}', ['as' => 'publicaciones.filtrarPorMoneda', 'uses' => 'PublicacionesController@filtrarPorMoneda']);
Route::post('publicaciones/moneda/{id}', ['as' => 'publicaciones.filtrarPorMoneda', 'uses' => 'PublicacionesController@filtrarPorMoneda']);
//////////// FIN Publicaciones ////////////

//////////// Normativas ////////////
Route::get('normativas', ['as' => 'normativas.index', function () {
    return view('normativas/index');
}]);
//////////// FIN Normativas ////////////

////////////////// Descarga de Excel //////////////////
Route::get('excel/exports/{filename}', [
    function ($filename) {
        $filePath = storage_path() . '/excel/exports/' . $filename;
        $fileContents = File::get($filePath);
        return Response::make($fileContents, 200, array('Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
    }
]);
////////////////// FIN Descarga de Excel //////////////////

Route::group(['middleware' => ['auth']], function () {
    //////////// Notifications ////////////
    Route::get('markAsRead/{notification}/{route}/{routeParam}', ['as' => 'notification.markAsRead', 'uses' => 'NotificationsController@markAsRead']);
    Route::get('markAllAsRead', ['as' => 'notification.markAllAsRead', 'uses' => 'NotificationsController@markAllAsRead']);
    //////////// Fin Notifications ////////////

    ////////////////// Dashboard //////////////////
    Route::resource('/', 'DashboardController');
    Route::get('widget/dashboard/{widget}', ['as' => 'widget.dashboard', 'uses' => 'DashboardController@widget']);

    Route::get('layout', ['as' => 'layout', 'uses' => 'DashboardController@elegirLayout']);
    Route::get('update/layout/{id}', ['as' => 'update.layout', 'uses' => 'DashboardController@updateLayout']);
    Route::get('widgets', ['as' => 'edit.widgets', 'uses' => 'DashboardController@editWidgets']);
    Route::post('update/widgets', ['as' => 'update.widgets', 'uses' => 'DashboardController@updateWidgets']);
    ////////////////// END Dashboard //////////////////

    ////////////////// Perfil //////////////////
    Route::get('seguridad/perfil', ['as' => 'seguridad.perfil', 'uses' => 'UsersController@perfil']);
    Route::post('seguridad/perfil', ['as' => 'seguridad.updatePerfil', 'uses' => 'UsersController@updatePerfil']);

    Route::get('seguridad/cambiarContrasenia', ['as' => 'seguridad.cambiarContrasenia', 'uses' => 'UsersController@cambiarContrasenia']);
    Route::post('seguridad/cambiarContrasenia', ['as' => 'seguridad.newPassword', 'uses' => 'UsersController@newPassword']);
    ////////////////// FIN Perfil //////////////////

    ////////////////// Contratos //////////////////
    Route::get('contratos/misContratos', ['as' => 'contratos.index', 'uses' => 'Contratos\ContratosController@misContratos']);
    Route::post('contratos/misContratos', ['as' => 'contratos.index.post', 'uses' => 'Contratos\ContratosController@misContratos']);

    //////////// Vistas ajax por Performance ////////////
    Route::get('contratosgetViews/{id}/{seccion}/{version?}/{visualizacion?}', ['as' => 'contrato.getViews', 'uses' => 'Contratos\ContratosController@getViews']);
    //////////// FIN Vistas ajax por Performance ////////

    Route::get('adenda/{id}/ver', ['as' => 'adenda.ver', 'uses' => 'Contratos\ContratosController@verAdenda']);
    Route::get('ampliacion/{id}/ver', ['as' => 'ampliacion.ver', 'uses' => 'Contratos\ContratosController@verAmpliacion']);

    // Vistas ajax por Performance
    Route::get('ampliacionCronogramagetViews/{id}/{seccion}/{visualizacion?}', ['as' => 'ampliacion.editar.getViews', 'uses' => 'Contratos\ContratosController@getViewsCronograma']);

    Route::post('contratos/exportar/contrato', ['as' => 'contrato.export', 'uses' => 'Contratos\ContratosController@exportarContratos']);
    Route::post('contratos/exportar/cronograma', ['as' => 'export.cronograma', 'uses' => 'Contratos\ContratosController@exportarCronograma']);
    Route::post('contratos/exportar/itemizado', ['as' => 'export.itemizado', 'uses' => 'Contratos\ContratosController@exportarItemizado']);

    Route::get('contratos/{id}/ver', ['as' => 'contratos.ver', 'uses' => 'Contratos\ContratosController@verContrato']);

    // Visualizacion de Contrato en estado incompleto
    Route::get('contratos/{id}/{accion}/editar', ['as' => 'contratos.editar.incompleto', 'uses' => 'Contratos\ContratosController@verEditar']);
    Route::get('contratos/{id}/{accion}/ver', ['as' => 'contratos.ver.incompleto', 'uses' => 'Contratos\ContratosController@verEditar']);

    Route::get('contratos/salto/{id_variacion}', ['as' => 'contratos.verSalto', 'uses' => 'Contratos\ContratosController@verSalto']);

    Route::get('contratoshistorial/{clase_id}/{clase_type}/{seccion}', ['as' => 'contrato.historial', 'uses' => 'Contratos\ContratosController@historial']);
    ////////////////// FIN Contratos //////////////////

    ////////////////// Solicitudes de Contrato //////////////////
    Route::get('contratos/asociar', ['as' => 'contrato.asociar', 'uses' => 'Contratos\ContratosController@asociar']);
    Route::post('contratos/asociar', ['as' => 'contrato.asociar.post', 'uses' => 'Contratos\ContratosController@updateAsociar']);

    Route::get('contratos/misSolicitudes', ['as' => 'contrato.solicitudes', 'uses' => 'Contratos\ContratosController@solicitudes']);
    Route::post('contratos/misSolicitudes', ['as' => 'contrato.solicitudes.post', 'uses' => 'Contratos\ContratosController@solicitudes']);
    Route::post('exportar/solicitudes/contrato', ['as' => 'solicitudes.contrato.export', 'uses' => 'ContratosController@exportarSolicitudes']);

    Route::get('contrato/solicitudes/{id}/ver', ['as' => 'contrato.solicitud.ver', 'uses' => 'Contratos\ContratosController@verSolicitud']);

    Route::get('widget/contratos/{widget}/{contrato_id}/{version}', ['as' => 'widget.contratos', 'uses' => 'Contratos\ContratosController@widget']);
    ////////////////// FIN Solicitudes de Contrato //////////////////

    ////////////////// Solicitudes de Redeterminacion //////////////////
    Route::get('solicitudes/redeterminacion', ['as' => 'redeterminaciones.index', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@index']);
    Route::post('solicitudes/redeterminacion', ['as' => 'redeterminaciones.index.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@index']);
    Route::post('exportar/solicitudes/redeterminacion', ['as' => 'solicitudes.redeterminacion.export', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@exportar']);

    Route::get('solicitudes/redeterminacion/solicitar/{id?}', ['as' => 'solicitudes.redeterminaciones.solicitar', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@solicitar']);
    Route::post('solicitudes/redeterminacion/solicitar', ['as' => 'solicitudes.redeterminaciones.solicitar.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@updateSolicitar']);
    Route::get('solicitudesRedeterminacion/{id}/ver', ['as' => 'solicitudes.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@ver']);

    Route::get('modalRedeterminacion/{instancia}/{id_solicitud}/{correccion}', ['as' => 'solicitudes.create.edit', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@createEdit']);
    Route::post('redeterminacion/guardar/{instancia}/{id_solicitud}/{correccion}', ['as' => 'solicitudes.update.store', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@updateOrStore']);

    Route::get('solicitudesRedeterminacion/descargarActa/{id}/{tipo}/{acta_id?}', ['as' => 'solicitudes.descargarActa', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActa']);

    // Route::get('solicitudesRedeterminacion/descargarActaRDP/{id}/{solicitud_id?}', ['as' => 'solicitudes.descargarActaRDP', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActaRDP']);
    ////////////////// FIN Solicitudes de Redeterminacion //////////////////

    ////////////////// Analisis de Precios //////////////////
    Route::get('AnalisisPrecios/{contrato_id}/editar', ['as' => 'AnalisisPrecios.edit', 'uses' => 'AnalisisPreciosController@edit']);
    Route::post('AnalisisPrecios/{contrato_id}/editar', ['as' => 'AnalisisPrecios.update', 'uses' => 'AnalisisPreciosController@update']);
    Route::post('AnalisisPrecios/{contrato_id}/exportar/AnalisisPrecios', ['as' => 'AnalisisPrecios.exportar', 'uses' => 'AnalisisPreciosController@exportar']);
    Route::get('AnalisisPrecios/historial/{contrato_id}', ['as' => 'AnalisisPrecios.historial', 'uses' => 'AnalisisPreciosController@historial']);
    Route::get('AnalisisPrecios/historialDetalle/{contrato_id}', ['as' => 'AnalisisPrecios.historialDetalle', 'uses' => 'AnalisisPreciosController@historialDetalle']);

    Route::get('AnalisisPrecios/categorias/{item_id}/add', ['as' => 'AnalisisPrecios.categorias.add', 'uses' => 'AnalisisPreciosController@addCategoria']);
    Route::post('AnalisisPrecios/categorias/{item_id}/add', ['as' => 'AnalisisPrecios.categorias.add.post', 'uses' => 'AnalisisPreciosController@addCategoriaPost']);

    Route::get('AnalisisPrecios/coeficiente/{coeficiente_id}/{dato}', ['as' => 'AnalisisPrecios.coeficiente.edit', 'uses' => 'AnalisisPreciosController@editCoeficiente']);
    Route::post('AnalisisPrecios/coeficiente/{coeficiente_id}/{dato}', ['as' => 'AnalisisPrecios.coeficiente.edit.update', 'uses' => 'AnalisisPreciosController@editCoeficientePost']);

    Route::get('AnalisisPrecios/insumos/{item_categoria_id}/add', ['as' => 'AnalisisPrecios.insumos.add', 'uses' => 'AnalisisPreciosController@addEditInsumo']);
    Route::post('AnalisisPrecios/insumos/{item_categoria_id}/add', ['as' => 'AnalisisPrecios.insumos.add.post', 'uses' => 'AnalisisPreciosController@addEditInsumoPost']);

    Route::get('AnalisisPrecios/error/{modelo}/{contrato_id}/{id?}', ['as' => 'AnalisisPrecios.error', 'uses' => 'AnalisisPreciosController@editError']);
    Route::post('AnalisisPrecios/error/{modelo}/{contrato_id}/{id?}', ['as' => 'AnalisisPrecios.error.update', 'uses' => 'AnalisisPreciosController@updateError']);

    Route::get('AnalisisPrecios/{contrato_id}/enviar_aprobar', ['as' => 'AnalisisPrecios.enviar_aprobar', 'uses' => 'AnalisisPreciosController@enviar_aprobar']);

    // HTML Request
    Route::get('AnalisisPrecios/item/{categoria_obra}/{contrato_id}/detalle', ['as' => 'AnalisisPrecios.item.detalle', 'uses' => 'AnalisisPreciosController@detalleItem']);

    ////////////////// FIN Analisis de Precios //////////////////

    ////////////////// Html Requests ////////////////////
    Route::get('html/SelectContrato/{id}', ['as' => 'html.SelectContrato', 'uses' => 'HtmlGetController@SelectContrato']);
    Route::get('html/GetSaltos/{id}', ['as' => 'html.GetSaltos', 'uses' => 'HtmlGetController@GetSaltos']);
    ////////////////// FIN Html Requests //////////////////
});

////////////////// Test //////////////////
Route::group(array('prefix' => 'test'), function () {
    Route::get('confirmarusuario/{cuit}', ['as' => 'test.confirmar.usuario', 'uses' => 'TestController@confirmarUsuario']);
    Route::get('validar/{cuit}', ['as' => 'test.validar.cuit', 'uses' => 'TestController@validarCuit']);
    Route::get('user', function () {
        return Auth::user();
    });
});
////////////////// FIN Test //////////////////



Route::get('exception/index', 'ExceptionController@index');
