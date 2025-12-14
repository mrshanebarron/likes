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
        $this->mergeConfigFrom(__DIR__ . '/../config/ld-likes.php', 'ld-likes');
    }

    public function boot(): void
    {
        // Register Livewire component
        Livewire::component('ld-reactions', Reactions::class);

        // Register Blade component
        $this->loadViewComponentsAs('ld', [
            BladeReactions::class,
        ]);

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ld-likes');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Publishable resources
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/ld-likes.php' => config_path('ld-likes.php'),
            ], 'ld-likes-config');

            // Views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/ld-likes'),
            ], 'ld-likes-views');

            // Migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'ld-likes-migrations');

            // Lottie assets
            $this->publishes([
                __DIR__ . '/../resources/lottie' => public_path('vendor/ld-likes/lottie'),
            ], 'ld-likes-assets');
        }
    }
}
