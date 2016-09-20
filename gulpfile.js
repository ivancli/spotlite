var del = require('del');
var elixir = require('laravel-elixir');
var gulp = require('gulp');
var task = elixir.Task;

elixir.extend('remove', function(path) {
    new task('remove', function() {
        return del(path);
    });
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.remove([
        'public/css',
        'public/js',
        'public/fonts',
        'public/images'
    ]);
    mix.styles([
        "node_modules/bootstrap/dist/css/bootstrap.css",
        "node_modules/datatables.net-bs/css/dataTables.bootstrap.css",
        "node_modules/select2/dist/css/select2.css",
        'vendor/driftyco/ionicons/css/ionicons.css',
        'node_modules/font-awesome/css/font-awesome.css',
        'vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css',
        'vendor/almasaeed2010/adminlte/dist/css/skins/_all-skins.css',
        'node_modules/dragula/dist/dragula.css',
        'resources/assets/css/spotlite.css'
    ], "public/css/main.css", "./");
    mix.scripts([
        // "node_modules/jquery/dist/jquery.js",
        'vendor/almasaeed2010/adminlte/plugins/jQuery/jquery-2.2.3.min.js',
        "node_modules/bootstrap/dist/js/bootstrap.js",
        "node_modules/datatables.net/js/jquery.dataTables.js",
        "node_modules/datatables.net-bs/js/dataTables.bootstrap.js",
        "node_modules/select2/dist/js/select2.js",
        'vendor/almasaeed2010/adminlte/plugins/slimScroll/jquery.slimscroll.min.js',
        'vendor/almasaeed2010/adminlte/plugins/fastclick/fastclick.js',
        'vendor/almasaeed2010/adminlte/dist/js/app.js',
        'node_modules/dragula/dist/dragula.js',
        'node_modules/dom-autoscroller/dist/dom-autoscroller.js',
        'resources/assets/js/commonFunctions.js',
        'resources/assets/js/spotlite.js'
    ], "public/js/main.js", "./");
    mix.scripts([
        "node_modules/highcharts/highcharts.js",
        "node_modules/highcharts/highcharts-more.js",
        "node_modules/highcharts/modules/exporting.js"
    ], "public/js/highcharts.js", "./");

    /* copy images */
    mix.copy('resources/assets/images', 'public/images');

    mix.copy("node_modules/bootstrap/dist/fonts", "public/fonts/");
    mix.copy("node_modules/font-awesome/fonts", "public/fonts/");
    mix.copy('vendor/driftyco/ionicons/fonts', 'public/fonts/');
});
