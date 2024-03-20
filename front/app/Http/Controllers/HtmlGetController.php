<?php

namespace App\Http\Controllers;

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
use View;

use Contrato\Contrato;
use SolicitudContrato\UserContrato;

class HtmlGetController extends Controller {

    public function __construct() {
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  int $id
    */
    public function SelectContrato($id) {

      $contrato_id = $id;
      $contrato = Contrato::find($contrato_id);

      $jsonResponse = array();

      $jsonResponse['select_placeholder']['contrato_moneda_id'] = trans('forms.select.contrato_moneda');
      foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
  			$jsonResponse['select']['contrato_moneda_id'][$valueContratoMoneda->id]['id'] = $valueContratoMoneda->id;
        $jsonResponse['select']['contrato_moneda_id'][$valueContratoMoneda->id]['nombre'] = $valueContratoMoneda->nombre;
				$jsonResponse['select']['contrato_moneda_id'][$valueContratoMoneda->id]['selected'] = false;
  		}

      return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
    */
    public function GetSaltos($id) {
      $contrato = Contrato::find($id);
      $id = $contrato->contrato_original_sin_adendas->id;
      $user_contrato = UserContrato::whereUserContratistaId(Auth::user()->user_publico->id)
                                   ->whereContratoId($id)->first();

      $jsonResponse = array();
      if($contrato->is_adenda_ampliacion) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        $jsonResponse['message'] = trans('mensajes.error.contrato_no_asociado');
        return response()->json($jsonResponse);
      } elseif($user_contrato == null) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        $jsonResponse['message'] = trans('mensajes.error.contrato_no_asociado');
        return response()->json($jsonResponse);
      } else {
        if(!$user_contrato->poder_esta_vigente) {
          $jsonResponse['status'] = false;
          Session::flash('error', trans('mensajes.error.poder_no_vigente'));
          $jsonResponse['message'] = trans('mensajes.error.poder_no_vigente');
          return response()->json($jsonResponse);
        } else {
          $jsonResponse['status'] = true;
          $jsonResponse['html'] = View::make('redeterminaciones.solicitudes.saltos', compact('contrato'))->render();

          return response()->json($jsonResponse);
        }
      }
    }

}
