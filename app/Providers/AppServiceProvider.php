<?php

namespace App\Providers;

use App\Models\ConfiguracionNegocio;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        // Comparte la configuración del negocio con TODAS las vistas
        if (Schema::hasTable('configuracion_negocio')) {
            $configNegocio = ConfiguracionNegocio::first();
            View::share('configNegocio', $configNegocio);
        }
    }
}
