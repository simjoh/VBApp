<?php

namespace App\Domain\Model\Acp\Service;

use App\common\CurrentUser;
use App\common\Rest\AcpReportRestClient;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Acp\Repository\AcpRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Permission\PermissionRepository;
use League\Csv\Writer;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class AcpService extends ServiceAbstract
{

    private $acprepository;
    private $permissionrepository;
    private TrackRepository $trackRepository;
    private AcpReportRestClient $acpReportRestClient;

    public function __construct(ContainerInterface $c, AcpRepository $acprepository, PermissionRepository $permissionRepository, TrackRepository $trackRepository, AcpReportRestClient $acpReportRestClient)
    {

        $this->acprepository = $acprepository;
        $this->permissionrepository = $permissionRepository;
        $this->trackRepository = $trackRepository;
        $this->acpReportRestClient = $acpReportRestClient;
    }


    public function getAcpReportFor(string $track_uid, string $currentuser_id)
    {
        $permissions = $this->getPermissions($currentuser_id);

        $track = $this->trackRepository->getTrackByUid($track_uid);

        if ($this->haspermission($permissions, 'READ')) {
            $data = $this->acprepository->getAcpReportFor($track_uid);

            $csv = Writer::createFromString('');

            $header1 = [
                "N° Homologation",
                "CLUB ORGANISATEUR",
                "",
                "",
                "code ACP",
                "DATE",
                "DISTANCE",
                "INFORMATIONS",
                ""
            ];

            $header2 = [
                " ",
                "Randonneurs Sverige, Umeå",
                "",
                "",
                "113000",
                $track->getStartDateTime(),
                $track->getDistance(),
                "Medaille",
                "Sexe",
            ];


            $header3 = ["",
                "NOM",
                "PRENOM",
                "CLUB DU PARTICIPANT",
                "",
                "",
                "",
                "(x)",
                "(F)",

            ];


            $records = [

                ["",
                    "Anderson",
                    "Erik",
                    "Cykelintresset",
                    "113072",
                    "11:18",
                    "",
                    "",
                    "M",
                ],
                ["",
                    "Burström",
                    "Lovisa",
                    "Cykelintresset",
                    113072,
                    "11:51",
                    "",
                    "",
                    "F",
                ],
                // Continue for the rest of the rows
            ];


            // Insert the header row
            $csv->insertOne($header1);
            $csv->insertOne($header2);
            $csv->insertOne($header3);

            // Insert the data into the CSV
            $csv->insertAll($records);
            // Get the CSV content as a string
            return $csv->toString();
        }
        return "";

    }

    public function reportToAcp(string $track_uid): bool
    {

        $currentUser = CurrentUser::getUser();

        $participantstoreport = array();
        //
        //get report
        //get participants
        $promise = $this->acpReportRestClient->sendAthletesDataAsync(48479, $participantstoreport);

        //save report

        return true;
    }

    public function tracksPossibleToReportOn($track_uid): array
    {

        $this->acprepository->getAcpReportFor($track_uid);

        return [];
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("ACPREPORT", $user_uid);
    }


}