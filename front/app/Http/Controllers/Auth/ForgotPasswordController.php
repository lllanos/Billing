<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Response;

use Yacyreta\Usuario\UserPublico;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    public function __construct() {
      $this->middleware('guest');
    }

    public function showLinkRequestFormPublic() {
      return view('auth.passwords.email');
    }

    public function sendResetLinkEmailPublic(Request $request) {
      return $this->sendResetLinkEmail($request);
    }

    public function sendResetLinkEmail(Request $request) {
      $credenciales = array();
      $credenciales = $request->only('email');
      $credenciales['usuario_sistema'] = 0;

      $this->validateEmail($request);

      $response = $this->broker()->sendResetLink(
          $credenciales
      );

      return $response == Password::RESET_LINK_SENT
                  ? $this->sendResetLinkResponse($request, $response)
                  : $this->sendResetLinkFailedResponse($request, $response);
    }

}
