const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

 mix
 .js([ 'resources/js/app.js',
       'node_modules/yacyreta-npm/src/js/selectize.js',
       'node_modules/yacyreta-npm/src/js/chosen.jquery.mobile.js',
       'node_modules/inputmask/dist/min/jquery.inputmask.bundle.min.js',
       'node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js',
       'node_modules/yacyreta-npm/src/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
       'node_modules/yacyreta-npm/src/js/fileinput_locale_es.js',
       'node_modules/yacyreta-npm/src/js/common.js',
     ], 'public/js/app.js')

 .js([	'resources/js/login/app.js',
 			  'resources/js/login/common.js'
 		], 'public/js/app_login.js')
 .sass('resources/sass/app.scss', 'public/css')
 .styles([
          'node_modules/argob-poncho/dist/css/poncho.min.css',
          'node_modules/yacyreta-npm/src/css/font-awesome.min.css',
           // 'node_modules/font-awesome/css/font-awesome.min.css',
           'public/css/icono-arg.css',
           'node_modules/yacyreta-npm/src/css/fontonic.css',
           'node_modules/chosen-js/chosen.css',
           'node_modules/yacyreta-npm/src/css/selectize.default.css',
           'node_modules/bootstrap-fileinput-npm/css/fileinput.min.css',
           'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
           'node_modules/yacyreta-npm/src/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
 	         'node_modules/yacyreta-npm/src/css/bootstrap-dialog.css',
           'node_modules/yacyreta-npm/src/css/styles.css',
         ], 'public/css/all.css')

 // .styles([	'resources/assets/css/matrice_login.css'
 // 				], 'public/css/login.css')

 .copy(['node_modules/chosen-js/chosen-sprite.png', 'node_modules/chosen-js/chosen-sprite@2x.png'], 'public/css', false)
 .copyDirectory(['node_modules/yacyreta-npm/src/fonts'], 'public/fonts', false)
 .copyDirectory(['node_modules/font-awesome/fonts'], 'public/fonts', false)
 .version();
