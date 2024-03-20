<?php

namespace App\Http\Controllers\Redeterminaciones;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Log;
use Response;
use Storage;
use View;
use Dompdf\Dompdf;

use App\Jobs\InstanciaCalculoPrecios;

use Contrato\Contrato;
use SolicitudContrato\UserContrato;
use SolicitudRedeterminacion\SolicitudRedeterminacion;

use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use SolicitudRedeterminacion\Instancia\Iniciada;
use SolicitudRedeterminacion\Instancia\AprobacionCertificados;
use SolicitudRedeterminacion\Instancia\VerificacionDesvio;
use SolicitudRedeterminacion\Instancia\GeneracionExpediente;
use SolicitudRedeterminacion\Instancia\AsignacionPartidaPresupuestaria;
use SolicitudRedeterminacion\Instancia\ProyectoActaRDP;
use SolicitudRedeterminacion\Instancia\FirmaResolucion;
use SolicitudRedeterminacion\Instancia\EmisionCertificadoRDP;

use SolicitudRedeterminacion\SolicitudRDP\TipoSolicitudRDP;

use App\Events\PasosSolicitudRedeterminacion;

use YacyretaPackageController\Redeterminaciones\SolicitudesRedeterminacionController as PackageSRController;
class SolicitudesRedeterminacionController extends PackageSRController {
    // Como agregar Pasos:
    // 1) Agregar un permiso con ese nombre-manage (ej: Paso1-manage)
    // 2) Crear la migration para la tabla de Paso1, tener en cuenta que varias columnas se repiten en
    // 'red_solicitudes', para tenerla version actual (se hace automatico en punto 5)
    // 3) Crear vistas con ese nombre en:
    //   - redeterminaciones\solicitudes\show\create_update\Paso1.blade.php
    //   - redeterminaciones\solicitudes\show\historial\Paso1.blade.php
    // En la primera van los datos que se cargan (form en modal)
    // En la segunda  los datos que se muestran
    // 4) Agregar los datos de la solicitud en
    //    - redeterminaciones\solicitudes\show\datos_cargados.blade.php
    // 5) Crear una funcion en el Controller con ese nombre
    //     - public function Paso1()
    // En el modelo:
    // - app\Models\Contrato\Redeterminacion\SolicitudRedeterminacion\Instancia\Paso1.php
    // Agregar las columnas que se copiaran automaticamente a la solicitud:
    // protected $columnas_solicitud = ['col1', 'col2', 'etc'];

    public function __construct() {
      View::share('ayuda', 'solicitudes');
      $this->middleware('auth', ['except' => 'logout']);
    }

//////////// Listados ////////////
    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function indexEnProceso(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $estado = 'en_proceso';

      $solicitudes_redeterminacion_admin = Auth::user()->solicitudes_redeterminacion_admin;

      $solicitudes = $solicitudes_redeterminacion_admin->filter(function($solicitud) {
                                        return $solicitud->en_curso;
                                       });

      $solicitudes = $this->ordenar($solicitudes);

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $solicitudes = $this->filtrar($solicitudes, $search_input);
        }
        $solicitudes = $this->paginateCustom($solicitudes);
      } else {
        $solicitudes = $this->filtrar($solicitudes, $search_input);
        $solicitudes = $this->paginateCustom($solicitudes, 1);
      }

      return view('redeterminaciones.solicitudes.index', compact('solicitudes', 'search_input', 'estado'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function indexFinalizadas(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $estado = 'finalizadas';
      $solicitudes_redeterminacion_admin = Auth::user()->solicitudes_redeterminacion_admin;

      $solicitudes = $solicitudes_redeterminacion_admin->filter(function($solicitud) {
                                         return !$solicitud->en_curso;
                                       });

      $solicitudes = $this->ordenar($solicitudes);

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $solicitudes = $this->filtrar($solicitudes, $search_input);
        }
        $solicitudes = $this->paginateCustom($solicitudes);
      } else {
        $solicitudes = $this->filtrar($solicitudes, $search_input);
        $solicitudes = $this->paginateCustom($solicitudes, 1);
      }

      return view('redeterminaciones.solicitudes.index', compact('solicitudes', 'search_input', 'estado'));
    }

    /**
     * @param  SolicitudRedeterminacion\SolicitudRedeterminacion $solicitudes
    */
    private function ordenar($solicitudes) {
      $solicitudes = $solicitudes->groupBy(function($solicitud, $key) {
        return $solicitud->contrato->expediente_madre;
      });

      $toArray = array();
      foreach ($solicitudes as $keySolContrato => $valueSolContrato) {
        $toArray[$keySolContrato] = $valueSolContrato->sortBy(function($solicitud, $key) {
          return $solicitud->salto->nro_salto;
        }, 1, true)->all();
      }
      ksort($toArray);

      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $ordered->push($value);
        }
      }
      return $ordered;
    }

    /**
     * @param  Illuminate\Database\Eloquent\Collection $contratos_admin
     * @param  string $input_lower
    */
    private function filtrar($solicitudes, $input_lower) {
      if($input_lower == '')
        return $solicitudes;

      return $solicitudes->filter(function($solicitud) use($input_lower) {

          return $this->stringContains($solicitud->contrato->expediente_madre, $input_lower) ||
                 $this->stringContains($solicitud->moneda->nombre, $input_lower) ||
                 $this->stringContains($solicitud->created_at, $input_lower) ||
                 $this->stringContains($solicitud->expediente, $input_lower) ||
                 $this->stringContains($solicitud->contrato->contratista->nombre_documento, $input_lower) ||
                 $this->stringContains($solicitud->salto->moneda_mes_anio, $input_lower) ||
                 $this->stringContains($solicitud->estado_nombre_color['nombre'], $input_lower) ||
                 $this->stringContains($solicitud->ultimo_movimiento, $input_lower) ||
                 $this->stringContains($solicitud->contrato->causante_nombre_color['nombre'], $input_lower);
      });
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param  string  $estado
    */
    public function exportar(Request $request, $estado) {
      $input = $request->all();
      $filtro = $input['excel_input'];
      $solicitudes_redeterminacion_admin = Auth::user()->solicitudes_redeterminacion_admin;

      if($estado == 'en_proceso') {
        $solicitudes = $solicitudes_redeterminacion_admin->filter(function($solicitud) {
                                            return $solicitud->en_curso;
                                         });
      } else {
        $solicitudes = $solicitudes_redeterminacion_admin->filter(function($solicitud) {
                                            return !$solicitud->en_curso;
                                         });
      }

      $solicitudes = $this->ordenar($solicitudes);
      $solicitudes = $solicitudes->map(function ($solicitud, $key) use($estado) {
          if($solicitud->a_termino)
            $arr[trans('sol_redeterminaciones.a_termino')] = trans('index.si');
          else
            $arr[trans('sol_redeterminaciones.a_termino')] = trans('index.no');            

          $arr[trans('forms.expediente_madre')] = $solicitud->contrato->expediente_madre;
          $arr[trans('forms.expediente_solicitud')] = $solicitud->nro_expediente;
          $arr[trans('forms.fecha_solicitud')] = $solicitud->created_at;

          if($solicitud->contrato->contratista_id != null)
            $arr[trans('forms.contratista')] = $solicitud->contrato->contratista->nombre_documento;
          else
            $arr[trans('forms.contratista')] = '';

          $arr[trans('forms.salto')] = $solicitud->salto->contrato_moneda_mes_anio;

          $arr[trans('forms.estado')] = '';
          if($estado == 'en_proceso')
            $arr[trans('forms.estado')] .= trans('index.esperando') . ' ';

          $arr[trans('forms.estado')] .= $solicitud->estado_nombre_color['nombre'];

          $arr[trans('forms.ultimo_movimiento')] = $solicitud->ultimo_movimiento;
          if(!Auth::user()->usuario_causante)
            $arr[trans('forms.causante')] = $solicitud->contrato->causante_nombre_color['nombre'];

          return $arr;
      });

      return $this->toExcel(trans('forms.sol_redeterminaciones_' . $estado),
                            $this->filtrarExportacion($solicitudes, $filtro));
    }
//////////// FIN Listados ////////////

//////////// Redeterminacion ////////////
    /**
     * @param  int   $id
    */
    public function ver($id) {
      $solicitud = SolicitudRedeterminacion::findOrFail($id);

      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('solicitudes.redeterminaciones_en_proceso')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      $contrato = $solicitud->salto->contrato_moneda->contrato;
      return view('redeterminaciones.solicitudes.show.index', compact('solicitud', 'contrato'));
    }

    // Retorna vista del modal de creacion
    /**
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    public function createEdit($instancia, $id_solicitud, $correccion = false) {
      $user = Auth::user();
      if((!$user->can($instancia . '-gestionar') && $correccion != "true" )
         // ||(!$user->can($instancia . '-corregir') && $correccion == "true")
       ) {
           $jsonResponse['message'] = trans('index.error403');
           Log::error(trans('index.error403'), ['User' => $user, 'Instancia' => $instancia]);
           $jsonResponse['status'] = false;
           return response()->json($jsonResponse);
      }

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');
        return response()->json($jsonResponse);
      }

      // Si es "ProyectoActaRDP" se cargan los datos para reemplazar en el template
      $acepta_select = true;
      if($instancia == 'ProyectoActaRDP') {
        $instancia_obj = $this->getInstancia($instancia, $id_solicitud, $correccion);
        $solicitud->acta = $instancia_obj->acta;
        $solicitud->resolucion = $instancia_obj->resolucion;
        $solicitud->informe = $instancia_obj->informe;

        // Aca se cargarian los templates
        $acepta_select = false;
        $instancia_acta = $solicitud->getInstancia('ProyectoActaRDP')->instancia;

        if(!$instancia_acta->editada) {
           $instancia_acta->editada = 1;
           $acta = $instancia_acta->acta;
           $resolucion = $instancia_acta->resolucion;
           $informe = $instancia_acta->informe;

           $variables[':nro_redeterminacion'] = $solicitud->salto->nro_salto;

           if($solicitud->contrato->is_adenda_certificacion)
               $variables[':denominacion_adenda'] = $solicitud->contrato->contrato_padre->denominacion;
           else
               $variables[':denominacion_adenda'] = '';

           $variables[':modalidad_y_nro_contratacion'] = $solicitud->contrato->numero_contratacion;
           $variables[':mes_anio_redet'] = $solicitud->salto->publicacion->mes_anio;
           $variables[':razon_social_contratista'] = $solicitud->contrato->contratista->razon_social;
           $variables[':domicilio_legal_contratista'] = $solicitud->contrato->contratista->domicilio_legal;
           $variables[':denominacion_contrato'] = $solicitud->contrato->denominacion;
           $variables[':representant_legal'] = $solicitud->contrato->contratista->representante_legal;
           $variables[':saldo_vigente'] = $this->toDosDec($solicitud->saldo);
           $variables[':saldo_vigent_a_valores_anteriores'] =  $solicitud->contrato_moneda->monto_vigente_dos_dec;

           if($solicitud->salto->salto_anterior)
               $variables[':mes_anio_rede_anterior'] = $solicitud->salto->salto_anterior->publicacion->mes_anio;
           else
               $variables[':mes_anio_rede_anterior'] = substr($solicitud->contrato->fecha_oferta, -7);

           $variables[':incremento'] =  $this->toDosDec($solicitud->mayor_gasto);
           $variables[':vr_definitivo'] = $this->toCuatroDec($solicitud->salto->variacion);
           $variables[':fecha_firma_contrato'] = $solicitud->contrato->fecha_oferta;
           $variables[':vr_definitiv_acumulado'] =  $this->toCuatroDec($solicitud->monto_vigente/$solicitud->contrato_moneda->monto_vigente_val_originales);

           $variables[':fecha_solicitud_redeterminacion'] = $solicitud->created_at;

           foreach ($variables as $keyVariable => $valueVariable) {
            $acta = str_replace($keyVariable, $valueVariable, $acta);
            $resolucion = str_replace($keyVariable, $valueVariable, $resolucion);
            $informe = str_replace($keyVariable, $valueVariable, $informe);
           }

           $instancia_acta->acta = $acta;
           $instancia_acta->resolucion = $resolucion;
           $instancia_acta->informe = $informe;
           $instancia_acta->save();
        }

        if($solicitud->instancia_actual->tipo_instancia->modelo == "ProyectoActaRDP") {
            $acta_content = $solicitud->instancia_actual->instancia->acta;
            $resolucion_content = $solicitud->instancia_actual->instancia->resolucion;
            $informe_content = $solicitud->instancia_actual->instancia->informe;
        } else {
            $acta_content = $solicitud->acta;
            $resolucion_content = $solicitud->resolucion;
            $informe_content = $solicitud->informe;
        }
      }

      return view('redeterminaciones.solicitudes.show.create_update.'.$instancia, compact('instancia', 'solicitud', 'id_solicitud', 'correccion', 'acepta_select', 'acta_content', 'resolucion_content', 'informe_content'));
    }

    /**
    * @param Request $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    public function updateOrStore(Request $request, $instancia, $id_solicitud, $correccion = false) {
      $user = Auth::user();
      $input = $request->all();
      if((!$user->can($instancia . '-gestionar') && $correccion != "true" )
         // || (!$user->can($instancia . '-corregir') && $correccion == "true")
       ) {
           Log::error(trans('index.error403'), ['User' => $user, 'Instancia' => $instancia]);
           $jsonResponse['message'] = [trans('index.error403')];
           $jsonResponse['permisos'] = true;
           $jsonResponse['status'] = false;
           return response()->json($jsonResponse);
      }

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');
        return response()->json($jsonResponse);
      }

      if($instancia == 'ProyectoActaRDP') {
          $instancia_acta = $solicitud->instancia_actual->instancia;
          $instancia_acta->acta = $input['acta_ck'];
          $instancia_acta->resolucion = $input['resolucion_ck'];
          $instancia_acta->informe = $input['informe_ck'];
          $instancia_acta->save();
      }

      $jsonResponse = $this->$instancia($request, $instancia, $id_solicitud, $correccion);
      $jsonResponse = $jsonResponse->getData();
      if($jsonResponse->status == true) {
        $solicitud = SolicitudRedeterminacion::find($id_solicitud);

        if($jsonResponse->cambia_estado)  // && !$correccion)
          $solicitud->pasarInstancia();

        $tipo_instancia = TipoInstanciaRedet::whereModelo($instancia)->first();
        event(new PasosSolicitudRedeterminacion($solicitud, $tipo_instancia, $correccion));

        // Lo busco de nuevo porque algunas relaciones persisten
        $solicitud = SolicitudRedeterminacion::find($id_solicitud);

        $jsonResponse->historial_refresh = View::make('redeterminaciones.solicitudes.show.historial',
                                                       compact('solicitud'))->render();

        $jsonResponse->estado_contrato = View::make('redeterminaciones.solicitudes.show.estado_solicitud',
                                                     compact('solicitud'))->render();

        $jsonResponse->acciones = View::make('redeterminaciones.solicitudes.show.acciones',
                                              compact('solicitud'))->render();

        $jsonResponse->datos_cargados = View::make('redeterminaciones.solicitudes.show.datos_cargados',
                                                    compact('solicitud'))->render();
      }

      return response()->json($jsonResponse);
    }
//////////// FIN Redeterminacion ////////////

//////////// Metodos de creacion y correccion de instancias ////////////
    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 1.1
    public function AprobacionCertificados(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      // if($correccion && $input['acepta_correccion'] == 0) {
      //   $jsonResponse['status'] = false;
      //   $jsonResponse['exige_confirmacion'] = true;
      //   $jsonResponse['message'] = [trans('sol_redeterminaciones.post_dictamen')];
      //   return response()->json($jsonResponse);
      // }

      // $id_solicitud = $this->correccionPostDictamen($id_solicitud, $instancia, $correccion);

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      if(isset($input['certificados_aprobados']))
        $input['certificados_aprobados'] = true;
      else
        $input['certificados_aprobados'] = false;

      $instancia->certificados_aprobados = $input['certificados_aprobados'];
      $instancia->user_creator_id = Auth::user()->id;

      if($instancia->has_instancia_siguiente) {
        $siguiente = $instancia->instancia_siguiente;
        $siguiente->fecha_inicio = date("Y-m-d H:i:s");
      }

      try {
        $instancia->save();
        if($instancia->has_instancia_siguiente)
          $siguiente->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 1.1

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 1.2
    public function VerificacionDesvio(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      $rules = array(
        'observaciones'    => 'nullable|' . $this->min3max255(),
      );

      $validator = Validator::make($input, $rules, $this->validationErrorMessages());

      // Validaciones Custom
      $errores = array();

      if($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      // if($correccion && $input['acepta_correccion'] == 0) {
      //   $jsonResponse['status'] = false;
      //   $jsonResponse['exige_confirmacion'] = true;
      //   $jsonResponse['message'] = [trans('sol_redeterminaciones.post_dictamen')];
      //   return response()->json($jsonResponse);
      // }

      // $id_solicitud = $this->correccionPostDictamen($id_solicitud, $instancia, $correccion);

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      if(isset($input['aplicar_penalidad_desvio']))
        $input['aplicar_penalidad_desvio'] = true;
      else
        $input['aplicar_penalidad_desvio'] = false;

      $instancia->aplicar_penalidad_desvio = $input['aplicar_penalidad_desvio'];
      $instancia->observaciones = $input['observaciones'];
      $instancia->user_creator_id = Auth::user()->id;

      if($instancia->has_instancia_siguiente) {
        $siguiente = $instancia->instancia_siguiente;
        $siguiente->fecha_inicio = date("Y-m-d H:i:s");
      }

      try {
        $instancia->save();
        if($instancia->has_instancia_siguiente)
          $siguiente->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      dispatch((new InstanciaCalculoPrecios($id_solicitud))->onQueue('calculos_variacion'));

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 1.2

    // Paso 2
    // El paso "CalculoPreciosRedeterminados" se realiza automaticamente
    // FIN Paso 2

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 3
    public function GeneracionExpediente(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      if(!isset($input['nro_expediente'])) {
        Session::flash('error', trans('mensajes.error'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error')];
        return response()->json($jsonResponse);
      }

      $instancia->nro_expediente = $input['nro_expediente'];
      $instancia->observaciones = $input['observaciones'];
      $instancia->user_creator_id = Auth::user()->id;

      if($instancia->has_instancia_siguiente) {
        $siguiente = $instancia->instancia_siguiente;
        $siguiente->fecha_inicio = date("Y-m-d H:i:s");
      }

      try {
        $instancia->save();
         if($instancia->has_instancia_siguiente)
          $siguiente->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 3

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 4
    public function AsignacionPartidaPresupuestaria(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      if(!isset($input['nro_partida_presupuestaria'])) {
        Session::flash('error', trans('mensajes.error'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error')];
        return response()->json($jsonResponse);
      }

      $instancia->nro_partida_presupuestaria = $input['nro_partida_presupuestaria'];

      if(isset($input['adjunto']) && $request->hasFile('adjunto')) {
        $adjuntos_json = $this->uploadFile($request, $instancia->id, 'adjunto', 'redeterminaciones');
        $instancia->adjunto = $adjuntos_json;
      }

      $instancia->observaciones = $input['observaciones'];
      $instancia->user_creator_id = Auth::user()->id;

      if($instancia->has_instancia_siguiente) {
        $siguiente = $instancia->instancia_siguiente;
        $siguiente->fecha_inicio = date("Y-m-d H:i:s");
      }

      try {
        $instancia->save();
         if($instancia->has_instancia_siguiente)
         $siguiente->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 4

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 5
    public function ProyectoActaRDP(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      // if($correccion && $input['acepta_correccion'] == 0) {
      //   $jsonResponse['status'] = false;
      //   $jsonResponse['exige_confirmacion'] = true;
      //   $jsonResponse['message'] = [trans('sol_redeterminaciones.post_dictamen')];
      //   return response()->json($jsonResponse);
      // }

      // $id_solicitud = $this->correccionPostDictamen($id_solicitud, $instancia, $correccion);

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      $instancia->acta = $input['acta_ck'];
      $instancia->resolucion = $input['resolucion_ck'];
      $instancia->informe = $input['informe_ck'];
      $instancia->user_creator_id = Auth::user()->id;

      if($input['borrador'] == 0) {
        $solicitud = $instancia->instancia->solicitud;
        $solicitud->acta = $input['acta_ck'];
        $solicitud->resolucion = $input['resolucion_ck'];
        $solicitud->informe = $input['informe_ck'];

        $instancia->borrador = $input['borrador'];
        $instancia->editada = 1;

        if($instancia->has_instancia_siguiente) {
          $siguiente = $instancia->instancia_siguiente;
          $siguiente->fecha_inicio = date("Y-m-d H:i:s");
        }
      }

      try {
        $instancia->save();
        if($input['borrador'] == 0) {
          $solicitud->save();
          if($instancia->has_instancia_siguiente)
            $siguiente->save();
        }
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado && $input['borrador'] == 0;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 5

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param string $instancia nombre del modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    // Paso 6
    public function FirmaResolucion(Request $request, $instancia, $id_solicitud, $correccion) {
      $input = $request->all();

      // if($correccion && $input['acepta_correccion'] == 0) {
      //   $jsonResponse['status'] = false;
      //   $jsonResponse['exige_confirmacion'] = true;
      //   $jsonResponse['message'] = [trans('sol_redeterminaciones.post_dictamen')];
      //   return response()->json($jsonResponse);
      // }

      // $id_solicitud = $this->correccionPostDictamen($id_solicitud, $instancia, $correccion);

      $solicitud = SolicitudRedeterminacion::find($id_solicitud);
      $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);

      $instancia->nro_resolucion = $input['nro_resolucion'];

      $acta_firmada_json = $this->uploadFile($request, $instancia->id, 'acta_firmada', 'redeterminaciones');
      $instancia->acta_firmada = $acta_firmada_json;

      $resolucion_firmada_json = $this->uploadFile($request, $instancia->id, 'resolucion_firmada', 'redeterminaciones');
      $instancia->resolucion_firmada = $resolucion_firmada_json;
      $instancia->user_creator_id = Auth::user()->id;

      $this->createRedeterminacion($solicitud);

      $redeterminacion = $solicitud->redeterminacion;
      $solicitud = $instancia->instancia->solicitud;

      $this->createCertificadosRedeterminados($solicitud, $redeterminacion);

      if($instancia->has_instancia_siguiente) {
        $siguiente = $instancia->instancia_siguiente;
        $siguiente->fecha_inicio = date("Y-m-d H:i:s");
      }

      $instancia_emision = $siguiente->instancia;
      if(!count($redeterminacion->certificados) > 0) {
        $instancia_emision->certificados_emitidos = 0;
      } else {
        $instancia_emision->certificados_emitidos = 1;
        $siguiente->instancia->save();
      }

      // recalculo saldo y vigente
      $solicitud->contrato->reCalculoMontoYSaldo($solicitud->contrato_moneda->id);
      $solicitud->finalizada = 1;

      try {
        $instancia->save();
        $solicitud->save();
        $instancia_emision->save();
        if($instancia->has_instancia_siguiente)
          $siguiente->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
      $jsonResponse['message'] = [trans('sol_redeterminaciones.exito.' . $instancia)];
      return response()->json($jsonResponse);
    }
    // FIN Paso 6


    // Busca una instancia:
    // - Si es gestion de una con orden trae la creada al iniciar la solicitud
    // - Si es gestion de una sin orden, la crea
    // - Si es correccion la busca por tipo e id
    /**
    * @param string $modelo
    * @param int $id_solicitud
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    public function getInstancia($modelo, $id_solicitud, $correccion) {
      if($correccion == "true") {
        $tipo_instancia = TipoInstanciaRedet::whereModelo($modelo)->first();
        $tipo_instancia_id = $tipo_instancia->id;
        try {
          $solicitud = SolicitudRedeterminacion::find($id_solicitud);
          $instancia_actual = $solicitud->instancia_actual;
          $orden_nueva = $instancia_actual->orden;

          $solicitud->updateOrdenInstanciasRestantes();

          $instancia = Instancia::create([
              'redeterminacion_id'    => $id_solicitud,
              'tipo_instancia_id'     => $tipo_instancia_id,
              'orden'                 => $orden_nueva
          ]);
          $instancia->correccion = 1;
          $instancia->fecha_inicio = date("Y-m-d H:i:s");
          $instancia->save();

          $modelo = "SolicitudRedeterminacion\Instancia\\$tipo_instancia->modelo";
          $instancia_model = $modelo::create(['instancia_id'      => $instancia->id,
                                              'user_creator_id'   => Auth::user()->id
                                          ]);

        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          Session::flash('error', trans('mensajes.error.insert_db'));
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);
        }
        return $instancia_model;
      } else {
        $tipo_instancia = TipoInstanciaRedet::whereModelo($modelo)->first();
        $tipo_instancia_id = $tipo_instancia->id;
        $solicitud = SolicitudRedeterminacion::find($id_solicitud);
        if($tipo_instancia->cambia_estado ||
          ($tipo_instancia->modelo =='SolicitudRDP' && $solicitud->solicitud_rdp == null)) {
          $instancia = Instancia::whereTipoInstanciaId($tipo_instancia_id)
                                ->whereSolicitudId($id_solicitud)
                                ->first();
          $instancia_model = $instancia->instancia;
        } else {
          try {
            $solicitud = SolicitudRedeterminacion::find($id_solicitud);
            $instancia_actual = $solicitud->instancia_actual;
            $orden_nueva = $instancia_actual->orden;

            // $solicitud->updateOrdenInstanciasRestantes();

            $instancia = Instancia::create(['solicitud_id'      => $id_solicitud,
                                            'tipo_instancia_id' => $tipo_instancia_id,
                                            'orden'             => $orden_nueva
                                        ]);

            $modelo = "SolicitudRedeterminacion\Instancia\\$modelo";
            $instancia_model = $modelo::create([
                                                'instancia_id'  => $instancia->id
                                              ]);
          } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
          }
        }
        return $instancia_model;
      }
    }


    // Si la correccion se dio despues del dictamen, se vuelve requiere volver a realizar
    // todos los pasos posteriores al corregido
    /**
    * @param int $id_solicitud
    * @param string $instancia nombre del modelo
    * @param string|boolean $correccion true: correccion, false: gestion
    */
    private function correccionPostDictamen($id_solicitud, $instancia, $correccion) {}

//////////// FIN Metodos de creacion y correccion de instancias ////////////


////////////  Metodos sobre la redeterminacion en si ////////////
    // Si se rechaza no se puede continuar, se debera solicitar una nueva redeterminacion
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
    */
    public function rechazar(Request $request, $id) {}

    // Si se suspende se puede reanudar (metodo continuar)
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
    */
    public function suspender(Request $request, $id) {}

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
    */
    public function continuar(Request $request, $id) {}
//////////// FIN Metodos sobre la redeterminacion en si ////////////

//////////// Cuadro Comparativo ////////////
    // En YacyretaPackageController\SolicitudesRedeterminacionController;
//////////// FIN Cuadro Comparativo ////////////

    public function finalizarPendientes() {
      echo 'INICIO finalizarPendientes <br>';
      $solicitudes = SolicitudRedeterminacion::whereFinalizada(0)
                                             ->get()->filter(function($solicitud) {
                                                if($solicitud->contrato_moneda->lleva_analisis)
                                                  return false;

                                                $instancia = app('SolicitudesRedeterminacionController')->getInstancia('EmisionCertificadoRDP', $solicitud->id, false);

                                               return $instancia->instancia_id == $solicitud->instancia_actual_id;
                                             })
                                             ->sortBy(function($solicitud) {
                                               return $solicitud->salto->nro_salto;
                                             });

      foreach ($solicitudes as $key => $solicitud) {
        $instancia = app('SolicitudesRedeterminacionController')->getInstancia('EmisionCertificadoRDP', $solicitud->id, false);
        if(!$solicitud->contrato_moneda->lleva_analisis && $instancia->instancia_id == $solicitud->instancia_actual_id) {
          $this->createRedeterminacion($solicitud);
          $redeterminacion = $solicitud->redeterminacion;
          $solicitud = $instancia->instancia->solicitud;

          $this->createCertificadosRedeterminados($solicitud, $redeterminacion);

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
          echo 'Solicitud id=' . $solicitud->id . ' finalizada  ' . route('solicitudes.ver', ['id' => $solicitud->id]) . '<br>';
        }
      }
        echo 'FIN finalizarPendientes <br>';
    }
}
