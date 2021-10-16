<?php

namespace App\common\Domain;

use App\common\Util;

abstract class Id
{
   private $id;

    /**
     * @param $id
     */
    public  function __construct($id)
    {
        Util::nullOrEmpty($id);
        $this->id = $id;
    }
}