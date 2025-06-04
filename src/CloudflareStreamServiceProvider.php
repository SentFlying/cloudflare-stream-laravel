<?php

namespace SentFlying\CloudflareStreamLaravel;

use Illuminate\Support\ServiceProvider;

class CloudflareStreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cloudflare-stream.php', 'cloudflare-stream'
        );
        
        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $app->make(\Illuminate\Http\Client\Factory::class),
                $app['config']['cloudflare-stream']
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cloudflare-stream.php' => config_path('cloudflare-stream.php'),
            ], 'cloudflare-stream-config');
        }
    }
}
