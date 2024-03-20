<?php

namespace App\Http\Controllers\Redeterminaciones;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use DateTime;
use DB;
use Log;
use Redirect;
use Response;
use Storage;
use View;

use App\User;

use Yacyreta\Causante;
use Yacyreta\Moneda;

use Contrato\Contrato;
use Contrato\EstadoContrato;

use Contrato\ContratoMoneda\ContratoMoneda;

use Itemizado\Itemizado;
use Itemizado\Item;

use Indice\IndiceTabla1;
use AnalisisPrecios\AnalisisItem;
use AnalisisPrecios\AnalisisPrecios;
use AnalisisPrecios\Categoria\CategoriaModelExtended;
use AnalisisPrecios\Categoria\Componente\ComponenteModelExtended;

use Indice\PublicacionIndice;
use Redeterminacion\CategoriaRedeterminacion;
use Redeterminacion\ComponenteRedeterminacion;
use Redeterminacion\PrecioRedeterminadoItem;
use Redeterminacion\Redeterminacion;

use App\Http\Controllers\Contratos\ContratosControllerExtended;
class RedeterminacionesController extends ContratosControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

//////////// Analisis de Precios ////////////

    /**
     * @param  int    $redeterminacion_id
    */
    public function ver($redeterminacion_id) {
      $redeterminacion = Redeterminacion::findOrFail($redeterminacion_id);

      if(!Auth::user()->puedeVerContrato($redeterminacion->contrato)) {
        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      $edit = false;
      return view('redeterminaciones.redeterminaciones.edit', compact('redeterminacion', 'edit'));
    }

    public function analisisItemVer($item_id) {
      $analisis_item = AnalisisItem::where('id', $item_id)->firstOrFail();
      $analisis_precios = AnalisisPrecios::where('id', $analisis_item->analisis_precios_id)->first();

      if(!Auth::user()->puedeVerContrato($analisis_precios->contrato)) {
        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      $edit = false;
      $redetermina = true;
      return view('analisis_precios.analisis_item.createEdit', compact('analisis_item', 'edit', 'redetermina'));
    }

}
