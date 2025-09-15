<?php

use App\Services\VoucherService;
use Illuminate\Database\Migrations\Migration;
use App\Models\Voucher;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $voucherService = new VoucherService();

        // Simple array of codes with their types
        $jerseyTor = [
            'MSR-N8UHC7',
            'MSR-9HGDUJ',
            'MSR-REUYJ3',
            'MSR-Z45KES',
            'MSR-6WR2TC',
            'MSR-M7ZAUK',
            'MSR-NEG77X',
            'MSR-AY5NBC',
            'MSR-59KE9M',
            'MSR-TMPTY4',
            'MSR-NMRX5J',
            'MSR-7MNPEA',
            'MSR-6T8YFB',
            'MSR-8ASPNR',
            'MSR-S5AP59',
            'MSR-DZMPN6',
            'MSR-GRA7JD',
            'MSR-QEA62R',
            'MSR-T3TQES',
            'MSR-DDDKCD',
            'MSR-GMM4X3',
            'MSR-E5SNKH',
            'MSR-8UAS95',
            'MSR-9EU9YV',
            'MSR-6DFQZW',
            'MSR-7K5BX3',
            'MSR-C6UHJA',
            'MSR-T8W58H',
            'MSR-WUNQS5',
            'MSR-CW59YB',
            'MSR-6PC4CV',
            'MSR-P4R3K6',
            'MSR-B76QW9',
            'MSR-49P4KQ',
            'MSR-PXRCGD',
            'MSR-PYG5C3',
            'MSR-U2JDN6',
            'MSR-KVEHNK',
            'MSR-VX35P4',
            'MSR-KJSM94',
            'MSR-YTXBNE',
            'MSR-8MHQ7P',
            'MSR-BVCEQF',
            'MSR-5KT8RU',
            'MSR-W9PBHJ',
            'MSR-UP85WJ',
            'MSR-U53PWU',
            'MSR-TPMXR5',
            'MSR-APBCUG',
            'MSR-SQBJEH',
            'MSR-JSK8M7',
            'MSR-RUHZDY',
            'MSR-5RR57Z',
            'MSR-QV4ESK',
            'MSR-RZ8CF4',
            'MSR-YYGEPX',
            'MSR-34QFNN',
            'MSR-AC2VHW',
            'MSR-A3JDJ9',
            'MSR-46V5F5',
            'MSR-K24AR8',
            'MSR-CM6D9J',
            'MSR-W9RD6R',
            'MSR-5GD8SG',
            'MSR-REWF4J',
            'MSR-P4GZJK',
            'MSR-YNSR5U',
            'MSR-ESKK3H',
            'MSR-279AQE',
            'MSR-TZK8A5',
            'MSR-9BEQJ8',
            // Add more TOR jersey codes here
        ];

        $jerseyGrand = [
            'MSR GRAND-Z2UWWM',
            'MSR GRAND-3TJFP9',
            'MSR GRAND-YRA4QP',
            'MSR GRAND-6KZW3C',
            'MSR GRAND-V6BYBC',
            'MSR GRAND-6HTKEC',
            'MSR GRAND-T4K3X8',
            'MSR GRAND-2QCFMF',
            'MSR GRAND-RRZJPN',
            'MSR GRAND-QTY49Z',
            'MSR GRAND-PYHMVG',
            'MSR GRAND-QREAJQ',
            'MSR GRAND-5V8V2W',
            'MSR GRAND-6HQDGK',
            'MSR GRAND-2NGEBQ',
            'MSR GRAND-7G9PE2',
            'MSR GRAND-AVACYG',
            'MSR GRAND-S5RSZ2',
            'MSR GRAND-D7N8PU',
            'MSR GRAND-UKJKV8',
            'MSR GRAND-EPN75F',
            'MSR GRAND-HXQ4SV',
            'MSR GRAND-3Y85GM',
            'MSR GRAND-BDFZ4J',
            'MSR GRAND-56H9JJ',
            'MSR GRAND-J75EV3',
            'MSR GRAND-3J82PU',
            'MSR GRAND-FV6XQD',
            'MSR GRAND-GBHEWD',
            'MSR GRAND-C8RJXZ',
            'MSR GRAND-MZ5KQT',
            'MSR GRAND-K6ACAG',
            'MSR GRAND-BPZA8Z',
            'MSR GRAND-GGQG4G',
            'MSR GRAND-9M3DYA',
            'MSR GRAND-URHJYM',
            'MSR GRAND-8RJNDN',
            'MSR GRAND-UGKMAW',
            'MSR GRAND-65HCJS',
            'MSR GRAND-XZFC45',
            'MSR GRAND-E83ZVY',
            'MSR GRAND-HWT464',
            'MSR GRAND-3HJPD2',
            'MSR GRAND-XKD59D',
            'MSR GRAND-AC2ZQ6',
            'MSR GRAND-QAE4SK',
            'MSR GRAND-U7AMQV',
            'MSR GRAND-82K39W',
            'MSR GRAND-4UQ9HY',
            'MSR GRAND-UMD4EC',
            'MSR GRAND-353HY6',
            'MSR GRAND-U976ZY',
            'MSR GRAND-BR6R7V',
            'MSR GRAND-6BBUTP',
            'MSR GRAND-JR9VKR',
            'MSR GRAND-G9ZBVU',
            'MSR GRAND-J9ZYYK',
            'MSR GRAND-CSC333',
            'MSR GRAND-2WM696',
            'MSR GRAND-7F36D8',
            'MSR GRAND-J8E2E5',
            'MSR GRAND-QGRQ49',
            'MSR GRAND-B4WKZF',
            'MSR GRAND-3VH8YG',
            'MSR GRAND-A3NSCY',
            'MSR GRAND-MK2CU4',
            'MSR GRAND-TZXG4E',
            'MSR GRAND-Q52ZVR',
            'MSR GRAND-MKPUA2',
            'MSR GRAND-T64ANP',
            'MSR GRAND-8VZR4G',
            // Add more GRAND jersey codes here
        ];

     /*    $jerseyUnisexCodes = [
            'UNI-001-2025-ABC',
            'UNI-002-2025-DEF',
            // Add more unisex jersey codes here
        ]; */

        // Convert to the format expected by the service
        $allVouchers = [];

        foreach ($jerseyTor as $code) {
            $allVouchers[] = [
                'code' => $code,
                'type' => 'jersey_tor',
                'product_id' => 1008,
                'notes' => 'Migration import - Tor jerseys'
            ];
        }

        foreach ($jerseyGrand as $code) {
            $allVouchers[] = [
                'code' => $code,
                'type' => 'jersey_grand',
                'product_id' => 1007,
                'notes' => 'Migration import - Grand jerseys'
            ];
        }
/*
        foreach ($jerseyUnisexCodes as $code) {
            $allVouchers[] = [
                'code' => $code,
                'type' => 'jersey_unisex',
                'notes' => 'Migration import - Unisex jerseys'
            ];
        } */

        // Import using the service
        $importedCount = $voucherService->importVoucherCodes($allVouchers);

        echo "Simple voucher import completed: {$importedCount} imported\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Voucher::where('notes', 'like', 'Migration import%')->delete();
        echo "Migration imported vouchers have been removed\n";
    }
};


