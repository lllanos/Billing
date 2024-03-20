<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Auth;
use Lang;
use View;

use App\User;

use Yacyreta\Usuario\UserPublico;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct() {
      View::share('ayuda', 'inicio_sesion');
      $this->middleware('guest', ['except' => 'logout']);
    }


////// Login Publico //////
    public function showLoginFormPublic() {
      return view('auth.login');
    }

    public function loginPublic(Request $request) {
      return $this->login($request);
    }

////// FIN Login Publico //////

    public function login(Request $request) {
      // Validacion de campos
      $this->validateLogin($request);
      $user = User::where('email', $request->email)
                  ->where('usuario_sistema', 0)->first();

      if($user == null) {
        return $this->sendFailedLoginResponse($request);
      }

        // Valida que no se haya inhabilitado desde el menu de usuarios
        if(!$this->validarUsuarioHabilitado($request)) {
          return $this->sendCustomFailedLoginResponse($request);
        }

        // Valida que el usuario haya confirmado desde el mail que se le
        // envia, en caso contrario se le da la opcion de volver a pedir el envio
        if(!$this->validarUsuarioConfirmado($request)) {
          return $this->sendCustomFailedLoginResponse($request);
        }

      // Validacion de intentos de logueo incorrectos
      if($this->hasTooManyLoginAttempts($request)) {
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
      }

      // Se intenta el login
      if($this->attemptLogin($request)) {
        return $this->sendLoginResponse($request);
      }

      $this->incrementLoginAttempts($request);

      return $this->sendFailedLoginResponse($request);
    }


////// Overriden //////

    protected function sendLoginResponse(Request $request) {
      $request->session()->regenerate();

      $this->clearLoginAttempts($request);

      if($request->expectsJson()) {
        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
      }

      return $this->authenticated($request, $this->guard()->user())
              ?: redirect()->intended($this->redirectPath());
    }

    protected function validateLogin(Request $request) {
      $this->validate($request, [
          $this->username() => 'required|string',
          'password'        => 'required|string',
      ]);
    }

    protected function attemptLogin(Request $request) {
        $credenciales = array();
        foreach($this->credentials($request) as $keyCredential => $valueCredential)
          $credenciales[$keyCredential] = $valueCredential;

        $credenciales['usuario_sistema'] = 0;

        return $this->guard()->attempt(
            $credenciales, $request->has('remember')
        );
    }


    protected function hasTooManyLoginAttempts(Request $request) {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), config('custom.intentos_login'), config('custom.tiempo_bloqueo_login')
        );
    }

    protected function sendFailedLoginResponse(Request $request) {
      $errors = [$this->username() => trans('auth.failed')];

      if($request->expectsJson()) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errors;
        return response()->json($jsonResponse);
      }

      return redirect()->back()
                       ->withInput($request->only($this->username(), 'remember'))
                       ->withErrors($errors);
    }

		protected function sendLockoutResponse(Request $request) {
      $seconds = $this->limiter()->availableIn(
          $this->throttleKey($request)
      );

			$minutes = floor($seconds / 60);

      $message = Lang::get('auth.throttle', ['minutes' => $minutes]);

      $errors = [$this->username() => $message];

      if($request->expectsJson()) {
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errors;
        return response()->json($jsonResponse);
      }

      return redirect()->back()
                       ->withInput($request->only($this->username(), 'remember'))
                       ->withErrors($errors);
    }

////// FIN Overriden //////

////// Custom //////
    protected function validarUsuarioHabilitado($request) {
      $user = User::where('email', $request->email)->first();
      if(empty($user)) {
        return true;
      } else {
        return $user->habilitado;
      }
    }

    protected function validarUsuarioConfirmado($request) {
      $user = User::where('email', $request->email)->first();
      if(empty($user)) {
        return true;
      } else {
        return $user->confirmado;
      }
    }

    protected function sendCustomFailedLoginResponse(Request $request) {
      $user = User::where('email', $request->email)->first();
      $url = route('reenviar.confirmacion', array($user->codigo_confirmacion));
      if(!$user->confirmado)
        $errors = [$this->username() => trans('auth.falta_confirmacion', ['nombre_usuario' => $request->email]),
                   'email_confirm' => $url];

      else if(!$user->habilitado)
        $errors = [$this->username() => trans('auth.usuario_deshabilitado')];

      if($request->expectsJson()) {
        return response()->json($errors, 422);
      }

      return redirect()->back()
                       ->withInput($request->only($this->username(), 'remember'))
                       ->withErrors($errors);
    }

    private function existeUsuario($request) {
      $cuit = str_replace('-', "", $request['cuit']);
      $user_publico = UserPublico::whereCuit($cuit)->first();
      if($user_publico == null)
        return false;

      $user = $user_publico->user;
      if($user == null)
        return false;

      return true;
    }
////// FIN Custom //////

}
