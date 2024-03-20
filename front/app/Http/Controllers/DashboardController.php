<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use View;
use Redirect;

use Yacyreta\Dashboard\Layout;
use Yacyreta\Dashboard\UserWidgets;
use Yacyreta\Dashboard\Widget;

class DashboardController extends Controller {
    // Como agregar Widgets:
    // 1) Agregar un  widget (ej: nuevo_widget) con back = 0
    // 3) Crear una vista dentro del directorio widgets del modulo
    //     ej: dashboard->widgets->nuevo_widget
    // 4) crea una funcion en el Controller con ese nombre
    //     ej: public function nuevo_widget()

    // public function __construct()
    // {
    //     $this->middleware('auth', ['except' => 'logout']);
    // }

    public function index() {
      if (Auth::check()) {
        return $this->dashboard();
      } else {
        return view('index.index_guest');
      }
    }

    public function dashboard() {
      $user = Auth::user();
      $widgets = Widget::whereBack(0)->get();
      $userWidgets = null;
      $layout = Layout::find($user->layout_id)->nombre;
      $layouts = Layout::all();

      if($user->widgets != null)
        $userWidgetsEdit = $user->widgets->pluck('nombre', 'id')->toArray();

      if(sizeof($widgets) > 0) {
        $userWidgets = $user->widgets;
  		  if($user->layout_id == null) {
  			  $user->layout_id = 1;
  			  $user->save();
  		  }
      }

      return view('index.index', compact('userWidgets', 'userWidgetsEdit', 'layout', 'layouts', 'widgets'));
    }

    ///////////// Widgets /////////////
    /**
     * @param  string $widget nombre del widget de acuerdo a "Como agregar Widgets"
    */
    public function widget($widget) {
      return $this->$widget();
    }

    public function mis_solicitudes() {
      $solicitudes = Auth::user()->user_publico->solicitudes_de_mis_contratos
                                       ->sortByDesc('ultimo_movimiento')->take(config('custom.items_por_widget'));
      $cantidad_solicitudes = sizeof(Auth::user()->user_publico->solicitudes_de_mis_contratos
                                                 ->sortByDesc('ultimo_movimiento'));

      return View::make('dashboard.widgets.mis_solicitudes', compact('solicitudes', 'cantidad_solicitudes'))->render();
    }

    public function mis_contratos() {
      $user_publico = Auth::user()->user_publico;

      // Primera tabla (Solicitudes de Asociacion)
      $solicitudes_contrato = $user_publico->solicitudes_contrato->filter(function($solicitud) {
          if(!$solicitud->instancia_actual->esta_aprobada)
            return true;
      });

      $cantidad_solicitudes = sizeof($solicitudes_contrato);
      $solicitudes_contrato = $solicitudes_contrato->take(config('custom.items_por_widget') / 2);

      // Segunda tabla (Contratos Asociados)
      $user_contratos =  $user_publico->contratos->take(config('custom.items_por_widget') / 2);

      return View::make('dashboard.widgets.mis_contratos', compact('user_contratos', 'solicitudes_contrato', 'cantidad_solicitudes'))->render();
    }
    ///////////// FIN Widgets /////////////

    ///////////// Configuracion /////////////
    public function elegirLayout() {
        $layouts = Layout::all();
        return view('dashboard.editLayout', compact('layouts'));
    }

    /**
     * @param  int $id
    */
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
    ///////////// FIN Configuracion /////////////

}
