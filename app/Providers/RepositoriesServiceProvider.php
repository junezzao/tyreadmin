<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\Contracts\UserRepository',
            'App\Repositories\Eloquent\UserRepository'
        );
        
        $this->app->bind(
            'App\Repositories\Contracts\DataRepositoryContract',
            'App\Repositories\Eloquent\DataRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\DataSheetRepositoryContract',
            'App\Repositories\Eloquent\DataSheetRepository'
        );
    }
}
