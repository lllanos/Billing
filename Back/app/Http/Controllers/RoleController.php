<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Log;
use Redirect;
use Response;
use View;

use App\Permission;
use App\Role;
use AlarmaSolicitud\AlarmaSolicitud;

class RoleController extends Controller {

    public function __construct() {
      View::share('ayuda', 'seguridad');
    }

    protected function validationErrorMessages() {
      return [
        'name.unique' => trans('validation_custom.distinct.rol_nombre')
      ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      $roles = Role::all();//::paginate(config('custom.items_por_pagina'));

      return view('roles.index', compact('roles', 'search_input'));
    }

    public function create() {
      $modulos = Permission::all()->groupBy('modulo')->transform(function($item, $k) {return $item->groupBy('submodulo');});

      return view('roles.create', compact('modulos'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function store(Request $request) {
      $this->validate($request, [
              'name' => 'required|min:3|max:255|unique:roles,name',
            ],
            $this->validationErrorMessages()
          );

      $input = $request->all();
      $role = new Role();
      $role->name = $input['name'];

      try{
        $role->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        return redirect()->route('seguridad.roles.index')
                         ->with(['error' => trans('error.user.guardando_en_db')]);
      }

      if(isset($input['permission'])) {
        foreach ($input['permission'] as $key => $value) {
          $role->attachPermission($value);
        }
      }

      return redirect()->route('seguridad.roles.index')
                       ->with(['success' => trans('mensajes.dato.rol').trans('mensajes.success.creado')]);
    }

    /**
    * @param int $id
    */
    public function edit($id) {
      $modulos = Permission::all()->groupBy('modulo')->transform(function($item, $k) {return $item->groupBy('submodulo');});
      $role = Role::findOrFail($id);

      $rolePermissions = $role->belongsToMany('App\Permission')
                              ->pluck('permission_id','permission_id')->toArray();

      return view('roles.edit', compact('role', 'rolePermissions', 'modulos'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function update(Request $request, $id) {
      $this->validate($request, [
            'name' => 'required|min:5|max:255|unique:roles,name,' . $id . ',id',
          ],
          $this->validationErrorMessages()
        );

      $input = $request->all();

      $role = Role::find($id);
      $role->name = $input['name'];
      $role->save();

      $role->detachPermissions($role->permissions);

      if(isset($input['permission'])) {
        foreach ($input['permission'] as $key => $value) {
          $role->attachPermission($value);
        }
      }

      return redirect()->route('seguridad.roles.index')
                       ->with(['success' => trans('mensajes.dato.rol').trans('mensajes.success.actualizado')]);
    }

    /**
    * @param int $id
    */
    public function preDelete($id) {
      $alarma = AlarmaSolicitud::whereRoleId($id)->first();
      if($alarma != null) {
        $jsonResponse['status'] = false;
        $jsonResponse['title'] = trans('index.eliminar') . ' ' . trans('index.rol');
        $jsonResponse['message'] = [trans('index.no_puede_eliminar.rol')];
        return response()->json($jsonResponse);
      } else {
        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
      }
    }

    /**
    * @param int $id
    */
    public function delete($id) {
      if($this->preDelete($id)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('index.no_puede_eliminar.rol')];
        return response()->json($jsonResponse);
      }

      $role = Role::find($id);

      try{
        $role->detachPermissions($role->permissions);
        $role->delete();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['action']['function'] = "deleteRow";
      $jsonResponse['action']['params'] = 'role_' . $id;
      $jsonResponse['message'] = [trans('mensajes.dato.rol') . trans('mensajes.success.eliminado')];

      return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportar(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $roles = Role::all();
      $roles_export = array();
      if($filtro == null || $filtro == 'undefined') {
        foreach ($roles as $key => $rol) {
          if($rol != null) {
            $roles_export[$key]['Nombre'] = $rol->name;
          }
        }
      } else {
        $filtro_array = array();
        array_push($filtro_array, $filtro);
        foreach ($roles as $key => $rol) {
          if($rol != null) {
            if( $this->filtrarBusqueda($rol->name, $filtro_array) ) {
              $roles_export[$key]['Nombre'] = $rol->name;
            }
          }
        }
      }

      return $this->toExcel(trans('index.roles'), $roles_export);
    }
}
