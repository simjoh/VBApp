<?php

namespace App\Domain\Model\Track\Rest;

use JsonSerializable;

class RusaControlRepresentation implements JsonSerializable
{
    private  $CONTROL_NUMBER;
    private string $CONTROL_NAME;
    private  $CONTROL_META_NAME;
    private  $CONTROL_DISTANCE_KM;
    private  $CONTROL_DISTANCE_MILE;
    private  string $OPEN;
    private  string $CLOSE;
    private  string $RELATIVE_OPEN;
    private  string $RELATIVE_CLOSE;
    private  string $GRAVEL_CLOSE;
    private  string $GRAVEL_CLOSE_RELATIVE;

    /**
     * @return mixed
     */
    public function getCONTROLNUMBER()
    {
        return $this->CONTROL_NUMBER;
    }

    /**
     * @param mixed $CONTROL_NUMBER
     */
    public function setCONTROLNUMBER($CONTROL_NUMBER): void
    {
        $this->CONTROL_NUMBER = $CONTROL_NUMBER;
    }

    /**
     * @return string
     */
    public function getCONTROLNAME(): string
    {
        return $this->CONTROL_NAME;
    }

    /**
     * @param string $CONTROL_NAME
     */
    public function setCONTROLNAME(string $CONTROL_NAME): void
    {
        $this->CONTROL_NAME = $CONTROL_NAME;
    }

    /**
     * @return mixed
     */
    public function getCONTROLMETANAME()
    {
        return $this->CONTROL_META_NAME;
    }

    /**
     * @param mixed $CONTROL_META_NAME
     */
    public function setCONTROLMETANAME($CONTROL_META_NAME): void
    {
        $this->CONTROL_META_NAME = $CONTROL_META_NAME;
    }

    /**
     * @return mixed
     */
    public function getCONTROLDISTANCEKM()
    {
        return $this->CONTROL_DISTANCE_KM;
    }

    /**
     * @param mixed $CONTROL_DISTANCE_KM
     */
    public function setCONTROLDISTANCEKM($CONTROL_DISTANCE_KM): void
    {
        $this->CONTROL_DISTANCE_KM = $CONTROL_DISTANCE_KM;
    }

    /**
     * @return mixed
     */
    public function getCONTROLDISTANCEMILE()
    {
        return $this->CONTROL_DISTANCE_MILE;
    }

    /**
     * @param mixed $CONTROL_DISTANCE_MILE
     */
    public function setCONTROLDISTANCEMILE($CONTROL_DISTANCE_MILE): void
    {
        $this->CONTROL_DISTANCE_MILE = $CONTROL_DISTANCE_MILE;
    }

    /**
     * @return string
     */
    public function getOPEN(): string
    {
        return $this->OPEN;
    }

    /**
     * @param string $OPEN
     */
    public function setOPEN(string $OPEN): void
    {
        $this->OPEN = $OPEN;
    }

    /**
     * @return string
     */
    public function getCLOSE(): string
    {
        return $this->CLOSE;
    }

    /**
     * @param string $CLOSE
     */
    public function setCLOSE(string $CLOSE): void
    {
        $this->CLOSE = $CLOSE;
    }

    /**
     * @return string
     */
    public function getRELATIVEOPEN(): string
    {
        return $this->RELATIVE_OPEN;
    }

    /**
     * @param string $RELATIVE_OPEN
     */
    public function setRELATIVEOPEN(string $RELATIVE_OPEN): void
    {
        $this->RELATIVE_OPEN = $RELATIVE_OPEN;
    }

    /**
     * @return string
     */
    public function getRELATIVECLOSE(): string
    {
        return $this->RELATIVE_CLOSE;
    }

    /**
     * @param string $RELATIVE_CLOSE
     */
    public function setRELATIVECLOSE(string $RELATIVE_CLOSE): void
    {
        $this->RELATIVE_CLOSE = $RELATIVE_CLOSE;
    }

    /**
     * @return string
     */
    public function getGRAVELCLOSE(): string
    {
        return $this->GRAVEL_CLOSE;
    }

    /**
     * @param string $GRAVEL_CLOSE
     */
    public function setGRAVELCLOSE(string $GRAVEL_CLOSE): void
    {
        $this->GRAVEL_CLOSE = $GRAVEL_CLOSE;
    }

    /**
     * @return string
     */
    public function getGRAVELCLOSERELATIVE(): string
    {
        return $this->GRAVEL_CLOSE_RELATIVE;
    }

    /**
     * @param string $GRAVEL_CLOSE_RELATIVE
     */
    public function setGRAVELCLOSERELATIVE(string $GRAVEL_CLOSE_RELATIVE): void
    {
        $this->GRAVEL_CLOSE_RELATIVE = $GRAVEL_CLOSE_RELATIVE;
    }





    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}