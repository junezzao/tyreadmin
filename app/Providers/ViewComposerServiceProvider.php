<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use DB;
use Schema;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // load the user variable 
        view()->composer('includes.header', function ($view) {
            $view->with('user', \Auth::user());
        });

        view()->composer('partials.menus.nav-sidebar', function ($view) {
            $view->with('user', \Auth::user());
        });

        view()->composer('dashboard.index', function ($view) {
            $view->with('user', \Auth::user());
        });

        $this->getCurrentVersion();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function getCurrentVersion()
    {
        if(Schema::hasTable('changelogs')){
            $currentVersion = DB::table('changelogs')->select('title')->orderBy('id', 'desc')->first();
            view()->composer('includes.footer', function ($view) use($currentVersion) {
                $view->with('siteVersion', $currentVersion->title);
            });    
        }else{
            view()->composer('includes.footer', function ($view) {
                $view->with('siteVersion', '');
            });  
        }
        
    }
}
