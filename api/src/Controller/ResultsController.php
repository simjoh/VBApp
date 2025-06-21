<?php

namespace App\Controller;

use App\Domain\Model\Competitor\Service\CompetitorService;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use App\Domain\Model\Result\Service\ResultService;
use App\Domain\Model\Track\Service\TrackService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use Slim\Views\Twig;
use DateTime;

class ResultsController
{

    private $settings;
    private $resultService;
    private $eventservice;
    private $trackService;
    private $participantservice;
    private $competitorservice;

    public function __construct(ContainerInterface $c, ResultService $resultService, EventService $eventService, TrackService $trackService, ParticipantService $participantservice, CompetitorService $competitorService)
    {
        $this->settings = $c->get('settings');
        $this->resultService = $resultService;
        $this->eventservice = $eventService;
        $this->trackService = $trackService;
        $this->participantservice = $participantservice;
        $this->competitorservice = $competitorService;
    }

    //Resultat på text BRM2021
    public function getResultView(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $year = $route->getArgument('year');

        $event = $this->eventservice->eventFor($eventUid, "");
        $result = $this->resultService->resultsOnEvent($eventUid, $year);
        return $view->render($response, 'result.html', [
            'link' => $this->settings['path'] . "resultList/year/" . strval($args['year']) . "/event/" . $args['eventUid'], 'event' => json_encode($event)
        ]);
    }

    public function getResultForEvent(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');

        $event = $this->eventservice->eventFor($eventUid, "");

        $result = $this->resultService->resultsOnEventNew($eventUid);
        return $view->render($response, 'resultonevent.html', ['event' => $event, 'results' => $result]);
    }

    public function getResultOnTrack(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');
        $track = $this->trackService->getTrackByTrackUid($trackUid,'');
        $event = $this->eventservice->eventFor($track->getEventUid(),'');
        $result = $this->resultService->resultsOnTrack($trackUid);
        return $view->render($response, 'resultontrack.html', ['track' => $track, 'startdate' => (new DateTime($track->getStartDateTime()))->format('Y-m-d'), 'starttime' => (new DateTime($track->getStartDateTime()))->format('H:i'), 'event' => $event ,'results' => $result]);
    }


    public function getCompetitorsAllResults(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('competitorUid');
        $competitor = $this->competitorservice->getCompetitorByUid($competitor_uid,'');
        $result = '';
      //  $result = $this->resultService->resultsOnTrack($trackUid);
        return $view->render($response, 'resultontrack.html', [ 'results' => $result]);
    }

    public function getTrackView(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $event = $this->eventservice->eventFor($eventUid, "");
        $tracks = $this->trackService->tracksForEvent("", $eventUid);
        return $view->render($response, 'trackonevent.html', [
            'link' => $this->settings['path'] . "tracker/" . "event/" . $args['eventUid'], 'event' => $event, 'tracks' => $tracks
        ]);
    }

    public function getTrackOnTrackView(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');

        $tracks = $this->trackService->getTrackByTrackUid($trackUid, "");
        $event = $this->eventservice->eventFor($tracks->getEventUid(), "");
        return $view->render($response, 'tracktrack.html', [
            'link' => $this->settings['path'] . "tracker/" . "track/" . $args['trackUid'], 'event' => json_encode($event === null ? null : $event), 'track' => json_encode($tracks === null ? null : $tracks)
        ]);
    }

   public function gettrackranonneurview(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');
        $participantUid = $route->getArgument('participantUid');

        $tracks = $this->trackService->getTrackByTrackUid($trackUid, "");
        $participant = $this->participantservice->participantFor($participantUid, '');

        $event = $this->eventservice->eventFor($tracks->getEventUid(), "");

        $result = $this->resultService->trackRandonneurOnTrack($participant->getParticipantUid(), $tracks->getTrackUid());
        
            // Filter checkpoints based on specific UIDs
            if ($trackUid == '05851816-58ff-442c-8d29-3588244d13ad') {
                $allowedCheckpointUids = [
                    '1304eec8-64e4-4ac8-ba3f-ef608cf37517',
                    '1d5db746-9598-4ce1-9835-43b328cbfaaa',
                    '1f114ec3-bd94-474d-ad66-bb37d81aecc2',
                    '2e101463-d50f-40de-95df-a3d48c59d249',
                    '6c248578-281b-4180-b43f-5b6f74d14154',
                    '6eec6814-5293-469f-8da4-e2bd750db079',
                    '700c76f6-de4f-488c-a2dd-20ee62a2744a',
                    '899cb804-aff2-4f55-b40b-98ada932d35d',
                    'd1b47bb1-80fe-4a84-865f-d9c7e3424c62',
                    'd9383cac-6a73-4d4f-81a1-b1733ab1e285',
                    'd9ed8e43-eca7-40e0-bf60-f12453a4c183',
                    'dbc0afe7-5e5c-4bb6-ae8d-243031624af3',
                    'e95002a7-4141-4d72-adc4-0253570aa79c',
                    'f3831dc2-a52a-4cdf-9510-0da7520e880b',
                    'fff43620-bc4e-443c-8d46-f8134c260cfa'
                ];
                
                if (is_array($result)) {
                    $result = array_filter($result, function($checkpoint) use ($allowedCheckpointUids) {
                        return in_array($checkpoint['checkpoint_uid'], $allowedCheckpointUids);
                    });
                }
            }

        $competitor = $this->competitorservice->getCompetitorByUid($participant->getCompetitorUid(), '');
        return $view->render($response, 'trackparticipantontrack.html', ['trackinginfo' => $result, 'track' => $tracks, 'competitor' => $competitor,
            'link' => $this->settings['path'] . "tracker/" . "track/" . $args['trackUid'] . '/participant/' . $participant->getParticipantUid() . '/checkpoints', 'participant' => $participant, 'starttime' => $tracks->getStartDateTime()
        ]);
    }

    public function gettrackranonneurcheckpoints(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $trackUid = $route->getArgument('trackUid');
        $participantUid = $route->getArgument('participantUid');
        $tracks = $this->trackService->getTrackByTrackUid($trackUid, "");
        $participant = $this->participantservice->participantFor($participantUid, null);

        $result = $this->resultService->trackRandonneurOnTrack($participant->getParticipantUid(), $tracks->getTrackUid());
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    //Resultat på text BRM2021
    public function getResultList(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $year = $route->getArgument('year');
        $result = $this->resultService->resultsOnEvent($eventUid, $year);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }

    public function track(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $tracks = $this->trackService->tracksForEvent("", $eventUid);
        $result = $this->resultService->trackContestants($eventUid, $tracks);
        $returnarray = array();
        foreach ($result as $results) {
            foreach ($tracks as $track) {
                if ($results['track_uid'] === $track->getTrackUid()) {
                    foreach ($track->getLinks() as $link) {
                        if ($link->getRel() === 'relation.track.tracktrack') {
                            $results['trackurl'] = $link->getUrl();
                        }
                    }
                }
            }
            array_push($returnarray, $results);
        }
        return $view->render($response, 'trackonevent.html', [
            'tracks' => $returnarray,
            'event' => $this->eventservice->eventFor($eventUid, ""),
        ]);
    }


    public function trackrandonneurontrack(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');
        $track = $this->trackService->getTrackByTrackUid($trackUid, "");
        $trackArray = array();

        array_push($trackArray, $track);

        $result = $this->resultService->trackContestants($track->getEventUid(), $trackArray);

        $preparedarray = array();

        foreach ($result as $result) {
            $participants = $this->participantservice->participantsForCompetitor($result['competitor_uid'], '');
            foreach ($participants as $participant) {
                if ($participant->getCompetitorUid() === $result['competitor_uid'] && $trackUid === $participant->getTrackUid()) {
                    $result['trackurl'] = $this->settings['path'] . 'track/' . $trackUid . '/participant/' . $participant->getParticipantUid() . '/view';
                }
            }
            array_push($preparedarray, $result);
        }


        return $view->render($response, 'trackontrack.html', [ 'track' => $track,'participants' => $preparedarray,
            'link' => $this->settings['path'] . "tracker/" . "track/" . $args['trackUid']
        ]);

    }


    public function resultForContestant(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $competitorUId = $route->getArgument("uid");
        $params = $request->getQueryParams();


        if (array_key_exists('trackUid', $params)) {
            $trackUid = $params["trackUid"];
        } else {
            $trackUid = "";
        }
        if (array_key_exists('eventUid', $params)) {
            $eventUid = $params['eventUid'];
        } else {
            $eventUid = "";
        }


        $result = $this->resultService->resultForContestant($competitorUId, $trackUid, $eventUid);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function getResultViewForContestant(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $competitor_uid = $route->getArgument('uid');
        $params = $request->getQueryParams();
        $link = "?";

        $params = http_build_query($params);

        if ($params != "") {
            $link = $link . $params;
        } else {
            $link = "";
        }
        return $view->render($response, 'resultcontestant.html', [
            'link' => $this->settings['path'] . "/results/randonneur/" . $args['uid'] . $link,
        ]);
    }

    //Resultat på text BRM2021
    public function getResult(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $response->getBody()->write("<html><body>jjsadjsajd</body></html>");
        return $response->withHeader('Content-Type', 'text/html')->withStatus(200);

    }

    // tillfälligt testdata
    private function getJson(): array
    {
        return [["ID" => '1042', "BRM" => '200K', "Efternam" => "Johansson", "Förnamn" => 'Kalle', 'Klubb' => 'Cykelintresset', "ACP-kod" => '113072', "Brevetnr" => '779642', "Tid" => '07:12', "SR" => ''],
            ["ID" => '1042', "BRM" => '200K', "Efternam" => "Johanesson", "Förnamn" => 'Johanna', 'Klubb' => 'Gimonäs', "ACP-kod" => '113110', "Brevetnr" => '132746', "Tid" => '39:12', "SR" => 'SR']];

    }

    //Resultat på text BRM2021
    public function getResultsPhp(ServerRequestInterface $request, ResponseInterface $response)
    {
        $arr[] = [["name" => "Kalle", "kon" => "man", "track" => "Brm 300 Lycksele"], ["name" => "Mona", "kon" => "Kvinna", "track" => "Brm 200 Bjurholm"]];
        $phpView = new PhpRenderer("../templates", ["title" => "My App"]);
        $phpView->setLayout("layout.php");
        $phpView->setAttributes(["resultlist" => $arr, "test" => 'Hej']);
        $phpView->render($response, "results.php", ["title" => "Hello - My App", "name" => "John"]);

        return $response;
    }

}
