var elixir = require('laravel-elixir');

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
    mix.less([
        'app.less',
        'skins/skin-black.less',
        'font-awesome/font-awesome.less',
        'fileupload/jquery.fileupload.less'
	], 'public/css/app.min.css');

	mix.scripts([
        'app.js',
        'common.js',
        'loading-modal-prompt.js'
    ], 'public/js/app.min.js');
    
    // fileupload
    mix.scripts([
        'jquery_ui_widgets.js',
        'jquery.fileupload.js'
    ], 'public/js/fileupload.min.js');

    // bootstrap treeview
    mix.scripts([
        'bootstrap-treeview.js'
    ], 'public/js/bootstrap-treeview.min.js');

    mix.less([
        'bootstrap-treeview/bootstrap-treeview.less'
    ], 'public/css/bootstrap-treeview.min.css');
});
