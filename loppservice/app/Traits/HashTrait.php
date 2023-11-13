<?php

namespace App\Traits;

trait HashTrait
{

    public function hashsumfor(string $stringtohash): string
    {
        return hash('sha256', $stringtohash);
    }

    public function hashwith(string $shalevel, string $stringtohash): string
    {
        return hash($shalevel, $stringtohash);
    }

}
