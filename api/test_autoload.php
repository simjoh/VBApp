<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    echo "Successfully created Spreadsheet object\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 