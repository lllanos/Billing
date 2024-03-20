<?php

Route::group(['middleware' => ['auth']], function() {

    #region Solicitudes de Contrato

    // Route::get('contratos/solicitudes', ['as' => 'solicitudes.index', 'uses' => 'SolicitudesController@index', 'middleware' => ['permission:sol-contrato-pendientes-list|sol-contrato-finalizadas-list']]);
    Route::get('contratos/solicitudes/asociacionesPendientes', ['as' => 'solicitudes.asociaciones_pendientes', 'uses' => 'SolicitudesContratoController@asociacionesPendientes', 'middleware' => ['permission:sol-contrato-pendientes-view']]);
    Route::get('contratos/solicitudes/asociacionesFinalizadas', ['as' => 'solicitudes.asociaciones_finalizadas', 'uses' => 'SolicitudesContratoController@asociacionesFinalizadas', 'middleware' => ['permission:sol-contrato-finalizadas-view']]);
    Route::post('exportar/solicitudes/contrato/{estado}', ['as' => 'solicitudes.contrato.export', 'uses' => 'SolicitudesContratoController@exportar', 'middleware' => ['permission:sol-contrato-pendientes-export|sol-contrato-finalizadas-export']]);

    Route::get('contratos/solicitudes/{id}/ver', ['as' => 'contrato.solicitud.ver', 'uses' => 'SolicitudesContratoController@verSolicitud', 'middleware' => ['permission:sol-contrato-pendientes-view|sol-contrato-finalizadas-view']]);
    Route::post('contratos/solicitudes/{id}/aprobar', ['as' => 'contrato.solicitud.aprobar', 'uses' => 'SolicitudesContratoController@aprobarSolicitud', 'middleware' => ['permission:sol-contrato-aprobar']]);
    Route::post('contratos/solicitudes/{id}/rechazar', ['as' => 'contrato.solicitud.rechazar', 'uses' => 'SolicitudesContratoController@rechazarSolicitud', 'middleware' => ['permission:sol-contrato-rechazar']]);

    #endregion

    #region Contratos

    Route::get('contratos', ['as' => 'contratos.index', 'uses' => 'Contratos\ContratosController@index', 'middleware' => ['permission:contrato-list']]);
    Route::post('contratos', ['as' => 'contratos.index.post', 'uses' => 'Contratos\ContratosController@index', 'middleware' => ['permission:contrato-list']]);
    Route::get('contratos/{id}/ver', ['as' => 'contratos.ver', 'uses' => 'Contratos\ContratosController@ver', 'middleware' => ['permission:contrato-view']]);
    Route::get('contratos/{id}/firmar', ['as' => 'contratos.firmar', 'uses' => 'Contratos\ContratosController@sign', 'middleware' => ['permission:contrato-view']]);
    Route::get('contratos/{id}/borrador', ['as' => 'contratos.borrador', 'uses' => 'Contratos\ContratosController@draft', 'middleware' => ['permission:contrato-view']]);

    Route::get('contratos/salto/{id_variacion}/{id_cuadro?}', ['as' => 'contratos.verSalto', 'uses' => 'Contratos\ContratosController@verSalto', 'middleware' => ['permission:salto-view']]);

    Route::post('contratos/exportar/contrato/{publicados}', ['as'=>'contratos.export', 'uses'=>'Contratos\ContratosController@exportar', 'middleware' => ['permission:contrato-export']]);

    Route::get('contratos/crear', ['as' => 'contratos.create', 'uses' => 'Contratos\ContratosController@create', 'middleware' => ['permission:contrato-create']]);
    Route::post('contratos/store', ['as' => 'contratos.storeUpdate', 'uses' => 'Contratos\ContratosController@storeUpdate', 'middleware' => ['permission:contrato-create|contrato-edit|contrato-edit-borrador']]);

    Route::get('contratos/{id}/editar', ['as' => 'contratos.edit', 'uses' => 'Contratos\ContratosController@edit', 'middleware' => ['permission:contrato-edit|contrato-edit-borrador']]);

    Route::get('contratos/preDelete/{id}', ['as' => 'contratos.preDelete', 'uses' => 'Contratos\ContratosController@preDelete', 'middleware' => ['permission:contrato-delete']]);
    Route::get('contratos/eliminar/{id}', ['as' => 'contratos.delete', 'uses' => 'Contratos\ContratosController@delete', 'middleware' => ['permission:contrato-delete']]);

    Route::get('contratoshistorial/{clase_id}/{clase_type}/{seccion}', ['as' => 'contrato.historial', 'uses' => 'Contratos\ContratosController@historial', 'middleware' => ['permission:contrato-view|polinomica-edit|itemizado-manage|itemizado-edit|cronograma-manage|cronograma-edit']]);

    // Visualizacion de Contrato en estado incompleto
    Route::get('contratos/{id}/{accion}/editar', ['as' => 'contratos.editar.incompleto', 'uses' => 'Contratos\ContratosController@verEditar', 'middleware' => ['permission:contrato-view|polinomica-edit|itemizado-manage|itemizado-edit|cronograma-manage|cronograma-edit']]);
    Route::get('contratos/{id}/{accion}/ver', ['as' => 'contratos.ver.incompleto', 'uses' => 'Contratos\ContratosController@verEditar', 'middleware' => ['permission:contrato-view|polinomica-edit|itemizado-manage|itemizado-edit|cronograma-manage|cronograma-edit']]);

    // Vistas ajax por Performance
    Route::get('contratosgetViews/{id}/{seccion}/{version?}/{visualizacion?}', ['as' => 'contrato.editar.getViews', 'uses' => 'Contratos\ContratosController@getViews', 'middleware' => ['permission:contrato-view|polinomica-edit|itemizado-manage|itemizado-edit|cronograma-manage|cronograma-edit']]);

    #region Cronograma

    // Guardar cronograma definitivo
    Route::post('contratos/editar/updateOrStore/cronograma/{contrato_id}', ['as' => 'cronograma.updateOrStore', 'uses' => 'Contratos\CronogramaController@updateOrStore', 'middleware' => ['permission:cronograma-edit']]);
    Route::get('contratos/editar/firma/cronograma/{contrato_id}', ['as' => 'cronograma.firmar', 'uses' => 'Contratos\CronogramaController@firmar', 'middleware' => ['permission:cronograma-edit']]);
    Route::get('contratos/editar/borrador/cronograma/{contrato_id}', ['as' => 'cronograma.borrador', 'uses' => 'Contratos\CronogramaController@borrador', 'middleware' => ['permission:cronograma-edit']]);

    // Guardar valores de items
    Route::get('contratos/cronograma/{cronograma_id}/{item_id}', ['as' => 'cronograma.item.getHtmlEdit', 'uses' => 'Contratos\CronogramaController@getHtmlEdit', 'middleware' => ['permission:cronograma-edit']]);
    Route::post('contratos/cronograma/{cronograma_id}/{item_id}', ['as' => 'cronograma.item.updateItemCronograma', 'uses' => 'Contratos\CronogramaController@updateItemCronograma', 'middleware' => ['permission:cronograma-edit']]);
    Route::post('contratos/exportar/cronograma', ['as'=>'export.cronograma', 'uses'=>'Contratos\CronogramaController@exportar', 'middleware' => ['permission:cronograma-export']]);
    #endregion

    #region Polinomica
    Route::post('contratos/editar/updateOrStore/polinomica/{contrato_id}', ['as' => 'polinomica.updateOrStore', 'uses' => 'Contratos\PolinomicaController@updateOrStore', 'middleware' => ['permission:polinomica-edit']]);
    #endregion

    #region Itemizado
    Route::post('contratos/editar/updateOrStore/itemizado/{contrato_id}', ['as' => 'itemizado.updateOrStore', 'uses' => 'Contratos\ItemizadoController@updateOrStore', 'middleware' => ['permission:itemizado-edit']]);
    Route::get('contratos/editar/delete/itemizado/{item_id}', ['as' => 'itemizado.deleteItem', 'uses' => 'Contratos\ItemizadoController@deleteItem', 'middleware' => ['permission:itemizado-edit']]);
    Route::post('contratos/editar/add/itemizado/{item_id}', ['as' => 'itemizado.addItem', 'uses' => 'Contratos\ItemizadoController@addItem', 'middleware' => ['permission:itemizado-edit']]);
    Route::get('contratos/editar/get/itemizado/{item_id}', ['as' => 'itemizado.getItem', 'uses' => 'Contratos\ItemizadoController@getItem', 'middleware' => ['permission:itemizado-edit']]);
    Route::post('contratos/editar/finalizar/itemizado/{contrato_id}', ['as' => 'itemizado.finalizar', 'uses' => 'Contratos\ItemizadoController@finalizar', 'middleware' => ['permission:itemizado-edit']]);
    Route::get('contratos/editar/firmar/itemizado/{contrato_id}', ['as' => 'itemizado.firmar', 'uses' => 'Contratos\ItemizadoController@firmar', 'middleware' => ['permission:itemizado-edit']]);
    Route::get('contratos/editar/borrador/itemizado/{contrato_id}', ['as' => 'itemizado.borrador', 'uses' => 'Contratos\ItemizadoController@borrador', 'middleware' => ['permission:itemizado-edit']]);

    Route::get('itemizadogetViews/{id}/{accion}/{item_id}', ['as' => 'itemizado.getViews', 'uses' => 'Contratos\ItemizadoController@getViews', 'middleware' => ['permission:itemizado-manage|itemizado-edit']]);
    Route::post('contratos/exportar/itemizado', ['as'=>'export.itemizados', 'uses'=>'Contratos\ItemizadoController@exportar', 'middleware' => ['permission:itemizado-export']]);
    Route::get('itemizadoOrdenar', ['as' => 'itemizado.regenerar', 'uses' => 'Contratos\ItemizadoController@regenerarOrden', 'middleware' => ['permission:itemizado-manage|itemizado-edit']]);

    Route::get('updateItemizados', ['as' => 'itemizado.updateItemizados', 'uses' => 'Contratos\ItemizadoController@updateItemizados', 'middleware' => ['permission:itemizado-manage|itemizado-edit']]);

    #endregion

    #endregion

    #region Adenda

    Route::get('adenda/{contrato_id}/solicitar', ['as' => 'adenda.create', 'uses' => 'Contratos\AdendasController@create', 'middleware' => ['permission:adenda_certificacion-create|adenda_ampliacion-create']]);
    Route::post('adenda/store', ['as' => 'adenda.storeUpdate', 'uses' => 'Contratos\AdendasController@storeUpdate', 'middleware' => ['permission:adenda_certificacion-create|adenda_ampliacion-create']]);

    Route::get('adenda/{id}/ver', ['as' => 'adenda.ver', 'uses' => 'Contratos\ContratosController@verAdenda', 'middleware' => ['permission:adenda_ampliacion-view|adenda_certificacion-view']]);
    Route::get('adenda/{id}/firmar', ['as' => 'adenda.firmar', 'uses' => 'Contratos\ContratosController@sign', 'middleware' => ['permission:contrato-view']]);
    Route::get('adenda/{id}/borrador', ['as' => 'adenda.borrador', 'uses' => 'Contratos\ContratosController@draft', 'middleware' => ['permission:contrato-view']]);

    Route::get('adenda/{id}/editar', ['as' => 'adenda.edit', 'uses' => 'Contratos\AdendasController@edit', 'middleware' => ['permission:adenda_ampliacion-edit|adenda_ampliacion-edit-borrador|adenda_certificacion-edit|adenda_certificacion-edit-borrador']]);
    Route::get('adenda/{id}/{accion}/editar', ['as' => 'adenda.editar.incompleto', 'uses' => 'Contratos\ContratosController@verEditar', 'middleware' => ['permission:adenda_ampliacion-view|adenda_certificacion-edit|itemizado-manage|itemizado-edit|cronograma-manage|cronograma-edit']]);

    Route::get('adenda/preDelete/{id}', ['as' => 'adenda.preDelete', 'uses' => 'Contratos\ContratosController@preDelete', 'middleware' => ['permission:adenda_ampliacion-delete|adenda_certificacion-delete']]);
    Route::get('adenda/eliminar/{id}', ['as' => 'adenda.delete', 'uses' => 'Contratos\ContratosController@delete', 'middleware' => ['permission:adenda_ampliacion-delete|adenda_certificacion-delete']]);

    Route::get('adendagetViews/{contrato_id}/{tipo_contrato}', ['as' => 'adenda.getViews', 'uses' => 'Contratos\AdendasController@getViews', 'middleware' => ['permission:adenda_certificacion-create|adenda_ampliacion-create|adenda_certificacion-edit|adenda_ampliacion-edit']]);

    #endregion

    #region Ampliacion

    Route::get('ampliacion/{contrato_id}/solicitar', ['as' => 'ampliacion.create', 'uses' => 'Contratos\AmpliacionController@create', 'middleware' => ['permission:reprogramacion-create|ampliacion-create']]);
    Route::post('ampliacion/store', ['as' => 'ampliacion.storeUpdate', 'uses' => 'Contratos\AmpliacionController@storeUpdate', 'middleware' => ['permission:reprogramacion-create|ampliacion-create']]);

    Route::get('ampliacion/{id}/ver', ['as' => 'ampliacion.ver', 'uses' => 'Contratos\AmpliacionController@ver', 'middleware' => ['permission:reprogramacion-create|ampliacion-create']]);

    Route::get('ampliacion/{id}/editar', ['as' => 'ampliacion.edit', 'uses' => 'Contratos\AmpliacionController@edit', 'middleware' => ['permission:reprogramacion-edit|ampliacion-edit']]);
    Route::get('ampliacion/{id}/{accion}/editar', ['as' => 'ampliacion.editar.incompleto', 'uses' => 'Contratos\AmpliacionController@verEditar', 'middleware' => ['permission:reprogramacion-edit|ampliacion-edit']]);

    Route::get('ampliacion/preDelete/{id}', ['as' => 'ampliacion.preDelete', 'uses' => 'Contratos\AmpliacionController@preDelete', 'middleware' => ['permission:reprogramacion-delete|ampliacion-delete']]);
    Route::get('ampliacion/eliminar/{id}', ['as' => 'ampliacion.delete', 'uses' => 'Contratos\AmpliacionController@delete', 'middleware' => ['permission:reprogramacion-delete|ampliacion-delete']]);

    Route::get('ampliaciongetViews/{contrato_id}/{tipo_id}', ['as' => 'ampliacion.getViews', 'uses' => 'Contratos\AmpliacionController@getViews', 'middleware' => ['permission:reprogramacion-create|ampliacion-create|reprogramacion-edit|ampliacion-edit']]);

    // Vistas ajax por Performance
    Route::get('ampliacionCronogramagetViews/{id}/{seccion}/{visualizacion?}', ['as' => 'ampliacion.editar.getViews', 'uses' => 'Contratos\AmpliacionController@getViewsCronograma', 'middleware' => ['permission:reprogramacion-edit|ampliacion-edit']]);

    // Guardar cronograma definitivo
    Route::post('ampliacion/editar/updateOrStore/cronograma/{id}', ['as' => 'cronograma.updateOrStoreAmpliacion', 'uses' => 'Contratos\CronogramaController@updateOrStoreAmpliacion', 'middleware' => ['permission:cronograma-edit']]);

    #endregion

    #region Certificado

    Route::get('certificado/{contrato_id}/crear', ['as' => 'certificado.create', 'uses' => 'Contratos\CertificadosController@create', 'middleware' => ['permission:certificado-create']]);

    Route::get('certificado/{id}/ver/{breadcrumb?}', ['as' => 'certificado.ver', 'uses' => 'Contratos\CertificadosController@ver', 'middleware' => ['permission:certificado-view']]);

    Route::get('certificado/{id}/editar', ['as' => 'certificado.edit', 'uses' => 'Contratos\CertificadosController@edit', 'middleware' => ['permission:certificado-edit']]);
    Route::post('certificado/{id}/store', ['as' => 'certificado.storeUpdate', 'uses' => 'Contratos\CertificadosController@storeUpdate', 'middleware' => ['permission:certificado-create']]);
    Route::get('certificado/{id}/sign', ['as' => 'certificado.sign', 'uses' => 'Contratos\CertificadosController@sign', 'middleware' => ['permission:certificado-edit']]);

    Route::get('certificado/preDelete/{id}', ['as' => 'certificado.preDelete', 'uses' => 'Contratos\CertificadosController@preDelete', 'middleware' => ['permission:certificado-delete']]);
    Route::get('certificado/eliminar/{id}', ['as' => 'certificado.delete', 'uses' => 'Contratos\CertificadosController@delete', 'middleware' => ['permission:certificado-delete']]);

    Route::get('certificadogetViews/{contrato_id}/{tipo_contrato}', ['as' => 'certificado.getViews', 'uses' => 'Contratos\CertificadosController@getViews', 'middleware' => ['permission:certificado-create|certificado-edit']]);

    Route::get('certificado/exportar/{id}', ['as'=>'export.certificado', 'uses'=>'Contratos\CertificadosController@exportar', 'middleware' => ['permission:certificado-view']]);

    Route::get('certificadohistorial/{id}', ['as' => 'certificado.historial', 'uses' => 'Contratos\SolicitudesCertificadosController@historial', 'middleware' => ['permission:certificado-create|certificado-edit|permission:certificado-view']]);

    #endregion

    #region Solicitudes Certificado

    Route::get('solicitudesCertificado/EnProceso', ['as' => 'solicitudes.certificado_en_proceso', 'uses' => 'Contratos\SolicitudesCertificadosController@indexEnProceso', 'middleware' => ['permission:certificado-en_proceso-list']]);
    Route::post('solicitudesCertificado/EnProceso', ['as' => 'solicitudes.certificado_en_proceso.post', 'uses' => 'Contratos\SolicitudesCertificadosController@indexEnProceso', 'middleware' => ['permission:certificado-en_proceso-list']]);

    Route::get('solicitudesCertificado/Finalizadas', ['as' => 'solicitudes.certificado_finalizadas', 'uses' => 'Contratos\SolicitudesCertificadosController@indexFinalizadas', 'middleware' => ['permission:certificado-finalizadas-list']]);
    Route::post('solicitudesCertificado/Finalizadas', ['as' => 'solicitudes.certificado_finalizadas.post', 'uses' => 'Contratos\SolicitudesCertificadosController@indexFinalizadas', 'middleware' => ['permission:certificado-finalizadas-list']]);

    Route::get('solicitudesCertificado/certificado/{id}/aprobar', ['as' => 'solicitudes.certificado.aprobar', 'uses' => 'Contratos\SolicitudesCertificadosController@aprobarCertificado', 'middleware' => ['permission:certificado-aprobar']]);
    Route::post('solicitudesCertificado/certificado/{id}/rechazar', ['as' => 'solicitudes.certificado.rechazar', 'uses' => 'Contratos\SolicitudesCertificadosController@rechazarCertificado', 'middleware' => ['permission:certificado-rechazar']]);

    Route::get('solicitudesCertificado/certificado/{id}/aprobarCertificadoRedeterminado', ['as' => 'solicitudes.certificado.aprobarCertificadoRedeterminado', 'uses' => 'Contratos\SolicitudesCertificadosController@aprobarCertificadoRedeterminado', 'middleware' => ['permission:certificado-aprobar']]);

    #endregion

    #region Empalme

    #region Certificado

    Route::get('empalme/{id}/preValidacion', ['as' => 'empalme.preValidacion', 'uses' => 'Contratos\EmpalmeController@preValidacion', 'middleware' => ['permission:empalme-manage']]);

    Route::get('contratos/{id}/finalizarEmpalme', ['as' => 'empalme.finalizarEmpalme', 'uses' => 'Contratos\EmpalmeController@finalizarEmpalme', 'middleware' => ['permission:empalme-manage']]);

    Route::get('empalme/certificado/{id}/editar', ['as' => 'empalme.edit', 'uses' => 'Contratos\CertificadosController@edit', 'middleware' => ['permission:empalme-manage']]);
    Route::get('empalme/certificado/{id}/ver', ['as' => 'empalme.ver', 'uses' => 'Contratos\CertificadosController@ver', 'middleware' => ['permission:empalme-manage']]);

    Route::get('empalme/certificado/{contrato_id}/{empalme?}/crear', ['as' => 'empalme.createCertificado', 'uses' => 'Contratos\CertificadosController@create', 'middleware' => ['permission:empalme-manage']]);

    #endregion

    #region Redeterminaciones
    Route::get('empalme/redeterminacion/{contrato_id}/crear', ['as' => 'empalme.createRedeterminacion', 'uses' => 'Redeterminaciones\RedeterminacionesController@create', 'middleware' => ['permission:redeterminaciones-create']]);
    Route::post('empalme/redeterminacion/{contrato_id}/store', ['as' => 'empalme.createRedeterminacion.store', 'uses' => 'Redeterminaciones\RedeterminacionesController@store', 'middleware' => ['permission:redeterminaciones-create']]);

    Route::get('empalme/redeterminacion/{redeterminacion_id}/ver', ['as' => 'empalme.redeterminacion.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@ver', 'middleware' => ['permission:redeterminaciones-view']]);

    Route::get('empalme/redeterminacion/{redeterminacion_id}/editar', ['as' => 'empalme.redeterminacion.edit', 'uses' => 'Redeterminaciones\RedeterminacionesController@edit', 'middleware' => ['permission:redeterminaciones-edit']]);
    Route::post('empalme/redeterminacion/{redeterminacion_id}/updateOrStore', ['as' => 'empalme.redeterminacion.updateOrStore', 'uses' => 'Redeterminaciones\RedeterminacionesController@updateOrStore', 'middleware' => ['permission:redeterminaciones-edit']]);

    Route::get('redeterminaciones/preDelete/{id}', ['as' => 'empalme.redeterminaciones.preDelete', 'uses' => 'Redeterminaciones\RedeterminacionesController@preDelete', 'middleware' => ['permission:redeterminaciones-delete']]);
    Route::get('redeterminaciones/eliminar/{id}', ['as' => 'empalme.redeterminaciones.delete', 'uses' => 'Redeterminaciones\RedeterminacionesController@delete', 'middleware' => ['permission:redeterminaciones-delete']]);

    Route::get('empalme/analisis_item/{analisis_item_id}/ver', ['as' => 'empalme.analisis_item.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@analisisItemVer', 'middleware' => ['permission:redeterminaciones-view']]);
    Route::get('empalme/analisis_item/{analisis_item_id}/editar', ['as' => 'empalme.analisis_item.edit', 'uses' => 'Redeterminaciones\RedeterminacionesController@analisisItemEdit', 'middleware' => ['permission:redeterminaciones-edit']]);
    Route::post('empalme/analisis_item/{analisis_item_id}/editar/componente', ['as' => 'empalme.analisis_item.componentes.edit', 'uses' => 'Redeterminaciones\RedeterminacionesController@updateComponentes', 'middleware' => ['permission:redeterminaciones-edit']]);

    #endregion

    #endregion

    #region Anticipo

    Route::post('anticipo/store', ['as' => 'anticipo.store', 'uses' => 'Contratos\AnticiposController@store', 'middleware' => ['permission:anticipos-create']]);
    Route::get('anticipo/firma/{id}', ['as' => 'anticipo.firmar', 'uses' => 'Contratos\AnticiposController@sign', 'middleware' => ['permission:anticipos-create']]);
    Route::get('anticipo/preDelete/{id}', ['as' => 'anticipo.preDelete', 'uses' => 'Contratos\AnticiposController@preDelete', 'middleware' => ['permission:anticipos-delete']]);
    Route::get('anticipo/eliminar/{id}', ['as' => 'anticipo.delete', 'uses' => 'Contratos\AnticiposController@delete', 'middleware' => ['permission:anticipos-delete']]);

    #endregion

    #region Garantias

    Route::post('garantia/store', ['as' => 'garantia.store', 'uses' => 'Contratos\GarantiasController@store', 'middleware' => ['permission:garantias-manage']]);

    #endregion

    Route::get('widget/contratos/{widget}/{contrato_id}/{version}', ['as' => 'widget.contratos', 'uses' => 'Contratos\ContratosControllerExtended@widget']);

    #region Analisis de Precios

    // Ruta de TEST
    // Route::get('createAnalisisPreciosItemizadosOld', ['as' => 'createAnalisisPreciosItemizadosOld', 'uses' => 'Contratos\ContratosControllerExtended@createAnalisisPreciosItemizadosOld']);

    Route::get('analisis_precioshistorial/{clase_id}/{seccion}', ['as' => 'analisis_precios.historial', 'uses' => 'Contratos\AnalisisPreciosController@historial', 'middleware' => ['permission:analisis_precios-edit']]);
    // Coeficiente K
    Route::get('analisis_precioscoeficientek/{analisis_precios_id}', ['as' => 'analisis_precios.editCoeficienteK', 'uses' => 'Contratos\AnalisisPreciosController@editCoeficienteK', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::post('analisis_precioscoeficientek/{analisis_precios_id}', ['as' => 'analisis_precios.updateCoeficienteK', 'uses' => 'Contratos\AnalisisPreciosController@updateCoeficienteK', 'middleware' => ['permission:analisis_precios-edit']]);

    // Rendimiento
    Route::get('analisis_preciosrendimiento/{categoria_id}', ['as' => 'analisis_precios.editRendimiento', 'uses' => 'Contratos\AnalisisPreciosController@editRendimiento', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::post('analisis_preciosrendimiento/{categoria_id}', ['as' => 'analisis_precios.updateRendimiento', 'uses' => 'Contratos\AnalisisPreciosController@updateRendimiento', 'middleware' => ['permission:analisis_precios-edit']]);

    Route::post('analisis_precios/updateOrStore/{analisis_precios_id}/{accion}', ['as' => 'analisis_precios.updateOrStore', 'uses' => 'Contratos\AnalisisPreciosController@updateOrStore', 'middleware' => ['permission:analisis_precios-edit']]);

    Route::post('contratos/exportar/analisis_precios', ['as'=>'export.analisis_precios', 'uses'=>'Contratos\AnalisisPreciosController@exportar', 'middleware' => ['permission:analisis_precios-export']]);

    // Analisis de Item
    Route::get('analisis_item/{analisis_item_id}/editar', ['as' => 'analisis_item.edit', 'uses' => 'Contratos\AnalisisPreciosController@AnalisisItemEdit', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::get('analisis_item/{analisis_item_id}/ver', ['as' => 'analisis_item.ver', 'uses' => 'Contratos\AnalisisPreciosController@AnalisisItemVer', 'middleware' => ['permission:analisis_precios-view']]);
    Route::post('analisis_item/updateOrStore/{analisis_item_id}/{accion}', ['as' => 'analisis_item.updateOrStore', 'uses' => 'Contratos\AnalisisPreciosController@updateOrStoreAnalisisItem', 'middleware' => ['permission:analisis_precios-edit']]);

    #region Componente

    Route::get('analisis_item_componente/{categoria_id}/create', ['as' => 'analisis_item.componente.createComponente', 'uses' => 'Contratos\AnalisisPreciosController@createComponente', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::get('analisis_item_componente/{componente_id}/edit', ['as' => 'analisis_item.componente.editComponente', 'uses' => 'Contratos\AnalisisPreciosController@editComponente', 'middleware' => ['permission:analisis_precios-edit']]);

    Route::get('contratos/analisis_item/{accion}/{analisis_item_id}', ['as' => 'analisis_item.storeUpdate', 'uses' => 'Contratos\AnalisisPreciosController@AnalisisItemstoreUpdate', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::post('analisis_item_componente/{categoria_id}/store', ['as'=>'analisis_item.componente.updateOrStore', 'uses'=>'Contratos\AnalisisPreciosController@updateOrStoreComponente', 'middleware' => ['permission:analisis_precios-edit']]);

    Route::get('analisis_item_componente/preDelete/{id}', ['as' => 'analisis_item_componente.preDelete', 'uses' => 'Contratos\AnalisisPreciosController@preDelete', 'middleware' => ['permission:analisis_precios-edit']]);
    Route::get('analisis_item_componente/eliminar/{id}', ['as' => 'analisis_item_componente.delete', 'uses' => 'Contratos\AnalisisPreciosController@delete', 'middleware' => ['permission:analisis_precios-edit']]);

    #endregion

    #endregion

    #region Redeterminacion
    Route::get('redeterminacion/{contrato_id}/create', ['as' => 'redeterminacioncreate', 'uses' => 'Redeterminaciones\RedeterminacionesController@create', 'middleware' => ['permission:redeterminaciones-create']]);
    // Para guardado definitivo
    Route::post('redeterminacion/updateOrStore/{redeterminacion_id}/', ['as' => 'redeterminacion.updateOrStore', 'uses' => 'Redeterminaciones\RedeterminacionesController@updateOrStore', 'middleware' => ['permission:analisis_precios-edit']]);

    // Analisis de Item
    Route::get('redeterminacion/analisis_item/{analisis_item_id}/ver', ['as' => 'redeterminacion.analisis_item.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@AnalisisItemVer', 'middleware' => ['permission:redeterminaciones-view']]);

    Route::get('redeterminacion/analisis_item/{analisis_item_id}/editar', ['as' => 'redeterminacion.analisis_item.edit', 'uses' => 'Redeterminaciones\RedeterminacionesController@AnalisisItemEdit', 'middleware' => ['permission:redeterminaciones-edit']]);
    Route::post('redeterminacion/analisis_item/updateOrStore/{analisis_item_id}', ['as' => 'redeterminacion.analisis_item.updateOrStore', 'uses' => 'Redeterminaciones\RedeterminacionesController@updateOrStoreAnalisisItem', 'middleware' => ['permission:redeterminaciones-edit']]);

    Route::get('analisis_precios/updateOrStore/{analisis_precios_id}/{accion}', ['as' => 'analisis_precios.updateOrStore', 'uses' => 'Contratos\AnalisisPreciosController@updateOrStore', 'middleware' => ['permission:analisis_precios-edit']]);

    Route::get('redeterminacion/preAprobar/{id}', ['as' => 'redeterminacion.preAprobarAnalisis', 'uses' => 'Redeterminaciones\RedeterminacionesController@preAprobarAnalisisPrecio', 'middleware' => ['permission:redeterminaciones-edit']]);
    Route::get('redeterminacion/aprobar/{id}', ['as' => 'redeterminacion.aprobarAnalisis', 'uses' => 'Redeterminaciones\RedeterminacionesController@aprobarAnalisisPrecio', 'middleware' => ['permission:redeterminaciones-edit']]);

    Route::get('redeterminacion/preDelete/{id}', ['as' => 'redeterminacion.preDelete', 'uses' => 'Redeterminaciones\RedeterminacionesController@preDelete', 'middleware' => ['permission:redeterminaciones-edit']]);
    Route::get('redeterminacion/eliminar/{id}', ['as' => 'redeterminacion.delete', 'uses' => 'Redeterminaciones\RedeterminacionesController@delete', 'middleware' => ['permission:redeterminaciones-edit']]);

    #endregion
});
