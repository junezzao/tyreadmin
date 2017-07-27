<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /* @var($var++) */
        \Blade::extend(function($view)
        {
            return preg_replace('/\@var\((.+)\)/', '<?php ${1}; ?>', $view);
        });
    }

    public function register()
    {
        //
    }
}