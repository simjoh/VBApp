<?php

namespace App\common\Rest;

use JsonSerializable;

class Link implements JsonSerializable
{
    private string $rel = "Htpddddddddddd:";
    private string $method = "sssss";
    private string $url;

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}