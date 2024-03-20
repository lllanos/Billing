<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

use Auth;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
      if(config('custom.maria_db'))
        Schema::defaultStringLength(191);

      if(config('app.env') == 'DEBUG') {
        DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });
      }

      Blade::directive('trans', function ($key) {
        return "<?php echo trans(" . $key . "); ?>";
      });

      Blade::directive('toDosDec', function ($valor) {
        // return number_format((float)$this->attributes[$valor], 2, ',', '.');
        return "<?php echo number_format((float)" . $valor . ", 2, ',', '.'); ?>";
      });

      Blade::directive('toCuatroDec', function ($valor) {
        // return number_format((float)$this->attributes[$valor], 2, ',', '.');
        return "<?php echo number_format((float)" . $valor . ", 4, ',', '.'); ?>";
      });

      Blade::directive('count', function ($items) {
        return "<?php echo count(" . $items . "); ?>";
      });

      Blade::directive('cant', function($expression) {
        return "<?php if (Auth::user()->cant{$expression}) : ?>";
      });

      Blade::directive('endcant', function() {
        return "<?php endif; ?>";
      });

      Blade::directive('ifcount', function($expression) {
        return "<?php if (count({$expression})) : ?>";
      });

      Blade::directive('elseifcount', function() {
        return "<?php else: ?>";
      });

      Blade::directive('endifcount', function() {
        return "<?php endif; ?>";
      });

      Blade::directive('ifnotcount', function($expression) {
        return "<?php if (count({$expression}) == 0) : ?>";
      });

      Blade::directive('elseifnotcount', function() {
        return "<?php else: ?>";
      });

      Blade::directive('endifnotcount', function() {
        return "<?php endif; ?>";
      });

      // FIX de Zizaco Entrust, si el primer permiso no estaba devolvia false
      // Sirve como OR, @permission es AND
      Blade::directive('permissions', function($expression) {
        $permisos = explode(",", str_replace(['(', ')'], '', $expression));
        $toBlade = "";
        foreach ($permisos as $keyPermiso => $valuePermiso) {
          $valuePermiso = str_replace(' ', '',$valuePermiso);
          $toBlade .= "Auth::user()->can({$valuePermiso}) || ";
        }

        $toBlade = substr_replace($toBlade, '', strrpos($toBlade, '||'), 2);
        return "<?php if ({$toBlade}) : ?>";
      });

      Blade::directive('includerelative', function ($path_relative) {
        $view_file_root = ''; // you need to find this path with help of php functions, try some of them.
        $full_path = $view_file_root . path_relative;
        return view::make($full_path)->render();
      });
    }
}
