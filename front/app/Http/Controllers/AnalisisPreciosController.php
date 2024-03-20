<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;

use Yacyreta\SIGO\WebService;

use App\User;

use Contrato\Contrato;
use SolicitudContrato\UserContrato;
use Indice\IndiceTabla1;

use AnalisisPrecios\AnalisisPrecios;
use AnalisisPrecios\AnalisisPreciosModelExtended;
use AnalisisPrecios\EstadoAnalisisPrecios;
use AnalisisPrecios\InstanciaAnalisisPrecios;
use AnalisisPrecios\ItemObra;
use AnalisisPrecios\Coeficiente\Coeficiente;
use AnalisisPrecios\Coeficiente\CostosCoeficiente;
use AnalisisPrecios\Coeficiente\ImpuestosCoeficiente;
use AnalisisPrecios\Item\CategoriaAnalisis;
use AnalisisPrecios\Item\Insumo;
use AnalisisPrecios\Item\InsumoAuxiliar;
use AnalisisPrecios\Item\ItemCategoria;
use AnalisisPrecios\Item\SubInstanciaAnalisisPrecios;

class AnalisisPreciosController extends Controller {

  public function __construct(WebService $ws) {
    View::share('ayuda', 'contratos');
    $this->web_service = $ws;
    $this->middleware('auth', ['except' => 'logout']);
  }

  /**
   * @param  int $contrato_id
   */
  public function edit($contrato_id) {
    $user = Auth::user();

    $user_contrato = UserContrato::whereContratoId($contrato_id)
                                 ->whereUserContratistaId($user->user_contratista_id)->first();
    if($user_contrato == null)
      return redirect()->route('contratos.index');

    $items_por_obra = ItemObra::whereContratoId($contrato_id)->get()->groupBy(function($item) {
            return $item->tipo_obra->getAttributes()['nombre'];
          });
    $contrato = Contrato::find($contrato_id);

    if(sizeof($items_por_obra) == 0) {
      // Pido el itemizado a SIGO
      try {
        $items_por_obra = $this->actualizarItemizado($contrato_id);
      } catch(\Exception $e) {
         Log::error('Error al conectar con SIGO', ['Exception' => $e]);
         // Si no funciona la actualizacion se informa a los usuarios
         // de DNV con el permiso
         $model_extended = new AnalisisPreciosModelExtended();
         $model_extended->notifyItemizadoFallido($contrato_id);
      }
    }

    if($contrato->coeficiente == null) {
      // Creo el coeficiente Resumen
      $coeficiente = Coeficiente::create(['contrato_id' => $contrato_id]);
      $costos_coeficiente = CostosCoeficiente::create(['coeficiente_id' => $coeficiente->id]);
      $impuestos_coeficiente = ImpuestosCoeficiente::create(['coeficiente_id' => $coeficiente->id]);
    }

    $contrato = Contrato::findOrFail($contrato_id);
    // $user_contrato = UserContrato::findOrFail(49);
    return view('analisis_precios.edit', compact('contrato', 'user_contrato', 'items_por_obra'));
  }

  /**
   * @param  int $contrato_id
   */
  private function actualizarItemizado($contrato_id) {
    $itemizado = $this->web_service->getItemizadoDe($contrato_id);
    return ItemObra::whereContratoId($contrato_id)->get()->groupBy(function($item) {
            return $item->tipo_obra->nombre;
          });
  }

  /**
   * @param  int $contrato_id
   */
  public function historial($contrato_id) {
    $contrato = Contrato::find($contrato_id);
    $instancias = $contrato->instancias_analisis_precios;

    $jsonResponse = View::make('analisis_precios.historial', compact('instancias'))->render();
    return response()->json($jsonResponse);
  }

  /**
   * @param  int $sub_instancia_id
   */
  public function historialDetalle($sub_instancia_id) {
    $subInstancia = SubInstanciaAnalisisPrecios::find($sub_instancia_id);
    $clase = $subInstancia->clase;
    if($subInstancia->detalle == '' || $subInstancia->detalle == null)
      return $clase->mas_data_historial;
    else
      return $clase->mas_data_historial_detalle($subInstancia->detalle);
  }

  /////////////////////// Request ajax por ser demasiada informacion ///////////////////////
    /**
    * @param  string $categoria_obra
     * @param  int $contrato_id
     */
    public function detalleItem($categoria_obra, $contrato_id) {
      $user = Auth::user();

      $user_contrato = UserContrato::whereContratoId($contrato_id)
                                   ->whereUserContratistaId($user->user_contratista_id)->first();
      if($user_contrato == null)
        return redirect()->route('contratos.index');

      $items = ItemObra::whereContratoId($contrato_id)->get()
                                ->filter(function($item) use($categoria_obra) {
                                  return $item->tipo_obra->getAttributes()['nombre'] == $categoria_obra;
                                });

      $contrato = Contrato::find($contrato_id);
      $jsonResponse = View::make('analisis_precios.tablas.items_detalle', compact('items', 'contrato'))->render();
      return response()->json($jsonResponse);
    }


  /////////////////////// FIN Request ajax por ser demasiada informacion ///////////////////////


  /////////////////////// Categorias ///////////////////////
  /**
   * @param  int $item_id
   */
  public function addCategoria($item_id) {
    $item = ItemObra::findOrFail($item_id);
    $categorias_item = $item->categorias;
    $categorias = CategoriaAnalisis::all();

    $diff = $categorias->diff($categorias_item);

    $select_options = array();
    foreach ($diff as $keyCategoria => $valueCategoria) {
      $select_options[$valueCategoria->id] = $valueCategoria->nombre;
    }

    return view('analisis_precios.modals.add_categoria', compact('select_options', 'item_id'));
  }

  /**
   * @param  int $item_id
   * @param  \Illuminate\Http\Request  $request
   */
  public function addCategoriaPost($item_id, Request $request) {
    $input = $request->all();
    $categoria = $input['categoria'];
    $valueItemCategoria = ItemCategoria::create([
                            'item_id'              => $item_id,
                            'categoria_id'         => $input['categoria'],
                            'user_creator_id'      => Auth::user()->id,
                        ]);

    $valueItem = ItemObra::findOrFail($item_id);
    $categoria = CategoriaAnalisis::find($input['categoria']);
    $contrato = $valueItem->contrato;
    $instancia_actual = $contrato->instancia_actual_analisis;
    $subInstancia = SubInstanciaAnalisisPrecios::create([
                            'accion'              => 'create',
                            'clase_type'          => $categoria->getClassName(),
                            'clase_id'            => $input['categoria'],
                            'instancia_id'        => $instancia_actual->id,
                            'user_creator_id'     => Auth::user()->id,
                        ]);

    Session::flash('success', trans('mensajes.dato.categoria') . trans('mensajes.success.agregada'));
    $jsonResponse['message'] = [trans('mensajes.dato.categoria') . trans('mensajes.success.agregada')];
    $jsonResponse['status'] = true;
    $jsonResponse['destino'] = '#categorias_' . $item_id;
    $jsonResponse['html'] = View::make('analisis_precios.categoria', compact('valueItemCategoria', 'valueItem', 'contrato'))->render();
    return response()->json($jsonResponse);
  }
  /////////////////////// FIN Categorias ///////////////////////

  /////////////////////// Insumos ///////////////////////
  /**
  * @param  int $item_categoria_id
  * @param  int $insumo_id
   */
  public function addEditInsumo($item_categoria_id, $insumo_id = null) {
    $item_categoria = ItemCategoria::findOrFail($item_categoria_id);
    $categoria = $item_categoria->categoria;
    if($insumo_id != null) {
      $insumo = Insumo::findOrFail($insumo_id);
    }

    $insumos_auxiliares = InsumoAuxiliar::allToArray();

    $indices = IndiceTabla1::all();
    return view('analisis_precios.modals.add_edit_insumo',
                compact('item_categoria', 'insumo', 'insumos_auxiliares', 'indices'));
  }

  /**
   * @param  int $item_categoria_id
   * @param  \Illuminate\Http\Request  $request
   */
  public function addEditInsumoPost($item_categoria_id, Request $request) {
    $input = $request->all();
    if($input['new_name'] === 1) {
      $rules = array(
        'nombre'           => $this->min3max255(),
      );
    } else {
      $rules = array();
    }

    $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

    $errores = array();
    $perdida = str_replace("%", "", $input['perdida']);
    $perdida = str_replace(" ", "", $perdida);
    if($perdida < 0)
      $errores['perdida'] = trans('mensajes.dato.perdida') . trans('mensajes.error.mayor_igual_0');


    if ($validator->fails() || sizeof($errores) > 0) {
      $errores = array_merge($errores, $validator->getMessageBag()->toArray());
      $jsonResponse['status'] = false;
      $jsonResponse['errores'] = $errores;
      $jsonResponse['message'] = [];

      return response()->json($jsonResponse);
    }

    $categoria = $input['categoria'];
    $valueItemCategoria = Insumo::create([
                            'item_id'              => $item_categoria_id,
                            'categoria_id'         => $input['categoria'],
                            'user_creator_id'      => Auth::user()->id,
                        ]);

    $valueItem = ItemObra::findOrFail($item_id);
    $contrato = $valueItem->contrato;
    $instancia_actual = $contrato->instancia_actual_analisis;

    $subInstancia = SubInstanciaAnalisisPrecios::create([
                            'accion'              => 'create',
                            'clase_type'          => $valueItem->getClassName(),
                            'clase_id'            => $item_id,
                            'instancia_id'        => $instancia_actual->id,
                            'user_creator_id'     => Auth::user()->id,
                        ]);

    Session::flash('success', trans('mensajes.dato.categoria') . trans('mensajes.success.agregada'));
    $jsonResponse['message'] = [trans('mensajes.dato.categoria') . trans('mensajes.success.agregada')];
    $jsonResponse['status'] = true;
    $jsonResponse['html'] = View::make('analisis_precios.categoria', compact('valueItemCategoria', 'valueItem'))->render();
    return response()->json($jsonResponse);
  }
  /////////////////////// FIN Insumos ///////////////////////


  /////////////////////// Instancias y validaciones ///////////////////////
  /**
   * @param  int $contrato_id
   */
  public function enviar_aprobar($contrato_id) {
    $user = Auth::user();

    $user_contrato = UserContrato::whereContratoId($contrato_id)
                                 ->whereUserContratistaId($user->user_contratista_id)->first();
    if($user_contrato == null)
      abort(403);

    $items = ItemObra::whereContratoId($contrato_id)->get();
    foreach($items as $keyItem => $valueItem) {
      if($valueItem->no_tiene_analisis) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('analisis_precios.sin_analisis', ['item' => $valueItem->nombre]));
        $jsonResponse['message'] = trans('analisis_precios.sin_analisis', ['item' => $valueItem->nombre]);
        return response()->json($jsonResponse);
      }
      if($user_contrato->contrato->coeficiente->costos_coeficiente->indice_tabla1 == null && $contrato->coeficiente->costos_coeficiente->costos_financieros == 0) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('analisis_precios.sin_indice_tabla1'));
        $jsonResponse['message'] = trans('analisis_precios.sin_indice_tabla1');
        return response()->json($jsonResponse);
      }
    }


    $contrato = Contrato::findOrFail($contrato_id);

    $instancia_anterior = $contrato->instancia_actual_analisis;
    $subInstancia = SubInstanciaAnalisisPrecios::create([
                            'accion'              => 'create_instancia_a_validar',
                            'clase_type'          => $instancia_anterior->getClassName(),
                            'clase_id'            => $instancia_anterior->id,
                            'instancia_id'        => $instancia_anterior->id,
                            'user_creator_id'     => Auth::user()->id,
                        ]);

    $estado_id = EstadoAnalisisPrecios::whereNombre('a_validar')->first()->id;
    $instancia = InstanciaAnalisisPrecios::create([
                          'estado_id'               => $estado_id,
                          'contrato_id'             => $contrato_id,
                          'observaciones'           => '',
                          'user_creator_id'         => Auth::user()->id,
                      ]);

    $jsonResponse['status'] = true;
    $jsonResponse['refresh'] = true;
    Session::flash('success', trans('analisis_precios.mensajes.success.enviar_aprobar'));
    $jsonResponse['message'] = trans('analisis_precios.mensajes.success.enviar_aprobar');
    return response()->json($jsonResponse);
  }
  /////////////////////// FIN Instancias y validaciones ///////////////////////

  /////////////////////// Error ///////////////////////
  /**
   * @param  string $modelo
   * @param  int $contrato_id
   * @param  int $id | nullable
   */
  public function editError($modelo, $contrato_id, $id = null) {
    $model_extended = new AnalisisPreciosModelExtended();
    if($id != null) {
      $objeto = app($model_extended->ClaseDe($modelo))::find($id);
    }

    if($id == null)
      $id = 0;

    return view('analisis_precios.modals.edit_error', compact('objeto', 'modelo', 'contrato_id', 'id'));
  }

  /**
   * @param  string $modelo
   * @param  int $contrato_id
   * @param  int $id | nullable
   * @param  \Illuminate\Http\Request  $request
   */
  public function updateError($modelo, $contrato_id, $id, Request $request) {
    $input = $request->all();
    $model_extended = new AnalisisPreciosModelExtended();
    if($id != null) {
      $objeto = app($model_extended->ClaseDe($modelo))::find($id);
      $objeto->error = $input['error'];
      $objeto->save();
    }

    $contrato = Contrato::find($contrato_id);
    Session::flash('success', trans('mensajes.dato.error') . trans('mensajes.success.modificado'));
    $jsonResponse['message'] = [trans('mensajes.dato.error') . trans('mensajes.success.modificado')];
    $jsonResponse['status'] = true;
    $jsonResponse['destino'] = '#accordion-coeficiente';
    $jsonResponse['html'] = View::make('analisis_precios.coeficiente', compact('contrato'))->render();
    return response()->json($jsonResponse);
  }
  /////////////////////// FIN Error ///////////////////////

  /////////////////////// Coeficiente ///////////////////////
  /**
   * @param  int $coeficiente_id
   * @param  string $dato
   */
  public function editCoeficiente($coeficiente_id, $dato) {
    $coeficiente = Coeficiente::find($coeficiente_id);
    $valor = $coeficiente->$dato;

    return view('analisis_precios.modals.edit_coeficiente', compact('coeficiente_id', 'dato', 'valor'));
  }

  /**
   * @param  int $coeficiente_id
   * @param  string $dato
   * @param  \Illuminate\Http\Request  $request
   */
  public function editCoeficientePost($coeficiente_id, $dato, Request $request) {
    $input = $request->all();
    $coeficiente_orig = Coeficiente::find($coeficiente_id);
    $coeficiente = $coeficiente_orig->replicate();
    $coeficiente->push();

    $costos_coeficiente = $coeficiente_orig->costos_coeficiente->replicate();
    $costos_coeficiente->coeficiente_id = $coeficiente->id;
    $costos_coeficiente->push();
    $coeficiente_orig->costos_coeficiente->delete();

    $impuestos_coeficiente = $coeficiente_orig->impuestos_coeficiente->replicate();
    $impuestos_coeficiente->coeficiente_id = $coeficiente->id;
    $impuestos_coeficiente->push();
    $coeficiente_orig->impuestos_coeficiente->delete();

    $coeficiente_orig->delete();

    $coeficiente->$dato = $input['valor'];

    try {
      $coeficiente->save();
    } catch (QueryException $e) {
      Log::error('QueryException', ['Exception' => $e]);
      Session::flash('error', trans('mensajes.error.insert_db'));
      $jsonResponse['status'] = false;
      $jsonResponse['message'] = trans('mensajes.error.insert_db');
      return response()->json($jsonResponse);
    }

    $contrato = Contrato::find($coeficiente->contrato_id);

    $instancia_actual = $contrato->instancia_actual_analisis;
    $subInstancia = SubInstanciaAnalisisPrecios::create([
                            'accion'              => 'update',
                            'clase_type'          => $coeficiente->getClassName(),
                            'clase_id'            => $coeficiente_id,
                            'instancia_id'        => $instancia_actual->id,
                            'user_creator_id'     => Auth::user()->id,
                        ]);

    $subInstancia->detalle = $dato;
    $subInstancia->save();

    Session::flash('success', trans('mensajes.dato.' . $dato) . trans('mensajes.success.modificado'));
    $jsonResponse['message'] = [trans('mensajes.dato.' . $dato) . trans('mensajes.success.modificado')];
    $jsonResponse['status'] = true;
    $jsonResponse['destino'] = '#accordion-coeficiente';
    $jsonResponse['html'] = View::make('analisis_precios.coeficiente', compact('contrato'))->render();
    return response()->json($jsonResponse);
  }
  /////////////////////// FIN Coeficiente ///////////////////////

}
