<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use YacyretaTraits\YacyretaUserTrait;

use App\Notifications\ReConfirmUserNotification;
use App\Notifications\UserResetPasswordNotification;
use App\Notifications\UserCreateNotification;

use YacyretaNotifications\SolicitudNotification;

use YacyretaNotifications\Contrato\SaltoNotification;
use YacyretaNotifications\Contrato\AsociacionGestionadaNotification;
use Carbon\Carbon;
use Auth;

// Overriden de Notifications por SQLServer
use App\Notifications\DatabaseNotificationYacyreta;

class User extends Authenticatable {
    use EntrustUserTrait, Notifiable;
    use SoftDeletes;
    use YacyretaUserTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nombre', 'apellido', 'email', 'password', 'usuario_sistema', 'codigo_confirmacion'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getIsUserPublicoAttribute() {
      return true;
    }

    public function getIsUserAdminAttribute() {
      return false;
    }

    // Datos del usuario del front
    public function user_publico() {
      return $this->hasOne('Yacyreta\Usuario\UserPublico');
    }

    public function getContratosAttribute () {
      return $this->user_publico->contratos;
    }

    // Notifications
    public function sendPasswordResetNotification($token) {
      $this->notify(new UserResetPasswordNotification($token));
    }

    public function sendCreateUserNotification($password) {
      $this->notify(new UserCreateNotification($password));
    }

    public function sendReConfirmUserNotification($token, $password) {
      $this->notify(new ReConfirmUserNotification($token, $password));
    }

    public function sendSolicitudNotification($alarma, $solicitud_id) {
      $args  = array();
      $args['solicitud_id'] = $solicitud_id;
      $args['alarma'] = $alarma->id;
      $args['user_id'] = null;

      $this->notify((new SolicitudNotification(json_encode($args)))->onQueue('redeterminaciones'));
    }

    public function sendSaltoNotification($variacion_id) {
      $args  = array();
      $args['variacion_id'] = $variacion_id;
      $args['user_id'] = null;

      $this->notify((new SaltoNotification(json_encode($args)))->onQueue('calculos_variacion'));
    }

    // FIN Notifications

    // Dashboard
    public function widgets() {
        return $this->belongsToMany('Yacyreta\Dashboard\Widget', 'user_widgets', 'user_id', 'widget_id')->orderBy('orden');
    }

    public function layout() {
      return $this->belongsTo('Yacyreta\Dashboard\Layout');
    }
    // FIN Dashboard

    // Ejemplo de como Crear Atributes Custom (Accessors y Mutators)
    // Se usa: $user->apellido_nombre
    public function getApellidoNombreAttribute () {
      return $this->apellido.', '.$this->nombre;
    }

    public function getNombreApellidoAttribute () {
      return $this->nombre.' '.$this->apellido;
    }

    public function getUserPublicoIdAttribute () {
      return $this->user_publico->id;
    }

    public function getUserContratistaIdAttribute () {
      return $this->user_publico->id;
    }

    public function getRolesStringAttribute () {
      foreach ($this->roles as $keyRol => $valueRol)
        $roles[] = $valueRol->name;

      if(isset($roles))
        return implode(", ", $roles);
      else
        return " ";
    }

    // Devuelve un array con todos los permisos que tiene el usuario
    public function getPermisosAttribute () {
      $permissions = array();
      foreach ($this->roles as $key => $rol)
        foreach ($rol->permissions as $key => $permission)
          array_push($permissions, $permission->name);

      return $permissions;
    }

    /**
     * @param  SolicitudContrato\UserContrato $user_contrato
     * @param  Contrato\Contrato | nullable $adenda
     */
    public function puedeSolicitarRedeterminacion($user_contrato, $adenda = null) {
      $contrato = $user_contrato->contrato;
      $poder_vigente = false;
      if($contrato->is_contrato ) {
        if($user_contrato->poder_esta_vigente)
        $poder_vigente = true;
      } elseif(Auth::user()->user_publico->tieneAsociadoElContratoPadre($contrato)) {
        $poder_vigente = true;
      }

      if($this->id == $user_contrato->user_publico->user_id) {
        if($adenda != null)
          return $poder_vigente && $adenda->tiene_saltos_redeterminables;
        else
          return $poder_vigente && $user_contrato->contrato->tiene_saltos_redeterminables;
      } else {
        return false;
      }
    }

    /**
     * @param  SolicitudContrato\UserContrato $user_contrato
     * @param  int $estado
     */
    public function puedeEditarAnalisisPrecios($user_contrato) {
      if($this->id == $user_contrato->user_publico->user_id) {
        $contrato = $user_contrato->contrato;
        // $estados_editables = ['sin_analisis',
        //                       'borrador',
        //                       'a_corregir'];
        //
        // $estado = $contrato->estado_actual_analisis->nombre;
        // $puede_por_estado = in_array($estado, $estados_editables);

        return $user_contrato->poder_esta_vigente && // $puede_por_estado &&
               !$contrato->es_banco;
      } else {
        return false;
      }
    }

    /**
     * @param  string $modulo
     * @param  int $estado
     */
    public function puedeRealizar($modulo, $estado) {
      if($modulo == 'analisis_precios') {
        switch ($estado) {
          case 'borrador':
          case 'enviar_aprobar':
          case 'a_validar':
            return true;
            break;
          case 'aprobar':
          case 'rechazar':
          return false;
            break;
        }
        return false;
      }
    }

    /**
     * @param  int $causante_id
     */
    public function puedeVerCausante($causante_id) {
      return true;
    }

    /**
     * @param  Contrato/Contrato $contrato
     */
    public function puedeVerContrato($contrato) {
      return $this->user_publico->contratos()
                                ->whereContratoId($contrato->contrato_original_sin_adendas->id)
                                ->first() != null;
    }

    /**
     * @param string $modulo
     */
    public function puedeVerModulo($modulo) {
      foreach ($this->roles as $key => $rol)
        foreach ($rol->permissions as $key => $permission)
          if($permission->modulo == $modulo)
            return true;
      return false;
    }
}
