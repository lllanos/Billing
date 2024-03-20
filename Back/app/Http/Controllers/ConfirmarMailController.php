<?php

namespace App\Http\Controllers;

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


class ConfirmarMailController extends Controller {
//    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('guest');
    }

    /**
     * @param  string $codigo_confirmacion
    */
    public function reenviarConfirmarUsuario($codigo_confirmacion) {
      $password = str_random(8);

      $user = User::whereCodigoConfirmacion($codigo_confirmacion)->first();
      $user->password = bcrypt($password);
      $user->save();
  	  try {
		    $user->sendReConfirmUserNotification($user->codigo_confirmacion, $password);
  		} catch (\Swift_TransportException $e) {
  		  Log::error('Swift_TransportException', ['Exception' => $e]);
  		  Session::flash('error', trans('mensajes.error.error_mail_usuario'));
  		  return Response::json(array(
  				'fail'      => false,
  				'back'      => false,
  				'redirect'  => '/seguridad/users'
  		  ));
  		}

      return redirect('/login')
          ->with('success', trans('login.confimacion_reenviada'));
    }

    /**
     * @param  string $codigo_confirmacion
    */
    public function confirmarUsuario($codigo_confirmacion) {
      if(!$codigo_confirmacion) {
        throw new InvalidConfirmationCodeException;
      }

      $user = User::whereCodigoConfirmacion($codigo_confirmacion)->first();

      if(!$user) {
        return redirect('/')
               ->with('error', trans('system.messages.code.error'));
      } else {
        if($user->confirmado == 1) {
          return redirect('/')
                 ->with('error', trans('login.confirm_already'));
        }
      }

      return view('auth.confirm', array('confirmation_code' => $codigo_confirmacion));
    }

    public function confirmarMailUsuario() {
      $codigo_confirmacion = Input::get('confirmation_code');
      $email = Input::get('email');
      if (!$codigo_confirmacion || !$email) {
        throw new InvalidConfirmationCodeException;
      }

      $user = User::where('codigo_confirmacion', '=', $codigo_confirmacion)
              ->where('email', '=', $email)
              ->first();

      $confirmado = false;
      if(!$user) {
        return redirect('/register/verify/' . $codigo_confirmacion)
               ->with('error', trans('login.email_error', ['email' => $email]));
      } else {
        if($user->email !== $email) {
          return redirect('/register/verify/' . $codigo_confirmacion)
                 ->with('error', trans('login.confirm_already'));
        }
        if($user->confirmado == 1) {
          $confirmado = true;
        } else {
          return view('auth.generarPassword', array('confirmation_code' => $codigo_confirmacion));
        }
      }
      return view('auth.generarPassword', array('confirmation_code' => $codigo_confirmacion));
    }

    public function finConfirmacionUsuario() {
      $codigo_confirmacion = Input::get('token');
      $password_original = Input::get('password_original');
      $password = Input::get('password');
      $password_confirmation = Input::get('password_confirmation');

      $rules = array(
          'password'          => 'required|min:6|confirmed',
          'password_original' => 'required',
      );

      $validator = Validator::make(Input::all(), $rules);

      if ($validator->fails()) {
        $errores = $validator->getMessageBag()->toArray();
        $error = '<ul>';
        foreach ($errores as $key => $value) {
          foreach ($value as $key => $val) {
            $error.= '<li>'.$val.'</li>';
          }
        }

        $error.= '</ul>';

        Session::flash('error', $error);
        return view('auth.generarPassword', array('confirmation_code' => $codigo_confirmacion));
      }

      $user = User::where('codigo_confirmacion', '=', $codigo_confirmacion)
              ->first();

      if(!Hash::check($password_original, $user->password)) {
        Session::flash('error', trans('login.error_password_original'));
        return view('auth.generarPassword', array('confirmation_code' => $codigo_confirmacion));
      } else {
        $user->confirmado = 1;
        $user->codigo_confirmacion = null;
        $user->password = Hash::make($password);
        $user->save();
        auth()->login($user);
      }

      Session::flash('success', trans('login.usuario_confirmado'));
      return redirect('/');
    }
    // FIN Creación de Usuario

}
