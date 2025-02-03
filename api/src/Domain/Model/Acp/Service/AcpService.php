<?php

namespace App\Domain\Model\Acp\Service;

use App\common\CurrentUser;
use App\common\Exceptions\BrevetException;
use App\common\Message\Messages;
use App\common\Rest\AcpReportRestClient;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Acp\AcpReport;
use App\Domain\Model\Acp\AcpReportAssembler;
use App\Domain\Model\Acp\Repository\AcpRepository;
use App\Domain\Model\Acp\Rest\AcpReportRepresentation;
use App\Domain\Model\Acp\Rest\Report\AcpReportParticipantRepresentation;
use App\Domain\Model\Acp\Rest\Report\AcpReportParticipantRepresentationBuilder;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Permission\PermissionRepository;
use GuzzleHttp\Exception\RequestException;
use League\Csv\Writer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;

class AcpService extends ServiceAbstract
{

    private $acprepository;
    private $permissionrepository;
    private TrackRepository $trackRepository;
    private AcpReportRestClient $acpReportRestClient;
    private AcpReportAssembler $acpReportAssembler;

    public function __construct(ContainerInterface $c, AcpRepository $acprepository, PermissionRepository $permissionRepository, TrackRepository $trackRepository, AcpReportRestClient $acpReportRestClient, AcpReportAssembler $acpReportAssembler)
    {

        $this->acprepository = $acprepository;
        $this->permissionrepository = $permissionRepository;
        $this->trackRepository = $trackRepository;
        $this->acpReportRestClient = $acpReportRestClient;
        $this->acpReportAssembler = $acpReportAssembler;

    }

    public function getAcpReportBy(string $report_uid)
    {
        $acpreport = $this->acprepository->getAcpReportBy($report_uid);
        return AcpReportAssembler::toRepresentation($acpreport);
    }

    public function getAcpReportAsCsv(string $track_uid)
    {
        $haspermission = $this->haspermission($this->getPermissions(CurrentUser::getUser()->getId()), "READ");
        $track = $this->trackRepository->getTrackByUid($track_uid);

        if ($haspermission) {
            $data = $this->acprepository->getParticipantsToReport($track_uid, "ss");

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

    public function reportToAcp(string $report_uid): bool
    {
        $roles = CurrentUser::getUser()->getRoles();

        $acpreport = $this->acprepository->getAcpReportBy($report_uid);

        if (!$acpreport) {
            throw new BrevetException(Messages::get('WELCOME'), 5, null);
        }
        //get participants
        $participantstoreport = $this->acprepository->getParticipantsToReport($report_uid, $acpreport->getTrackUid());


        $acprows = array();
        foreach ($participantstoreport as $item) {
            $row = AcpReportParticipantRepresentation::builder()
                ->setMedaille($item->getMedaille())
                ->setTemps($item->getTemps())
                ->setSexe($item->getSexe())
                ->setNom($item->getNom())
                ->setPrenom($item->getPrenom())
                ->setNaissance($item->getNaissance())
                ->setCodeclub($item->getCodeclub())
                ->setNomclub($item->getNomclub())
                ->build();
            array_push($acprows, (object)$row);

        }
        if (count($acprows) > 0) {
            //get report
            $promise = $this->acpReportRestClient->sendAthletesDataAsync($acpreport->getBrmId(), $acprows);
            return $promise->then(
                function (ResponseInterface $res) {
                    if ($res->getStatusCode() === 201) {
                        $report_uid = "";
                        // save all paricipants in report in a table
                        $this->acprepository->markAsDeliveredToAcp($report_uid);
                        $this->acprepository->markparticipantsAsdeliverd($report_uid);

                        return $res->getBody()->getContents();
                    } else {
                        return "{}";
                    }
                },
                function (RequestException $e) {
                    throw new BrevetException(Messages::get('ERROR_500'), 5, null);
                }
            )->wait();
        }

        return false;
    }


    public function markAsreadyForApproval($report_uid): bool
    {
        if ($this->haspermission($this->getPermissions(CurrentUser::getUser()->getId()), "WRITE")) {
            return $this->acprepository->markAsReadyForApproval($report_uid);
        } else {
            throw new BrevetException(Messages::get('WELCOME'), 5, null);
        }
    }

    public function deleteReport(string $report_uid): bool
    {
        if ($this->haspermission($this->getPermissions(CurrentUser::getUser()->getId()), "WRITE")) {
            return $this->acprepository->deleteReport($report_uid);
        } else {
            throw new BrevetException(Messages::get('WELCOME'), 5, null);
        }

    }

    public function createReport(AcpReportRepresentation $acpReportRepresentation): ?AcpReportRepresentation
    {
        if ($this->haspermission($this->getPermissions(CurrentUser::getUser()->getId()), "WRITE")) {
            $acpReport = $this->acpReportAssembler->toDomain($acpReportRepresentation);
            $acpReportcreated = $this->acprepository->createAcpreport($acpReport);
        } else {
            throw new BrevetException(Messages::get('WELCOME'), 5, null);
        }

        $acpReportRepresentation->setReportUid($acpReportcreated->getReportUid());
        return $acpReportRepresentation;
    }

    public function tracksPossibleToReportOn($track_uid): array
    {
        return $this->acprepository->getTracks($track_uid);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("ACPREPORT", $user_uid);
    }


}