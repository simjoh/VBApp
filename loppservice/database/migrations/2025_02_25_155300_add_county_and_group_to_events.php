<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('county_id')->nullable()->constrained('countys');
            $table->foreignUuid('event_group_uid')->nullable()->constrained('event_groups', 'eventgroup_uid');
        });

        // Get county IDs
        $vasterbottenId = DB::table('countys')
            ->where('county_code', '24')
            ->value('id');

        $vasternorrlandId = DB::table('countys')
            ->where('county_code', '22')
            ->value('id');

        // Set Västernorrland for Bönhamn events
        DB::table('events')
            ->where('title', 'like', '%bonhamn%')
            ->orWhere('title', 'like', '%bönhamn%')
            ->update(['county_id' => $vasternorrlandId]);

        // Set Västerbotten for all other events
        DB::table('events')
            ->whereNull('county_id')
            ->update(['county_id' => $vasterbottenId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['county_id']);
            $table->dropForeign(['event_group_uid']);
            $table->dropColumn(['county_id', 'event_group_uid']);
        });
    }
};
