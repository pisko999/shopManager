<?php

namespace App\Providers;

use App\Repositories\GiftRepositoryInterface;
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
            'App\Repositories\GiftListRepositoryInterface',
            'App\Repositories\GiftListRepository'
        );

        $this->app->bind(
            'App\Repositories\GiftItemRepositoryInterface',
            'App\Repositories\GiftItemRepository'
        );

        $this->app->bind(
            'App\Repositories\DeckRepositoryInterface',
            'App\Repositories\DeckRepository'
        );
        $this->app->bind(
            'App\Repositories\CardDeckRepositoryInterface',
            'App\Repositories\CardDeckRepository'
        );
        $this->app->bind(
            'App\Repositories\AddressRepositoryInterface',
            'App\Repositories\AddressRepository'
        );
        $this->app->bind(
            'App\Repositories\StockChangesRepositoryInterface',
            'App\Repositories\StockChangesRepository'
        );
        $this->app->bind(
            'App\Repositories\ComplaintRepositoryInterface',
            'App\Repositories\ComplaintRepository'
        );
        $this->app->bind(
            'App\Repositories\EvaluationRepositoryInterface',
            'App\Repositories\EvaluationRepository'
        );
        $this->app->bind(
            'App\Repositories\MethodRepositoryInterface',
            'App\Repositories\MethodRepository'
        );
        $this->app->bind(
            'App\Repositories\ShippingMethodRepositoryInterface',
            'App\Repositories\ShippingMethodRepository'
        );
        $this->app->bind(
            'App\Repositories\UserRepositoryInterface',
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Repositories\BuyItemRepositoryInterface',
            'App\Repositories\BuyItemRepository'
        );
        $this->app->bind(
            'App\Repositories\BuyCommandRepositoryInterface',
            'App\Repositories\BuyCommandRepository'
        );
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
            'App\Repositories\StatusRepositoryInterface',
            'App\Repositories\StatusRepository'
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

        $this->app->bind(
            'App\Repositories\AllProductsRepositoryInterface',
            'App\Repositories\AllProductsRepository'
        );

        $this->app->bind(
            'App\Repositories\GiftRepositoryInterface',
            'App\Repositories\GiftRepository'
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
