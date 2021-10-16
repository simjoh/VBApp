<?php

namespace App\common;

 use Exception;

 final class Util
{

    public final function nullOrEmpty($str): bool{
        return strlen(($str) == '' ? True : throw new Exception("Id must have a value"));
 }



}