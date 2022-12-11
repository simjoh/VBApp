<?php

namespace App\Domain\Model\Track\Rest;

use JsonSerializable;

class RusaTrackRepresentation implements JsonSerializable
{
    private $EVENT_DISTANCE_KM;
    private $EVENT_DISTANCE_MILE;
    private $ROUTE_DISTANCE_KM;
    private $ROUTE_DISTANCE_MILE;
    private string $MAX_TIME;
    private string $MIN_TIME;
    private string $START_DATE;
    private string $START_TIME;
    private string $START_DATE_PRINTABLE;
    private $GRAVEL_DISTANCE_KM;
    private $GRAVEL_DISTANCE_MILE;
    private string $GRAVEL_PERCENT;
    private string $GRAVEL_EXTRA_TIME;
    private string $GRAVEL_MAX_TIME;
    private string $CALC_METHOD;
    private ?string $TRACK_TITLE;
    private ?string $LINK_TO_TRACK;

    /**
     * @return string|null
     */
    public function getLINKTOTRACK(): ?string
    {
        return $this->LINK_TO_TRACK;
    }

    /**
     * @param string|null $LINK_TO_TRACK
     */
    public function setLINKTOTRACK(?string $LINK_TO_TRACK): void
    {
        $this->LINK_TO_TRACK = $LINK_TO_TRACK;
    }

    /**
     * @return string|null
     */
    public function getTRACKTITLE(): ?string
    {
        return $this->TRACK_TITLE;
    }

    /**
     * @param string|null $TRACK_TITLE
     */
    public function setTRACKTITLE(?string $TRACK_TITLE): void
    {
        $this->TRACK_TITLE = $TRACK_TITLE;
    }

    /**
     * @return mixed
     */
    public function getEVENTDISTANCEKM()
    {
        return $this->EVENT_DISTANCE_KM;
    }

    /**
     * @param mixed $EVENT_DISTANCE_KM
     */
    public function setEVENTDISTANCEKM($EVENT_DISTANCE_KM): void
    {
        $this->EVENT_DISTANCE_KM = $EVENT_DISTANCE_KM;
    }

    /**
     * @return mixed
     */
    public function getEVENTDISTANCEMILE()
    {
        return $this->EVENT_DISTANCE_MILE;
    }

    /**
     * @param mixed $EVENT_DISTANCE_MILE
     */
    public function setEVENTDISTANCEMILE($EVENT_DISTANCE_MILE): void
    {
        $this->EVENT_DISTANCE_MILE = $EVENT_DISTANCE_MILE;
    }

    /**
     * @return mixed
     */
    public function getROUTEDISTANCEKM()
    {
        return $this->ROUTE_DISTANCE_KM;
    }

    /**
     * @param mixed $ROUTE_DISTANCE_KM
     */
    public function setROUTEDISTANCEKM($ROUTE_DISTANCE_KM): void
    {
        $this->ROUTE_DISTANCE_KM = $ROUTE_DISTANCE_KM;
    }

    /**
     * @return mixed
     */
    public function getROUTEDISTANCEMILE()
    {
        return $this->ROUTE_DISTANCE_MILE;
    }

    /**
     * @param mixed $ROUTE_DISTANCE_MILE
     */
    public function setROUTEDISTANCEMILE($ROUTE_DISTANCE_MILE): void
    {
        $this->ROUTE_DISTANCE_MILE = $ROUTE_DISTANCE_MILE;
    }

    /**
     * @return string
     */
    public function getMAXTIME(): string
    {
        return $this->MAX_TIME;
    }

    /**
     * @param string $MAX_TIME
     */
    public function setMAXTIME(string $MAX_TIME): void
    {
        $this->MAX_TIME = $MAX_TIME;
    }

    /**
     * @return string
     */
    public function getMINTIME(): string
    {
        return $this->MIN_TIME;
    }

    /**
     * @param string $MIN_TIME
     */
    public function setMINTIME(string $MIN_TIME): void
    {
        $this->MIN_TIME = $MIN_TIME;
    }

    /**
     * @return string
     */
    public function getSTARTDATE(): string
    {
        return $this->START_DATE;
    }

    /**
     * @param string $START_DATE
     */
    public function setSTARTDATE(string $START_DATE): void
    {
        $this->START_DATE = $START_DATE;
    }

    /**
     * @return string
     */
    public function getSTARTTIME(): string
    {
        return $this->START_TIME;
    }

    /**
     * @param string $START_TIME
     */
    public function setSTARTTIME(string $START_TIME): void
    {
        $this->START_TIME = $START_TIME;
    }

    /**
     * @return string
     */
    public function getSTARTDATEPRINTABLE(): string
    {
        return $this->START_DATE_PRINTABLE;
    }

    /**
     * @param string $START_DATE_PRINTABLE
     */
    public function setSTARTDATEPRINTABLE(string $START_DATE_PRINTABLE): void
    {
        $this->START_DATE_PRINTABLE = $START_DATE_PRINTABLE;
    }

    /**
     * @return mixed
     */
    public function getGRAVELDISTANCEKM()
    {
        return $this->GRAVEL_DISTANCE_KM;
    }

    /**
     * @param mixed $GRAVEL_DISTANCE_KM
     */
    public function setGRAVELDISTANCEKM($GRAVEL_DISTANCE_KM): void
    {
        $this->GRAVEL_DISTANCE_KM = $GRAVEL_DISTANCE_KM;
    }

    /**
     * @return mixed
     */
    public function getGRAVELDISTANCEMILE()
    {
        return $this->GRAVEL_DISTANCE_MILE;
    }

    /**
     * @param mixed $GRAVEL_DISTANCE_MILE
     */
    public function setGRAVELDISTANCEMILE($GRAVEL_DISTANCE_MILE): void
    {
        $this->GRAVEL_DISTANCE_MILE = $GRAVEL_DISTANCE_MILE;
    }

    /**
     * @return string
     */
    public function getGRAVELPERCENT(): string
    {
        return $this->GRAVEL_PERCENT;
    }

    /**
     * @param string $GRAVEL_PERCENT
     */
    public function setGRAVELPERCENT(string $GRAVEL_PERCENT): void
    {
        $this->GRAVEL_PERCENT = $GRAVEL_PERCENT;
    }

    /**
     * @return string
     */
    public function getGRAVELEXTRATIME(): string
    {
        return $this->GRAVEL_EXTRA_TIME;
    }

    /**
     * @param string $GRAVEL_EXTRA_TIME
     */
    public function setGRAVELEXTRATIME(string $GRAVEL_EXTRA_TIME): void
    {
        $this->GRAVEL_EXTRA_TIME = $GRAVEL_EXTRA_TIME;
    }

    /**
     * @return string
     */
    public function getGRAVELMAXTIME(): string
    {
        return $this->GRAVEL_MAX_TIME;
    }

    /**
     * @param string $GRAVEL_MAX_TIME
     */
    public function setGRAVELMAXTIME(string $GRAVEL_MAX_TIME): void
    {
        $this->GRAVEL_MAX_TIME = $GRAVEL_MAX_TIME;
    }

    /**
     * @return string
     */
    public function getCALCMETHOD(): string
    {
        return $this->CALC_METHOD;
    }

    /**
     * @param string $CALC_METHOD
     */
    public function setCALCMETHOD(string $CALC_METHOD): void
    {
        $this->CALC_METHOD = $CALC_METHOD;
    }


    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}