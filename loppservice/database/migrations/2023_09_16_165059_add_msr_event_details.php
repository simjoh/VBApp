<?php

use App\Models\Event;
use App\Models\EventConfiguration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $event = new Event();
        $event->event_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $event->title = "Midnight sun randonee 1200";
        $event->description = 'Epic ride in midnigtht sun';
        $event->startdate = '2024-06-21';
        $event->enddate = '2024-06-24';
        $event->completed = false;
        $event->save();

        $eventconfiguration = new EventConfiguration();
        $eventconfiguration->max_registrations = 200;
        $eventconfiguration->registration_opens = '2023-09-01 00:00:00';
        $eventconfiguration->registration_closes = '2024-06-01 00:00:00';
        $event->eventconfiguration()->save($eventconfiguration);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
