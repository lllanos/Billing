<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use Auth;

use Yacyreta\Usuario\UserPublico;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
      $this->middleware('guest');
    }

	protected function validationErrorMessages() {
      return [
          'password.confirmed'  => trans('validation_custom.password.confirmed'),
          'password.regex'      => trans('validation_custom.password.formato_invalido')
      ];
    }

    // public function reset(Request $request) {
    //     $cuit = str_replace("-", "", $request['cuit']);
    //     $user_publico = UserPublico::whereCuit($cuit)->first();
    //
    //     if($user_publico != null) {
    //       $request['email'] = $user_publico->user->email;
    //     } else {
    //       $errors = ['cuit' => trans('auth.usuario_inexistente', ['cuit' => $request->cuit])];
    //       return redirect()->back()
    //           ->withInput($request->only($request->cuit, 'remember'))
    //           ->withErrors($errors);
    //     }
    //
    //     $this->validate($request, $this->rules(), $this->validationErrorMessages());
    //
    //     $response = $this->broker()->reset(
    //         $this->credentials($request), function ($user, $password) {
    //             $this->resetPassword($user, $password);
    //         }
    //     );
    //
    //     return $response == Password::PASSWORD_RESET
    //                 ? $this->sendResetResponse($response)
    //                 : $this->sendResetFailedResponse($request, $response);
    // }

    protected function rules()
    {
        return [
            'token'     => 'required',
            'email'     => 'required|email',
            'password'  => 'required|min:6|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ];
    }
}
