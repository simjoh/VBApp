<?php

namespace App\Domain\Model\User;

class Role
{

    public int $id = 1;

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
}