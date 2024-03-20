<?php

namespace App\Jobs;

use CalculoRedeterminacion\VariacionMesPolinomica;
use CuadroComparativo\CategoriaCuadroComparativo;
use CuadroComparativo\ComponenteCuadroComparativo;
use CuadroComparativo\CuadroComparativo;
use CuadroComparativo\ItemCuadroComparativo;
use DateTime;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Indice\PublicacionIndice;
use Indice\ValorIndicePublicado;
use Log;
use YacyretaJobs\BaseJob;

class CalculoPrecios extends BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $salto_id;

    /**
     * Create a new job instance.
     * @param int $salto_id
     * @return void
     */
    public function __construct($salto_id = null)
    {
        $this->salto_id = $salto_id;
    }

    public function handle()
    {
        set_time_limit(0);
        ini_set('memory_limit',-1);

        $relanzar = false;
        $encolar_instancia = false;

        $this->toLog('Inicio CalculoPrecios');
        $query = VariacionMesPolinomica::with([
                'contrato_moneda.itemizado.analisis_precios',
                'contrato_moneda.itemizado.analisis_precios_vigente',
                'contrato_moneda.itemizado_vigente.analisis_precios',
                'contrato_moneda.itemizado_vigente.analisis_precios_vigente',
            ])
            ->whereEsSalto(1)
            ->whereCalculado(0)
            ->orderBy('contrato_moneda_id')
            ->orderBy('nro_salto');

        if ($this->salto_id)
            $query ->whereId($this->salto_id);

        $saltos = $query->get();

        $this->toLog('Cantidad de Saltos Pre-filtrado: ' . sizeof($saltos));
        $cantitdades = array();

        // Se eliminan los saltos que con moneda que llevan analisis de precio, pero no tengan analisis de precio
        // o no esten aporbados
        foreach ($saltos as $keySalto => $valueSalto) {
            $analisisPrecios = $valueSalto->contrato_moneda->itemizado_actual->analisis_precios_actual;
            $analisisPrecioEstado = $analisisPrecios ? $analisisPrecios->estado['nombre'] : null;

            if ((is_null($analisisPrecios) || $analisisPrecioEstado != 'aprobado')  && $valueSalto->contrato_moneda->lleva_analisis) {
                if (!$analisisPrecios)
                    $this->toLog("Sin Análisis de Precios: salto_id=$valueSalto->id");
                else
                    $this->toLog("El Análisis de Precios del salto id=$valueSalto->id se encuentra en estado: $analisisPrecioEstado");

                if (isset($cantitdades[$analisisPrecioEstado]))
                    $cantitdades[$analisisPrecioEstado]++;
                else
                    $cantitdades[$analisisPrecioEstado] = 0;

                $saltos->forget($keySalto);
            }
        }

        // Log de cantidades de saltos por estado
        $msg_cant = '';

        foreach ($cantitdades as $keyEstado => $valueEstado) {
            $msg_cant .= 'Hay ' . $valueEstado . ' en estado "' . $keyEstado . '". ';
        }

        if ($msg_cant != '')
            $this->toLog($msg_cant);

        // Registra la cantidad se saltos que se procesará
        $this->toLog('Cantidad de Saltos Post-filtrado: ' . sizeof($saltos));

        foreach ($saltos as $keySalto => $valueSalto) {
            $param = array();
            $param['vrs_indices'] = array();
            $param['valueSalto'] = $valueSalto;
            $param['contrato_moneda'] = $param['valueSalto']->contrato_moneda;

            if (!$param['contrato_moneda']->contrato->falta_completar_empalme) {
                $param['salto_anterior'] = $param['valueSalto']->salto_anterior_o_inicio;
                $calculado = 1;

                $param['solicitud_id'] = null;
                if ($param['valueSalto']->solicitado)
                    $param['solicitud_id'] = $param['valueSalto']->solicitud->id;

                $param['primera_empalme'] = $param['contrato_moneda']->contrato->empalme &&
                    ($param['salto_anterior']->nro_salto == null || $param['salto_anterior']->empalme);

                if ($param['primera_empalme'])
                    $param['ultima_redeterminacion_empalme'] = $param['contrato_moneda']->ultima_redeterminacion_empalme;
                else
                    $param['ultima_redeterminacion_empalme'] = null;

                // Si el salto anterior no se calclulo, no se calcula este
                $falta_anterior = false;
                if (!$param['primera_empalme']) {
                    if (!$param['salto_anterior']->calculado && $param['valueSalto']->nro_salto != 1) {
                        $falta_anterior = true;
                        $calculado = 0;
                    }
                }

                if ($param['valueSalto']->solicitud != null && $param['valueSalto']->solicitud->cuadro_comparativo != null) {
                    $this->toLog('El salto id=' . $param['valueSalto']->id . ' (n° Salto: ' . $param['valueSalto']->nro_salto . ') no se pudo calcular porque ya tiene Cuadro Comparativo id=' . $param['valueSalto']->solicitud->cuadro_comparativo->id, 'error');
                }
                elseif ($falta_anterior) {
                    $this->toLog('El salto id=' . $param['valueSalto']->id . ' (n° Salto: ' . $param['valueSalto']->nro_salto . ') no se pudo calcular porque falta el de id=' . $param['salto_anterior']->id, 'error');
                }
                else {
                    $this->toLog('Cálculo de variación de Salto: id=' . $param['valueSalto']->id);

                    // Datos basicos del calculo
                    if ($param['valueSalto']->nro_salto == 1) {
                        $fecha_oferta = DateTime::createFromFormat('d/m/Y', $param['valueSalto']->contrato->fecha_oferta);

                        $param['publicacion_anterior'] = PublicacionIndice::whereMes($fecha_oferta->format('m'))
                            ->whereAnio($fecha_oferta->format('Y'))
                            ->whereMoneda_id($param['contrato_moneda']->moneda_id)
                            ->first();
                    }
                    elseif ($param['primera_empalme']) {
                        $param['publicacion_anterior'] = $param['ultima_redeterminacion_empalme']->publicacion;
                    }
                    else {
                        $param['publicacion_anterior'] = $param['salto_anterior']->publicacion;
                    }

                    $param['publicacion_actual'] = $param['valueSalto']->publicacion;
                    $contrato = $param['contrato_moneda']->contrato;
                    $param['itemizado'] = $param['contrato_moneda']->itemizado_actual;
                    // FIN Datos basicos del calculo

                    // Si el contrato no comenzo el dia 1, hay un mes extra
                    $fecha_acta_inicio = DateTime::createFromFormat('d/m/Y', $contrato->fecha_acta_inicio);
                    $primerDia = $fecha_acta_inicio->format('d');
                    $publicacion_mes_anio = DateTime::createFromFormat('d/m/Y', '01/' . $param['publicacion_actual']->mes_anio);

                    $date_diff = $publicacion_mes_anio->diff($fecha_acta_inicio);
                    $param['meses_diferencia'] = $date_diff->m + ($date_diff->y * 12);
                    // Si salta antes del inicio del contrato o en la misma fecha
                    if ($date_diff->invert == 0) {
                        $param['meses_diferencia'] = 0;
                    }
                    elseif ($param['meses_diferencia'] > 0) {
                        $param['meses_diferencia'] = $param['meses_diferencia'] + 1;
                        if ($date_diff->d > 0)
                            $param['meses_diferencia'] = $param['meses_diferencia'] + 1;
                    }
                    elseif ($param['meses_diferencia'] == 0 && $date_diff->d > 0) {
                        $param['meses_diferencia'] = 1;
                    }
                    // FIN Si el contrato no comenzo el dia 1, hay un mes extra

                    if ($param['contrato_moneda']->lleva_analisis) {
                        // Caso 1: con Analisis de Precios
                        $this->toLog('Cálculo con Análisis de Precios');

                        $param['analisis_precios'] = $param['itemizado']->analisis_precios_actual;

                        // Verifico que haya Analisis de Precios aprobado
                        if ($param['analisis_precios'] != null && $param['analisis_precios']->estado['nombre'] == 'aprobado') {
                            // Transaction
                            $transaction = DB::transaction(function () use ($param) {
                                $this->toLog('Análisis de Precios id=' . $param['analisis_precios']->id);

                                // Calculo vr de cada indice, sin importar el componente al que fue asociado
                                foreach ($param['analisis_precios']->all_indices as $keyIndice => $valueIndice) {
                                    $this->toLog('  Publicación Anterior: ' . $param['publicacion_anterior']->mes_anio . '  Publicación Actual: ' . $param['publicacion_actual']->mes_anio);

                                    $this->toLog('indice id=' . $valueIndice->id);

                                    $tabla_indice_inicial = ValorIndicePublicado::wherePublicacionId($param['publicacion_anterior']->id)->whereTablaIndicesId($valueIndice->id)->first();
                                    
                                    if(is_null($tabla_indice_inicial))
                                        $valor_inicial = 0;
                                    else
                                        $valor_inicial = ValorIndicePublicado::wherePublicacionId($param['publicacion_anterior']->id)->whereTablaIndicesId($valueIndice->id)->first()->valor;   

                                    $valor_final = ValorIndicePublicado::wherePublicacionId($param['publicacion_actual']->id)
                                        ->whereTablaIndicesId($valueIndice->id)
                                        ->first()
                                        ->valor;

                                    if ($valor_inicial == 0)
                                        $param['vrs_indices'][$valueIndice->id] = 1;
                                    else
                                        $param['vrs_indices'][$valueIndice->id] = $valor_final / $valor_inicial;
                                }
                                // FIN Calculo vr de cada indice, sin importar el componente al que fue asociado

                                // Creo CuadroComparativo y tomo el id
                                $cuadro_comparativo = CuadroComparativo::create(['itemizado_id' => $param['itemizado']->id,
                                    'contrato_moneda_id' => $param['contrato_moneda']->id,
                                    'solicitud_id' => $param['solicitud_id'],
                                    'salto_id' => $param['valueSalto']->id,
                                    'publicacion_anterior_id' => $param['publicacion_anterior']->id,
                                    'publicacion_actual_id' => $param['publicacion_actual']->id
                                ]);
                                $cuadro_comparativo_id = $cuadro_comparativo->id;
                                // FIN Creo CuadroComparativo y tomo el id

                                foreach ($param['analisis_precios']->items_hoja as $keyItem => $valueItemHoja) {
                                    $this->toLog('Cálculo de item id=' . $valueItemHoja->id . ' (' . $valueItemHoja->descripcion . ')');
                                    $analisis_item = $param['analisis_precios']->getAnalisisItem($valueItemHoja->id);

                                    // Calculo el VR de equipos
                                    $total_inicial = 0;
                                    $total_final = 0;

                                    foreach ($analisis_item->componentes_equipo as $keyComponente => $valueComponente) {
                                        if ($param['valueSalto']->nro_salto == 1) {
                                            $costo_total_anterior = $valueComponente->costo_total_adaptado;
                                        }
                                        elseif ($param['primera_empalme']) {
                                            $componente = $param['ultima_redeterminacion_empalme']->analisisItemRedeterminadoId($item_cuadro_comp->item_id)
                                                ->all_componentes
                                                ->filter(function ($componente) use ($valueComponente) {
                                                    return $componente->componente_id == $valueComponente->id;
                                                })->first();

                                            if ($componente == null)
                                                $costo_total_anterior = $valueComponente->costo_total_adaptado;
                                            else
                                                $costo_total_anterior = $componente->costo_total;
                                        }
                                        else {
                                            $cuadro_comparativo_anterior = $param['salto_anterior']->cuadro_comparativo;

                                            $costo_total_anterior = $cuadro_comparativo_anterior->getItemCuadroComparativo($valueItemHoja)
                                                ->getCategoriaCuadro($valueComponente->categoria_id)
                                                ->componentes()->whereComponenteId($valueComponente->id)
                                                ->first()->vr_por_costo;
                                        }
                                        $total_inicial = $total_inicial + $costo_total_anterior;
                                        $total_final = $total_final + $costo_total_anterior * $param['vrs_indices'][$valueComponente->indice_id];
                                    }

                                    if ($total_inicial == 0) {
                                        $vr_componentes_equipo = 1;
                                    }
                                    else {
                                        $vr_componentes_equipo = $total_final / $total_inicial;
                                    }
                                    $this->toLog('VR Equipos: ' . $vr_componentes_equipo);
                                    // FIN Calculo el VR de equipos

                                    // Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)
                                    $medicion_cronograma = $this->medicionCronograma($valueItemHoja, $param['meses_diferencia']);
                                    // FIN Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)

                                    // Creo ItemCuadroComparativo, agrego VR de equipos y tomo el id
                                    $item_cuadro_comp = ItemCuadroComparativo::create(['item_id' => $valueItemHoja->id,
                                        'cuadro_comparativo_id' => $cuadro_comparativo_id,
                                        'medicion_cronograma' => $medicion_cronograma
                                    ]);

                                    $item_cuadro_comp->vr_equipos = $vr_componentes_equipo;
                                    $item_cuadro_comp->save();
                                    $item_cuadro_comp_id = $item_cuadro_comp->id;
                                    // FIN Creo ItemCuadroComparativo, agrego VR de equipos y tomo el id

                                    foreach ($analisis_item->categorias as $keyCategoria => $valueCategoria) {
                                        $valueCategoria = $valueCategoria->thisWithClase();
                                        $categoria_cuadro = CategoriaCuadroComparativo::create(['item_cuadro_id' => $item_cuadro_comp_id,
                                            'categoria_id' => $valueCategoria->id
                                        ]);
                                        $categoria_cuadro_id = $categoria_cuadro->id;
                                        if ($valueCategoria->tiene_componentes) {
                                            foreach ($valueCategoria->componentes as $keyComponente => $valueComponente) {
                                                if ($valueComponente->indice == null) {
                                                    $vr = $vr_componentes_equipo;
                                                }
                                                else {
                                                    $vr = $param['vrs_indices'][$valueComponente->indice_id];
                                                }

                                                if ($param['valueSalto']->nro_salto == 1) {
                                                    $costo_total_anterior = $valueComponente->costo_total_adaptado;
                                                }
                                                elseif ($param['primera_empalme']) {
                                                    $componente = $param['ultima_redeterminacion_empalme']->analisisItemRedeterminadoId($item_cuadro_comp->item_id)
                                                        ->all_componentes
                                                        ->filter(function ($componente) use ($valueComponente) {
                                                            return $componente->componente_id == $valueComponente->id;
                                                        })->first();

                                                    if ($componente == null)
                                                        $costo_total_anterior = $valueComponente->costo_total_adaptado;
                                                    else
                                                        $costo_total_anterior = $componente->costo_total_adaptado;
                                                }
                                                else {
                                                    $cuadro_comparativo_anterior = $param['salto_anterior']->cuadro_comparativo;

                                                    $item_cuadro_anterior = $cuadro_comparativo_anterior->getItemCuadroComparativo($valueItemHoja);
                                                    if ($item_cuadro_anterior == null) {
                                                        $this->toLog('No existe el Item id="' . $valueItemHoja->id . '" en el CUadro Comparativo Anterior: id=' . $cuadro_comparativo_anterior->id);
                                                        dispatch((new CalculoItemAdenda())->onQueue('calculos_variacion'));

                                                        return false;
                                                    }
                                                    $costo_total_anterior = $item_cuadro_anterior->getCategoriaCuadro($valueCategoria->id)
                                                        ->componentes()->whereComponenteId($valueComponente->id)
                                                        ->first()->vr_por_costo;

                                                }

                                                $vr_por_costo = $vr * $costo_total_anterior;

                                                $this->toLog('Componente "' . $valueComponente->nombre . '" con costo_total_adaptado de: ' . $costo_total_anterior . ' y vr: ' . $vr . ' a ' . $vr_por_costo);
                                                // Creo ComponenteCuadroComparativo
                                                $componente_cuadro = ComponenteCuadroComparativo::create(['componente_id' => $valueComponente->id,
                                                    'categoria_id' => $categoria_cuadro_id,
                                                    'vr' => $vr,
                                                    'vr_por_costo' => number_format((float)$vr_por_costo, 8, '.', ''),
                                                    'costo_anterior' => number_format((float)$costo_total_anterior, 2, '.', ''),
                                                ]);
                                                // FIN Creo ComponenteCuadroComparativo
                                            }
                                        }
                                    }

                                    $categorias_hoja = $item_cuadro_comp->categorias->filter(function ($categoria) {
                                        return $categoria->tiene_componentes;
                                    });

                                    foreach ($categorias_hoja as $keyCategoriaCuadro => $valueCategoriaCuadro) {
                                        $valueCategoriaCuadro->calcularTotal();
                                    }

                                    $this->toLog('FIN Cálculo de item id=' . $valueItemHoja->id . ' (' . $valueItemHoja->descripcion . ')');
                                }
                            });
                            // FIN Transaction
                        }
                        elseif ($param['analisis_precios'] == null) {
                            // Error de no existe Analisis de Precios
                            $this->toLog('El Salto id=' . $param['valueSalto']->id . ' no tiene Análisis de Precios', 'error');
                            $calculado = 0;
                        }
                        else {
                            // Si el Analisis de Precios no esta aprobado, no se calcula
                            Log::info('El Análisis de Precios del salto id=' . $param['valueSalto']->id . ' se encuentra en estado: ' . $param['analisis_precios']->estado['nombre']);
                            $calculado = 0;
                        }
                        // FIN Caso 1: con Analisis de Precios
                    }
                    else {
                        // Caso 2: sin Analisis de Precios
                        $this->toLog('Cálculo sin Análisis de Precios');
                        $vr = $param['valueSalto']->variacion;

                        // Creo CuadroComparativo
                        $cuadro_comparativo = CuadroComparativo::create(['itemizado_id' => $param['itemizado']->id,
                            'contrato_moneda_id' => $param['contrato_moneda']->id,
                            'solicitud_id' => $param['solicitud_id'],
                            'salto_id' => $param['valueSalto']->id,
                            'publicacion_anterior_id' => $param['publicacion_anterior']->id,
                            'publicacion_actual_id' => $param['publicacion_actual']->id
                        ]);
                        $cuadro_comparativo_id = $cuadro_comparativo->id;
                        foreach ($param['itemizado']->items_hoja as $keyItem => $valueItemHoja) {
                            // Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)
                            $medicion_cronograma = $this->medicionCronograma($valueItemHoja, $param['meses_diferencia']);
                            // FIN Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)

                            // Creo ItemCuadroComparativo
                            $item_cuadro_comp = ItemCuadroComparativo::create(['item_id' => $valueItemHoja->id,
                                'cuadro_comparativo_id' => $cuadro_comparativo_id,
                                'medicion_cronograma' => $medicion_cronograma
                            ]);

                        }
                    }
                    // FIN Caso 2: sin Analisis de Precios
                }
                $this->toLog('FIN Cálculo de Salto id=' . $param['valueSalto']->id);

                $param['valueSalto']->calculado = $calculado;
                $param['valueSalto']->save();
                if ($calculado)
                    $relanzar = true;

                if (!$encolar_instancia && $param['valueSalto']->calculado && $param['valueSalto']->solicitado) {
                    $solicitud = $param['valueSalto']->solicitud;
                    $estado_calculo = $solicitud->estado_nombre_color['nombre'] == trans('sol_redeterminaciones.instancias.CalculoPreciosRedeterminados');
                    if ($estado_calculo) {
                        $encolar_instancia = true;
                    }
                }
            }
            else {
                $this->toLog('El salto id=' . $param['valueSalto']->id . ' (n° Salto: ' . $param['valueSalto']->nro_salto . ') no se pudo calcular porque falta Completar el Empalme', 'error');
            }
        }

        if ($relanzar) {
            $this->toLog('CalculoPrecios Re-encolado');
            dispatch((new CalculoPrecios())->onQueue('calculos_variacion'));
        }

        if ($encolar_instancia) {
            $this->toLog('InstanciaCalculoPrecios Re-encolado');
            dispatch((new InstanciaCalculoPrecios($solicitud->id))->onQueue('calculos_variacion'));
        }

        $this->toLog('FIN CalculoPrecios');
    }

    /**
     * @param Itemizado\Item $valueItemHoja
     * @param int $meses_diferencia
     */
    public function medicionCronograma($valueItemHoja, $meses_diferencia)
    {
        // Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)
        if ($valueItemHoja->is_ajuste_alzado) {
            $medida = 'porcentaje';
        }
        elseif ($valueItemHoja->is_unidad_medida) {
            $medida = 'cantidad';
        }

        return $valueItemHoja->items_cronograma()
            ->where('mes', '>=', $meses_diferencia)
            ->sum($medida);
        // Calculo la Medicion a Certificar en base a los meses que quedan certificar (medicion_cronograma)
    }

}
