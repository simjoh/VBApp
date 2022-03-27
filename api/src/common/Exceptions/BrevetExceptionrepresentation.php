<?php

namespace App\common\Exceptions;

use JsonSerializable;

class BrevetExceptionrepresentation implements JsonSerializable
{

    private string $code;
    private string $message;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }



    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }

}