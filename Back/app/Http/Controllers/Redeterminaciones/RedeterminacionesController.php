<?php

namespace App\Http\Controllers\Redeterminaciones;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use DateTime;
use DB;
use Log;
use Redirect;
use Response;
use Storage;
use View;

use App\User;

use Yacyreta\Causante;
use Yacyreta\Moneda;

use Contrato\Contrato;
use Contrato\EstadoContrato;

use Contrato\ContratoMoneda\ContratoMoneda;

use Itemizado\Itemizado;
use Itemizado\Item;

use Indice\IndiceTabla1;
use AnalisisPrecios\AnalisisItem;
use AnalisisPrecios\AnalisisPrecios;
use AnalisisPrecios\Categoria\CategoriaModelExtended;
use AnalisisPrecios\Categoria\Componente\ComponenteModelExtended;

use Indice\PublicacionIndice;
use Redeterminacion\CategoriaRedeterminacion;
use Redeterminacion\ComponenteRedeterminacion;
use Redeterminacion\PrecioRedeterminadoItem;
use Redeterminacion\Redeterminacion;

use CalculoRedeterminacion\VariacionMesPolinomica;

use App\Http\Controllers\Contratos\ContratosControllerExtended;
class RedeterminacionesController extends ContratosControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

//////////// Analisis de Precios ////////////

    /**
     * @param  int    $contrato_id
    */
    public function create($contrato_id) {
      $contrato = Contrato::find($contrato_id);

      if(!Auth::user()->puedeVerCausante($contrato->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      if(!$contrato->permite_redeterminacion) {
        Session::flash('error', trans('redeterminaciones.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('redeterminaciones.sin_permisos')]);
      }

      $publicaciones = PublicacionIndice::getOpcionesPublicadas();

      // Tomo el primer dia del mes
      $fecha_oferta_primero = DateTime::createFromFormat('d/m/Y', $contrato->fecha_oferta);
      $fecha_oferta_primero = date_create($fecha_oferta_primero->format('Y-m-01'));

      foreach ($publicaciones as $keyPublicacion => $valuePublicacion) {
        if($keyPublicacion != "") {
          $fecha_publicacion = DateTime::createFromFormat('m/Y', $valuePublicacion);
          $fecha_publicacion = date_create($fecha_publicacion->format('Y-m-01'));
          if($fecha_publicacion < $fecha_oferta_primero)
            unset($publicaciones[$keyPublicacion]);
        }
      }

      return view('redeterminaciones.redeterminaciones.create', compact('contrato', 'publicaciones'));
    }

    /**
     * @param  int    $contrato_id
     * @param  \Illuminate\Http\Request  $request
    */
    public function store($contrato_id, Request $request) {
      $contrato = Contrato::find($contrato_id);

      if(!Auth::user()->puedeVerCausante($contrato->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      if(!$contrato->permite_redeterminacion) {
        Session::flash('error', trans('redeterminaciones.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('redeterminaciones.sin_permisos')]);
      }

      $input = $request->all();
      $contrato_moneda = ContratoMoneda::find($input['contrato_moneda_id']);

      $last_redeterminacion = $contrato->redeterminaciones()->whereItemizadoId($contrato_moneda->itemizado_actual->id)
                                       ->orderBy('nro_salto', 'DESC')->first();

      $input = $request->all();
      $publicacion = PublicacionIndice::find($input['publicacion_id']);

      $errores = array();
      if($contrato_moneda->moneda->lleva_analisis && $contrato_moneda->analisis_precios == null) {
        $errores['falta_analisis_precios'] = trans('mensajes.error.falta_analisis_precios');
      }

      if($last_redeterminacion != null) {
        if($last_redeterminacion->borrador) {
          $errores['borrador'] = trans('mensajes.error.tiene_redeterminacion_borrador', ['moneda' => $contrato_moneda->moneda->nombre_simbolo]);
        } else {
          if($input['nro_salto'] <= $last_redeterminacion->nro_salto)
            $errores['nrosalto'] = trans('mensajes.error.nro_salto_menor_anterior');

          if(($publicacion->anio < $last_redeterminacion->publicacion->anio) ||
             ($publicacion->anio == $last_redeterminacion->publicacion->anio && $publicacion->mes <= $last_redeterminacion->publicacion->mes))
              $errores['publicacion'] = trans('mensajes.error.publicacion_menor_anterior');
        }
      }

      $fecha_publicacion = DateTime::createFromFormat('m/Y', $publicacion->mes_anio);
      $fecha_publicacion = date_create($fecha_publicacion->format('Y-m-01'));

      $fecha_oferta_primero = DateTime::createFromFormat('d/m/Y', $contrato->fecha_oferta);
      $fecha_oferta_primero = date_create($fecha_oferta_primero->format('Y-m-01'));

      if($fecha_publicacion < $fecha_oferta_primero) {
        $errores['publicacion'] = trans('mensajes.error.publicacion_menor_fecha_base');
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      $parametros = ['solicitud_id'       => null,
                     'nro_salto'          => $input['nro_salto'],
                     'contrato_moneda_id' => $contrato_moneda->id,
                     'itemizado_id'       => $contrato_moneda->itemizado_actual->id,
                     'contrato_id'        => $contrato->id,
                     'publicacion_id'     => $publicacion->id,
                    ];

      $redeterminacion_id = DB::transaction(function () use ($parametros) {
        $redeterminacion = Redeterminacion::create($parametros);

        $redeterminacion->empalme = 1;
        $redeterminacion->save();
        $redeterminacion_id = $redeterminacion->id;

        $itemizado = Itemizado::find($parametros['itemizado_id']);

        $moneda = $itemizado->contrato_moneda->moneda;

        foreach($itemizado->items_hoja as $keyItem => $valueItem) {
          $precio_redet = PrecioRedeterminadoItem::create(['redeterminacion_id'  => $redeterminacion_id,
                                                           'item_id'             => $valueItem->id]);

          if($moneda->lleva_analisis) {
            $analisis_item = AnalisisItem::whereItemId($precio_redet['item_id'])->first();
          }
        }

        return $redeterminacion_id;
      });

      if($contrato_moneda->lleva_analisis) {
        $analisis_precios_existente = $contrato_moneda->itemizado_actual->analisis_precios_actual;

        if($analisis_precios_existente->estado['nombre'] != 'borrador') {
           $this->createAnalisisPreciosRedeterminados($analisis_precios_existente, $redeterminacion_id);
        }
      }

      Session::flash('success', trans('mensajes.dato.redeterminacion') . trans('mensajes.success.creada'));
      $jsonResponse['message'] = [trans('mensajes.dato.redeterminacion') . trans('mensajes.success.creada')];
      $jsonResponse['status'] = true;
      $jsonResponse['url'] = route('empalme.redeterminacion.edit', ['redeterminacion_id' => $redeterminacion_id]);
      return response()->json($jsonResponse);
    }

    /**
     * @param  int    $redeterminacion_id
    */
    public function edit($redeterminacion_id) {
      $redeterminacion = Redeterminacion::findOrFail($redeterminacion_id);

      if(!$redeterminacion->permite_editar) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      $edit = true;
      return view('redeterminaciones.redeterminaciones.edit', compact('redeterminacion', 'edit'));
    }

    /**
     * @param  int    $redeterminacion_id
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateOrStore($redeterminacion_id, Request $request) {
      $redeterminacion = Redeterminacion::find($redeterminacion_id);

      if(!Auth::user()->puedeVerCausante($redeterminacion->contrato->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      if(!$redeterminacion->permite_editar) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      $input = $request->all();
      $errores = [];

      foreach ($input['precio_unitario'] as $keyPrecio => $valuePrecio) {
        $item = Item::find($keyPrecio);
        $valuePrecio = (float)$valuePrecio;

        if(!$input['borrador'] && ($valuePrecio === "" || $valuePrecio === null ))
          $errores['precio_unitario_error_' . $keyPrecio] = trans('redeterminaciones.mensajes.error.required', ['item'       => $item->descripcion_codigo,
                                                                                                                'attribute'  => trans('redeterminaciones.monto_unitario_redeterminado')]);

        if(!($valuePrecio === "" || $valuePrecio === null)) {
          $input['precio_unitario'][$keyPrecio] = str_replace(".", "", $input['precio_unitario'][$keyPrecio]);
          $input['precio_unitario'][$keyPrecio] = str_replace(",", ".", $input['precio_unitario'][$keyPrecio]);

          $error = $this->validarTamanio($input['precio_unitario'], $keyPrecio, $keyPrecio);
          if(sizeof($error) > 0) {
            $error[$keyPrecio . '_' . $keyPrecio] = $item->descripcion_codigo . ': ' . $error[$keyPrecio . '_' . $keyPrecio];
            $errores = array_merge($errores, $error);
          }
        }
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      //update precio unitario redeterminado si no hubo error
      foreach ($input['precio_unitario'] as $keyPrecio => $valuePrecio) {
        $precio_redeterminado_item = $redeterminacion->precios_redeterminados_item()
                                                     ->whereItemId($keyPrecio)
                                                     ->first();

        $cantidad = (float)$precio_redeterminado_item->item->cantidad;

        $precio_redeterminado_item->precio = $valuePrecio;
        $precio_redeterminado_item->cantidad = $cantidad ;
        $precio_redeterminado_item->precio_total = $valuePrecio * $cantidad;

        $monto_unitario_anterior = (float)$precio_redeterminado_item->monto_unitario_anterior;

        if($monto_unitario_anterior == 0)
          $precio_redeterminado_item->variacion = 0;
        else
          $precio_redeterminado_item->variacion =  $valuePrecio / $monto_unitario_anterior;

        $precio_redeterminado_item->save();
      }

      //validacion analisis x precio unitario redeterminado
      if(!$input['borrador'] && $redeterminacion->contrato_moneda->moneda->lleva_analisis) {
        $analisis_precios =  $redeterminacion->itemizado->analisisPreciosRedeterminado($redeterminacion->id);
        foreach ($analisis_precios->analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
          $analisis_redeterminado_item = (float)$valueAnalisisItem->costo_unitario_adaptado;

          if($analisis_redeterminado_item == "" || $analisis_redeterminado_item == null )
            $errores['analisis_item_error_' . $keyAnalisisItem] = trans('redeterminaciones.mensajes.error.required', ['item'=> $valueAnalisisItem->item->descripcion_codigo,
                                                                                                              'attribute'  => trans(' analisis_item.costo_unitario')]);

            $precio_redeterminado_item = $valueAnalisisItem->item->precio_redeterminado_item($redeterminacion->id);

            $importe = $precio_redeterminado_item->precio;
            $coeficiente = (float)$valueAnalisisItem->costo_unitario_adaptado * $analisis_precios->coeficiente_k;
            $compare = ($importe / 100 ) * config('custom.delta_2d');

            if (abs($importe - $coeficiente) > $compare) {
              $errores['analisis_item_' . $keyAnalisisItem] = trans('analisis_item.mensajes.error.precio', ['item'=> $valueAnalisisItem->item->descripcion_codigo]);
            }
        }
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      //aprueba analisis si es guardado definitivo y no hubo error
      if(!$input['borrador'] && $redeterminacion->contrato_moneda->moneda->lleva_analisis) {
          $accion = 'aprobar_precios';
          foreach ($analisis_precios->analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
            if(in_array($accion, $valueAnalisisItem->acciones_posibles))
              $valueAnalisisItem->createProximaInstanciaHistorial($accion);
          }

          $analisis_precios->createProximaInstanciaHistorial($accion);
      }

      if($redeterminacion->redeterminacion_anterior)
         $importe_anterior = (float)$redeterminacion->redeterminacion_anterior->importe_total;
      else
         $importe_anterior = (float)$redeterminacion->itemizado->total;

      $redeterminacion->variacion = abs($redeterminacion->importe_total / $importe_anterior);

      $redeterminacion->borrador = $input['borrador'];
      $redeterminacion->save();

      $jsonResponse['status'] = true;
      $jsonResponse['borrador'] = $input['borrador'];
      $jsonResponse['message'] = [trans('mensajes.dato.redeterminacion') . trans('mensajes.success.guardada')];


      if(!$input['borrador'])
         $jsonResponse['url'] = route('contratos.ver.incompleto', ['id' => $redeterminacion->contrato->id, 'accion' => 'empalme']);
      else
         $jsonResponse['refresh'] = true;

      return response()->json($jsonResponse);
    }

    /**
     * @param  int    $redeterminacion_id
    */
    public function ver($redeterminacion_id) {
      $redeterminacion = Redeterminacion::findOrFail($redeterminacion_id);

      if(!Auth::user()->puedeVerCausante($redeterminacion->contrato->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      $edit = false;
      return view('redeterminaciones.redeterminaciones.edit', compact('redeterminacion', 'edit'));
    }

    public function analisisItemEdit($item_id) {
      $analisis_item = AnalisisItem::where('id', $item_id)->firstOrFail();
      $analisis_precios = AnalisisPrecios::where('id', $analisis_item->analisis_precios_id)->first();

      if(!$analisis_item->permite_editar) {
        return redirect()->route('contratos.index');
      }

      // workaround xq no accede al atributo causante_id mediante $analisis_precios->contrato
      $causante_id = Contrato::whereId($analisis_precios->contrato->id)->pluck('causante_id')->first();

      if(!Auth::user()->puedeVerCausante($causante_id)) {
        return redirect()->route('contratos.index');
      }

      $edit = true;
      $redetermina = true;
      $estado_key = $analisis_item->estado['nombre'];
      return view('analisis_precios.analisis_item.createEdit', compact('analisis_item', 'edit', 'estado_key', 'redetermina'));
    }

    public function analisisItemVer($item_id) {
      $analisis_item = AnalisisItem::where('id', $item_id)->firstOrFail();
      $analisis_precios = AnalisisPrecios::where('id', $analisis_item->analisis_precios_id)->first();

      if(!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
        return redirect()->route('contratos.index');
      }

      $edit = false;
      $redetermina = true;
      return view('analisis_precios.analisis_item.createEdit', compact('analisis_item', 'edit', 'redetermina'));
    }

    /**
     * @param  int    $redeterminacion_id
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateComponentes($analisisItem_id, Request $request) {
      $input = $request->get('val');

      $errores = array();
      foreach($input as $keyCategoria => $valueComponentes) {
        foreach($valueComponentes as $keyComponente => $valueComponente) {
          if ($valueComponente == "" || $valueComponente == null) {
           $error[$keyComponente . '_' . $keyComponente] = trans('validation.required', ['attribute' => $componente->nombre . ' (' . trans('index.categoria') . ' ' . $categoria->nombre . ')']);
           $errores = array_merge($errores, $error);
         } elseif(!($valueComponente == "" || $valueComponente == null)) {
            $input[$keyCategoria][$keyComponente] = str_replace(".", "", $input[$keyCategoria][$keyComponente]);
            $input[$keyCategoria][$keyComponente] = str_replace(",", ".", $input[$keyCategoria][$keyComponente]);

            $error = $this->validarTamanio($input[$keyCategoria], $keyComponente, $keyComponente);
            if(sizeof($error) > 0) {
              $categoria = CategoriaModelExtended::findWithClase($keyCategoria);
              $componente = ComponenteModelExtended::findWithClase($keyComponente);
              $error[$keyComponente . '_' . $keyComponente] = $componente->nombre . ' (' . trans('index.categoria') . ' ' . $categoria->nombre . '): ' . $error[$keyComponente . '_' . $keyComponente];
              $errores = array_merge($errores, $error);
            }
          }
        }
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      foreach($input as $keyCategoria => $valueComponentes) {
        $categoria = CategoriaModelExtended::findWithClase($keyCategoria);

        foreach($valueComponentes as $keyComponente => $valueComponente) {
           $componente = ComponenteModelExtended::findWithClase($keyComponente);

           $componente->costo_total_adaptado = $valueComponente;
           $componente->save();
        }

        $categoria->calcularTotal();
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('mensajes.dato.componente').trans('mensajes.success.editado')];

      return response()->json($jsonResponse);
    }

//////////// FIN Analisis de Precios ////////////

//////////// Eliminar Redeterminacion ////////////
    /**
    * @param int $id
    */
    public function preAprobarAnalisisPrecio($id) {
      $analisis_precios = AnalisisPrecios::find($id);

      if(Auth::user()->cant('analisis_precios-edit')) {
        $jsonResponse['status'] = false;

        $jsonResponse['message'] = [trans('mensajes.error.permisos')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      return response()->json($jsonResponse);
    }

    public function aprobarAnalisisPrecio($id) {
      $analisis_precios = AnalisisPrecios::find($id);

      if(Auth::user()->cant('analisis_precios-aprobar_precios')) {
        $jsonResponse['status'] = false;

        $jsonResponse['message'] = [trans('mensajes.error.permisos')];
        return response()->json($jsonResponse);
      }

      $errores = array();
      foreach ($analisis_precios->analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
        $precio_redeterminado_item = (float)$valueAnalisisItem->costo_unitario_adaptado;

        if($precio_redeterminado_item == "" || $precio_redeterminado_item == null )
          $errores['analisis_item_error_' . $keyAnalisisItem] = trans('redeterminaciones.mensajes.error.required', ['item'=> $valueAnalisisItem->item->descripcion_codigo,
                                                                                                            'attribute'  => trans('analisis_item.costo_unitario')]);

          $importe = $valueAnalisisItem->item->monto_unitario;
          $coeficiente = (float)$valueAnalisisItem->costo_unitario_adaptado * $analisis_precios->coeficiente_k;
          $compare = ($importe / 100 ) * config('custom.delta_2d');

          if (abs($importe - $coeficiente) > $compare) {
            $errores['analisis_item_' . $keyAnalisisItem] = trans('analisis_item.mensajes.error.precio', ['item'=> $valueAnalisisItem->item->descripcion_codigo]);
          }
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }


      foreach ($analisis_precios->analisis_items as $keyAnalisisItem => $valueAnalisisItem) {
        if(in_array($accion, $valueAnalisisItem->acciones_posibles))
          $valueAnalisisItem->createProximaInstanciaHistorial($accion);
      }

      $analisis_precios->createProximaInstanciaHistorial($accion);

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('analisis_precios.mensajes.success.' . $accion)];

      $jsonResponse['status'] = true;
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function preDelete($id) {
      $redeterminacion = Redeterminacion::find($id);

      if(Auth::user()->cant('redeterminaciones-delete')) {
        $jsonResponse['status'] = false;
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('mensajes.dato.redeterminacion');

        $jsonResponse['message'] = [trans('mensajes.error.permisos')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function delete($id) {
      $preDelete = $this->preDelete($id)->getData();
      if($preDelete->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = $preDelete->message;
        return response()->json($jsonResponse);
      }

     $redeterminacion = Redeterminacion::find($id);
     $analisis_precios = $redeterminacion->itemizado->analisisPreciosRedeterminado($id);

      try {
        if($redeterminacion->itemizado->contrato_moneda->lleva_analisis)
           $analisis_precios->delete();
        $redeterminacion->delete();

      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('mensajes.dato.redeterminacion') . trans('mensajes.success.eliminada')];

      return response()->json($jsonResponse);
    }
//////////// FIN Eliminar Redeterminacion ////////////

}
