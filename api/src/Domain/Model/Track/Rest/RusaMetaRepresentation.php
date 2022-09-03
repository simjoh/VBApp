<?php

namespace App\Domain\Model\Track\Rest;

use JsonSerializable;

class RusaMetaRepresentation implements JsonSerializable
{

    private string $API_VERSION;
    private string $API_CONTACT;
    private string $ERROR;

    /**
     * @return string
     */
    public function getAPIVERSION(): string
    {
        return $this->API_VERSION;
    }

    /**
     * @param string $API_VERSION
     */
    public function setAPIVERSION(string $API_VERSION): void
    {
        $this->API_VERSION = $API_VERSION;
    }

    /**
     * @return string
     */
    public function getAPICONTACT(): string
    {
        return $this->API_CONTACT;
    }

    /**
     * @param string $API_CONTACT
     */
    public function setAPICONTACT(string $API_CONTACT): void
    {
        $this->API_CONTACT = $API_CONTACT;
    }

    /**
     * @return string
     */
    public function getERROR(): string
    {
        return $this->ERROR;
    }

    /**
     * @param string $ERROR
     */
    public function setERROR(string $ERROR): void
    {
        $this->ERROR = $ERROR;
    }



    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}