<?php

namespace App\Http\Controllers;

use App\Grupo;
use Contratista\Contratista;
use Contratista\ContratistaTelefono;
use Contratista\ContratistaUte;
use Contratista\TipoContratista;
use Contrato\Contrato;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Log;
use Redirect;
use Response;
use View;
use Yacyreta\Pais;
use Yacyreta\TipoDocumento;

class ContratistasController extends Controller
{

    public function __construct()
    {
        View::share('ayuda', 'contratistas');
        $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $search_input = '';
        if ($request->getMethod() == "GET" || $input['search_input'] == '') {
            $contratistas = Contratista::get();
            if (isset($input['search_input']))

                $search_input = $this->minusculaSinAcentos($input['search_input']);
        } else {
            $search_input = $input['search_input'];
            $input_lower = $this->minusculaSinAcentos($input['search_input']);

            $contratistas = Contratista::get()
                ->filter(function ($contratista) use ($input_lower) {
                    // if(isset($contratista->created_at) && substr_count(date_format($contratista->created_at, "d/m/Y") , $input_lower)>0) {
                    //   return true;
                    // }
                    if (substr_count($this->minusculaSinAcentos($contratista->tipo->nombre), $input_lower) > 0) {
                        return true;
                    }
                    if (substr_count($this->minusculaSinAcentos($contratista->nombre_fantasia), $input_lower) > 0) {
                        return true;
                    }
                    if (substr_count($this->minusculaSinAcentos($contratista->razon_social), $input_lower) > 0) {
                        return true;
                    }
                    if (
                        isset($contratista->nro_documento) &&
                        substr_count(str_replace('-', "", $contratista->tipo_num_documento), str_replace('-', "", $input_lower)) > 0
                    ) {
                        return true;
                    }
                    if (substr_count($this->minusculaSinAcentos($contratista->email), $input_lower) > 0) {
                        return true;
                    }
                });
        }
        //$contratistas = Contratista::with('tipo')->with('tipo_documento')->get();
        $contratistas = Contratista::with('tipo')->with('tipo_documento')->orderBy('id', 'desc')->get();
        //$contratistas = $this->ordenar($contratistas);

        if ($request->getMethod() == "GET") {
            if ($search_input != '') {
                $contratistas = $this->filtrar($contratistas, $search_input);
            }
            $contratistas = $this->paginateCustom($contratistas);
        } else {
            $contratistas = $this->filtrar($contratistas, $this->minusculaSinAcentos($search_input));
            $contratistas = $this->paginateCustom($contratistas, 1);
        }

        return view('contratistas.index', compact('contratistas', 'search_input'));
    }

    /**
     * @param  Contrato\Contrato $contratistas
     */
    private function ordenar($contratistas)
    {
        $contratistas = $contratistas->groupBy(function ($contratista, $key) {
            $reg = $this->fechaDeA($contratista->fecha_registro, 'd/m/Y', 'm/d/Y');
            return strtotime($reg);
        });
        $contratistas = $contratistas->all();
        ksort($contratistas);
        $contratistas = collect($contratistas);

        $toArray = array();
        foreach ($contratistas as $keyContratista => $valueContratista) {
            $toArray[$keyContratista] = $valueContratista->sortBy(function ($contratista, $key) {
                return $contratista->razon_social;
            })->all();
        }

        $ordered = collect();
        foreach ($toArray as $keyArray => $valueArray) {
            foreach ($valueArray as $key => $value) {
                $ordered->push($value);
            }
        }
        return $ordered;
    }

    /**
     * @param  string $search_input
     */
    private function filtrar($contratistas, $search_input)
    {
        if ($search_input == '')
            return $contratistas;

        return $contratistas->filter(function ($contratista) use ($search_input) {
            // if(isset($contratista->created_at) && substr_count(date_format($contratista->created_at, "d/m/Y") , $search_input) > 0) {
            //   return true;
            // }
            if (substr_count($this->minusculaSinAcentos($contratista->tipo->nombre), $search_input) > 0) {
                return true;
            }
            if (substr_count($this->minusculaSinAcentos($contratista->nombre_fantasia), $search_input) > 0) {
                return true;
            }
            if (substr_count($this->minusculaSinAcentos($contratista->razon_social), $search_input) > 0) {
                return true;
            }
            if (
                isset($contratista->nro_documento) &&
                substr_count(str_replace('-', "", $contratista->tipo_num_documento), str_replace('-', "", $search_input)) > 0
            ) {
                return true;
            }
            if (substr_count($this->minusculaSinAcentos($contratista->email), $search_input) > 0) {
                return true;
            }
        });
    }

    public function create()
    {
        $paises = Pais::all()->sortBy('id')->pluck('nombre', 'id')->prepend(trans('forms.select.pais'), '');
        $tiposContratistas = TipoContratista::getOpciones();
        $tiposDocumentos = TipoDocumento::getOpciones();
        $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
        $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
        $contratistas_posibles = Contratista::where('borrador', 0)->where("tipo_id", "<>", $tipoUte)->get()->sortBy('nombre_documento_lower')->pluck('nombre_documento', 'id')->prepend(trans('forms.select.contratista'), '');

        return view('contratistas.create', compact('paises', 'tiposContratistas', 'tiposDocumentos', 'contratistas_posibles', 'tipoUte'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
        if ($input['tipo_id'] == $tipoUte) {
            if ($input['integrante_ute'] != null && count($input['integrante_ute']) > 0) {
                if (!$input['integrante_ute'][1]) {
                    $rules = array(
                        'integrante_ute'    => 'required',
                    );
                    $messages = [
                        'integrante_ute.required'            => trans('validation.required', ['attribute' => trans('forms.integrante_ute')]),
                    ];
                    $validator = Validator::make($input['integrante_ute'], $rules, $messages);

                    if ($validator->fails()) {
                        return Redirect::to('contratistas/crear')
                            ->withErrors($validator)
                            ->withInput(Input::all());
                    }
                }
            }
        }

        if ($input['borrador'] == 0) {
            $rules = [
                'tipo_id'           => 'required',
                'tipo_documento_id' => 'required',                
                'nro_documento'     => 'required|'.$this->documentCuitFormat(),
                'cbu'     => $this->cbuFormat(),
                //'nombre'            => $this->requiredN(100),
                'nombre'            => $this->requiredLettersSpacesN(100),
                'domicilio_legal'   => $this->required255(),
                'email'             => $this->required255(),
                'integrante_ute'    => 'required',
            ];

            $messages = [
                'tipo_id.required'            => trans('validation.required', ['attribute' => trans('forms.tipo_contratista')]),
                'tipo_documento_id.required'  => trans('validation.required', ['attribute' => trans('forms.tipo_documento')]),
                'nro_documento.required'      => trans('validation.required', ['attribute' => trans('forms.nro_documento')]),
                'cbu.numeric'                 => trans('validation.numeric', ['attribute' => trans('forms.cbu')]),
                'nombre.required'             => trans('validation.required', ['attribute' => trans('forms.nombre_razon_social')]),
                'domicilio_legal.required'    => trans('validation.required', ['attribute' => trans('forms.domicilio_legal')]),
                'email.required'              => trans('validation.required', ['attribute' => trans('forms.email')]),
                'email.required'              => trans('validation.required', ['attribute' => trans('forms.email')]),
            ];
        } else {
            $rules = [
                'tipo_id'           => 'required',
                'tipo_documento_id' => 'required',
                //'nro_documento'     => $this->required50(),
                'nro_documento'     => 'required|'.$this->documentCuitFormat(),
            ];

            $messages = [
                'tipo_id.required'            => trans('validation.required', ['attribute' => trans('forms.tipo_contratista')]),
                'tipo_documento_id.required'  => trans('validation.required', ['attribute' => trans('forms.tipo_documento')]),
                'nro_documento.required'      => trans('validation.required', ['attribute' => trans('forms.nro_documento')]),
            ];
        }
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return Redirect::to('contratistas/crear')
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $checkDuplicate = Contratista::where('documento_id', $input['tipo_documento_id'])->where('nro_documento', $input['nro_documento'])->first();
            if ($checkDuplicate != null) {
                $validator = array(trans('validation.unique_documento', ['attribute' => trans('forms.contratista')]));
                return Redirect::to('contratistas/crear')
                    ->withErrors($validator)
                    ->withInput(Input::all());
            }

            if ($request->has('nombre') && $input['nombre'] != '') {
                $checkDuplicate = Contratista::where('razon_social', $input['nombre'])->first();
                if ($checkDuplicate != null) {
                    $validator = array(trans('validation.unique_nombre', ['attribute' => trans('forms.contratista')]));
                    return Redirect::to('contratistas/crear')
                        ->withErrors($validator)
                        ->withInput(Input::all());
                }
            }

            $contratista = new Contratista();
            $contratista->tipo_id         = $input['tipo_id'];
            $contratista->documento_id    = $input['tipo_documento_id'];
            $contratista->nro_documento   = $input['nro_documento'];
            $contratista->razon_social    = $input['nombre'];
            $contratista->nombre_fantasia = $input['nombre_fantasia'];
            if ($request->has('pais_id') && $input['pais_id'] != '') {
                $contratista->pais_id = $input['pais_id'];
            }
            $contratista->representante_legal = $input['representante_legal'];
            $contratista->entidad_bancaria = $input['entidad_bancaria'];
            $contratista->cbu = $input['cbu'];
            $contratista->domicilio_legal = $input['domicilio_legal'];
            $contratista->email = $input['email'];
            $contratista->observaciones = $input['observaciones'];
            $contratista->borrador = $input['borrador'];

            try {
                $arrayPrefijo = $input['telefono_prefijo'];
                $arrayNro = $input['telefono_numero'];

                foreach ($arrayPrefijo as $index => $prefijo) {
                    if ($prefijo != null & $arrayNro[$index] != null) {
                        if(!(is_numeric($prefijo))){
                            $validator = array(trans('validation.numeric', ['attribute' => trans('forms.telefono_prefijo')]));
                            return Redirect::to('contratistas/crear')
                                ->withErrors($validator)
                                ->withInput(Input::all());
                        }
                        if(!(is_numeric($arrayNro[$index]))){
                            $validator = array(trans('validation.numeric', ['attribute' => trans('forms.telefono')]));
                            return Redirect::to('contratistas/crear')
                                ->withErrors($validator)
                                ->withInput(Input::all());
                        }                        
                    }
                }

                $contratista->save();                

                foreach ($arrayPrefijo as $index => $prefijo) {
                    if ($prefijo != null & $arrayNro[$index] != null) {
                        $contratistaTelefono = new ContratistaTelefono();
                        $contratistaTelefono->contratista_id = $contratista->id;
                        $contratistaTelefono->prefijo = $prefijo;
                        $contratistaTelefono->numero = $arrayNro[$index];
                        $contratistaTelefono->save();
                    }
                }
                
                if ($input['tipo_id'] == $tipoUte) {
                    $arrayIntegranteUTE = $input['integrante_ute'];

                    foreach ($arrayIntegranteUTE as $index => $integrante) {
                        $contratistaIntegrante = new ContratistaUte();
                        $contratistaIntegrante->ute_id = $contratista->id;
                        $contratistaIntegrante->contratista_id = $integrante;
                        $contratistaIntegrante->save();
                    }
                }
            } catch (QueryException $e) {
                Log::error('QueryException', ['Exception' => $e]);
                return redirect()->route('contratistas.index')
                    ->with(['error' => trans('error.user.guardando_en_db')]);
            }

            return redirect()->route('contratistas.index')
                ->with(['success' => trans('mensajes.dato.contratista') . trans('mensajes.success.creado')]);
        }
    }

    /**
     * @param int $id
     */
    public function preDelete($id)
    {
        $contratista = Contratista::find($id);
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('index.contrato');
        $jsonResponse['status'] = true;
        
        if (count($contratista->contratos)) {
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.contratista')];
            $jsonResponse['status'] = false;
        }
        $apareceUTE = ContratistaUte::where('contratista_id', $id)->get();
        if (count($apareceUTE)) {
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.contratista_ute')];
            $jsonResponse['status'] = false;
        }

        return response()->json($jsonResponse);
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        $preDelete = $this->preDelete($id)->getData();
        if ($preDelete->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = $preDelete->message;
            return response()->json($jsonResponse);
        }

        $contratista = Contratista::find($id);

        try {
            $contratista->delete();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['action']['function'] = "deleteRow";
        $jsonResponse['action']['params'] = 'contratista_' . $id;
        $jsonResponse['message'] = [trans('mensajes.dato.contratista') . trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }


    public function preDeleteTelefono($id)
    {
        $telefono = ContratistaTelefono::find($id);

        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
    }

    public function deleteContratistaTelefonos($id)
    {
        try {
            ContratistaTelefono::where('contratista_id', '=', $id)->delete();
            $jsonResponse['status'] = true;
            return response()->json($jsonResponse);
        }
        catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }
    }

    public function deleteTelefono($id)
    {
        if ($this->preDeleteTelefono($id)->getData()->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.telefono')];
            return response()->json($jsonResponse);
        }

        $telefono = ContratistaTelefono::find($id);

        try {
            $telefono->delete();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['action']['function'] = "deleteRow";
        $jsonResponse['action']['params'] = 'telefono_' . $id;
        $jsonResponse['message'] = [trans('mensajes.dato.telefono') . trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }

    public function preDeleteContratista($id)
    {
        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
    }

    public function deleteContratista($id, $uteId)
    {
        if ($this->preDelete($id)->getData()->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.contratista')];
            return response()->json($jsonResponse);
        }


        if (count(ContratistaUte::where('ute_id', $uteId)->get()) <= 1) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.contratista')];
            return response()->json($jsonResponse);
        }

        $contratista = ContratistaUte::where(['contratista_id' => $id, 'ute_id' =>  $uteId])->first();

        $contrato = Contrato::where('contratista_id', $uteId)->first();

        if ($contrato) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('index.contratista');
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.contratista')];
            return response()->json($jsonResponse);
        }

        try {
            $contratista->delete();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['action']['function'] = "deleteRow";
        $jsonResponse['action']['params'] = 'contratista_' . $id;
        $jsonResponse['message'] = [trans('mensajes.dato.contratista') . trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }


    /**
     * @param  int $id
     */
    public function show($id)
    {
        $contratista = Contratista::findOrFail($id);
        $contratistasUTE = $contratista->contratistas_ute;
        $apareceUTE = ContratistaUte::where('contratista_id', $id)->get();

        return view('contratistas.show', compact('contratista', 'contratistasUTE', 'apareceUTE'));
    }

    /**
     * @param  int $id
     */
    public function edit($id)
    {
        $paises = Pais::all()->sortBy('id')->pluck('nombre', 'id')->prepend(trans('forms.select.pais'), '');
        $tiposContratistas = TipoContratista::getOpciones();
        $tiposDocumentos = TipoDocumento::getOpciones();
        $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
        $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
        $contratistas_posibles = Contratista::where('borrador', 0)->where("tipo_id", "<>", $tipoUte)->get()->sortBy('nombre_documento_lower')->pluck('nombre_documento', 'id')->prepend(trans('forms.select.contratista'), '');

        $contratista = Contratista::findOrFail($id);
        $contratistasUTE = $contratista->contratistas_ute;
        $contratistaTelefonos = $contratista->telefonos()->get();
        $i = 1;
        return view('contratistas.edit', compact('contratista', 'paises', 'tiposContratistas', 'tiposDocumentos', 'contratistas_posibles', 'contratistasUTE', 'contratistaTelefonos', 'i'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function update(Request $request)
    {
        $input = $request->all();

        $contratista = Contratista::findOrFail($input['id']);
        $id=$input['id'];

        if ($input['borrador'] == 0) {
            $rules = array(
                'tipo_documento_id' => 'required',                
                'cbu'     => $this->cbuFormat(),                
                'nro_documento'     => 'required|'.$this->documentCuitFormat(),                
                'nombre'            => $this->requiredLettersSpacesN(100),                
                'domicilio_legal'   => $this->required255(),
                'email'             => $this->required255(),
            );

            $messages = [
                'tipo_documento_id.required'  => trans('validation.required', ['attribute' => trans('forms.tipo_documento')]),
                'cbu.numeric'                 => trans('validation.numeric', ['attribute' => trans('forms.cbu')]),
                'nro_documento.required'      => trans('validation.required', ['attribute' => trans('forms.nro_documento')]),
                'nombre.required'             => trans('validation.required', ['attribute' => trans('forms.nombre_razon_social')]),
                'domicilio_legal.required'    => trans('validation.required', ['attribute' => trans('forms.domicilio_legal')]),
                'email.required'              => trans('validation.required', ['attribute' => trans('forms.email')]),
            ];
        } else {
            $rules = array(
                'tipo_documento_id' => 'required',
                //'nro_documento'     => $this->required50(),
                //'nro_documento'     => $this->intNumbers(),                
                'nro_documento'     => 'required|'.$this->documentCuitFormat(),                
            );

            $messages = [
                'tipo_documento_id.required'  => trans('validation.required', ['attribute' => trans('forms.tipo_documento')]),
                'nro_documento.required'      => trans('validation.required', ['attribute' => trans('forms.nro_documento')]),
            ];
        }
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return Redirect::to('contratistas/'.$id.'/editar')
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $checkDuplicate = Contratista::where('id', '<>', $contratista->id)->where('documento_id', $input['tipo_documento_id'])->where('nro_documento', $input['nro_documento'])->first();
            if ($checkDuplicate != null) {
                $validator = array(trans('validation.unique_documento', ['attribute' => trans('forms.contratista')]));
                return Redirect::to('contratistas/'.$id.'/editar')
                    ->withErrors($validator)
                    ->withInput(Input::all());
            }

            if ($request->has('nombre') && $input['nombre'] != '') {
                $checkDuplicate = Contratista::where('id', '<>', $contratista->id)->where('razon_social', $input['nombre'])->first();
                if ($checkDuplicate != null) {
                    $validator = array(trans('validation.unique_nombre', ['attribute' => trans('forms.contratista')]));
                    return Redirect::to('contratistas/'.$id.'/editar')
                        ->withErrors($validator)
                        ->withInput(Input::all());
                }
            }

            $contratista->documento_id    = $input['tipo_documento_id'];
            $contratista->nro_documento   = $input['nro_documento'];
            $contratista->razon_social    = $input['nombre'];
            $contratista->nombre_fantasia = $input['nombre_fantasia'];
            if ($request->has('pais_id') && $input['pais_id'] != '') {
                $contratista->pais_id = $input['pais_id'];
            }
            if (isset($input['representante_legal']))
                $contratista->representante_legal = $input['representante_legal'];

            $contratista->entidad_bancaria = $input['entidad_bancaria'];
            $contratista->cbu = $input['cbu'];
            $contratista->domicilio_legal = $input['domicilio_legal'];
            $contratista->email = $input['email'];
            $contratista->observaciones = $input['observaciones'];
            $contratista->borrador = $input['borrador'];
            
            if (isset($input['telefono_prefijo'])) {                                

                $arrayPrefijo = $input['telefono_prefijo'];
                $arrayNro = $input['telefono_numero'];
                foreach ($arrayPrefijo as $index => $prefijo) {
                    if ($prefijo != null & $arrayNro[$index] != null) {
                        if(!(is_numeric($prefijo))){
                            $validator = array(trans('validation.numeric', ['attribute' => trans('forms.telefono_prefijo')]));
                            return Redirect::to('contratistas/'.$id.'/editar')
                                ->withErrors($validator)
                                ->withInput(Input::all());
                        }
                        if(!(is_numeric($arrayNro[$index]))){
                            $validator = array(trans('validation.numeric', ['attribute' => trans('forms.telefono')]));
                            return Redirect::to('contratistas/'.$id.'/editar')
                                ->withErrors($validator)
                                ->withInput(Input::all());
                        }
                    }
                }
            }
            

            $contratista->save();

            if (isset($input['telefono_prefijo'])) {
                
                $this->deleteContratistaTelefonos($contratista->id);
                $arrayPrefijo = $input['telefono_prefijo'];
                $arrayNro = $input['telefono_numero'];
               
                foreach ($arrayNro as $index => $numero) {
                    $contratistaTelefono = new ContratistaTelefono();
                    $contratistaTelefono->contratista_id = $contratista->id;
                    $contratistaTelefono->prefijo = $arrayPrefijo[$index];
                    $contratistaTelefono->numero = $numero;
                    $contratistaTelefono->save();
                }
            }

            $tipoUte = TipoContratista::whereNombre('UT')->first()->id;
            if ($contratista->tipo_id == $tipoUte) {
                $arrayIntegranteUTE = $input['integrante_ute'];
                foreach ($arrayIntegranteUTE as $index => $integrante) {
                    if ($integrante) {
                        $existe = ContratistaUte::where('ute_id', $contratista->id)->where('contratista_id', $integrante)->first();

                        if (!$existe) {
                            $contratistaIntegrante = new ContratistaUte();
                            $contratistaIntegrante->ute_id = $contratista->id;
                            $contratistaIntegrante->contratista_id = $integrante;
                            $contratistaIntegrante->save();
                        } else {
                            return redirect()->back()
                                ->with(['error' => trans('mensajes.error.ute_repetido')]);
                        }
                    }
                }
            }

            return redirect()->route('contratistas.index')
                ->with(['success' => trans('mensajes.dato.contratista') . trans('mensajes.success.editado')]);
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request)
    {
        $input = $request->all();
        $filtro = $input['excel_input'];
        $contratistas = Contratista::all();

        $contratistas = $contratistas->map(function ($contratista, $key) {
            return [
                trans('forms.nombre_razon_social')  => $contratista->razon_social,
                trans('forms.tipo_contratista')     => $contratista->tipo->nombre,
                trans('forms.tipo_doc_num_doc')     => $contratista->tipo_num_documento,
                trans('forms.nombre_fantasia')      => '' . $contratista->nombre_fantasia,
                trans('forms.mail')                 => $contratista->email,
            ];
        });

        return $this->toExcel(
            trans('index.contratistas'),
            $this->filtrarExportacion($contratistas, $filtro)
        );
    }
}
