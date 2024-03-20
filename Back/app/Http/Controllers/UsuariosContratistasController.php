<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Auth;
use Log;
use Response;
use View;


use App\User;
use Yacyreta\Dashboard\UserWidgets;
use Yacyreta\Dashboard\Widget;
use Yacyreta\Usuario\UserPublico;
use Yacyreta\TipoDocumento;
use Yacyreta\Pais;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class UsuariosContratistasController extends Controller {

    public function __construct() {
      View::share('ayuda', 'usuarios');
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    */
    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $usuarios = User::whereUsuarioSistema(0)->get();      

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $usuarios = $this->filtrar($usuarios, $search_input);
        }
        $usuarios = $this->paginateCustom($usuarios);
      } else {
        $usuarios = $this->filtrar($usuarios, $search_input);
        $usuarios = $this->paginateCustom($usuarios, 1);
      }

      return view('contratistas.usuarios.index', compact('usuarios', 'search_input'));
    }

    /**
     * @param  int  $id
    */
    public function edit($id) {
      $user = User::findOrFail($id);      
      if(Auth::user()->usuario_causante && ($user->causante_id != Auth::user()->causante_id)) {
        return redirect()->route('seguridad.users.index')
                         ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
      }

      $paises = Pais::pluck('nombre', 'id');
      $userPais = $user->pais;
      $tipos_documento = TipoDocumento::all()->pluck('nombre', 'id');

      return view('contratistas.usuarios.edit', compact('user', 'paises', 'userPais', 'tipos_documento'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
    */
    public function update(Request $request, $id) {
      $rules = array(
          'nombre'        => 'required|min:3|max:256',
          'apellido'      => 'required|min:3|max:256',
      );

      $input = $request->all();
      $validator = Validator::make($input, $rules);

      $errores = array();

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        return Redirect::to('contratistas/usuarios/' . $id . '/editar')
                       ->withErrors($errores)
                       ->withInput($input);
      } 

      $user = User::find($id);
      $usuario = $user->user_publico;
      $user->nombre = $input['nombre'];
      $user->apellido = $input['apellido'];      
      $usuario->tipo_documento_id = $input['tipo_documento'];
      $usuario->documento = $input['documento'];
      $usuario->pais_id = $input['pais'];

      try{        
        $usuario->save();
        $user->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        return redirect()->route('contratistas.usuarios.index')
                        ->with('error', trans('mensajes.error.insert_db'));
      }
      
      return redirect()->route('contratistas.usuarios.index')
                       ->with('success', trans('mensajes.dato.usuario').trans('mensajes.success.actualizado'));
    }

    /**
    * @param int $id
    * @param  string $accion
    */
    public function preToggleHabilitar($id, $accion) {
      $user = User::find($id);
      $user_publico = $user->user_publico;

      $jsonResponse['status'] = false;
      if(Auth::user()->cant('user-' . $accion)) {
        $jsonResponse['title'] = trans('index.' . $accion) . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_habilitar.user_causante_' . $accion, ['nombre' => $user->apellido_nombre])];
      } elseif((Auth::user()->usuario_causante && ($user->causante_id != Auth::user()->causante_id))) {
        $jsonResponse['title'] = trans('index.' . $accion) . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_habilitar.user_causante_' . $accion, ['nombre' => $user->apellido_nombre])];
      } elseif($user_publico == null) {
        $jsonResponse['title'] = trans('index.' . $accion). ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_habilitar.user_gestion_' . $accion, ['nombre' => $user->apellido_nombre])];
      } else {
        $jsonResponse['status'] = true;
      }
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    * @param  string $accion
    */
    public function toggleHabilitado($id, $accion) {
      if($this->preToggleHabilitar($id, $accion)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = $this->preToggleHabilitar($id)->getData()->message;
        return response()->json($jsonResponse);
      }

      $user = User::find($id);

      try {
        if($accion == 'enable')
          $user->habilitado = 1;
        else
          $user->habilitado = 0;
        $user->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['errores'] = [];
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
      }

      $jsonResponse['errores'] = [];
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      if($accion == 'enable')
        $jsonResponse['message'] = [trans('mensajes.dato.usuario') . trans('mensajes.success.habilitado')];
      else
        $jsonResponse['message'] = [trans('mensajes.dato.usuario') . trans('mensajes.success.deshabilitado')];

      return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public  function exportar(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $users = User::whereUsuarioSistema(0)->get();

      $user_export = array();
      if($filtro == null || $filtro == 'undefined') {
        foreach ($users as $key => $usuario) {
          if($usuario != null) {
            $user_export[$key][trans('forms.estado')] = ($usuario->habilitado) ? trans('index.habilitado') : trans('index.deshabilitado');  
            $user_export[$key][trans('forms.name')] = $usuario->apellido_nombre;
            $user_export[$key][trans('forms.mail')] = $usuario['email'];
            $user_export[$key][trans('forms.documento')] = $usuario->documento;
            $user_export[$key][trans('forms.pais')] = $usuario->pais;          
            $user_export[$key][trans('index.confirmado')] = ($usuario->confirmado) ? trans('index.confirmado') : trans('index.no_confirmado');
          }
        }
      } else {
        $filtro_array = array();
        array_push($filtro_array, $filtro);
        foreach ($usuarios as $key => $usuario) {
          if($usuario != null) {
            if( $this->filtrarBusqueda($usuario->apellido_nombre, $filtro_array) ||
                $this->filtrarBusqueda($usuario->documento, $filtro_array) ||
                $this->filtrarBusqueda($usuario['email'], $filtro_array) ||
                $this->filtrarBusqueda($usuario->pais, $filtro_array)) {
                $user_export[$key][trans('forms.name')] = $usuario->apellido_nombre;
                $user_export[$key][trans('forms.documento')] = $usuario->documento;
                $user_export[$key][trans('forms.mail')] = $usuario['email'];
                $user_export[$key][trans('forms.pais')] = $usuario->pais;    
            }
          }
        }
      }

      return $this->toExcel(trans('index.usuarios'), $user_export);
    }

    public function showRegistrationForm() {
        $tipos_documento = TipoDocumento::all()->pluck('nombre', 'id');
        $paises = Pais::all()->pluck('nombre', 'id');

        return view('contratistas.usuarios.create', compact('tipos_documento', 'paises'));
    }
    
    protected function validator(array $data) {
        return Validator::make($data, [
            'nombre'                  =>    'required|min:3|max:255',
            'apellido'                =>    'required|min:3|max:255',
            'documento'               =>    'required',
            'pais_id'                 =>    'required',
            'email'     =>    'required|email|max:255|unique:users,email,NULL,usuario_sistema,usuario_sistema,0,deleted_at,NULL',
        ], $this->validationErrorMessages());
    }

    public function register(Request $request) {
        $this->validator($request->all())->validate();
        $input = $request->all();
        //$reCaptchaRespuesta = $this->captcha($input['g-recaptcha-response']);

        $errores = array();
        // if(!$reCaptchaRespuesta->success){
        //   $errores['robot'] = trans('mensajes.error.captcha');
        // }
        if(User::whereEmail($input['email'])->whereHabilitado(0)->first() != null) {
          $errores["email"] = trans('mensajes.error.email_existente');
        }
        
        if(sizeof($errores) > 0) {
            return redirect()->route('contratistas.usuarios.create')
                             ->withInput($input)
                             ->withErrors($errores);
        }

        $user = $this->store($request);

        if($user == null)
          return redirect()->route('contratistas.usuarios.create');

        return redirect()->route('contratistas.usuarios.index')
                       ->with('success', trans('mensajes.dato.usuario').trans('mensajes.success.creado'));
    }

    protected function store(Request $request) {
        $input = $request->all();
        $documento = str_replace('.', "", $input['documento']);
        $codigo_confirmacion = str_random(30);

        $password = str_random(8);
        $letters = "abcdefghijklmnopqrstuvwxyz";
        $password = str_shuffle(strtoupper($letters[rand(0, 25)]).
                                rand(100, 999) . $letters[rand(0, 25)] . $letters[rand(0, 25)]);

        try {
          $user = User::create([
              'nombre'                => $input['nombre'],
              'apellido'              => $input['apellido'],
              'email'                 => $input['email'],
              'codigo_confirmacion'   => $codigo_confirmacion,
              'usuario_sistema'       => 0,
              'password'              => bcrypt($password),
          ]);
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          return null;
        }

        $user_publico = new UserPublico();
        $user_publico->user_id = $user->id;
        $user_publico->pais_id = $input['pais_id'];
        $user_publico->tipo_documento_id = $input['tipo_documento'];
        $user_publico->documento = $documento;

        try {
          $user_publico->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          return null;
        }

        $widgets = Widget::whereBack(0)->get();
        $orden = 1;
        foreach ($widgets as $key => $widget) {

          try {
              $user_widget = UserWidgets::create([
                  'user_id' => $user->id,
                  'widget_id' => $widget->id,
                  'orden' => $orden,
                ]);
          }
          catch(\QueryException $e) {
             Log::error('QueryException', ['Exception' => $e]);
             return redirect()->back()
                     ->with(['error' => trans('error.user.guardando_en_db')]);
          }

          $orden++;
        }

        try {
          $user->sendCreateUserNotification($password);
        } catch (\Swift_TransportException $e) {
          Log::error('Swift_TransportException', ['Exception' => $e]);
          return null;
        }

        return $user;
    }
}
