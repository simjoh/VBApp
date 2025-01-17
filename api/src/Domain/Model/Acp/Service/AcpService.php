<?php

namespace App\Domain\Model\Acp\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\Acp\Repository\AcpRepository;
use App\Domain\Permission\PermissionRepository;
use League\Csv\Writer;
use Psr\Container\ContainerInterface;

class AcpService extends ServiceAbstract
{

    private $acprepository;
    private $permissionrepository;

    public function __construct(ContainerInterface $c, AcpRepository $acprepository, PermissionRepository $permissionRepository)
    {

        $this->acprepository = $acprepository;
        $this->permissionrepository = $permissionRepository;
    }


    public function getAcpReportFor(string $track_uid, string $currentuser_id)
    {
        $permissions = $this->getPermissions($currentuser_id);


        if (in_array("ACPREPORT", $permissions)) {
            $data = $this->acprepository->getAcpReportFor($track_uid);

            $csv = Writer::createFromString('');

            $header1 = [' ', 'CLUB ORGANISATEUR', 'CLUB_DU_PARICIPANT', 'ACPKOD', 'DURTION', 'SEXE', 'Track UID', 'Participant UID', 'Breve Number'];
            $header2 = [' ', 'NOM', 'PRENOM', 'CLUB_DU_PARICIPANT', 'ACPKOD', 'DURTION', 'SEXE', 'Track UID', 'Participant UID', 'Breve Number'];


            // Insert the header row
            $csv->insertOne($header1);

            // Insert the data into the CSV
            $csv->insertAll($data);
            // Get the CSV content as a string
            return $csv->toString();
        }

        return array();
    }


    public function getPermissions($user_uid): array
    {

        return $this->permissionrepository->getPermissionsTodata("ACPREPORT", $user_uid);
    }

}