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
            'App\Repositories\Contracts\MerchantRepository',
            'App\Repositories\Eloquent\MerchantRepository'
        );
        $this->app->bind(
            'App\Repositories\Contracts\SupplierRepository',
            'App\Repositories\Eloquent\SupplierRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\OrderRepository',
            'App\Repositories\Eloquent\OrderRepository'
        );

        $this->app->bind(
            'App\Repositories\Contracts\ChannelRepository',
            'App\Repositories\Eloquent\ChannelRepository'
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
