<?php

namespace App\Domain\Model\Loppservice\Rest\LoppserviceRepresentations;

class EventConfiguration {
    public int $id;
    public int $max_registrations;
    public string $registration_opens;
    public string $registration_closes;
    public int $resarvation_on_event;
    public string $eventconfiguration_type;
    public string $eventconfiguration_id;
    public string $created_at;
    public string $updated_at;
    public StartNumberConfig $startnumberconfig;
    public ReservationConfig $reservationconfig;
    public array $products;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->max_registrations = $data['max_registrations'];
        $this->registration_opens = $data['registration_opens'];
        $this->registration_closes = $data['registration_closes'];
        $this->resarvation_on_event = $data['resarvation_on_event'];
        $this->eventconfiguration_type = $data['eventconfiguration_type'];
        $this->eventconfiguration_id = $data['eventconfiguration_id'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
        $this->startnumberconfig = new StartNumberConfig($data['startnumberconfig']);
        $this->reservationconfig = new ReservationConfig($data['reservationconfig']);
        $this->products = array_map(fn($product) => new Product($product), $data['products']);
    }
}