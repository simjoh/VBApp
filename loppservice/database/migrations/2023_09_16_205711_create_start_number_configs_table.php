<?php

use App\Models\Event;
use App\Models\StartNumberConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('startnumberconfigs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('begins_at');
            $table->bigInteger('ends_at');
            $table->bigInteger('increments');
            $table->morphs('startnumberconfig', 's_num_conf');
        });


        $event = Event::find('d32650ff-15f8-4df1-9845-d3dc252a7a84')->get()->first();

        $eventconfig = $event->eventconfiguration;

        $startnumberconfig = new StartNumberConfig();
        $startnumberconfig->begins_at = 1000;
        $startnumberconfig->ends_at = 1200;
        $startnumberconfig->increments = 1;
        $eventconfig->startnumberconfig()->save($startnumberconfig);
        $event->save();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('start_number_configs');
    }
};
