<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StripeSyncService;
use Illuminate\Support\Facades\App;


// usage php artisan stripe:sync-products

class SyncStripeProducts extends Command
{
    protected $signature = 'stripe:sync-products {--report : Show detailed report}';
    protected $description = 'Sync mapped products with Stripe (fetches default_price from product)';

    public function handle(StripeSyncService $stripeSyncService)
    {
        // Prevent running in production environment
        if (App::isProduction()) {
            $this->error('This command is not allowed in production environment!');
            $this->error('This command is only available for testing and development.');
            return 1;
        }

        $environment = App::isProduction() ? 'production' : 'dev';
        $this->info("Syncing products for {$environment} environment...");

        $results = $stripeSyncService->syncMappedProductsWithStripe();

        $this->info("✓ Successfully synced: {$results['success']} products");

        if ($results['failed'] > 0) {
            $this->error("✗ Failed: {$results['failed']} products");
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }

        if ($this->option('report') && !empty($results['updated'])) {
            $this->info("\n=== SYNCED PRODUCTS ===");
            foreach ($results['updated'] as $update) {
                $this->line("✓ {$update['product_name']} (Local ID: {$update['local_id']})");
                $this->line("  -> Stripe Product: {$update['stripe_product_id']}");
                $this->line("  -> Price ID: {$update['price_id']}");
                $this->line("  -> Price: {$update['price']} {$update['currency']}");
                $this->line("");
            }
        }

        $this->info("Sync completed!");
    }
}
