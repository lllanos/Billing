<?php

namespace App\Http\Controllers\Contratos;

use CalculoRedeterminacion\Composicion;
use CalculoRedeterminacion\Polinomica;
use Contrato\Contrato;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Log;
use Redirect;
use Response;
use Storage;
use View;

class PolinomicaController extends ContratosControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string $accion
     * @param  int $contrato_id
    */
    public function updateOrStore(Request $request, $contrato_id) {
      $user = Auth::user();
      $contrato = Contrato::find($contrato_id);

      if($user->cant('polinomica-edit')) {
        Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'polinomica-edit']);
        $jsonResponse['message'] = [trans('index.error403')];
        $jsonResponse['permisos'] = true;
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      if(!Auth::user()->puedeVerCausante($contrato->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');
        return response()->json($jsonResponse);
      }

      $jsonResponse = $this->update_polinomica($request, $contrato_id);
      $jsonResponse = $jsonResponse->getData();
      return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $contrato_id
    */
    public function update_polinomica(Request $request, $contrato_id) {
      $input = $request->all();
      // Validaciones Custom
      $errores = array();

      // Validacion de nombre de insumos
      foreach ($input['polinomicas'] as $keyPolinomica => $valuePolinomica) {
        foreach ($valuePolinomica as $keyComposicion => $valueComposicion) {
          if(isset($valueComposicion['tabla_indices_id']) || isset($valueComposicion['porcentaje']) ||
             isset($valueComposicion['nombre'])) {
            $nombre = $valueComposicion['nombre'];
            if($input['borrador'] == 0) {
              if(strlen($nombre) < 3)
                $errores['polinomicas_nombre_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation.min.string', ['min' => 3, 'attribute' => trans('forms.nombre')]);
            } else {
              if(strlen($nombre) < 3 && strlen($nombre) != 0)
                $errores['polinomicas_nombre_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation.min.string', ['min' => 3, 'attribute' => trans('forms.nombre')]);
            }

            if(strlen($nombre) > 255)
              $errores['polinomicas_nombre_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation.max.string', ['max' => 255, 'attribute' => trans('forms.nombre')]);
          }
        }
      }

      // Validacion de Sumas
      if($input['borrador'] == 0) {
        foreach ($input['polinomicas'] as $keyPolinomica => $valuePolinomica) {
          foreach ($valuePolinomica as $keyComposicion => $valueComposicion) {
            if(isset($valueComposicion['tabla_indices_id']) || isset($valueComposicion['porcentaje']) ||
               isset($valueComposicion['nombre'])) {
              $total = 0;

              foreach ($valuePolinomica as $keyComposicion => $valueComposicion) {
                $porcentaje = str_replace(",", ".", $valueComposicion['porcentaje']);
                $porcentaje = str_replace("_", "0", $porcentaje);
                // Toda composicion tiene que tener indice
                if(!isset($valueComposicion['tabla_indices_id']) || (isset($valueComposicion['tabla_indices_id']) && $valueComposicion['tabla_indices_id'] == null)) {
                  $errores['tabla_indices_id_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation.required', ['attribute' => trans('forms.indice')]);
                }

                // Toda composicion tiene que tener porcentaje
                if($porcentaje == '' || $porcentaje == null)
                  $errores['polinomicas_porcentaje_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation.required', ['attribute' => trans('forms.composicion')]);

                if($porcentaje == null)
                  $porcentaje = 0;
                else
                  $porcentaje = str_replace(",", ".", $porcentaje);

                // El porcentaje tiene que ser <= 1
                if((int)$porcentaje > 1)
                  $errores['polinomicas_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation_custom.mayor_1', ['attribute' => trans('forms.porcentaje')]);

                if((int)$porcentaje < 0)
                  $errores['polinomicas_' . $keyPolinomica . '_' . $keyComposicion] = trans('validation_custom.menor_0', ['attribute' => trans('forms.porcentaje')]);

                $total = $total + $porcentaje;
              }

              // El total tiene debe sumar 1
              if (abs($total - 1) > config('custom.delta')) {
                $errores['polinomicas_suma_' . $keyPolinomica] = trans('validation_custom.polinomica_composicion_1');
              }
            }
            if(sizeof($valuePolinomica) == 1 && $valuePolinomica[0]['porcentaje'] == null)
              $errores['polinomicas_suma_' . $keyPolinomica] = trans('validation_custom.polinomica_vacia');
          }
        }
      }

      if(sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $date = date("Y-m-d H:i:s");
      $composiciones = array();
      $composiciones_sin_indice = array();

      foreach($input['polinomicas'] as $keyPolinomica => $valuePolinomica) {
        $polinomica = Polinomica::find($keyPolinomica);
        $polinomica->composiciones->each->delete();

        foreach ($valuePolinomica as $keyComposicion => $valueComposicion) {
          $porcentaje = str_replace(",", ".", $valueComposicion['porcentaje']);
          $porcentaje = str_replace("_", "0", $porcentaje);
          // $porcentaje = $valueComposicion['porcentaje'];
          if($porcentaje == '' || $porcentaje == null)
            $porcentaje = 0;

          if($valueComposicion['porcentaje'] == null)
            $porcentaje = '';

          if(isset($valueComposicion['tabla_indices_id'])) {
            $composiciones[] = ([
              'porcentaje'          => $porcentaje,
              'polinomica_id'       => $keyPolinomica,
              'tabla_indices_id'    => $valueComposicion['tabla_indices_id'],
              'nombre'              => $valueComposicion['nombre'],
              'user_creator_id'     => Auth::user()->id,
              'user_modifier_id'    => Auth::user()->id,
              'updated_at'          => $date,
              'created_at'          => $date,
            ]);
          } elseif($input['borrador'] == 1) {
            if(isset($valueComposicion['porcentaje']) || isset($valueComposicion['nombre'])) {
              $composiciones_sin_indice[] = ([
                'porcentaje'        => $porcentaje,
                'polinomica_id'     => $keyPolinomica,
                'nombre'            => $valueComposicion['nombre'],
                'user_creator_id'   => Auth::user()->id,
                'user_modifier_id'  => Auth::user()->id,
                'updated_at'        => $date,
                'created_at'        => $date,
              ]);
            }
          }
        }
      }

      if(sizeof($composiciones) > 0) {
        try {
          Composicion::insert($composiciones);
        } catch(\QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          Session::flash('error', trans('mensajes.error.insert_db'));
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);
        }
      }

      if($input['borrador'] == 1 && sizeof($composiciones_sin_indice) > 0) {
        try {
          Composicion::insert($composiciones_sin_indice);
        } catch(\QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          Session::flash('error', trans('mensajes.error.insert_db'));
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);
        }
      }

      $jsonResponse['status'] = true;
      $jsonResponse['borrador'] = $input['borrador'];
      if($input['borrador'] == 1) {
        $jsonResponse['message'] = [trans('polinomica.mensajes.polinomica_borrador')];
      } else {
        foreach ($input['polinomicas'] as $keyPolinomica => $valuePolinomica) {
          $polinomica = Polinomica::find($keyPolinomica);
          $polinomica->borrador = 0;
          $polinomica->save();
        }
        $this->completarContrato($contrato_id);

        $contrato = Contrato::find($contrato_id);
        $this->createInstanciaHistorial($contrato, 'polinomica', 'aprobado');

        $jsonResponse['message'] = [trans('polinomica.mensajes.polinomica')];
      }

      return response()->json($jsonResponse);
    }
}
