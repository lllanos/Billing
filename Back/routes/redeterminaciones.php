<?php

Route::group(['middleware' => ['auth']], function() {
////////////////// Solicitudes de Redeterminacion //////////////////
    Route::get('solicitudesRedeterminacion/EnProceso', ['as' => 'solicitudes.redeterminaciones_en_proceso', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexEnProceso', 'middleware' => ['permission:redeterminaciones-en_proceso-list']]);
    Route::post('solicitudesRedeterminacion/EnProceso', ['as' => 'solicitudes.redeterminaciones_en_proceso.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexEnProceso', 'middleware' => ['permission:redeterminaciones-en_proceso-list']]);

    Route::get('solicitudesRedeterminacion/Finalizadas', ['as' => 'solicitudes.redeterminaciones_finalizadas', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexFinalizadas', 'middleware' => ['permission:redeterminaciones-finalizadas-list']]);
    Route::post('solicitudesRedeterminacion/Finalizadas', ['as' => 'solicitudes.redeterminaciones_finalizadas.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexFinalizadas', 'middleware' => ['permission:redeterminaciones-finalizadas-list']]);

    Route::get('solicitudesRedeterminacion/{id}/ver', ['as' => 'solicitudes.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@ver', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);

    Route::get('modalRedeterminacion/{instancia}/{id_solicitud}/{correccion}', ['as' => 'solicitudes.create.edit', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@createEdit']);

    Route::post('redeterminacion/guardar/{instancia}/{id_solicitud}/{correccion}', ['as' => 'solicitudes.update.store', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@updateOrStore']);
    Route::post('exportar/solicitudes/redeterminacion/{estado}', ['as' => 'solicitudes.redeterminacion.export', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@exportar', 'middleware' => ['permission:redeterminaciones-en_proceso-export|redeterminaciones-finalizadas-export']]);

    // Route::post('solicitudesRedeterminacion/{id}/rechazar', ['as' => 'solicitudes.rechazar', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@rechazar', 'middleware' => ['permission:redeterminaciones-rechazar']]);
    // Route::post('solicitudesRedeterminacion/{id}/suspender', ['as' => 'solicitudes.suspender', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@suspender', 'middleware' => ['permission:redeterminaciones-suspender']]);
    // Route::post('solicitudesRedeterminacion/{id}/continuar', ['as' => 'solicitudes.continuar', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@continuar', 'middleware' => ['permission:redeterminaciones-continuar']]);

    Route::get('solicitudesRedeterminacion/descargarActa/{id}/{tipo}/{acta_id?}', ['as' => 'solicitudes.descargarActa', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActa', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);
    // Route::post('solicitudesRedeterminacion/descargarActaPost/{id}/{tipo}/{acta_id?}', ['as' => 'solicitudes.descargarActa.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActa', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);
    Route::get('cuadroComparativo/{id}/ver', ['as' => 'cuadroComparativo.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@verCuadroComparativo', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);

    Route::get('cuadroComparativo/item/{id}/ver', ['as' => 'cuadroComparativo.item.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@verItemCuadroComparativo', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);

    Route::get('redeterminaciones/certificado/{id}/ver/{breadcrumb?}', ['as' => 'redeterminaciones.certificado.ver', 'uses' => 'Contratos\SolicitudesCertificadosController@verRedeterminacion', 'middleware' => ['permission:certificado-view']]);

////////////////// FIN Solicitudes de Redeterminacion //////////////////

////////////////// Redeterminaciones //////////////////
    Route::get('redeterminacion/{redeterminacion_id}/ver', ['as' => 'redeterminacion.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@edit', 'middleware' => ['permission:redeterminaciones-ver']]);
////////////////// FIN Redeterminaciones //////////////////

Route::get('FakeJobController/CalculoPrecios', ['as' => 'redeterminacion.CalculoPrecios', 'uses' => 'Redeterminaciones\FakeJobController@CalculoPrecios']);
});
