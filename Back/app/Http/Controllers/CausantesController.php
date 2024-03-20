<?php

namespace App\Http\Controllers;

use App\User;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;
use Response;
use Storage;
use View;
use Yacyreta\Causante;
use Yacyreta\LogEntry;

class CausantesController extends Controller
{
    public function __construct()
    {
        View::share('ayuda', 'configuracion');
        $this->middleware('auth', ['except' => 'logout']);
    }

    protected function validationErrorMessages()
    {
        return [
          'nombre.unique' => trans('validation_custom.distinct.grupo_nombre')
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $search_input = '';
        if ($request->getMethod() == "GET") {
            $causantes = Causante::all();//::paginate(config('custom.items_por_pagina'));
        } else {
            $search_input = $input['search_input'];
            $causantes = Causante::where('nombre', 'like',
              '%'.$input['search_input'].'%')->paginate(config('custom.items_por_pagina'));
        }

        return view('causantes.index', compact('causantes', 'search_input'));
    }

    public function create()
    {
        $usuarios = User::all()->mapWithKeys(function ($item) {
              return [$item->id => $item->apellido . ', ' . $item->nombre];
          })->toArray();

        return view('causantes.create', compact('usuarios'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $rules = array(
          'nombre' => 'required|min:3|max:100|unique:causantes,nombre,NULL,deleted_at',
          'color' => 'required|min:5|max:7',
        );

        $dobleFirma = (boolean) $request->get('doble_firma');

        if ($dobleFirma) {
            $rules['jefe_contrato_ar'] = "required";
            $rules['jefe_contrato_py'] = "required";
            $rules['jefe_obras_ar'] = "required";
            $rules['jefe_obras_py'] = "required";
        }

        $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput(Input::all());
        }

        try {
            $causante = new Causante();
            $causante->nombre = $input['nombre'];
            $causante->color = strtoupper($input['color']);
            $causante->doble_firma = $dobleFirma;
            $causante->jefe_contrato_ar = $request->get('jefe_contrato_ar');
            $causante->jefe_contrato_py = $request->get('jefe_contrato_py');
            $causante->jefe_obras_ar = $request->get('jefe_obras_ar');
            $causante->jefe_obras_py = $request->get('jefe_obras_py');
            $causante->save();

            LogEntry::log([
              'type' => 'create',
              'message' => __('log.causante.create', ['name' => $causante->nombre]),
              'object' => $causante,
              'data' => $causante,
            ]);
        }
        catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            return redirect()->route('causantes.index')->with(['error' => trans('mensajes.error.insert_db')]);
        }

        return redirect()->route('causantes.index')->with(['success' => trans('mensajes.dato.causante').trans('mensajes.success.actualizado')]);

    }

    /**
     * @param  int  $id
     */
    public function edit($id)
    {
        $causante = Causante::findOrFail($id);

        $usuarios = User::all()->mapWithKeys(function ($item) {
            return [$item->id => $item->apellido . ', ' . $item->nombre];
        })->toArray();

        return view('causantes.edit', compact('causante', 'usuarios'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request, $id)
    {
        $causante = Causante::findOrFail($id);

        $input = $request->all();

        $rules = array(
          'nombre' => 'required|min:3|max:100|unique:causantes,nombre,'.$id.',id,deleted_at,NULL',
          'color' => 'required|min:5|max:7',
        );

        $dobleFirma = (boolean) $request->get('doble_firma');

        if ($dobleFirma) {
            $rules['jefe_contrato_ar'] = "required";
            $rules['jefe_contrato_py'] = "required";
            $rules['jefe_obras_ar'] = "required";
            $rules['jefe_obras_py'] = "required";

            if (
             $rules['jefe_contrato_ar'] == $causante->jefe_contrato_ar
             && $rules['jefe_contrato_py'] == $causante->jefe_contrato_py
             && $rules['jefe_obras_ar'] == $causante->jefe_obras_ar
             && $rules['jefe_obras_py'] == $causante->jefe_obras_py
            ) {
                $rules['comment'] = "required";
            }
        }

        $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput(Input::all());
        }

        try {
            if (
                $causante->jefe_contrato_ar != $request->get('jefe_contrato_ar')
                || $causante->jefe_contrato_py != $request->get('jefe_contrato_py')
                || $causante->jefe_obras_ar != $request->get('jefe_obras_ar')
                || $causante->jefe_obras_py != $request->get('jefe_obras_py')
            ) {
                $message = $request->get('comment');
            }
            else {
                $message = __('log.causante.update', ['name' => $causante->nombre]);
            }

            $causante->nombre = $input['nombre'];
            $causante->color = strtoupper($input['color']);
            $causante->doble_firma = (boolean) $request->get('doble_firma');
            $causante->jefe_contrato_ar = $request->get('jefe_contrato_ar');
            $causante->jefe_contrato_py = $request->get('jefe_contrato_py');
            $causante->jefe_obras_ar = $request->get('jefe_obras_ar');
            $causante->jefe_obras_py = $request->get('jefe_obras_py');
            $causante->save();

            LogEntry::log([
                'type' => 'update',
                'message' => $message,
                'object' => $causante,
                'data' => $causante,
            ]);
        }
        catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            return redirect()->route('causantes.index')->with(['error' => trans('mensajes.error.insert_db')]);
        }

        return redirect()->route('causantes.index')->with(['success' => trans('mensajes.dato.causante').trans('mensajes.success.actualizado')]);

    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request)
    {
        $input = $request->all();
        $filtro = $input['excel_input'];

        $causantes = Causante::all()->map(function ($item, $key) {
            return [
              trans('forms.nombre') => $item->nombre, trans('forms.cantidad_usuarios') => $item->cantidad_usuarios,
              trans('forms.cantidad_contratos') => $item->cantidad_contratos,
            ];
        });

        return $this->toExcel(trans('index.causantes'), $this->filtrarExportacion($causantes, $filtro));
    }

    /**
     * @param  int  $id
     */
    public function preDelete($id)
    {
        $causante = Causante::find($id);

        if ($causante->cantidad_usuarios > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('index.causante');
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.causante_usuarios')];
            return response()->json($jsonResponse);
        } elseif ($causante->cantidad_contratos > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('index.causante');
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.causante_contratos')];
            return response()->json($jsonResponse);
        } else {
            $jsonResponse['status'] = true;
            return response()->json($jsonResponse);
        }
    }

    /**
     * @param  int  $id
     */
    public function delete($id)
    {
        if ($this->preDelete($id)->getData()->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [$this->preDelete($id)->getData()->message];
            return response()->json($jsonResponse);
        }

        $causante = Causante::find($id);

        try {
            $causante->delete();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        $jsonResponse['message'] = [trans('mensajes.dato.causante').trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }

}
