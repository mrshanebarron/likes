<?php

namespace MrShaneBarron\Likes;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use MrShaneBarron\Likes\Livewire\Reactions;
use MrShaneBarron\Likes\View\Components\Reactions as BladeReactions;

class LikesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sb-likes.php', 'sb-likes');
    }

    public function boot(): void
    {
        // Register Livewire component
        Livewire::component('sb-reactions', Reactions::class);

        // Register Blade component
        $this->loadViewComponentsAs('ld', [
            BladeReactions::class,
        ]);

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sb-likes');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Publishable resources
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/sb-likes.php' => config_path('sb-likes.php'),
            ], 'sb-likes-config');

            // Views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/sb-likes'),
            ], 'sb-likes-views');

            // Migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'sb-likes-migrations');

            // Lottie assets
            $this->publishes([
                __DIR__ . '/../resources/lottie' => public_path('vendor/sb-likes/lottie'),
            ], 'sb-likes-assets');
        }
    }
}
