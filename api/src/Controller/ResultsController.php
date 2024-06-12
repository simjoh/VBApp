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

class ResultsController
{

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
        return $view->render($response, 'resultontrack.html', ['track' => $track, 'event' => $event ,'results' => $result]);
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
            'link' => $this->settings['path'] . "tracker/" . "event/" . $args['eventUid'], 'event' => json_encode($event === null ? "" : $event), 'tracks' => $tracks
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

        $tracks = $this->trackService->getTrackByTrackUid($trackUid, "");
        $participant = $this->participantservice->participantFor($participantUid, '');

        $result = $this->resultService->trackRandonneurOnTrack($participant->getParticipantUid(), $tracks->getTrackUid());

        $competitor = $this->competitorservice->getCompetitorByUid($participant->getCompetitorUid(), '');
        return $view->render($response, 'trackparticipantontrack.html', ['trackinginfo' => $result, 'track' => $tracks, 'competitor' => $competitor,
            'link' => $this->settings['path'] . "tracker/" . "track/" . $args['trackUid'] . '/participant/' . $participant->getParticipantUid() . '/checkpoints'
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
            'tracks' => $returnarray
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