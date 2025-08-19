<?php

namespace App\common\Service;

class CliLoggerService
{
    public function info(string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        echo "[INFO] " . $message . $contextStr . "\n";
    }

    public function error(string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        echo "[ERROR] " . $message . $contextStr . "\n";
    }

    public function warning(string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        echo "[WARNING] " . $message . $contextStr . "\n";
    }

    public function debug(string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        echo "[DEBUG] " . $message . $contextStr . "\n";
    }

    public function critical(string $message, array $context = []): void
    {
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        echo "[CRITICAL] " . $message . $contextStr . "\n";
    }
} 