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
use Dompdf\Dompdf;

use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoRedeterminado;
use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\VerificacionDesvio;

use YacyretaPackageController\Contratos\SolicitudesCertificadosController as PackageSCSController;
class SolicitudesCertificadosController extends PackageSCSController {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
    * @param int $id
    */
    public function solicitarEnviarAprobar($id) {
      $certificado = CertificadoRedeterminado::findOrFail($id);
      if(!$certificado->permite_enviar_aprobar) {
        $jsonResponse['status'] = false;
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);
        $jsonResponse['message'] = [trans('mensajes.error.contrato_no_asociado')];
        return response()->json($jsonResponse);
      }

      $certificado->createInstancia('a_validar');

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('certificado.mensajes.enviado_aprobar')];

      return response()->json($jsonResponse);
    }
}
