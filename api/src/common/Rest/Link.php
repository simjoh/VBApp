<?php

namespace App\common\Rest;

use JsonSerializable;

class Link implements JsonSerializable
{
    private string $rel = ":";
    private string $method = "sssss";
    private string $url;

    /**
     * @param string $rel
     * @param string $method
     * @param string $url
     */
    public function __construct(string $rel, string $method, string $url)
    {
        $this->rel = $rel;
        $this->method = $method;
        $this->url = $url;
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function jsonSerialize(): mixed {
        return (object) get_object_vars($this);
    }
}