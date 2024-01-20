<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('crm_user', function () {
            return Http::baseUrl('https://crmuser.queleadscrm.com/api');
        });

        Http::macro('crm_company',function(){
            return Http::baseUrl('https://crmcompany.queleadscrm.com/api');
        });
    }
}
