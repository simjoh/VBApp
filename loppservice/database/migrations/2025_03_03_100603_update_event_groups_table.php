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
        // First, get the list of foreign keys on the events table
        $foreignKeys = $this->getForeignKeys('events');
        
        // Drop the foreign key if it exists
        if (in_array('events_event_group_uid_foreign', $foreignKeys)) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign('events_event_group_uid_foreign');
            });
        }

        Schema::table('event_groups', function (Blueprint $table) {
            // Rename title to name
            $table->renameColumn('title', 'name');
            
            // Drop only unused columns
            $table->dropColumn([
                'active',
                'canceled',
                'completed'
            ]);

            // Rename primary key column
            $table->renameColumn('eventgroup_uid', 'uid');
        });

        // Add event_group_uid column if it doesn't exist
        if (!Schema::hasColumn('events', 'event_group_uid')) {
            Schema::table('events', function (Blueprint $table) {
                $table->uuid('event_group_uid')->nullable();
            });
        }

        // Add the new foreign key
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('event_group_uid')
                  ->references('uid')
                  ->on('event_groups')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, get the list of foreign keys on the events table
        $foreignKeys = $this->getForeignKeys('events');
        
        // Drop the foreign key if it exists
        if (in_array('events_event_group_uid_foreign', $foreignKeys)) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropForeign('events_event_group_uid_foreign');
            });
        }

        Schema::table('event_groups', function (Blueprint $table) {
            // Restore original column names
            $table->renameColumn('name', 'title');
            $table->renameColumn('uid', 'eventgroup_uid');
            
            // Restore dropped columns
            $table->boolean('active');
            $table->boolean('canceled');
            $table->boolean('completed');
        });

        // Restore the original foreign key
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('event_group_uid')
                  ->references('eventgroup_uid')
                  ->on('event_groups')
                  ->onDelete('set null');
        });
    }

    /**
     * Get all foreign key constraint names for a table
     */
    private function getForeignKeys(string $tableName): array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = array_map(
            function($key) {
                return $key->getName();
            },
            $conn->listTableForeignKeys($tableName)
        );
        
        return $foreignKeys;
    }
}; 