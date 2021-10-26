<?php

namespace App\common\Rest;

class Link implements \JsonSerializable
{
    private string $rel = "Httpddddddddddd:";
    private string $method;
    private string $url;

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}