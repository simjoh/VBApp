<?php

namespace Feature;

use App\Events\PreRegistrationSuccessEvent;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PreRegistrationSuccessEventTest extends TestCase
{



    public function test_orders_can_be_shipped(): void
    {

        Event::fake([
            PreRegistrationSuccessEvent::class
        ]);



    }

}
