<?php

namespace App\Jobs;

use Auth;
use CalculoRedeterminacion\VariacionIndicePolinomica;
use CalculoRedeterminacion\VariacionMesPolinomica;
use Contrato\Contrato;
use Contrato\TipoContrato;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Indice\PublicacionIndice;
use Indice\ValorIndicePublicado;
use Log;
use YacyretaJobs\BaseJob;

class CalculoVariacionEnPublicacion extends BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contrato_id;

    /**
     * Create a new job instance.
     * @param  int  $contrato_id
     * @return void
     */
    public function __construct($contrato_id = null)
    {
        $this->contrato_id = $contrato_id;
    }

    public function handle()
    {
        $this->toLog('Inicio CalculoVariacionEnPublicacion');
        $tipos_contratos_id = TipoContrato::where('nombre', 'contrato')
            ->orWhere('nombre', 'adenda_certificacion')
            ->pluck('id', 'id');

        $contratos_calculables = Contrato::whereIn('tipo_id', $tipos_contratos_id)
            ->whereBorrador(0)->whereNoRedetermina(0);

        if ($this->contrato_id != null) {
            $contratos_calculables = $contratos_calculables->whereId($this->contrato_id);
        }

        $contratos_calculables = $contratos_calculables->get()->filter(function ($contrato) {
            return !$contrato->incompleto['status']
                && !$contrato->falta_completar_empalme;
        });

        $this->toLog('Cantidad: '.sizeof($contratos_calculables));

        foreach ($contratos_calculables as $keyContrato => $contrato) {
            $this->toLog('Cálculo de variación de contrato: id='.$contrato->id);

            //Fecha que se usara para saber si la obra inicio
            $fecha_acta_inicio = DateTime::createFromFormat('d/m/Y', $contrato->fecha_acta_inicio);
            
            // Publicaciones desde la fecha_oferta
            $publicacion_indice = new PublicacionIndice();
            try {
                $publicaciones = $publicacion_indice->desdeFecha($contrato->fecha_oferta);
            } catch (Exception $e) {
                return $this->createException($e, ' Contrato '.$contrato->expediente.' sin fecha_oferta');
            }

            foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
                if ($valueContratoMoneda->fecha_ultima_redeterminacion != null) {
                    $publicaciones = $publicacion_indice->desdeFecha($valueContratoMoneda->fecha_ultima_redeterminacion);
                    $publicacionesContratoMoneda = \Illuminate\Database\Eloquent\Collection::make();

                    foreach ($publicaciones as $publicacion) {
                        if($publicacion->moneda_id === $valueContratoMoneda->moneda_id && $publicacion->publicado)
                            $publicacionesContratoMoneda->add($publicacion);
                    }

                    $ultima_redeterminacion_empalme = $valueContratoMoneda->ultima_redeterminacion_empalme;
                }
                else {
                    $publicacionesContratoMoneda = \Illuminate\Database\Eloquent\Collection::make();
                    foreach ($publicaciones as $publicacion) {
                        if($publicacion->moneda_id === $valueContratoMoneda->moneda_id && $publicacion->publicado)
                            $publicacionesContratoMoneda->add($publicacion);
                    }
                    $ultima_redeterminacion_empalme = null;
                }

                $moneda = $valueContratoMoneda->moneda;
                $this->toLog('  Moneda: '.$moneda->nombre_simbolo);
                $this->toLog('  Fecha de Oferta: '.$contrato->fecha_oferta);

                $porcentaje_salto = ($moneda->porcentaje_salto / 100) + 1;

                $polinomica = $valueContratoMoneda->polinomica;

                
                if ($polinomica != null) {
                    $salto_old = VariacionMesPolinomica::wherePolinomicaId($polinomica->id)
                        ->whereContratoMonedaId($valueContratoMoneda->id)
                        ->where('nro_salto', '!=', null)
                        ->get();

                    if (count($salto_old) > 0) {
                        // Si hubo un salto previo parto de ese numero de salto
                        $nro_salto = VariacionMesPolinomica::wherePolinomicaId($polinomica->id)
                            ->whereContratoMonedaId($valueContratoMoneda->id)
                            ->where('nro_salto', '!=', null)
                            ->max('nro_salto');
                    }
                    elseif ($ultima_redeterminacion_empalme != null) {
                        // Si es de empalme y no hubo salto, parto del numero de salto de la redeterminacion de empalme
                        $nro_salto = $ultima_redeterminacion_empalme->nro_salto;
                    } else {
                        $nro_salto = 0;
                    }
                    $this->toLog('  Nro. de Salto: '.$nro_salto);

                    $composiciones = $polinomica->composiciones;

                    // Flag para ver si es la primera vez, en ese caso se usa la primera publicacion
                    $inicio = true;

                    // Filtro: si las variaciones fueron calculadas tomo a partir de ese momento
                    if ($valueContratoMoneda->ultima_variacion != null) {
                        $ultima_pub_variacion = $valueContratoMoneda->ultima_variacion->publicacion;
                        $ultima_pub_variacion_id = $ultima_pub_variacion->id;
                        $publicacionesContratoMoneda = $publicacionesContratoMoneda->filter(function ($publicacion) use
                        (
                            $ultima_pub_variacion_id
                        ) {
                            if ($publicacion->id > $ultima_pub_variacion_id) {
                                return true;
                            }
                        });
                    }

                    foreach ($publicacionesContratoMoneda as $keyPublicacion => $valuePublicacion) {
                        $this->toLog('  Publicación: '.$valuePublicacion->mes_anio);
                        if ($valuePublicacion->publicado) {
                            // Tomo la primera para comparar
                            if ($inicio) {
                                // Si ya fue calculada uso la ultima vez para calcular nuevas variaciones
                                if ($valueContratoMoneda->ultima_variacion != null) {
                                    if ($valueContratoMoneda->ultimo_salto != null) {
                                        $primera_pub = $valueContratoMoneda->ultimo_salto->publicacion;
                                    } else {
                                        $primera_pub = $valuePublicacion->publicacion_anterior;
                                    }
                                } else {
                                    // De piso tomo la anterior
                                    $primera_pub = $valuePublicacion->publicacion_anterior;
                                }

                                // Lo que valia en el salto/inicio
                                $valores_pub_inicio = array();

                                foreach ($composiciones as $keyComp => $valueComp) {
                                    $valores_temp = ValorIndicePublicado::wherePublicacionId($primera_pub->id)
                                        ->whereTablaIndicesId($valueComp->tabla_indices_id)
                                        ->first();
                                    if ($valores_temp != null) {
                                        $valores_pub_inicio[$valueComp->id] = (float) $valores_temp->valor;
                                    } else {
                                        $valores_pub_inicio[$valueComp->id] = 0;
                                    }

                                }

                                $inicio = false;
                            }

                            $valores_hoy = array();
                            foreach ($composiciones as $keyComp => $valueComp) {
                                $valores_temp = ValorIndicePublicado::wherePublicacionId($valuePublicacion->id)
                                    ->whereTablaIndicesId($valueComp->tabla_indices_id)
                                    ->first();

                                if ($valores_temp != null) {
                                    $valores_hoy[$valueComp->id] = (float) $valores_temp->valor;
                                }
                                else {
                                    $valores_hoy[$valueComp->id] = 0;
                                }
                            }

                            $variacion_desde = 0;

                            foreach ($composiciones as $keyComp => $valueComp) {
                                $variacion_indice = empty($valores_pub_inicio[$valueComp->id])
                                    ? 1
                                    : ($valores_hoy[$valueComp->id] / $valores_pub_inicio[$valueComp->id]);

                                $variacion_desde = $variacion_desde + $variacion_indice * (float) $valueComp->porcentaje;

                                VariacionIndicePolinomica::whereComposicionId($valueComp->id)
                                    ->wherePublicacionId($valuePublicacion->id)
                                    ->delete();

                                $valor_indice_pol = new VariacionIndicePolinomica();
                                $valor_indice_pol->composicion_id = $valueComp->id;
                                $valor_indice_pol->publicacion_id = $valuePublicacion->id;
                                $valor_indice_pol->variacion = $variacion_indice;
                                $valor_indice_pol->save();
                            }

                            $variacion_mes_old = VariacionMesPolinomica::wherePolinomicaId($polinomica->id)
                                ->wherePublicacionId($valuePublicacion->id)
                                ->whereContratoMonedaId($valueContratoMoneda->id)
                                ->get();
                            if (sizeof($variacion_mes_old) > 0) {
                                foreach ($variacion_mes_old as $keyVarMesOld => $valueVarMesOld) {
                                    $valueVarMesOld->delete();
                                }
                            }

                            $fecha_publicacion = DateTime::createFromFormat('d/m/Y', '01/'.$valuePublicacion->mes_anio);
                             

                            //Si la variacion supera el porcentaje de salto y la fecha de la publicacion es mayor a la fecha de inicio --> ES SALTO
                            //Es decir que la obra inició o comienza durante ese mismo mes
                            if ($variacion_desde < $porcentaje_salto ||
                               ($fecha_publicacion < $fecha_acta_inicio && !($fecha_publicacion->format('m/Y') == $fecha_acta_inicio->format('m/Y')))
                            ) {                                
                                $variacion = VariacionMesPolinomica::create([
                                    'polinomica_id' => $polinomica->id,
                                    'publicacion_id' => $valuePublicacion->id,
                                    'contrato_moneda_id' => $valueContratoMoneda->id,
                                    'variacion' => $variacion_desde,
                                    'es_salto' => 0
                                ]);
                            }
                            else {
                                $variacion = VariacionMesPolinomica::create([
                                    'polinomica_id' => $polinomica->id,
                                    'publicacion_id' => $valuePublicacion->id,
                                    'contrato_moneda_id' => $valueContratoMoneda->id,
                                    'variacion' => $variacion_desde,
                                    'es_salto' => 1
                                ]);

                                $nro_salto = $nro_salto + 1;
                                $variacion->nro_salto = $nro_salto;

                                $variacion->save();

                                $fecha_publicacion = $this->datafechaDeA('01/'.$valuePublicacion->mes_anio, 'd/m/Y',
                                    'Y-m-d');

                                if ($valueContratoMoneda->fecha_ultima_redeterminacion == null) {
                                    $fecha_ultima_redeterminacion = null;
                                } else {
                                    $fecha_ultima_redeterminacion = $this->datafechaDeA($valueContratoMoneda->fecha_ultima_redeterminacion,
                                        'd/m/Y', 'Y-m-d');
                                }

                                if ($fecha_publicacion <= $fecha_ultima_redeterminacion
                                    // || $fecha_ultima_redeterminacion == null
                                ) {
                                    $variacion->solicitado = 1;
                                    // $variacion->redeterminacion_id = $solicitud->id;
                                    $variacion->save();
                                }

                                // Recalculo variacion al salto
                                $primera_pub = $valuePublicacion;
                                $valores_pub_inicio = array();

                                foreach ($composiciones as $keyComp => $valueComp) {

                                    $valores_temp = ValorIndicePublicado::wherePublicacionId($primera_pub->id)
                                        ->whereTablaIndicesId($valueComp->tabla_indices_id)
                                        ->first();
                                    if ($valores_temp != null) {
                                        $valores_pub_inicio[$valueComp->id] = (float) $valores_temp->valor;
                                    } else {
                                        $valores_pub_inicio[$valueComp->id] = 0;
                                    }
                                }

                                $variacion->save();
                                $valueContratoMoneda->ultimo_salto_id = $variacion->id;

                                $valueContratoMoneda->ultimo_salto_id = $variacion->id;

                                // Notifico a todos los usuarios que tengan el contrato asociado
                                $contrato->notifySaltoDe($variacion->id);

                                // if($contrato->normativa->banco)
                                // $this->solicitarAutomaticamente($contrato);
                            }

                            $valueContratoMoneda->ultima_variacion_id = $variacion->id;
                            $valueContratoMoneda->save();

                        }
                    }
                }
            }
        }
        if (sizeof($contratos_calculables) > 0) {
            dispatch((new CalculoPrecios())->onQueue('calculos_variacion'));
        }

        $this->toLog('FIN CalculoVariacionEnPublicacion');
    }

    /**
     * @param  date  $fecha
     * @param  string  $formato_original
     * @param  string  $formato_nuevo
     */
    public function datafechaDeA($fecha, $formato_original, $formato_nuevo)
    {
        return DateTime::createFromFormat($formato_original, $fecha)->format($formato_nuevo);
    }

    /**
     * @param  Contrato\Contrato  $contrato
     */
    // public function solicitarAutomaticamente($contrato) {
    //   $this->toLog('solicita Automáticamente: id=' . $contrato->id);
    //
    //   foreach($contrato->obras as $keyContratoMoneda => $valueContratoMoneda) {
    //     foreach($valueContratoMoneda->saltos_redeterminables as $keySalto => $valueSalto) {
    //       $publicacion = $valueSalto->publicacion;
    //
    //       try {
    //         $solicitud = SolicitudRedeterminacion::create([
    //           'contrato_id'             => $contrato_id,
    //           'salto_id'                => $valueSalto->id,
    //           'user_contratista_id'     => null,
    //           'user_modifier_id'        => 1,
    //           'a_termino'               => true,
    //         ]);
    //
    //   ///////////// Creacion de Instancias /////////////
    //         $tipos_instancia = TipoInstanciaRedet::where('cambia_estado', '=', 1)->get()->filter(function($ti) {
    //           return $ti->banco;
    //         });
    //
    //         $orden = 0;
    //         foreach ($tipos_instancia as $keySeccion => $valueTipoInstancia) {
    //           $orden++;
    //           try {
    //             $instancia = Instancia::create([
    //                 'solicitud_id'          => $solicitud->id,
    //                 'tipo_instancia_id'     => $valueTipoInstancia->id,
    //                 'orden'                 => $orden
    //             ]);
    //
    //             if($valueTipoInstancia->modelo == 'CalculoPreciosRedeterminados') {
    //               $instancia_actual_id = $instancia->id;
    //               $instancia->fecha_inicio = date("Y-m-d H:i:s");
    //               $instancia->save();
    //             }
    //
    //             $modelo = "SolicitudRedeterminacion\Instancia\\$valueTipoInstancia->modelo";
    //             $instancia_model = $modelo::create([
    //                 'instancia_id'      => $instancia->id,
    //                 'user_creator_id'   => 1
    //             ]);
    //
    //           } catch (QueryException $e) {
    //             return false;
    //           }
    //         }
    //
    //         $solicitud->instancia_actual_id = $instancia_actual_id;
    //         $solicitud->ultimo_movimiento =  date("Y-m-d H:i:s");
    //         $solicitud->save();
    //
    //   ///////////// FIN Creacion de Instancias /////////////
    //
    //         $valueSalto->solicitado = 1;
    //         $valueSalto->redeterminacion_id = $solicitud->id;
    //         $valueSalto->save();
    //       } catch (QueryException $e) {
    //         return false;
    //       }
    //
    //       event(new NewSolicitudRedeterminacion($solicitud));
    //
    //       $tipo_instancia = TipoInstanciaRedet::whereModelo('Iniciada')->first();
    //       event(new PasosSolicitudRedeterminacion($solicitud, $tipo_instancia, "false"));
    //     }
    //
    //     $valueContratoMoneda->save();
    //
    //   }
    //
    //   try {
    //     $contrato->ultima_solicitud = date("Y-m-d H:i:s");
    //     $contrato->save();
    //   } catch (QueryException $e) {
    //     return false;
    //   }
    //
    //   return true;
    // }
}
