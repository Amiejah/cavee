<?php

namespace App\Providers;

use App\Implementations\EspressoMachineImplementation;
use App\Interfaces\BeansContainer;
use App\Interfaces\EspressoMachineInterface;
use App\Interfaces\WaterContainer;
use BeansContainerImplementation;
use Illuminate\Support\ServiceProvider;
use WaterContainerImplementation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BeansContainer::class, BeansContainerImplementation::class);
        $this->app->bind(WaterContainer::class, WaterContainerImplementation::class);
        $this->app->bind(EspressoMachineInterface::class, EspressoMachineImplementation::class);
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
