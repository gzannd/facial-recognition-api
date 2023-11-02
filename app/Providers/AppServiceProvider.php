<?php

namespace App\Providers;
use App\Interfaces;
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
        $this->app->bind('App\Interfaces\IPushNotificationService', 'App\Http\Services\LarafirebasePushNotificationService');
        $this->app->bind('App\Interfaces\IFacialRecognitionService', 'App\Http\Services\CompreFaceFacialRecognitionService');
        $this->app->bind('App\Interfaces\IDeviceService', 'App\Http\Services\DeviceService');
        $this->app->bind('App\Interfaces\IJwtService', 'App\Http\Services\JwtService');
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
