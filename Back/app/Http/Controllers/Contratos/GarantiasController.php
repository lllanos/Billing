<?php

namespace App\Http\Controllers\Contratos;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use DB;
use DateTime;
use Log;
use Response;
use Storage;
use View;

use Contrato\Contrato;
use Contrato\Garantias\Garantias;
use Contrato\Garantias\GarantiaAdjunto;

use App\Http\Controllers\Contratos\ContratosControllerExtended;
class GarantiasController extends ContratosControllerExtended {

	public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function store(Request $request) {
      $input = $request->except(['_token']);    

      if((Auth::user()->cant('garantias-manage'))) {
          $jsonResponse['status'] = false;
          $jsonResponse['title'] = trans('index.crear');
          $jsonResponse['message'] = [trans('mensajes.error.permisos')];
          return response()->json($jsonResponse);
      }

      $rules = array(
          'is_valido'  => 'required',
          'observacion'  => 'max:255',
        );

      $validator = Validator::make($input, $rules);
      $errores = array();

      if($validator->fails() || sizeof($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
        Session::flash('error', trans('mensajes.error.revisar'));
        $jsonResponse['message'] = [trans('mensajes.error.revisar')];
        return response()->json($jsonResponse);
      }

      $garantia = Garantias::whereContrato_id($input['contrato'])->first();

      if(!$garantia){

          $garantia = new Garantias();
          $garantia->is_valido = $input['is_valido'];
          $garantia->contrato_id = $input['contrato'];    

          if(isset($input['observacion'])) {
            $garantia->observacion = $input['observacion'];
          }

          $garantia->save();
      }else{  

          $garantia->is_valido = $input['is_valido'];
          $garantia->contrato_id = $input['contrato'];    

          if(isset($input['observacion'])) {
            $garantia->observacion = $input['observacion'];
          }

          $garantia->save();
      }

      
      if(isset($input['adjunto']) && $request->hasFile('adjunto')) {        
        foreach ($input['adjunto'] as $keyAdjunto => $valueAdjunto) {
          $adjuntos_json = $this->uploadFile($request, $garantia->id, 'adjunto|' . $keyAdjunto, 'contrato');
          GarantiaAdjunto::create([ 'garantias_id'  => $garantia->id,
                                    'adjunto'     => $adjuntos_json,
                                 ]);
        }
      }

      $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success', trans('mensajes.dato.garantia') . trans('mensajes.success.actualizado'));
        $jsonResponse['message'] = [trans('mensajes.dato.garantia') . trans('mensajes.success.actualizado')];
      return response()->json($jsonResponse);
    }

}