<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\RunningText;

class RunningTextServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\Contracts\RunningTextServiceInterface', function ($app) {
            return new RunningText();
            // return new RunningText2(); //replace jika ada tambahan
        });
    }
}
