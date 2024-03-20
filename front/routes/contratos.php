<?php

Route::group(['middleware' => ['auth']], function() {
////////////////// Redeterminaciones //////////////////
  Route::get('empalme/redeterminacion/{redeterminacion_id}/ver', ['as' => 'empalme.redeterminacion.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@ver']);
  Route::get('empalme/analisis_item/{analisis_item_id}/ver', ['as' => 'empalme.analisis_item.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@analisisItemVer']);

  Route::get('redeterminacion/analisis_item/{analisis_item_id}/ver', ['as' => 'redeterminacion.analisis_item.ver', 'uses' => 'Redeterminaciones\RedeterminacionesController@AnalisisItemVer']);
////////////////// FIN Redeterminaciones //////////////////

////////////////// Certificados //////////////////
  Route::get('certificado/{contrato_id}/crear', ['as' => 'certificado.create', 'uses' => 'Contratos\CertificadosController@create']);

  Route::get('certificado/{id}/editar', ['as' => 'certificado.edit', 'uses' => 'Contratos\CertificadosController@edit']);
  Route::post('certificado/{id}/store', ['as' => 'certificado.storeUpdate', 'uses' => 'Contratos\CertificadosController@storeUpdate']);

  Route::get('certificado/preDelete/{id}', ['as' => 'certificado.preDelete', 'uses' => 'Contratos\CertificadosController@preDelete']);
  Route::get('certificado/eliminar/{id}', ['as' => 'certificado.delete', 'uses' => 'Contratos\CertificadosController@delete']);

  Route::get('certificado/{id}/ver', ['as' => 'certificado.ver', 'uses' => 'Contratos\CertificadosController@ver']);
  Route::get('certificado/exportar/{id}', ['as'=>'export.certificado', 'uses'=>'Contratos\CertificadosController@exportar']);

  Route::get('certificadohistorial/{id}', ['as' => 'certificado.historial', 'uses' => 'Contratos\SolicitudesCertificadosController@historial']);
////////////////// FIN Certificados //////////////////
});
