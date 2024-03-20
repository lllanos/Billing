<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use Log;
use View;
use Redirect;

use Yacyreta\Dashboard\Layout;
use Yacyreta\Dashboard\UserWidgets;
use Yacyreta\Dashboard\Widget;

use Contrato\EstadoContrato;
use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

class DashboardController extends Controller {
    // Como agregar Widgets:
    // 1) Agregar un permiso con ese nombre (ej: nuevo_widget)
    //  Usar _ en vez de - por el nombre de la vista
    // 2) Agregarlo a la tabla de widgets con ese nombre
    // 3) Crear una vista dentro del directorio widgets del modulo
    //     ej: dashboard->widgets->nuevo_widget
    // 4) Crear una funcion en el Controller con ese nombre
    //     ej: public function nuevo_widget()

    public function __construct() {
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function index() {
      $user = Auth::user();
      $widgets = Widget::all();
      $userWidgets = null;
      $layout = Layout::find($user->layout_id)->nombre;
      $layouts = Layout::all();

      $userWidgetsEdit = [];
      if($user->widgets != null)
        $userWidgetsEdit = $user->widgets->pluck('nombre', 'id')->toArray();

      if(sizeof($widgets) > 0) {
        if($this->tieneAlgunPermiso($widgets->pluck('nombre')->toArray())) {
          $userWidgets = $user->widgets;
    		  if($user->layout_id == null) {
    			  $user->layout_id = 1;
    			  $user->save();
    		  }
        }
      }

      return view('inicio', compact('userWidgets', 'userWidgetsEdit', 'layout', 'layouts', 'widgets'));
    }

    ///////////// Widgets /////////////
    /**
     * @param  string $widget
    */
    public function widget($widget) {
      return $this->$widget();
    }

    public function contratos_por_estado() {
      $estados = EstadoContrato::all();

      $estados_nombre_color = array();
      foreach ($estados as $keyEstado => $valueEstado) {
        $estados_nombre_color[$valueEstado->id] = $valueEstado->nombre_color;
      }
      $contratos_admin = Auth::user()->contratos_admin;

      $contratos = $contratos_admin;
      $contratos_por_estado = $contratos_admin->filter(function($contrato) {
                                return !$contrato->borrador;
                              })->groupBy(function($contrato, $key) {
                                return $contrato->estado_id;
                              });

      // Necesita 0, 1, 2, etc
      $series_ids = array();
      $ids = 0;
      foreach ($contratos_por_estado as $keyCon => $valueCon) {
        $series_ids[$keyCon] = $ids;
        $ids ++;
      }

      $serie1 = array();
      foreach ($contratos_por_estado as $keyCon => $valueCon) {
        $serie1[$series_ids[$keyCon]]['color'] = '#' . $estados_nombre_color[$keyCon]['color'];
        $serie1[$series_ids[$keyCon]]['name'] = $estados_nombre_color[$keyCon]['nombre'];
        $serie1[$series_ids[$keyCon]]['y'] = (int) sizeof($valueCon);
      }

      if(count($serie1) > 0) {
        return View::make('dashboard.widgets.contratos_por_estado', compact('serie1'))->render();
      }
      else {
        return View::make('dashboard.widgets.no_data')->render();
      }
    }

    public function mis_asignaciones() {
      $user = Auth::user();
      if($user->cant('realizar-inspeccion'))
        return View::make('dashboard.widgets.no_data')->render();

      $contratos_inspector = Auth::user()->contratos_inspector;

      $cantidad_contratos = sizeof($contratos_inspector);
      $contratos = $contratos_inspector->take(config('custom.items_por_widget'));

      return View::make('dashboard.widgets.mis_asignaciones', compact('contratos', 'cantidad_contratos'))->render();
    }

    public function redeterminaciones_por_estado() {
      $tipos_instancia = TipoInstanciaRedet::all();

      $tipos_instancia_nombre_color = array();
      foreach ($tipos_instancia as $keyTipoInstancia => $valueTipoInstancia) {
        $tipos_instancia_nombre_color[$valueTipoInstancia->id] = $valueTipoInstancia->estado_nombre_color;
      }

      $solicitudes_admin = Auth::user()->solicitudes_redeterminacion_admin;

      $redeterminaciones = $solicitudes_admin;
      $redeterminaciones_por_estado = $solicitudes_admin
          ->filter(function($redeterminacion) {
            if(!$redeterminacion->finalizada && !$redeterminacion->anulada && !$redeterminacion->suspendida)
              return true;
          })->groupBy(function($redeterminacion, $key) {
            return $redeterminacion->instancia_actual->tipo_instancia_id;
          });

      // Necesita 0, 1, 2, etc
      $series_ids = array();
      $ids = 0;
      foreach ($redeterminaciones_por_estado as $keyRed => $valueRed) {
        $series_ids[$keyRed] = $ids;
        $ids ++;
      }

      $serie1 = array();
      foreach ($redeterminaciones_por_estado as $keyRed => $valueRed) {
        $serie1[$series_ids[$keyRed]]['color'] = '#' . $tipos_instancia_nombre_color[$keyRed]['color'];
        $serie1[$series_ids[$keyRed]]['name'] = $tipos_instancia_nombre_color[$keyRed]['nombre'];
        $serie1[$series_ids[$keyRed]]['y'] = (int) sizeof($valueRed);
      }

      if(count($serie1) > 0) {
        return View::make('dashboard.widgets.redeterminaciones_por_estado', compact('serie1'))->render();
      } else {
        return View::make('dashboard.widgets.no_data')->render();
      }
    }

    public function tiempos_redeterminaciones() {
      $tipos_instancia_iniciada = TipoInstanciaRedet::select('id')
                                                    ->where('modelo', '=', 'Iniciada')
                                                    ->first();

      $tipos_instancia = TipoInstanciaRedet::where('orden', '!=', null)
                                              ->where('modelo', '!=', 'Iniciada')
                                              ->get();

      $solicitudes_admin = Auth::user()->solicitudes_redeterminacion_tiempos_admin;
      $instancias = collect();
      foreach ($solicitudes_admin as $keySolicitud => $valueSolicitud) {
        $instancias = $instancias->merge($valueSolicitud->instancias);
      }

      $tiempos_promedios = array();
      $cantidades = array();

      foreach ($instancias as $keyInstancia => $valueInstancia) {
        $id = $valueInstancia->tipo_instancia_id;
        if($id != $tipos_instancia_iniciada->id) {
          if(!isset($tiempos_promedios[$id])) {
            $tiempos_promedios[$id] = 0;
            $cantidades[$id] = 0;
          }
          $tiempos_promedios[$id] = $tiempos_promedios[$id] + $valueInstancia->inicio_fin_diff;
          $cantidades[$id]++;
        }
      }

      foreach ($tiempos_promedios as $keyTiempo => $valueTiempo) {
        $tiempos_promedios[$keyTiempo] = (float)number_format($tiempos_promedios[$keyTiempo] / $cantidades[$keyTiempo], 2);
      }

      $serie1 = array();
      $pointPlacement = -0.3;
      $i = 0;
      foreach ($tipos_instancia as $keyTipoInstancia => $valueInstancia) {
        // Esperado
        if(isset($tiempos_promedios[$valueInstancia->id])) {
          $serie1[$i]['name'] = trans('sol_redeterminaciones.instancias.' . $valueInstancia->modelo);
          $serie1[$i]['color'] = $this->hex2rgba('#'.$valueInstancia->color, 1);
          $serie1[$i]['data'] = [$valueInstancia->plazo];
          $serie1[$i]['pointPadding'] = 0.5;
          $serie1[$i]['pointWidth'] = 15;
          $serie1[$i]['pointPlacement'] = $pointPlacement;

          $i++;

          // Estimado
          $serie1[$i]['name'] = trans('sol_redeterminaciones.instancias.' . $valueInstancia->modelo);
          $serie1[$i]['linkedTo'] = ':previous';
          $serie1[$i]['color'] = $this->hex2rgba('#'.$valueInstancia->color, 0.7);
          $serie1[$i]['data'] = [$tiempos_promedios[$valueInstancia->id]];
          $serie1[$i]['pointPadding'] = 0.4;
          $serie1[$i]['pointWidth'] = 30;
          $serie1[$i]['pointPlacement'] = $pointPlacement;

          $pointPlacement = $pointPlacement  + 0.1;
          $i++;
        }
      }

      if(count($serie1) > 0) {
        ksort($serie1);
        return View::make('dashboard.widgets.tiempos_redeterminaciones', compact('serie1'))->render();
      }
      else {
        return View::make('dashboard.widgets.no_data')->render();
      }
    }

    ///////////// FIN Widgets /////////////

    ///////////// Configuracion /////////////
    public function elegirLayout() {
      $layouts = Layout::all();
      return view('dashboard.editLayout', compact('layouts'));
    }

    public function updateLayout($id) {
      $user = Auth::user();
      $user->layout_id=$id;

      try {
        $user->save();
      }
    	catch(\QueryException $e) {
    	   Log::error('QueryException', ['Exception' => $e]);
    	   return redirect()->back()
    						 ->with(['error' => trans('error.user.guardando_en_db')]);
    	}

      return Redirect::to('/');
    }

    public function editWidgets() {
      $user = Auth::user();
      $layouts = Layout::all();
      $widgets = Widget::all();
      $userWidgets = $user->widgets->pluck('nombre', 'id')->toArray();

      return view('dashboard.editWidgets', compact('userWidgets', 'widgets'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateWidgets(Request $request) {
      $user = Auth::user();
      $i = 1;
      UserWidgets::where('user_id', '=', $user->id)->delete();
      if($request->input('widget') != null) {
        foreach ($request->input('widget') as $key => $value) {

          try {
              $user_widget = UserWidgets::create([
                  'user_id'   => $user->id,
                  'widget_id' => $key,
                  'orden'     => $i,
                ]);
          }
        	catch(\QueryException $e) {
        	   Log::error('QueryException', ['Exception' => $e]);
        	   return redirect()->back()
        						 ->with(['error' => trans('error.user.guardando_en_db')]);
        	}

          $i++;
        }
      }

      return Redirect::to('/');
    }
    ///////////// FINVConfiguracion /////////////

}
