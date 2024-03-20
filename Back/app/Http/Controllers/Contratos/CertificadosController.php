<?php

namespace App\Http\Controllers\Contratos;

use App\Jobs\InstanciaCalculoPrecios;
use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoMonedaContratista;
use Contrato\Certificado\CertificadosAdjuntos;
use Contrato\Certificado\ItemCertificado;
use Contrato\Certificado\TiposAdjuntos;
use Contrato\Contrato;
use Cronograma\ItemCronograma;
use DB;
use Dompdf\Dompdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Itemizado\Item;
use Log;
use Response;
use Storage;
use View;
use Yacyreta\Causante;

class CertificadosController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

    #region Creacion/edición

    /**
     * @param  int  $contrato_id
     */
    public function create($contrato_id, $empalme = 0)
    {
        $contrato = Contrato::find($contrato_id);

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        if (!$empalme && !$contrato->permite_certificados) {
            Session::flash('error', trans('certificado.sin_permisos'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('certificado.sin_permisos')]);
        }

        if ($empalme && !$contrato->permite_certificado_empalme) {
            Session::flash('error', trans('certificado.sin_permisos'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('certificado.sin_permisos')]);
        }

        $mes = $contrato->mes_proximo_certificado;

        if ($empalme) {
            $create = [
                'contrato_id' => $contrato_id,
                'mes' => $mes,
                'empalme' => 1
            ];
        }
        else {
            $create = [
                'contrato_id' => $contrato_id,
                'mes' => $mes,
                'empalme' => 0
            ];
        }

        $certificado = DB::transaction(function () use ($create, $contrato) {
            $certificado = new Certificado($create);
            $certificado->save();

            $certificado_ant = Certificado::select('id')
                ->whereContratoId($create['contrato_id'])
                ->whereMes($create['mes'] - 1)
                ->first();

            foreach ($contrato->responsables as $keyResponsable => $responsable_id) {
                foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    $itemizado = $valueContratoMoneda->itemizado_actual;
                    $cronograma_id = $itemizado->cronograma->id;
                    $certificado_moneda_ct = CertificadoMonedaContratista::create([
                        'certificado_id' => $certificado->id,
                        'contrato_moneda_id' => $valueContratoMoneda->id,
                        'itemizado_id' => $itemizado->id,
                        'contratista_id' => $responsable_id
                    ]);
                    $items_array = [];
                    $padres = [];
                    $items = $itemizado->itemsDe($responsable_id);
                    $now = date("Y-m-d H:i:s");

                    $last_level = 1;
                    foreach ($items as $keyItem => $valueItem) {
                        if ($valueItem->nivel > $last_level) {
                            $last_level = $valueItem->nivel;
                        }

                        $valuePrimerItem = $valueItem->primer_item_original;
                        // Calculo acumulado anterior
                        if ($certificado_ant != null) {
                            $certificado_moneda_ct_ant = CertificadoMonedaContratista::select('id')
                                ->whereCertificadoId($certificado_ant->id)
                                ->whereContratistaId($responsable_id)
                                ->whereContratoMonedaId($valueContratoMoneda->id)
                                ->first();

                            $acumulado_anterior = ItemCertificado::select(DB::raw("cantidad + acumulado_anterior AS acumulado"))
                                ->whereItemId($valuePrimerItem->id)
                                ->whereCertificadoId($certificado_moneda_ct_ant->id)
                                ->first();

                            if ($acumulado_anterior != null) {
                                $acumulado_anterior = $acumulado_anterior->acumulado;
                            } else {
                                $acumulado_anterior = 0;
                            }
                        } else {
                            $acumulado_anterior = 0;
                        }
                        // FIN Calculo acumulado anterior

                        // Calculo de esperado
                        if ($valueItem->is_hoja) {
                            if ($valueItem->is_ajuste_alzado)
                                $actual = 'porcentaje';
                            else
                                $actual = 'cantidad';

                            if ($create['empalme']) {
                                $esperado_mes = $certificado->valorItemCertificado($valueItem->id, $cronograma_id, $create['mes']);
                            }
                            else {
                                $esperado_mes = ItemCronograma::select($actual)
                                    ->whereItemId($valueItem->id)
                                    ->whereCronogramaId($cronograma_id)
                                    ->whereMes($create['mes'])
                                    ->sum($actual);
                            }
                        }
                        else if ($create['empalme']) {
                            $esperado_mes = $certificado->valorItemCertificado($valueItem->id, $cronograma_id,
                                $create['mes']);
                        }
                        else {
                            $actual = 'valor';

                            $esperado_mes = 0;
                            $children = $valueItem->child()->whereResponsableId($responsable_id)->get();
                            foreach ($children as $valueSubItem) {
                                $esperado_mes += ItemCronograma::select($actual)
                                    ->whereItemId($valueSubItem->id)
                                    ->whereCronogramaId($cronograma_id)
                                    ->whereMes($create['mes'])
                                    ->sum($actual);
                            }
                        }

                        $esperado_anterior = $certificado_moneda_ct->esperadoAcumuladoItem($valuePrimerItem->id);
                        // FIN Calculo de esperado

                        // Calculo de desvio
                        $esperado_total = $esperado_mes + $esperado_anterior;
                        if ($acumulado_anterior == null || $acumulado_anterior == 0) {
                            $acumulado_anterior = '0.00';
                            $desvio = '0.00';
                        } elseif ($esperado_total == 0) {
                            $desvio = 100;
                        } else {
                            $desvio = round((($acumulado_anterior * 100) / ($esperado_total))
                                - 100, 2);
                        }

                        // FIN Calculo de desvio
                        $items_array[] = ([
                            'item_id' => $valuePrimerItem->id,
                            'acumulado_anterior' => $acumulado_anterior,
                            'esperado' => $esperado_mes,
                            'monto' => '0.00',
                            'desvio' => $desvio,
                            'porcentaje' => '0.00',
                            'cantidad' => '0.00',
                            'certificado_id' => $certificado_moneda_ct->id,
                            'user_creator_id' => Auth::user()->id,
                            'user_modifier_id' => Auth::user()->id,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]);

                        if ($valuePrimerItem->padre_id != null) {
                            $primerPadre = Item::find($valuePrimerItem->padre_id)
                                ->primer_item_original;

                            $padres[$valuePrimerItem->id] = $primerPadre->id;
                        }
                    }

                    // Clonar jerarquia de itemizado
                    ItemCertificado::insert($items_array);
                    foreach ($padres as $keyPadre => $valuePadre) {
                        $item_hijo = ItemCertificado::whereCertificadoId($certificado_moneda_ct->id)
                            ->whereItemId($keyPadre)->first();

                        $item_padre = ItemCertificado::whereCertificadoId($certificado_moneda_ct->id)
                            ->whereItemId($valuePadre)->first();

                        $item_hijo->padre_id = $item_padre->id;
                        $item_hijo->save();
                    }
                    // FIN Clonar jerarquia de itemizado
                }
            }

            return $certificado;
        });

        $certificado->createInstancia('borrador');

        $this->createInstanciaHistorial($certificado, 'certificado', 'borrador');

        return redirect()->route('certificado.edit', ['id' => $certificado->id]);
    }

    /**
     * @param  int  $id
     */
    public function edit($id, $breadcrumb = null)
    {
        $certificado = Certificado::findOrFail($id);

        if (!Auth::user()->puedeVerCausante($certificado->contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        if ($certificado->redeterminacion_id != null) {
            Session::flash('error', trans('certificado.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('certificado.sin_permisos')]);
        }

        if (!$certificado->borrador or !$certificado->puede_editar) {
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
            foreach ($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista) {
                $sub_header[$valuePorContratista->id] = $valuePorContratista->datos_subheader;
            }
        }

        return view('contratos.certificados.createEdit',
            compact('certificado', 'certificados_por_moneda', 'is_ute', 'edit', 'porcentaje_desvio',
                'sub_header', 'breadcrumb'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function storeUpdate(Request $request, $id)
    {
        $input = $request->except(['_token']);
        $certificado = Certificado::findOrFail($id);
        $certificado->load('contrato.causante');
        $contrato = $certificado->contrato;

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('certificado.sin_permisos'));
            $jsonResponse['url'] = route('contratos.index');
            return $jsonResponse;
        }

        if (!$certificado->borrador) {
            Session::flash('error', trans('certificado.sin_permisos'));
            $jsonResponse['url'] = route('contratos.index');
            return $jsonResponse;
        }

        $borrador = $input['borrador'] == 1;
        $empalme = $input['empalme'] == 1;

        $error_html = array();
        $hay_errores = false;

        foreach ($input['val'] as $keyItemsCertificado => $valueItemsCertificado) {
            $certificado_moneda_ct = CertificadoMonedaContratista::select('contrato_moneda_id',
                'contratista_id')->find($keyItemsCertificado);
            $contrato_moneda_id = $certificado_moneda_ct->contrato_moneda_id;

            $error_html[$contrato_moneda_id]['moneda'] = $certificado_moneda_ct->contrato_moneda->moneda->nombre_simbolo;
            $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['nombre'] = $certificado_moneda_ct->contratista->nombre_documento;
            $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'] = array();

            foreach ($valueItemsCertificado as $keyItem => $keyItem) {
                $valor = $this->dosDecToDB($input['val'][$keyItemsCertificado][$keyItem]);
                $input['val'][$keyItemsCertificado][$keyItem] = $valor;

                if ($empalme) {
                    $cantidad = $this->dosDecToDB($input['cant'][$keyItemsCertificado][$keyItem]);
                }

                $error_tamanio = $this->validarTamanio($input['val'][$keyItemsCertificado], $keyItem,
                    $keyItemsCertificado);
                $item_certificado = ItemCertificado::find($keyItem);
                $item = $item_certificado->item;

                if (sizeof($error_tamanio) > 0) {
                    $hay_errores = true;
                    $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'][] = $item->descripcion_codigo.': '.reset($error_tamanio);
                }
                $acumulado = $valor + $item_certificado->acumulado_anterior;

                if ($item->is_ajuste_alzado) {
                    $maximo = 100;
                } else {
                    $maximo = $item->cantidad;
                }

                if ($empalme) {
                    $acumulado = $valor + (float) $item_certificado->montoItemCertAnterior($item_certificado);
                    $maximo = $item->subtotal;
                }

                if (!$borrador && ($acumulado > $maximo)) {
                    $hay_errores = true;
                    $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'][] = trans('validation_custom.acumulado_mayor_total',
                        ['item' => $item->descripcion_codigo]);
                }

                if (!$borrador && $empalme) {
                    if ($item->is_ajuste_alzado) {
                        $maximo = 100;
                    } else {
                        $maximo = $item->cantidad;
                    }

                    if ($cantidad > $maximo) {
                        $hay_errores = true;
                        $error_html[$contrato_moneda_id]['contratista'][$keyItemsCertificado]['errores'][] = trans('validation_custom.avance_mayor_total',
                            ['item' => $item->descripcion_codigo]);
                    }
                }
            }
        }

        if (isset($input['tiene_redeterminado'])) {
            foreach ($input['redeterminado'] as $keyRedeterminado => $valueRedeterminado) {
                $valor = $this->dosDecToDB($input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes']);
                $input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes'] = $valor;
                $error_tamanio = $this->validarTamanio($input['redeterminado'][$keyRedeterminado],
                    'desc_anticipo_importes_por_ajustes', $keyRedeterminado);

                $valor = $this->dosDecToDB($input['redeterminado'][$keyRedeterminado]['importes_por_ajustes']);
                $input['redeterminado'][$keyRedeterminado]['importes_por_ajustes'] = $valor;
                $error_tamanio = $this->validarTamanio($input['redeterminado'][$keyRedeterminado],
                    'importes_por_ajustes', $keyRedeterminado);

                if ($input['redeterminado'][$keyRedeterminado]['desc_anticipo_importes_por_ajustes'] > $input['redeterminado'][$keyRedeterminado]['importes_por_ajustes']) {
                    $hay_errores = true;
                    $error_html[$contrato_moneda_id]['contratista'][$keyRedeterminado]['errores'][] = trans('validation_custom.descuento_mayor_importes',
                        ['item' => $item->descripcion_codigo]);
                }
            }
        }

        if ($hay_errores) {
            $errores = '';
            foreach ($error_html as $keyErroresMoneda => $valueErroresMoneda) {
                $errores_temp = '<li>'.$valueErroresMoneda['moneda'].':</li>';
                $hay_errores_en_moneda = false;
                foreach ($valueErroresMoneda['contratista'] as $keyErroresContratista => $valueErroresContratista) {
                    if (sizeof($valueErroresContratista['errores']) > 0) {
                        $hay_errores_en_moneda = true;
                        $errores_temp .= '<ul class="pl-1"><li>'.$valueErroresContratista['nombre'].':</li><ul class="pl-1">';
                        foreach ($valueErroresContratista['errores'] as $keyErroresError => $valueErroresError) {
                            $errores_temp .= '<li>'.$valueErroresError.'</li>';
                        }
                        $errores_temp .= '</ul></ul>';
                    }
                }
                if ($hay_errores_en_moneda) {
                    $errores .= $errores_temp;
                }

                $errores .= '';
            }
            $errores = [$errores];
        }

        if (isset($errores) && sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['errores_as_string'] = true;
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        if (!$empalme) {
            if (!$borrador) {
                if (!$certificado->acta_medicion) {
                    $rules['acta_medicion'] = 'required';
                }

                if (!$certificado->seguro_civil) {
                    $rules['seguro_responsabilidad_civil'] = 'required';
                }

                if (!$certificado->seguro_vida) {
                    $rules['seguro_vida'] = 'required';
                }

                if (!$certificado->art) {
                    $rules['ART'] = 'required';
                }

                if (isset($rules)) {
                    $validator = Validator::make($input, $rules);
                    $errores = array();

                    if ($validator->fails() || sizeof($errores) > 0) {
                        $jsonResponse['status'] = false;
                        $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
                        Session::flash('error', trans('mensajes.error.revisar'));
                        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
                        return response()->json($jsonResponse);
                    }
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

                if ($item_certificado->item->nivel > $last_level[$keyItemsCertificado]) {
                    $last_level[$keyItemsCertificado] = $item_certificado->item->nivel;
                }

                $cantidad_nueva = $valueItem;

                if (!$empalme) {
                    $item_certificado->cantidad = $cantidad_nueva;
                }

                $esperado = $certificado_moneda_ct->esperadoAcumuladoItem($item_certificado->item->id);

                if (!$empalme) {
                    $item_certificado->monto = $cantidad_nueva * $item_certificado->item->monto_unitario_o_porcentual;
                }
                else {
                    $item_certificado->monto = $cantidad_nueva;
                }

                $ejecutado_acumulado = $cantidad_nueva + $item_certificado->acumulado_anterior;

                if (!$empalme) {
                    // $planificado_acumulado = $item_certificado->acumulado_anterior + $esperado;
                    if ($ejecutado_acumulado == $esperado) {
                        $item_certificado->desvio = 0;
                    } else {
                        if ($ejecutado_acumulado == 0 || $esperado == 0) {
                            $item_certificado->desvio = 100;
                        } else {
                            $item_certificado->desvio = round((($ejecutado_acumulado * 100) / $esperado)
                                - 100, 2);
                        }
                    }
                }
                // Fuerzo el updated_at para verificar que hayan sido modificados en caso de empalme
                if ($empalme) {
                    $item_certificado->updated_at = $now;
                }

                $item_certificado->save();
            }
        }

        if ($empalme) {
            foreach ($input['cant'] as $keyItemsCertificado => $valueItemsCertificado) {
                $certificado_moneda_ct = CertificadoMonedaContratista::find($keyItemsCertificado);
                $certificados_moneda_ct->push($certificado_moneda_ct);
                $last_level[$keyItemsCertificado] = 1;

                foreach ($valueItemsCertificado as $keyItem => $valueItem) {
                    $item_certificado = ItemCertificado::find($keyItem);
                    $item_certificado->cantidad = $this->dosDecToDB($valueItem);
                    $item_certificado->save();
                }
            }

            $tiene_redeterminados = false;

            if (isset($input['tiene_redeterminado'])) {
                if ($certificado->tiene_redeterminados) {
                    $certificado_redeterminado = $certificado->certificado_redeterminado_empalme;
                    $tiene_redeterminados = true;
                }
                else {
                    $create = [
                        'contrato_id' => $certificado->contrato_id,
                        'mes' => $certificado->mes,
                        'empalme' => 1
                    ];
                    $certificado_redeterminado = new Certificado($create);
                    $certificado_redeterminado->borrador = 0;
                    $certificado_redeterminado->redeterminado = 1;
                    $certificado_redeterminado->redeterminacion_id = $certificado->contrato->last_redeterminacion->id;
                    $certificado_redeterminado->certificado_id = $certificado->id;
                    $certificado_redeterminado->save();

                    $certificado_redeterminado->createInstancia($certificado->estado['nombre']);
                }

                foreach ($certificado->cert_moneda_contratista as $keyCertMoneda => $valueCertMoneda) {
                    $id = $valueCertMoneda->id;
                    $certificado_moneda_ct = null;
                    if ($tiene_redeterminados) {
                        $wheres = [
                            'certificado_id' => $certificado_redeterminado->id,
                            'contrato_moneda_id' => $valueCertMoneda->contrato_moneda_id,
                            'itemizado_id' => $valueCertMoneda->itemizado_id,
                            'contratista_id' => $valueCertMoneda->contratista_id
                        ];

                        $certificado_moneda_ct = CertificadoMonedaContratista::where($wheres)->first();
                    }

                    if ($certificado_moneda_ct == null) {
                        $certificado_moneda_ct = CertificadoMonedaContratista::create([
                            'certificado_id' => $certificado_redeterminado->id,
                            'contrato_moneda_id' => $valueCertMoneda->contrato_moneda_id,
                            'itemizado_id' => $valueCertMoneda->itemizado_id,
                            'contratista_id' => $valueCertMoneda->contratista_id
                        ]);
                    }

                    $certificado_moneda_ct->monto_bruto = $input['redeterminado'][$id]['importes_por_ajustes'];
                    $certificado_moneda_ct->monto = $input['redeterminado'][$id]['importes_por_ajustes'] - $input['redeterminado'][$id]['desc_anticipo_importes_por_ajustes'];
                    $certificado_moneda_ct->save();
                }

            }
            else {
                if ($certificado->tiene_redeterminados) {
                    $certificado_redeterminado = $certificado->certificado_redeterminado_empalme;
                    $certificado_redeterminado->delete();
                }
            }
        }

        #region Calculo para arriba los valores de los items sumarizando sus hijos
        foreach ($certificados_moneda_ct as $valueCertifMonedaContr) {
            for ($i = $last_level[$valueCertifMonedaContr->id]; $i > 0; $i--) {
                $items_n = $valueCertifMonedaContr->items_nivel_n($i);

                foreach ($items_n as $valueItem) {
                    $valueItem->monto = $valueItem->child_sum;

                    $planificado_acumulado = $valueItem->acumulado_anterior + $valueItem->esperado;

                    if ($planificado_acumulado == 0) {
                        $valueItem->desvio = 100;
                    }
                    else {
                        $valueItem->desvio = round((($valueItem->child_sum * 100) / $planificado_acumulado) - 100, 2);
                    }

                    $valueItem->save();
                }
            }

            // Calculo el total del CertificadoMonedaContratista
            $valueCertifMonedaContr->monto_bruto = $valueCertifMonedaContr->items_nivel1_sum;

            if ($empalme) {
                if (isset($input['anticipo_id'])) {
                    $certificado->anticipo_id = $input['anticipo_id'];
                    $certificado->save();
                }
            }

            $anticipo = $valueCertifMonedaContr->certificado->anticipo;

            if ($anticipo == null) {
                $valueCertifMonedaContr->monto = $valueCertifMonedaContr->monto_bruto;
            }
            elseif ($empalme) {
                $uno = $valueCertifMonedaContr->monto_bruto;
                $dos = $this->dosDecToDB($input['anticipo_monto']);
                $valueCertifMonedaContr->monto = $uno - $dos;
            }
            else {
                $anticipo_item = $valueCertifMonedaContr->item_anticipo;
                $valueCertifMonedaContr->monto = $valueCertifMonedaContr->monto_bruto * (1 - ($anticipo_item->porcentaje_100));
            }

            $valueCertifMonedaContr->save();

            // Calculo el desvio del CertificadoMonedaContratista
            $valueCertifMonedaContr->calcularDesvio();

        }
        #endregion

        #region Adjuntos
        if (!$empalme) {
            if (isset($input['acta_medicion']) && $request->hasFile('acta_medicion'))
                $this->saveAdjuntos($request, $certificado, 'acta_medicion');

            if (isset($input['seguro_responsabilidad_civil']) && $request->hasFile('seguro_responsabilidad_civil'))
                $this->saveAdjuntos($request, $certificado, 'seguro_responsabilidad_civil');

            if (isset($input['seguro_vida']) && $request->hasFile('seguro_vida'))
                $this->saveAdjuntos($request, $certificado, 'seguro_vida');

            if (isset($input['ART']) && $request->hasFile('ART'))
                $this->saveAdjuntos($request, $certificado, 'ART');

            if (isset($input['nueve_tres_uno']) && $request->hasFile('nueve_tres_uno'))
                $this->saveAdjuntos($request, $certificado, 'nueve_tres_uno');

            if (isset($input['adjunto']) && $request->hasFile('adjunto'))
                $this->saveAdjuntos($request, $certificado, 'adjunto');
        }
        #endregion

        // Doble firma
        $causante = $contrato->causante;

        if ($causante)
            $this->inputDoblefirma($input, $causante);

        if (!$borrador) {
            $estado = 'aprobado';

            $certificado->borrador = 0;
            $certificado->doble_firma = \Arr::get($input, 'doble_firma');
            $certificado->firma_ar = \Arr::get($input, 'firma_ar');
            $certificado->firma_py = \Arr::get($input, 'firma_py');
            $certificado->save();

            $estado = 'aprobado';

            if ($certificado->doble_firma) {
                if (!$certificado->firma_ar && !$certificado->firma_py)
                    $estado = 'a_firmar';
                else
                    $estado = 'firma';
            }

            $certificado->createInstancia($estado);
            $this->createInstanciaHistorial($certificado, 'certificado', $estado);

            if ($estado == 'aprobado')
                $this->finish($certificado);
        }

        $jsonResponse['status'] = true;

        if ($borrador) {
            Session::flash('success', trans('mensajes.dato.certificado').trans('mensajes.success.editado'));
            $jsonResponse['message'] = [trans('mensajes.dato.certificado').trans('mensajes.success.editado')];
            $jsonResponse['refresh'] = false;
        }
        else {
            Session::flash('success', trans('mensajes.dato.certificado').trans('mensajes.success.creado'));
            $jsonResponse['message'] = [trans('mensajes.dato.certificado').trans('mensajes.success.creado')];
        }

        $jsonResponse['url'] = route('contratos.ver.incompleto', [
            'id' => $certificado->contrato_id,
            'accion' => 'certificados'
        ]);
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     */
    public function ver($id)
    {
        $breadcrumb = Request()->breadcrumb;
        $certificado = Certificado::findOrFail($id);

        if (!Auth::user()->puedeVerCausante($certificado->contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        if ($certificado->redeterminacion_id != null) {
            Session::flash('error', trans('certificado.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('certificado.sin_permisos')]);
        }

        $is_ute = $certificado->contrato->contratista->is_ute;
        $certificados_por_moneda = $certificado->certificados_por_moneda;
        $porcentaje_desvio = ItemCertificado::getPorcentajeDesvio();
        $edit = false;

        $sub_header = array();

        foreach ($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda) {
            foreach ($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista) {
                $sub_header[$valuePorContratista->id] = $valuePorContratista->datos_subheader;
            }
        }

        return view('contratos.certificados.createEdit', compact(
            'certificado', 'certificados_por_moneda', 'is_ute', 'edit', 'porcentaje_desvio',
            'sub_header', 'breadcrumb'
        ));
    }

    public function sign($id)
    {
        $certificado = Certificado::findOrFail($id);
        $certificado->load('contrato.causante');

        // Contrato
        $contrato = $certificado->contrato;

        // Causante
        $causante = $contrato->causante;

        if (!$causante) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_sin_causante', [
                    'name' => trans('index.certificado')
                ])
            ]);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_no_admite', [
                    'name' => trans('index.anticipo')
                ])
            ]);
        }

        // Verifica se si es uno de los jefes que deben firmar
        $firma_ar = $causante->jefe_contrato_ar;
        $firma_py = $causante->jefe_contrato_py;

        if (!in_array(Auth::user()->id, [$firma_ar, $firma_py])) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_no_es_jefe', [
                    'name' => trans('index.contrato')
                ])
            ]);
        }

        // Firma si es el del lado argentino
        if ($firma_ar == Auth::user()->id) {
            $certificado->firma_ar = Auth::user()->id;
        }

        // Firma si es el del lado paraguay
        if ($firma_py == Auth::user()->id) {
            $certificado->firma_py = Auth::user()->id;
        }

        // Firma conseguida
        if ($certificado->firma_ar && $certificado->firma_py) {
            $certificado->doble_firma = false;
        }

        // Guardar cambios
        $certificado->save();

        $estado = 'aprobado';

        if ($certificado->doble_firma) {
            if (!$certificado->firma_ar && !$certificado->firma_py)
                $estado = 'a_firmar';
            else
                $estado = 'firma';
        }

        $certificado->createInstancia($estado);
        $this->createInstanciaHistorial($certificado, 'certificado', $estado);

        if (!$certificado->doble_firma)
            $this->finish($certificado);

        // Respuesta
        $response = [];
        $response['status'] = true;
        $response['refresh'] = true;

        $message = trans('mensajes.success.firmado', [
            'type' => 'certificado',
            'name' => '',
        ]);

        $response['message'] = [
            $message,
        ];

        Session::flash('success', $message);

        return response()->json($response);
    }

    public function finish($certificado) {
        if ($certificado->mes == 1) {
            foreach ($certificado->cert_moneda_contratista as $valueCertifMonedaContr) {
                foreach ($valueCertifMonedaContr->itemizado->items as $valueItem) {
                    if (!$valueItem->certificado) {
                        $valueItem->certificado = 1;
                        $valueItem->save();
                    }
                }
            }
        }

        foreach ($certificado->cert_moneda_contratista as $valueCertifMonedaContr) {
            $contrato_moneda = $valueCertifMonedaContr->contrato_moneda;
            $certificado->contrato->reCalculoMontoYSaldo($contrato_moneda->id);
        }

        if ($certificado->tiene_redeterminados || isset($certificado_redeterminado)) {
            if (isset($certificado_redeterminado) && $certificado_redeterminado != null)
                $certificado_redeterminado = $certificado_redeterminado;
            else
                $certificado_redeterminado = $certificado->certificado_redeterminado_empalme;

            $certificado_redeterminado->borrador = 0;
            $certificado_redeterminado->createInstancia('aprobado');
            $certificado_redeterminado->save();
        }

        // aprobar instancia en solicitud de redeterminacion
        // monto_vigente null es que todavia no pasaron por etapa de CalculoPreciosRedeterminados
        $solicitudes = $certificado->contrato->solicitudes_redeterminacion()
            ->whereFinalizada(0)
            ->where('monto_vigente', null)
            ->get();

        if ($solicitudes) {
            // checkeo si existe solicitud x mes actual
            $fecha_certificado = date('m/Y', strtotime($certificado->fecha));

            foreach ($solicitudes as $valueSolicitud) {
                $mes_publicacion = $valueSolicitud->salto->publicacion->mes_anio_anterior;

                if ($fecha_certificado == $mes_publicacion) {
                    // si la solicitud esta en esa instancia la apruebo
                    $instancia_actual = $valueSolicitud->instancia_actual;
                    $instancia_tipo = $instancia_actual->tipo_instancia;

                    if ($instancia_tipo->modelo == 'AprobacionCertificados') {

                        $instancia = $instancia_actual->instancia;
                        $instancia->certificado_id = $certificado->id;
                        $instancia->certificados_aprobados = 1;
                        $instancia->save();

                        $instancia_siguiente = $instancia_actual->instancia_siguiente;
                        $valueSolicitud->instancia_actual_id = $instancia_siguiente->id;
                        $valueSolicitud->save();

                        $instancia_siguiente->fecha_inicio = date("Y-m-d H:i:s");
                        $instancia_siguiente->save();

                        if ($instancia_siguiente->tipo_instancia->modelo == 'CalculoPreciosRedeterminados') {
                            dispatch((new InstanciaCalculoPrecios($valueSolicitud->id))
                                ->onQueue('calculos_variacion'));
                        }
                    }
                }
            }
        }

        $contrato = $certificado->contrato;

        // Creo los de empalme
        /*foreach ($contrato->redeterminaciones_empalme as $keyRedeterminacion => $valueRedeterminacion) {
            $solicitud = new SolicitudRedeterminacion();
            $solicitud->a_termino = true;
            app('SolicitudesRedeterminacionController')->createCertificadosRedeterminadosAnteriores($valueRedeterminacion, $certificado);
          }*/

        $solicitudes = $contrato->solicitudes_redeterminacion()
            ->whereFinalizada(1)
            ->get();

        // Creo los que tienen solicitud
        foreach ($solicitudes as $valueSolicitud) {
            $redeterminacion = $valueSolicitud->redeterminacion;
            app('SolicitudesRedeterminacionController')
                ->createCertificadosRedeterminadosAnteriores($redeterminacion, $certificado);
        }
    }

    #endregion

    #region Eliminar

    /**
     * @param  int  $id
     */
    public function preDelete($id)
    {
        $certificado = Certificado::select('borrador')->find($id);
        if ($certificado->borrador) {
            $jsonResponse['status'] = true;
        } else {
            $jsonResponse['status'] = false;
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('contratos.certificado');

            $jsonResponse['message'] = [trans('index.no_puede_eliminar.certificado')];
        }
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     */
    public function delete($id)
    {
        if ($this->preDelete($id)->getData()->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [$this->preDelete($id)->getData()->message];
            return response()->json($jsonResponse);
        }

        $certificado = Certificado::find($id);

        try {
            if ($certificado->tiene_redeterminados) {
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
        $jsonResponse['message'] = [trans('mensajes.dato.certificado').trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }

    #endregion

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  Contrato\Certificado\Certificado  $certificado
     * @param  string  $input
     */
    public function saveAdjuntos($request, $certificado, $input)
    {
        $adjuntoTipo = TiposAdjuntos::whereNombre($input)->first();
        if ($adjuntoTipo == null) {
            $adjuntoTipo = TiposAdjuntos::whereNombre('otros')->first();
        }

        switch ($input) {
            case 'acta_medicion':
                if ($certificado->acta_medicion) {
                    $certificado->acta_medicion->delete();
                }
                break;
            case 'seguro_responsabilidad_civil':
                if ($certificado->seguro_civil) {
                    $certificado->seguro_civil->delete();
                }
                break;
            case 'seguro_vida':
                if ($certificado->seguro_vida) {
                    $certificado->seguro_vida->delete();
                }
                break;
            case 'ART':
                if ($certificado->art) {
                    $certificado->art->delete();
                }
                break;
            case 'nueve_tres_uno':
                if ($certificado->nueve_tres_uno) {
                    $certificado->nueve_tres_uno->delete();
                }
                break;
            default:
                if ($certificado->otros_adjuntos) {
                    $certificado->otros_adjuntos->delete();
                }
                break;
        }

        $adjuntos_json = $this->uploadFile($request, $certificado->id, $input, 'certificado');
        CertificadosAdjuntos::create([
            'certificado_id' => $certificado->id,
            'adjunto_tipo_id' => $adjuntoTipo->id,
            'adjunto' => $adjuntos_json,
        ]);
    }

    /**
     * @param  int  $id
     */
    public function exportar($id)
    {
        try{
            $certificado = Certificado::findOrFail($id);
            $certificados_por_moneda = $certificado->certificados_por_moneda;
            /*
            $array = array();
            foreach($certificados_por_moneda as $keyCertMoneda => $valueCertMoneda){
                foreach($valueCertMoneda['certificados'] as $keyPorContratista => $valuePorContratista){
                    foreach($valuePorContratista->items as $item){
                        
                        if(isset($item->item->is_hoja)){
                            array_push($array, $item->item);                            
                        }
                    }
                }
            }
            */
            
            $dompdf = new Dompdf();
    
            $nombre = $certificado->mes_show.' '.$certificado->mesAnio('fecha', 'Y-m-d');
    
            $html = View::make('contratos.certificados.descarga.certificado', compact('certificado', 'certificados_por_moneda', 'array'))->render();
            //$jsonResponse = View::make('publicaciones.historial', compact('instancias'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $dompdf->stream(trans('contratos.certificado').' nº'.$certificado->id.' - '.$nombre.' - '.$certificado->contrato->numero_contrato.'.pdf');
           
        }
        catch(\Exception $ex) {
            //report($ex);
            return response()->json([
                'data' => $ex->getMessage().' - Line'.$ex->getLine(),
                'status' => 500]);

        }
        
    }

}
