<?php

namespace App\Http\Controllers;
use YacyretaPackageController\ConfirmarMailController as ConfirmarMailControllerPackage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;

use App;
use Config;
use Cookie;
use DB;
use Hash;
use Log;
use Mail;
use Redirect;

use App\Permission;
use App\Role;
use App\User;


class ConfirmarMailController extends ConfirmarMailControllerPackage {
    protected function validationErrorMessages() {
      return [
          'password.confirmed'              => trans('validation_custom.password.confirmed'),
          'password.regex'                  => trans('validation_custom.password.formato_invalido'),
          'terminos_y_condiciones.required' => trans('validation_custom.required.terminos_y_condiciones')
      ];
    }

    public function confirmarMailUsuario() {
      $errors = array();
      $confirmation_code = Input::get('confirmation_code');
      $email = Input::get('email');
      if (!$confirmation_code || !$email) {
        throw new InvalidConfirmationCodeException;
      }

      $user = User::where('codigo_confirmacion', '=', $confirmation_code)
                  ->where('email', '=', $email)
                  ->first();

      $confirmado = false;
      if (!$user) {
       return redirect()->route('register.verify', array('confirmation_code' => $confirmation_code))
                        ->with(['error' => trans('login.email_error', ['email' => $email])]);
      } else {

        if($user->email !== $email) {
         return redirect()->route('register.verify', array('confirmation_code' => $confirmation_code))
                          ->with(['error' => trans('login.confirm_already')]);
        }

        if($user->confirmado == 1) {
          $confirmado = true;
        } else {
          return view('auth.generarContrasenia', compact('confirmation_code', 'user', 'errors'));
        }
      }

      return view('auth.generarContrasenia', compact('confirmation_code', 'user', 'errors'));
    }

    private function confirmarMailUsuarioWithErrors($confirmation_code, $email, $errors) {
      if (!$confirmation_code || !$email) {
        throw new InvalidConfirmationCodeException;
      }

      $user = User::where('codigo_confirmacion', '=', $confirmation_code)
                  ->where('email', '=', $email)
                  ->first();

      $confirmado = false;
      if (!$user) {
       return redirect()->route('register.verify', array('confirmation_code' => $confirmation_code))
                        ->with(['error' => trans('login.email_error', ['email' => $email])]);
      } else {

        if($user->email !== $email) {
         return redirect()->route('register.verify', array('confirmation_code' => $confirmation_code))
                          ->with(['error' => trans('login.confirm_already')]);
        }

        if($user->confirmado == 1) {
          $confirmado = true;
        } else {
          return view('auth.generarContrasenia', compact('confirmation_code', 'user', 'errors'));
        }
      }

      return view('auth.generarContrasenia', compact('confirmation_code', 'user', 'errors'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function finConfirmacionUsuario(Request $request) {
      $input = $request->all();
      $rules = array(
          'password'                => 'required|min:6|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
          'password_original'       => 'required',
          'terminos_y_condiciones'  =>'required',
      );

      $codigo_confirmacion = $input['token'];
      $user = User::where('codigo_confirmacion', '=', $codigo_confirmacion)
                  ->first();

      $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

      if($validator->fails()) {
        $errors = $validator->getMessageBag()->toArray();
        return $this->confirmarMailUsuarioWithErrors($codigo_confirmacion, $user->email, $errors);
      }

      $confirmation_code = $input['token'];
      $password_original = $input['password_original'];
      $password = $input['password'];
      $password_confirmation = $input['password_confirmation'];

      $errors = array();

      if(!Hash::check($password_original, $user->password))
        $errors["password_original"]["password_original"] = trans('login.error_password_original');

      $errors = array_merge($errors, $validator->getMessageBag()->toArray());

      if ($validator->fails() || sizeof($errors) > 0) {
        return view('auth.generarContrasenia', compact('confirmation_code', 'user', 'errors'));
      }

      $user->confirmado = 1;
      $user->codigo_confirmacion = null;
      $user->password = Hash::make($password);
      $user->save();

      $user_publico = $user->user_publico;
      $user_publico->save();

      auth()->login($user);

      return redirect()->route('index')
                       ->with(['success' => trans('login.usuario_confirmado')]);
    }
}
