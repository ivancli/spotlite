var del = require('del');
var elixir = require('laravel-elixir');
var gulp = require('gulp');
var task = elixir.Task;

elixir.extend('remove', function (path) {
    new task('remove', function () {
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

elixir(function (mix) {
    mix.remove([
        'public/css',
        'public/js',
        'public/fonts',
        'public/images',
        'public/videos'
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
        'resources/assets/css/file-tree.css',
        'vendor/almasaeed2010/adminlte/plugins/datepicker/datepicker3.css',
        'vendor/almasaeed2010/adminlte/plugins/daterangepicker/daterangepicker.css',
        'vendor/almasaeed2010/adminlte/plugins/icheck/square/blue.css',
        'node_modules/jquery-contextmenu/dist/jquery.contextMenu.css',
        'resources/assets/css/spotlite.css'
    ], "public/css/main.css", "./");
    mix.scripts([
        'node_modules/bootstrap-tour/build/css/bootstrap-tour.css'
    ], "public/css/product-tour.css", './');
    mix.styles([
        'resources/assets/css/email.css'
    ], "public/css/email.css", "./");
    mix.styles([
        'resources/assets/css/email_import.css'
    ], "public/css/email_import.css", "./");
    mix.styles([
        'resources/assets/css/email_brand.css'
    ], "public/css/email_brand.css", "./");
    mix.scripts([
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
        "node_modules/highcharts/highcharts.js",
        "node_modules/highcharts/highcharts-more.js",
        "node_modules/highcharts/modules/exporting.js",
        "node_modules/highcharts/modules/no-data-to-display.js",
        'vendor/almasaeed2010/adminlte/plugins/datepicker/bootstrap-datepicker.js',
        'vendor/almasaeed2010/adminlte/plugins/daterangepicker/moment.js',
        'vendor/almasaeed2010/adminlte/plugins/daterangepicker/daterangepicker.js',
        'vendor/almasaeed2010/adminlte/plugins/iCheck/icheck.js',
        'node_modules/jquery-contextmenu/dist/jquery.contextMenu.js',
        'resources/assets/js/commonFunctions.js',
        'resources/assets/js/google_analytics.js',
        'resources/assets/js/sidebar.js',
        'resources/assets/js/spotlite.js'
    ], "public/js/main.js", "./");
    mix.scripts([
        'vendor/almasaeed2010/adminlte/plugins/jQuery/jquery-2.2.3.min.js',
        "node_modules/bootstrap/dist/js/bootstrap.js",
        "node_modules/select2/dist/js/select2.js",
        'vendor/almasaeed2010/adminlte/plugins/slimScroll/jquery.slimscroll.min.js',
        'vendor/almasaeed2010/adminlte/plugins/fastclick/fastclick.js',
        'vendor/almasaeed2010/adminlte/dist/js/app.js',
        'vendor/almasaeed2010/adminlte/plugins/iCheck/icheck.js',
        'resources/assets/js/commonFunctions.js',
        'resources/assets/js/google_analytics.js'
    ], "public/js/auth.js", "./");

    mix.scripts([
        'node_modules/bootstrap-tour/build/js/bootstrap-tour.js'
    ], "public/js/product-tour.js", './');

    /* copy images */
    mix.copy('resources/assets/images', 'public/images');
    mix.copy('vendor/almasaeed2010/adminlte/plugins/iCheck/square/blue.png', 'public/images//blue.png');
    mix.copy('vendor/almasaeed2010/adminlte/plugins/iCheck/square/blue@2x.png', 'public/images//blue@2x.png');
    mix.copy('resources/assets/plugins/jquery.fileTree-1.01/images', 'public/images');
    mix.copy('resources/assets/videos', 'public/videos');
    mix.copy('resources/assets/others', 'public/others');

    mix.copy("node_modules/bootstrap/dist/fonts", "public/fonts/");
    mix.copy("node_modules/font-awesome/fonts", "public/fonts/");
    mix.copy('vendor/driftyco/ionicons/fonts', 'public/fonts/');
});
