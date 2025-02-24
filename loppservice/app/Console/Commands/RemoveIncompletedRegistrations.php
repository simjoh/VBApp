<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveIncompletedRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-incompleted-registrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $records = Registration::whereNull('startnumber')
            ->whereNull('ref_nr')
            ->get();

        if (count($records) > 0) {
            Log::info(count($records) . ' to remove');
            // Proceed with deletion
            // Delete records where startnumber and ref_nr are null
            Registration::whereNull('startnumber')
                ->whereNull('ref_nr')
                ->delete();
        } else {
            Log::info('No records to remove');
        }

        Log::info(sizeof($records) . ' to remove');

    }
}
