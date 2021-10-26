<?php

namespace App\Domain\Model\User;

use JsonSerializable;

class Role implements JsonSerializable
{



    private int $id = 1;

    /**
     * @return string
     */
    public function getTest(): int
    {
        return $this->test;
    }

    /**
     * @param string $test
     */
    public function setTest(string $test): void
    {
        $this->test = $test;
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}