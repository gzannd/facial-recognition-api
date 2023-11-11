<?php

namespace App\Providers;
use App\Interfaces;
use App\Http\Services\PasswordService;
use Illuminate\Support\ServiceProvider;
use App\Http\Services\EventLogService;

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
        $this->app->bind('App\Interfaces\IPasswordService', function() {
            return new PasswordService($this->app->makeWith(EventLogService::class), $_ENV['MIN_PASSWORD_LENGTH'], $_ENV['MAX_PASSWORD_LENGTH']);
        });
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
