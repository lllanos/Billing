<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

use Auth;
use Closure;

class Authenticate extends Middleware
{

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string[]  ...$guards
   * @return mixed
   *
   * @throws \Illuminate\Auth\AuthenticationException
   */
   // Overwriten de vendor\laravel\framework\src\Illuminate\Auth\Middleware
   public function handle($request, Closure $next, ...$guards) {
      $this->authenticate($request, $guards);

      if(!Auth::user()->habilitado) {
        Auth::logout();
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
      }

      return $next($request);
    }
    
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
