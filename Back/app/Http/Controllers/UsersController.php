<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Hash;
use Log;
use Redirect;
use Response;
use View;

use App\Role;
use App\User;
use Yacyreta\Usuario\UserAdmin;
use Yacyreta\Causante;

use YacyretaPackageController\UsersController as PackageUsersController;

class UsersController extends PackageUsersController {
    use RegistersUsers;

    public function __construct() {
      View::share('ayuda', 'seguridad');
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';

      $users = User::whereUsuarioSistema(1)->with('causante');
      if(Auth::user()->usuario_causante) {
        $users = $users->whereCausanteId(Auth::user()->causante_id);
      }
      $users = $users->get();

      return view('users.index', compact('users', 'search_input'));
    }

    public function create() {
      $roles = Role::pluck('name', 'id');
      $causantes = Causante::all()->sortBy('id')->pluck('nombre', 'id')->prepend(trans('forms.select.causante'), '');

      return view('users.create', compact('roles', 'causantes'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function store(Request $request) {
      $rules = array(
          'nombre'      => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
          'apellido'    => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
          'email'       => 'required|email|max:255|unique:users,email,NULL,usuario_sistema,usuario_sistema,1,deleted_at,NULL',
      );

      $validator = Validator::make(Input::all(), $rules);

      $input = $request->all();

      $errores = array();
      if(!Auth::user()->usuario_causante) {
        if(isset($input['usuario_causante']) && $input['causante_id'] == null) {
          $errores["usuario_causante"] = trans('mensajes.error.usuario_causante');
        }
      }

      if($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        return Redirect::to('seguridad/usuarios/crear')
                       ->withErrors($errores)
                       ->withInput(Input::all());
      } else {
        $codigo_confirmacion = str_random(30);
        $password = str_random(8);

        $user = User::create([
            'nombre'                => $input['nombre'],
            'apellido'              => $input['apellido'],
            'email'                 => $input['email'],
            'codigo_confirmacion'   => $codigo_confirmacion,
            'usuario_sistema'       => 1,
            'password'              => bcrypt($password),
        ]);

        if(Auth::user()->usuario_causante) {
          $input['causante_id'] = Auth::user()->causante_id;
          $input['usuario_causante'] = 'on';
        }

        if(!isset($input['usuario_causante'])) {
          $user->usuario_causante = 0;
          $user->causante_id = null;
        } else {
          $user->usuario_causante = 1;
          $user->causante_id = $input['causante_id'];
        }

        $user->save();
      }

			if(null !== $request->input('roles')) {
				foreach ($request->input('roles') as $key => $value) {
					$user->attachRole($value);
				}
			}

      try {
        $user->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        return redirect()->route('seguridad.users.index')
                         ->with('error', trans('mensajes.error.insert_db'));
      }

  		$user_admin = new UserAdmin();
      $user_admin->user_id = $user->id;

      try {
        $user_admin->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        return null;
      }

      try {
        $user->sendCreateUserNotification($password);
      } catch (\Swift_TransportException $e) {
        Log::error('Swift_TransportException', ['Exception' => $e]);
        return redirect()->route('seguridad.users.index')
                         ->with('error', trans('mensajes.error.envio_mail'));
      }

      return redirect()->route('seguridad.users.index')
                       ->with('success', trans('mensajes.dato.usuario') . trans('mensajes.success.creado'));
    }

    public function show($id) {
      $user = User::find($id);
      return view('users.show', compact('user'));
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

      $roles = Role::pluck('name', 'id');
      $userRoles = $user->roles->pluck('id', 'id')->toArray();
      $causantes = Causante::all()->sortBy('id')->pluck('nombre', 'id')->prepend(trans('forms.select.causante'), '');

      return view('users.edit', compact('user', 'roles', 'userRoles', 'causantes'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
    */
    public function update(Request $request, $id) {
      $rules = array(
          'nombre'        => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
          'apellido'      => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
      );

      $validator = Validator::make(Input::all(), $rules);
      $input = $request->all();
      dd($validator);

      $errores = array();
      if(Auth::user()->usuario_causante) {
        if (isset($input['usuario_causante']) && $input['causante_id'] == null) {
          $errores["usuario_causante"] = trans('mensajes.error.usuario_causante');
        }
      }

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        return Redirect::to('seguridad/usuarios/' . $id . '/editar')
                       ->withErrors($errores)
                       ->withInput(Input::all());
      } else {
        $user = User::find($id);

        $user->detachRoles($user->roles);

        $user->nombre     = $input['nombre'];
        $user->apellido   = $input['apellido'];

  			if(null !== $request->input('roles')) {
  				foreach ($request->input('roles') as $key => $value) {
  					$user->attachRole($value);
  				}
  			}

        if(Auth::user()->usuario_causante) {
          $input['causante_id'] = Auth::user()->causante_id;
          $input['usuario_causante'] = 'on';
        }

        if(!isset($input['usuario_causante'])) {
          $user->usuario_causante = 0;
          $user->causante_id = null;
        } else {
          $user->usuario_causante = 1;
          $user->causante_id = $input['causante_id'];
        }

        try{
          $user->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          return redirect()->route('seguridad.users.index')
                          ->with('error', trans('mensajes.error.insert_db'));
        }

        return redirect()->route('seguridad.users.index')
                         ->with('success', trans('mensajes.dato.usuario').trans('mensajes.success.actualizado'));
      }
    }

//////////// Eliminar y Habilitar ////////////
    /**
    * @param int $id
    */
    public function preDelete($id) {
      $user = User::find($id);
      // $alarma = null;

      $jsonResponse['status'] = false;
      if((Auth::user()->usuario_causante && ($user->causante_id != Auth::user()->causante_id))) {
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_eliminar.user_causante', ['nombre' => $user->apellido_nombre])];
      } elseif(count($user->representante_eby) > 0) {
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_eliminar.user_representante_eby', ['nombre' => $user->apellido_nombre])];
      } else {
        $jsonResponse['status'] = true;
      }
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function delete($id) {
      if($this->preDelete($id)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = $this->preDelete($id)->getData()->message;
        return response()->json($jsonResponse);
      }

      $user = User::find($id);
      $user_admin = $user->user_admin;

      if($user_admin != null) {
        try{
          $user_admin->delete();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          $jsonResponse['errores'] = [];
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        }
      }

      try{
        $user->delete();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['errores'] = [];
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
      }

      $jsonResponse['errores'] = [];
      $jsonResponse['status'] = true;
      $jsonResponse['action']['function'] = "deleteRow";
      $jsonResponse['action']['params'] = 'user_' . $id;
      $jsonResponse['message'] = [trans('mensajes.dato.usuario') . trans('mensajes.success.eliminado')];

      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    * @param  string $accion
    */
    public function preToggleHabilitar($id, $accion) {
      $user = User::find($id);
      $user_admin = $user->user_admin;

      $jsonResponse['status'] = false;
      if(Auth::user()->cant('user-' . $accion)) {
        $jsonResponse['title'] = trans('index.' . $accion) . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_habilitar.user_causante_' . $accion, ['nombre' => $user->apellido_nombre])];
      } elseif((Auth::user()->usuario_causante && ($user->causante_id != Auth::user()->causante_id))) {
        $jsonResponse['title'] = trans('index.' . $accion) . ' ' . trans('index.user');
        $jsonResponse['message'] = [trans('index.no_puede_habilitar.user_causante_' . $accion, ['nombre' => $user->apellido_nombre])];
      } elseif($user_admin == null) {
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
//////////// FIN Eliminar y Habilitar ////////////

//////////// Perfil ////////////
    public function perfil() {
      $user = Auth::user();
      return view('perfil', compact('user'));
    }

    public function updatePerfil(Request $request) {
      $rules = array(
          'nombre'      => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
          'apellido'    => 'required|min:3|max:256|regex:/^[a-zA-Z\s]*$/',
      );

      $validator = Validator::make(Input::all(), $rules);
      $input = $request->all();

      if ($validator->fails()) {
        return Redirect::back()
            ->withErrors($validator)
            ->withInput(Input::all());
      } else {
        $user = Auth::user();
        $user->nombre = $input['nombre'];
        $user->apellido = $input['apellido'];
        if(isset($input['notificaciones_por_mail']))
          $user->notificaciones_por_mail = true;
        else
          $user->notificaciones_por_mail = false;

        try {
          $user->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          return redirect()->back()
                           ->with(['error' => trans('mensajes.error.insert_db')]);
        }

        Session::flash('success', trans('mensajes.dato.perfil').trans('mensajes.success.actualizado'));
        return redirect()->back()
                         ->with(['success' => trans('mensajes.dato.perfil').trans('mensajes.success.actualizado')]);
      }
    }
//////////// FIN Perfil ////////////

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public  function exportar(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $users = User::whereUsuarioSistema(1);
      if(Auth::user()->usuario_causante) {
        $users = $users->whereCausanteId(Auth::user()->causante_id);
      }
      $users = $users->with('causante')->get();

      $user_export = array();
      if($filtro == null || $filtro == 'undefined') {
        foreach ($users as $key => $usuario) {
          if($usuario != null) {
            $user_export[$key][trans('forms.estado')] = ($usuario->habilitado) ? trans('index.habilitado') : trans('index.deshabilitado');
            $user_export[$key][trans('forms.name')] = $usuario->apellido_nombre;
            $user_export[$key][trans('forms.mail')] = $usuario['email'];
            $user_export[$key][trans('forms.causante')] = $usuario->causante_nombre_color['nombre'];
            $user_export[$key]['Roles'] = $usuario->roles_string;
          }
        }
      } else {
        $filtro_array = array();
        array_push($filtro_array, $filtro);
        foreach ($usuarios as $key => $usuario) {
          if($usuario != null) {
            if( $this->filtrarBusqueda($usuario->apellido_nombre, $filtro_array) ||
                $this->filtrarBusqueda($usuario['email'], $filtro_array) ||
                $this->filtrarBusqueda($usuario->causante_nombre_color['nombre'], $filtro_array) ||
                $this->filtrarBusqueda($usuario->roles_string, $filtro_array)) {
                  $user_export[$key][trans('forms.estado')] = ($usuario->habilitado) ? trans('index.habilitado') : trans('index.deshabilitado');
                  $user_export[$key][trans('forms.name')] = $usuario->apellido_nombre;
                  $user_export[$key][trans('forms.mail')] = $usuario['email'];
                  $user_export[$key][trans('forms.causante')] = $usuario->causante_nombre_color['nombre'];
                  $user_export[$key]['Roles'] = $usuario->roles_string;
            }
          }
        }
      }

      return $this->toExcel(trans('index.usuarios'), $user_export);
    }

    protected function validator(array $data) {
      return Validator::make($data, [
        'nombre'  => 'required|max:255',
        'email'   => 'required|email|max:255|unique:users',
      ]);
    }

}
