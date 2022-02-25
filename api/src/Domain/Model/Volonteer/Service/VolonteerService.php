<?php

namespace App\Domain\Model\Volonteer\Service;



use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\Volonteer\Repository\VolonteerRepository;
use App\Domain\Model\Volonteer\Rest\ParticipantToPassCheckpointAssembly;
use App\Domain\Permission\PermissionRepository;

class VolonteerService extends ServiceAbstract
{

    public function __construct(VolonteerRepository $volonteerRepository, ParticipantToPassCheckpointAssembly $participantToPassCheckpointAssembly, PermissionRepository $permissionRepository, UserRepository $userRepository)
    {
        $this->volonteerRepository = $volonteerRepository;
        $this->participantToPassCheckpointAssembly = $participantToPassCheckpointAssembly;
        $this->permissionrepository = $permissionRepository;
        $this->userepository = $userRepository;
    }


    public function getRandoneursForCheckpoint(string $track_uid, string $checkpoint_uid, string $currentUserUID){

//       $userRoles = $this->userepository->getUserRoles($currentUserUID);
//       $roleID = 0;
//       if(count($userRoles) > 0){
//           foreach ($userRoles as $role_id) {
//               if($this->userepository->isVolonteer($role_id)){
//                   $roleID = $role_id;
//               }
//           }
//           if(!$this->userepository->isVolonteer($roleID)){
//               throw new BrevetException("Användaren ha inte behörighet", 5, null);
//           }
//       }

        $permissions = $this->getPermissions($currentUserUID);
        $pparticipantToPassCheckpoint = $this->volonteerRepository->getRandoneurToPassCheckpoint($track_uid, $checkpoint_uid);
        if(count($pparticipantToPassCheckpoint) > 0){
            return $this->participantToPassCheckpointAssembly->toRepresentations($pparticipantToPassCheckpoint,$currentUserUID,$permissions);
        }
        return array();
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("VOLONTEER",$user_uid);
        // TODO: Implement getPermissions() method.
    }
}