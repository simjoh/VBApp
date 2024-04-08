<?php

namespace App\Domain\Model\Country;

class Country
{
    public int $country_id;
    public string $country_name_en;
    public string $country_name_sv;
    public string $country_code;
    public string $flag_url_svg;
    public string $flag_url_png;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $countryInfo = [])
    {
        if (!empty($countryInfo)) {
            $this->initialize($countryInfo);
        }
    }

    public function initialize(array $countryInfo)
    {
        $this->country_id = $countryInfo['country_id'];
        $this->country_name_en = $countryInfo['country_name_en'];
        $this->country_name_sv = $countryInfo['country_name_sv'];
        $this->country_code = $countryInfo['country_code'];
        $this->flag_url_svg = $countryInfo['flag_url_svg'];
        $this->flag_url_png = $countryInfo['flag_url_png'];
        $this->created_at = $countryInfo['created_at'];
        $this->updated_at = $countryInfo['updated_at'];
    }

    public function toArray()
    {
        return [
            'country_id' => $this->country_id,
            'country_name_en' => $this->country_name_en,
            'country_name_sv' => $this->country_name_sv,
            'country_code' => $this->country_code,
            'flag_url_svg' => $this->flag_url_svg,
            'flag_url_png' => $this->flag_url_png,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}