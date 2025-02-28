<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;
use App\Models\Organizer;

return new class extends Migration
{
    public function up()
    {
        // Get the Cykelintresset organizer
        $cykelintresset = Organizer::where('organization_name', 'Cykelintresset')->first();

        // Update all BRM events to be associated with Cykelintresset
        if ($cykelintresset) {
            Event::where('event_type', 'BRM')->update([
                'organizer_id' => $cykelintresset->id
            ]);
        }
    }

    public function down()
    {
        Event::where('event_type', 'BRM')->update([
            'organizer_id' => null
        ]);
    }
};
