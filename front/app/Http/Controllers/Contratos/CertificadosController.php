<?php

namespace App\Http\Controllers\Contratos;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use DB;
use DateTime;
use Log;
use Response;
use Storage;
use View;
use Dompdf\Dompdf;

use Contrato\Contrato;
use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoMonedaContratista;
use Contrato\Certificado\InstanciaCertificado;
use Contrato\Certificado\ItemCertificado;
use Contrato\Certificado\CertificadosAdjuntos;
use Contrato\Certificado\TiposAdjuntos;

use Contrato\EstadoInstanciaContrato;
use Contrato\InstanciaContrato;

use SolicitudRedeterminacion\Instancia\Instancia;

use Cronograma\ItemCronograma;
use Itemizado\Item;

use YacyretaPackageController\ControllerExtended;
class CertificadosController extends ControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

    //////////// Creacion ////////////
    /**
    * @param int $id
    */
    public function ver($id) {
      $certificado = Certificado::findOrFail($id);
      if(!Auth::user()->puedeVerContrato($certificado->contrato)) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      $is_ute = $certificado->contrato->contratista->is_ute;
      $certificados_por_moneda = $certificado->certificados_por_moneda;
      $porcentaje_desvio = ItemCertificado::getPorcentajeDesvio();
      $edit = false;

      $sub_header = array();

      foreach ($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda) {
        foreach($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista) {
          $sub_header[$valuePorContratista->id] = $valuePorContratista->datos_subheader;
        }
      }
      return view('contratos.certificados.createEdit', compact('certificado', 'certificados_por_moneda', 'is_ute', 'edit', 'porcentaje_desvio',
                                                               'sub_header'));
    }

    public function exportar($id) {
      $certificado = Certificado::find($id);
      $certificados_por_moneda = $certificado->certificados_por_moneda;
      $dompdf = new Dompdf();

      $nombre = $certificado->mes_shoe. ' ' .$certificado->mesAnio('fecha', 'Y-m-d');

      $html = view('contratos.certificados.descarga.certificado', compact('certificado', 'certificados_por_moneda'));
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'landscape');
      $dompdf->render();
      $dompdf->stream(trans('contratos.certificado') . ' nº' . $certificado->id . ' - ' . $nombre . ' - ' .  $certificado->contrato->numero_contrato .'.pdf');
    }

    //////////// Creacion ////////////
    /**
     * @param int $contrato_id
    */
    public function create($contrato_id) {
      $contrato = Contrato::find($contrato_id);

      if(!Auth::user()->puedeVerContrato($contrato)) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      if(!$contrato->permite_certificados) {
        Session::flash('error', trans('certificado.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('certificado.sin_permisos')]);
      }


      $mes = $contrato->mes_proximo_certificado;
      $create = ['contrato_id' => $contrato_id, 'mes' => $mes, 'empalme' => 0];


      $certificado = DB::transaction(function () use ($create, $contrato) {
        $certificado = new Certificado($create);
        $certificado->save();

        $certificado_ant = Certificado::select('id')
                                      ->whereContratoId($create['contrato_id'])
                                      ->whereMes($create['mes'] - 1)
                                      ->first();

        foreach ($contrato->responsables as $keyResponsable => $valueResponsable) {
          foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
            $itemizado = $valueContratoMoneda->itemizado_actual;
            $certificado_moneda_ct = CertificadoMonedaContratista::create([
                                                  'certificado_id'      => $certificado->id,
                                                  'contrato_moneda_id'  => $valueContratoMoneda->id,
                                                  'itemizado_id'        => $itemizado->id,
                                                  'contratista_id'      => $valueResponsable
                                                ]);
            $items_array = [];
            $padres = [];
            $items = $itemizado->itemsDe($valueResponsable);
            $now = date("Y-m-d H:i:s");

            foreach ($items as $keyItem => $valueItem) {
              $valuePrimerItem = $valueItem->primer_item_original;
              if($certificado_ant != null) {
                $certificado_moneda_ct_ant = CertificadoMonedaContratista::select('id')
                                                                         ->whereCertificadoId($certificado_ant->id)
                                                                         ->whereContratistaId($valueResponsable)
                                                                         ->whereContratoMonedaId($valueContratoMoneda->id)
                                                                         ->first();

                $acumulado_anterior = ItemCertificado::select(DB::raw("cantidad + acumulado_anterior AS acumulado"))
                                                     ->whereItemId($valuePrimerItem->id)
                                                     ->whereCertificadoId($certificado_moneda_ct_ant->id)
                                                     ->first();

                if($acumulado_anterior != null)
                  $acumulado_anterior = $acumulado_anterior->acumulado;
                else
                  $acumulado_anterior = 0;
              } else {
                $acumulado_anterior = 0;
              }

              // Calculo de esperado
              if($valueItem->is_nodo) {
                $actual = 'valor';
              } else if($valueItem->is_ajuste_alzado) {
                $actual = 'porcentaje';
              } else {
                $actual = 'cantidad';
              }

              $esperado_mes = ItemCronograma::select($actual)
                                              ->whereItemId($valueItem->id)
                                              ->whereCronogramaId($itemizado->cronograma->id)
                                              ->whereMes($create['mes'])
                                              ->sum($actual);

              $esperado_anterior = $certificado_moneda_ct->esperadoAcumuladoItem($valuePrimerItem->id);
              // FIN Calculo de esperado

              $esperado_total = $esperado_mes + $esperado_anterior;
              if($acumulado_anterior == null || $acumulado_anterior == 0) {
                $acumulado_anterior = '0.00';
                $desvio = '0.00';
              } elseif ($esperado_total == 0) {
                $desvio = 100;
              } else {
                $desvio = round((($acumulado_anterior * 100) / ($esperado_total))
                            - 100, 2);
              }

              $items_array[] = ([
                  'item_id'             => $valuePrimerItem->id,
                  'acumulado_anterior'  => $acumulado_anterior,
                  'esperado'            => $esperado_mes,
                  'desvio'              => $desvio,
                  'porcentaje'          => '0.00',
                  'cantidad'            => '0.00',
                  'certificado_id'      => $certificado_moneda_ct->id,
                  'user_creator_id'     => Auth::user()->id,
                  'user_modifier_id'    => Auth::user()->id,
                  'updated_at'          => $now,
                  'created_at'          => $now,
              ]);

              if($valuePrimerItem->padre_id != null) {
                $primerPadre = Item::find($valuePrimerItem->padre_id)
                                   ->primer_item_original;

                $padres[$valuePrimerItem->id] = $primerPadre->id;
              }
            }

            ItemCertificado::insert($items_array);
            foreach ($padres as $keyPadre => $valuePadre) {
              $item_hijo = ItemCertificado::whereCertificadoId($certificado_moneda_ct->id)
                                          ->whereItemId($keyPadre)->first();

              $item_padre = ItemCertificado::whereCertificadoId($certificado_moneda_ct->id)
                                           ->whereItemId($valuePadre)->first();

              $item_hijo->padre_id = $item_padre->id;
              $item_hijo->save();
            }
          }
        }
        return $certificado;
      });

      $certificado->createInstancia('borrador_por_contratista');

      $this->createInstanciaHistorial($certificado, 'certificado', 'borrador');

      return redirect()->route('certificado.edit', ['id' => $certificado->id]);
    }

    /**
    * @param int $id
    */
    public function edit($id) {
      $certificado = Certificado::findOrFail($id);

      if(!Auth::user()->puedeVerContrato($certificado->contrato)) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      if(!$certificado->borrador OR !$certificado->puede_editar) {
        Session::flash('error', trans('certificado.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('certificado.sin_permisos')]);
      }

      $is_ute = $certificado->contrato->contratista->is_ute;
      $certificados_por_moneda = $certificado->certificados_por_moneda;
      $edit = true;

      $porcentaje_desvio = ItemCertificado::getPorcentajeDesvio();

      $sub_header = array();
      foreach ($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda) {
        foreach($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista) {
          $sub_header[$valuePorContratista->id] = $valuePorContratista->datos_subheader;
        }
      }

      return view('contratos.certificados.createEdit', compact('certificado', 'certificados_por_moneda', 'is_ute', 'edit', 'porcentaje_desvio',
                                                               'sub_header'));
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param int $id
    */
    public function storeUpdate(Request $request, $id) {
      $input = $request->except(['_token']);
      $certificado = Certificado::findOrFail($id);

      if(!Auth::user()->puedeVerContrato($certificado->contrato)) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('certificado.sin_permisos'));
        $jsonResponse['url'] = route('contratos.index');
        return $jsonResponse;
      }

      if(!$certificado->borrador) {
        Session::flash('error', trans('certificado.sin_permisos'));
        $jsonResponse['url'] = route('contratos.index');
        return $jsonResponse;
      }

      $borrador = $input['borrador'] == 1;

      $error_html = array();
      $hay_errores = false;

      foreach ($input['val'] as $keyItemsCertificado => $valueItemsCertificado) {
        $certificado_moneda_ct = CertificadoMonedaContratista::select('contrato_moneda_id', 'contratista_id')->find($keyItemsCertificado);
        $contrato_moneda_id = $certificado_moneda_ct->contrato_moneda_id;

        $error_html[$contrato_moneda_id]['moneda'] = $certificado_moneda_ct->contrato_moneda->moneda->nombre_simbolo;
        $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['nombre'] = $certificado_moneda_ct->contratista->nombre_documento;
        $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'] = array();

        foreach ($valueItemsCertificado as $keyItem => $keyItem) {
          $valor = $this->dosDecToDB($input['val'][$keyItemsCertificado][$keyItem]);
          $input['val'][$keyItemsCertificado][$keyItem] = $valor;

          $error_tamanio = $this->validarTamanio($input['val'][$keyItemsCertificado], $keyItem, $keyItemsCertificado);
          $item_certificado = ItemCertificado::find($keyItem);
          $item = $item_certificado->item;

          if(sizeof($error_tamanio) > 0) {
            $hay_errores = true;
            $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'][] = $item->descripcion_codigo . ': ' . reset($error_tamanio);
          }
          $acumulado = $valor + $item_certificado->acumulado_anterior;

          if($item->is_ajuste_alzado)
            $maximo = 100;
          else
            $maximo = $item->cantidad;

          if(!$borrador && ($acumulado > $maximo)) {
            $hay_errores = true;
            $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'][] = trans('validation_custom.acumulado_mayor_total', ['item' => $item->descripcion_codigo]);
          }
        }
      }

      if(isset($input['tiene_redeterminado'])) {
        foreach ($input['redeterminado'] as $keyRedeterminado => $valueRedeterminado) {
          $valor = $this->dosDecToDB($input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes']);
          $input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes'] = $valor;
          $error_tamanio = $this->validarTamanio($input['redeterminado'][$keyRedeterminado], 'desc_anticipo_importes_por_ajustes', $keyRedeterminado);

          $valor = $this->dosDecToDB($input['redeterminado'][$keyRedeterminado]['importes_por_ajustes']);
          $input['redeterminado'][$keyRedeterminado]['importes_por_ajustes'] = $valor;
          $error_tamanio = $this->validarTamanio($input['redeterminado'][$keyRedeterminado], 'importes_por_ajustes', $keyRedeterminado);

          if($input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes'] > $input['redeterminado'][$keyRedeterminado]['importes_por_ajustes']) {
            $hay_errores = true;
            $error_html[$contrato_moneda_id]['contratista'][$keyRedeterminado]['errores'][] = trans('validation_custom.descuento_mayor_importes', ['item' => $item->descripcion_codigo]);
          }
        }
      }

      if($hay_errores) {
        $errores = '';
        foreach ($error_html as $keyErroresMoneda => $valueErroresMoneda) {
          $errores_temp = '<li>' . $valueErroresMoneda['moneda'] .':</li>';
          $hay_errores_en_moneda = false;
          foreach ($valueErroresMoneda['contratista'] as $keyErroresContratista => $valueErroresContratista) {
            if(sizeof($valueErroresContratista['errores']) > 0) {
              $hay_errores_en_moneda = true;
              $errores_temp .= '<ul class="pl-1"><li>' . $valueErroresContratista['nombre'] .':</li><ul class="pl-1">';
              foreach ($valueErroresContratista['errores'] as $keyErroresError => $valueErroresError) {
                $errores_temp .= '<li>' . $valueErroresError . '</li>';
              }
              $errores_temp .= '</ul></ul>';
            }
          }
          if($hay_errores_en_moneda)
            $errores .= $errores_temp;

          $errores .= '';
        }
        $errores = [$errores];
      }

      if(isset($errores) && sizeof($errores) > 0 ) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['errores_as_string'] = true;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      // Adjuntos
      if(!$borrador) {
        if(!$certificado->acta_medicion)
           $rules['acta_medicion'] = 'required';

        if(!$certificado->seguro_civil)
           $rules['seguro_responsabilidad_civil'] = 'required';

        if(!$certificado->seguro_vida)
           $rules['seguro_vida'] = 'required';

        if(!$certificado->art)
          $rules['ART'] = 'required';

        if(isset($rules)) {
          $validator = Validator::make($input, $rules);
          $errores = array();

          if($validator->fails() || sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
          }
        }
      }

      $certificados_moneda_ct = collect();
      $last_level = array();

      $now = now();
      foreach ($input['val'] as $keyItemsCertificado => $valueItemsCertificado) {
        $certificado_moneda_ct = CertificadoMonedaContratista::find($keyItemsCertificado);
        $certificados_moneda_ct->push($certificado_moneda_ct);
        $last_level[$keyItemsCertificado] = 1;

        foreach ($valueItemsCertificado as $keyItem => $valueItem) {
          $item_certificado = ItemCertificado::find($keyItem);

          if($item_certificado->item->nivel > $last_level[$keyItemsCertificado])
            $last_level[$keyItemsCertificado] = $item_certificado->item->nivel;

          $cantidad_nueva = $valueItem;

          $item_certificado->cantidad = $cantidad_nueva;

          $esperado = $certificado_moneda_ct->esperadoAcumuladoItem($item_certificado->item->id);
          $item_certificado->monto = $cantidad_nueva * $item_certificado->item->monto_unitario_o_porcentual;

          $ejecutado_acumulado = $cantidad_nueva + $item_certificado->acumulado_anterior;

          if($ejecutado_acumulado == $esperado) {
            $item_certificado->desvio = 0;
          } else if($ejecutado_acumulado == 0 || $esperado == 0) {
            $item_certificado->desvio = 100;
          } else {
            $item_certificado->desvio = round((($ejecutado_acumulado * 100) / $esperado)
                                              - 100, 2);
          }

          $item_certificado->save();
        }
      }

      // Calculo para arriba los valores de los items sumarizando sus hijos
      foreach ($certificados_moneda_ct as $keyCertifMonedaContr => $valueCertifMonedaContr) {
        for ($i = $last_level[$valueCertifMonedaContr->id]; $i > 0; $i--) {
          $items_n = $valueCertifMonedaContr->items_nivel_n($i);
          foreach ($items_n as $keyItem => $valueItem) {
            $valueItem->monto = $valueItem->child_sum;

            $esperado = $valueItem->total_esperado_mes;

            $planificado_acumulado = $item_certificado->acumulado_anterior + $esperado;
            if($planificado_acumulado == 0)
              $valueItem->desvio = 100;
            else
              $valueItem->desvio = round((($valueItem->child_sum * 100) / $planificado_acumulado)
                                              - 100, 2);
            $valueItem->save();
          }
        }

        // Calculo el total del CertificadoMonedaContratista
        $valueCertifMonedaContr->monto_bruto = $valueCertifMonedaContr->items_nivel1_sum;

        $anticipo = $valueCertifMonedaContr->certificado->anticipo;
        if($anticipo == null) {
          $valueCertifMonedaContr->monto = $valueCertifMonedaContr->monto_bruto;
        } else {
          $anticipo_item = $valueCertifMonedaContr->item_anticipo;
          $valueCertifMonedaContr->monto = $valueCertifMonedaContr->monto_bruto * (1 - ($anticipo_item->porcentaje_100));
        }

        $valueCertifMonedaContr->save();

        // Calculo el desvio del CertificadoMonedaContratista
        $valueCertifMonedaContr->calcularDesvio();
      }
      // FIN Calculo para arriba los valores de los items sumarizando sus hijos

      if(isset($input['acta_medicion']) && $request->hasFile('acta_medicion')) {
        $this->saveAdjuntos($request, $certificado, 'acta_medicion');
      }

      if(isset($input['seguro_responsabilidad_civil']) && $request->hasFile('seguro_responsabilidad_civil')) {
        $this->saveAdjuntos($request, $certificado, 'seguro_responsabilidad_civil');
      }

      if(isset($input['seguro_vida']) && $request->hasFile('seguro_vida')) {
        $this->saveAdjuntos($request, $certificado, 'seguro_vida');
      }

      if(isset($input['ART']) && $request->hasFile('ART')) {
        $this->saveAdjuntos($request, $certificado, 'ART');
      }

      if(isset($input['nueve_tres_uno']) && $request->hasFile('nueve_tres_uno')) {
        $this->saveAdjuntos($request, $certificado, 'nueve_tres_uno');
      }

      if(isset($input['adjunto']) && $request->hasFile('adjunto')) {
        $this->saveAdjuntos($request, $certificado,'adjunto');
      }
      // END Adjuntos

      if(!$borrador) {
        $certificado->createInstancia('a_validar');

        $this->createInstanciaHistorial($certificado, 'certificado', 'a_validar');
        //$certificado->borrador = 0;
        $certificado->save();
        if($certificado->mes == 1) {
          foreach ($certificado->cert_moneda_contratista as $keyCertifMonedaContr => $valueCertifMonedaContr) {
            $contrato_moneda = $valueCertifMonedaContr->contrato_moneda;
            $contrato_moneda->saldo = $contrato_moneda->saldo - $valueCertifMonedaContr->monto;
            $contrato_moneda->save();

            foreach ($valueCertifMonedaContr->itemizado->items as $keyItem => $valueItem) {
              if(!$valueItem->certificado) {
                $valueItem->certificado = 1;
                $valueItem->save();
              }
            }
          }
        }

        if($certificado->tiene_redeterminados) {
          $certificado_redeterminado = $certificado->certificado_redeterminado_empalme;
          $certificado_redeterminado->borrador = 0;
          $certificado_redeterminado->save();
        }

        // aprobar instancia en solicitud de redeterminacion
        // $solicitudes = $certificado->contrato->solicitudes_redeterminacion;
        // if($solicitudes) {
        //   // checkeo si existe solicitud x mes actual
        //   $fecha_certificado = date('m/Y', strtotime($certificado->fecha));
        //   foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
        //     $mes_publicacion = $valueSolicitud->salto->publicacion->mes_anio;
        //     if($fecha_certificado == $mes_publicacion) {
        //       // si la solicitud esta en esa instancia la apruebo
        //       $instancia_actual = $valueSolicitud->instancia_actual;
        //       $instancia_tipo = $instancia_actual->tipo_instancia;
        //
        //       if($instancia_tipo->modelo == 'AprobacionCertificados') {
        //         $desvio = $valueSolicitud->salto->desvio_acumulado;
        //
        //         $instancia = $instancia_actual->instancia;
        //         $instancia->certificados_aprobados = 1;
        //         $instancia->save();
        //
        //         $instancia_siguiente = $instancia_actual->instancia_siguiente;
        //         $valueSolicitud->instancia_actual_id = $instancia_siguiente->id;
        //         $valueSolicitud->save();
        //
        //         if($instancia_siguiente->tipo_instancia->modelo == 'CalculoPreciosRedeterminados') {
        //           $status = app('SolicitudesRedeterminacionController')
        //                       ->CalculoPreciosRedeterminados('CalculoPreciosRedeterminados', $valueSolicitud->id, false)
        //                       ->getData()->status;
        //         }
        //       }
        //     }
        //   }
        // }

      }

      $jsonResponse['status'] = true;
      if($borrador) {
        Session::flash('success', trans('mensajes.dato.certificado') . trans('mensajes.success.editado'));
        $jsonResponse['message'] = [trans('mensajes.dato.certificado') . trans('mensajes.success.editado')];
        $jsonResponse['refresh'] = false;
      } else {
        Session::flash('success', trans('certificado.mensajes.enviado_aprobar'));
        $jsonResponse['message'] = [trans('certificado.mensajes.enviado_aprobar')];
      }

      $jsonResponse['url'] = route('contratos.ver', ['id' => $certificado->contrato_id]);
      return response()->json($jsonResponse);
    }

    //////////// Eliminar ////////////
    /**
    * @param int $id
    */
    public function preDelete($id) {
      $certificado = Certificado::select('borrador')->find($id);
      if($certificado->borrador) {
        $jsonResponse['status'] = true;
      } else {
        $jsonResponse['status'] = false;
          $jsonResponse['status'] = false;
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('contratos.certificado');

        $jsonResponse['message'] = [trans('index.no_puede_eliminar.certificado')];
      }
      return response()->json($jsonResponse);
    }

    public function delete($id) {
      if($this->preDelete($id)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [$this->preDelete($id)->getData()->message];
        return response()->json($jsonResponse);
      }

      $certificado = Certificado::find($id);

      try {
        if($certificado->tiene_redeterminados) {
          $certificado_redeterminado = $certificado->certificado_redeterminado_empalme;
          $certificado_redeterminado->delete();
        }

        $certificado->delete();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('mensajes.dato.certificado') . trans('mensajes.success.eliminado')];

      return response()->json($jsonResponse);
    }
    //////////// Eliminar ////////////

    public function saveAdjuntos($request, $certificado, $input) {
      $adjuntoTipo = TiposAdjuntos::whereNombre($input)->first();
      if($adjuntoTipo== null)
        $adjuntoTipo = TiposAdjuntos::whereNombre('otros')->first();

      switch ($input) {
        case 'acta_medicion':
          if($certificado->acta_medicion)
             $certificado->acta_medicion->delete();
          break;
        case 'seguro_responsabilidad_civil':
          if($certificado->seguro_civil)
             $certificado->seguro_civil->delete();
          break;
        case 'seguro_vida':
          if($certificado->seguro_vida)
             $certificado->seguro_vida->delete();
          break;
        case 'ART':
          if($certificado->art)
            $certificado->art->delete();
          break;
        case 'nueve_tres_uno':
          if($certificado->nueve_tres_uno)
            $certificado->nueve_tres_uno->delete();
          break;
        default:
          if($certificado->otros_adjuntos)
            $certificado->otros_adjuntos->delete();
          break;
      }

      $adjuntos_json = $this->uploadFile( $request, $certificado->id, $input, 'certificado');
        CertificadosAdjuntos::create([ 'certificado_id'   => $certificado->id,
                                       'adjunto_tipo_id'  => $adjuntoTipo->id,
                                       'adjunto'          => $adjuntos_json,
                                      ]);
    }

    //////////// Validaciones de Doubles ////////////
    /**
     * @param  array $input
     * @param  int $valor
     * @param  int $keyInput
    */
    public function validarTamanio($input, $valor, $keyInput = null) {
      $errores = array();
      if($input[$valor] == null)
        return $errores;

      $inputVar = explode(".", $input[$valor]);

      if(!isset($inputVar[1]))
        $inputVar[1] = "00";

      if(strlen($inputVar[1]) < 2)
        $inputVar[1] = str_pad($inputVar[1], 2, "0");

      if(strlen($inputVar[0]) > 12) {
        if($keyInput != null)
          $key = $valor . '_' . $keyInput;
        else
          $key = $valor;
        $errores[$key] = trans('mensajes.error.max_number_12');
      }

      if(strlen($inputVar[1]) > 2) {
        if($keyInput != null)
          $key = $valor . '_' . $keyInput;
        else
          $key = $valor;
        $errores[$key] = trans('mensajes.error.max_decimal_2');
      }

      return $errores;
    }
    //////////// FIN Validaciones de Doubles ////////////

        /**
     * @param  Object $object
     * @param  string $seccion
     * @param  string $estado
    */
    public function createInstanciaHistorial($object, $seccion, $estado) {
      $estado_id = EstadoInstanciaContrato::whereNombre($estado)->first()->id;
      try {
        $instancia = InstanciaContrato::create([
            'seccion'           => $seccion,
            'clase_type'        => $object->getClassName(),
            'clase_id'          => $object->id,
            'estado_id'         => $estado_id,
            'observaciones'     => '',
            'user_creator_id'   => Auth::user()->id,
            'user_modifier_id'  => Auth::user()->id
          ]);
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        return 'ERROR';
      }
      return 'OK';
    }
}
