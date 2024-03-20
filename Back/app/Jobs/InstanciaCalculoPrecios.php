<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use SolicitudRedeterminacion\SolicitudRedeterminacion;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use YacyretaJobs\BaseJob;

class InstanciaCalculoPrecios extends BaseJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id_solicitud;

    /**
     * Create a new job instance.
     * @param  int   $id_solicitud
     * @return void
    */
    public function __construct($id_solicitud = null) {
      $this->id_solicitud = $id_solicitud;
    }

    public function handle() {
      $relanzar = false;
      $this->toLog('Inicio InstanciaCalculoPrecios');

      if($this->id_solicitud != null) {
        $solicitudes_calculables = SolicitudRedeterminacion::whereId($this->id_solicitud)
                                                           ->whereFinalizada(0);
      } else {
        $solicitudes_calculables = SolicitudRedeterminacion::whereFinalizada(0);
      }

      $solicitudes_calculables = $solicitudes_calculables->orderBy('contrato_id')
                                                         ->orderBy('salto_id')
                                                         ->get();

      $solicitudes_calculables = $solicitudes_calculables->filter(function($solicitud) {
              if($solicitud->solicitud_anterior != null && $solicitud->solicitud_anterior->monto_vigente == null)
                return false;
              return $solicitud->instancia_actual->tipo_instancia->modelo == "CalculoPreciosRedeterminados"
                     && $solicitud->salto->calculado;
            });

      $this->toLog('Cantidad de Solicitudes: ' . sizeof($solicitudes_calculables));

      foreach ($solicitudes_calculables as $keySolicitud => $solicitud) {
        $certificado = $solicitud->certificado;

        $aplica_penalidad_45_dias = !$solicitud->a_termino;
        $certificado_anterior = null;
        if($aplica_penalidad_45_dias) {
          $certificado_anterior = $solicitud->certificado_anterior;
        }

        if($aplica_penalidad_45_dias && $certificado_anterior == null) {
          $mes_ultimo_certificado = $solicitud->contrato->mes_ultimo_certificado;
          if(isset($mes_ultimo_certificado['mes']) && $mes_ultimo_certificado['mes'] == $solicitud->contrato->meses_cronograma) {

            $ultimo_certificado = $solicitud->contrato->certificados_basicos()
                                            ->whereMes($mes_ultimo_certificado['mes'])
                                            ->first();
            $ultimo_certificado->avanceAcumuladoPorMoneda($solicitud->contrato_moneda->id) == 100;

            $certificado_anterior = $ultimo_certificado;
          }
        }

        if($aplica_penalidad_45_dias && $certificado_anterior == null) {
          $this->toLog('La Solicitud id=' . $solicitud->id . ' no fue pedida a término y no tiene el certificado anterior a la Fecha de Solicitud', 'error');
        } else {
          if($certificado_anterior != null)
          $this->toLog('La Solicitud id=' . $solicitud->id . ' no fue pedida a término y se usa el certificado anterior id=' . $certificado_anterior->id);
          if($solicitud->contrato_moneda->lleva_analisis) {
            $lleva_analisis = true;
            $this->toLog('Cálculo de Precios con Análisis de Precios de Solicitud: id=' . $solicitud->id);
          } else {
            $lleva_analisis = false;
            $this->toLog('Cálculo de Precios sin Análisis de Precios de Solicitud: id=' . $solicitud->id);
          }

          $id_solicitud = $solicitud->id;
          $solicitud = SolicitudRedeterminacion::find($id_solicitud);
          $cuadro_comparativo = $solicitud->salto->cuadro_comparativo;
          if($cuadro_comparativo->solicitud_id == null) {
            $cuadro_comparativo->solicitud_id = $id_solicitud;
            $cuadro_comparativo->save();
          }

          $instancia = app('SolicitudesRedeterminacionController')->getInstancia('CalculoPreciosRedeterminados', $id_solicitud, false);

          $tipo_instancia_id = TipoInstanciaRedet::whereModelo('VerificacionDesvio')->first()->id;
          $instancia_desvio = $solicitud->instancias()->whereTipoInstanciaId($tipo_instancia_id)->first();
          $aplica_penalidad_desvio = false;

          if($instancia_desvio != null && $instancia_desvio->instancia->aplicar_penalidad_desvio) {
            $this->toLog('Aplica Desvío');
            $aplica_penalidad_desvio = true;
            $cuadro_comparativo->aplicar_penalidad_desvio = 1;
          }

          if($cuadro_comparativo != null) {
            foreach ($cuadro_comparativo->items_cuadro as $keyItem => $valueItem) {
              if($aplica_penalidad_desvio)
                $valueItem->aplicar_penalidad_desvio = 1;
              else
                $valueItem->aplicar_penalidad_desvio = 0;
              if($certificado == null) {
                // 100 % ó Cantidad Total
                $valueItem->medicion_certificado = $valueItem->medicion_cronograma;
              }

              if($certificado == null && !$aplica_penalidad_45_dias) {
                $valueItem->medicion_utilizada  = $valueItem->medicion_cronograma;
              } else {
                // if(!$aplica_penalidad_45_dias && !$aplica_penalidad_desvio) {
                if($certificado != null) {
                  $items_cert = $certificado->getItemCertificado($valueItem->item_id);
                  $acumulado_anterior = $items_cert->sum('acumulado_anterior');
                  if($valueItem->item->is_ajuste_alzado) {
                    $porcentaje = $items_cert->sum('cantidad');
                    $total_certificado = $acumulado_anterior + $porcentaje;
                    $valueItem->medicion_certificado = 100 - $total_certificado;
                  } else {
                    $cantidad = $items_cert->sum('cantidad');
                    $total_certificado = $acumulado_anterior + $cantidad;
                    $valueItem->medicion_certificado = $valueItem->item->cantidad - $total_certificado;
                  }
                  $valueItem->medicion_utilizada = $valueItem->medicion_certificado;
                }
                // } elseif($aplica_penalidad_45_dias) {

                if($aplica_penalidad_45_dias) {
                  if($certificado_anterior == null) {
                    $this->toLog('No existe el Certificado Anterior id=' . $certificado_anterior->id, 'error');
                  } else {
                    $items_cert = $certificado_anterior->getItemCertificado($valueItem->item_id);
                    $acumulado_anterior = $items_cert->sum('acumulado_anterior');
                    if($valueItem->item->is_ajuste_alzado) {
                      $porcentaje = $items_cert->sum('cantidad');
                      $total_certificado = $acumulado_anterior + $porcentaje;
                      $valueItem->medicion_45_dias = 100 - $total_certificado;
                    } else {
                      $cantidad = $items_cert->sum('cantidad');
                      $total_certificado = $acumulado_anterior + $cantidad;
                      $valueItem->medicion_45_dias = $valueItem->item->cantidad - $total_certificado;
                    }
                  }

                  $valueItem->aplicar_penalidad_45_dias = 1;
                  $valueItem->medicion_utilizada = $valueItem->medicion_45_dias;

                  $cuadro_comparativo->aplicar_penalidad_45_dias = 1;
                  $cuadro_comparativo->save();
                } else {
                  if(!$aplica_penalidad_desvio) {
                    $valueItem->medicion_utilizada = $valueItem->medicion_certificado;
                  } else {
                    $valueItem->aplicar_penalidad_desvio = 1;
                    $valueItem->medicion_utilizada = $valueItem->medicion_cronograma;

                    $cuadro_comparativo->aplicar_penalidad_desvio = 1;
                    $cuadro_comparativo->save();
                  }
                }
              }
              $valueItem->save();

              $this->toLog('Cálculo Total de Item: id=' . $valueItem->id);
              if($lleva_analisis)
                $valueItem->calcularTotal();
              else
                $valueItem->calcularTotalSinAnalisis();
            }
            $this->toLog('Cálculo Total de Cuadro Comparativo: id=' . $cuadro_comparativo->id);
            $cuadro_comparativo->calcularTotal();

            $calculosAutomaticos = $instancia->calculosAutomaticos();
            if(!$calculosAutomaticos) {
              $this->toLog('No se pudieron calcular los precios de Solicitud id=' . $solicitud->id, 'error');
            }

            $this->toLog('Pasar Instancia de Solicitud id=' . $solicitud->id);
            $solicitud->pasarInstancia();

            if(!$lleva_analisis) {
              $instancia = app('SolicitudesRedeterminacionController')->getInstancia('EmisionCertificadoRDP', $solicitud->id, false);

              app('SolicitudesRedeterminacionController')->createRedeterminacion($solicitud);
              $redeterminacion = $solicitud->redeterminacion;
              $solicitud = $instancia->instancia->solicitud;

              app('SolicitudesRedeterminacionController')->createCertificadosRedeterminados($solicitud, $redeterminacion);

              if(!count($redeterminacion->certificados) > 0) {
                $instancia->certificados_emitidos = 0;
              } else {
                $instancia->certificados_emitidos = 1;
                $instancia->save();
              }

              // recalculo saldo y vigente
              $solicitud->contrato->reCalculoMontoYSaldo($solicitud->contrato_moneda->id);
              $solicitud->finalizada = 1;

              $instancia->save();
              $solicitud->save();
            }

            $relanzar = true;
          } else {
            $this->toLog('No existe el Cuadro Comparativo de Solicitud id=' . $solicitud->id . ' y Salto id=' . $solicitud->salto->id, 'error');
          }
        }
      }

      if($relanzar) {
        $this->toLog('InstanciaCalculoPrecios Re-encolado');
        dispatch((new InstanciaCalculoPrecios($solicitud->id))->onQueue('calculos_variacion'));
      }

      $this->toLog('FIN InstanciaCalculoPrecios');
    }

  }
