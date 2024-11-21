<?php

namespace App\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('elasticsearch', function ($app) {
            return $client = ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
                ->setRetries(2)  // Retry on failure
                ->build();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
