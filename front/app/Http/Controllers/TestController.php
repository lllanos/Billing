<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Log;
use Response;
use Storage;
use View;

use App\Grupo;
use App\Role;
use App\User;

use Yacyreta\Usuario\UserPublico;

class TestController extends Controller {

  public function index () {
    return view('test');
  }

  public function confirmarUsuario($cuit) {
    $user_publico = UserPublico::whereCuit($cuit)->first();
    $codigo_confirmacion = str_random(30);
    $password = 123456;

    $user = User::find($user_publico->user_id);
    $user->codigo_confirmacion = $codigo_confirmacion;
    $user->password = bcrypt($password);
    $user->save();

    return redirect()->guest("verificar/registro/$codigo_confirmacion");
  }

  public function validarCuit($cuit) {
      $cuit = str_replace(['_', '-'], "", $cuit);

      if(strlen($cuit) != 11) {
        return false;
      }

      $acumulado = 0;
      $digitos = str_split($cuit);
      $digito = array_pop($digitos);

      for($i = 0; $i < count($digitos); $i++) {
        $acumulado += $digitos[9 - $i] * (2 + ( $i % 6 ));
      }

      $verif = 11 - ($acumulado % 11);
      $verif = $verif == 11? 0 : $verif;

      return $digito == $verif;
  }
}
