<?php


namespace App\Traits;


use App\Enums\Months;
use Illuminate\Support\Facades\App;

trait GenderTrait
{




    public function gendersSv(): array
    {
        return array("1"=>"Kvinna", "2"=>"Man");
    }

    public function gendersEn(): array
    {
        return array("1"=>"Female", "2"=>"Male");
    }


    public function gendersFor(string $val): string
    {
        $genders = array("1"=>"Kvinna", "2"=>"Man", "3"=>"Annat");
        return $genders[$val];

    }
}
