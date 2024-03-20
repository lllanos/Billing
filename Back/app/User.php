<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use YacyretaTraits\YacyretaUserTrait;

use App\Notifications\ReConfirmUserNotification;
use App\Notifications\UserResetPasswordNotification;
use App\Notifications\UserCreateNotification;

use YacyretaNotifications\Contrato\SaltoNotification;
use YacyretaNotifications\SolicitudNotification;
use YacyretaNotifications\Contrato\VencimientoPoderNotification;

use Contrato\Contrato;
use Contrato\TipoContrato;

use SolicitudContrato\SolicitudContrato;

use SolicitudRedeterminacion\SolicitudRedeterminacion;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use Log;
use Auth;

class User extends Authenticatable
{
    use EntrustUserTrait, Notifiable;
    use SoftDeletes;
    use YacyretaUserTrait;

     protected $fillable = [
       'nombre', 'apellido', 'email', 'password', 'usuario_sistema', 'codigo_confirmacion'
     ];

    protected $hidden = [
      'password', 'remember_token',
    ];

    protected $casts = [
      'email_verified_at' => 'datetime',
    ];

		protected $auditable = true;

    public function isAuditable() {
      return $this->auditable;
    }

    public function getIsUserPublicoAttribute() {
      return false;
    }

    public function getIsUserAdminAttribute() {
      return true;
    }

    public function user_publico() {
      return $this->hasOne('Yacyreta\Usuario\UserPublico');
    }

    public function user_admin() {
      return $this->hasOne('Yacyreta\Usuario\UserAdmin');
    }

    public function roles() {
      return $this->belongsToMany('App\Role');
    }

    public function causante() {
      return $this->belongsTo('Yacyreta\Causante');
    }

    public function representante_eby() {
      return $this->hasMany('Contrato\RepresentanteEby', 'user_id');
    }

    // Dashboard
    public function widgets() {
      return $this->belongsToMany('Yacyreta\Dashboard\Widget', 'user_widgets', 'user_id', 'widget_id')->orderBy('orden');
    }

    public function layout() {
      return $this->belongsTo('Yacyreta\Dashboard\Layout');
    }
    // FIN Dashboard

    /////////// Notifications ////////////
    public function sendPasswordResetNotification($token) {
      $this->notify(new UserResetPasswordNotification($token));
    }

    public function sendCreateUserNotification($password) {
      $this->notify(new UserCreateNotification($password));
    }

    public function sendReConfirmUserNotification($token, $password) {
      $this->notify(new ReConfirmUserNotification($token, $password));
    }

    public function sendVencimientoPoderNotification($contrato_id) {
      try{
        $args  = array();
        $args['contrato_id'] = $contrato_id;
        $args['user_id'] = null;
        $users = $this->user_publico->user;

        \Notification::send($this, (new VencimientoPoderNotification(json_encode($args)))->onQueue('poderes'));
      } catch (\Swift_TransportException $e) {
        Log::error('Swift_TransportException', ['Exception' => $e]);
        
        return true;
      }
    }

    public function sendSolicitudNotification($alarma, $solicitud_id) {
      try{
        $args  = array();
        $args['solicitud_id'] = $solicitud_id;
        $args['alarma'] = $alarma->id;
        $args['user_id'] = null;

        \Notification::send($this, (new SolicitudNotification(json_encode($args)))->onQueue('redeterminaciones'));
      } catch (\Swift_TransportException $e) {
        Log::error('Swift_TransportException', ['Exception' => $e]);
        
        return true;
      }
    }
    /////////// FIN Notifications ////////////

    protected function updateAudit() {
      $auth = Auth::user();
      if($auth != null)
        $auth = $auth->id;

			if(!is_null('user_modifier_id') && !$this->isDirty('user_modifier_id')) {
				$this->user_modifier_id = $auth;
			}

			if(!$this->exists && ! is_null('user_creator_id') && !$this->isDirty('user_creator_id')) {
				$this->user_creator_id = $auth;
			}
			return $this;
		}

		////// Overriden //////
    protected function performInsert(\Illuminate\Database\Eloquent\Builder $query) {
      if($this->fireModelEvent('creating') === false) {
        return false;
      }

      if($this->usesTimestamps()) {
        $this->updateTimestamps();
      }

			if($this->isAuditable()) {
        $this->updateAudit();
      }

      $attributes = $this->getAttributes();

      if($this->getIncrementing()) {
        $this->insertAndSetId($query, $attributes);
      } else {
        if(empty($attributes)) {
          return true;
        }
        $query->insert($attributes);
      }
			$this->exists = true;

      $this->wasRecentlyCreated = true;

      $this->fireModelEvent('created', false);

      return true;
    }

    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query) {
			if($this->fireModelEvent('updating') === false) {
        return false;
      }

			if($this->usesTimestamps()) {
        $this->updateTimestamps();
      }

			if($this->isAuditable()) {
        $this->updateAudit();
      }

			$dirty = $this->getDirty();

      if(count($dirty) > 0) {
        $this->setKeysForSaveQuery($query)->update($dirty);

        $this->syncChanges();

        $this->fireModelEvent('updated', false);
      }

      return true;
    }
		////// FIN Overriden //////

    /////////// Accesors ////////////
    // Ejemplo de como Crear Atributes Custom (Accessors y Mutators)
    // Se usa: $user->apellido_nombre
    public function getApellidoNombreAttribute () {
      return $this->apellido . ', ' . $this->nombre;
    }

    public function getNombreApellidoAttribute () {
      return $this->nombre . ' ' . $this->apellido;
    }

    public function getFechaRegistroAttribute () {
      return $this->created_at;
    }

    public function getRolesStringAttribute () {
      foreach ($this->roles as $keyRol => $valueRol)
        $roles[] = $valueRol->name;

      if(isset($roles))
        return implode(", ", $roles);
      else
        return " ";
    }

    public function getNotUsuarioCausanteAttribute () {
      return !$this->usuario_causante;
    }

    public function getCausanteNombreColorAttribute () {
      $causante = $this->causante;

      if($causante == null) {
        $response['color'] = '263238';
        $response['nombre'] = trans('index.sin_causante');
      } else {
        $response['color'] = $causante->color;
        $response['nombre'] = $causante->nombre;
      }

     return $response;
    }

    public function getDocumentoAttribute () {
      if(!$this->usuario_sistema)
         return $this->user_publico->documento;
      else
         return null;
    }

    public function getPaisAttribute () {
      if(!$this->usuario_sistema)
         return $this->user_publico->pais->nombre;
      else
         return null;
    }

    public function getConfirmadoNombreColorAttribute () {
      $usuario = $this->user_publico;

      if($usuario != null) {
         if($this->confirmado){
             $response['color'] = '00C853';
             $response['nombre'] = trans('index.confirmado');
          }else{
             $response['color'] = '263238';
             $response['nombre'] = trans('index.sin_confirmar');
          }
      } else {
        $response['color'] = null;
        $response['nombre'] = null;
      }

      return $response;
    }

    public function getSolicitudesRedeterminacionAdminAttribute() {
      $user_admin = $this->user_admin;
      if($user_admin == null) {
        return 0;
      } else {
        $solicitudes_builder = SolicitudRedeterminacion::orderBy('ultimo_movimiento', 'DESC');

        $solicitudes_builder = $solicitudes_builder->with('contrato')->with('contrato.causante')
                                                   ->with('contrato.contratista')
                                                   ->with('salto.contrato_moneda.moneda')
                                                   ->with('instancia_actual');
        if($this->usuario_causante) {
          // Si es de causante: las del suyo
          return $solicitudes_builder->get()->filter(function($redeterminacion) {
                                               return $redeterminacion->causante_id == $this->causante_id;
                                            });
        }
        return $solicitudes_builder->get();
      }
    }

    public function getSolicitudesRedeterminacionTiemposAdminAttribute() {
      $user_admin = $this->user_admin;
      if($user_admin == null) {
        return 0;
      } else {
        $model = new SolicitudRedeterminacion();
        $solicitudes_builder = SolicitudRedeterminacion::with('contrato')->with('contrato.causante')
                                                       ->with('instancias')->with('instancias.solicitud')
                                                       ->with('instancias.tipo_instancia')
                                                       ->with('instancias.AprobacionCertificados')
                                                       ->with('instancias.AsignacionPartidaPresupuestaria')
                                                       ->with('instancias.CalculoPreciosRedeterminados')
                                                       ->with('instancias.EmisionCertificadoRDP')
                                                       ->with('instancias.FirmaResolucion')
                                                       ->with('instancias.GeneracionExpediente')
                                                       ->with('instancias.ProyectoActaRDP')
                                                       ->with('instancias.VerificacionDesvio')
                                                       ->with('instancias.Iniciada')
                                                   ;
        if($this->usuario_causante) {
          // Si es de causante: las del suyo
          return $solicitudes_builder->get()->filter(function($redeterminacion) {
                                               return $redeterminacion->causante_id == $this->causante_id;
                                            });
        }
        return $solicitudes_builder->get();
      }
    }

    public function getContratosAdminAttribute() {
      $user_admin = $this->user_admin;
      if($user_admin == null) {
        return collect();
      } else {
        $tipoContrato = TipoContrato::whereNombre('contrato')->first();
        $contratos_builder = Contrato::whereTipoId($tipoContrato->id);
        // Si es de causante: los del suyo
        if($this->usuario_causante) {
          $contratos_builder = $contratos_builder->whereCausanteId($this->causante_id);
        }

        $contratos_builder = $contratos_builder->with('causante')->with('contratista')
                                               ->with('contratos_monedas')->with('contratos_monedas.moneda')
                                               ->with('estado');

        return $contratos_builder->orderBy('id', 'DESC')->get();
      }
    }

    public function getContratosInspectorAttribute() {
      if($this->cant('realizar-inspeccion')) {
        return collect();
      } else {
        return $this->contratos_admin->filter(function($contrato) {
          return $contrato->representante_eby->contains('user_id', $this->id);
        });
      }
    }

    public function getSolicitudesAdminAttribute() {
      $user_admin = $this->user_admin;
      if($user_admin == null) {
        return 0;
      } else {
        if(!$this->usuario_causante) {
          // Si no es de causante: todas
          return SolicitudContrato::all();
        } else {
          // Si es de causante: las del suyo
          return SolicitudContrato::all()->filter(function($solicitud) {
            return $solicitud->causante_id == $this->causante_id;
          });
        }
      }
    }

    public function getCantidadSolicitudesContratoAttribute() {
      $user_publico = $this->user_publico;
      if($user_publico == null)
        return 0;
      else
        return $user_publico->cantidad_solicitudes_contrato;
    }

    public function getCantidadContratosAttribute() {
      return 0;
      $user_publico = $this->user_publico;
      if($user_publico != null)
        return $this->user_publico->cantidad_contratos;
      else
        return 0;
    }

    public function getSolicitudesContratoAttribute() {
      $user_publico = $this->user_publico;
      if($user_publico != null)
        return $this->user_publico->solicitudes_contrato;
      else
        return array();
      }
    /////////// FIN Accesors ////////////

    /**
     * @param  int $causante_id
     */
    public function puedeVerCausante($causante_id) {
      return !$this->usuario_causante || $this->causante_id == $causante_id;
    }

    /**
     * @param  Contrato\TipoContrato $tipo_contrato
     */
    public function puedeVerTipoContrato($tipo_contrato) {
      return Auth::user()->can($tipo_contrato->nombre . '-view');
    }

    /**
     * @param string $modulo
     */
    public function puedeVerModulo ($modulo) {
      foreach ($this->roles as $key => $rol)
        foreach ($rol->permissions as $key => $permission)
          if($permission->modulo == $modulo)
            return true;

      return false;
    }

    /**
     * @param  Contrato\Contrato $contrato
     */
    public function puedeVerContrato($contrato) {
      return $this->puedeVerCausante($contrato->causante_id);
    }

}
