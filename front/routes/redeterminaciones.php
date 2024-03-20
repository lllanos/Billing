<?php

Route::group(['middleware' => ['auth']], function() {
////////////////// Solicitudes de Redeterminacion //////////////////
    Route::get('solicitudesRedeterminacion/EnProceso', ['as' => 'solicitudes.redeterminaciones_en_proceso', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexEnProceso']);
    Route::post('solicitudesRedeterminacion/EnProceso', ['as' => 'solicitudes.redeterminaciones_en_proceso.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexEnProceso']);

    Route::get('solicitudesRedeterminacion/Finalizadas', ['as' => 'solicitudes.redeterminaciones_finalizadas', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexFinalizadas']);
    Route::post('solicitudesRedeterminacion/Finalizadas', ['as' => 'solicitudes.redeterminaciones_finalizadas.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@indexFinalizadas']);

    Route::get('solicitudesRedeterminacion/{id}/ver', ['as' => 'solicitudes.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@ver']);

    Route::post('redeterminacion/guardar/{instancia}/{id_solicitud}/{correccion}', ['as' => 'solicitudes.update.store', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@updateOrStore']);

    Route::get('solicitudesRedeterminacion/descargarActa/{id}/{tipo}/{acta_id?}', ['as' => 'solicitudes.descargarActa', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActa']);
    // Route::post('solicitudesRedeterminacion/descargarActaPost/{id}/{tipo}/{acta_id?}', ['as' => 'solicitudes.descargarActa.post', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@descargarActa', 'middleware' => ['permission:redeterminaciones-en_proceso-view|redeterminaciones-finalizadas-view']]);
    Route::get('cuadroComparativo/{id}/ver', ['as' => 'cuadroComparativo.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@verCuadroComparativo']);
    Route::get('cuadroComparativo/item/{id}/ver', ['as' => 'cuadroComparativo.item.ver', 'uses' => 'Redeterminaciones\SolicitudesRedeterminacionController@verItemCuadroComparativo']);

    Route::get('redeterminaciones/certificado/{id}/ver/{breadcrumb?}', ['as' => 'redeterminaciones.certificado.ver', 'uses' => 'Contratos\SolicitudesCertificadosController@verRedeterminacion']);

    Route::get('redeterminaciones/certificado/{id}/enviar_aprobar', ['as' => 'redeterminaciones.certificado.enviar_aprobar', 'uses' => 'Contratos\SolicitudesCertificadosController@solicitarEnviarAprobar']);
////////////////// FIN Solicitudes de Redeterminacion //////////////////

////////////////// Redeterminaciones //////////////////
    Route::get('redeterminacion/{redeterminacion_id}/ver', ['as' => 'redeterminacion.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@edit']);
////////////////// FIN Redeterminaciones //////////////////

});
