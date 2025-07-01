<?php

namespace App\Action\Participant;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\Loppservice\Rest\LoppserviceParticipantTranformer;
use App\Domain\Model\Loppservice\Rest\LoppservicePersonRepresentation;
use App\Domain\Model\Loppservice\Rest\LoppserviceRegistrationRepresentation;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationRepresentation;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationRepresentationTransformer;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use Exception;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\UploadedFile;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Track\Service\TrackService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ParticipantAction
{

    private $participantService;
    private $settings;
    private $trackService;
    public function __construct(ContainerInterface $c, ParticipantService $participantService, TrackService $trackService)
    {
        $this->participantService = $participantService;
        $this->settings = $c->get('settings');
        $this->trackService = $trackService;
    }

    public function participants(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participantUid = $route->getArgument('participantUid');
        $part = $this->participantService->participantFor($participantUid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($part));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getCheckpointsForparticipant(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('participantUid');
        $checkpointforrandoneur = $this->participantService->checkpointsForParticipant($participant_uid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDnf($participant_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function markasDNS(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->setDns($participant_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function stampAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->stampAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function checkoutAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->checkoutAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackstampAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->rollbackstampAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackCheckoutAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $response->getBody()->write(json_encode($this->participantService->rollbackCheckoutAdmin($participant_uid, $checkpoint_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateCheckpointTime(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        
        // Get request body data
        $data = $request->getParsedBody();
        $stamptime = $data['stamptime'] ?? null;
        $checkouttime = $data['checkouttime'] ?? null;
        
        if (!$stamptime && !$checkouttime) {
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $result = false;
        
        if ($stamptime) {
            $result = $this->participantService->updateCheckpointTime(
                $participant_uid, 
                $checkpoint_uid, 
                $stamptime,
                $request->getAttribute('currentuserUid')
            );
        }
        
        if ($checkouttime) {
            // Handle checkout time update
            $result = $this->participantService->updateCheckoutTime(
                $participant_uid, 
                $checkpoint_uid, 
                $checkouttime,
                $request->getAttribute('currentuserUid')
            );
        }
        
        $response->getBody()->write(json_encode(['success' => $result]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDnf($participant_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackDNS(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $response->getBody()->write(json_encode($this->participantService->rollbackDns($participant_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEvent($event_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantsOnTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrack($track_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantsOnTrackMore(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrackWithMoreInformation($track_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $part_uid = $route->getArgument('uid');
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEventAndTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEventAndTrack($event_uid, $track_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateParticipant(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $track_uid = $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $newTime = $params["newTime"];
        $this->participantService->updateTime($track_uid, $participant_uid, $newTime);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }


    public function updateTime(ServerRequestInterface $request, ResponseInterface $response)
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $track_uid = $route->getArgument('trackUid');
        $params = $request->getQueryParams();
        $newTime = $params["newTime"];
        $this->participantService->updateTime($track_uid, $participant_uid, $newTime);
//        $this->participantService->updatparticipant($track_uid, $newParticipant);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function addParticipantOntrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $jsonDecoder = new JsonDecoder();

        $json = $request->getBody();
        $data = json_decode($json, true);
        $jsonDecoder->register(new ParticipantInformationRepresentationTransformer());
        $newParticipant = $jsonDecoder->decode($request->getBody()->getContents(), ParticipantInformationRepresentation::class);
        $this->participantService->addParticipantOnTrack($track_uid, $newParticipant);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    public function addParticipantOntrack2(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new LoppserviceParticipantTranformer());
        $test = json_decode($request->getBody()->getContents(), true);

        try {
            $registration = $jsonDecoder->decode($request->getBody()->getContents(), LoppserviceRegistrationRepresentation::class);
            $data = $jsonDecoder->decode($request->getBody()->getContents(), LoppservicePersonRepresentation::class);
            
            $club = $data->club ?? null;
            $medal = $data->medal ?? false;

            $result = $this->participantService->addParticipantOnTrackFromLoppservice($data, $track_uid, $registration, $club, $medal);
            
            if ($result) {
                $response->getBody()->write(json_encode([
                    'valid' => true, 
                    'response_uid' => $data->response_uid, 
                    'registration_uid' => $registration->registration['registration_uid'],
                    'message' => 'Participant successfully created',
                    'person_uid' => $data->participant['person_uid']
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode([
                    'valid' => false, 
                    'response_uid' => $data->response_uid, 
                    'registration_uid' => $registration->registration['registration_uid'], 
                    'message' => 'Could not create participant - unknown error'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

        } catch (BrevetException $e) {
            // Handle business logic exceptions with appropriate status codes
            $statusCode = 400; // Bad Request for validation errors
            if (strpos($e->getMessage(), 'already registered') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                $statusCode = 409; // Conflict for duplicate entries
            } elseif (strpos($e->getMessage(), 'not exists') !== false ||
                      strpos($e->getMessage(), 'not found') !== false) {
                $statusCode = 404; // Not Found
            }

            $response->getBody()->write(json_encode([
                'valid' => false, 
                'response_uid' => $data->response_uid ?? null, 
                'registration_uid' => $registration->registration['registration_uid'] ?? null, 
                'message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
            
        } catch (Exception $e) {
            // Handle unexpected exceptions
            $response->getBody()->write(json_encode([
                'valid' => false, 
                'response_uid' => $data->response_uid ?? null, 
                'registration_uid' => $registration->registration['registration_uid'] ?? null, 
                'message' => 'Internal server error: ' . $e->getMessage(),
                'error_type' => 'unexpected_error'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function uploadParticipants(ServerRequestInterface $request, ResponseInterface $response)
    {

        $uploadDir = $this->settings['upload_directory'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles as $uploadedFile) {
//            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($uploadDir, $uploadedFile);

//            }
        }
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $uploadedParticipants = $this->participantService->parseUplodesParticipant($filename, $uploadDir, $track_uid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($uploadedParticipants));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);


        //return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteParticipant(ServerRequestInterface $request, ResponseInterface $response)
    {
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipant($route->getArgument('uid'), $currentuserUid);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function deleteParticipantsontrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->participantService->deleteParticipantsOnTrack($route->getArgument('trackUid'), $currentuserUid);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }



    public function addbrevetnumber(ServerRequestInterface $request, ResponseInterface $response)
    {
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $params = $request->getQueryParams();
        $participant_uid = $route->getArgument('uid');
        $brevenumber = $params["brevenr"];
        $this->participantService->updateParticipantwithbrevenumber($participant_uid, $brevenumber);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantclickeddnsinmail(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $currentuserUid = $request->getAttribute('currentuserUid');
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $participant_uid = $route->getArgument('uid');
        $participant = $this->participantService->participantFor($participant_uid,"");
        $track = $this->trackService->getTrackByTrackUid($participant->getTrackUid(), "");
        $result = $this->participantService->participantclickeddnsinmail($participant_uid, "");
   
        return $view->render($response, 'participantdns.html', ['track' => $track ,'results' => $result, 'participant' => $participant]);
    }

    public function exportParticipantsToExcel(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        
        error_log("Starting Excel export for track: " . $track_uid);
        
        // Test data instead of service call
        $participants = [
            (object)[
                'homologationNumber' => '123456',
                'lastName' => 'Smith',
                'firstName' => 'Alice',
                'club' => 'Cycling Pro Club',
                'acpCode' => 'AC123',
                'time' => '08:32',
                'gender' => 'F',
                'birthDate' => '1985-06-14'
            ],
            (object)[
                'homologationNumber' => '123457',
                'lastName' => 'Johnson',
                'firstName' => 'Bob',
                'club' => 'Fast Wheels',
                'acpCode' => 'AC124',
                'time' => '08:50',
                'gender' => 'M',
                'birthDate' => '1990-02-10'
            ],
            (object)[
                'homologationNumber' => '123458',
                'lastName' => 'Anderson',
                'firstName' => 'Carol',
                'club' => 'Mountain Riders',
                'acpCode' => 'AC125',
                'time' => '09:15',
                'gender' => 'F',
                'birthDate' => '1988-11-23'
            ],
            (object)[
                'homologationNumber' => '123459',
                'lastName' => 'Wilson',
                'firstName' => 'David',
                'club' => 'Road Warriors',
                'acpCode' => 'AC126',
                'time' => '09:30',
                'gender' => 'M',
                'birthDate' => '1992-04-05'
            ],
            (object)[
                'homologationNumber' => '123460',
                'lastName' => 'Brown',
                'firstName' => 'Emma',
                'club' => 'Speed Demons',
                'acpCode' => 'AC127',
                'time' => '09:45',
                'gender' => 'F',
                'birthDate' => '1995-08-17'
            ]
        ];
        
        error_log("Test data created with " . count($participants) . " participants");
        
        // Create new Spreadsheet using fully qualified class name
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        error_log("Spreadsheet created");

        // ====== HEADER ROWS ======
        // Row 1: Merged title headers
        $sheet->mergeCells('C1:E1')->setCellValue('C1', 'ORGANIZING CLUB');
        $sheet->mergeCells('F1:F1')->setCellValue('F1', 'ACP code number');
        $sheet->mergeCells('G1:G1')->setCellValue('G1', 'DATE');
        $sheet->mergeCells('H1:H1')->setCellValue('H1', 'DISTANCE');
        $sheet->mergeCells('J1:K1')->setCellValue('J1', 'INFORMATION');

        error_log("Header row 1 created");

        // Row 2: Column headers
        $headers = [
            'A2' => 'Homologation number',
            'B2' => 'LAST NAME',
            'C2' => 'FIRST NAME',
            'D2' => "RIDER'S CLUB",
            'E2' => 'ACP CODE NUMBER',
            'F2' => 'TIME',
            'G2' => '(x)',
            'H2' => '(F)',
            'I2' => 'BIRTH DATE',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        error_log("Header row 2 created");

        // ====== STYLING ======
        $sheet->getStyle('A1:K2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:K2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        error_log("Styling applied");

        // ====== DATA ROWS ======
        $rowIndex = 3;
        foreach ($participants as $participant) {
            $rider = [
                $participant->homologationNumber,
                $participant->lastName,
                $participant->firstName,
                $participant->club,
                $participant->acpCode,
                $participant->time,
                'x',
                $participant->gender,
                $participant->birthDate
            ];
            $sheet->fromArray($rider, null, "A$rowIndex");
            $rowIndex++;
        }

        error_log("Data rows added, total rows: " . ($rowIndex - 1));

        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'riders') . '.xlsx';
        error_log("Temporary file created: " . $tempFile);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        error_log("File saved to: " . $tempFile);

        // Create stream and return response
        $stream = new \Slim\Psr7\Stream(fopen($tempFile, 'rb'));
        error_log("Stream created from file");

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="rider_sheet.xlsx"')
            ->withBody($stream);
    }

    public function generateHomologationCsv(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $result = $this->participantService->generateHomologationCsv($track_uid);
        
        // Create stream from CSV content
        $stream = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));
        $stream->write($result['content']);
        $stream->rewind();

        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"')
            ->withBody($stream);
    }

    public function generateParticipantListCsv(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');

        $result = $this->participantService->generateParticipantListCsv($track_uid);
        
        // Create stream from CSV content
        $stream = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));
        $stream->write($result['content']);
        $stream->rewind();

        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"')
            ->withBody($stream);
    }

    public function getParticipantStats(ServerRequestInterface $request, ResponseInterface $response)
    {
        $date = date('Y-m-d');  // Use current date
        error_log("Getting stats for date: " . $date);
        $stats = $this->participantService->getParticipantStats($date);
        
        $response->getBody()->write(json_encode($stats));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getTopTracks(ServerRequestInterface $request, ResponseInterface $response)
    {
        error_log("Getting top tracks");
        $tracks = $this->participantService->getTopTracks();
        error_log("Top tracks result: " . json_encode($tracks));
        $response->getBody()->write(json_encode($tracks));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = $uploadedFile->getClientFilename(); // see http://php.net/manual/en/function.random-bytes.php
        $filename = $basename;
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}