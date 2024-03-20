<?php

namespace App\Http\Controllers\Contratos;

use App\User;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use DB;
use Hash;
use Log;
use Redirect;
use Response;
use Storage;
use View;

use Contrato\Contrato;
use Contrato\ContratoMoneda\ContratoMoneda;
use SolicitudContrato\EstadoSolicitudContrato;
use SolicitudContrato\InstanciaSolicitudContrato;
use SolicitudContrato\SolicitudContrato;
use SolicitudContrato\UserContrato;
use SolicitudContrato\Poder;

use CalculoRedeterminacion\VariacionMesPolinomica;
use CalculoRedeterminacion\CalculoModelExtended;

use Contrato\Ampliacion\Ampliacion;
use Contrato\Ampliacion\TipoAmpliacion;

use Cronograma\Cronograma;
use Cronograma\ItemCronograma;

use Itemizado\Itemizado;
use Itemizado\Item;

use Contratista\Contratista;
use Itemizado\UnidadMedida;

use Indice\IndiceTabla1;
use Contrato\InstanciaContrato;

use App\Http\Controllers\Contratos\ContratosControllerExtended;
class ContratosController extends ContratosControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contratos');
      $this->middleware('auth', ['except' => 'logout']);
    }

    ////////////////// Contratos //////////////////
    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function misContratos(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $user_contratos = Auth::user()->contratos;
      $user_contratos = $this->ordenar($user_contratos);

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $user_contratos = $this->filtrar($user_contratos, $search_input);
        }
        $contratos = $this->paginateCustom($user_contratos);
      } else {
        $user_contratos = $this->filtrar($user_contratos, $search_input);
        $contratos = $this->paginateCustom($user_contratos, 1);
      }

      return view('contratos.contratos.index', compact('user_contratos', 'publicados', 'search_input'));
    }

    /**
     * @param  SolicitudContrato\UserContrato $user_contratos
    */
    private function ordenar($user_contratos) {
      $user_contratos = $user_contratos->groupBy(function($user_contrato, $key) {
        if($user_contrato->contrato->ultimo_salto == null)
          return null;
        else
          return $user_contrato->contrato->ultimo_salto->publicacion_id;
      });

      $toArray = array();
      foreach ($user_contratos as $keyUserContrato => $valueUserContrato) {
        $toArray[$keyUserContrato] = $valueUserContrato->sortBy(function($user_contrato, $key) {
          return $user_contrato->expediente_madre;
        })->all();
      }

      krsort($toArray);
      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $ordered->push($value);
        }
      }
      return $ordered;
    }

    /**
     * @param  Illuminate\Database\Eloquent\Collection $user_contratos
     * @param  string $input_lower
    */
    private function filtrar($user_contratos, $input_lower) {
      if($input_lower == '')
        return $user_contratos;

      return $user_contratos->filter(function($user_contrato) use($input_lower) {
        return
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->numero_contrato), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->numero_contratacion), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->expediente_madre), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->resoluc_adjudic), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->denominacion), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->ultima_solicitud), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($user_contrato->contrato->estado_nombre_color['nombre']), $input_lower) > 0;
      });

      }

    /**
     * @param  int $id
     * @param  string $accion | nullable
    */
    public function verEditar($id, $accion = null) {
      return $this->verContrato($id, $accion);
    }

    /**
     * @param  int $id
    */
    public function verContrato($id, $accion = null) {
      $user_contratista_id = Auth::user()->user_contratista_id;
      $user_contrato = UserContrato::whereContratoId($id)->whereUserContratistaId($user_contratista_id)->first();

      if($user_contrato == null) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      $contrato = $user_contrato->contrato;

      $contrato_incompleto = $contrato->incompleto;
      if($contrato->incompleto['status']) {
        if($contrato_incompleto['polinomica'] || $contrato_incompleto['itemizado']) {
          $ids = ContratoMoneda::select('moneda_id')
                               ->whereClaseId($id)
                               ->whereClaseType($contrato->getClassName())
                               ->get()->pluck('moneda_id', 'moneda_id')->toArray();

          $indices = IndiceTabla1::whereIn('moneda_id', $ids)->get()->groupBy('moneda_id');
          if($contrato_incompleto['itemizado']) {
            $unidadesMedida = UnidadMedida::getOpciones();
            $responsables = Contratista::getOpciones();
          }
        }
      }

      $opciones['version'] = 'vigente';
      $opciones['visualizacion'] = 'porcentaje';

      return view('contratos.contratos.show.index', compact('contrato', 'accion', 'opciones', 'contrato_incompleto', 'user_contrato'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportarContratos(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $user_contratos =  Auth::user()->contratos;

      $user_contratos = $this->ordenar($user_contratos);

      $user_contratos = $user_contratos->map(function ($user_contrato, $key) {
        $arr = array();
        $arr[trans('forms.expedient_ppal_elec')] = $user_contrato->expediente_madre;
        $arr[trans('forms.fecha_licitacion')] = $user_contrato->contrato->fecha_licitacion;
        $arr[trans('forms.descripcion')] = $user_contrato->descripcion;
        //$arr[trans('forms.contratista')] = $user_contrato->contrato->contratista->nombre_cuit;
        $arr[trans('forms.contratista')] = $user_contrato->contrato->contratista->razon_social;
        $vr = '';
        $separador_vr = ' - ';
        foreach($user_contrato->contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
          if($valueContratoMoneda->ultima_variacion != null)
            $vr .=   '' . $valueContratoMoneda->ultima_variacion->variacion_show . $separador_vr;
          else
          $vr .= '1' . $separador_vr;
        }
        if(substr($vr, -3) == $separador_vr)
          $vr = substr($vr, 0, -3);

        $arr[trans('forms.vr')] = $vr;
        $arr[trans('forms.ultimo_salto')] = $user_contrato->contrato->ultimo_salto_my;
        $arr[trans('forms.ultima_solicitud')] = $user_contrato->contrato->ultima_solicitud;

        return $arr;
      });

      return $this->toExcel(trans('index.mis') . trans('index.contratos'),
                            $this->filtrarExportacion($user_contratos, $filtro));
    }
    ////////////////// END Contratos //////////////////

     /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportarCronograma(Request $request) {
      Excel::create(trans('index.cronogramas'), function($excel) use ($request) {
        $input = $request->all();
        $visualizacion = $input['visualizacion'];
        $contrato = Contrato::where('id', $input['excel_input'])->first();

        foreach($contrato->contratos_monedas as $valueContratoMoneda) {
          $excel->sheet($valueContratoMoneda->moneda->nombre_simbolo, function($sheet) use ($valueContratoMoneda, $visualizacion, $input, $contrato) {

            if($contrato->has_itemizado_vigente )
              $itemizado = Itemizado::where('id', $valueContratoMoneda->itemizado_vigente_id)->first();
              $cronograma =  Cronograma::where('itemizado_id', $itemizado->id)->first();

            $items = Item::where('itemizado_id', $itemizado->id)->orderBy('codigo', 'ASC')->get();

            $totales = [trans('forms.total')];

            foreach($items as $k => $item) {

              $arr_excel[$k] = [
                    trans('forms.codigo') => $item->codigo,
                    trans('forms.descripcion') => $item->descripcion,
              ];

              $itemCronogramas = ItemCronograma::where('item_id', $item->id)->get();

              $mes = 0;
              foreach($itemCronogramas as $itemCro) {

                 if($visualizacion == 'porcentaje') {
                    $arr_excel[$k][$itemCro->mes] = $itemCro->porcentaje. ' ' . '%' . '   ';

                    $mes++;
                    if(isset($totales[$mes])) {
                      $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                    } else {
                      $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                    }
                 } elseif($visualizacion == 'moneda') {
                    $arr_excel[$k][$itemCro->mes] = (float) $itemCro->valor. '   ';

                    if($item->is_hoja) {
                      if(isset($totales[$itemCro->mes])) {
                          $totales[$itemCro->mes] += (float) $itemCro->valor;
                      } else {
                          $totales[$itemCro->mes] = (float) $itemCro->valor;
                      }
                    }
                 } elseif($visualizacion == 'all') {
                    if(!$item->is_hoja) {
                       $mes++;
                       $arr_excel[$k][$itemCro->mes] = $cronograma->valorItem($item->id, $visualizacion, $mes) . ' ' . '%' . '   ';
                    } else {
                       $arr_excel[$k][$itemCro->mes] = (float) $itemCro->cantidad . ' ' . $item->unidad_medida_nombre. '   ';
                    }

                    if(isset($totales[$mes])) {
                      $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                    } else {
                      $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                    }
                 }
              }

            }
            array_unshift($totales, ' ');
            $arr_excel[] = $totales;
            $last = count($arr_excel) + 1;

            $sheet->fromArray($arr_excel, null, 'A1', false, true);

            $rows = 1;
            foreach($arr_excel as $item) {
              if(isset($item['Codigo'])) {
                $rows++;
                if(strlen($item['Codigo']) == '4') {
                  $sheet->row($rows, function($row) {
                      $row->setBackground('#b2b2b2'); ;
                  });
                } elseif (strlen($item['Codigo']) == '7') {
                  $sheet->row($rows, function($row) {
                      $row->setBackground('#dddddd'); ;
                  });
                } elseif (strlen($item['Codigo']) == '10') {
                  $sheet->row($rows, function($row) {
                      $row->setBackground('#eeeeee'); ;
                  });
                }
              }
            }

            $sheet->row(1, function($row) {
                $row->setBackground('#808080'); $row->setFontColor('#ffffff'); $row->setFontWeight('bold');
            });
             $sheet->row($last, function($row) {
                $row->setBackground('#808080'); $row->setFontColor('#ffffff'); $row->setFontWeight('bold');
            });
          });
        }
      })->store('xlsx', storage_path('excel/exports'));

      return Response::json(array(
            'href' => '/excel/exports/' . trans('index.cronogramas') . '.xlsx',
      ));
    }

    public function exportarItemizado(Request $request) {
      Excel::create(trans('index.itemizados'), function($excel) use ($request) {
        $input = $request->all();

        $contrato = Contrato::where('id', $input['excel_input'])->first();

        foreach($contrato->contratos_monedas as $valueContratoMoneda) {
          $excel->sheet($valueContratoMoneda->moneda->nombre_simbolo, function($sheet) use ($valueContratoMoneda, $input, $contrato) {

            $itemizado = Itemizado::where('id', $valueContratoMoneda->itemizado_vigente_id)->first();

            $results =  Item::where('itemizado_id', $itemizado->id)->orderBy('codigo', 'ASC')->get();

            $arr_excel =  $results->map(function ($item) {
              $arr = array();

              if($item->categoria_id == 1) {
                $es_ajuste_alzado = true;
              } else {
                $es_ajuste_alzado = false;
              }

              $arr[trans('forms.codigo')] = $item->codigo;
              $arr[trans('forms.descripcion')] = $item->descripcion;

              if(!$es_ajuste_alzado) {
                $arr[trans('forms.cantidad')] = $item->cantidad;
                $arr[trans('forms.unidad_medida_um')] = $item->unidad_medida_nombre;
              } else {
                $arr[trans('forms.cantidad')] = (float) 1;
                $arr[trans('forms.unidad_medida_um')] = trans('forms.ajuste_alzado');
              }

              $arr[trans('forms.montos')] = (int) $item->monto_unitario;
              $arr[trans('forms.total')] = (float) $item->monto_total;

              return $arr;
            });

            $last = count($arr_excel) + 2;
            $sheet->fromArray($arr_excel, null, 'A1', false, true);
            $sheet->appendRow(array(' ',' ', ' ',trans('forms.total'), $itemizado->total));

            $rows = 1;
            foreach($arr_excel as $item) {
              $rows++;
              if(strlen($item['Codigo']) == '4') {
                $sheet->row($rows, function($row) {
                    $row->setBackground('#b2b2b2'); ;
                });
              } elseif (strlen($item['Codigo']) == '7') {
                $sheet->row($rows, function($row) {
                    $row->setBackground('#dddddd'); ;
                });
              } elseif (strlen($item['Codigo']) == '10') {
                $sheet->row($rows, function($row) {
                    $row->setBackground('#eeeeee'); ;
                });
              }

            }
            $sheet->row(1, function($row) {
                $row->setBackground('#808080'); $row->setFontColor('#ffffff'); $row->setFontWeight('bold');
            });
             $sheet->row($last, function($row) {
                $row->setBackground('#808080'); $row->setFontColor('#ffffff'); $row->setFontWeight('bold');
            });
          });
        }
      })->store('xlsx', storage_path('excel/exports'));

      return Response::json(array(
            'href' => '/excel/exports/' . trans('index.itemizados') . '.xlsx',
      ));
    }

    ////////////////// Solicitudes //////////////////
    public function solicitudes(Request $request) {
      $input = $request->all();
      $search_input = '';

      $user_publico = Auth::user()->user_publico;
      $solicitudes_contrato = $user_publico->solicitudes_contrato;

      if($request->getMethod() != "GET") {
        $search_input = $input['search_input'];
        $input_lower = $this->minusculaSinAcentos($input['search_input']);

        if($input_lower != '') {
          $solicitudes_contrato = $solicitudes_contrato->filter(function($solicitud_contrato) use($input_lower) {
            return
              substr_count($this->minusculaSinAcentos($solicitud_contrato->created_at), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->expediente_madre), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->estado_nombre_color['nombre']), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->ultimo_movimiento), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->descripcion), $input_lower) > 0 ;
          });
        }
      }

      $solicitudes_contrato = $this->ordenarSolicitudes($solicitudes_contrato);
      $solicitudes_contrato = $this->paginateCustom($solicitudes_contrato);
      return view('contratos.solicitudes.index', compact('solicitudes_contrato', 'search_input'));
    }

    /**
     * @param  SolicitudContrato\SolicitudContrato $solicitudes_contrato
    */
    private function ordenarSolicitudes($solicitudes_contrato) {
      $solicitudes_contrato = $solicitudes_contrato->groupBy(function($solicitud_contrato, $key) {
        $um = $this->fechaDeA($solicitud_contrato->ultimo_movimiento, 'd/m/Y', 'm/d/Y');
        return strtotime($um);
      });

      $toArray = array();
      foreach ($solicitudes_contrato as $keySolContrato => $valueSolContrato) {
        $toArray[$keySolContrato] = $valueSolContrato->sortBy(function($solicitud_contrato, $key) {
          return $solicitud_contrato->contrato->expediente_ppal;
        })->all();
      }
      krsort($toArray);

      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $ordered->push($value);
        }
      }
      return $ordered;
    }

    public function asociar() {
      $traduccion_ddjj = 'contratos.solicitar';

      return view('contratos.contratos.asociar', compact('traduccion_ddjj'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateAsociar(Request $request) {
      $input = $request->all();
      
      if($input['apoderado_representante'] == 0) {
        $rules = array(
            'descripcion'         => $this->required50(),
            'adjuntar_poder'      => $this->requiredFile(),
            'observaciones'       => $this->max255(),
        );
      } else {
        $rules = array(
            'descripcion'         => $this->required50(),
            'adjuntar_poder'      => $this->requiredFile(),
            'fecha_fin_poder'     => $this->required255(),
            'observaciones'       => $this->max255(),
        );
      }

      $validator = Validator::make($input, $rules, $this->validationErrorMessages());
      $errores = array();
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());

      if(!isset($input['checklist_items']) ||
        (isset($input['checklist_items']) && (sizeof($input['checklist_items']) != sizeof(trans('contratos.asociar_checklist'))))) {
        $errores["checklist_items"] = trans('contratos.error.checklist_items_no_chk');
      }

      if(!isset($input['terminos_y_condiciones'])) {
        $errores["terminos_y_condiciones"] = trans('contratos.error.terminos_y_condiciones');
      }

      if(isset($input['fecha_fin_poder']) && $this->fechaDeA($input['fecha_fin_poder'], 'd/m/Y', 'Y-m-d') < date('Y-m-d') == true) {
        $errores["fecha_fin_poder"] = trans('contratos.error.fecha_fin_poder_menor_hoy');
      }

      if(!$request->hasFile('adjuntar_poder')) {
        if(isset($input['adjuntar_poder'])) {
          $errores["adjuntar_poder"] = 'El máximo permitido es ' . ini_get('post_max_size');
        } else {
          if($input['apoderado_representante'] == 0)
            $errores["adjuntar_poder"] = trans('contratos.error.adjuntar_poder');
          else
            $errores["adjuntar_poder"] = trans('contratos.error.adjuntar_acta_estatuto');
        }
      }

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $user_publico = Auth::user()->user_publico;

      // Solo redeterminan automaticamente los bancos
      $redeterminacion_auto = false;
      $contrato = Contrato::find($input['contrato_id']);
      if($contrato->es_banco)
        $redeterminacion_auto = true;

      // Si tiene otro le pido confirmacion
      $solicitudes_contrato_old = SolicitudContrato::whereUserContratistaId(Auth::user()->user_publico->id)
                                                   ->whereContratoId($input['contrato_id'])->get();
      if($input['confirmado'] == 0) {
        foreach ($solicitudes_contrato_old as $key => $solicitudes_contrato_old) {

          if($solicitudes_contrato_old != null) {
            if($solicitudes_contrato_old->instancia_actual->esta_pendiente) {
              $jsonResponse['status'] = true;
              $jsonResponse['a_confirmar'] = true;
              $jsonResponse['conf_message'] = trans('contratos.confirmacion.esta_pendiente');
              return response()->json($jsonResponse);
            }

            if($solicitudes_contrato_old->instancia_actual->esta_aprobada) {
              $jsonResponse['status'] = true;
              $jsonResponse['a_confirmar'] = true;
              $jsonResponse['conf_message'] = trans('contratos.confirmacion.esta_aprobada');
              return response()->json($jsonResponse);
            }
          }
        }
      }

      try {
        $solicitudes_contrato[] = SolicitudContrato::create([
                                    'contrato_id'           => $input['contrato_id'],
                                    'descripcion'           => $input['descripcion'],
                                    'user_contratista_id'   => $user_publico->id,
                                    'representante'       => $input['apoderado_representante'],
                                    'user_modifier_id'      => Auth::user()->id
                              ]);

      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $poderes = $this->uploadPoderes($request);

      foreach($solicitudes_contrato as $keySolicitud => $valueSolicitud) {
        if(sizeof($poderes) > 0) {
          foreach($poderes as $keyPoder => $valuePoder) {
            $valueSolicitud->poderes()->attach($valuePoder->id);
          }
        }
        $valueSolicitud->observaciones = $input['observaciones'];
        $valueSolicitud->save();
      }

      $id_estado_pendiente = EstadoSolicitudContrato::whereNombre('contratos.estados.solicitud.pendiente')->first()->id;

      foreach($solicitudes_contrato as $keySolicitud => $valueSolicitud) {
        try {
          $instancia_solicitud = InstanciaSolicitudContrato::create([
                                      'solicitud_id'          => $valueSolicitud->id,
                                      'estado_id'             => $id_estado_pendiente,
                                      'user_modifier_id'      => Auth::user()->id
                                ]);

        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          $jsonResponse['status'] = false;
          $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);

        }
        //comentar para evitar el envio de notificaciones
        $valueSolicitud->sendNuevaAsociacionNotification();
      }

      $jsonResponse['status'] = true;
      $jsonResponse['url'] = route('contrato.solicitudes');
      Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.realizada'));
      return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
    */
    public function verSolicitud($id) {
      $solicitud = SolicitudContrato::findOrFail($id);

      return view('contratos.solicitudes.show.index', compact('solicitud'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportarSolicitudes(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $user_publico = Auth::user()->user_publico;
      $solicitudes_contrato = $user_publico->solicitudes_contrato;
      $solicitudes_contrato = $this->ordenarSolicitudes($solicitudes_contrato);

      $solicitudes_contrato = $solicitudes_contrato->map(function ($item, $key) {
          return [
              trans('forms.fecha_solicitud_th')   => $item->created_at,
              trans('forms.expedient_ppal_elec')  => $item->expediente_madre,
              trans('forms.descripcion')          => $item->descripcion,
              trans('forms.estado')               => $item->estado_nombre_color['nombre'],
              trans('forms.ultimo_movimiento_th') => $item->ultimo_movimiento,
          ];
      });

      return $this->toExcel(trans('index.mis') . trans('index.solicitudes_asociacion'),
                            $this->filtrarExportacion($solicitudes_contrato, $filtro));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    private function uploadPoderes($request) {
      if(!$request->hasFile('adjuntar_poder'))
        return array();
      $input = $request->all();
      $user_id = Auth::user()->id;
      $files = $request->file('adjuntar_poder');
      //dd($files);
      foreach ($files as $keyFile => $file) {
        
        $adjuntos_json = $this->uploadFile($request, $user_id, 'adjuntar_poder|'.$keyFile,'poderes');
        $poderes[] = Poder::create([
                      'adjunto'           => $adjuntos_json,
                      'user_modifier_id'  => $user_id,
                      'fecha_fin_poder'   => $input['fecha_fin_poder'],
        ]);
      }
      return $poderes;
    }
    ////////////////// END Solicitudes //////////////////

////////////////////////////////////// END Contrato ///////////////////////////////////

//////////// Ver Adenda ////////////
    public function verAdenda($id, $accion = null) {
      $contrato = Contrato::findOrFail($id);

      if($contrato->is_adenda) {

        $contrato = Contrato::findOrFail($id);

        if(!Auth::user()->puedeVerContrato($contrato)) {
          return redirect()->route('contratos.index');
        }

        $contrato_incompleto = $contrato->incompleto;
        if($contrato->incompleto['status']) {
          if($contrato_incompleto['polinomica'] || $contrato_incompleto['itemizado']) {
            $ids = ContratoMoneda::select('moneda_id')
                                 ->whereClaseId($id)
                                 ->whereClaseType($contrato->getClassName())
                                 ->get()->pluck('moneda_id', 'moneda_id')->toArray();

            $indices = IndiceTabla1::whereIn('moneda_id', $ids)->get()->groupBy('moneda_id');
            if($contrato_incompleto['itemizado']) {
              $unidadesMedida = UnidadMedida::getOpciones();
              $responsables = Contratista::getOpciones();
            }
          }
        }

        $opciones['version'] = 'vigente';
        $opciones['visualizacion'] = 'porcentaje';

        return view('contratos.contratos.show.index', compact('contrato', 'contrato_incompleto', 'accion', 'indices', 'opciones', 'unidadesMedida', 'responsables'));
      } else {
        return redirect()->route('contratos.index');
      }
    }
//////////// FIN Ver Adenda ////////

//////////// Ver Ampliacion ////////////
  /**
   * @param  int $id
   * @param  string $accion  |nullable
  */
  public function verAmpliacion($id, $accion = null) {
    // La variable se llama $contrato en vez de $ampliacion
    // para poder reusar la logica de contratos
    $contrato = Ampliacion::findOrFail($id);

    $contrato_padre = $contrato->contrato;

    $tipo_ampliacion = $contrato->tipo_ampliacion->nombre;

    $contrato_incompleto = $contrato->incompleto;

    $opciones['version'] = 'vigente';
    $opciones['visualizacion'] = 'porcentaje';

    return view('contratos.ampliaciones.show.index', compact('contrato_id', 'contrato', 'contrato_incompleto', 'accion', 'opciones'));
  }
//////////// FIN Ver Ampliacion ////////////



/////////////// Vistas ajax ////////////////
  /**
   * @param  int $id
   * @param  string $seccion
   * @param  string $version  |nullable
   * @param  string $visualizacion  | nullable
  */
  public function getViews($id, $seccion, $version = 'vigente', $visualizacion = 'porcentaje') {
    $contrato = Contrato::findOrFail($id);

    $jsonResponse['highcharts'] = false;
    if($visualizacion == 'curva_inversion')
      $jsonResponse['highcharts'] = $visualizacion;

    $contrato_incompleto = $contrato->incompleto;
    if($contrato_incompleto['status']) {
      if($contrato_incompleto['polinomica'] || $contrato_incompleto['itemizado']) {
        $ids = ContratoMoneda::select('moneda_id')
                             ->whereClaseId($id)
                             ->whereClaseType($contrato->getClassName())
                             ->get()->pluck('moneda_id', 'moneda_id')->toArray();

        $indices = IndiceTabla1::whereIn('moneda_id', $ids)->get()->groupBy('moneda_id');
        if($contrato_incompleto['itemizado']) {
          $unidadesMedida = UnidadMedida::getOpciones();
          $responsables = Contratista::getOpciones();
        }
      }
    }

    // if(!$contrato->completo && !$contrato->incompleto[$seccion] && Auth::user()->can($seccion. '-manage'))
    if(!($contrato->completo || (!$contrato->incompleto[$seccion])))
      $visualizacion = 'all';

    $opciones['version'] = $version;
    $opciones['visualizacion'] = $visualizacion;
    $fromAjax = true;
    $publicados = true;

    $jsonResponse['view'] = View::make("contratos.contratos.show.{$seccion}.index", compact('contrato', 'contrato_incompleto', 'accion', 'indices', 'opciones', 'fromAjax', 'unidadesMedida', 'publicados', 'responsables'))->render();

    $metodo = 'has_' . $seccion . '_vigente';
    if($opciones['version'] == 'vigente' && $contrato->$metodo)
      $id = $contrato->id;
      // $id = $contrato->adenda_vigente_id;

    $jsonResponse['historial'] = route('contrato.historial', ['clase_id' => $id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => $seccion]);

    return response()->json($jsonResponse);
  }

  /**
   * @param  int $id
   * @param  string $seccion
   * @param  string $visualizacion  | nullable
  */
  public function getViewsCronograma($id, $seccion, $visualizacion = 'porcentaje') {

    $contrato = Ampliacion::findOrFail($id);

    $contrato_incompleto = $contrato->incompleto;

    if(!$contrato->completo || (!$contrato->completo && !$contrato->incompleto['$seccion'] ))
      $visualizacion = 'all';

    $opciones['version'] = 'original';
    $opciones['visualizacion'] = $visualizacion;
    $fromAjax = true;

    $jsonResponse['view'] = View::make("contratos.contratos.show.{$seccion}.index", compact('contrato', 'contrato_incompleto', 'accion', 'indices', 'opciones', 'fromAjax', 'unidadesMedida', 'publicados', 'responsables'))->render();

    $metodo = 'has_' . $seccion . '_vigente';
    if($opciones['version'] == 'vigente' && $contrato->$metodo)
      $id = $contrato->id;
      // $id = $contrato->adenda_vigente_id;

    $jsonResponse['historial'] = route('contrato.historial', ['clase_id' => $id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => $seccion]);
    return response()->json($jsonResponse);

  }
//////////// FIN Vistas ajax ////////////



//////////////////  Salto //////////////////
    /**
     * @param  int $id_variacion
    */
    public function verSalto($id_variacion) {
      $salto = VariacionMesPolinomica::findOrFail($id_variacion);

      if(!Auth::user()->user_publico->tieneAsociadoElContrato($salto->contrato_moneda->contrato->id))
        return redirect()->route('contrato.solicitudes');

      $user_contrato = UserContrato::whereUserContratistaId(Auth::user()->user_publico->id)
                                         ->whereContratoId($salto->contrato_moneda->contrato->id)->first();

      return view('contratos.contratos.show.saltos.index', compact('salto', 'user_contrato'));
    }
//////////////////  END Salto //////////////////
  /**
   * @param  int $clase_id
   * @param  string $clase_type
   * @param  string $seccion
   */
  public function historial($clase_id, $clase_type, $seccion) {
    $instancias = InstanciaContrato::whereClaseId($clase_id)
                                   ->whereSeccion($seccion)
                                   ->get()->filter(function($instancia) use ($clase_type) {
                                     return $this->toKey($instancia->clase_type) == $clase_type;
                                   });

    $jsonResponse['view'] = View::make('contratos.contratos.historial', compact('instancias', 'seccion'))->render();
    $jsonResponse['title'] = trans('index.de') . ' ' . trans('contratos.' . $seccion);

    return response()->json($jsonResponse);
  }
///////////// Widgets /////////////
    /**
     * @param  string $widget
     * @param  int $contrato_id
     * @param  string $seccion
    */
    public function widget($widget, $contrato_id, $version) {
      return $this->$widget($contrato_id, $version);
    }
    /**
     * @param  int $contrato_id
     * @param  string $seccion
    */
    public function curva_inversion($contrato_id, $version = 'vigente') {
      $contrato = Contrato::find($contrato_id);
      $serie1_temp = array();
      if($version == 'vigente' && $contrato->has_cronograma_vigente) {
        foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
          $cronograma = $valueContratoMoneda->cronograma_vigente;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['name'] = $valueContratoMoneda->moneda->nombre_simbolo;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['name'] = $valueContratoMoneda->moneda->nombre_simbolo . ' ' . trans('cronograma.curva_inversion.acumulado');

          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['type'] = 'column';
          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['yAxis'] = 1;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['type'] = 'spline';
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['yAxis'] = 2;

          for($mes = 1; $mes <= $cronograma->meses ; $mes++) {
            $valor = str_replace(".", "", $cronograma->valorItemizado('moneda', $mes));
            $valor = str_replace(",", ".", $valor);
            $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['data'][$mes - 1] = (float) $valor;

            if(!isset($serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data']))
              $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 1] = (float) $valor;
            else
              $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 1] = $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 2] + (float) $valor;
          }
        }
      } else {
        foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
          $cronograma = $valueContratoMoneda->cronograma;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['name'] = $valueContratoMoneda->moneda->nombre_simbolo;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['name'] = $valueContratoMoneda->moneda->nombre_simbolo . ' ' . trans('cronograma.curva_inversion.acumulado');

          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['type'] = 'column';
          $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['yAxis'] = 1;
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['type'] = 'spline';
          $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['yAxis'] = 2;

          for($mes = 1; $mes <= $cronograma->meses ; $mes++) {
            $valor = str_replace(".", "", $cronograma->valorItemizado('moneda', $mes));
            $valor = str_replace(",", ".", $valor);
            $serie1_temp[$valueContratoMoneda->moneda_id . '_mensual']['data'][$mes - 1] = (float) $valor;

            if(!isset($serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data']))
              $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 1] = (float) $valor;
            else
              $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 1] = $serie1_temp[$valueContratoMoneda->moneda_id . '_acumulado']['data'][$mes - 2] + (float) $valor;
          }
        }
      }

      $categories = array();
      for($mes = 1; $mes <= $cronograma->meses ; $mes++) {
        $categories[] = $mes;
      }

      // Highcharts necesita que sea 0, 1, etc
      foreach($serie1_temp as $keySerie => $valueSerie) {
        $serie1 [] = array (
              "name"  => $serie1_temp[$keySerie]['name'],
              "type"  => $serie1_temp[$keySerie]['type'],
              "yAxis" => $serie1_temp[$keySerie]['yAxis'],
              "data"  => $serie1_temp[$keySerie]['data']
        );
      }

      if(count($serie1_temp) > 0) {
          $title = trans('contratos.cronograma') . ' ' . trans('cronograma.vista.tag.' . $version);

          return View::make('contratos.contratos.show.cronograma.widgets.curva_inversion', compact('serie1', 'title', 'categories'))->render();
      } else {
        return View::make('dashboard.widgets.no_data')->render();
      }
    }
///////////// FIN Widgets /////////////
}
