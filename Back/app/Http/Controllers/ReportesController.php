<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

use DateTime;
use DB;
use Log;
use Redirect;
use Response;
use View;
use DatePeriod;
use DateInterval;

use App\User;
use Yacyreta\Causante;
use Yacyreta\Moneda;

use Contrato\Contrato;
use Contratista\Contratista;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\RepresentanteEby;
use Contrato\EstadoContrato;

use Yacyreta\Reporte\Reporte;

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class ReportesController extends Controller {

    public function __construct() {
      View::share('ayuda', 'reportes');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      $user = Auth::user();
      $reportes = Reporte::all()->filter(function($reporte) use($user) {
                                  return $user->can($reporte->nombre);
                                });

      return view('reportes.index', compact('reportes', 'search_input'));
    }

    /**
     * @param  string  $nombre
    */
    public function generar($nombre) {
      $reporte = Reporte::whereNombre($nombre)->firstOrFail();
      if(Auth::user()->cant($nombre)) {
        $jsonResponse['message'] = [trans('mensajes.error.permisos')];
        Session::flash('error', trans('mensajes.error.permisos'));
        $jsonResponse['status'] = false;
        return redirect()->route('reportes.index');
      }

      return view('reportes.generar', compact('reporte'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportar($nombre, Request $request) {
      if(Auth::user()->cant($nombre)) {
        $jsonResponse['message'] = [trans('mensajes.error.permisos')];
        Session::flash('error', trans('mensajes.error.permisos'));
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      $input = $request->except(['_token']);

      $errores = array();      
      if(DateTime::createFromFormat('d/m/Y', $input['periodo']['hasta']) <= DateTime::createFromFormat('d/m/Y', $input['periodo']['desde'])) {
        $errores['fechas'] = trans('reportes.error.hasta_anterior_desde');        
      }

      if(count($errores) > 0) {
        $jsonResponse['message'] = [trans('reportes.error.revisar')];
        $jsonResponse['errores'] = $errores;
        Session::flash('error', trans('reportes.error.revisar'));
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      $reporte = Reporte::whereNombre($nombre)->firstOrFail();

      $response = $this->procesarFiltros($input, $nombre);
//dd($response);
      //validar fechas en ReporteFinanciero
      if(isset($response['errores'])){
        Session::flash('error', trans('mensajes.error.revisar'));
        return response()->json($response);
      }

      $reporte = $reporte->getModel();
      $datos = $reporte->getDatos($response);

      if(count($datos) == 0) {
        $jsonResponse['message'] = [trans('reportes.error.no_data')];
        $jsonResponse['errores'] = array();
        Session::flash('error', trans('reportes.error.no_data'));
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      // Que cada reporte lo arme a su manera en sus respectivos modelos con los datos ya filtrados
      $filename = $reporte->generarXlsx($datos, $response['filtros'], $response['periodo_meses']);

      // Usando "/excel/exports/'" en vez de "storage_path('excel/exports') .'/'" lo descarga automaticamente
      return Response::json(array(
            'status'  => true,
            'href'    => '/excel/exports/' . $filename['file'],
      ));
    }

    /**
     * @param  array  $filtros
     * @param  string  $nombre
     */
    public function procesarFiltros($filtros, $nombre) {
      
      $response['contrato_ids'] = array();
      $response['moneda_id'] = null;
      $response['estado_redeterminacion_id'] = array();
      $response['filtros'] = $filtros;
      
      
      $primero = true;
      foreach ($response['filtros'] as $keyFiltro => $valueFiltro) {

        if($keyFiltro == 'contrato') {
          if($valueFiltro != null) {
            $array_contrato_ids[] = $valueFiltro;
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $array_contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $array_contrato_ids);
            }
            $response['filtros'][$keyFiltro] = Contrato::find($valueFiltro)->nombre_completo;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

        if($keyFiltro == 'inspector') {
          if($valueFiltro != null) {
            $contrato_ids = RepresentanteEby::select('contrato_id')->whereUserId($valueFiltro)
                                            ->pluck('contrato_id', 'contrato_id')->toArray();
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = User::find($valueFiltro)->apellido_nombre;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

        if($keyFiltro == 'estado_contrato') {
          if($valueFiltro != null) {
            $contrato_ids = Contrato::select('id')->whereEstadoId($valueFiltro)
                                    ->pluck('id', 'id')->toArray();
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = EstadoContrato::find($valueFiltro)->nombre_trans;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

        if($keyFiltro == 'causante') {
          if(Auth::user()->usuario_causante)
            $valueFiltro = Auth::user()->causante_id;

          if($valueFiltro != null) {
            $contrato_ids = Contrato::select('id')->whereCausanteId($valueFiltro)
                                    ->pluck('id', 'id')->toArray();
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = Causante::find($valueFiltro)->nombre;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

        if($keyFiltro == 'contratista') {
          if($valueFiltro != null) {
            $contrato_ids = Contrato::select('id')->whereContratistaId($valueFiltro)
                                    ->pluck('id', 'id')->toArray();
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = Contratista::find($valueFiltro)->nombre_documento;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }

        }

        if($keyFiltro == 'moneda') {
          if($valueFiltro != null) {
            $contrato_ids = ContratoMoneda::select('clase_id')->whereMonedaId($valueFiltro)
                                          ->pluck('clase_id', 'clase_id')->toArray();
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = Moneda::find($valueFiltro)->nombre_simbolo;
            $response['moneda_id'] = $valueFiltro;
          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

        if($keyFiltro == 'periodo') {
          if($valueFiltro != null) {
            $desde = $this->fechaDeA($valueFiltro['desde'], 'd/m/Y', 'Y-m-d');
            $hasta = $this->fechaDeA($valueFiltro['hasta'], 'd/m/Y', 'Y-m-d');

            //validar fecha
            if($nombre == 'ReporteFinanciero'){
              $hoy = new DateTime();
              $error = null;
              /*if($hoy->format('Y-m-d') > $hasta)
                    $error = true;*/

              if($error) {
                $errores['periodo_hasta'] = trans('reportes.error.fecha_hasta_financiero');
                $response['status'] = false;
                $response['errores'] = $errores;
                $response['message'] = [trans('mensajes.error.revisar')];
                return $response;
              }
            }

            $response['periodo_meses'] = $this->getMesesPeriodo($desde, $hasta);
//dd($response['periodo_meses']);
            if(in_array($nombre, ["ReporteAdendas", "ReporteRedeterminaciones"])) {
              $contrato_ids = Contrato::select('id')->whereBetween('fecha_acta_inicio', [$desde, $hasta])
                                      ->pluck('id', 'id')->toArray();
            } elseif(in_array($nombre, ["ReporteEconomico", "ReporteFisico", "ReporteFinanciero"])) {
              $desde_Ym = $this->fechaDeA($valueFiltro['desde'], 'd/m/Y', 'Ym');
              $hasta_Ym = $this->fechaDeA($valueFiltro['hasta'], 'd/m/Y', 'Ym');
              // Uso DB::table('v_planificado_ejecutado_basico') en vez de $contrato_ids = ReporteEconomico
              // por performance
              //dd($desde_Ym, $hasta_Ym);
              $contrato_ids = DB::table('v_planificado_ejecutado_basico')
                                ->select('contrato_id')->whereBetween('mes_calendario', [$desde_Ym, $hasta_Ym])
                                ->pluck('contrato_id', 'contrato_id')->toArray();
                     
            }
            if($primero) {
              $primero = false;
              $response['contrato_ids'] = $contrato_ids;
            } else {
              $response['contrato_ids'] = array_intersect($response['contrato_ids'], $contrato_ids);
            }
            $response['filtros'][$keyFiltro] = $valueFiltro['desde'] . ' - ' . $valueFiltro['hasta'];
          }
        }

        if($keyFiltro == 'estado_redeterminacion') {
          if($valueFiltro != null) {
            $response['estado_redeterminacion_id'] = $valueFiltro;
            if($valueFiltro == 'aprobadas')
              $response['filtros'][$keyFiltro] = trans('forms.sol_redeterminaciones_aprobadas_tag');
            else
              $response['filtros'][$keyFiltro] = trans('forms.sol_redeterminaciones_en_proceso_tag');

          } else {
            $response['filtros'][$keyFiltro] = trans('forms.todos.' . $keyFiltro);
          }
        }

      }

      return $response;
    }


    /**
     * @param  array $array
     * @param  int $cantidad
    */
    public function getMesesPeriodo($desde, $hasta) {
      $desde = (new DateTime($desde))->modify('-1 month');
      $start    = $desde->modify('first day of this month');
      $end      = (new DateTime($hasta))->modify('first day of next month');
      $interval = DateInterval::createFromDateString('1 month');
      $period   = new DatePeriod($start, $interval, $end);

      foreach ($period as $keyDate => $valueDate) {
         $date = $valueDate->format('Ym');
         $array[] = $date;
      }
      return $array;
    }
  }
