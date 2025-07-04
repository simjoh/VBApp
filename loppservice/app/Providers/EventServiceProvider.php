<?php

namespace App\Providers;

use App\Events\CanceledPaymentEvent;
use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\CreateParticipantInCyclingAppEvent;
use App\Events\FailedParticipantTransferEvent;
use App\Events\FailedPaymentEvent;
use App\Events\NonParticipantOrderSuccesEvent;
use App\Events\PreRegistrationSuccessEvent;
use App\Events\UpdateCompetitorInfoEvent;
use App\Listeners\CanceledPaymentEventListener;
use App\Listeners\CompletedRegistrationSuccessEventListener;
use App\Listeners\CreateParticipantInCyclingAppEventListener;
use App\Listeners\FailedparticipantTransferEventListener;
use App\Listeners\FailedPaymentEventListener;
use App\Listeners\NonParticipantOrderSuccesListener;
use App\Listeners\PreRegistrationSuccessEventListener;
use App\Listeners\UpdateCompetitorInfoEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        PreRegistrationSuccessEvent::class => [
            PreRegistrationSuccessEventListener::class,
        ],
        CompletedRegistrationSuccessEvent::class => [
            CompletedRegistrationSuccessEventListener::class
        ],
        CanceledPaymentEvent::class => [
            CanceledPaymentEventListener::class
        ],
        FailedPaymentEvent::class => [
            FailedPaymentEventListener::class
        ],
        CreateParticipantInCyclingAppEvent::class => [
            CreateParticipantInCyclingAppEventListener::class
        ],
        NonParticipantOrderSuccesEvent::class => [
            NonParticipantOrderSuccesListener::class
        ],
        FailedParticipantTransferEvent::class => [
            FailedparticipantTransferEventListener::class
        ],
        UpdateCompetitorInfoEvent::class => [
            UpdateCompetitorInfoEventListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
