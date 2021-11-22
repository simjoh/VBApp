<?php

namespace App\Domain\Model\User\Rest;
use App\common\Rest\Link;
use App\Domain\Model\User\UserInfo;

class UserInfoAssembly
{

    public function toRepresentation(UserInfo $s): UserInfoRepresentation {
        $userinforepr = new UserInfoRepresentation();
        $userinforepr->setUid($s->getUid());
        $userinforepr->setUserUid($s->getUserUid());
        $userinforepr->setPhone($s->getPhone());
        $userinforepr->setEmail($s->getEmail());

        $linkArray = array();
        array_push($linkArray, new Link("self", 'GET', '/api/userinfo/' . $s->getUid()));
        $userinforepr->setLink($linkArray);
        return $userinforepr;
    }

    public function toUserinfo(UserInfoRepresentation $userInfoRepresentation, string $user_uid, bool $create): UserInfo {

        if($create){
            $uid = "";
        } else {
          $uid =  $userInfoRepresentation->getUid();
        }

        $userinfo = new UserInfo($user_uid,$uid, $userInfoRepresentation->getPhone(), $userInfoRepresentation->getEmail());
        return $userinfo;
    }

}