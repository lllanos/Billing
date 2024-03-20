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
use Response;

use App\Role;
use App\User;
use Yacyreta\TipoDocumento;

use YacyretaPackageController\UsersController as PackageUsersController;
class UsersController extends PackageUsersController
{
    use RegistersUsers;

    public function __construct() {
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function perfil() {
      $user = Auth::user();
      $user_publico = $user->user_publico;
      $tipos_documento = TipoDocumento::getOpciones();;

      return view('perfil', compact('user', 'user_publico', 'tipos_documento'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updatePerfil(Request $request) {
      $user = Auth::user();
      $rules = array(
          'nombre'            => $this->requiredLettersSpacesN(100),                
          'apellido'          => $this->requiredLettersSpacesN(100),                
          'tipo_documento'    => 'required',
          'documento'         => 'required|'.$this->documentCuitFormat(), 
      );

      $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());
      $input = $request->all();

      if ($validator->fails()) {
        return Redirect::back()->withErrors($validator)
                               ->withInput(Input::all());
      } else {
        $user->nombre = $input['nombre'];
        $user->apellido = $input['apellido'];

        $user_publico = $user->user_publico;
        $user_publico->tipo_documento_id = $input['tipo_documento'];
        $user_publico->documento = $input['documento'];

        if(isset($input['notificaciones_por_mail']))
          $user->notificaciones_por_mail = true;
        else
          $user->notificaciones_por_mail = false;

        try {
          $user->save();
          $user_publico->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          return redirect()->back()
                           ->with(['error' => trans('mensajes.error.insert_db')]);
        }
        return redirect()->back()
                         ->with(['success' => trans('mensajes.dato.perfil').trans('mensajes.success.actualizado')]);
      }
    }

}
