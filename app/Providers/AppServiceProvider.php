<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(
            'App\Repositories\StockRepositoryInterface',
            'App\Repositories\StockRepository'
        );
        $this->app->bind(
            'App\Repositories\PaymentRepositoryInterface',
            'App\Repositories\PaymentRepository'
        );
        $this->app->bind(
            'App\Repositories\CommandRepositoryInterface',
            'App\Repositories\CommandRepository'
        );
        $this->app->bind(
            'App\Repositories\ItemRepositoryInterface',
            'App\Repositories\ItemRepository'
        );
        $this->app->bind(
            'App\Repositories\StatusNamesRepositoryInterface',
            'App\Repositories\StatusNamesRepository'
        );
        $this->app->bind(
            'App\Repositories\ExpansionRepositoryInterface',
            'App\Repositories\ExpansionRepository'
        );

        $this->app->bind(
            'App\Repositories\CardRepositoryInterface',
            'App\Repositories\CardRepository'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
