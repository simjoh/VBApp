<?php


namespace App\Listeners;

use App\Events\NonParticipantOrderSuccesEvent;
use App\Mail\OrderEmail;
use App\Models\Event;
use App\Models\NonParticipantOptionals;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NonParticipantOrderSuccesListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NonParticipantOrderSuccesEvent $event): void
    {
        Log::debug('Handling NonParticipantOrderSuccesEvent ' . $event->optionals->optional_uid);
        $optional = NonParticipantOptionals::find($event->optionals->optional_uid);
        $event_course = Event::find($optional->course_uid)->get()->first();

        $product = Product::find($optional->productID);
        //
        if (App::isProduction()) {
            Mail::to($optional->email)
                ->send(new OrderEmail($optional, $event_course, $product))->subject("Dinner order confirmation");
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new OrderEmail($optional, $event_course, $product))->subject("Dinner order confirmation");
        }
    }
}
