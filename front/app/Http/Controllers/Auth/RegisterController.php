<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Log;
use Response;
use View;


use App\User;
use Yacyreta\Dashboard\UserWidgets;
use Yacyreta\Dashboard\Widget;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct() {
      View::share('ayuda', 'registro');
      $this->middleware('guest');
    }

    public function showRegistrationForm() {
      return null;
        // $tipos_documento = TipoDocumento::all()->pluck('nombre', 'id');
        // $paises = Pais::all()->pluck('nombre', 'id');
        //
        // return view('auth.register', compact('tipos_documento', 'paises'));
    }

    // protected function validationErrorMessages() {
    //     return [
    //         'terminos_y_condiciones.required' => trans('validation_custom.required.terminos_y_condiciones')
    //     ];
    // }
    // 
    // protected function validator(array $data) {
    //     return Validator::make($data, [
    //         'nombre'                  =>    'required|min:3|max:255',
    //         'apellido'                =>    'required|min:3|max:255',
    //         'cuit'                    =>    'required|size:13',
    //         'documento'               =>    'required',
    //         'terminos_y_condiciones'  =>'required',
    //         'email'     =>    'required|email|max:255|unique:users,email,NULL,usuario_sistema,usuario_sistema,0,deleted_at,NULL',
    //     ], $this->validationErrorMessages());
    // }

    public function register(Request $request) {
      return null;
    //     $this->validator($request->all())->validate();
    //     $input = $request->all();
    //     $cuit = str_replace('-', "", $input['cuit']);
    //
    //   //  $reCaptchaRespuesta = $this->captcha($input['g-recaptcha-response']);
    //
    //     $errores = array();
    //
    //     // if(!$reCaptchaRespuesta->success){
    //     //   $errores['robot'] = trans('mensajes.error.captcha');
    //     // }
    //
    //     if(UserPublico::whereCuit($cuit)->first() != null) {
    //       $errores["cuit"] = trans('mensajes.error.cuit_existente');
    //     }
    //
    //     if(!$this->validarCuit($cuit)) {
    //       $errores["cuit"] = trans('login.cuit_invalido');
    //     }
    //     if(sizeof($errores) > 0) {
    //         return redirect()->route('registrarse')
    //                          ->withInput($input)
    //                          ->withErrors($errores);
    //     }
    //
    //     $user = $this->store($request);
    //
    //     if($user == null)
    //       return redirect()->route('registrarse');
    //
    //     $mensaje = trans('login.mail_registro_enviado');
    //     return view('auth.mensaje', compact('mensaje'));
    //
    //     $this->guard()->login($user);
    //
    //     return $this->registered($request, $user)
    //                     ?: redirect($this->redirectPath());
    }

    protected function store(Request $request) {
      return null;
    //     $input = $request->all();
    //
    //     $cuit = str_replace('-', "", $input['cuit']);
    //     $documento = str_replace('.', "", $input['documento']);
    //     $codigo_confirmacion = str_random(30);
    //
    //     $password = str_random(8);
    //     $letters = "abcdefghijklmnopqrstuvwxyz";
    //     $password = str_shuffle(strtoupper($letters[rand(0, 25)]).
    //                             rand(100, 999) . $letters[rand(0, 25)] . $letters[rand(0, 25)]);
    //
    //     try {
    //       $user = User::create([
    //           'nombre'                => $input['nombre'],
    //           'apellido'              => $input['apellido'],
    //           'email'                 => $input['email'],
    //           'codigo_confirmacion'   => $codigo_confirmacion,
    //           'usuario_sistema'       => 0,
    //           'password'              => bcrypt($password),
    //       ]);
    //
    //     } catch (QueryException $e) {
    //       Log::error('QueryException', ['Exception' => $e]);
    //       return null;
    //     }
    //
    // 		$user_publico = new UserPublico();
    //     $user_publico->user_id = $user->id;
    //     $user_publico->pais_id = $input['pais_id'];
    //     $user_publico->cuit = $cuit;
    //     $user_publico->tipo_documento_id = $input['tipo_documento'];
    //     $user_publico->documento = $documento;
    //
    //     try {
    //       $user_publico->save();
    //     } catch (QueryException $e) {
    //       Log::error('QueryException', ['Exception' => $e]);
    //       return null;
    //     }
    //
    //
    //     $widgets = Widget::whereBack(0)->get();
    //     $orden = 1;
    //     foreach ($widgets as $key => $widget) {
    //
    //       try {
    //           $user_widget = UserWidgets::create([
    //               'user_id' => $user->id,
    //               'widget_id' => $widget->id,
    //               'orden' => $orden,
    //             ]);
    //       }
    //     	catch(\QueryException $e) {
    //     	   Log::error('QueryException', ['Exception' => $e]);
    //     	   return redirect()->back()
    //     						 ->with(['error' => trans('error.user.guardando_en_db')]);
    //     	}
    //
    //       $orden++;
    //     }
    //
    //     try {
    //       $user->sendCreateUserNotification($password);
    //     } catch (\Swift_TransportException $e) {
    //       Log::error('Swift_TransportException', ['Exception' => $e]);
    //       return null;
    //     }
    //
    //     return $user;
    }

    public function terminosYCondiciones() {
      return view('auth.terminos_y_condiciones');
    }

    // public function captcha($reCaptchaRespuesta) {
    //
    //   $keySecret = env('SECRET_KEY_CAPTCHA_BACK');
    //   $reCaptchaRespuesta = $reCaptchaRespuesta;
    //   $url = 'https://www.google.com/recaptcha/api/siteverify';
    //
    //   $client = new Client(['verify'=>false]);
    //
    //   $resp = $client->post($url, [
    //       "form_params" => array(
    //       "secret"      => $keySecret,
    //       "response"    => $reCaptchaRespuesta
    //     )
    //   ]);
    //   $result = json_decode($resp->getBody()->getContents());
    //
    //   return $result;
    // }
}
