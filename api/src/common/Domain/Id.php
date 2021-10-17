<?php

namespace App\common\Domain;

use App\common\Exceptions\BrevetException;
use App\common\Util;

abstract class Id
{
   private $id;

    /**
     * @param $id
     */
    public  function __construct($id)
    {
//      if(Util::nullOrEmpty($id)){
//          throw new BrevetException("Id is set",1);
//      }
        $this->id = $id;
    }
}