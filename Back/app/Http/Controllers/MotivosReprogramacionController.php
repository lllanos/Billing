<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

use Contrato\MotivoReprogramacion;

class MotivosReprogramacionController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      if($request->getMethod() == "GET") {
        $motivos = MotivoReprogramacion::all();//::paginate(config('custom.items_por_pagina'));
      } else {
        $search_input = $input['search_input'];
        $motivos = MotivoReprogramacion::where('nombre', 'like', '%' . $input['search_input'] . '%')->paginate(config('custom.items_por_pagina'));
      }
     
      return view('motivos.index', compact('motivos', 'search_input')); 
    }


    public function create() {

      return view('motivos.create');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function store(Request $request) {
      $input = $request->all();

      $rules = array(
          'descripcion'  => 'required|min:3|unique:con_motivos_reprogramacion,descripcion|max:255',
          'responsable'  => 'required|min:3',
          'user_creator_id'  => 'required',
      );

      $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

      if($validator->fails()) {
          return Redirect::back()
                         ->withErrors($validator)
                         ->withInput(Input::all());
      }

      try {
        $motivo = new MotivoReprogramacion();
        $motivo->descripcion = $input['descripcion'];
        $motivo->responsable = $input['responsable'];
        $motivo->user_creator_id = $input['user_creator_id'];
        $motivo->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        return redirect()->route('motivos.index')
                         ->with(['error' => trans('mensajes.error.insert_db')]);
      }

      return redirect()->route('motivos.index')
                       ->with(['success' => trans('mensajes.dato.motivo').trans('mensajes.success.creado')]);

    }

    /**
    * @param int $id
    */
    public function edit($id) {
      $motivo = MotivoReprogramacion::find($id);

      return view('motivos.edit', compact('motivo'));
    }

     /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function update(Request $request, $id) {
      $motivo = MotivoReprogramacion::find($id);

      $input = $request->all();

      $rules = array(
          'descripcion'  => 'required|min:3,'.$id.',id,deleted_at,NULL|unique:con_motivos_reprogramacion,descripcion|max:255',
          'user_modifier_id'  => 'required',
      );

      $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

      if($validator->fails()) {
          return Redirect::back()
              ->withErrors($validator)
              ->withInput(Input::all());
      }

      try {
        $motivo->descripcion = $input['descripcion'];
        $motivo->responsable = $input['responsable'];
        $motivo->user_modifier_id = $input['user_modifier_id'];
        $motivo->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        return redirect()->route('motivos.index')
                         ->with(['error' => trans('mensajes.error.insert_db')]);
      }

      return redirect()->route('motivos.index')
                       ->with(['success' => trans('mensajes.dato.motivo').trans('mensajes.success.actualizado')]);

    }

    /**
    * @param int $id
    */
    public function preDelete($id) {
      $motivo = MotivoReprogramacion::find($id);

      $jsonResponse['status'] = true;
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function delete($id) {
      if($this->preDelete($id)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [$this->preDelete($id)->getData()->message];
        return response()->json($jsonResponse);
      }

      $motivo = MotivoReprogramacion::find($id);

      try{
        $motivo->delete();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('mensajes.dato.motivo').trans('mensajes.success.eliminado')];

      return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportar(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $motivos = MotivoReprogramacion::all()->map(function ($item, $key) {
        return [
          trans('forms.descripcion')    => $item->descripcion,
          trans('forms.responsable')    => $item->responsable,
        ];
      });
      
      $filtro = null;
      return $this->toExcel(trans('index.motivos'),
                            $this->filtrarExportacion($motivos, $filtro));
    }

}
