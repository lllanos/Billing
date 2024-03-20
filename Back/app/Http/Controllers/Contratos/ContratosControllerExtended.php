<?php

namespace App\Http\Controllers\Contratos;

use AnalisisPrecios\AnalisisItem;
use AnalisisPrecios\AnalisisPrecios;
use App\Jobs\CalculoItemAdenda;
use App\Jobs\CalculoVariacionEnPublicacion;
use Auth;
use Carbon\Carbon;
use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoMonedaContratista;
use Contrato\Certificado\EstadoInstanciaCertificado;
use Contrato\Contrato;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\EstadoInstanciaContrato;
use Contrato\InstanciaContrato;
use Cronograma\Cronograma;
use Cronograma\ItemCronograma;
use DB;
use Illuminate\Support\Facades\Session;
use Itemizado\Item;
use Log;
use Redeterminacion\Redeterminacion;
use Response;
use Storage;
use View;
use YacyretaPackageController\Contratos\ContratosControllerExtended as PackageContratosController;

class ContratosControllerExtended extends PackageContratosController
{

    /**
     * @param  string  $required
     */
    public function getRulesContrato($required)
    {
        return [
          'numero_contratacion' => $this->required50(),
          'numero_contrato' => $required.$this->min3maxN(6),
          'expediente_madre' => $required.$this->min3maxN(50),
          'denominacion' => $required.$this->min3max255(),
          'contratista_id' => $required,
          'organo_aprobador_id' => $required,
          'repre_leg_contratista' => $required.$this->min3max1024(),
          'resoluc_adjudic' => $required.$this->min3maxN(50),
          'causante_id' => $required,
          'fecha_oferta' => $required.$this->dateFormat(),
          'fecha_acta_inicio' => 'nullable|'.$this->dateFormat(),
          'estado_id' => $required, 'plazo_id' => $required,
          'plazo' => $required,
        ];
    }

    /**
     *
     * @param  int  $id
     * @param  string  $tipo
     */
    public function completarContrato($id, $tipo = 'Contrato\Contrato')
    {
        $object = $tipo::find($id);

        if (!$object->incompleto['status']) {
            $object->completo = 1;
            $object->save();

            if ($object->is_adenda_ampliacion) {
                $contrato_padre = $object->contrato_padre;
                $contrato_padre->plazo_vigente = $object->plazo;
                $contrato_padre->save();

                foreach ($object->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    $contrato_moneda_madre = ContratoMoneda::whereClaseId($contrato_padre->id)
                      ->whereClaseType($contrato_padre->getClassName())
                      ->whereMonedaId($valueContratoMoneda->moneda_id)
                      ->first();

                    $contrato_moneda_madre->itemizado_vigente_id = $valueContratoMoneda->itemizado_id;
                    $contrato_moneda_madre->monto_vigente_val_originales = $valueContratoMoneda->monto_vigente;
                    $contrato_moneda_madre->save();

                    $this->updateAnalisisPrecios($object->contrato_padre, $valueContratoMoneda->itemizado);
                }

                $maniana = Carbon::tomorrow();
                $maniana->addHours(config('custom.hora_proceso'))->addMinutes(config('custom.min_proceso'));
                $this->dispatch((new CalculoItemAdenda())->delay($maniana)->onQueue('calculos_variacion'));
            }
            else if (!$object->falta_completar_empalme) {
                $calcular = false;
                $cant_sin_analisis = 0;

                foreach ($object->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    if (!$valueContratoMoneda->lleva_analisis) {
                        $cant_sin_analisis++;
                    }
                    elseif (
                      $valueContratoMoneda->analisis_precios != null
                      && count($valueContratoMoneda->analisis_precios->acciones_posibles) == 0
                    ) {
                        $calcular = true;
                    }
                }

                if ($cant_sin_analisis == count($object->contratos_monedas))
                    $calcular = true;

                if ($calcular) {
                    $maniana = Carbon::tomorrow();
                    $maniana->addHours(config('custom.hora_proceso'))->addMinutes(config('custom.min_proceso'));
                    $this->dispatch((new CalculoVariacionEnPublicacion())->delay($maniana)->onQueue('calculos_variacion'));
                }
            }
        }

        return $object->completo;
    }

    /**
     * @param  Cronograma\Cronograma  $cronograma
     * @param  int  $item_id
     * @param  int  $meses
     */
    public function updateParents($cronograma, $item_id, $meses, $suma_hijos = [])
    {
        if ($item_id == null)
            return 'OK';

        $item = Item::find($item_id);
        $cronograma_id = $cronograma->id;

        if (sizeof($item->allChilds) > 0) {
            $date = date("Y-m-d H:i:s");

            foreach ($item->allChilds as $keySubItem => $valueSubItem) {
                for ($mes = 1; $mes <= $meses; $mes++) {
                    if (!isset($suma_hijos[$mes])) {
                        $suma_hijos[$mes] = ([
                          'mes' => $mes,
                          'valor' => 0,
                          'cantidad' => 0,
                          'porcentaje' => 0,
                          'item_id' => $item_id,
                          'cronograma_id' => $cronograma_id,
                          'user_creator_id' => Auth::user()->id,
                          'user_modifier_id' => Auth::user()->id,
                          'updated_at' => $date,
                          'created_at' => $date,
                        ]);
                    }

                    $item_cronograma_temp = ItemCronograma::select('valor', 'cantidad',
                      'porcentaje')->whereItemId($valueSubItem->id)->whereCronogramaId($cronograma_id)->whereMes($mes)->first();

                    if ($item_cronograma_temp == null) {
                        $valor = 0;
                        $cantidad = 0;
                    }
                    else {
                        $valor = $item_cronograma_temp->valor;
                        $cantidad = $item_cronograma_temp->cantidad;
                    }

                    $suma_hijos[$mes]['valor'] = $suma_hijos[$mes]['valor'] + $valor;
                    $suma_hijos[$mes]['cantidad'] = $suma_hijos[$mes]['cantidad'] + $cantidad;
                    $suma_hijos[$mes]['porcentaje'] = round(($suma_hijos[$mes]['valor'] / $item->subtotal) * 100, 4);
                }
            }

            if (sizeof($suma_hijos) > 0) {
                foreach ($suma_hijos as $keyValor => $valueValor) {
                    $item_cronograma = ItemCronograma::whereMes($valueValor['mes'])->whereItemId($item_id)->whereCronogramaId($cronograma_id)->first();

                    $valueValor['valor'] = number_format(round($valueValor['valor'], 2), 2, '.', '');

                    if ($item_cronograma != null) {
                        $item_cronograma->user_modifier_id = Auth::user()->id;
                        $item_cronograma->valor = $valueValor['valor'];
                        $item_cronograma->porcentaje = $valueValor['porcentaje'];
                        $item_cronograma->cantidad = $valueValor['cantidad'];
                        $item_cronograma->save();

                        unset($suma_hijos[$keyValor]);

                        if (!isset($contrato_id))
                            $contrato_id = $item_cronograma->cronograma->contrato_id;

                    }
                }

                $meses = $valueValor['mes'];

                ItemCronograma::insert($suma_hijos);
            }
        }

        $this->updateParents($cronograma, $item->padre_id, $meses);
        return 'OK';
    }

    /**
     * @param  Itemizado\Itemizado  $itemizado
     */
    public function createCronograma($itemizado)
    {
        $moneda_id = $itemizado->contrato_moneda->moneda_id;
        $contrato = $itemizado->contrato_moneda->contrato;
        $meses = $contrato->meses_cronograma;

        if ($meses) {
            $cronograma = Cronograma::create([
              'meses' => $meses,
              'itemizado_id' => $itemizado->id
            ]);

            $response['cronograma_id'] = $cronograma->id;
        }
        else
            $response['cronograma_id'] = null;

        $this->createInstanciaHistorial($contrato, 'cronograma', 'borrador');
        $response['meses'] = $meses;
        $response['status'] = true;

        if (!$contrato->is_adenda_ampliacion)
            $this->createAnalisisPrecios($itemizado);


        return $response;
    }

    /**
     * @param  Object  $object
     * @param  string  $seccion
     * @param  string  $estado
     */
    public function createInstanciaHistorial($object, $seccion, $estado, $observaciones = null)
    {
        if ($seccion == 'certificado')
            $estado_id = EstadoInstanciaCertificado::whereNombre($estado)->first()->id;
        else
            $estado_id = EstadoInstanciaContrato::whereNombre($estado)->first()->id;

        try {
            InstanciaContrato::create([
              'seccion' => $seccion,
              'clase_type' => $object->getClassName(),
              'clase_id' => $object->id,
              'estado_id' => $estado_id,
              'observaciones' => $observaciones,
              'user_creator_id' => Auth::user()->id,
              'user_modifier_id' => Auth::user()->id
            ]);
        }
        catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            return 'ERROR';
        }

        return 'OK';
    }

//#region Analisis de Precios

    /**
     * @param  Itemizado\Itemizado  $itemizado
     */
    public function createAnalisisPrecios($itemizado)
    {
        $moneda = $itemizado->contrato_moneda->moneda;

        if (!$moneda->lleva_analisis) {
            $response['status'] = true;
            return $response;
        }

        DB::transaction(function () use ($itemizado) {
            $analisis_precios = AnalisisPrecios::create(['itemizado_id' => $itemizado->id]);
            $analisis_precios->createInstanciaHistorial('borrador');
            $analisis_precios_id = $analisis_precios->id;

            foreach ($itemizado->items_hoja as $keyItem => $valueItem) {
                $analisis_item = AnalisisItem::create([
                  'analisis_precios_id' => $analisis_precios_id, 'item_id' => $valueItem->primer_item_original->id
                ]);

                $analisis_item->createCategorias();
                $analisis_item->createInstanciaHistorial('borrador');
            }
        });

        $response['status'] = true;

        return $response;
    }

    /**
     * @param  AnalisisPrecios\AnalisisPrecios  $analisis_precios_anterior
     * @param  int  $redeterminacion_id
     */
    public function createAnalisisPreciosRedeterminados($analisis_precios_anterior, $redeterminacion_id)
    {
        $redeterminacion = Redeterminacion::find($redeterminacion_id);

        if ($redeterminacion->redeterminacion_anterior) {
            $analisis_precios_anterior = $redeterminacion->redeterminacion_anterior->analisis_precios;
            $redeterminacion->analisis_precios_original_id = $redeterminacion->redeterminacion_anterior->analisis_precios_original_id;
            $redeterminacion->save();
        }
        else {
            $redeterminacion->analisis_precios_original_id = $analisis_precios_anterior->id;
            $redeterminacion->save();
        }

        $moneda = $analisis_precios_anterior->itemizado->contrato_moneda->moneda;
        if (!$moneda->lleva_analisis) {
            $response['status'] = true;
            return $response;
        }

        $redeterminacion->save();

        DB::transaction(function () use ($analisis_precios_anterior, $redeterminacion) {
            if ($analisis_precios_anterior->itemizado_vigente_id) {
                $analisis_precios = AnalisisPrecios::create(['itemizado_id' => $analisis_precios_anterior->itemizado_vigente_id]);
            } else {
                $analisis_precios = AnalisisPrecios::create(['itemizado_id' => $analisis_precios_anterior->itemizado_id]);
            }
            $analisis_precios->createInstanciaHistorial('borrador');
            $analisis_precios->coeficiente_k = $analisis_precios_anterior->coeficiente_k;
            $analisis_precios->es_redeterminacion = 1;
            $analisis_precios->redeterminacion_id = $redeterminacion->id;
            $analisis_precios->save();

            foreach ($analisis_precios_anterior->analisis_items as $keyItem => $valueItem) {
                $newAnalisisItem = $valueItem->replicate();
                $newAnalisisItem->item_id = $valueItem->item->item_vigente->id;
                $newAnalisisItem->analisis_precios_id = $analisis_precios->id;
                $newAnalisisItem->save();

                foreach ($valueItem->categorias_padres as $keyCategiria => $valueCategoria)
                    $newAnalisisItem->createCategoriasRedeterminaciones($valueCategoria, $newAnalisisItem);

                $newAnalisisItem->createInstanciaHistorial('borrador');
            }
        });

        $response['status'] = true;

        return $response;
    }

    /**
     * @param  Contrato\Contrato  $contrato
     * @param  Itemizado\Itemizado  $itemizado
     */
    public function updateAnalisisPrecios($contrato, $itemizado)
    {
        $moneda = $itemizado->contrato_moneda->moneda;
        if (!$moneda->lleva_analisis) {
            $response['status'] = true;
            return $response;
        }

        DB::transaction(function () use ($contrato, $itemizado) {
            $analisis_precios_col = $contrato->analisis_precios;
            foreach ($analisis_precios_col as $keyAnalisisPrecios => $analisis_precios) {
                $analisis_precios_id = $analisis_precios->id;

                $items_hoja = $itemizado->items_hoja->filter(function ($item_hoja) {
                    return $item_hoja->item_original_id == null;
                });

                if (count($items_hoja) > 0) {
                    $analisis_precios->createInstanciaHistorial('nuevo_item');
                    foreach ($items_hoja as $keyItem => $valueItem) {
                        $analisis_item = AnalisisItem::create([
                          'analisis_precios_id' => $analisis_precios_id,
                          'item_id' => $valueItem->primer_item_original->id
                        ]);

                        $analisis_item->createCategorias();
                        $analisis_item->createInstanciaHistorial('borrador');
                    }
                }
                $analisis_precios->itemizado_vigente_id = $itemizado->id;
                $analisis_precios->save();
            }
        });

        $response['status'] = true;

        return $response;
    }

//#endregion

#region Validaciones de Doubles

    /**
     * @param  array  $input
     * @param  int  $valor
     * @param  int  $keyInput
     */
    public function validarTamanio($input, $valor, $keyInput = null)
    {
        $errores = [];

        if ($input[$valor] == null)
            return $errores;

        $inputVar = explode(".", $input[$valor]);

        if (!isset($inputVar[1]))
            $inputVar[1] = "00";

        if (strlen($inputVar[1]) < 2)
            $inputVar[1] = str_pad($inputVar[1], 2, "0");

        if (strlen($inputVar[0]) > 12) {
            if ($keyInput != null)
                $key = $valor.'_'.$keyInput;
            else
                $key = $valor;

            $errores[$key] = trans('mensajes.error.max_number_12');
        }

        if (strlen($inputVar[1]) > 2) {
            if ($keyInput != null)
                $key = $valor.'_'.$keyInput;
            else
                $key = $valor;

            $errores[$key] = trans('mensajes.error.max_decimal_2');
        }

        return $errores;
    }

#endregion

//#region Widgets

    /**
     * @param  string  $widget
     * @param  int  $contrato_id
     * @param  string  $seccion
     */
    public function widget($widget, $contrato_id, $version)
    {
        return $this->$widget($contrato_id, $version);
    }

    /**
     * @param  int  $contrato_id
     * @param  string  $seccion
     */
    public function curva_inversion($contrato_id, $version = 'vigente')
    {
        $contrato = Contrato::find($contrato_id);
        $serie1_temp = array();
        $certificados = $contrato->has_certificados;

        if ((count($contrato->certificados) == 1) && $contrato->ultimo_certificado_es_borrador) {
            $certificados = false;
        }

        foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
            if ($version == 'vigente' && $contrato->has_cronograma_vigente) {
                $cronograma = $valueContratoMoneda->cronograma_vigente;
            } else {
                $cronograma = $valueContratoMoneda->cronograma;
            }

            // Add name
            $serie1_temp[$valueContratoMoneda->moneda_id.'_mensual']['name'] = $valueContratoMoneda->moneda->nombre_simbolo;

            if ($certificados) {
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['name'] = $valueContratoMoneda->moneda->nombre_simbolo.' '.trans('forms.certificado');
            }

            $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['name'] = $valueContratoMoneda->moneda->nombre_simbolo.' '.trans('cronograma.curva_inversion.acumulado');

            if ($certificados) {
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado_acumulado']['name'] = $valueContratoMoneda->moneda->nombre_simbolo.' '.trans('forms.certificado').' '.trans('cronograma.curva_inversion.acumulado');
            }

            // Add type & yAxis
            $serie1_temp[$valueContratoMoneda->moneda_id.'_mensual']['type'] = 'column';
            $serie1_temp[$valueContratoMoneda->moneda_id.'_mensual']['yAxis'] = 1;

            if ($certificados) {
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['type'] = 'column';
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['yAxis'] = 1;
            }

            $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['type'] = 'spline';
            $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['yAxis'] = 2;

            if ($certificados) {
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado_acumulado']['type'] = 'spline';
                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado_acumulado']['yAxis'] = 2;
            }

            // Add Data
            for ($mes = 1; $mes <= $cronograma->meses; $mes++) {
                $valor = str_replace(".", "", $cronograma->valorItemizado('moneda', $mes));
                $valor = str_replace(",", ".", $valor);
                $serie1_temp[$valueContratoMoneda->moneda_id.'_mensual']['data'][$mes - 1] = (float) $valor;

                if ($mes == 1) {
                    $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['data'][$mes - 1] = (float) $valor;
                } else {
                    $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['data'][$mes - 1] = (float) $valor + $serie1_temp[$valueContratoMoneda->moneda_id.'_acumulado']['data'][$mes - 2];
                }

                if ($certificados) {
                    $certificado = Certificado::whereMes($mes)->whereContratoId($valueContratoMoneda->clase_id)->first();

                    if ($certificado && !$certificado->es_borrador) {
                        $contrato_cert_monedas = CertificadoMonedaContratista::whereCertificadoId($certificado->id)->whereContratoMonedaId($valueContratoMoneda->id)->get();

                        foreach ($contrato_cert_monedas as $keyCertMoneda => $valueCertMoneda) {
                            if (!isset($serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['data'][$mes - 1])) {
                                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['data'][$mes - 1] = (float) $valueCertMoneda->monto_bruto;
                            } else {
                                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado']['data'][$mes - 1] += (float) $valueCertMoneda->monto_bruto;
                            }

                            if (!isset($serie1_temp[$valueContratoMoneda->moneda_id.'_certificado_acumulado']['data'][$mes - 1])) {
                                $serie1_temp[$valueContratoMoneda->moneda_id.'_certificado_acumulado']['data'][$mes - 1] = (float) $certificado->importeAcumuladoPorMoneda($valueContratoMoneda->id);
                            }
                        }
                    }
                }
            }
        }


        $categories = array();
        for ($mes = 1; $mes <= $cronograma->meses; $mes++) {
            $categories[] = $mes;
        }

        // Highcharts necesita que sea 0, 1, etc
        foreach ($serie1_temp as $keySerie => $valueSerie) {
            $serie1 [] = array(
              "name" => $serie1_temp[$keySerie]['name'], "type" => $serie1_temp[$keySerie]['type'],
              "yAxis" => $serie1_temp[$keySerie]['yAxis'], "data" => $serie1_temp[$keySerie]['data']
            );
        }

        if (count($serie1_temp) > 0) {
            $title = trans('contratos.cronograma').' '.trans('cronograma.vista.tag.'.$version);

            return View::make('contratos.contratos.show.cronograma.widgets.curva_inversion',
              compact('serie1', 'title', 'categories'))->render();
        } else {
            return View::make('dashboard.widgets.no_data')->render();
        }
    }

//#endregion

    protected function inputDoblefirma(&$input, $causante) {
        $input['doble_firma'] = $causante->doble_firma;

        $firma_ar = $causante->jefe_contrato_ar;
        $firma_py = $causante->jefe_contrato_py;

        if ($firma_ar == Auth::user()->id)
            $input['firma_ar'] = $firma_ar;

        if ($firma_py == Auth::user()->id)
            $input['firma_py'] = $firma_py;

        if (
            isset($input['firma_ar']) && $input['firma_ar']
            && isset($input['firma_py']) && $input['firma_py']
        )
            $input['doble_firma'] = false;

        return $input['doble_firma'];
    }

    /**
     * @param  array  $messages
     * @param  string  $route
     * @param  array  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorJsonResponse($messages = [], $route = 'contratos.index', $options = [])
    {
        $response['status'] = false;

        if ($route) {
            $response['url'] = route($route);
        }

        $response['message'] = $messages;

        foreach ($messages as $message) {
            Session::flash('error', trans($message));
        }

        return response()->json(array_merge($response, $options));
    }

}
