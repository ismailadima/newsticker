<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register Interface and Repository in here
        // You must place Interface in first place
        // If you dont, the Repository will not get readed.
        $this->app->bind(
            'App\Interfaces\RunningTextInterface',
            'App\Repositories\RunningTextRepository'
        );

        $this->app->bind(
            'App\Interfaces\RunningTextInewsInterface',
            'App\Repositories\RunningTextInewsRepository'
        );

        $this->app->bind(
            'App\Interfaces\RunningTextCommandInterface',
            'App\Repositories\RunningTextCommandRepository'
        );
    }
}