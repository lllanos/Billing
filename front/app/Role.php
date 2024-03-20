<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {
  public function permissions() {
      return $this->belongsToMany('App\Permission');
  }
  public function users() {
    return $this->belongsToMany('App\User');
  }
}
