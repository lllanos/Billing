<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

use App\User;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
      $this->middleware('guest')->except('logout');
      $this->decayMinutes = config('custom.tiempo_bloqueo_login');
    }

    public function login(Request $request) {
      $this->validateLogin($request);
      $user = User::where('email', $request->email)
                  ->where('usuario_sistema', 1)->first();

      if($user == null) {
        return $this->sendFailedLoginResponse($request);
      }

      if(!$this->validarUsuarioHabilitado($request)) {
        return $this->sendCustomFailedLoginResponse($request);
          // return $this->sendLoginResponse($request);
      }

      if(!$this->validarUsuarioConfirmado($request)) {
        return $this->sendCustomFailedLoginResponse($request);
      }

      if($this->hasTooManyLoginAttempts($request)) {
        $this->fireLockoutEvent($request);

        return $this->sendLockoutResponse($request);
      }

      if($this->attemptLogin($request)) {
        return $this->sendLoginResponse($request);
      }

      $this->incrementLoginAttempts($request);

      return $this->sendFailedLoginResponse($request);
    }

    ////// Overriden //////
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

      $credenciales['usuario_sistema'] = 1;

      return $this->guard()->attempt(
        $credenciales, $request->has('remember')
      );
    }

    protected function hasTooManyLoginAttempts(Request $request) {
      return $this->limiter()->tooManyAttempts(
        $this->throttleKey($request), config('custom.intentos_login'), config('custom.tiempo_bloqueo_login')
      );
    }

    protected function sendLockoutResponse(Request $request) {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $minutes = (int) ($seconds / 60);

        throw ValidationException::withMessages([
          $this->username() => [Lang::get('auth.throttle', ['minutes' => $minutes])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }
    ////// FIN Overriden //////

    ////// Custom //////
    protected function validarUsuarioHabilitado($request) {
      $user = User::where('email', $request->email)
                  ->where('usuario_sistema', 1)->first();
      if(empty($user)) {
        return true;
      } else {
        return $user->habilitado;
      }
    }

    protected function validarUsuarioConfirmado($request) {
      $user = User::where('email', $request->email)
                  ->where('usuario_sistema', 1)->first();
      if(empty($user)) {
        return false;
      } else {
        return $user->confirmado;
      }
    }

    protected function sendCustomFailedLoginResponse(Request $request) {
      $user = User::where('email', $request->email)
                  ->where('usuario_sistema', 1)->first();

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
    ////// FIN Custom //////

}
