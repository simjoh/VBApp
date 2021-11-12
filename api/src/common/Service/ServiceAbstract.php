<?php

namespace App\common\Service;

abstract class ServiceAbstract
{

    abstract public function getPermissions($user_uid): array;

}