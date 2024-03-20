<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Indice\PublicacionIndice;
use Indice\ValorIndicePublicado;

use CuadroComparativo\CuadroComparativo;
use CuadroComparativo\ItemCuadroComparativo;
use CuadroComparativo\CategoriaCuadroComparativo;
use CuadroComparativo\ComponenteCuadroComparativo;

use AnalisisPrecios\AnalisisItem;

use DB;
use Log;
use DateTime;

use YacyretaJobs\BaseJob;
class CalculoItemAdenda extends BaseJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $analisis_item_id;

    /**
     * Create a new job instance.
     * @param  int   $analisis_item_id
     * @return void
    */
    public function __construct($analisis_item_id = null) {
      $this->analisis_item_id = $analisis_item_id;
    }

    public function handle() {
      $this->toLog('Inicio CalculoItemAdenda');

      if($this->analisis_item_id != null) {
        $analisis_items = AnalisisItem::whereId($this->analisis_item_id)
                                      ->whereARedeterminar(1)
                                      ->orderBy('analisis_precios_id')
                                      ->get();
      } else {
        $analisis_items = AnalisisItem::whereARedeterminar(1)
                                      ->orderBy('analisis_precios_id')
                                      ->get();
      }

      // Solo tomo Items hoja
      foreach ($analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
        if($valueAnalisisItem->item->is_nodo)
          $analisis_items->forget($valueAnalisisItem);
      }

      $this->toLog('Cantidad de Saltos: ' . sizeof($analisis_items));

      $contratosMonedas = collect();
      foreach ($analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
        $this->toLog('Cálculo de Análisis de Item: id=' . $valueAnalisisItem->id);
        $param = array();
        $param['analisis_item'] = $valueAnalisisItem;
        $param['item'] = $valueAnalisisItem->item;
        $param['contrato_moneda'] = $valueAnalisisItem->analisis_precios->itemizado->contrato_moneda;

        $contratosMonedas->push($param['contrato_moneda']);

        $param['saltos'] = $param['contrato_moneda']->saltos->filter(function($salto) {
                                                              return $salto->calculado;
                                                            });

        $param['ultimo_salto'] = $param['saltos']->sortByDesc('nro_salto')->first();

        // Transaction
        $transaction = DB::transaction(function () use ($param) {
          $all_indices = $param['analisis_item']->all_indices;

          $fecha_oferta = DateTime::createFromFormat('d/m/Y', $param['ultimo_salto']->contrato->fecha_oferta);
          $publicacion_inicial = PublicacionIndice::whereMes($fecha_oferta->format('m'))
                                                  ->whereAnio($fecha_oferta->format('Y'))
                                                  ->first();

          $publicacion_final = $param['ultimo_salto']->publicacion;

          foreach ($all_indices as $keyIndice => $valueIndice) {
            $valor_inicial = ValorIndicePublicado::wherePublicacionId($publicacion_inicial->id)
                                                 ->whereTablaIndicesId($valueIndice->id)
                                                 ->first()
                                                 ->valor;

            $valor_final = ValorIndicePublicado::wherePublicacionId($publicacion_final->id)
                                               ->whereTablaIndicesId($valueIndice->id)
                                               ->first()
                                               ->valor;

            if($valor_inicial == 0)
              $vrs_indices[$valueIndice->id] = 1;
            else
              $vrs_indices[$valueIndice->id] = $valor_final / $valor_inicial;
          }
          // FIN Calculo vr de cada indice, sin importar el componente al que fue asociado

          // Calculo el VR de equipos
          $total_inicial = 0;
          $total_final = 0;

          foreach ($param['analisis_item']->componentes_equipo as $keyComponente => $valueComponente) {
            // El costo anterior es el original del componente
            $costo_total_anterior = $valueComponente->costo_total_adaptado;

            $total_inicial = $total_inicial + $costo_total_anterior;
            $total_final = $total_final + $costo_total_anterior * $vrs_indices[$valueComponente->indice_id];
          }

          if($total_inicial == 0) {
            $vr_componentes_equipo = 1;
          } else {
            $vr_componentes_equipo = $total_final / $total_inicial;
          }

          // Medicion 100 Es nuevo y los valores "certificados" son 0
          if($param['analisis_item']->item->is_ajuste_alzado) {
            $medicion = 100;
          } elseif($param['analisis_item']->item->is_unidad_medida) {
            $medicion = $param['analisis_item']->item->cantidad;
          }

          $cuadro_comparativo_id = $param['ultimo_salto']->cuadro_comparativo->id;

          // Creo ItemCuadroComparativo, agrego VR de equipos y tomo el id
          $item_cuadro_comp = ItemCuadroComparativo::create(['item_id'                => $param['analisis_item']->item_id,
                                                             'cuadro_comparativo_id'  => $cuadro_comparativo_id,
                                                             'medicion_cronograma'    => $medicion
                                                           ]);

          $item_cuadro_comp->oculto = 1;
          $item_cuadro_comp->aplicar_penalidad_45_dias;
          $item_cuadro_comp->aplicar_penalidad_desvio;
          $item_cuadro_comp->vr_equipos = $vr_componentes_equipo;
          $item_cuadro_comp->save();

          $item_cuadro_comp_id = $item_cuadro_comp->id;
          // FIN Creo ItemCuadroComparativo, agrego VR de equipos y tomo el id

          foreach ($param['analisis_item']->categorias as $keyCategoria => $valueCategoria) {
            $valueCategoria = $valueCategoria->thisWithClase();
            $categoria_cuadro = CategoriaCuadroComparativo::create(['item_cuadro_id'  => $item_cuadro_comp_id,
                                                                    'categoria_id'    => $valueCategoria->id
                                                                  ]);
            $categoria_cuadro_id = $categoria_cuadro->id;
            if($valueCategoria->tiene_componentes) {
              foreach ($valueCategoria->componentes as $keyComponente => $valueComponente) {
                if($valueComponente->indice == null) {
                  $vr = $vr_componentes_equipo;
                } else {
                  $vr = $vrs_indices[$valueComponente->indice_id];
                }

                $costo_total_anterior = $valueComponente->costo_total_adaptado;

                $vr_por_costo = $vr * $costo_total_anterior;

                $this->toLog('Componente "' . $valueComponente->nombre . '" con costo_total_adaptado de: ' . $costo_total_anterior . ' y vr: ' . $vr . ' a ' . $vr_por_costo);
                // Creo ComponenteCuadroComparativo
                $componente_cuadro = ComponenteCuadroComparativo::create(['componente_id'   => $valueComponente->id,
                                                                          'categoria_id'    => $categoria_cuadro_id,
                                                                          'vr'              => $vr,
                                                                          'vr_por_costo'    => number_format((float)$vr_por_costo, 8, '.', ''),
                                                                          'costo_anterior'  => number_format((float)$costo_total_anterior, 2, '.', ''),
                                                                        ]);
                // FIN Creo ComponenteCuadroComparativo
              }
            }
          }

          $categorias_hoja = $item_cuadro_comp->categorias->filter(function($categoria) {
            return $categoria->tiene_componentes;
          });

          foreach($categorias_hoja as $keyCategoriaCuadro => $valueCategoriaCuadro) {
            $valueCategoriaCuadro->calcularTotal();
          }

          $param['analisis_item']->a_redeterminar = 0;
          $param['analisis_item']->save();

          $item_cuadro_comp->calcularTotal();

          $this->toLog('VR Equipos: ' . $vr_componentes_equipo);
        });
        // FIN Transaction

        foreach ($contratosMonedas as $keyContratoMoneda => $valueContratoMoneda) {
          $valueContratoMoneda->reCalculoMontoYSaldo();
        }

      }

      $this->toLog('FIN CalculoItemAdenda');
    }

  }
