<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\FaceDetectionDidComplete;
use App\Events\FaceDetectionDidCompleteHandler;
use App\Events\FacialRecognitionGeometryCreated;
use App\Listeners\GenerateCroppedFacialImages;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */

    protected $listen = [
        FaceDetectionDidComplete::class => [
          FaceDetectionDidCompleteHandler::class,
        ],
        FaceDetectionDidFail::class => [
          FaceDetectionDidFailHandler::class,
        ],
        FacialRecognitionGeometryCreated::class => [
          GenerateCroppedFacialImages::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
