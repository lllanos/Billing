<?php

namespace App\Http\Controllers\Contratos;

use Contrato\Anticipo\Anticipo;
use Contrato\Anticipo\ItemAnticipo;
use Contrato\Contrato;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;
use View;
use Yacyreta\Causante;

class AnticiposController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

    public function store(Request $request)
    {
        $input = $request->except(['_token']);
        $contrato = Contrato::find($input['contrato']);

        if ((Auth::user()->cant('anticipos-create'))) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.crear');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            return response()->json($jsonResponse);
        }

        $rules = [
            'anticipo_fecha' => $this->required50(),
            'anticipo_descripcion' => $this->required255(),
        ];

        $validator = Validator::make($input, $rules);
        $errores = array();

        if ($validator->fails() || sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        foreach ($input['anticipo_porcentaje'] as $index => $porcentaje) {
            $porcentaje = (float) str_replace(",", ".", $porcentaje);
            $delta = round($contrato->anticipo, 2);

            if (abs(round($porcentaje, 2) - config('custom.delta')) > $delta) {
                $jsonResponse['status'] = false;
                $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
                Session::flash('error', trans('mensajes.error.revisar'));
                $jsonResponse['message'] = [trans('mensajes.error.porcentaje_menor_o_igual')];
                return response()->json($jsonResponse);
            }
        }

        // Doble firma
        $causante = Causante::find($contrato->causante_id);

        if ($causante)
            $this->inputDoblefirma($input, $causante);

        $anticipo = new Anticipo();
        $anticipo->fecha = $this->fechaDeA($input['anticipo_fecha'], 'd/m/Y', 'Y-m-d');
        $anticipo->descripcion = $input['anticipo_descripcion'];
        $anticipo->contrato_id = $input['contrato'];
        $anticipo->doble_firma = \Arr::get($input, 'doble_firma');
        $anticipo->firma_ar = \Arr::get($input, 'firma_ar');
        $anticipo->firma_py = \Arr::get($input, 'firma_py');
        $anticipo->save();

        $arrayPorcentaje = $input['anticipo_porcentaje'];
        $arrayTotal = $input['anticipo_total'];
        $arrayContratoMonedas = $input['anticipo_contratos_monedas'];

        foreach ($arrayPorcentaje as $index => $porcentaje) {
            $porcentaje = (float) str_replace(",", ".", $porcentaje);

            $ItemAnticipo = new ItemAnticipo();
            $ItemAnticipo->anticipo_id = $anticipo->id;
            $ItemAnticipo->contrato_moneda_id = intval($arrayContratoMonedas[$index]);
            $ItemAnticipo->total = $arrayTotal[$index];
            $ItemAnticipo->porcentaje = $porcentaje;
            $ItemAnticipo->save();
        }

        foreach ($contrato->contratos_monedas as $valueContratoMoneda)
            $contrato->reCalculoMontoYSaldo($valueContratoMoneda->id);

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success', trans('mensajes.dato.anticipo').trans('mensajes.success.creado'));
        $jsonResponse['message'] = [trans('mensajes.dato.anticipo').trans('mensajes.success.creado')];
        return response()->json($jsonResponse);
    }

    public function sign($id)
    {
        $anticipo = Anticipo::find($id);
        $anticipo->load('contrato.causante');

        // Contrato
        $contrato = $anticipo->contrato;

        // Causante
        $causante = $contrato->causante;

        if (!$causante) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_sin_causante', [
                    'name' => trans('index.anticipo')
                ])
            ]);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_no_admite', [
                    'name' => trans('index.anticipo')
                ])
            ]);
        }

        // Verifica se si es uno de los jefes que deben firmar
        $firma_ar = $causante->jefe_contrato_ar;
        $firma_py = $causante->jefe_contrato_py;

        if (!in_array(Auth::user()->id, [$firma_ar, $firma_py])) {
            return $this->errorJsonResponse([
                trans('mensajes.error.doble_firma_no_es_jefe', [
                    'name' => trans('index.contrato')
                ])
            ]);
        }

        // Firma si es el del lado argentino
        if ($firma_ar == Auth::user()->id) {
            $anticipo->firma_ar = Auth::user()->id;
        }

        // Firma si es el del lado paraguay
        if ($firma_py == Auth::user()->id) {
            $anticipo->firma_py = Auth::user()->id;
        }

        // Firma conseguida
        if ($anticipo->firma_ar && $anticipo->firma_py) {
            $anticipo->doble_firma = false;
        }

        // Guardar cambios
        $anticipo->save();

        // Respuesta
        $response = [];
        $response['status'] = true;
        $response['refresh'] = true;

        $message = trans('mensajes.success.firmado', [
            'type' => 'anticipo',
            'name' => $contrato->denominacion
        ]);

        $response['message'] = [
            $message,
        ];

        Session::flash('success', $message);

        return response()->json($response);
    }

    public function preDelete($id)
    {
        $anticipo = Anticipo::find($id);

        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
    }

    public function delete($id)
    {
        if ($this->preDelete($id)->getData()->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [$this->preDelete($id)->getData()->message];
            return response()->json($jsonResponse);
        }

        $anticipo = Anticipo::find($id);

        try {
            $anticipo->delete();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        $jsonResponse['message'] = [trans('mensajes.dato.anticipo').trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }

}
