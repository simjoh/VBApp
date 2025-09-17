<?php

namespace App\Console\Commands;

use App\Services\VoucherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportVouchers extends Command
{
    protected $signature = 'vouchers:import {file} {--type=jersey_male}';
    protected $description = 'Import voucher codes from CSV file';

    public function handle(VoucherService $voucherService)
    {
        $filename = $this->argument('file');
        $type = $this->option('type');

        if (!Storage::exists($filename)) {
            $this->error("File {$filename} not found in storage.");
            return 1;
        }

        $csv = Storage::get($filename);
        $lines = explode("\n", trim($csv));
        $vouchers = [];

        foreach ($lines as $line) {
            $code = trim($line);
            if (!empty($code)) {
                $vouchers[] = [
                    'code' => $code,
                    'type' => $type
                ];
            }
        }

        $imported = $voucherService->importVoucherCodes($vouchers);

        $this->info("Successfully imported {$imported} voucher codes of type '{$type}'.");
        return 0;
    }
}
